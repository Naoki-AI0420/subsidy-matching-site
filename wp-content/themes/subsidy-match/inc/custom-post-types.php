<?php
/**
 * カスタム投稿タイプ: 補助金 (subsidy)
 *
 * @package SubsidyMatch
 */

/**
 * 補助金カスタム投稿タイプ登録
 */
function subsidy_match_register_post_types() {
    $labels = array(
        'name'               => '補助金・助成金',
        'singular_name'      => '補助金',
        'menu_name'          => '補助金管理',
        'add_new'            => '新規追加',
        'add_new_item'       => '補助金を追加',
        'edit_item'          => '補助金を編集',
        'new_item'           => '新しい補助金',
        'view_item'          => '補助金を表示',
        'search_items'       => '補助金を検索',
        'not_found'          => '補助金が見つかりません',
        'not_found_in_trash' => 'ゴミ箱に補助金はありません',
        'all_items'          => '補助金一覧',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true,
        'rest_base'           => 'subsidy',
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-money-alt',
        'supports'            => array('title', 'editor', 'thumbnail'),
        'rewrite'             => array('slug' => 'subsidy'),
    );

    register_post_type('subsidy', $args);
}
add_action('init', 'subsidy_match_register_post_types');

/**
 * カスタムフィールド（メタボックス）
 */
