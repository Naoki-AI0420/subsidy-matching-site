<?php
/**
 * REST API エンドポイント
 *
 * @package SubsidyMatch
 */

/**
 * API ルート登録
 */
function subsidy_match_register_routes() {
    // マッチング実行（6問版）
    register_rest_route('subsidy/v1', '/match', array(
        'methods'             => 'POST',
        'callback'            => 'subsidy_match_handle_match',
        'permission_callback' => '__return_true',
    ));

    // リード登録（結果画面のリードゲート）
    register_rest_route('subsidy/v1', '/register-lead', array(
        'methods'             => 'POST',
        'callback'            => 'subsidy_match_handle_register_lead',
        'permission_callback' => '__return_true',
    ));

    // お問い合わせ
    register_rest_route('subsidy/v1', '/contact', array(
        'methods'             => 'POST',
        'callback'            => 'subsidy_match_handle_contact',
        'permission_callback' => '__return_true',
    ));

    // 補助金一覧（フィルタ付き）
    register_rest_route('subsidy/v1', '/subsidies', array(
        'methods'             => 'GET',
        'callback'            => 'subsidy_match_handle_list',
        'permission_callback' => '__return_true',
    ));

    // 今月申請期限の補助金件数
    register_rest_route('subsidy/v1', '/deadline-count', array(
        'methods'             => 'GET',
        'callback'            => 'subsidy_match_handle_deadline_count',
        'permission_callback' => '__return_true',
    ));

    // 統計情報
    register_rest_route('subsidy/v1', '/stats', array(
        'methods'             => 'GET',
        'callback'            => 'subsidy_match_handle_stats',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'subsidy_match_register_routes');

/**
 * 都道府県名マッピング
 */
function subsidy_match_get_pref_map() {
    return array(
        '北海道'=>'北海道','青森県'=>'青森県','岩手県'=>'岩手県','宮城県'=>'宮城県','秋田県'=>'秋田県',
        '山形県'=>'山形県','福島県'=>'福島県','茨城県'=>'茨城県','栃木県'=>'栃木県','群馬県'=>'群馬県',
        '埼玉県'=>'埼玉県','千葉県'=>'千葉県','東京都'=>'東京都','神奈川県'=>'神奈川県','新潟県'=>'新潟県',
        '富山県'=>'富山県','石川県'=>'石川県','福井県'=>'福井県','山梨県'=>'山梨県','長野県'=>'長野県',
        '岐阜県'=>'岐阜県','静岡県'=>'静岡県','愛知県'=>'愛知県','三重県'=>'三重県','滋賀県'=>'滋賀県',
        '京都府'=>'京都府','大阪府'=>'大阪府','兵庫県'=>'兵庫県','奈良県'=>'奈良県','和歌山県'=>'和歌山県',
        '鳥取県'=>'鳥取県','島根県'=>'島根県','岡山県'=>'岡山県','広島県'=>'広島県','山口県'=>'山口県',
        '徳島県'=>'徳島県','香川県'=>'香川県','愛媛県'=>'愛媛県','高知県'=>'高知県','福岡県'=>'福岡県',
        '佐賀県'=>'佐賀県','長崎県'=>'長崎県','熊本県'=>'熊本県','大分県'=>'大分県','宮崎県'=>'宮崎県',
        '鹿児島県'=>'鹿児島県','沖縄県'=>'沖縄県',
    );
}

/**
 * マッチング処理（6問版）
 */
function subsidy_match_handle_match($request) {
    $params = $request->get_json_params();

    $prefecture          = sanitize_text_field($params['prefecture'] ?? '');
    $city                = sanitize_text_field($params['city'] ?? '');
    $industry            = sanitize_text_field($params['industry'] ?? '');
    $capital             = sanitize_text_field($params['capital'] ?? '');
    $employee_size       = sanitize_text_field($params['employee_size'] ?? '');
    $establishment_years = sanitize_text_field($params['establishment_years'] ?? '');

    $pref_map = subsidy_match_get_pref_map();
    $prefecture_name = isset($pref_map[$prefecture]) ? $pref_map[$prefecture] : $prefecture;

    if (empty($prefecture)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => '都道府県を入力してください。',
        ), 400);
    }

    // 全補助金取得
    $subsidies = get_posts(array(
        'post_type'      => 'subsidy',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ));

    $results = array();

    foreach ($subsidies as $post) {
        $score = 0;

        // 地域マッチ（25点）
        $target_regions = get_post_meta($post->ID, '_subsidy_target_regions', true);
        $target_regions = is_array($target_regions) ? $target_regions : array();
        $single_region  = get_post_meta($post->ID, '_subsidy_region', true);

        $region_matched = false;
        if (!empty($target_regions)) {
            $region_matched = in_array('all', $target_regions) || in_array($prefecture, $target_regions) || in_array($prefecture_name, $target_regions);
            if ($region_matched) $score += 25;
        } elseif (!empty($single_region)) {
            $region_matched = ($single_region === '全国' || $single_region === $prefecture_name || strpos($single_region, $prefecture_name) !== false);
            if (!$region_matched && mb_strlen($prefecture_name) > 2) {
                $pref_short = mb_substr($prefecture_name, 0, mb_strlen($prefecture_name) - 1);
                $region_matched = (strpos($single_region, $pref_short) !== false);
            }
            if ($region_matched) $score += 25;
        } else {
            $region_matched = true;
            $score += 25;
        }

        if (!$region_matched) continue;

        // 個人向け補助金を除外
        $target_type = get_post_meta($post->ID, '_subsidy_target_type', true);
        if ($target_type === 'personal') continue;

        // 公募中のみ優先（公募終了は除外）
        $subsidy_status = get_post_meta($post->ID, '_subsidy_status', true);
        if ($subsidy_status === 'closed') continue;

        // 業種マッチ（25点）
        $target_industries = get_post_meta($post->ID, '_subsidy_target_industries', true);
        $target_industries = is_array($target_industries) ? $target_industries : array();

        // 補助金のタイトル・概要から業種関連キーワードを判定
        $title_content = $post->post_title . ' ' . $post->post_content;
        $industry_label = '';
        $industry_map = array(
            'food_service' => array('飲食', '食品', 'レストラン', 'カフェ'),
            'manufacturing' => array('製造', 'ものづくり', '工場', '金属'),
            'construction' => array('建設', '建築', '工事', '土木'),
            'wholesale_retail' => array('小売', '商店', '店舗', '販売'),
            'medical_welfare' => array('医療', '介護', '福祉', '病院'),
            'accommodation' => array('宿泊', 'ホテル', '旅館', '民泊'),
            'transportation' => array('運輸', '物流', '運送', 'タクシー'),
            'information_technology' => array('IT', 'ソフトウェア', 'システム', 'プログラム'),
            'agriculture' => array('農業', '農家', '畜産', '漁業', '林業'),
            'education' => array('教育', '学習', '塾', 'スクール'),
            'real_estate' => array('不動産', '賃貸', 'マンション'),
            'professional_services' => array('士業', '会計', '法律', 'コンサル'),
        );

        $industry_keyword_match = false;
        if (!empty($industry) && isset($industry_map[$industry])) {
            foreach ($industry_map[$industry] as $kw) {
                if (mb_strpos($title_content, $kw) !== false) {
                    $industry_keyword_match = true;
                    break;
                }
            }
        }

        if (!empty($target_industries) && in_array($industry, $target_industries)) {
            $score += 25;
        } elseif ($industry_keyword_match) {
            $score += 20; // タイトル・概要に業種キーワードがある
        } elseif (empty($target_industries)) {
            $score += 5; // タグなしは大幅減点（全部マッチさせない）
        }

        // 従業員規模マッチ（20点）
        $target_emp = get_post_meta($post->ID, '_subsidy_target_employee_size', true);
        $target_emp = is_array($target_emp) ? $target_emp : array();
        if (!empty($target_emp) && in_array($employee_size, $target_emp)) {
            $score += 20;
        } elseif (empty($target_emp)) {
            $score += 5; // タグなしは大幅減点
        }

        // 資本金マッチ（15点）
        $target_cap = get_post_meta($post->ID, '_subsidy_target_capital', true);
        $target_cap = is_array($target_cap) ? $target_cap : array();
        if (!empty($target_cap) && in_array($capital, $target_cap)) {
            $score += 15;
        } elseif (empty($target_cap)) {
            $score += 3; // タグなしは大幅減点
        }

        // 設立年数ボーナス（15点）
        $is_startup_subsidy = (
            strpos($post->post_title, '創業') !== false ||
            strpos($post->post_title, 'スタートアップ') !== false ||
            strpos($post->post_title, '起業') !== false
        );
        if (in_array($establishment_years, array('under_1y', '1_3y')) && $is_startup_subsidy) {
            $score += 15;
        } elseif (!$is_startup_subsidy) {
            $score += 3; // 一般補助金は設立年数ボーナス小さく
        }

        // 適合度
        if ($score >= 65) {
            $match_level = 'high';
        } elseif ($score >= 45) {
            $match_level = 'medium';
        } else {
            $match_level = 'low';
        }

        if ($score < 35) continue; // 閾値を上げて無関係な補助金を除外

        $adoption_rate = get_post_meta($post->ID, '_subsidy_adoption_rate', true);
        $adoption_rate = $adoption_rate ? (float) $adoption_rate : null;
        $max_amount    = (int) get_post_meta($post->ID, '_subsidy_max_amount', true);

        $results[] = array(
            'id'                  => $post->ID,
            'title'               => $post->post_title,
            'max_amount'          => $max_amount,
            'rate'                => get_post_meta($post->ID, '_subsidy_subsidy_rate', true)
                                     ?: get_post_meta($post->ID, '_subsidy_rate', true),
            'summary'             => get_post_meta($post->ID, '_subsidy_summary', true)
                                     ?: wp_strip_all_tags($post->post_content),
            'deadline'            => get_post_meta($post->ID, '_subsidy_deadline', true)
                                     ?: get_post_meta($post->ID, '_subsidy_application_period', true),
            'official_url'        => get_post_meta($post->ID, '_subsidy_official_url', true)
                                     ?: get_post_meta($post->ID, '_subsidy_detail_url', true),
            'score'               => $score,
            'match_level'         => $match_level,
            'adoption_rate'       => $adoption_rate,
            'eligible_entities'   => get_post_meta($post->ID, '_subsidy_eligible_entities', true),
            'purpose'             => get_post_meta($post->ID, '_subsidy_purpose', true),
            'eligible_expenses'   => get_post_meta($post->ID, '_subsidy_eligible_expenses', true),
            'implementing_agency' => get_post_meta($post->ID, '_subsidy_implementing_agency', true),
            'amount_text'         => get_post_meta($post->ID, '_subsidy_amount_text', true),
        );
    }

    // スコア降順ソート
    usort($results, function ($a, $b) {
        if ($b['score'] !== $a['score']) return $b['score'] - $a['score'];
        $rate_a = $a['adoption_rate'] ?? 0;
        $rate_b = $b['adoption_rate'] ?? 0;
        return $rate_b <=> $rate_a;
    });

    return new WP_REST_Response(array(
        'success' => true,
        'results' => $results,
    ), 200);
}

