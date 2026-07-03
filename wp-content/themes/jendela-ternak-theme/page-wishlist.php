<?php
/**
 * Template Name: Wishlist Page
 * page-wishlist.php
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

get_header(); ?>

<main id="main-content" class="jt-main" role="main">
    <div class="jt-container">
        <h1 class="jt-page-title" style="font-size:24px;font-weight:800;color:var(--color-primary);margin-bottom:20px;">
            <?php esc_html_e( 'Wishlist Saya', 'jendela-ternak' ); ?>
        </h1>

        <div class="jt-page-content">
            <?php get_template_part( 'template-parts/product/wishlist-content' ); ?>
        </div>
    </div>
</main>

<?php get_footer();
