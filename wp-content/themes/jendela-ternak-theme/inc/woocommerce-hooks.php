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

// Remove default WooCommerce proceed to checkout button in cart totals (we render our own custom green button)
remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );

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

    // Insert Wishlist before Logout
    $logout = isset( $items['customer-logout'] ) ? $items['customer-logout'] : '';
    unset( $items['customer-logout'] );

    $items['wishlist'] = __( 'Wishlist Saya', 'jendela-ternak' );

    if ( $logout ) {
        $items['customer-logout'] = __( 'Keluar', 'jendela-ternak' );
    }

    return $items;
}

// ─────────────────────────────────────────────────────────────────
// 11. CUSTOM PAGINATION ARGUMENTS (Prev/Next SVG Arrows)
// ─────────────────────────────────────────────────────────────────
add_filter( 'woocommerce_pagination_args', 'jt_custom_pagination_args' );
function jt_custom_pagination_args( $args ) {
    $args['prev_text'] = '<span class="jt-arrow-icon-circle"><svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:block;"><line x1="12" y1="8" x2="4" y2="8"></line><polyline points="8 12 4 8 8 4"></polyline></svg></span> ' . esc_html__( 'Previous', 'jendela-ternak' );
    $args['next_text'] = esc_html__( 'Next', 'jendela-ternak' ) . ' <span class="jt-arrow-icon-circle"><svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:block;"><line x1="4" y1="8" x2="12" y2="8"></line><polyline points="8 4 12 8 8 12"></polyline></svg></span>';
    return $args;
}

// ─────────────────────────────────────────────────────────────────
// 12. WISHLIST MY ACCOUNT ENDPOINT REGISTRATION
// ─────────────────────────────────────────────────────────────────
add_action( 'init', 'jt_add_wishlist_endpoint' );
function jt_add_wishlist_endpoint() {
    add_rewrite_endpoint( 'wishlist', EP_PAGES );
}

add_filter( 'query_vars', 'jt_wishlist_query_vars', 0 );
function jt_wishlist_query_vars( $vars ) {
    $vars[] = 'wishlist';
    return $vars;
}

add_action( 'woocommerce_account_wishlist_endpoint', 'jt_wishlist_endpoint_content' );
function jt_wishlist_endpoint_content() {
    get_template_part( 'template-parts/product/wishlist-content' );
}

// ─────────────────────────────────────────────────────────────────
// 13. AJAX WISHLIST PRODUCTS FETCH
// ─────────────────────────────────────────────────────────────────
add_action( 'wp_ajax_jt_get_wishlist_products', 'jt_get_wishlist_products' );
add_action( 'wp_ajax_nopriv_jt_get_wishlist_products', 'jt_get_wishlist_products' );

function jt_get_wishlist_products() {
    if ( ! isset( $_POST['product_ids'] ) || ! is_array( $_POST['product_ids'] ) ) {
        wp_send_json_error( 'Invalid product list' );
    }

    $product_ids = array_map( 'absint', $_POST['product_ids'] );

    if ( empty( $product_ids ) ) {
        ob_start();
        get_template_part( 'template-parts/product/wishlist-empty-state' );
        $html = ob_get_clean();
        wp_send_json_success( [ 'html' => $html, 'count' => 0 ] );
    }

    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'post__in'       => $product_ids,
        'orderby'        => 'post__in',
    );

    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            global $product;
            $product = wc_get_product( get_the_ID() );
            $id = $product->get_id();
            ?>
            <div class="jt-wishlist-card-wrapper" data-product-id="<?php echo esc_attr( $id ); ?>">
                <!-- Remove from wishlist button (X) -->
                <button type="button" class="jt-wishlist-remove-btn" @click="removeFromWishlist(<?php echo esc_attr( $id ); ?>, $event)" aria-label="<?php esc_attr_e( 'Hapus dari Wishlist', 'jendela-ternak' ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Standard Product Card -->
                <?php get_template_part( 'template-parts/product/product-card' ); ?>

                <!-- Quick AJAX Add to Cart button overlay -->
                <div class="jt-wishlist-hover-action">
                    <button type="button" class="jt-btn jt-btn--primary jt-btn--sm jt-wishlist-add-cart-btn" @click="quickAddToCart(<?php echo esc_attr( $id ); ?>, $event)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <?php esc_html_e( 'Masukkan Keranjang', 'jendela-ternak' ); ?>
                    </button>
                </div>
            </div>
            <?php
        }
        wp_reset_postdata();
    } else {
        get_template_part( 'template-parts/product/wishlist-empty-state' );
    }
    $html = ob_get_clean();

    wp_send_json_success( [ 'html' => $html, 'count' => $query->found_posts ] );
}

// ─────────────────────────────────────────────────────────────────
// 14. REDIRECT STANDALONE WISHLIST PAGE TO MY ACCOUNT ENDPOINT
// ─────────────────────────────────────────────────────────────────
add_action( 'template_redirect', 'jt_redirect_standalone_wishlist' );
function jt_redirect_standalone_wishlist() {
    global $wp;
    $current_slug = isset( $wp->request ) ? trim( $wp->request, '/' ) : '';
    if ( 'wishlist' === $current_slug || is_page( 'wishlist' ) ) {
        wp_safe_redirect( wc_get_account_endpoint_url( 'wishlist' ) );
        exit;
    }
}

// ─────────────────────────────────────────────────────────────────
// 15. CUSTOM CHECKOUT ORDER BUTTON TEXT
// ─────────────────────────────────────────────────────────────────
add_filter( 'woocommerce_order_button_text', 'jt_custom_checkout_button_text' );
function jt_custom_checkout_button_text( $button_text ) {
    return __( 'Pesan Sekarang', 'jendela-ternak' );
}

