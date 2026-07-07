<?php
/**
 * Empty cart page
 *
 * Customized layout for empty shopping cart page.
 *
 * @package JendelaTernakMalang
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked wc_empty_cart_message - 10
 */
?>

<div class="jt-empty-cart-page-wrapper font-sans py-16 px-4 max-w-lg mx-auto text-center">
    <!-- Shopee-style Empty State Illustration -->
    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gray-50 text-gray-300 mb-6 text-5xl">
        <i class="fa-solid fa-basket-shopping"></i>
    </div>
    
    <h2 class="text-xl font-bold text-gray-800 mb-2"><?php esc_html_e( 'Keranjang Belanja Anda Kosong', 'jendela-ternak' ); ?></h2>
    
    <p class="text-sm text-gray-400 mb-8 leading-relaxed">
        <?php esc_html_e( 'Yuk, isi keranjang belanjaan Anda dengan produk pakan berkualitas, vitamin, obat hewan, dan peralatan ternak terbaik dari Jendela Ternak Malang!', 'jendela-ternak' ); ?>
    </p>

    <!-- Hidden WooCommerce default notice to maintain action hook compatibility -->
    <div class="hidden">
        <?php do_action( 'woocommerce_cart_is_empty' ); ?>
    </div>

    <?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
        <p class="return-to-shop m-0">
            <a class="jt-btn jt-btn--primary bg-green-700 hover:bg-green-800 text-white font-bold py-3 px-8 rounded-lg transition shadow-sm inline-flex items-center gap-2" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
                <i class="fa-solid fa-store"></i>
                <?php
                    echo esc_html( apply_filters( 'woocommerce_return_to_shop_text', __( 'Mulai Belanja', 'jendela-ternak' ) ) );
                ?>
            </a>
        </p>
    <?php endif; ?>
</div>
