<?php
/**
 * 補助金データ全件バッチインポートスクリプト
 *
 * 49,000件超の補助金データを効率的に WordPress にバッチ投入する。
 * WP-CLI 経由で実行: wp eval-file scripts/wp-import-all.php
 *
 * @package SubsidyMatch
 */

if (!class_exists('WP_CLI')) {
    echo "このスクリプトは WP-CLI 経由で実行してください:\n";
    echo "  wp eval-file scripts/wp-import-all.php\n";
    exit(1);
}

function subsidy_bulk_import_main() {
    global $wpdb;

    // Docker 内実行時は /tmp、ローカル実行時は data/ を参照
    $json_path = dirname(__FILE__) . '/../data/subsidies.json';
    if (!file_exists($json_path) && file_exists('/tmp/subsidies.json')) {
        $json_path = '/tmp/subsidies.json';
    }

    if (!file_exists($json_path)) {
        WP_CLI::error("ファイルが見つかりません: {$json_path}");
        return;
    }

    WP_CLI::log("JSONファイル読み込み中...");
    $json = file_get_contents($json_path);
    $data = json_decode($json, true);
    unset($json); // メモリ解放

    if (!$data || !isset($data['items'])) {
        WP_CLI::error("無効なJSONデータ");
        return;
    }

    $items = $data['items'];
    $total = count($items);
    unset($data); // メモリ解放

    WP_CLI::log("総件数: {$total}");

    // 既存 portal_id 一覧をプリロード（重複チェック高速化）
    WP_CLI::log("既存データの portal_id を取得中...");
    $existing_portal_ids = $wpdb->get_col(
        "SELECT meta_value FROM {$wpdb->postmeta} pm
         INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
         WHERE pm.meta_key = '_subsidy_portal_id'
         AND p.post_type = 'subsidy'
         AND p.post_status IN ('publish', 'draft')"
    );
    $existing_map = array_flip($existing_portal_ids);
    unset($existing_portal_ids);

    // 既存タイトル一覧もプリロード（portal_id がない場合のフォールバック）
    WP_CLI::log("既存データのタイトルを取得中...");
    $existing_titles = $wpdb->get_col(
        "SELECT post_title FROM {$wpdb->posts}
         WHERE post_type = 'subsidy'
         AND post_status IN ('publish', 'draft')"
    );
    $title_map = array_flip($existing_titles);
    unset($existing_titles);

    $existing_count = count($existing_map) + count($title_map);
    WP_CLI::log("既存投稿: portal_id={$existing_count} 件をキャッシュ済み");

    // パフォーマンス最適化
    wp_defer_term_counting(true);
    wp_defer_comment_counting(true);
    $wpdb->query('SET autocommit = 0');

    $imported = 0;
    $skipped  = 0;
    $errors   = 0;
    $batch_size = 1000;

    for ($i = 0; $i < $total; $i++) {
        $item = $items[$i];

        $title = isset($item['title']) ? trim($item['title']) : '';
        if (empty($title)) {
            $skipped++;
            continue;
        }

        $portal_id = isset($item['portal_id']) ? trim($item['portal_id']) : '';

        // 重複チェック: portal_id 優先、なければタイトル
        if ($portal_id && isset($existing_map[$portal_id])) {
            $skipped++;
            continue;
        }
        if (!$portal_id && isset($title_map[$title])) {
            $skipped++;
            continue;
        }

        // ステータス判定
        $status_text = isset($item['status']) ? $item['status'] : '';
        $post_status = ($status_text === 'active' || $status_text === 'upcoming') ? 'publish' : 'draft';

        // 本文構築
        $content = '';
        if (!empty($item['summary'])) {
            $content = '<p>' . esc_html($item['summary']) . '</p>';
        }

        // 投稿作成
        $post_id = wp_insert_post(array(
            'post_title'   => $title,
            'post_type'    => 'subsidy',
            'post_status'  => $post_status,
            'post_content' => $content,
        ), true);

        if (is_wp_error($post_id)) {
            $errors++;
            if ($errors <= 10) {
                WP_CLI::warning("エラー [{$title}]: " . $post_id->get_error_message());
            }
            continue;
        }

        // メタフィールド設定
        $meta = array(
            '_subsidy_portal_id'           => $portal_id,
            '_subsidy_region'              => isset($item['region']) ? $item['region'] : '',
            '_subsidy_max_amount'          => isset($item['max_amount']) ? (int) $item['max_amount'] : 0,
            '_subsidy_amount_text'         => isset($item['amount_text']) ? $item['amount_text'] : '',
            '_subsidy_status'              => $status_text,
            '_subsidy_application_period'  => isset($item['application_period']) ? $item['application_period'] : '',
            '_subsidy_detail_url'          => isset($item['detail_url']) ? esc_url_raw($item['detail_url']) : '',
            '_subsidy_target_type'         => isset($item['target_type']) ? $item['target_type'] : '',
            '_subsidy_implementing_agency' => isset($item['implementing_agency']) ? $item['implementing_agency'] : '',
            '_subsidy_purpose'             => isset($item['purpose']) ? $item['purpose'] : '',
            '_subsidy_eligible_entities'   => isset($item['eligible_entities']) ? $item['eligible_entities'] : '',
            '_subsidy_eligible_expenses'   => isset($item['eligible_expenses']) ? $item['eligible_expenses'] : '',
            '_subsidy_subsidy_rate'        => isset($item['subsidy_rate_detail']) ? $item['subsidy_rate_detail'] : (isset($item['subsidy_rate']) ? $item['subsidy_rate'] : ''),
            '_subsidy_summary'             => isset($item['summary']) ? $item['summary'] : '',
            '_subsidy_official_url'        => isset($item['official_url']) ? esc_url_raw($item['official_url']) : '',
            '_subsidy_tags'                => !empty($item['tags']) ? wp_json_encode($item['tags'], JSON_UNESCAPED_UNICODE) : '',
            '_subsidy_data_source'         => 'hojyokin-portal',
            '_subsidy_imported_at'         => current_time('mysql'),
        );

        // 地域コード変換 (_subsidy_target_regions)
        $region = isset($item['region']) ? $item['region'] : '';
        $region_code = subsidy_bulk_region_to_code($region);
        $meta['_subsidy_target_regions'] = serialize(array($region_code));

        foreach ($meta as $key => $value) {
            if ($value !== '' && $value !== 0 && $value !== null) {
                update_post_meta($post_id, $key, $value);
            }
        }

        // 重複マップに追加
        if ($portal_id) {
            $existing_map[$portal_id] = true;
        }
        $title_map[$title] = true;

        $imported++;

        // バッチコミット & メモリ管理
        if (($imported + $skipped + $errors) % $batch_size === 0) {
            $wpdb->query('COMMIT');
            $wpdb->query('SET autocommit = 0');

            // WP オブジェクトキャッシュをクリア
            wp_cache_flush();

            $processed = $i + 1;
            $mem = round(memory_get_usage(true) / 1024 / 1024);
            WP_CLI::log("Imported: {$imported}/{$total} (processed: {$processed}, skipped: {$skipped}, errors: {$errors}, mem: {$mem}MB)");
        }
    }

    // 最終コミット
    $wpdb->query('COMMIT');
    $wpdb->query('SET autocommit = 1');

    wp_defer_term_counting(false);
    wp_defer_comment_counting(false);

    WP_CLI::success("インポート完了: 新規={$imported}, スキップ={$skipped}, エラー={$errors} / 総件数={$total}");
}

