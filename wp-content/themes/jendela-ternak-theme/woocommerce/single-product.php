<?php
/**
 * woocommerce/single-product.php
 * Override for single product page layout.
 * Copied from: woocommerce/templates/single-product.php
 *
 * @package JendelaTernakMalang
 * @version 1.6.4
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' ); ?>

<?php
/**
 * Hook: woocommerce_before_main_content.
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );
?>

<?php while ( have_posts() ) : ?>
    <?php the_post(); ?>
    <?php wc_get_template_part( 'content', 'single-product' ); ?>
<?php endwhile; ?>

<?php
/**
 * Hook: woocommerce_after_main_content.
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );
?>

<?php get_footer( 'shop' );
