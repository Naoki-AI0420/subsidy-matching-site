<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="header-inner">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo">
            <svg class="logo-icon" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="2" y="8" width="28" height="20" rx="2" stroke="white" stroke-width="2"/>
                <path d="M8 4h16l2 4H6l2-4z" fill="white"/>
                <rect x="8" y="14" width="16" height="2" rx="1" fill="white"/>
                <rect x="8" y="19" width="12" height="2" rx="1" fill="white"/>
            </svg>
            <?php bloginfo('name'); ?>
        </a>
        <button class="menu-toggle" aria-label="メニュー">☰</button>
        <nav class="header-nav">
            <a href="<?php echo esc_url(home_url('/')); ?>">トップ</a>
            <a href="<?php echo esc_url(home_url('/matching/')); ?>">補助金診断</a>
            <a href="<?php echo esc_url(home_url('/contact/')); ?>">お問い合わせ</a>
        </nav>
    </div>
</header>
