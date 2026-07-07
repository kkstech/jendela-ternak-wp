<?php
/**
 * Lost password confirmation text.
 *
 * Customized layout for lost password confirmation message.
 *
 * @package JendelaTernakMalang
 * @version 3.9.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="jt-lost-password-confirmation-wrapper font-sans max-w-md mx-auto my-12">
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-8 text-center">
        <!-- Paper Plane / Mail Check Icon -->
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-50 text-green-700 mb-6 text-2xl">
            <i class="fa-solid fa-paper-plane animate-pulse"></i>
        </div>
        
        <h2 class="text-xl font-extrabold text-green-900 mb-4"><?php esc_html_e( 'Email Reset Terkirim!', 'jendela-ternak' ); ?></h2>
        
        <?php do_action( 'woocommerce_before_lost_password_confirmation_message' ); ?>
        
        <p class="text-sm text-gray-600 leading-relaxed mb-8">
            <?php echo esc_html( apply_filters( 'woocommerce_lost_password_confirmation_message', esc_html__( 'Tautan untuk mereset kata sandi telah dikirim ke alamat email akun Anda. Proses ini mungkin memakan waktu beberapa menit. Silakan periksa kotak masuk atau folder spam Anda.', 'jendela-ternak' ) ) ); ?>
        </p>
        
        <div class="bg-yellow-50 text-yellow-800 rounded-lg p-3 text-xs font-semibold mb-6 border border-yellow-100">
            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
            <?php esc_html_e( 'Harap tunggu minimal 10 menit sebelum mencoba mengirim ulang permintaan reset password.', 'jendela-ternak' ); ?>
        </div>

        <?php do_action( 'woocommerce_after_lost_password_confirmation_message' ); ?>
        
        <div class="flex flex-col gap-3">
            <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="w-full jt-btn jt-btn--primary bg-green-700 hover:bg-green-800 text-white font-bold py-2.5 px-6 rounded-lg transition">
                <?php esc_html_e( 'Kembali Ke Login', 'jendela-ternak' ); ?>
            </a>
            
            <a href="<?php echo esc_url( home_url() ); ?>" class="w-full jt-btn jt-btn--outline py-2.5 text-xs text-gray-500 hover:text-green-800 transition">
                <?php esc_html_e( 'Kembali Ke Beranda', 'jendela-ternak' ); ?>
            </a>
        </div>
    </div>
</div>