/**
 * 地域名を都道府県コードに変換
 */
function subsidy_bulk_region_to_code($region) {
    $map = array(
        '全国' => 'all',
        '北海道' => '01', '青森' => '02', '岩手' => '03', '宮城' => '04',
        '秋田' => '05', '山形' => '06', '福島' => '07', '茨城' => '08',
        '栃木' => '09', '群馬' => '10', '埼玉' => '11', '千葉' => '12',
        '東京' => '13', '神奈川' => '14', '新潟' => '15', '富山' => '16',
        '石川' => '17', '福井' => '18', '山梨' => '19', '長野' => '20',
        '岐阜' => '21', '静岡' => '22', '愛知' => '23', '三重' => '24',
        '滋賀' => '25', '京都' => '26', '大阪' => '27', '兵庫' => '28',
        '奈良' => '29', '和歌山' => '30', '鳥取' => '31', '島根' => '32',
        '岡山' => '33', '広島' => '34', '山口' => '35', '徳島' => '36',
        '香川' => '37', '愛媛' => '38', '高知' => '39', '福岡' => '40',
        '佐賀' => '41', '長崎' => '42', '熊本' => '43', '大分' => '44',
        '宮崎' => '45', '鹿児島' => '46', '沖縄' => '47',
    );

    foreach ($map as $name => $code) {
        if (mb_strpos($region, $name) !== false) {
            return $code;
        }
    }

    return 'all';
}

// 実行
subsidy_bulk_import_main();
