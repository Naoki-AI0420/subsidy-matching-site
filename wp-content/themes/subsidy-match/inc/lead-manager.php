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
        id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        company_name        VARCHAR(255) NOT NULL DEFAULT '',
        contact_name        VARCHAR(255) NOT NULL DEFAULT '',
        phone               VARCHAR(50) NOT NULL DEFAULT '',
        email               VARCHAR(255) NOT NULL DEFAULT '',
        prefecture          VARCHAR(10) NOT NULL DEFAULT '',
        city                VARCHAR(100) NOT NULL DEFAULT '',
        industry            VARCHAR(100) NOT NULL DEFAULT '',
        capital             VARCHAR(50) NOT NULL DEFAULT '',
        employee_size       VARCHAR(20) NOT NULL DEFAULT '',
        establishment_years VARCHAR(20) NOT NULL DEFAULT '',
        matched_count       INT UNSIGNED NOT NULL DEFAULT 0,
        matched_ids         TEXT,
        system_interest     VARCHAR(20) NOT NULL DEFAULT '',
        ip_address          VARCHAR(45) DEFAULT NULL,
        user_agent          VARCHAR(500) DEFAULT NULL,
        created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_created_at (created_at),
        INDEX idx_company (company_name)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
add_action('after_switch_theme', 'subsidy_match_create_leads_table');

/**
 * テーマ初回読み込み時にもテーブル存在確認・マイグレーション
 */
function subsidy_match_maybe_create_leads_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'leads';

    // テーブルがなければ作成
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") !== $table_name) {
        subsidy_match_create_leads_table();
        return;
    }

    // company_name カラムがなければマイグレーション
    $col = $wpdb->get_results("SHOW COLUMNS FROM {$table_name} LIKE 'company_name'");
    if (empty($col)) {
        $wpdb->query("ALTER TABLE {$table_name}
            ADD COLUMN company_name VARCHAR(255) NOT NULL DEFAULT '' AFTER id,
            ADD COLUMN contact_name VARCHAR(255) NOT NULL DEFAULT '' AFTER company_name,
            ADD COLUMN phone VARCHAR(50) NOT NULL DEFAULT '' AFTER contact_name,
            ADD COLUMN city VARCHAR(100) NOT NULL DEFAULT '' AFTER prefecture,
            ADD COLUMN establishment_years VARCHAR(20) NOT NULL DEFAULT '' AFTER employee_size,
            ADD COLUMN matched_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER establishment_years,
            ADD COLUMN system_interest VARCHAR(20) NOT NULL DEFAULT '' AFTER matched_ids
        ");
        // 不要カラムの削除（旧スキーマ互換）
        $old_cols = array('challenges', 'annual_revenue', 'has_experience');
        foreach ($old_cols as $oc) {
            $exists = $wpdb->get_results("SHOW COLUMNS FROM {$table_name} LIKE '{$oc}'");
            if (!empty($exists)) {
                $wpdb->query("ALTER TABLE {$table_name} DROP COLUMN {$oc}");
            }
        }
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
        'company_name'        => $data['company_name'] ?? '',
        'contact_name'        => $data['contact_name'] ?? '',
        'phone'               => $data['phone'] ?? '',
        'email'               => $data['email'] ?? '',
        'prefecture'          => $data['prefecture'] ?? '',
        'city'                => $data['city'] ?? '',
        'industry'            => $data['industry'] ?? '',
        'capital'             => $data['capital'] ?? '',
        'employee_size'       => $data['employee_size'] ?? '',
        'establishment_years' => $data['establishment_years'] ?? '',
        'matched_count'       => (int) ($data['matched_count'] ?? 0),
        'matched_ids'         => $data['matched_ids'] ?? '',
        'system_interest'     => $data['system_interest'] ?? '',
        'ip_address'          => $_SERVER['REMOTE_ADDR'] ?? '',
        'user_agent'          => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
        'created_at'          => current_time('mysql'),
    ), array(
        '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s'
    ));

    return $wpdb->insert_id;
}

/**
 * リード一覧取得（検索・フィルタ対応）
 */
function subsidy_match_get_leads($args = array()) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'leads';
    $per_page   = isset($args['per_page']) ? (int) $args['per_page'] : 50;
    $page       = isset($args['page']) ? (int) $args['page'] : 1;
    $offset     = ($page - 1) * $per_page;
    $search     = isset($args['search']) ? sanitize_text_field($args['search']) : '';
    $industry   = isset($args['industry']) ? sanitize_text_field($args['industry']) : '';

    $where = '1=1';
    $params = array();

    if ($search) {
        $like = '%' . $wpdb->esc_like($search) . '%';
        $where .= " AND (company_name LIKE %s OR contact_name LIKE %s OR email LIKE %s OR phone LIKE %s)";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    if ($industry) {
        $where .= " AND industry = %s";
        $params[] = $industry;
    }

    $params[] = $per_page;
    $params[] = $offset;

    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE {$where} ORDER BY created_at DESC LIMIT %d OFFSET %d",
            ...$params
        )
    );

    // トータル件数
    $count_params = array_slice($params, 0, -2);
    if (!empty($count_params)) {
        $total = (int) $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) FROM {$table_name} WHERE {$where}", ...$count_params)
        );
    } else {
        $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE {$where}");
    }

    return array(
        'items' => $results,
        'total' => $total,
        'pages' => ceil($total / $per_page),
    );
}

/**
 * リードをCSV出力用の配列として取得
 */
function subsidy_match_get_leads_for_csv($args = array()) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'leads';
    return $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY created_at DESC", ARRAY_A);
}