/**
 * リード登録（結果画面のリードゲート + 通知）
 */
function subsidy_match_handle_register_lead($request) {
    $params = $request->get_json_params();

    $company_name = sanitize_text_field($params['company_name'] ?? '');
    $contact_name = sanitize_text_field($params['contact_name'] ?? '');
    $phone        = sanitize_text_field($params['phone'] ?? '');
    $email        = sanitize_email($params['email'] ?? '');

    if (empty($company_name) || empty($contact_name) || empty($phone) || empty($email)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => '必須項目を入力してください。',
        ), 400);
    }

    // 診断データ
    $prefecture          = sanitize_text_field($params['prefecture'] ?? '');
    $city                = sanitize_text_field($params['city'] ?? '');
    $industry            = sanitize_text_field($params['industry'] ?? '');
    $capital             = sanitize_text_field($params['capital'] ?? '');
    $employee_size       = sanitize_text_field($params['employee_size'] ?? '');
    $establishment_years = sanitize_text_field($params['establishment_years'] ?? '');
    $matched_count       = (int) ($params['matched_count'] ?? 0);
    $matched_ids         = isset($params['matched_ids']) ? wp_json_encode($params['matched_ids']) : '';
    $matched_subsidies   = $params['matched_subsidies'] ?? array();

    $lead_id = subsidy_match_save_lead(array(
        'company_name'        => $company_name,
        'contact_name'        => $contact_name,
        'phone'               => $phone,
        'email'               => $email,
        'prefecture'          => $prefecture,
        'city'                => $city,
        'industry'            => $industry,
        'capital'             => $capital,
        'employee_size'       => $employee_size,
        'establishment_years' => $establishment_years,
        'matched_count'       => $matched_count,
        'matched_ids'         => $matched_ids,
    ));

    // 通知を非同期的に送信（エラーがあってもリードは保存済み）
    subsidy_match_send_notifications($company_name, $contact_name, $phone, $email, $industry, $matched_count, $matched_subsidies);

    return new WP_REST_Response(array(
        'success' => true,
        'lead_id' => $lead_id,
    ), 200);
}

