<?php
/**
 * Checkout Payment Section
 *
 * Customized modern payment options container with clean spacing and styled buttons.
 *
 * @package JendelaTernakMalang
 * @version 9.8.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! wp_doing_ajax() ) {
    do_action( 'woocommerce_review_order_before_payment' );
}
?>
<div id="payment" class="woocommerce-checkout-payment font-sans">
    <?php if ( WC()->cart && WC()->cart->needs_payment() ) : ?>
        <ul class="wc_payment_methods payment_methods methods list-none p-0 m-0 space-y-3">
            <?php
            if ( ! empty( $available_gateways ) ) {
                foreach ( $available_gateways as $gateway ) {
                    wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
                }
            } else {
                echo '<li class="p-4 bg-yellow-50 text-yellow-800 border border-yellow-100 rounded-lg text-xs font-semibold">';
                wc_print_notice( apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__( 'Maaf, tampaknya tidak ada metode pembayaran yang tersedia. Hubungi kami jika Anda memerlukan bantuan.', 'jendela-ternak' ) : esc_html__( 'Silakan isi detail alamat Anda di atas untuk melihat metode pembayaran yang tersedia.', 'jendela-ternak' ) ), 'notice' );
                echo '</li>';
            }
            ?>
        </ul>
    <?php endif; ?>
    
    <div class="form-row place-order mt-6 pt-4 border-t border-gray-150">
        <noscript>
            <?php
            /* translators: $1 and $2 opening and closing emphasis tags respectively */
            printf( esc_html__( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ), '<em>', '</em>' );
            ?>
            <br/><button type="submit" class="button alt<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>"><?php esc_html_e( 'Update totals', 'woocommerce' ); ?></button>
        </noscript>

        <!-- Terms and conditions -->
        <div class="jt-checkout-terms-container text-xs text-gray-500 mb-4 leading-relaxed">
            <?php wc_get_template( 'checkout/terms.php' ); ?>
        </div>

        <?php do_action( 'woocommerce_review_order_before_submit' ); ?>

        <!-- Native checkout button styled for Desktop (Hidden on mobile where sticky nav button is used) -->
        <div class="jt-desktop-place-order-wrapper">
            <?php 
            $order_button_html = '<button type="submit" class="w-full py-3 px-6 rounded-lg bg-green-700 hover:bg-green-800 text-white font-extrabold text-sm transition shadow-sm" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '"><i class="fa-solid fa-shield-check mr-2"></i>' . esc_html( $order_button_text ) . '</button>';
            echo apply_filters( 'woocommerce_order_button_html', $order_button_html ); 
            ?>
        </div>

        <?php do_action( 'woocommerce_review_order_after_submit' ); ?>

        <?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
    </div>
</div>
<?php
if ( ! wp_doing_ajax() ) {
    do_action( 'woocommerce_review_order_after_payment' );
}
