<?php
/**
 * Edit account form
 *
 * Customized modern layout for editing profile details and changing password.
 *
 * @package JendelaTernakMalang
 * @version 10.5.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' );
?>

<div class="jt-edit-account-wrapper font-sans pb-10">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
        <h2 class="text-lg font-bold text-green-900 mb-6 flex items-center gap-2">
            <i class="fa-solid fa-user-pen"></i>
            <?php esc_html_e( 'Ubah Detail Akun Saya', 'jendela-ternak' ); ?>
        </h2>

        <form class="woocommerce-EditAccountForm edit-account space-y-6" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

            <?php do_action( 'woocommerce_edit_account_form_start' ); ?>

            <!-- First Name and Last Name Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-row">
                    <label class="block text-xs font-bold text-gray-400 mb-1.5 uppercase tracking-wider" for="account_first_name">
                        <?php esc_html_e( 'Nama Depan', 'jendela-ternak' ); ?>&nbsp;<span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-600 font-semibold text-sm transition" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" aria-required="true" />
                </div>

                <div class="form-row">
                    <label class="block text-xs font-bold text-gray-400 mb-1.5 uppercase tracking-wider" for="account_last_name">
                        <?php esc_html_e( 'Nama Belakang', 'jendela-ternak' ); ?>&nbsp;<span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-600 font-semibold text-sm transition" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" aria-required="true" />
                </div>
            </div>

            <!-- Display Name -->
            <div class="form-row">
                <label class="block text-xs font-bold text-gray-400 mb-1.5 uppercase tracking-wider" for="account_display_name">
                    <?php esc_html_e( 'Nama Tampilan', 'jendela-ternak' ); ?>&nbsp;<span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-600 font-semibold text-sm transition" name="account_display_name" id="account_display_name" aria-describedby="account_display_name_description" value="<?php echo esc_attr( $user->display_name ); ?>" aria-required="true" />
                <span id="account_display_name_description" class="block text-xs text-gray-400 mt-1.5 italic">
                    <?php esc_html_e( 'Nama ini akan ditampilkan di bagian akun Anda dan ulasan produk.', 'jendela-ternak' ); ?>
                </span>
            </div>

            <!-- Email Address -->
            <div class="form-row">
                <label class="block text-xs font-bold text-gray-400 mb-1.5 uppercase tracking-wider" for="account_email">
                    <?php esc_html_e( 'Alamat Email', 'jendela-ternak' ); ?>&nbsp;<span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="email" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-600 font-semibold text-sm transition" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" aria-required="true" />
            </div>

            <?php do_action( 'woocommerce_edit_account_form_fields' ); ?>

            <!-- Change Password Section -->
            <fieldset class="border border-gray-150 rounded-xl p-5 md:p-6 bg-gray-50 mt-8 space-y-4">
                <legend class="px-3 text-xs font-bold text-gray-500 bg-white border border-gray-150 rounded-full uppercase tracking-wider">
                    <i class="fa-solid fa-lock mr-1.5 text-green-700"></i><?php esc_html_e( 'Ubah Password (Opsional)', 'jendela-ternak' ); ?>
                </legend>

                <div class="form-row">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5" for="password_current">
                        <?php esc_html_e( 'Password Saat Ini (biarkan kosong jika tidak ingin mengubah)', 'jendela-ternak' ); ?>
                    </label>
                    <input type="password" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-600 font-semibold text-sm bg-white transition" name="password_current" id="password_current" autocomplete="current-password" />
                </div>

                <div class="form-row">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5" for="password_1">
                        <?php esc_html_e( 'Password Baru (biarkan kosong jika tidak ingin mengubah)', 'jendela-ternak' ); ?>
                    </label>
                    <input type="password" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-600 font-semibold text-sm bg-white transition" name="password_1" id="password_1" autocomplete="new-password" />
                </div>

                <div class="form-row">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5" for="password_2">
                        <?php esc_html_e( 'Konfirmasi Password Baru', 'jendela-ternak' ); ?>
                    </label>
                    <input type="password" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-600 font-semibold text-sm bg-white transition" name="password_2" id="password_2" autocomplete="new-password" />
                </div>
            </fieldset>

            <?php do_action( 'woocommerce_edit_account_form' ); ?>

            <!-- Action buttons -->
            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
                <button type="submit" class="jt-btn jt-btn--primary bg-green-700 hover:bg-green-800 text-white font-bold py-2.5 px-6 rounded-lg transition" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>">
                    <i class="fa-solid fa-floppy-disk mr-2"></i><?php esc_html_e( 'Simpan Perubahan', 'jendela-ternak' ); ?>
                </button>
                <input type="hidden" name="action" value="save_account_details" />
            </div>

            <?php do_action( 'woocommerce_edit_account_form_end' ); ?>
        </form>
    </div>
</div>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
