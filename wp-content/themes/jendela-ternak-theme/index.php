<?php
/**
 * index.php — fallback template
 *
 * @package JendelaTernakMalang
 */
defined( 'ABSPATH' ) || exit;

get_header(); ?>

<main id="main-content" class="jt-main">
    <div class="jt-container">
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) : the_post();
                get_template_part( 'template-parts/product/product-card' );
            endwhile;
        else :
            echo '<p class="jt-no-posts">' . esc_html__( 'No content found.', 'jendela-ternak' ) . '</p>';
        endif;
        ?>
    </div>
</main>

<?php get_footer();
