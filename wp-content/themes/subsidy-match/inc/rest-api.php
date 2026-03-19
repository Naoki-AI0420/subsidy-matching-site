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

        $results[] = array(
            'id'           => $post->ID,
            'title'        => $post->post_title,
            'max_amount'   => (int) get_post_meta($post->ID, '_subsidy_max_amount', true),
            'rate'         => get_post_meta($post->ID, '_subsidy_rate', true),
            'summary'      => get_post_meta($post->ID, '_subsidy_summary', true),
            'deadline'     => get_post_meta($post->ID, '_subsidy_deadline', true),
            'official_url' => get_post_meta($post->ID, '_subsidy_official_url', true),
            'score'        => $score,
            'match_level'  => $match_level,
        );
    }

    // スコア降順ソート
    usort($results, function ($a, $b) {
        return $b['score'] - $a['score'];
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
