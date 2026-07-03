<?php
/**
 * page.php — standard page template
 *
 * Mendukung dua mode:
 * 1. Elementor Template Location aktif → Elementor render konten langsung (antara header/footer tema).
 * 2. Elementor content via the_content() → konten Elementor dirender di dalam .jt-main,
 *    lebar dikontrol oleh CSS elementor-compat.css (bukan tema container).
 * 3. Halaman biasa (non-Elementor) → layout container .jt-container seperti biasa.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

get_header();

/**
 * Mode 1: Elementor Theme Location 'single' aktif.
 * Elementor mengambil alih rendering konten sepenuhnya.
 * Header & footer tema tetap tampil.
 */
if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'single' ) ) {
    get_footer();
    return;
}

// Deteksi apakah halaman ini dibangun dengan Elementor (via the_content mode)
$is_elementor_page = (
    class_exists( '\Elementor\Plugin' )
    && isset( \Elementor\Plugin::$instance->db )
    && is_singular()
    && \Elementor\Plugin::$instance->db->is_built_with_elementor( get_the_ID() )
);
?>

<main id="main-content" class="jt-main" role="main">

    <?php if ( $is_elementor_page ) : ?>
        <?php
        /**
         * Mode 2: Konten dibangun Elementor — render langsung di dalam .jt-main
         * tanpa .jt-container pembatas tema. Lebar section Elementor dikontrol
         * oleh CSS di elementor-compat.css (max-width: 1280px via --container-max).
         */
        while ( have_posts() ) :
            the_post();
            the_content();
        endwhile;
        ?>

    <?php else : ?>
        <?php
        /**
         * Mode 3: Halaman biasa (Classic Editor / blok Gutenberg)
         * Gunakan .jt-container untuk membatasi lebar konten.
         */
        ?>
        <div class="jt-container">
            <?php
            while ( have_posts() ) :
                the_post();
                ?>
                <article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php if ( ! is_front_page() && ! is_cart() && ! is_checkout() && ! is_account_page() ) : ?>
                        <h1 class="jt-page-title" style="font-size:24px;font-weight:800;color:var(--color-primary);margin-bottom:16px;">
                            <?php the_title(); ?>
                        </h1>
                    <?php endif; ?>

                    <div class="jt-page-content">
                        <?php
                        the_content();

                        wp_link_pages( [
                            'before' => '<div class="page-links">' . esc_html__( 'Halaman:', 'jendela-ternak' ),
                            'after'  => '</div>',
                        ] );
                        ?>
                    </div>
                </article>
                <?php
            endwhile;
            ?>
        </div>

    <?php endif; ?>

</main>

<?php get_footer();

