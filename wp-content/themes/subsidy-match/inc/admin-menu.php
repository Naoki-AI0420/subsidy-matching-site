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
 * リード一覧ページ描画
 */
function subsidy_match_leads_page() {
    $page = isset($_GET['paged']) ? max(1, (int) $_GET['paged']) : 1;
    $data = subsidy_match_get_leads(array('page' => $page, 'per_page' => 30));

    $industries = subsidy_match_get_industries();
    $challenges_map = subsidy_match_get_challenges();
    ?>
    <div class="wrap">
        <h1>リード管理</h1>
        <p>補助金診断を完了したユーザーの一覧です。（全 <?php echo esc_html($data['total']); ?> 件）</p>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width:50px">ID</th>
                    <th>メールアドレス</th>
                    <th>所在地</th>
                    <th>業種</th>
                    <th>従業員数</th>
                    <th>課題</th>
                    <th>マッチ数</th>
                    <th>登録日時</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['items'])) : ?>
                    <tr><td colspan="8">リードデータがありません。</td></tr>
                <?php else : ?>
                    <?php foreach ($data['items'] as $lead) : ?>
                        <?php
                        $lead_challenges = json_decode($lead->challenges, true);
                        $challenge_labels = array();
                        if (is_array($lead_challenges)) {
                            foreach ($lead_challenges as $c) {
                                $challenge_labels[] = $challenges_map[$c] ?? $c;
                            }
                        }
                        $matched = json_decode($lead->matched_ids, true);
                        $match_count = is_array($matched) ? count($matched) : 0;
                        ?>
                        <tr>
                            <td><?php echo esc_html($lead->id); ?></td>
                            <td><?php echo esc_html($lead->email); ?></td>
                            <td><?php echo esc_html($lead->prefecture); ?></td>
                            <td><?php echo esc_html($industries[$lead->industry] ?? $lead->industry); ?></td>
                            <td><?php echo esc_html($lead->employee_size); ?></td>
                            <td><?php echo esc_html(implode(', ', $challenge_labels)); ?></td>
                            <td><?php echo esc_html($match_count); ?></td>
                            <td><?php echo esc_html($lead->created_at); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($data['pages'] > 1) : ?>
            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <?php for ($i = 1; $i <= $data['pages']; $i++) : ?>
                        <?php if ($i === $page) : ?>
                            <span class="tablenav-pages-navspan button disabled"><?php echo $i; ?></span>
                        <?php else : ?>
                            <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=subsidy-leads&paged=' . $i)); ?>"><?php echo $i; ?></a>
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
        if ($amount) {
            echo esc_html(number_format((int) $amount)) . '円';
        } else {
            echo '—';
        }
    }
    if ($column === 'subsidy_deadline') {
        $deadline = get_post_meta($post_id, '_subsidy_deadline', true);
        echo $deadline ? esc_html($deadline) : '—';
    }
}
add_action('manage_subsidy_posts_custom_column', 'subsidy_match_admin_column_content', 10, 2);
