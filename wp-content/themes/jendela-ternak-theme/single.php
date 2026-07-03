<?php
/**
 * single.php — standard single post template
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
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'jt-single-post' ); ?> style="background:#fff;border-radius:12px;padding:24px;box-shadow:var(--shadow-sm);">
                
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="jt-post-thumbnail" style="margin-bottom:20px;border-radius:8px;overflow:hidden;aspect-ratio:21/9;">
                        <?php the_post_thumbnail( 'large', [ 'style' => 'width:100%;height:100%;object-fit:cover;' ] ); ?>
                    </div>
                <?php endif; ?>

                <h1 class="jt-post-title" style="font-size:28px;font-weight:800;color:var(--color-primary);margin-bottom:8px;">
                    <?php the_title(); ?>
                </h1>
                
                <div class="jt-post-meta" style="font-size:12px;color:var(--color-text-muted);margin-bottom:20px;">
                    <span>📅 <?php echo esc_html( get_the_date( 'j F Y' ) ); ?></span>
                    <span style="margin:0 8px;">·</span>
                    <span>✍️ <?php the_author(); ?></span>
                </div>

                <div class="jt-post-content" style="font-size:15px;line-height:1.8;color:var(--color-text);">
                    <?php
                    the_content();
                    
                    wp_link_pages( [
                        'before' => '<div class="page-links">' . esc_html__( 'Halaman:', 'jendela-ternak' ),
                        'after'  => '</div>',
                    ] );
                    ?>
                </div>
                
                <div class="jt-post-footer" style="margin-top:30px;padding-top:20px;border-top:1px solid var(--color-border);display:flex;justify-content:space-between;align-items:center;">
                    <a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="jt-btn jt-btn--outline">
                        ← <?php esc_html_e( 'Kembali ke Blog', 'jendela-ternak' ); ?>
                    </a>
                </div>
            </article>
            <?php
        endwhile;
        ?>
    </div>
</main>

<?php get_footer();
