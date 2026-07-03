<?php
/**
 * woocommerce/archive-product.php
 * Custom WooCommerce Catalog page layout with desktop sidebar and mobile filter drawer.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

// Remove default archive headers and loops which we replace with custom layouts
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
?>

<div class="jt-shop-layout jt-container" x-data="jtShopFilter()" id="jt-shop-layout-root">

    <div class="jt-shop-grid-wrapper">

        <!-- ── Left Column: Desktop Sidebar Filter (Hidden on Mobile) ── -->
        <aside id="jt-desktop-sidebar" class="jt-shop-sidebar hide-mobile" aria-label="<?php esc_attr_e( 'Filter Desktop', 'jendela-ternak' ); ?>">
            <?php get_template_part( 'template-parts/shop/sidebar-filters' ); ?>
        </aside>

        <!-- ── Right Column: Catalog Grid ── -->
        <div class="jt-shop-catalog-column">

            <!-- Catalog Top Bar (Title + Ordering + Mobile filter toggle) -->
            <div class="jt-catalog-header">
                <h1 class="jt-catalog-title"><?php woocommerce_page_title(); ?></h1>
                
                <div class="jt-catalog-actions">
                    <!-- Mobile Filter Toggle Button -->
                    <button 
                        id="jt-mobile-filter-btn"
                        class="jt-btn jt-btn--outline show-mobile" 
                        @click="mobileFilterOpen = true" 
                        aria-label="<?php esc_attr_e( 'Buka Filter', 'jendela-ternak' ); ?>"
                    >
                        🔍 <?php esc_html_e( 'Filter', 'jendela-ternak' ); ?>
                    </button>

                    <!-- Sort dropdown -->
                    <?php woocommerce_catalog_ordering(); ?>
                </div>
            </div>

            <!-- Notifications & Notices -->
            <div class="jt-catalog-notices">
                <?php woocommerce_output_all_notices(); ?>
            </div>

            <!-- Loop Products wrapper with loading state -->
            <div class="jt-products-grid-container" style="position:relative;">
                <!-- Loading Spinner Overlay -->
                <div 
                    class="jt-filter-loading-overlay" 
                    x-show="loading" 
                    x-cloak 
                    aria-hidden="true"
                >
                    <div class="jt-spinner"></div>
                </div>

                <div id="jt-products-catalog-grid" :class="loading ? 'jt-opacity-50' : ''">
                    <?php if ( woocommerce_product_loop() ) : ?>

                        <div class="jt-products-grid">
                            <?php while ( have_posts() ) : the_post(); ?>
                                <?php get_template_part( 'template-parts/product/product-card' ); ?>
                            <?php endwhile; ?>
                        </div>

                    <?php else : ?>
                        <!-- No Products Found -->
                        <div class="jt-no-products">
                            <svg width="64" height="64" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="opacity:0.3;margin:0 auto 12px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p><?php esc_html_e( 'Tidak ada produk yang cocok dengan pilihan Anda.', 'jendela-ternak' ); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pagination -->
            <div class="jt-catalog-pagination">
                <?php woocommerce_pagination(); ?>
            </div>

        </div><!-- .jt-shop-catalog-column -->

    </div><!-- .jt-shop-grid-wrapper -->

    <!-- ── Mobile Slide-over Overlay ── -->
    <div 
        x-show="mobileFilterOpen" 
        x-cloak 
        class="jt-cart-overlay" 
        @click="mobileFilterOpen = false"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        aria-hidden="true"
    ></div>

    <!-- ── Mobile Slide-over Sidebar Filter Drawer ── -->
    <aside 
        id="jt-mobile-filter-drawer" 
        class="jt-cart-drawer" 
        x-show="mobileFilterOpen" 
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        role="dialog"
        aria-modal="true"
        aria-label="<?php esc_attr_e( 'Filter Produk Mobile', 'jendela-ternak' ); ?>"
        style="transform: translateX(100%);"
        :style="mobileFilterOpen ? 'transform: translateX(0)' : 'transform: translateX(100%)'"
    >
        <div class="jt-cart-drawer__header">
            <h2>🔍 <?php esc_html_e( 'Filter Produk', 'jendela-ternak' ); ?></h2>
            <button class="jt-cart-drawer__close" @click="mobileFilterOpen = false" aria-label="<?php esc_attr_e( 'Tutup Filter', 'jendela-ternak' ); ?>">&times;</button>
        </div>
        
        <div style="flex:1; overflow-y:auto; padding:20px;">
            <?php get_template_part( 'template-parts/shop/sidebar-filters' ); ?>
        </div>
    </aside>

</div><!-- .jt-shop-layout -->

<?php
get_footer( 'shop' );
