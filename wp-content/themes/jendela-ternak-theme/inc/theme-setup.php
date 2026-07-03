<?php
/**
 * inc/theme-setup.php
 * Theme support, menus, image sizes.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'jt_theme_setup' );
function jt_theme_setup() {

    // Core WP support
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
    add_theme_support( 'custom-logo', [
        'height'      => 60,
        'width'       => 200,
        'flex-width'  => true,
        'flex-height' => true,
    ] );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );

    // WooCommerce support
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

    // Elementor compatibility
    // Mendeklarasikan tema ini kompatibel dengan Elementor.
    // Logika integrasi detail ada di inc/elementor-support.php
    add_theme_support( 'elementor' );

    // Menus
    register_nav_menus( [
        'primary' => __( 'Primary Menu', 'jendela-ternak' ),
        'footer'  => __( 'Footer Menu', 'jendela-ternak' ),
        'mobile'  => __( 'Mobile Menu', 'jendela-ternak' ),
    ] );

    // i18n
    load_theme_textdomain( 'jendela-ternak', JT_THEME_DIR . '/languages' );
}

// Custom image sizes
add_action( 'after_setup_theme', 'jt_image_sizes' );
function jt_image_sizes() {
    add_image_size( 'jt-product-card',   400, 400, true ); // 1:1 product card
    add_image_size( 'jt-product-large',  800, 800, true ); // PDP main
    add_image_size( 'jt-hero-banner',   1280, 480, true ); // Hero carousel
    add_image_size( 'jt-category-icon',  200, 200, true ); // Category hexagon
}

// WooCommerce: set columns
add_filter( 'loop_shop_columns', function() { return 5; } );
add_filter( 'loop_shop_per_page', function() { return 20; } );

// Sidebar
add_action( 'widgets_init', 'jt_register_sidebars' );
function jt_register_sidebars() {
    register_sidebar( [
        'name'          => __( 'Shop Sidebar', 'jendela-ternak' ),
        'id'            => 'shop-sidebar',
        'before_widget' => '<div class="jt-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="jt-widget__title">',
        'after_title'   => '</h3>',
    ] );
}

// Star rating helper
if ( ! function_exists( 'jt_render_stars' ) ) {
    function jt_render_stars( float $rating ): string {
        $stars  = '';
        $filled = floor( $rating );
        for ( $i = 0; $i < 5; $i++ ) {
            $stars .= $i < $filled ? '★' : '☆';
        }
        return $stars;
    }
}

