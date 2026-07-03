<?php
/**
 * page-blog.php
 * Custom blog archive template consistent with theme header/footer.
 *
 * @package JendelaTernakMalang
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main-content" class="jt-main" aria-label="Blog">
    <div class="jt-container">

        <!-- Page Header -->
        <div style="text-align:center;padding:32px 0 24px;">
            <h1 class="jt-section-title" style="font-size:28px;margin:0 auto;display:inline-block;">
                📰 <?php esc_html_e( 'Blog & Artikel Edukasi', 'jendela-ternak' ); ?>
            </h1>
            <p style="color:var(--color-text-muted);margin-top:12px;font-size:15px;">
                <?php esc_html_e( 'Tips peternakan, kesehatan hewan, dan info promo terkini.', 'jendela-ternak' ); ?>
            </p>
        </div>

        <?php if ( have_posts() ) : ?>

            <!-- Blog Grid -->
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-bottom:40px;" class="jt-blog-grid">
                <?php while ( have_posts() ) : the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class( 'jt-blog-card' ); ?> style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);transition:transform 0.2s,box-shadow 0.2s;" onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(11,94,52,0.12)'" onmouseleave="this.style.transform='';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'">

                    <?php if ( has_post_thumbnail() ) : ?>
                    <a href="<?php the_permalink(); ?>" style="display:block;aspect-ratio:16/9;overflow:hidden;">
                        <?php the_post_thumbnail( 'medium_large', [ 'style' => 'width:100%;height:100%;object-fit:cover;' ] ); ?>
                    </a>
                    <?php endif; ?>

                    <div style="padding:16px;">
                        <!-- Category -->
                        <?php
                        $cats = get_the_category();
                        if ( $cats ) :
                            $cat = $cats[0];
                        ?>
                        <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"
                           style="display:inline-block;background:var(--color-accent);color:#1A1A1A;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;padding:2px 10px;border-radius:9999px;margin-bottom:8px;">
                            <?php echo esc_html( $cat->name ); ?>
                        </a>
                        <?php endif; ?>

                        <!-- Title -->
                        <h2 style="font-size:15px;font-weight:700;color:var(--color-primary);line-height:1.4;margin-bottom:8px;display:-webkit-box;-webkit-line-clamp:2;line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            <a href="<?php the_permalink(); ?>" style="color:inherit;"><?php the_title(); ?></a>
                        </h2>

                        <!-- Excerpt -->
                        <p style="font-size:13px;color:var(--color-text-muted);line-height:1.6;display:-webkit-box;-webkit-line-clamp:3;line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:12px;">
                            <?php echo esc_html( get_the_excerpt() ); ?>
                        </p>

                        <!-- Meta -->
                        <div style="display:flex;align-items:center;justify-content:space-between;font-size:11px;color:var(--color-text-muted);">
                            <span>📅 <?php echo esc_html( get_the_date( 'j M Y' ) ); ?></span>
                            <a href="<?php the_permalink(); ?>"
                               style="color:var(--color-secondary);font-weight:600;font-size:12px;">
                                <?php esc_html_e( 'Baca Selengkapnya →', 'jendela-ternak' ); ?>
                            </a>
                        </div>
                    </div>

                </article>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <div style="display:flex;justify-content:center;margin-bottom:40px;">
                <?php
                the_posts_pagination( [
                    'mid_size'  => 2,
                    'prev_text' => '← ' . __( 'Sebelumnya', 'jendela-ternak' ),
                    'next_text' => __( 'Selanjutnya', 'jendela-ternak' ) . ' →',
                ] );
                ?>
            </div>

        <?php else : ?>
            <div style="text-align:center;padding:60px 20px;color:var(--color-text-muted);">
                <div style="font-size:64px;margin-bottom:16px;">📭</div>
                <p style="font-size:16px;"><?php esc_html_e( 'Belum ada artikel yang diterbitkan.', 'jendela-ternak' ); ?></p>
            </div>
        <?php endif; ?>

    </div><!-- .jt-container -->
</main>

<?php get_footer();
