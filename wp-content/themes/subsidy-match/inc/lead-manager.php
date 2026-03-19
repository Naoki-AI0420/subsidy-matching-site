<?php
/**
 * リード管理
 *
 * @package SubsidyMatch
 */

/**
 * テーマ有効化時にリードテーブルを作成
 */
function subsidy_match_create_leads_table() {
    global $wpdb;

    $table_name      = $wpdb->prefix . 'leads';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table_name} (
        id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        email           VARCHAR(255) NOT NULL,
        prefecture      VARCHAR(10) NOT NULL DEFAULT '',
        industry        VARCHAR(100) NOT NULL DEFAULT '',
        employee_size   VARCHAR(20) NOT NULL DEFAULT '',
        capital         VARCHAR(20) NOT NULL DEFAULT '',
        challenges      TEXT NOT NULL,
        annual_revenue  VARCHAR(20) NOT NULL DEFAULT '',
        has_experience  TINYINT(1) NOT NULL DEFAULT 0,
        matched_ids     TEXT,
        ip_address      VARCHAR(45) DEFAULT NULL,
        user_agent      VARCHAR(500) DEFAULT NULL,
        created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_created_at (created_at)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
add_action('after_switch_theme', 'subsidy_match_create_leads_table');

// テーマ初回読み込み時にもテーブル存在確認
function subsidy_match_maybe_create_leads_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'leads';

    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") !== $table_name) {
        subsidy_match_create_leads_table();
    }
}
add_action('admin_init', 'subsidy_match_maybe_create_leads_table');

/**
 * リード保存
 */
function subsidy_match_save_lead($data) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'leads';

    $wpdb->insert($table_name, array(
        'email'          => $data['email'],
        'prefecture'     => $data['prefecture'],
        'industry'       => $data['industry'],
        'employee_size'  => $data['employee_size'],
        'capital'        => $data['capital'],
        'challenges'     => $data['challenges'],
        'annual_revenue' => $data['annual_revenue'],
        'has_experience' => $data['has_experience'],
        'matched_ids'    => $data['matched_ids'] ?? '',
        'ip_address'     => $_SERVER['REMOTE_ADDR'] ?? '',
        'user_agent'     => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
        'created_at'     => current_time('mysql'),
    ), array(
        '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s'
    ));

    return $wpdb->insert_id;
}

/**
 * リード一覧取得
 */
function subsidy_match_get_leads($args = array()) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'leads';
    $per_page   = isset($args['per_page']) ? (int) $args['per_page'] : 50;
    $page       = isset($args['page']) ? (int) $args['page'] : 1;
    $offset     = ($page - 1) * $per_page;

    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$table_name} ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $per_page,
            $offset
        )
    );

    $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");

    return array(
        'items' => $results,
        'total' => $total,
        'pages' => ceil($total / $per_page),
    );
}
