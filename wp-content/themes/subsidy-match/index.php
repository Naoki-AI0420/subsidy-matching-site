<?php
/**
 * メインテンプレート（フォールバック）
 *
 * @package SubsidyMatch
 */

get_header();
?>

<main class="section">
    <div class="container">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article class="card mb-24">
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <?php the_excerpt(); ?>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <p>コンテンツが見つかりません。</p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