/**
 * 通知送信（メール + Discord + 自動返信）
 */
function subsidy_match_send_notifications($company, $name, $phone, $email, $industry, $matched_count, $matched_subsidies) {
    $industries = subsidy_match_get_industries();
    $industry_label = $industries[$industry] ?? $industry;

    // 1. 管理者メール通知
    $notification_email = subsidy_match_get_notification_email();
    $subject = "【新規リード】{$company} - 補助金マッチング";
    $body = "新しいリードが登録されました。\n\n"
          . "会社名: {$company}\n"
          . "担当者: {$name}\n"
          . "電話番号: {$phone}\n"
          . "メール: {$email}\n"
          . "業種: {$industry_label}\n"
          . "マッチ件数: {$matched_count}件\n"
          . "\n---\n"
          . "補助金マッチングサイト\n";

    wp_mail($notification_email, $subject, $body);

    // 2. Discord Webhook 通知
    $discord_url = subsidy_match_get_discord_webhook();
    if ($discord_url) {
        $discord_body = array(
            'content' => "**【新規リード】** {$company}\n"
                       . "担当者: {$name}\n"
                       . "TEL: {$phone} / Mail: {$email}\n"
                       . "業種: {$industry_label} / マッチ: {$matched_count}件",
        );
        wp_remote_post($discord_url, array(
            'timeout' => 10,
            'headers' => array('Content-Type' => 'application/json'),
            'body'    => wp_json_encode($discord_body),
        ));
    }

    // 3. 自動返信メール
    $top_subsidies = '';
    $subsidy_list = is_array($matched_subsidies) ? array_slice($matched_subsidies, 0, 3) : array();
    foreach ($subsidy_list as $s) {
        $s_title  = sanitize_text_field($s['title'] ?? '');
        $s_amount = sanitize_text_field($s['amount_text'] ?? '');
        if (!$s_amount && !empty($s['max_amount'])) {
            $amt = (int) $s['max_amount'];
            $s_amount = $amt >= 10000 ? number_format($amt / 10000) . '万円' : number_format($amt) . '円';
        }
        $top_subsidies .= "  - {$s_title}（最大 {$s_amount}）\n";
    }

    $reply_subject = "【補助金マッチング】診断結果のご案内 - 株式会社Growing up";
    $reply_body = "{$name}様\n\n"
                . "この度は補助金マッチング診断をご利用いただき、誠にありがとうございます。\n\n"
                . "診断の結果、{$name}様の企業に該当する可能性のある補助金が {$matched_count}件 見つかりました。\n\n";

    if ($top_subsidies) {
        $reply_body .= "【マッチした補助金（上位3件）】\n{$top_subsidies}\n";
    }

    $reply_body .= "詳しい活用方法について、無料でご相談を承っております。\n"
                 . "お気軽にお問い合わせください。\n\n"
                 . "---\n"
                 . "株式会社Growing up\n"
                 . "メール: info@ai-growing-up.co.jp\n"
                 . "Web: https://ai-growing-up.co.jp/\n";

    $reply_headers = array(
        'From: 株式会社Growing up <info@ai-growing-up.co.jp>',
    );
    wp_mail($email, $reply_subject, $reply_body, $reply_headers);
}

