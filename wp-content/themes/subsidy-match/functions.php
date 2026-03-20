<?php
/**
 * Subsidy Match テーマ functions.php
 *
 * @package SubsidyMatch
 */

define('SUBSIDY_MATCH_VERSION', '1.0.0');

/**
 * テーマセットアップ
 */
function subsidy_match_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    register_nav_menus(array(
        'primary'   => 'ヘッダーナビゲーション',
        'footer'    => 'フッターナビゲーション',
    ));
}
add_action('after_setup_theme', 'subsidy_match_setup');

/**
 * スタイル・スクリプト読み込み
 */
function subsidy_match_enqueue_scripts() {
    // 共通CSS
    wp_enqueue_style(
        'subsidy-match-common',
        get_template_directory_uri() . '/assets/css/common.css',
        array(),
        SUBSIDY_MATCH_VERSION
    );

    wp_enqueue_style(
        'subsidy-match-style',
        get_stylesheet_uri(),
        array('subsidy-match-common'),
        SUBSIDY_MATCH_VERSION
    );

    // 一問一答ページ
    if (is_page_template('page-matching.php') || is_page('matching')) {
        wp_enqueue_style(
            'subsidy-match-matching',
            get_template_directory_uri() . '/assets/css/matching.css',
            array('subsidy-match-common'),
            SUBSIDY_MATCH_VERSION
        );
        wp_enqueue_style(
            'subsidy-match-result',
            get_template_directory_uri() . '/assets/css/result.css',
            array('subsidy-match-common'),
            SUBSIDY_MATCH_VERSION
        );
        wp_enqueue_script(
            'subsidy-match-matching',
            get_template_directory_uri() . '/assets/js/matching.js',
            array(),
            SUBSIDY_MATCH_VERSION,
            true
        );
        wp_localize_script('subsidy-match-matching', 'subsidyMatchApi', array(
            'root'  => esc_url_raw(rest_url()),
            'nonce' => wp_create_nonce('wp_rest'),
        ));
    }

    // フロントページ
    if (is_front_page()) {
        wp_enqueue_script(
            'subsidy-match-front-page',
            get_template_directory_uri() . '/assets/js/front-page.js',
            array(),
            SUBSIDY_MATCH_VERSION,
            true
        );
    }

    // お問い合わせページ
    if (is_page_template('page-contact.php') || is_page('contact')) {
        wp_enqueue_script(
            'subsidy-match-contact',
            get_template_directory_uri() . '/assets/js/contact.js',
            array(),
            SUBSIDY_MATCH_VERSION,
            true
        );
        wp_localize_script('subsidy-match-contact', 'subsidyContactApi', array(
            'root'  => esc_url_raw(rest_url()),
            'nonce' => wp_create_nonce('wp_rest'),
        ));
    }

    // モバイルメニュー
    wp_add_inline_script('subsidy-match-common', "
        document.addEventListener('DOMContentLoaded', function() {
            var toggle = document.querySelector('.menu-toggle');
            var nav = document.querySelector('.header-nav');
            if (toggle && nav) {
                toggle.addEventListener('click', function() {
                    nav.classList.toggle('is-open');
                });
            }
        });
    ");
}
add_action('wp_enqueue_scripts', 'subsidy_match_enqueue_scripts');

/**
 * インラインスクリプト用のダミーハンドル登録
 */
function subsidy_match_register_inline_handle() {
    wp_register_script('subsidy-match-common', false);
    wp_enqueue_script('subsidy-match-common');
}
add_action('wp_enqueue_scripts', 'subsidy_match_register_inline_handle', 5);

// カスタム投稿タイプ
require_once get_template_directory() . '/inc/custom-post-types.php';

// REST API
require_once get_template_directory() . '/inc/rest-api.php';

// リード管理
require_once get_template_directory() . '/inc/lead-manager.php';

// 管理画面
require_once get_template_directory() . '/inc/admin-menu.php';

// SEO: サイトマップ有効化（WordPress 5.5+ 標準機能）
add_filter('wp_sitemaps_enabled', '__return_true');

// SEO: title タグ
add_theme_support('title-tag');
