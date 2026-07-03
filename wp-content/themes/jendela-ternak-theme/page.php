<?php
/**
 * page.php — standard page template
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

get_header(); ?>

<main id="main-content" class="jt-main" role="main">
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
</main>

<?php get_footer();
