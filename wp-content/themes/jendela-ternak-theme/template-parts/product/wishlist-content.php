<?php
/**
 * template-parts/product/wishlist-content.php
 * Reusable wishlist page content. Used in page-wishlist.php and My Account endpoint.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

// Query popular products for empty state recommendation
$recommendation_args = array(
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => 5,
    'meta_key'       => 'total_sales',
    'orderby'        => 'meta_value_num',
    'order'          => 'DESC',
);
$recommendations = new WP_Query( $recommendation_args );
?>

<div id="jt-wishlist-page-container" class="jt-wishlist-container" x-data="wishlistPage()" x-init="initPage()">
    <!-- Notification Toast -->
    <div 
        class="jt-toast-notification" 
        x-show="message" 
        x-text="message" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        x-cloak
    ></div>

    <!-- 1. LOADING SKELETON STATE -->
    <div class="jt-wishlist-skeleton-grid" x-show="loading" x-cloak>
        <template x-for="i in 5">
            <div class="jt-wishlist-skeleton-card">
                <div class="jt-wishlist-skeleton-img jt-skeleton-shimmer"></div>
                <div class="jt-wishlist-skeleton-text jt-skeleton-shimmer short"></div>
                <div class="jt-wishlist-skeleton-text jt-skeleton-shimmer medium"></div>
                <div class="jt-wishlist-skeleton-text jt-skeleton-shimmer long"></div>
            </div>
        </template>
    </div>

    <!-- 2. EMPTY STATE -->
    <div class="jt-wishlist-empty-state" x-show="isEmpty" x-cloak>
        <div class="jt-wishlist-empty-graphic">
            <!-- Customized Heart & Pet footprints themed SVG -->
            <svg class="mx-auto text-gray-300" width="120" height="120" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Large Dashed Heart -->
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" stroke="var(--color-primary)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="3 3"/>
                <!-- Cute Animal Footprint (Jejak Kaki Ternak) inside the heart in accent gold color -->
                <circle cx="12" cy="11.5" r="2.5" fill="var(--color-accent)"/>
                <circle cx="8.5" cy="8.5" r="1.5" fill="var(--color-accent)"/>
                <circle cx="15.5" cy="8.5" r="1.5" fill="var(--color-accent)"/>
                <circle cx="8" cy="12" r="1.2" fill="var(--color-accent)"/>
                <circle cx="16" cy="12" r="1.2" fill="var(--color-accent)"/>
            </svg>
        </div>
        
        <h3 class="jt-wishlist-empty-title"><?php esc_html_e( 'Wishlist kamu kosong nih', 'jendela-ternak' ); ?></h3>
        <p class="jt-wishlist-empty-text"><?php esc_html_e( 'Yuk, jelajahi ribuan produk pakan, vitamin, dan perlengkapan hewan ternak terbaik kami dan simpan produk favoritmu di sini.', 'jendela-ternak' ); ?></p>
        
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="jt-btn jt-btn--primary jt-wishlist-shop-btn">
            <?php esc_html_e( 'Jelajahi Produk sekarang', 'jendela-ternak' ); ?>
        </a>

        <!-- Recommendation Section inside empty state -->
        <?php if ( $recommendations->have_posts() ) : ?>
            <div class="jt-wishlist-recommendations-wrapper">
                <h4 class="jt-wishlist-rec-title">
                    <span>🌟</span> <?php esc_html_e( 'Rekomendasi Produk Terlaris', 'jendela-ternak' ); ?>
                </h4>
                <div class="jt-products-grid">
                    <?php
                    while ( $recommendations->have_posts() ) :
                        $recommendations->the_post();
                        get_template_part( 'template-parts/product/product-card' );
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- 3. WISHLIST PRODUCTS GRID -->
    <div x-show="!isEmpty && !loading" x-cloak>
        <div class="jt-wishlist-grid-header">
            <span class="jt-wishlist-count-badge" x-text="wishlistCountText()"></span>
            <button type="button" class="jt-wishlist-clear-all" @click="clearAllWishlist()">
                🗑️ <?php esc_html_e( 'Hapus Semua', 'jendela-ternak' ); ?>
            </button>
        </div>

        <div class="jt-products-grid jt-wishlist-grid" x-html="productsHtml"></div>
    </div>
</div>
