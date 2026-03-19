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
    // マッチング実行
    register_rest_route('subsidy/v1', '/match', array(
        'methods'             => 'POST',
        'callback'            => 'subsidy_match_handle_match',
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

    // 統計情報
    register_rest_route('subsidy/v1', '/stats', array(
        'methods'             => 'GET',
        'callback'            => 'subsidy_match_handle_stats',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'subsidy_match_register_routes');

/**
 * マッチング処理
 */
function subsidy_match_handle_match($request) {
    $params = $request->get_json_params();

    $prefecture     = sanitize_text_field($params['prefecture'] ?? '');
    $industry       = sanitize_text_field($params['industry'] ?? '');
    $employee_size  = sanitize_text_field($params['employee_size'] ?? '');
    $capital        = sanitize_text_field($params['capital'] ?? '');
    $challenges     = isset($params['challenges']) && is_array($params['challenges'])
                        ? array_map('sanitize_text_field', $params['challenges'])
                        : array();
    $annual_revenue = sanitize_text_field($params['annual_revenue'] ?? '');
    $has_experience = isset($params['has_experience']) ? (int) $params['has_experience'] : 0;
    $email          = sanitize_email($params['email'] ?? '');

    // 全補助金取得
    $subsidies = get_posts(array(
        'post_type'      => 'subsidy',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ));

    $results = array();

    // 売上規模から資本金推定によるボーナス判定
    $revenue_scale = subsidy_match_parse_revenue($annual_revenue);

    foreach ($subsidies as $post) {
        $score = 0;

        // 地域マッチ（20点）
        $target_regions = get_post_meta($post->ID, '_subsidy_target_regions', true);
        $target_regions = is_array($target_regions) ? $target_regions : array();
        if (empty($target_regions) || in_array('all', $target_regions) || in_array($prefecture, $target_regions)) {
            $score += 20;
        }

        // 業種マッチ（25点）
        $target_industries = get_post_meta($post->ID, '_subsidy_target_industries', true);
        $target_industries = is_array($target_industries) ? $target_industries : array();
        if (empty($target_industries) || in_array($industry, $target_industries)) {
            $score += 25;
        }

        // 従業員規模マッチ（15点）
        $target_emp = get_post_meta($post->ID, '_subsidy_target_employee_size', true);
        $target_emp = is_array($target_emp) ? $target_emp : array();
        if (empty($target_emp) || in_array($employee_size, $target_emp)) {
            $score += 15;
        }

        // 資本金マッチ（15点）
        $target_cap = get_post_meta($post->ID, '_subsidy_target_capital', true);
        $target_cap = is_array($target_cap) ? $target_cap : array();
        if (empty($target_cap) || in_array($capital, $target_cap)) {
            $score += 15;
        }

        // 課題マッチ（25点）
        $target_challenges = get_post_meta($post->ID, '_subsidy_target_challenges', true);
        $target_challenges = is_array($target_challenges) ? $target_challenges : array();
        if (!empty($challenges) && !empty($target_challenges)) {
            $intersect = array_intersect($challenges, $target_challenges);
            if (count($intersect) > 0) {
                $ratio = count($intersect) / count($target_challenges);
                $score += (int) round(25 * min($ratio * 1.5, 1.0));
            }
        } elseif (empty($target_challenges)) {
            $score += 25;
        }

        // 補助金経験者ボーナス（+5点）
        if ($has_experience && $score >= 40) {
            $score += 5;
        }

        // 売上規模と補助金上限額の適合性ボーナス（+5点）
        $max_amount = (int) get_post_meta($post->ID, '_subsidy_max_amount', true);
        if ($revenue_scale > 0 && $max_amount > 0) {
            // 売上の10%以内の補助金額なら適合性が高い
            if ($max_amount <= $revenue_scale * 0.1) {
                $score += 5;
            }
        }

        // 適合度
        if ($score >= 70) {
            $match_level = 'high';
        } elseif ($score >= 40) {
            $match_level = 'medium';
        } else {
            $match_level = 'low';
        }

        // 低スコアは除外
        if ($score < 30) continue;

        // 採択率データ取得
        $adoption_rate = get_post_meta($post->ID, '_subsidy_adoption_rate', true);
        $adoption_rate = $adoption_rate ? (float) $adoption_rate : null;

        $results[] = array(
            'id'             => $post->ID,
            'title'          => $post->post_title,
            'max_amount'     => $max_amount,
            'rate'           => get_post_meta($post->ID, '_subsidy_rate', true),
            'summary'        => get_post_meta($post->ID, '_subsidy_summary', true),
            'deadline'       => get_post_meta($post->ID, '_subsidy_deadline', true),
            'official_url'   => get_post_meta($post->ID, '_subsidy_official_url', true),
            'score'          => $score,
            'match_level'    => $match_level,
            'adoption_rate'  => $adoption_rate,
        );
    }

    // スコア降順ソート（同スコアの場合は採択率の高い順）
    usort($results, function ($a, $b) {
        if ($b['score'] !== $a['score']) {
            return $b['score'] - $a['score'];
        }
        $rate_a = $a['adoption_rate'] ?? 0;
        $rate_b = $b['adoption_rate'] ?? 0;
        return $rate_b <=> $rate_a;
    });

    // リード保存
    $lead_id = 0;
    if ($email) {
        $lead_id = subsidy_match_save_lead(array(
            'email'          => $email,
            'prefecture'     => $prefecture,
            'industry'       => $industry,
            'employee_size'  => $employee_size,
            'capital'        => $capital,
            'challenges'     => wp_json_encode($challenges),
            'annual_revenue' => $annual_revenue,
            'has_experience' => $has_experience,
            'matched_ids'    => wp_json_encode(array_column($results, 'id')),
        ));
    }

    return new WP_REST_Response(array(
        'success' => true,
        'results' => $results,
        'lead_id' => $lead_id,
    ), 200);
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

    // 管理者へメール送信
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
    $status   = sanitize_text_field($request->get_param('status') ?: '');
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
            array(
                'key'     => '_subsidy_target_regions',
                'value'   => $region,
                'compare' => 'LIKE',
            ),
            array(
                'key'     => '_subsidy_target_regions',
                'value'   => 'all',
                'compare' => 'LIKE',
            ),
        );
    }

    if ($source) {
        $meta_query[] = array(
            'key'   => '_subsidy_data_source',
            'value' => $source,
        );
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
            'rate'         => get_post_meta($post->ID, '_subsidy_rate', true),
            'summary'      => get_post_meta($post->ID, '_subsidy_summary', true),
            'deadline'     => get_post_meta($post->ID, '_subsidy_deadline', true),
            'official_url' => get_post_meta($post->ID, '_subsidy_official_url', true),
            'region'       => get_post_meta($post->ID, '_subsidy_target_regions', true),
            'source'       => get_post_meta($post->ID, '_subsidy_data_source', true),
        );
    }

    return new WP_REST_Response(array(
        'success'    => true,
        'items'      => $items,
        'total'      => (int) $query->found_posts,
        'pages'      => (int) $query->max_num_pages,
        'page'       => $page,
        'per_page'   => $per_page,
    ), 200);
}

