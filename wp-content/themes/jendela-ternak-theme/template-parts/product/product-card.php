<?php
/**
 * template-parts/product/product-card.php
 * Reusable product card — used in shop loop and homepage sections.
 * Expects global $product or $post to be set.
 *
 * @package JendelaTernakMalang
 */
defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product ) {
    $product = wc_get_product( get_the_ID() );
}
if ( ! $product || ! $product->is_visible() ) {
    return;
}

$id           = $product->get_id();
$link         = get_permalink( $id );
$name         = get_the_title( $id );
$img_id       = $product->get_image_id();
$img_url      = $img_id ? wp_get_attachment_image_url( $img_id, 'jt-product-card' ) : wc_placeholder_img_src( 'jt-product-card' );
$img_alt      = $img_id ? get_post_meta( $img_id, '_wp_attachment_image_alt', true ) : $name;
$is_on_sale   = $product->is_on_sale();
$regular_price = $product->get_regular_price();
$sale_price   = $product->get_sale_price();
$avg_rating   = $product->get_average_rating();
$rating_count = $product->get_rating_count();
$total_sold   = $product->get_total_sales();
$city         = get_post_meta( $id, '_product_city', true ) ?: 'Malang';

// Discount percentage
$discount_pct = 0;
if ( $is_on_sale && $regular_price > 0 ) {
    $discount_pct = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
}

// Star rating helper is declared globally in inc/theme-setup.php
?>

<article id="product-<?php echo esc_attr( $id ); ?>" class="jt-product-card" itemscope itemtype="https://schema.org/Product">

    <a href="<?php echo esc_url( $link ); ?>" class="jt-product-card__image" aria-label="<?php echo esc_attr( $name ); ?>">
        <img
            src="<?php echo esc_url( $img_url ); ?>"
            alt="<?php echo esc_attr( $img_alt ?: $name ); ?>"
            loading="lazy"
            width="400"
            height="400"
            itemprop="image"
        >

        <!-- Badges -->
        <div class="jt-product-card__badges">
            <?php if ( $is_on_sale && $discount_pct > 0 ) : ?>
                <span class="jt-badge jt-badge--discount">Diskon <?php echo esc_html( $discount_pct ); ?>%</span>
            <?php endif; ?>
        </div>
    </a>

    <div class="jt-product-card__info">

        <a href="<?php echo esc_url( $link ); ?>" class="jt-product-card__name" itemprop="name">
            <?php echo esc_html( $name ); ?>
        </a>

        <!-- Price -->
        <div class="jt-product-card__price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
            <?php if ( $is_on_sale ) : ?>
                <span class="jt-product-card__sale-price" itemprop="price" content="<?php echo esc_attr( $sale_price ); ?>">
                    <?php echo wp_kses_post( wc_price( $sale_price ) ); ?>
                </span>
                <span class="jt-product-card__regular-price">
                    <?php echo wp_kses_post( wc_price( $regular_price ) ); ?>
                </span>
                <meta itemprop="priceCurrency" content="<?php echo esc_attr( get_woocommerce_currency() ); ?>">
            <?php else : ?>
                <span class="jt-product-card__sale-price" itemprop="price" content="<?php echo esc_attr( $product->get_price() ); ?>">
                    <?php echo wp_kses_post( wc_price( $product->get_price() ) ); ?>
                </span>
                <meta itemprop="priceCurrency" content="<?php echo esc_attr( get_woocommerce_currency() ); ?>">
            <?php endif; ?>
        </div>

        <!-- Rating -->
        <?php if ( $rating_count > 0 ) : ?>
        <div class="jt-product-card__rating">
            <span class="jt-stars" aria-hidden="true"><?php echo jt_render_stars( (float) $avg_rating ); ?></span>
            <span class="jt-rating-count">(<?php echo esc_html( $rating_count ); ?>)</span>
        </div>
        <?php endif; ?>

        <!-- Meta: sold + city -->
        <div class="jt-product-card__meta">
            <?php if ( $total_sold > 0 ) : ?>
                <span><?php echo esc_html( number_format( $total_sold ) ); ?> terjual</span>
                <span>·</span>
            <?php endif; ?>
            <span><?php echo esc_html( $city ); ?></span>
        </div>

    </div><!-- .jt-product-card__info -->

</article><!-- .jt-product-card -->
