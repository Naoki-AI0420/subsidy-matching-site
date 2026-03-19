#!/usr/bin/env php
<?php
/**
 * 補助金データ WordPress インポートスクリプト
 *
 * JSON データを WordPress カスタム投稿タイプ「subsidy」にインポートする。
 * WP-CLI 経由で実行: wp eval-file scripts/import-to-wordpress.php
 *
 * @package SubsidyMatch
 */

if (!defined('ABSPATH')) {
    // WP-CLI 以外からの実行を防止
    if (php_sapi_name() !== 'cli') {
        die('WP-CLI 経由で実行してください: wp eval-file scripts/import-to-wordpress.php');
    }
}

/**
 * メイン処理
 */
function subsidy_import_main() {
    $base_dir = dirname(__FILE__) . '/../data';

    // インポート対象ファイル
    $files = array(
        'subsidies.json'          => 'hojyokin-portal',
        'mirasapo-subsidies.json' => 'mirasapo',
    );

    // 採択率データ読み込み
    $adoption_rates = array();
    $rates_file = $base_dir . '/adoption-rates.json';
    if (file_exists($rates_file)) {
        $rates_data = json_decode(file_get_contents($rates_file), true);
        if ($rates_data && isset($rates_data['subsidies'])) {
            $adoption_rates = $rates_data['subsidies'];
        }
        WP_CLI::log("採択率データ読み込み完了");
    }

    $imported = 0;
    $updated  = 0;
    $skipped  = 0;
    $errors   = 0;

    foreach ($files as $filename => $source) {
        $filepath = $base_dir . '/' . $filename;

        if (!file_exists($filepath)) {
            WP_CLI::warning("ファイルが見つかりません: {$filepath}");
            continue;
        }

        $json = file_get_contents($filepath);
        $data = json_decode($json, true);

        if (!$data || !isset($data['items'])) {
            WP_CLI::warning("無効なJSONデータ: {$filepath}");
            continue;
        }

        WP_CLI::log("--- {$source} からインポート開始 ({$data['total_count']} 件) ---");

        foreach ($data['items'] as $item) {
            $result = subsidy_import_item($item, $source, $adoption_rates);

            switch ($result) {
                case 'imported':
                    $imported++;
                    break;
                case 'updated':
                    $updated++;
                    break;
                case 'skipped':
                    $skipped++;
                    break;
                case 'error':
                    $errors++;
                    break;
            }
        }
    }

    WP_CLI::success("インポート完了: 新規={$imported}, 更新={$updated}, スキップ={$skipped}, エラー={$errors}");
}

/**
 * 個別アイテムのインポート
 */
function subsidy_import_item($item, $source, $adoption_rates) {
    $title = isset($item['title']) ? trim($item['title']) : '';
    $name  = isset($item['name']) ? trim($item['name']) : '';

    $post_title = $title ?: $name;
    if (empty($post_title)) {
        return 'skipped';
    }

    // 既存投稿チェック（タイトルで重複判定）
    $existing = get_posts(array(
        'post_type'      => 'subsidy',
        'title'          => $post_title,
        'posts_per_page' => 1,
        'post_status'    => array('publish', 'draft'),
    ));

    $is_update = !empty($existing);
    $post_id   = $is_update ? $existing[0]->ID : 0;

    // 投稿データ
    $post_data = array(
        'post_title'   => $post_title,
        'post_type'    => 'subsidy',
        'post_status'  => subsidy_import_get_status($item),
        'post_content' => subsidy_import_build_content($item),
    );

    if ($is_update) {
        $post_data['ID'] = $post_id;
        $result = wp_update_post($post_data, true);
    } else {
        $result = wp_insert_post($post_data, true);
    }

    if (is_wp_error($result)) {
        WP_CLI::warning("エラー [{$post_title}]: " . $result->get_error_message());
        return 'error';
    }

    $post_id = $result;

    // メタデータ設定
    subsidy_import_set_meta($post_id, $item, $source, $adoption_rates);

    $action = $is_update ? '更新' : '新規';
    WP_CLI::log("  [{$action}] {$post_title} (ID: {$post_id})");

    return $is_update ? 'updated' : 'imported';
}

/**
 * 投稿ステータス判定
 */
function subsidy_import_get_status($item) {
    $status = isset($item['status']) ? $item['status'] : '';

    if ($status === 'active' || $status === '公募中') {
        return 'publish';
    }

    if ($status === 'upcoming' || $status === '公募予定') {
        return 'publish';
    }

    // ミラサポの主要補助金は常に公開
    if (isset($item['id'])) {
        return 'publish';
    }

    return 'draft';
}

/**
 * 投稿本文を構築
 */