/**
 * 統計情報
 */
function subsidy_match_handle_stats($request) {
    $total = wp_count_posts('subsidy');
    $published = (int) $total->publish;

    // ソース別集計
    global $wpdb;
    $sources = $wpdb->get_results(
        "SELECT meta_value AS source, COUNT(*) AS count
         FROM {$wpdb->postmeta}
         WHERE meta_key = '_subsidy_data_source'
         GROUP BY meta_value",
        ARRAY_A
    );

    // 地域別上位
    $regions = $wpdb->get_results(
        "SELECT meta_value AS region, COUNT(*) AS count
         FROM {$wpdb->postmeta}
         WHERE meta_key = '_subsidy_target_regions'
         GROUP BY meta_value
         ORDER BY count DESC
         LIMIT 10",
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
 * 売上テキストを数値（円）に変換
 */
function subsidy_match_parse_revenue($text) {
    if (empty($text)) return 0;

    $text = str_replace(array(',', ' ', '　'), '', $text);

    if (preg_match('/(\d+(?:\.\d+)?)\s*億/', $text, $m)) {
        return (int) ($m[1] * 100000000);
    }
    if (preg_match('/(\d+(?:\.\d+)?)\s*万/', $text, $m)) {
        return (int) ($m[1] * 10000);
    }
    if (preg_match('/(\d+)/', $text, $m)) {
        return (int) $m[1];
    }

    return 0;
}
