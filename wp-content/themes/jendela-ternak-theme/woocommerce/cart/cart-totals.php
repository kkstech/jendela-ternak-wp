<?php
/**
 * Branded cart totals.
 *
 * @package JendelaTernakMalang
 * @version 11.0.1
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cart_totals jt-cart-totals <?php echo WC()->customer->has_calculated_shipping() ? 'calculated_shipping' : ''; ?>">
    <?php do_action( 'woocommerce_before_cart_totals' ); ?>

    <h2><?php esc_html_e( 'Total Belanja', 'jendela-ternak' ); ?></h2>

    <table cellspacing="0" class="shop_table shop_table_responsive">
        <tr class="cart-subtotal">
            <th><?php esc_html_e( 'Subtotal', 'jendela-ternak' ); ?></th>
            <td data-title="<?php esc_attr_e( 'Subtotal', 'jendela-ternak' ); ?>"><?php wc_cart_totals_subtotal_html(); ?></td>
        </tr>

        <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
            <tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                <th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
                <td data-title="<?php echo esc_attr( wc_cart_totals_coupon_label( $coupon, false ) ); ?>"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
            <?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>
            <?php wc_cart_totals_shipping_html(); ?>
            <?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>
        <?php elseif ( WC()->cart->needs_shipping() && 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?>
            <tr class="shipping">
                <th><?php esc_html_e( 'Pengiriman', 'jendela-ternak' ); ?></th>
                <td data-title="<?php esc_attr_e( 'Pengiriman', 'jendela-ternak' ); ?>"><?php woocommerce_shipping_calculator(); ?></td>
            </tr>
        <?php endif; ?>

        <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
            <tr class="fee">
                <th><?php echo esc_html( $fee->name ); ?></th>
                <td data-title="<?php echo esc_attr( $fee->name ); ?>"><?php wc_cart_totals_fee_html( $fee ); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
            <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
                <tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                    <th><?php echo esc_html( $tax->label ); ?></th>
                    <td data-title="<?php echo esc_attr( $tax->label ); ?>"><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>
        <tr class="order-total">
            <th><?php esc_html_e( 'Total', 'jendela-ternak' ); ?></th>
            <td data-title="<?php esc_attr_e( 'Total', 'jendela-ternak' ); ?>"><?php wc_cart_totals_order_total_html(); ?></td>
        </tr>
        <?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>
    </table>

    <div class="wc-proceed-to-checkout"><?php do_action( 'woocommerce_proceed_to_checkout' ); ?></div>
    <?php do_action( 'woocommerce_after_cart_totals' ); ?>
</section>
