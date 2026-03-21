<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-PM59CBKN');</script>
    <!-- End Google Tag Manager -->
    <meta name="format-detection" content="telephone=no">
    <meta name="description" content="中小企業・小規模事業者向け補助金・助成金マッチングサービス。簡単な質問に答えるだけで、活用可能な補助金をご案内します。">
    <meta property="og:title" content="<?php wp_title('|', true, 'right'); bloginfo('name'); ?>">
    <meta property="og:description" content="簡単な質問に答えるだけで、貴社に該当する補助金・助成金をご案内。約2分で診断完了、登録不要。">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo esc_url(get_permalink()); ?>">
    <meta property="og:site_name" content="補助金マッチングサイト">
    <meta property="og:locale" content="ja_JP">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="補助金マッチングサイト — あなたの会社で活用できる補助金を探しましょう">
    <meta name="twitter:description" content="簡単な質問に答えるだけで、貴社に該当する補助金・助成金をご案内。約2分で診断完了。">
    <meta property="og:image" content="<?php echo get_template_directory_uri(); ?>/assets/images/ogp.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PM59CBKN"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
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
