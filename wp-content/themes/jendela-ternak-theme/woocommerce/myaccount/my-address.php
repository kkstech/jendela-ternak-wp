<?php
/**
 * woocommerce/myaccount/my-address.php
 * Override My Account addresses page to display multiple saved shipping addresses.
 *
 * @package JendelaTernakMalang
 * @version 9.3.0
 */

defined( 'ABSPATH' ) || exit;

$user_id = get_current_user_id();
$action  = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
$addr_id = isset( $_GET['id'] ) ? sanitize_text_field( $_GET['id'] ) : '';

$addresses = jt_get_user_shipping_addresses( $user_id );
$edit_address_url = wc_get_account_endpoint_url( 'edit-address' );

// ─────────────────────────────────────────────────────────────────
// VIEW 1: ADD OR EDIT ADDRESS FORM
// ─────────────────────────────────────────────────────────────────
if ( 'add' === $action || ( 'edit' === $action && ! empty( $addr_id ) ) ) {
    $current_address = null;

    if ( 'edit' === $action ) {
        foreach ( $addresses as $addr ) {
            if ( $addr['id'] === $addr_id ) {
                $current_address = $addr;
                break;
            }
        }
        if ( ! $current_address ) {
            echo '<div class="woocommerce-error">' . esc_html__( 'Alamat tidak ditemukan.', 'jendela-ternak' ) . '</div>';
            return;
        }
    }

    // Set default values for the form fields
    $fields_values = array(
        'billing_first_name' => $current_address ? $current_address['first_name'] : '',
        'billing_last_name'  => $current_address ? $current_address['last_name'] : '',
        'billing_phone'      => $current_address ? $current_address['phone'] : '',
        'billing_address_1'  => $current_address ? $current_address['address_1'] : '',
        'billing_address_2'  => $current_address ? $current_address['address_2'] : '',
        'billing_city'       => $current_address ? $current_address['city'] : '',
        'billing_state'      => $current_address ? $current_address['state'] : '',
        'postcode'           => $current_address ? $current_address['postcode'] : '',
        'default'            => $current_address ? $current_address['default'] : false,
    );

    // Get Indonesia default WooCommerce address fields
    $wc_fields = WC()->countries->get_address_fields( 'ID', 'billing_' );

    // Tailor fields list to our clean popup format
    unset( $wc_fields['billing_company'] );
    unset( $wc_fields['billing_country'] );

    // Adjust labels and classes for grid aesthetics
    $wc_fields['billing_first_name']['class'] = array( 'form-row-first' );
    $wc_fields['billing_last_name']['class']  = array( 'form-row-last' );
    $wc_fields['billing_phone']['required']   = true;
    $wc_fields['billing_phone']['class']      = array( 'form-row-wide' );
    $wc_fields['billing_address_1']['class']  = array( 'form-row-wide' );
    $wc_fields['billing_address_2']['class']  = array( 'form-row-wide' );
    $wc_fields['billing_city']['class']       = array( 'form-row-first' );
    $wc_fields['billing_state']['class']      = array( 'form-row-last' );
    $wc_fields['billing_postcode']['class']   = array( 'form-row-wide' );

    ?>
    <div class="jt-myaccount-address-form-wrapper">
        <div class="jt-myaccount-section-header">
            <h2 class="jt-myaccount-section-title">
                <?php echo 'edit' === $action ? esc_html__( 'Ubah Alamat Pengiriman', 'jendela-ternak' ) : esc_html__( 'Tambah Alamat Baru', 'jendela-ternak' ); ?>
            </h2>
            <a href="<?php echo esc_url( $edit_address_url ); ?>" class="jt-btn-back">
                <i class="fa-solid fa-arrow-left"></i> <?php esc_html_e( 'Kembali', 'jendela-ternak' ); ?>
            </a>
        </div>

        <form method="post" action="<?php echo esc_url( $edit_address_url ); ?>" class="jt-address-editor-form">
            <?php wp_nonce_field( 'jt_save_address', 'jt_address_nonce' ); ?>
            <input type="hidden" name="address_id" value="<?php echo esc_attr( $addr_id ); ?>" />
            <input type="hidden" name="billing_country" id="billing_country" value="ID" class="country_to_state" />

            <div class="woocommerce-address-fields">
                <div class="woocommerce-address-fields__field-wrapper">
                    <?php
                    foreach ( $wc_fields as $key => $field ) {
                        // Resolve field value
                        $value = '';
                        if ( isset( $fields_values[ $key ] ) ) {
                            $value = $fields_values[ $key ];
                        } elseif ( 'billing_postcode' === $key && isset( $fields_values['postcode'] ) ) {
                            $value = $fields_values['postcode'];
                        }
                        
                        woocommerce_form_field( $key, $field, $value );
                    }
                    ?>

                    <!-- Default Address Checkbox -->
                    <p class="form-row form-row-wide jt-checkbox-default-row" id="billing_default_field">
                        <label class="checkbox">
                            <input type="checkbox" name="billing_default" id="billing_default" value="1" <?php checked( $fields_values['default'], true ); ?> />
                            <span><?php esc_html_e( 'Jadikan Alamat Utama / Default', 'jendela-ternak' ); ?></span>
                        </label>
                    </p>
                </div>
            </div>

            <div class="jt-address-form-actions">
                <button type="submit" name="jt_save_address_submit" class="jt-btn jt-btn--primary">
                    <?php esc_html_e( 'Simpan Alamat', 'jendela-ternak' ); ?>
                </button>
                <a href="<?php echo esc_url( $edit_address_url ); ?>" class="jt-btn jt-btn--outline">
                    <?php esc_html_e( 'Batal', 'jendela-ternak' ); ?>
                </a>
            </div>
        </form>
    </div>
    <?php

// ─────────────────────────────────────────────────────────────────
// VIEW 2: SAVED ADDRESSES LIST
// ─────────────────────────────────────────────────────────────────
} else {
    ?>
    <div class="jt-myaccount-addresses-wrapper">
        <div class="jt-myaccount-section-header">
            <h2 class="jt-myaccount-section-title"><?php esc_html_e( 'Alamat Saya', 'jendela-ternak' ); ?></h2>
            <a href="<?php echo esc_url( add_query_arg( 'action', 'add', $edit_address_url ) ); ?>" class="jt-btn jt-btn--primary jt-btn--sm">
                <i class="fa-solid fa-plus"></i> <?php esc_html_e( 'Tambah Alamat Baru', 'jendela-ternak' ); ?>
            </a>
        </div>

        <?php if ( empty( $addresses ) ) : ?>
            <div class="jt-no-addresses-container">
                <div class="jt-no-addresses-icon">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <p class="jt-no-addresses-text"><?php esc_html_e( 'Anda belum menyimpan alamat pengiriman.', 'jendela-ternak' ); ?></p>
                <a href="<?php echo esc_url( add_query_arg( 'action', 'add', $edit_address_url ) ); ?>" class="jt-btn jt-btn--primary mt-4">
                    <?php esc_html_e( 'Tambah Alamat Pertama Anda', 'jendela-ternak' ); ?>
                </a>
            </div>
        <?php else : ?>
            <div class="jt-addresses-list">
                <?php foreach ( $addresses as $addr ) :
                    $full_name = trim( $addr['first_name'] . ' ' . $addr['last_name'] );
                    
                    // Format state text if possible
                    $state_code = $addr['state'];
                    $states = WC()->countries->get_states( 'ID' );
                    $state_name = isset( $states[ $state_code ] ) ? $states[ $state_code ] : $state_code;
                    
                    $full_address = trim( $addr['address_1'] . ( $addr['address_2'] ? ', ' . $addr['address_2'] : '' ) . ', ' . $addr['city'] . ', ' . $state_name . ' ' . $addr['postcode'] );
                    
                    // Build action links with nonces
                    $set_default_url = wp_nonce_url(
                        add_query_arg( array( 'action' => 'set_default', 'address_id' => $addr['id'] ), $edit_address_url ),
                        'jt_set_default_address_' . $addr['id']
                    );
                    $delete_url = wp_nonce_url(
                        add_query_arg( array( 'action' => 'delete', 'address_id' => $addr['id'] ), $edit_address_url ),
                        'jt_delete_address_' . $addr['id']
                    );
                    $edit_url = add_query_arg( array( 'action' => 'edit', 'id' => $addr['id'] ), $edit_address_url );
                ?>
                    <div class="jt-address-card <?php echo $addr['default'] ? 'jt-address-card--default' : ''; ?>">
                        <div class="jt-address-card__content">
                            <div class="jt-address-card__meta">
                                <span class="jt-address-card__name"><?php echo esc_html( $full_name ); ?></span>
                                <span class="jt-address-card__divider">|</span>
                                <span class="jt-address-card__phone"><?php echo esc_html( $addr['phone'] ); ?></span>
                            </div>
                            
                            <div class="jt-address-card__details">
                                <p class="jt-address-card__text"><?php echo esc_html( $full_address ); ?></p>
                            </div>
                            
                            <?php if ( $addr['default'] ) : ?>
                                <span class="jt-address-card__badge-default"><?php esc_html_e( 'Utama', 'jendela-ternak' ); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="jt-address-card__actions">
                            <div class="jt-address-card__crud">
                                <a href="<?php echo esc_url( $edit_url ); ?>" class="jt-address-action-link" title="<?php esc_attr_e( 'Ubah', 'jendela-ternak' ); ?>">
                                    <?php esc_html_e( 'Ubah', 'jendela-ternak' ); ?>
                                </a>
                                <?php if ( ! $addr['default'] ) : ?>
                                    <a href="<?php echo esc_url( $delete_url ); ?>" class="jt-address-action-link jt-address-action-link--danger" onclick="return confirm('<?php esc_attr_e( 'Apakah Anda yakin ingin menghapus alamat ini?', 'jendela-ternak' ); ?>');" title="<?php esc_attr_e( 'Hapus', 'jendela-ternak' ); ?>">
                                        <?php esc_html_e( 'Hapus', 'jendela-ternak' ); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            
                            <div class="jt-address-card__set-default">
                                <?php if ( ! $addr['default'] ) : ?>
                                    <a href="<?php echo esc_url( $set_default_url ); ?>" class="jt-btn jt-btn--outline jt-btn--xs">
                                        <?php esc_html_e( 'Atur Sebagai Utama', 'jendela-ternak' ); ?>
                                    </a>
                                <?php else : ?>
                                    <button class="jt-btn jt-btn--primary jt-btn--xs" disabled>
                                        <?php esc_html_e( 'Alamat Utama', 'jendela-ternak' ); ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}
