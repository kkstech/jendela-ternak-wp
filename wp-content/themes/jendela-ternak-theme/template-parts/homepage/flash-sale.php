<?php
/**
 * template-parts/homepage/flash-sale.php
 * Flash sale section with countdown timer + horizontal scrolling product grid.
 *
 * @package JendelaTernakMalang
 */
defined( 'ABSPATH' ) || exit;

// Respect the Flash Sale toggle from admin settings
if ( jt_get_setting( 'toggle_flash_sale', '1' ) !== '1' ) {
    return;
}

$end_time = jt_get_setting( 'flash_sale_end', '' );

// Get on-sale products
$sale_args = [
    'post_type'      => 'product',
    'posts_per_page' => 10,
    'post_status'    => 'publish',
    'meta_query'     => [
        [
            'key'     => '_sale_price',
            'value'   => '',
            'compare' => '!=',
        ],
    ],
];

$sale_query = new WP_Query( $sale_args );

if ( ! $sale_query->have_posts() ) {
    wp_reset_postdata();
    return;
}
?>

<section class="jt-flash-sale" aria-labelledby="jt-flash-heading">
    <div class="jt-flash-sale__header">
        <div class="jt-flash-sale__label">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <span id="jt-flash-heading"><?php esc_html_e( 'FLASH SALE', 'jendela-ternak' ); ?></span>
        </div>

        <?php if ( $end_time ) : ?>
        <div class="jt-countdown" data-end-time="<?php echo esc_attr( $end_time ); ?>" role="timer" aria-label="<?php esc_attr_e( 'Waktu tersisa', 'jendela-ternak' ); ?>">
            <div class="jt-countdown__unit" data-jt-hours>00</div>
            <span class="jt-countdown__sep">:</span>
            <div class="jt-countdown__unit" data-jt-minutes>00</div>
            <span class="jt-countdown__sep">:</span>
            <div class="jt-countdown__unit" data-jt-seconds>00</div>
        </div>
        <?php endif; ?>

        <a href="<?php echo esc_url( add_query_arg( 'orderby', 'date', wc_get_page_permalink( 'shop' ) ) ); ?>" class="jt-btn jt-btn--accent" style="font-size:12px;padding:6px 14px;">
            <?php esc_html_e( 'Lihat Semua', 'jendela-ternak' ); ?>
        </a>
    </div>

    <div class="jt-flash-sale__products">
        <?php while ( $sale_query->have_posts() ) : $sale_query->the_post();
            global $product;
            $product = wc_get_product( get_the_ID() );
            if ( $product && $product->is_visible() ) :
                $id           = $product->get_id();
                $link         = get_permalink();
                $name         = get_the_title();
                $img_id       = $product->get_image_id();
                $img_url      = $img_id ? wp_get_attachment_image_url( $img_id, 'jt-product-card' ) : wc_placeholder_img_src();
                $regular      = $product->get_regular_price();
                $sale         = $product->get_sale_price();
                $discount_pct = ( $regular > 0 ) ? round( ( ( $regular - $sale ) / $regular ) * 100 ) : 0;
                $total_stock  = $product->get_stock_quantity();
                $total_sold   = $product->get_total_sales();
                // Stock percentage
                $stock_pct = 100;
                if ( $total_stock && $total_sold ) {
                    $total = $total_stock + $total_sold;
                    $stock_pct = round( ( $total_stock / $total ) * 100 );
                }
            ?>
            <article class="jt-product-card" style="width:160px;">
                <a href="<?php echo esc_url( $link ); ?>" class="jt-product-card__image">
                    <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy" width="160" height="160">
                    <div class="jt-product-card__badges">
                        <?php if ( $discount_pct > 0 ) : ?>
                            <span class="jt-badge jt-badge--discount"><?php echo esc_html( $discount_pct ); ?>%</span>
                        <?php endif; ?>
                        <span class="jt-badge jt-badge--flash">⚡ Flash</span>
                    </div>
                </a>
                <div class="jt-product-card__info">
                    <a href="<?php echo esc_url( $link ); ?>" class="jt-product-card__name"><?php echo esc_html( $name ); ?></a>
                    <div class="jt-product-card__price">
                        <span class="jt-product-card__sale-price"><?php echo wp_kses_post( wc_price( $sale ) ); ?></span>
                        <span class="jt-product-card__regular-price"><?php echo wp_kses_post( wc_price( $regular ) ); ?></span>
                    </div>
                    <!-- Stock bar -->
                    <div class="jt-product-card__stock-bar" title="<?php echo esc_attr( sprintf( __( 'Sisa stok: %d%%', 'jendela-ternak' ), $stock_pct ) ); ?>">
                        <div class="jt-product-card__stock-fill" style="width:<?php echo esc_attr( $stock_pct ); ?>%;"></div>
                    </div>
                    <div style="font-size:10px;color:rgba(255,255,255,0.7);margin-top:4px;"><?php esc_html_e( 'Stok tersisa', 'jendela-ternak' ); ?> <?php echo esc_html( $stock_pct ); ?>%</div>
                </div>
            </article>
            <?php endif;
        endwhile;
        wp_reset_postdata();
        ?>
    </div>
</section>
