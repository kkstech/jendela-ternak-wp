<?php
/**
 * inc/enqueue-assets.php
 * Enqueue all scripts and styles.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'jt_enqueue_assets' );
function jt_enqueue_assets() {
    // wp_enqueue_scripts fires only on the frontend, but extra guard for safety
    if ( is_admin() ) {
        return;
    }


    // Google Fonts — Plus Jakarta Sans
    wp_enqueue_style(
        'jt-google-fonts',
        'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
        [],
        null
    );

    // Theme style.css (CSS variables + base reset)
    wp_enqueue_style(
        'jt-style',
        get_stylesheet_uri(),
        [ 'jt-google-fonts' ],
        JT_THEME_VERSION
    );

    // Main component CSS
    wp_enqueue_style(
        'jt-main',
        JT_THEME_URI . '/assets/css/main.css',
        [ 'jt-style' ],
        JT_THEME_VERSION
    );

    // Tailwind CSS CDN (Play CDN — development)
    wp_enqueue_script(
        'tailwind-cdn',
        'https://cdn.tailwindcss.com',
        [],
        null,
        false
    );

    // Theme JS files (enqueued first so they execute and register components/listeners before Alpine core executes)
    wp_enqueue_script(
        'jt-countdown',
        JT_THEME_URI . '/assets/js/countdown.js',
        [],
        JT_THEME_VERSION,
        true
    );

    wp_enqueue_script(
        'jt-cart-drawer',
        JT_THEME_URI . '/assets/js/cart-drawer.js',
        [ 'jquery' ],
        JT_THEME_VERSION,
        true
    );

    // Shop filter only on WooCommerce archive/shop pages
    $is_shop_page = false;
    if ( class_exists( 'WooCommerce' ) ) {
        $is_shop_page = is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy();
    }

    if ( $is_shop_page ) {
        wp_enqueue_script(
            'jt-shop-filter',
            JT_THEME_URI . '/assets/js/shop-filter.js',
            [ 'jquery' ],
            JT_THEME_VERSION,
            true
        );
    }

    // Bottom nav only on single product pages
    if ( is_singular( 'product' ) ) {
        wp_enqueue_script(
            'jt-bottom-nav',
            JT_THEME_URI . '/assets/js/bottom-nav.js',
            [ 'jquery' ],
            JT_THEME_VERSION,
            true
        );

        wp_enqueue_script(
            'jt-variant-chips',
            JT_THEME_URI . '/assets/js/variant-chips.js',
            [ 'jquery' ],
            JT_THEME_VERSION,
            true
        );

        wp_enqueue_script(
            'jt-lightbox',
            JT_THEME_URI . '/assets/js/lightbox.js',
            [],
            JT_THEME_VERSION,
            true
        );

        wp_enqueue_script(
            'jt-qty-stepper',
            JT_THEME_URI . '/assets/js/qty-stepper.js',
            [ 'jquery' ],
            JT_THEME_VERSION,
            true
        );
    }

    // Alpine.js CDN (enqueued last so it registers and starts after component listeners are in place)
    $alpine_deps = array( 'jt-cart-drawer' );
    if ( is_singular( 'product' ) ) {
        $alpine_deps[] = 'jt-bottom-nav';
        $alpine_deps[] = 'jt-variant-chips';
        $alpine_deps[] = 'jt-lightbox';
        $alpine_deps[] = 'jt-qty-stepper';
    }
    if ( $is_shop_page ) {
        $alpine_deps[] = 'jt-shop-filter';
    }

    wp_enqueue_script(
        'alpinejs',
        'https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js',
        $alpine_deps,
        '3.14.1',
        true
    );


    // WooCommerce scripts (cart fragments / AJAX)
    if ( class_exists( 'WooCommerce' ) ) {
        wp_enqueue_script( 'wc-cart-fragments' );
    }

    // Localize script data for JS
    wp_localize_script( 'jt-cart-drawer', 'jt_vars', [
        'ajax_url'       => admin_url( 'admin-ajax.php' ),
        'nonce'          => wp_create_nonce( 'jt-nonce' ),
        'cart_url'       => wc_get_cart_url(),
        'checkout_url'   => wc_get_checkout_url(),
        'currency_symbol' => get_woocommerce_currency_symbol(),
    ] );
}

// Add defer attribute to Alpine and theme scripts to prevent race conditions
add_filter( 'script_loader_tag', 'jt_add_defer_attribute', 10, 2 );
function jt_add_defer_attribute( $tag, $handle ) {
    $handles_to_defer = [ 'alpinejs', 'jt-cart-drawer', 'jt-bottom-nav', 'jt-countdown', 'jt-variant-chips', 'jt-shop-filter', 'jt-lightbox', 'jt-qty-stepper' ];
    if ( in_array( $handle, $handles_to_defer, true ) ) {
        if ( false === strpos( $tag, ' defer' ) ) {
            $tag = str_replace( ' src', ' defer src', $tag );
        }
    }
    return $tag;
}


// Admin enqueue
add_action( 'admin_enqueue_scripts', 'jt_admin_assets' );
function jt_admin_assets() {
    wp_enqueue_style(
        'jt-admin',
        JT_THEME_URI . '/assets/css/admin.css',
        [],
        JT_THEME_VERSION
    );
}

