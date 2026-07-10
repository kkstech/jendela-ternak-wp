<?php
/**
 * inc/shortcodes.php
 * Custom shortcodes for the Jendela Ternak theme.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shortcode to render the default homepage layout.
 * Usage: [jt_homepage]
 */
add_shortcode( 'jt_homepage', 'jt_homepage_shortcode' );
function jt_homepage_shortcode() {
    ob_start();

    // Retrieve custom options
    $settings     = get_option( 'jt_theme_settings', array() );
    $banner1_img  = $settings['banner1_img']  ?? '';
    $banner1_link = $settings['banner1_link'] ?? '#';
    $banner2_img  = $settings['banner2_img']  ?? '';
    $banner2_link = $settings['banner2_link'] ?? '#';

    // Dynamic section settings
    $section_title_bestseller = jt_get_setting( 'section_title_bestseller', 'Produk Terlaris' );
    $section_title_offers     = jt_get_setting( 'section_title_offers',     'Penawaran Terbaik' );
    $show_banners             = jt_get_setting( 'toggle_banners', '1' ) === '1';
    ?>

    <main id="main-content" class="min-h-screen bg-gray-50/50 pb-16" aria-label="<?php esc_attr_e( 'Halaman Utama', 'jendela-ternak' ); ?>">

        <!-- Hero Slider Section -->
        <div class="max-w-7xl mx-auto px-4 pt-6">
            <?php get_template_part( 'template-parts/homepage/hero-banner' ); ?>
        </div>

        <!-- Main Content Container -->
        <div class="max-w-7xl mx-auto px-4 mt-6 md:mt-8 space-y-6 md:space-y-8">

            <!-- 1. Section: Produk Terlaris -->
            <section aria-labelledby="jt-best-sellers-heading" class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🔥</span>
                        <h2 class="text-lg md:text-xl font-extrabold text-[#0B5E34]" id="jt-best-sellers-heading">
                            <?php echo esc_html( $section_title_bestseller ); ?>
                        </h2>
                    </div>
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="text-xs font-bold text-[#0B5E34] hover:underline">
                        <?php esc_html_e( 'Lihat Semua →', 'jendela-ternak' ); ?>
                    </a>
                </div>

                <?php
                $terlaris_args = [
                    'post_type'      => 'product',
                    'posts_per_page' => 12,
                    'post_status'    => 'publish',
                    'orderby'        => 'meta_value_num',
                    'meta_key'       => 'total_sales',
                    'order'          => 'DESC',
                ];
                $terlaris_query = new WP_Query( $terlaris_args );

                if ( $terlaris_query->have_posts() ) : ?>
                    <div class="jt-horizontal-scroll">
                        <?php while ( $terlaris_query->have_posts() ) :
                            $terlaris_query->the_post();
                            get_template_part( 'template-parts/product/product-card' );
                        endwhile;
                        wp_reset_postdata(); ?>
                    </div>
                <?php else : ?>
                    <p class="text-xs text-gray-400 py-6 text-center">Belum ada produk terlaris saat ini.</p>
                <?php endif; ?>
            </section>

            <!-- 2. Banner Image 1 -->
            <?php if ( $show_banners ) : ?>
            <div class="w-full">
                <?php if ( ! empty( $banner1_img ) ) : ?>
                    <a href="<?php echo esc_url( $banner1_link ); ?>" class="block w-full rounded-2xl overflow-hidden shadow-sm hover:shadow-md hover:scale-[1.01] transition-all duration-300">
                        <img src="<?php echo esc_url( $banner1_img ); ?>" alt="Promo Banner 1" class="w-full h-auto">
                    </a>
                <?php else : ?>
                    <a href="<?php echo esc_url( $banner1_link ); ?>" class="block w-full h-[130px] md:h-[180px] bg-gradient-to-r from-[#0B5E34] to-[#C8D400] rounded-2xl flex items-center justify-between px-8 md:px-16 text-white shadow-sm hover:shadow-md transition-all duration-300 transform hover:scale-[1.01] overflow-hidden relative">
                        <div class="space-y-1.5 relative z-10">
                            <span class="text-[9px] md:text-xs font-black uppercase tracking-wider text-black bg-[#D4B106] px-2.5 py-1 rounded-full inline-block">Penawaran Spesial</span>
                            <h3 class="text-base md:text-2xl font-black">Peralatan Peternakan Berkualitas</h3>
                            <p class="text-[10px] md:text-xs text-green-50 opacity-90">Hemat hingga 40% untuk produk kandang & tempat makan ayam otomatis.</p>
                        </div>
                        <div class="text-xs md:text-sm font-bold bg-white text-[#0B5E34] px-5 py-2.5 rounded-xl hidden sm:block relative z-10 shadow-sm whitespace-nowrap">Lihat Koleksi</div>
                        <div class="absolute right-0 top-0 bottom-0 w-1/3 bg-white/10 skew-x-12 transform origin-top-right"></div>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; // show_banners ?>

            <!-- 3. Section: Penawaran Terbaik -->
            <section aria-labelledby="jt-best-offers-heading" class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">⚡</span>
                        <h2 class="text-lg md:text-xl font-extrabold text-[#0B5E34]" id="jt-best-offers-heading">
                            <?php echo esc_html( $section_title_offers ); ?>
                        </h2>
                    </div>
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="text-xs font-bold text-[#0B5E34] hover:underline">
                        <?php esc_html_e( 'Lihat Semua →', 'jendela-ternak' ); ?>
                    </a>
                </div>

                <?php
                $best_offers_args = [
                    'post_type'      => 'product',
                    'posts_per_page' => 12,
                    'post_status'    => 'publish',
                    'post__in'       => array_merge( array( 0 ), wc_get_product_ids_on_sale() ),
                ];
                $best_offers_query = new WP_Query( $best_offers_args );
                
                // Fallback if no sale products exist
                if ( ! $best_offers_query->have_posts() ) {
                    $best_offers_args = [
                        'post_type'      => 'product',
                        'posts_per_page' => 12,
                        'post_status'    => 'publish',
                    ];
                    $best_offers_query = new WP_Query( $best_offers_args );
                }

                if ( $best_offers_query->have_posts() ) : ?>
                    <div class="jt-horizontal-scroll">
                        <?php while ( $best_offers_query->have_posts() ) :
                            $best_offers_query->the_post();
                            get_template_part( 'template-parts/product/product-card' );
                        endwhile;
                        wp_reset_postdata(); ?>
                    </div>
                <?php else : ?>
                    <p class="text-xs text-gray-400 py-6 text-center">Belum ada penawaran saat ini.</p>
                <?php endif; ?>
            </section>

            <!-- 4. Banner Image 2 -->
            <?php if ( $show_banners ) : ?>
            <div class="w-full">
                <?php if ( ! empty( $banner2_img ) ) : ?>
                    <a href="<?php echo esc_url( $banner2_link ); ?>" class="block w-full rounded-2xl overflow-hidden shadow-sm hover:shadow-md hover:scale-[1.01] transition-all duration-300">
                        <img src="<?php echo esc_url( $banner2_img ); ?>" alt="Promo Banner 2" class="w-full h-auto">
                    </a>
                <?php else : ?>
                    <a href="<?php echo esc_url( $banner2_link ); ?>" class="block w-full h-[130px] md:h-[180px] bg-gradient-to-r from-[#4CAF50] to-[#0B5E34] rounded-2xl flex items-center justify-between px-8 md:px-16 text-white shadow-sm hover:shadow-md transition-all duration-300 transform hover:scale-[1.01] overflow-hidden relative">
                        <div class="space-y-1.5 relative z-10">
                            <span class="text-[9px] md:text-xs font-black uppercase tracking-wider text-black bg-[#D4B106] px-2.5 py-1 rounded-full inline-block">Rekomendasi Nutrisi</span>
                            <h3 class="text-base md:text-2xl font-black">Pakan Ternak Tinggi Protein</h3>
                            <p class="text-[10px] md:text-xs text-green-50 opacity-90">Nutrisi lengkap untuk mempercepat pertumbuhan dan kesehatan hewan ternak.</p>
                        </div>
                        <div class="text-xs md:text-sm font-bold bg-white text-[#0B5E34] px-5 py-2.5 rounded-xl hidden sm:block relative z-10 shadow-sm whitespace-nowrap">Beli Sekarang</div>
                        <div class="absolute right-0 top-0 bottom-0 w-1/3 bg-white/10 skew-x-12 transform origin-top-right"></div>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; // show_banners ?>

        </div><!-- .jt-container -->

    </main>

    <?php
    return ob_get_clean();
}
