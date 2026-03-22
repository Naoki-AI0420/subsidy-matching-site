<?php
/**
 * 管理画面カスタマイズ
 *
 * @package SubsidyMatch
 */

/**
 * リード管理メニュー追加
 */
function subsidy_match_admin_menu() {
    add_menu_page(
        'リード管理',
        'リード管理',
        'manage_options',
        'subsidy-leads',
        'subsidy_match_leads_page',
        'dashicons-groups',
        30
    );
}
add_action('admin_menu', 'subsidy_match_admin_menu');

/**
 * CSV エクスポート処理
 */
function subsidy_match_handle_csv_export() {
    if (!isset($_GET['page']) || $_GET['page'] !== 'subsidy-leads') return;
    if (!isset($_GET['action']) || $_GET['action'] !== 'csv_export') return;
    if (!current_user_can('manage_options')) return;

    $leads = subsidy_match_get_leads_for_csv();
    $industries = subsidy_match_get_industries();

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="leads_' . date('Ymd_His') . '.csv"');

    $output = fopen('php://output', 'w');
    // BOM for Excel UTF-8
    fwrite($output, "\xEF\xBB\xBF");

    fputcsv($output, array(
        'ID', '会社名', '担当者名', '電話番号', 'メール', '都道府県', '市区町村',
        '業種', '資本金', '従業員数', '設立年数', 'マッチ数', 'システム興味', '登録日時'
    ));

    foreach ($leads as $lead) {
        fputcsv($output, array(
            $lead['id'],
            $lead['company_name'],
            $lead['contact_name'],
            $lead['phone'],
            $lead['email'],
            $lead['prefecture'],
            $lead['city'],
            $industries[$lead['industry']] ?? $lead['industry'],
            $lead['capital'],
            $lead['employee_size'],
            $lead['establishment_years'],
            $lead['matched_count'],
            $lead['system_interest'],
            $lead['created_at'],
        ));
    }

    fclose($output);
    exit;
}
add_action('admin_init', 'subsidy_match_handle_csv_export');

/**
 * リード一覧ページ描画
 */
function subsidy_match_leads_page() {
    $page     = isset($_GET['paged']) ? max(1, (int) $_GET['paged']) : 1;
    $search   = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    $industry = isset($_GET['industry_filter']) ? sanitize_text_field($_GET['industry_filter']) : '';

    $data = subsidy_match_get_leads(array(
        'page'     => $page,
        'per_page' => 30,
        'search'   => $search,
        'industry' => $industry,
    ));

    $industries = subsidy_match_get_industries();
    $csv_url = admin_url('admin.php?page=subsidy-leads&action=csv_export');

    $system_labels = array('yes' => 'はい', 'no' => 'いいえ', 'undecided' => '未定');
    ?>
    <div class="wrap">
        <h1>リード管理</h1>
        <p>補助金診断を完了したユーザーの一覧です。（全 <?php echo esc_html($data['total']); ?> 件）</p>

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:8px;">
            <form method="get" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <input type="hidden" name="page" value="subsidy-leads">
                <input type="text" name="s" value="<?php echo esc_attr($search); ?>" placeholder="会社名・担当者名・メール・電話" style="min-width:250px;">
                <select name="industry_filter">
                    <option value="">全業種</option>
                    <?php foreach ($industries as $key => $label) : ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($industry, $key); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="button">検索</button>
                <?php if ($search || $industry) : ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=subsidy-leads')); ?>" class="button">クリア</a>
                <?php endif; ?>
            </form>
            <a href="<?php echo esc_url($csv_url); ?>" class="button button-secondary">CSV エクスポート</a>
        </div>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width:40px">ID</th>
                    <th>会社名</th>
                    <th>担当者</th>
                    <th>電話番号</th>
                    <th>メール</th>
                    <th>所在地</th>
                    <th>業種</th>
                    <th style="width:60px">マッチ</th>
                    <th style="width:80px">システム</th>
                    <th>登録日時</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['items'])) : ?>
                    <tr><td colspan="10">リードデータがありません。</td></tr>
                <?php else : ?>
                    <?php foreach ($data['items'] as $lead) : ?>
                        <tr>
                            <td><?php echo esc_html($lead->id); ?></td>
                            <td><strong><?php echo esc_html($lead->company_name); ?></strong></td>
                            <td><?php echo esc_html($lead->contact_name); ?></td>
                            <td><?php echo esc_html($lead->phone); ?></td>
                            <td><a href="mailto:<?php echo esc_attr($lead->email); ?>"><?php echo esc_html($lead->email); ?></a></td>
                            <td><?php echo esc_html($lead->prefecture . $lead->city); ?></td>
                            <td><?php echo esc_html($industries[$lead->industry] ?? $lead->industry); ?></td>
                            <td><?php echo esc_html($lead->matched_count); ?>件</td>
                            <td><?php echo esc_html($system_labels[$lead->system_interest] ?? '-'); ?></td>
                            <td><?php echo esc_html($lead->created_at); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($data['pages'] > 1) : ?>
            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <?php
                    $base_url = admin_url('admin.php?page=subsidy-leads');
                    if ($search) $base_url .= '&s=' . urlencode($search);
                    if ($industry) $base_url .= '&industry_filter=' . urlencode($industry);
                    ?>
                    <?php for ($i = 1; $i <= $data['pages']; $i++) : ?>
                        <?php if ($i === $page) : ?>
                            <span class="tablenav-pages-navspan button disabled"><?php echo $i; ?></span>
                        <?php else : ?>
                            <a class="button" href="<?php echo esc_url($base_url . '&paged=' . $i); ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * 補助金一覧のカラム追加
 */
function subsidy_match_admin_columns($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['subsidy_amount']   = '最大金額';
            $new_columns['subsidy_deadline'] = '申請期限';
        }
    }
    return $new_columns;
}
add_filter('manage_subsidy_posts_columns', 'subsidy_match_admin_columns');

/**
 * カラム内容表示
 */
function subsidy_match_admin_column_content($column, $post_id) {
    if ($column === 'subsidy_amount') {
        $amount = get_post_meta($post_id, '_subsidy_max_amount', true);
        echo $amount ? esc_html(number_format((int) $amount)) . '円' : '—';
    }
    if ($column === 'subsidy_deadline') {
        $deadline = get_post_meta($post_id, '_subsidy_deadline', true);
        echo $deadline ? esc_html($deadline) : '—';
    }
}
add_action('manage_subsidy_posts_custom_column', 'subsidy_match_admin_column_content', 10, 2);
