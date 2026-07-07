<?php
/**
 * Checkout shipping information form
 *
 * Customized layout for shipping fields and toggle.
 *
 * @package JendelaTernakMalang
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-shipping-fields font-sans mt-4">
    <?php if ( true === WC()->cart->needs_shipping_address() ) : ?>

        <h3 id="ship-to-different-address" class="m-0 mb-4">
            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox flex items-center gap-2 cursor-pointer text-xs font-bold text-gray-700">
                <input id="ship-to-different-address-checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox rounded text-green-700 focus:ring-green-600" <?php checked( apply_filters( 'woocommerce_ship_to_different_address_checked', 'shipping' === get_option( 'woocommerce_ship_to_destination' ) ? 1 : 0 ), 1 ); ?> type="checkbox" name="ship_to_different_address" value="1" /> 
                <span><?php esc_html_e( 'Kirim ke alamat yang berbeda?', 'jendela-ternak' ); ?></span>
            </label>
        </h3>

        <div class="shipping_address transition-all duration-300">

            <?php do_action( 'woocommerce_before_checkout_shipping_form', $checkout ); ?>

            <div class="woocommerce-shipping-fields__field-wrapper jt-shipping-fields-grid grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <?php
                $fields = $checkout->get_checkout_fields( 'shipping' );

                foreach ( $fields as $key => $field ) {
                    woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
                }
                ?>
            </div>

            <?php do_action( 'woocommerce_after_checkout_shipping_form', $checkout ); ?>

        </div>

    <?php endif; ?>
</div>

<!-- Additional fields / Notes wrapper -->
<div class="woocommerce-additional-fields font-sans mt-6">
    <?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>

    <!-- Note: order_comments is custom rendered directly in form-checkout.php to prevent AJAX focus issues.
         So we only render other non-standard custom order fields if they exist. -->
    <?php 
    $order_fields = $checkout->get_checkout_fields( 'order' );
    // Exclude standard order_comments from showing twice
    if ( isset( $order_fields['order_comments'] ) ) {
        unset( $order_fields['order_comments'] );
    }
    
    if ( ! empty( $order_fields ) ) : ?>
        <div class="woocommerce-additional-fields__field-wrapper grid grid-cols-1 gap-4">
            <?php foreach ( $order_fields as $key => $field ) : ?>
                <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
</div>
