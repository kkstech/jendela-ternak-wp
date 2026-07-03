<?php
/**
 * template-parts/product/bottom-nav.php
 * Mobile-only sticky bottom navigation bar for single product pages.
 * Hidden on desktop via CSS (min-width: 768px → display:none).
 *
 * @package JendelaTernakMalang
 */
defined( 'ABSPATH' ) || exit;

global $product;
if ( ! $product instanceof WC_Product ) {
    $product = wc_get_product( get_the_ID() );
}
if ( ! $product || ! $product->is_purchasable() ) {
    return;
}

$product_id = $product->get_id();
$in_stock   = $product->is_in_stock();
?>

<nav
    id="jt-bottom-nav"
    class="jt-bottom-nav"
    x-data="bottomNav"
    data-product-id="<?php echo esc_attr( $product_id ); ?>"
    role="navigation"
    aria-label="<?php esc_attr_e( 'Aksi Produk Mobile', 'jendela-ternak' ); ?>"
>
    <!-- Toast message -->
    <div
        x-show="message"
        x-cloak
        x-text="message"
        x-transition
        style="position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:#1A1A1A;color:#fff;padding:10px 20px;border-radius:9999px;font-size:13px;font-weight:600;z-index:200;white-space:nowrap;box-shadow:0 4px 16px rgba(0,0,0,0.2);"
    ></div>

    <!-- Wishlist Button -->
    <button
        id="jt-bn-wishlist"
        class="jt-bottom-nav__wishlist"
        :class="{ 'wishlisted': wishlisted }"
        @click="toggleWishlist()"
        :title="wishlisted ? '<?php esc_attr_e( 'Hapus dari Wishlist', 'jendela-ternak' ); ?>' : '<?php esc_attr_e( 'Tambah ke Wishlist', 'jendela-ternak' ); ?>'"
        :aria-pressed="wishlisted"
        aria-label="<?php esc_attr_e( 'Wishlist', 'jendela-ternak' ); ?>"
    >
        <svg xmlns="http://www.w3.org/2000/svg"
             :fill="wishlisted ? 'currentColor' : 'none'"
             viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
    </button>

    <?php if ( $in_stock ) : ?>

        <!-- Add to Cart Button -->
        <button
            id="jt-bn-add-to-cart"
            class="jt-bottom-nav__add-to-cart"
            @click="$store.pdpDrawer.show('cart')"
            aria-label="<?php esc_attr_e( 'Tambah ke Keranjang', 'jendela-ternak' ); ?>"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span><?php esc_html_e( 'Keranjang', 'jendela-ternak' ); ?></span>
        </button>

        <!-- Buy Now Button -->
        <button
            id="jt-bn-buy-now"
            class="jt-bottom-nav__buy-now"
            @click="$store.pdpDrawer.show('buy')"
            aria-label="<?php esc_attr_e( 'Beli Sekarang', 'jendela-ternak' ); ?>"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <span><?php esc_html_e( 'Beli Sekarang', 'jendela-ternak' ); ?></span>
        </button>

    <?php else : ?>

        <!-- Out of Stock placeholder -->
        <button class="jt-bottom-nav__add-to-cart" disabled style="opacity:0.5;flex:2;" aria-disabled="true">
            <?php esc_html_e( 'Stok Habis', 'jendela-ternak' ); ?>
        </button>

    <?php endif; ?>

</nav><!-- .jt-bottom-nav -->

<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