function subsidy_match_add_meta_boxes() {
    add_meta_box(
        'subsidy_details',
        '補助金詳細情報',
        'subsidy_match_render_meta_box',
        'subsidy',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'subsidy_match_add_meta_boxes');

/**
 * メタボックス描画
 */
function subsidy_match_render_meta_box($post) {
    wp_nonce_field('subsidy_match_save_meta', 'subsidy_match_meta_nonce');

    $fields = array(
        '_subsidy_max_amount'         => array('label' => '最大補助金額（円）', 'type' => 'number'),
        '_subsidy_rate'               => array('label' => '補助率（例: 1/2, 2/3）', 'type' => 'text'),
        '_subsidy_summary'            => array('label' => '概要（200文字程度）', 'type' => 'textarea'),
        '_subsidy_deadline'           => array('label' => '申請期限', 'type' => 'date'),
        '_subsidy_official_url'       => array('label' => '公式サイトURL', 'type' => 'url'),
        '_subsidy_match_priority'     => array('label' => '表示優先度（1-100）', 'type' => 'number'),
    );

    $multi_fields = array(
        '_subsidy_target_industries'  => array('label' => '対象業種', 'options' => subsidy_match_get_industries()),
        '_subsidy_target_regions'     => array('label' => '対象地域', 'options' => subsidy_match_get_regions()),
        '_subsidy_target_employee_size' => array('label' => '対象従業員規模', 'options' => subsidy_match_get_employee_sizes()),
        '_subsidy_target_capital'     => array('label' => '対象資本金', 'options' => subsidy_match_get_capital_ranges()),
        '_subsidy_target_challenges'  => array('label' => '対象課題', 'options' => subsidy_match_get_challenges()),
    );

    echo '<table class="form-table">';

    foreach ($fields as $key => $field) {
        $value = get_post_meta($post->ID, $key, true);
        echo '<tr>';
        echo '<th><label for="' . esc_attr($key) . '">' . esc_html($field['label']) . '</label></th>';
        echo '<td>';
        if ($field['type'] === 'textarea') {
            echo '<textarea name="' . esc_attr($key) . '" id="' . esc_attr($key) . '" rows="4" class="large-text">' . esc_textarea($value) . '</textarea>';
        } else {
            echo '<input type="' . esc_attr($field['type']) . '" name="' . esc_attr($key) . '" id="' . esc_attr($key) . '" value="' . esc_attr($value) . '" class="regular-text">';
        }
        echo '</td>';
        echo '</tr>';
    }

    foreach ($multi_fields as $key => $field) {
        $saved = get_post_meta($post->ID, $key, true);
        $saved = is_array($saved) ? $saved : array();
        echo '<tr>';
        echo '<th>' . esc_html($field['label']) . '</th>';
        echo '<td>';
        foreach ($field['options'] as $val => $label) {
            $checked = in_array($val, $saved) ? 'checked' : '';
            echo '<label style="display:inline-block;margin-right:16px;margin-bottom:4px;">';
            echo '<input type="checkbox" name="' . esc_attr($key) . '[]" value="' . esc_attr($val) . '" ' . $checked . '> ';
            echo esc_html($label);
            echo '</label>';
        }
        echo '</td>';
        echo '</tr>';
    }

    echo '</table>';
}

/**
 * メタデータ保存
 */
function subsidy_match_save_meta($post_id) {
    if (!isset($_POST['subsidy_match_meta_nonce']) ||
        !wp_verify_nonce($_POST['subsidy_match_meta_nonce'], 'subsidy_match_save_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $text_fields = array(
        '_subsidy_max_amount',
        '_subsidy_rate',
        '_subsidy_summary',
        '_subsidy_deadline',
        '_subsidy_official_url',
        '_subsidy_match_priority',
    );

    foreach ($text_fields as $key) {
        if (isset($_POST[$key])) {
            update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
        }
    }

    $array_fields = array(
        '_subsidy_target_industries',
        '_subsidy_target_regions',
        '_subsidy_target_employee_size',
        '_subsidy_target_capital',
        '_subsidy_target_challenges',
    );

    foreach ($array_fields as $key) {
        if (isset($_POST[$key]) && is_array($_POST[$key])) {
            $clean = array_map('sanitize_text_field', $_POST[$key]);
            update_post_meta($post_id, $key, $clean);
        } else {
            update_post_meta($post_id, $key, array());
        }
    }
}
add_action('save_post_subsidy', 'subsidy_match_save_meta');

/**
 * マスタデータ
 */
function subsidy_match_get_industries() {
    return array(
        'manufacturing'        => '製造業',
        'construction'         => '建設業',
        'information_technology' => '情報通信業',
        'wholesale_retail'     => '卸売業・小売業',
        'food_service'         => '飲食サービス業',
        'accommodation'        => '宿泊業',
        'medical_welfare'      => '医療・福祉',
        'education'            => '教育・学習支援業',
        'professional_services' => '専門・技術サービス業',
        'transportation'       => '運輸業・郵便業',
        'real_estate'          => '不動産業',
        'agriculture'          => '農業・林業・漁業',
        'other'                => 'その他',
    );
}

function subsidy_match_get_regions() {
    return array(
        'all'  => '全国',
        '01' => '北海道', '02' => '青森', '03' => '岩手', '04' => '宮城',
        '05' => '秋田', '06' => '山形', '07' => '福島', '08' => '茨城',
        '09' => '栃木', '10' => '群馬', '11' => '埼玉', '12' => '千葉',
        '13' => '東京', '14' => '神奈川', '15' => '新潟', '16' => '富山',
        '17' => '石川', '18' => '福井', '19' => '山梨', '20' => '長野',
        '21' => '岐阜', '22' => '静岡', '23' => '愛知', '24' => '三重',
        '25' => '滋賀', '26' => '京都', '27' => '大阪', '28' => '兵庫',
        '29' => '奈良', '30' => '和歌山', '31' => '鳥取', '32' => '島根',
        '33' => '岡山', '34' => '広島', '35' => '山口', '36' => '徳島',
        '37' => '香川', '38' => '愛媛', '39' => '高知', '40' => '福岡',
        '41' => '佐賀', '42' => '長崎', '43' => '熊本', '44' => '大分',
        '45' => '宮崎', '46' => '鹿児島', '47' => '沖縄',
    );
}

function subsidy_match_get_employee_sizes() {
    return array(
        '1-5'   => '1〜5名',
        '6-20'  => '6〜20名',
        '21-50' => '21〜50名',
        '51-100' => '51〜100名',
        '101+'  => '101名以上',
    );
}

function subsidy_match_get_capital_ranges() {
    return array(
        'under_3m'  => '300万円未満',
        '3m_10m'    => '300万〜1,000万円',
        '10m_30m'   => '1,000万〜3,000万円',
        '30m_100m'  => '3,000万〜1億円',
        'over_100m' => '1億円以上',
    );
}

function subsidy_match_get_challenges() {
    return array(
        'equipment'  => '設備投資',
        'it_dx'      => 'IT化・DX',
        'hiring'     => '人材採用',
        'overseas'   => '海外展開',
        'rnd'        => '研究開発',
        'succession' => '事業承継',
    );
}
