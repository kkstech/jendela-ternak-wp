<?php
/**
 * Lost password form
 *
 * Customized layout for lost password page.
 *
 * @package JendelaTernakMalang
 * @version 9.2.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_form' );
?>

<div class="jt-lost-password-wrapper font-sans max-w-md mx-auto my-12">
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-8 text-center">
        <!-- Key Icon Header -->
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-50 text-green-700 mb-6 text-2xl">
            <i class="fa-solid fa-key"></i>
        </div>
        
        <h2 class="text-xl font-extrabold text-green-900 mb-3"><?php esc_html_e( 'Lupa Password?', 'jendela-ternak' ); ?></h2>
        
        <p class="text-xs text-gray-500 leading-relaxed mb-6">
            <?php echo apply_filters( 'woocommerce_lost_password_message', esc_html__( 'Silakan masukkan username atau alamat email Anda. Anda akan menerima tautan untuk membuat password baru melalui email.', 'jendela-ternak' ) ); ?>
        </p>

        <form method="post" class="woocommerce-ResetPassword lost_reset_password text-left space-y-5">

            <div class="form-row">
                <label class="block text-xs font-bold text-gray-400 mb-1.5 uppercase tracking-wider" for="user_login">
                    <?php esc_html_e( 'Username atau Alamat Email', 'jendela-ternak' ); ?>&nbsp;<span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-600 font-semibold text-sm transition" type="text" name="user_login" id="user_login" autocomplete="username" required aria-required="true" />
            </div>

            <?php do_action( 'woocommerce_lostpassword_form' ); ?>

            <div class="pt-2">
                <input type="hidden" name="wc_reset_password" value="true" />
                <button type="submit" class="w-full jt-btn jt-btn--primary bg-green-700 hover:bg-green-800 text-white font-bold py-2.5 px-6 rounded-lg transition" value="<?php esc_attr_e( 'Reset password', 'woocommerce' ); ?>">
                    <?php esc_html_e( 'Kirim Tautan Reset', 'jendela-ternak' ); ?>
                </button>
            </div>

            <?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

        </form>
    </div>
</div>

<?php
do_action( 'woocommerce_after_lost_password_form' );
