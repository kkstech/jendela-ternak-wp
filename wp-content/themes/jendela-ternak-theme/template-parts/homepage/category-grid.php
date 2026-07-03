<?php
/**
 * template-parts/homepage/category-grid.php
 * Hexagon category icon grid — 8-10 categories.
 *
 * @package JendelaTernakMalang
 */
defined( 'ABSPATH' ) || exit;

$categories = get_terms( [
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'number'     => 10,
    'orderby'    => 'count',
    'order'      => 'DESC',
    'exclude'    => [ get_option( 'default_product_cat' ) ],
] );

if ( is_wp_error( $categories ) || empty( $categories ) ) {
    return;
}

// Fallback emoji icons when no category image set
$fallback_icons = [ '🐔', '🐐', '🐄', '🐟', '💊', '🌾', '🏠', '🥩', '🐦', '🦆' ];
?>

<section class="jt-section" aria-labelledby="jt-cat-heading">
    <div class="jt-container">
        <div class="jt-section-header">
            <h2 class="jt-section-title" id="jt-cat-heading">
                <?php esc_html_e( 'Kategori Produk', 'jendela-ternak' ); ?>
            </h2>
            <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="jt-section-link">
                <?php esc_html_e( 'Semua Kategori →', 'jendela-ternak' ); ?>
            </a>
        </div>

        <div class="jt-category-grid">
            <?php foreach ( $categories as $idx => $cat ) :
                $cat_link  = get_term_link( $cat );
                $cat_img   = '';
                $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
                if ( $thumbnail_id ) {
                    $cat_img = wp_get_attachment_image_url( $thumbnail_id, 'jt-category-icon' );
                }
                $icon = $fallback_icons[ $idx ] ?? '🐾';
            ?>
            <a href="<?php echo esc_url( $cat_link ); ?>" class="jt-category-item" title="<?php echo esc_attr( $cat->name ); ?>">
                <div class="jt-category-item__hex">
                    <?php if ( $cat_img ) : ?>
                        <img src="<?php echo esc_url( $cat_img ); ?>" alt="<?php echo esc_attr( $cat->name ); ?>" loading="lazy" width="44" height="44">
                    <?php else : ?>
                        <span style="font-size:22px;position:relative;z-index:1;"><?php echo $icon; ?></span>
                    <?php endif; ?>
                </div>
                <span class="jt-category-item__name text-clamp-2"><?php echo esc_html( $cat->name ); ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
