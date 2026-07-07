<?php
/**
 * Edit address form
 *
 * Customized layout for editing billing/shipping addresses.
 *
 * @package JendelaTernakMalang
 * @version 9.3.0
 */

defined( 'ABSPATH' ) || exit;

$page_title = ( 'billing' === $load_address ) ? esc_html__( 'Alamat Penagihan', 'jendela-ternak' ) : esc_html__( 'Alamat Pengiriman', 'jendela-ternak' );

do_action( 'woocommerce_before_edit_account_address_form' ); ?>

<?php if ( ! $load_address ) : ?>
    <?php wc_get_template( 'myaccount/my-address.php' ); ?>
<?php else : ?>

    <div class="jt-edit-address-wrapper font-sans pb-10">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <div class="flex items-center gap-3 mb-6">
                <!-- Back to Address Dashboard -->
                <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>" class="text-gray-500 hover:text-green-800 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <h2 class="text-lg font-bold text-green-900 m-0">
                    <?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $page_title, $load_address ); ?>
                </h2>
            </div>

            <form method="post" novalidate class="space-y-6">

                <div class="woocommerce-address-fields">
                    <?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>

                    <!-- Styled Field Wrapper -->
                    <div class="woocommerce-address-fields__field-wrapper jt-address-fields-grid">
                        <?php
                        foreach ( $address as $key => $field ) {
                            woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
                        }
                        ?>
                    </div>

                    <?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>

                    <!-- Action buttons -->
                    <div class="pt-6 border-t border-gray-100 flex justify-end mt-8">
                        <?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
                        <button type="submit" class="jt-btn jt-btn--primary bg-green-700 hover:bg-green-800 text-white font-bold py-2.5 px-6 rounded-lg transition" name="save_address" value="<?php esc_attr_e( 'Save address', 'woocommerce' ); ?>">
                            <i class="fa-solid fa-floppy-disk mr-2"></i><?php esc_html_e( 'Simpan Alamat', 'jendela-ternak' ); ?>
                        </button>
                        <input type="hidden" name="action" value="edit_address" />
                    </div>
                </div>

            </form>
        </div>
    </div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>
