<?php
/**
 * Checkout login form
 *
 * Customized layout for returning customer login banner at checkout.
 *
 * @package JendelaTernakMalang
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

$registration_at_checkout   = WC_Checkout::instance()->is_registration_enabled();
$login_reminder_at_checkout = 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder' );

if ( is_user_logged_in() ) {
    return;
}
?>

<div class="jt-checkout-login-wrapper font-sans mb-4">
    <?php if ( $login_reminder_at_checkout ) : ?>
        <!-- Custom styled banner -->
        <div class="bg-green-50 border border-green-100 rounded-lg p-3 text-xs flex items-center justify-between gap-3 text-green-900">
            <div class="flex items-center gap-2 font-semibold">
                <span class="text-sm text-green-700"><i class="fa-solid fa-circle-user"></i></span>
                <span><?php esc_html_e( 'Sudah punya akun Jendela Ternak?', 'jendela-ternak' ); ?></span>
            </div>
            <a href="#" class="showlogin text-green-800 hover:underline font-extrabold" role="button">
                <?php esc_html_e( 'Masuk Di Sini', 'jendela-ternak' ); ?>
            </a>
        </div>
    <?php endif; ?>

    <?php
    if ( $registration_at_checkout || $login_reminder_at_checkout ) :
        // Always show the form after a login attempt.
        $show_form = isset( $_POST['login'] );

        // Wrap the standard login form in a custom container for styling
        echo '<div class="jt-checkout-login-form-container mt-3" style="' . ( $show_form ? '' : 'display:none;' ) . '">';
        woocommerce_login_form(
            array(
                'message'  => esc_html__( 'Jika Anda sudah pernah berbelanja dengan kami sebelumnya, silakan masukkan detail login Anda di bawah ini. Jika Anda pelanggan baru, silakan lanjutkan langsung ke bagian Alamat Pengiriman.', 'jendela-ternak' ),
                'redirect' => wc_get_checkout_url(),
                'hidden'   => false, // Handled by outer container toggle
            )
        );
        echo '</div>';
    endif;
    ?>
</div>
