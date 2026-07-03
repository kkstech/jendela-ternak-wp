<?php
/**
 * inc/woocommerce-hooks.php
 * All WooCommerce hook overrides, Buy Now logic, cart drawer AJAX.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

// ─────────────────────────────────────────────────────────────────
// 1. DECLARE WOOCOMMERCE COMPATIBILITY
// ─────────────────────────────────────────────────────────────────
add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );

// ─────────────────────────────────────────────────────────────────
// 2. REMOVE DEFAULT WOOCOMMERCE WRAPPERS (use our own layout)
// ─────────────────────────────────────────────────────────────────
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

// Remove default single product gallery to prevent duplicates
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );

// Remove default single product tabs to prevent duplicates
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );

// Remove default related products & upsells (we render them ourselves in content-single-product.php)
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

add_action( 'woocommerce_before_main_content', 'jt_wc_wrapper_start', 10 );
function jt_wc_wrapper_start() {
    echo '<main id="main-content" class="jt-main"><div class="jt-container">';
}

add_action( 'woocommerce_after_main_content', 'jt_wc_wrapper_end', 10 );
function jt_wc_wrapper_end() {
    echo '</div></main>';
}

// ─────────────────────────────────────────────────────────────────
// 3. BUY NOW BUTTON — add beside Add to Cart on Single Product
// ─────────────────────────────────────────────────────────────────
add_action( 'woocommerce_after_add_to_cart_button', 'jt_buy_now_button' );
function jt_buy_now_button() {
    global $product;
    if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
        return;
    }
    ?>
    <button
        type="submit"
        name="buy_now"
        value="1"
        class="jt-btn jt-btn--buy-now single_add_to_cart_button button alt"
    >
        <?php esc_html_e( 'Beli Sekarang', 'jendela-ternak' ); ?>
    </button>
    <?php
}

// ─────────────────────────────────────────────────────────────────
// 4. BUY NOW REDIRECT FILTER
//    Clears cart, adds product, redirects straight to checkout
// ─────────────────────────────────────────────────────────────────
add_filter( 'woocommerce_add_to_cart_redirect', 'jt_buy_now_redirect', 99 );
function jt_buy_now_redirect( $url ) {
    if ( ! isset( $_REQUEST['buy_now'] ) ) {
        return $url;
    }
    // Empty the cart first so only this item goes to checkout
    WC()->cart->empty_cart();
    return wc_get_checkout_url();
}

// ─────────────────────────────────────────────────────────────────
// 5. CART DRAWER — Ajax Fragments for Alpine.js
// ─────────────────────────────────────────────────────────────────
add_filter( 'woocommerce_add_to_cart_fragments', 'jt_cart_drawer_fragment' );
function jt_cart_drawer_fragment( $fragments ) {

    ob_start();
    ?>
    <span class="jt-cart-badge" data-jt-cart-count>
        <?php echo WC()->cart->get_cart_contents_count(); ?>
    </span>
    <?php
    $fragments['span.jt-cart-badge'] = ob_get_clean();

    // Full cart drawer HTML fragment
    ob_start();
    jt_render_cart_drawer();
    $fragments['#jt-cart-drawer-inner'] = ob_get_clean();

    return $fragments;
}

/**
 * Render cart drawer contents (called in header & via fragments)
 */