/**
 * system_interest を更新
 */
function subsidy_match_register_system_interest_route() {
    register_rest_route('subsidy/v1', '/system-interest', array(
        'methods'             => 'POST',
        'callback'            => 'subsidy_match_handle_system_interest',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'subsidy_match_register_system_interest_route');

function subsidy_match_handle_system_interest($request) {
    $params  = $request->get_json_params();
    $lead_id = (int) ($params['lead_id'] ?? 0);
    $value   = sanitize_text_field($params['system_interest'] ?? '');

    if (!$lead_id || !in_array($value, array('yes', 'no', 'undecided'))) {
        return new WP_REST_Response(array('success' => false), 400);
    }

    global $wpdb;
    $table = $wpdb->prefix . 'leads';
    $wpdb->update($table, array('system_interest' => $value), array('id' => $lead_id), array('%s'), array('%d'));

    return new WP_REST_Response(array('success' => true), 200);
}

/**
 * お問い合わせ処理
 */
function subsidy_match_handle_contact($request) {
    $params = $request->get_json_params();

    $company = sanitize_text_field($params['company_name'] ?? '');
    $name    = sanitize_text_field($params['contact_name'] ?? '');
    $email   = sanitize_email($params['email'] ?? '');
    $phone   = sanitize_text_field($params['phone'] ?? '');
    $message = sanitize_textarea_field($params['message'] ?? '');

    if (empty($company) || empty($name) || empty($email) || empty($message)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => '必須項目を入力してください。',
        ), 400);
    }

    $admin_email = get_option('admin_email');
    $subject     = '【補助金マッチングサイト】お問い合わせ: ' . $company;
    $body        = "会社名: {$company}\n"
                 . "担当者: {$name}\n"
                 . "メール: {$email}\n"
                 . "電話: {$phone}\n\n"
                 . "ご相談内容:\n{$message}\n";

    $headers = array('Reply-To: ' . $email);
    wp_mail($admin_email, $subject, $body, $headers);

    return new WP_REST_Response(array(
        'success' => true,
        'message' => 'お問い合わせを受け付けました。',
    ), 200);
}

