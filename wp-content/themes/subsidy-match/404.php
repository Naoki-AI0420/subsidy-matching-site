<?php
/**
 * 404 Not Found
 *
 * @package SubsidyMatch
 */

get_header();
?>

<main class="section">
    <div class="container text-center" style="padding: 80px 0;">
        <h1>ページが見つかりません</h1>
        <p class="text-muted mb-24">お探しのページは移動または削除された可能性があります。</p>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">トップページへ戻る</a>
    </div>
</main>

<?php get_footer(); ?>