function jt_render_cart_drawer() {
    $cart  = WC()->cart;
    $items = $cart->get_cart();
    ?>
    <div id="jt-cart-drawer-inner">
        <?php if ( empty( $items ) ) : ?>
            <div class="jt-cart-drawer__empty">
                <svg width="64" height="64" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="opacity:0.3;margin:0 auto 12px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p><?php esc_html_e( 'Keranjang kamu masih kosong', 'jendela-ternak' ); ?></p>
                <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="jt-btn jt-btn--primary" style="margin-top:12px;display:inline-block;">
                    <?php esc_html_e( 'Belanja Sekarang', 'jendela-ternak' ); ?>
                </a>
            </div>
        <?php else : ?>
            <ul class="jt-cart-drawer__list">
                <?php foreach ( $items as $cart_item_key => $cart_item ) :
                    $_product = $cart_item['data'];
                    if ( ! $_product->is_visible() ) continue;
                    $permalink = $_product->get_permalink();
                    $img_id    = $_product->get_image_id();
                    $img_url   = $img_id ? wp_get_attachment_image_url( $img_id, 'thumbnail' ) : wc_placeholder_img_src();
                ?>
                <li class="jt-cart-drawer__item">
                    <a href="<?php echo esc_url( $permalink ); ?>" class="jt-cart-drawer__thumb">
                        <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $_product->get_name() ); ?>" width="64" height="64" loading="lazy">
                    </a>
                    <div class="jt-cart-drawer__info">
                        <a href="<?php echo esc_url( $permalink ); ?>" class="jt-cart-drawer__name text-clamp-2">
                            <?php echo esc_html( $_product->get_name() ); ?>
                        </a>
                        <div class="jt-cart-drawer__price">
                            <?php echo wp_kses_post( WC()->cart->get_product_price( $_product ) ); ?>
                        </div>
                        <div class="jt-cart-drawer__qty">
                            <?php esc_html_e( 'Qty:', 'jendela-ternak' ); ?> <?php echo esc_html( $cart_item['quantity'] ); ?>
                        </div>
                    </div>
                    <a href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>"
                       class="jt-cart-drawer__remove" title="<?php esc_attr_e( 'Hapus', 'jendela-ternak' ); ?>">&times;</a>
                </li>
                <?php endforeach; ?>
            </ul>
            <div class="jt-cart-drawer__footer">
                <div class="jt-cart-drawer__subtotal">
                    <span><?php esc_html_e( 'Subtotal', 'jendela-ternak' ); ?></span>
                    <strong><?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></strong>
                </div>
                <div class="jt-cart-drawer__actions">
                    <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="jt-btn jt-btn--outline">
                        <?php esc_html_e( 'Lihat Keranjang', 'jendela-ternak' ); ?>
                    </a>
                    <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="jt-btn jt-btn--primary">
                        <?php esc_html_e( 'Checkout', 'jendela-ternak' ); ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

// ─────────────────────────────────────────────────────────────────
// 6. INJECT BOTTOM NAV on single product pages
// ─────────────────────────────────────────────────────────────────
add_action( 'woocommerce_after_single_product', 'jt_inject_bottom_nav' );
function jt_inject_bottom_nav() {
    if ( is_singular( 'product' ) ) {
        get_template_part( 'template-parts/product/bottom-nav' );
    }
}

// ─────────────────────────────────────────────────────────────────
// 7. PRODUCT CARD — override loop item template
// ─────────────────────────────────────────────────────────────────
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

// ─────────────────────────────────────────────────────────────────
// 8. BREADCRUMB wrapper
// ─────────────────────────────────────────────────────────────────
add_filter( 'woocommerce_breadcrumb_defaults', function( $defaults ) {
    $defaults['wrap_before'] = '<nav class="jt-breadcrumb" aria-label="Breadcrumb"><ol itemscope itemtype="https://schema.org/BreadcrumbList">';
    $defaults['wrap_after']  = '</ol></nav>';
    $defaults['before']      = '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
    $defaults['after']       = '</li>';
    $defaults['delimiter']   = '';
    return $defaults;
} );

// ─────────────────────────────────────────────────────────────────
// 10. CUSTOM MY ACCOUNT ENDPOINTS
//     Hide downloads tab and rename links to Indonesian
// ─────────────────────────────────────────────────────────────────
add_filter( 'woocommerce_account_menu_items', 'jt_custom_account_menu_items', 99 );
function jt_custom_account_menu_items( $items ) {
    // Hide downloads endpoint
    unset( $items['downloads'] );

    // Rename existing endpoints
    if ( isset( $items['dashboard'] ) ) {
        $items['dashboard'] = __( 'Dashboard', 'jendela-ternak' );
    }
    if ( isset( $items['orders'] ) ) {
        $items['orders'] = __( 'Pesanan Saya', 'jendela-ternak' );
    }
    if ( isset( $items['edit-address'] ) ) {
        $items['edit-address'] = __( 'Alamat Pengiriman', 'jendela-ternak' );
    }
    if ( isset( $items['edit-account'] ) ) {
        $items['edit-account'] = __( 'Detail Akun', 'jendela-ternak' );
    }
    if ( isset( $items['customer-logout'] ) ) {
        $items['customer-logout'] = __( 'Keluar', 'jendela-ternak' );
    }

    return $items;
}
