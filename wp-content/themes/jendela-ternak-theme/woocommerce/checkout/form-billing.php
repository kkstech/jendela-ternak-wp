<?php
/**
 * Checkout billing information form
 *
 * Customized layout for checkout billing details and account registration fields.
 *
 * @package JendelaTernakMalang
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-billing-fields font-sans">
    
    <!-- Title is managed by form-checkout.php address card header, but keep native hook wrapper -->
    <div class="hidden">
        <?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>
            <h3><?php esc_html_e( 'Billing &amp; Shipping', 'woocommerce' ); ?></h3>
        <?php else : ?>
            <h3><?php esc_html_e( 'Billing details', 'woocommerce' ); ?></h3>
        <?php endif; ?>
    </div>

    <?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

    <!-- Grid Wrapper for Billing Fields -->
    <div class="woocommerce-billing-fields__field-wrapper jt-billing-fields-grid grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php
        $fields = $checkout->get_checkout_fields( 'billing' );

        foreach ( $fields as $key => $field ) {
            woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
        }
        ?>
    </div>

    <?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
</div>

<!-- Guest Registration Form -->
<?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
    <div class="woocommerce-account-fields font-sans bg-gray-50 border border-gray-150 rounded-xl p-5 mt-6 space-y-4">
        <?php if ( ! $checkout->is_registration_required() ) : ?>

            <p class="form-row form-row-wide create-account m-0">
                <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox flex items-center gap-2 cursor-pointer text-xs font-bold text-gray-700">
                    <input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox rounded text-green-700 focus:ring-green-600" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?> type="checkbox" name="createaccount" value="1" /> 
                    <span><?php esc_html_e( 'Buat akun baru?', 'jendela-ternak' ); ?></span>
                </label>
            </p>

        <?php endif; ?>

        <?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

        <?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>

            <div class="create-account grid grid-cols-1 gap-4 pt-2">
                <p class="text-xs text-gray-400 leading-normal m-0 mb-1">
                    <?php esc_html_e( 'Buat akun dengan memasukkan password di bawah ini. Jika Anda memilih tidak membuat akun, transaksi akan diproses sebagai Guest/Tamu.', 'jendela-ternak' ); ?>
                </p>
                <?php foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) : ?>
                    <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
                <?php endforeach; ?>
                <div class="clear"></div>
            </div>

        <?php endif; ?>

        <?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
    </div>
<?php endif; ?>