function subsidy_import_build_content($item) {
    $parts = array();

    if (!empty($item['summary'])) {
        $parts[] = '<p>' . esc_html($item['summary']) . '</p>';
    }

    if (!empty($item['meta_description'])) {
        $parts[] = '<p>' . esc_html($item['meta_description']) . '</p>';
    }

    if (!empty($item['target_description'])) {
        $parts[] = '<h3>対象者</h3>';
        $parts[] = '<p>' . esc_html($item['target_description']) . '</p>';
    }

    if (!empty($item['categories'])) {
        $parts[] = '<h3>申請枠・類型</h3>';
        $parts[] = '<ul>';
        foreach ($item['categories'] as $cat) {
            $parts[] = '<li>' . esc_html($cat) . '</li>';
        }
        $parts[] = '</ul>';
    }

    if (!empty($item['amount_note'])) {
        $parts[] = '<p><strong>備考:</strong> ' . esc_html($item['amount_note']) . '</p>';
    }

    return implode("\n", $parts);
}

/**
 * メタデータ設定
 */
function subsidy_import_set_meta($post_id, $item, $source, $adoption_rates) {
    // 最大金額
    $max_amount = 0;
    if (!empty($item['max_amount_scraped'])) {
        $max_amount = (int) $item['max_amount_scraped'];
    } elseif (!empty($item['max_amount'])) {
        $max_amount = (int) $item['max_amount'];
    }
    if ($max_amount > 0) {
        update_post_meta($post_id, '_subsidy_max_amount', $max_amount);
    }

    // 補助率
    $rate = '';
    if (!empty($item['rate_scraped'])) {
        $rate = $item['rate_scraped'];
    } elseif (!empty($item['rate'])) {
        $rate = $item['rate'];
    }
    if ($rate) {
        update_post_meta($post_id, '_subsidy_rate', sanitize_text_field($rate));
    }

    // 概要
    $summary = '';
    if (!empty($item['summary'])) {
        $summary = $item['summary'];
    } elseif (!empty($item['meta_description'])) {
        $summary = $item['meta_description'];
    }
    if ($summary) {
        update_post_meta($post_id, '_subsidy_summary', sanitize_text_field(mb_substr($summary, 0, 200)));
    }

    // 申請期限
    if (!empty($item['deadline'])) {
        update_post_meta($post_id, '_subsidy_deadline', sanitize_text_field($item['deadline']));
    } elseif (!empty($item['application_end'])) {
        update_post_meta($post_id, '_subsidy_deadline', sanitize_text_field($item['application_end']));
    }

    // 公式URL
    $url = '';
    if (!empty($item['detail_url'])) {
        $url = $item['detail_url'];
    } elseif (!empty($item['url'])) {
        $url = $item['url'];
    } elseif (!empty($item['official_url'])) {
        $url = $item['official_url'];
    }
    if ($url) {
        update_post_meta($post_id, '_subsidy_official_url', esc_url_raw($url));
    }

    // 対象業種
    if (!empty($item['target_industries']) && is_array($item['target_industries'])) {
        update_post_meta($post_id, '_subsidy_target_industries', $item['target_industries']);
    }

    // 対象地域（デフォルト: 全国）
    if (!empty($item['target_regions']) && is_array($item['target_regions'])) {
        update_post_meta($post_id, '_subsidy_target_regions', $item['target_regions']);
    } else {
        // 地域情報がない場合は全国として設定
        $region = !empty($item['region']) ? subsidy_import_region_to_code($item['region']) : 'all';
        update_post_meta($post_id, '_subsidy_target_regions', array($region));
    }

    // 対象従業員規模
    if (!empty($item['target_employee_size']) && is_array($item['target_employee_size'])) {
        update_post_meta($post_id, '_subsidy_target_employee_size', $item['target_employee_size']);
    }

    // 対象課題
    if (!empty($item['target_challenges']) && is_array($item['target_challenges'])) {
        update_post_meta($post_id, '_subsidy_target_challenges', $item['target_challenges']);
    }

    // 採択率（カスタムメタとして保存）
    $subsidy_id = isset($item['id']) ? $item['id'] : '';
    if ($subsidy_id && isset($adoption_rates[$subsidy_id])) {
        $rate_data = $adoption_rates[$subsidy_id];
        update_post_meta($post_id, '_subsidy_adoption_rate', $rate_data['average_rate']);
        update_post_meta($post_id, '_subsidy_adoption_rate_detail', wp_json_encode($rate_data));
    }

    // データソース
    update_post_meta($post_id, '_subsidy_data_source', $source);
    update_post_meta($post_id, '_subsidy_imported_at', current_time('mysql'));
}

/**
 * 地域名を都道府県コードに変換
 */
function subsidy_import_region_to_code($region) {
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

    // 部分一致で検索
    foreach ($map as $name => $code) {
        if (mb_strpos($region, $name) !== false) {
            return $code;
        }
    }

    return 'all';
}

// 実行
if (class_exists('WP_CLI')) {
    subsidy_import_main();
} else {
    echo "このスクリプトは WP-CLI 経由で実行してください:\n";
    echo "  wp eval-file scripts/import-to-wordpress.php\n";
    exit(1);
}
