<?php
/**
 * Branded add payment method form.
 *
 * @package JendelaTernakMalang
 * @version 10.9.0
 */

defined( 'ABSPATH' ) || exit;

$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
?>
<section class="jt-payment-methods jt-add-payment-method">
    <div class="jt-payment-methods__header">
        <div>
            <h2><?php esc_html_e( 'Tambah Metode Pembayaran', 'jendela-ternak' ); ?></h2>
            <p><?php esc_html_e( 'Pilih metode yang ingin disimpan untuk transaksi berikutnya.', 'jendela-ternak' ); ?></p>
        </div>
    </div>

    <?php if ( $available_gateways ) : ?>
        <form id="add_payment_method" method="post" class="jt-checkout-payment-card">
            <div id="payment">
                <ul class="woocommerce-PaymentMethods payment_methods methods" aria-label="<?php esc_attr_e( 'Metode pembayaran', 'jendela-ternak' ); ?>">
                    <?php current( $available_gateways )->set_current(); ?>
                    <?php foreach ( $available_gateways as $gateway ) : ?>
                        <?php wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) ); ?>
                    <?php endforeach; ?>
                </ul>
                <?php do_action( 'woocommerce_add_payment_method_form_bottom' ); ?>
                <div class="form-row place-order">
                    <?php wp_nonce_field( 'woocommerce-add-payment-method', 'woocommerce-add-payment-method-nonce' ); ?>
                    <button type="submit" class="woocommerce-Button woocommerce-Button--alt button alt" id="place_order" value="<?php esc_attr_e( 'Tambah metode pembayaran', 'jendela-ternak' ); ?>"><?php esc_html_e( 'Tambah Metode Pembayaran', 'jendela-ternak' ); ?></button>
                    <input type="hidden" name="woocommerce_add_payment_method" value="1" />
                </div>
            </div>
        </form>
    <?php else : ?>
        <div class="jt-payment-methods__empty"><p><?php esc_html_e( 'Metode pembayaran baru saat ini hanya dapat ditambahkan saat checkout.', 'jendela-ternak' ); ?></p></div>
    <?php endif; ?>
</section>