/**
 * 補助金一覧取得（フィルタ付き）
 */
function subsidy_match_handle_list($request) {
    $page     = max(1, (int) $request->get_param('page'));
    $per_page = min(100, max(1, (int) ($request->get_param('per_page') ?: 20)));
    $region   = sanitize_text_field($request->get_param('region') ?: '');
    $search   = sanitize_text_field($request->get_param('search') ?: '');
    $source   = sanitize_text_field($request->get_param('source') ?: '');

    $args = array(
        'post_type'      => 'subsidy',
        'posts_per_page' => $per_page,
        'paged'          => $page,
        'post_status'    => 'publish',
        'orderby'        => 'meta_value_num',
        'meta_key'       => '_subsidy_match_priority',
        'order'          => 'DESC',
    );

    if ($search) {
        $args['s'] = $search;
    }

    $meta_query = array('relation' => 'AND');

    if ($region) {
        $meta_query[] = array(
            'relation' => 'OR',
            array('key' => '_subsidy_target_regions', 'value' => $region, 'compare' => 'LIKE'),
            array('key' => '_subsidy_target_regions', 'value' => 'all', 'compare' => 'LIKE'),
        );
    }

    if ($source) {
        $meta_query[] = array('key' => '_subsidy_data_source', 'value' => $source);
    }

    if (count($meta_query) > 1) {
        $args['meta_query'] = $meta_query;
    }

    $query = new WP_Query($args);
    $items = array();

    foreach ($query->posts as $post) {
        $items[] = array(
            'id'           => $post->ID,
            'title'        => $post->post_title,
            'max_amount'   => (int) get_post_meta($post->ID, '_subsidy_max_amount', true),
            'rate'         => get_post_meta($post->ID, '_subsidy_subsidy_rate', true) ?: get_post_meta($post->ID, '_subsidy_rate', true),
            'summary'      => get_post_meta($post->ID, '_subsidy_summary', true) ?: wp_strip_all_tags($post->post_content),
            'deadline'     => get_post_meta($post->ID, '_subsidy_deadline', true) ?: get_post_meta($post->ID, '_subsidy_application_period', true),
            'official_url' => get_post_meta($post->ID, '_subsidy_official_url', true) ?: get_post_meta($post->ID, '_subsidy_detail_url', true),
            'region'       => get_post_meta($post->ID, '_subsidy_target_regions', true),
            'source'       => get_post_meta($post->ID, '_subsidy_data_source', true),
        );
    }

    return new WP_REST_Response(array(
        'success'  => true,
        'items'    => $items,
        'total'    => (int) $query->found_posts,
        'pages'    => (int) $query->max_num_pages,
        'page'     => $page,
        'per_page' => $per_page,
    ), 200);
}

