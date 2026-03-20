<?php
/**
 * 主要補助金5件の手動登録スクリプト
 *
 * 業種・従業員・資本金タグを正確に設定した主要補助金を登録する。
 * WP-CLI 経由で実行: wp eval-file scripts/register-major-subsidies.php
 *
 * @package SubsidyMatch
 */

if (!class_exists('WP_CLI')) {
    echo "このスクリプトは WP-CLI 経由で実行してください:\n";
    echo "  wp eval-file scripts/register-major-subsidies.php\n";
    exit(1);
}

$major_subsidies = array(
    array(
        'title'   => 'IT導入補助金2026',
        'content' => '中小企業・小規模事業者がITツール（ソフトウェア、サービス等）を導入する際の経費の一部を補助する制度です。会計ソフト、受発注ソフト、決済ソフト、ECソフトなど幅広いITツールが対象。インボイス対応類型では、安価なITツールの導入も支援します。',
        'meta'    => array(
            '_subsidy_max_amount'          => 4500000,
            '_subsidy_amount_text'         => '最大450万円',
            '_subsidy_rate'                => '1/2〜3/4',
            '_subsidy_subsidy_rate'        => '通常枠: 1/2以内、インボイス枠: 3/4以内',
            '_subsidy_region'              => '全国',
            '_subsidy_target_regions'      => array('all'),
            '_subsidy_target_industries'   => array(
                'manufacturing', 'construction', 'information_technology',
                'wholesale_retail', 'food_service', 'accommodation',
                'medical_welfare', 'education', 'professional_services',
                'transportation', 'real_estate', 'agriculture', 'other'
            ),
            '_subsidy_target_employee_size' => array('1-5', '6-20', '21-50', '51-100', '101+'),
            '_subsidy_target_capital'       => array('under_3m', '3m_10m', '10m_30m', '30m_100m'),
            '_subsidy_target_challenges'    => array('it_dx', 'equipment'),
            '_subsidy_status'               => 'active',
            '_subsidy_application_period'   => '2026年4月〜2026年12月（複数回公募予定）',
            '_subsidy_implementing_agency'  => '経済産業省 / 中小企業庁',
            '_subsidy_category'             => 'it',
            '_subsidy_purpose'              => 'IT化・DX推進',
            '_subsidy_eligible_entities'    => '中小企業・小規模事業者',
            '_subsidy_data_source'          => 'manual',
            '_subsidy_match_priority'       => 100,
            '_subsidy_adoption_rate'        => 0.62,
        ),
    ),
    array(
        'title'   => 'ものづくり・商業・サービス生産性向上促進補助金',
        'content' => '中小企業・小規模事業者が取り組む革新的サービス開発・試作品開発・生産プロセスの改善を行う際の設備投資等を支援する制度です。通常枠のほか、デジタル枠、グリーン枠、グローバル展開型などの特別枠があります。',
        'meta'    => array(
            '_subsidy_max_amount'          => 12500000,
            '_subsidy_amount_text'         => '最大1,250万円',
            '_subsidy_rate'                => '1/2〜2/3',
            '_subsidy_subsidy_rate'        => '通常枠: 1/2（小規模事業者 2/3）',
            '_subsidy_region'              => '全国',
            '_subsidy_target_regions'      => array('all'),
            '_subsidy_target_industries'   => array(
                'manufacturing', 'construction', 'food_service',
                'wholesale_retail', 'professional_services', 'other'
            ),
            '_subsidy_target_employee_size' => array('1-5', '6-20', '21-50', '51-100', '101+'),
            '_subsidy_target_capital'       => array('under_3m', '3m_10m', '10m_30m', '30m_100m'),
            '_subsidy_target_challenges'    => array('equipment', 'it_dx', 'rnd'),
            '_subsidy_status'               => 'active',
            '_subsidy_application_period'   => '2026年度（随時公募）',
            '_subsidy_implementing_agency'  => '経済産業省 / 中小企業庁',
            '_subsidy_category'             => 'manufacturing',
            '_subsidy_purpose'              => '設備投資・生産性向上',
            '_subsidy_eligible_entities'    => '中小企業・小規模事業者',
            '_subsidy_data_source'          => 'manual',
            '_subsidy_match_priority'       => 95,
            '_subsidy_adoption_rate'        => 0.45,
        ),
    ),
    array(
        'title'   => '小規模事業者持続化補助金',
        'content' => '小規模事業者が経営計画を策定して取り組む販路開拓等の取組を支援する制度です。ウェブサイト作成、チラシ作成、展示会出展、店舗改装など幅広い経費が対象になります。',
        'meta'    => array(
            '_subsidy_max_amount'          => 2000000,
            '_subsidy_amount_text'         => '最大200万円',
            '_subsidy_rate'                => '2/3',
            '_subsidy_subsidy_rate'        => '補助率 2/3（通常枠: 上限50万円、特別枠: 上限200万円）',
            '_subsidy_region'              => '全国',
            '_subsidy_target_regions'      => array('all'),
            '_subsidy_target_industries'   => array(
                'manufacturing', 'construction', 'information_technology',
                'wholesale_retail', 'food_service', 'accommodation',
                'medical_welfare', 'education', 'professional_services',
                'transportation', 'real_estate', 'agriculture', 'other'
            ),
            '_subsidy_target_employee_size' => array('1-5', '6-20'),
            '_subsidy_target_capital'       => array('under_3m', '3m_10m', '10m_30m'),
            '_subsidy_target_challenges'    => array('equipment', 'it_dx', 'hiring'),
            '_subsidy_status'               => 'active',
            '_subsidy_application_period'   => '2026年5月31日締切（次回公募あり）',
            '_subsidy_implementing_agency'  => '経済産業省 / 商工会議所',
            '_subsidy_category'             => 'sales',
            '_subsidy_purpose'              => '販路開拓・経営改善',
            '_subsidy_eligible_entities'    => '従業員20名以下の小規模事業者',
            '_subsidy_data_source'          => 'manual',
            '_subsidy_match_priority'       => 90,
            '_subsidy_adoption_rate'        => 0.55,
        ),
    ),
    array(
        'title'   => '事業再構築補助金',
        'content' => 'ポストコロナ時代の経済社会の変化に対応するため、中小企業等の思い切った事業再構築を支援する制度です。新分野展開、事業転換、業種転換、業態転換、事業再編などが対象。成長枠、グリーン成長枠などの類型があります。',
        'meta'    => array(
            '_subsidy_max_amount'          => 150000000,
            '_subsidy_amount_text'         => '最大1.5億円',
            '_subsidy_rate'                => '1/2〜3/4',
            '_subsidy_subsidy_rate'        => '成長枠: 1/2（中小）、1/3（中堅）',
            '_subsidy_region'              => '全国',
            '_subsidy_target_regions'      => array('all'),
            '_subsidy_target_industries'   => array(
                'manufacturing', 'construction', 'information_technology',
                'wholesale_retail', 'food_service', 'accommodation',
                'medical_welfare', 'education', 'professional_services',
                'transportation', 'real_estate', 'agriculture', 'other'
            ),
            '_subsidy_target_employee_size' => array('1-5', '6-20', '21-50', '51-100', '101+'),
            '_subsidy_target_capital'       => array('under_3m', '3m_10m', '10m_30m', '30m_100m', 'over_100m'),
            '_subsidy_target_challenges'    => array('equipment', 'it_dx', 'overseas', 'rnd'),
            '_subsidy_status'               => 'active',
            '_subsidy_application_period'   => '2026年7月31日締切（予定）',
            '_subsidy_implementing_agency'  => '経済産業省 / 中小企業庁',
            '_subsidy_category'             => 'restructure',
            '_subsidy_purpose'              => '事業再構築・新分野展開',
            '_subsidy_eligible_entities'    => '中小企業・中堅企業',
            '_subsidy_data_source'          => 'manual',
            '_subsidy_match_priority'       => 85,
            '_subsidy_adoption_rate'        => 0.38,
        ),
    ),
    array(
        'title'   => '人材開発支援助成金',
        'content' => '事業主が雇用する労働者に対して、職務に関連した専門的な知識及び技能を習得させるための職業訓練等を計画に沿って実施した場合に、訓練経費や訓練期間中の賃金の一部を助成する制度です。',
        'meta'    => array(
            '_subsidy_max_amount'          => 10000000,
            '_subsidy_amount_text'         => '最大1,000万円',
            '_subsidy_rate'                => '経費助成 最大75%',
            '_subsidy_subsidy_rate'        => '経費助成率: 45〜75%、賃金助成: 760〜960円/時',
            '_subsidy_region'              => '全国',
            '_subsidy_target_regions'      => array('all'),
            '_subsidy_target_industries'   => array(
                'manufacturing', 'construction', 'information_technology',
                'wholesale_retail', 'food_service', 'accommodation',
                'medical_welfare', 'education', 'professional_services',
                'transportation', 'real_estate', 'agriculture', 'other'
            ),
            '_subsidy_target_employee_size' => array('1-5', '6-20', '21-50', '51-100', '101+'),
            '_subsidy_target_capital'       => array('under_3m', '3m_10m', '10m_30m', '30m_100m', 'over_100m'),
            '_subsidy_target_challenges'    => array('hiring', 'it_dx'),
            '_subsidy_status'               => 'active',
            '_subsidy_application_period'   => '通年（随時申請可）',
            '_subsidy_implementing_agency'  => '厚生労働省',
            '_subsidy_category'             => 'training',
            '_subsidy_purpose'              => '人材育成・スキルアップ',
            '_subsidy_eligible_entities'    => '雇用保険適用事業主',
            '_subsidy_data_source'          => 'manual',
            '_subsidy_match_priority'       => 80,
            '_subsidy_adoption_rate'        => 0.70,
        ),
    ),
);