/**
 * 統計情報
 */
function subsidy_match_handle_stats($request) {
    $total = wp_count_posts('subsidy');
    $published = (int) $total->publish;

    global $wpdb;
    $sources = $wpdb->get_results(
        "SELECT meta_value AS source, COUNT(*) AS count FROM {$wpdb->postmeta} WHERE meta_key = '_subsidy_data_source' GROUP BY meta_value",
        ARRAY_A
    );

    $regions = $wpdb->get_results(
        "SELECT meta_value AS region, COUNT(*) AS count FROM {$wpdb->postmeta} WHERE meta_key = '_subsidy_target_regions' GROUP BY meta_value ORDER BY count DESC LIMIT 10",
        ARRAY_A
    );

    return new WP_REST_Response(array(
        'success'     => true,
        'total'       => $published,
        'by_source'   => $sources ?: array(),
        'by_region'   => $regions ?: array(),
        'last_import' => get_option('subsidy_last_import_at', ''),
    ), 200);
}

/**
 * 今月申請期限の補助金件数
 */
function subsidy_match_handle_deadline_count($request) {
    $now = current_time('Y-m');
    $first_day = $now . '-01';
    $last_day  = date('Y-m-t', strtotime($first_day));

    $subsidies = get_posts(array(
        'post_type'      => 'subsidy',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_query'     => array(array('key' => '_subsidy_deadline', 'compare' => 'EXISTS')),
    ));

    $count = 0;
    foreach ($subsidies as $post) {
        $deadline = get_post_meta($post->ID, '_subsidy_deadline', true);
        if (empty($deadline)) continue;

        $deadline_normalized = str_replace(array('年', '月', '日'), array('-', '-', ''), $deadline);
        $deadline_ts = strtotime($deadline_normalized);
        if (!$deadline_ts) continue;

        $deadline_date = date('Y-m-d', $deadline_ts);
        if ($deadline_date >= $first_day && $deadline_date <= $last_day) $count++;
    }

    return new WP_REST_Response(array('success' => true, 'count' => $count, 'month' => $now), 200);
}

/**
 * 売上テキストを数値（円）に変換
 */
function subsidy_match_parse_revenue($text) {
    if (empty($text)) return 0;
    $text = str_replace(array(',', ' ', '　'), '', $text);
    if (preg_match('/(\d+(?:\.\d+)?)\s*億/', $text, $m)) return (int) ($m[1] * 100000000);
    if (preg_match('/(\d+(?:\.\d+)?)\s*万/', $text, $m)) return (int) ($m[1] * 10000);
    if (preg_match('/(\d+)/', $text, $m)) return (int) $m[1];
    return 0;
}