$registered = 0;
$skipped = 0;

foreach ($major_subsidies as $subsidy) {
    // 重複チェック（タイトルで）
    $existing = get_posts(array(
        'post_type'   => 'subsidy',
        'post_status' => array('publish', 'draft'),
        'title'       => $subsidy['title'],
        'numberposts' => 1,
    ));

    if (!empty($existing)) {
        // 既存があればメタを更新
        $post_id = $existing[0]->ID;
        WP_CLI::log("更新: {$subsidy['title']} (ID: {$post_id})");
    } else {
        // 新規作成
        $post_id = wp_insert_post(array(
            'post_title'   => $subsidy['title'],
            'post_type'    => 'subsidy',
            'post_status'  => 'publish',
            'post_content' => '<p>' . esc_html($subsidy['content']) . '</p>',
        ), true);

        if (is_wp_error($post_id)) {
            WP_CLI::warning("エラー: {$subsidy['title']} - " . $post_id->get_error_message());
            continue;
        }
        WP_CLI::log("新規登録: {$subsidy['title']} (ID: {$post_id})");
    }

    // メタフィールド設定
    foreach ($subsidy['meta'] as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }

    // サマリーもメタに保存（結果表示用）
    update_post_meta($post_id, '_subsidy_summary', $subsidy['content']);

    $registered++;
}

WP_CLI::success("主要補助金の登録完了: {$registered}件");
