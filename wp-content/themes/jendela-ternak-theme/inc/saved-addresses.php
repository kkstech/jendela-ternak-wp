<?php
/**
 * inc/saved-addresses.php
 * Logic for managing multiple user shipping addresses (CRUD)
 * and integrating them with WooCommerce checkout.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get all saved shipping addresses for a user
 *
 * @param int $user_id User ID.
 * @return array
 */
function jt_get_user_shipping_addresses( $user_id ) {
    if ( ! $user_id ) {
        return array();
    }
    $addresses = get_user_meta( $user_id, '_jt_shipping_addresses', true );
    return is_array( $addresses ) ? $addresses : array();
}

/**
 * Save or update a shipping address for a user
 *
 * @param int   $user_id      User ID.
 * @param array $address_data Address data.
 * @return string Address ID.
 */
function jt_save_user_shipping_address( $user_id, $address_data ) {
    if ( ! $user_id ) {
        return '';
    }

    $addresses = jt_get_user_shipping_addresses( $user_id );

    // Clean and validate address data
    $new_address = array(
        'first_name' => isset( $address_data['first_name'] ) ? sanitize_text_field( $address_data['first_name'] ) : '',
        'last_name'  => isset( $address_data['last_name'] ) ? sanitize_text_field( $address_data['last_name'] ) : '',
        'phone'      => isset( $address_data['phone'] ) ? sanitize_text_field( $address_data['phone'] ) : '',
        'address_1'  => isset( $address_data['address_1'] ) ? sanitize_text_field( $address_data['address_1'] ) : '',
        'address_2'  => isset( $address_data['address_2'] ) ? sanitize_text_field( $address_data['address_2'] ) : '',
        'city'       => isset( $address_data['city'] ) ? sanitize_text_field( $address_data['city'] ) : '',
        'state'      => isset( $address_data['state'] ) ? sanitize_text_field( $address_data['state'] ) : '',
        'postcode'   => isset( $address_data['postcode'] ) ? sanitize_text_field( $address_data['postcode'] ) : '',
        'country'    => 'ID', // Force default to Indonesia
    );

    $id = isset( $address_data['id'] ) ? sanitize_text_field( $address_data['id'] ) : '';
    $is_default = ! empty( $address_data['default'] );

    if ( empty( $addresses ) ) {
        // If it's the first address, it must be the default
        $is_default = true;
    }

    if ( $is_default ) {
        // Unset default status from all existing addresses
        foreach ( $addresses as &$addr ) {
            $addr['default'] = false;
        }
        $new_address['default'] = true;
    } else {
        $new_address['default'] = false;
    }

    if ( ! empty( $id ) ) {
        // Edit existing address
        $found = false;
        foreach ( $addresses as $key => $addr ) {
            if ( $addr['id'] === $id ) {
                $new_address['id'] = $id;
                // If we edited the default address and it is no longer default, but it was default,
                // keep it default unless another default was set.
                if ( $addr['default'] && ! $is_default ) {
                    $new_address['default'] = true;
                }
                $addresses[ $key ] = $new_address;
                $found = true;
                break;
            }
        }
        if ( ! $found ) {
            $new_address['id'] = 'addr_' . uniqid();
            $addresses[] = $new_address;
        }
    } else {
        // Add new address
        $new_address['id'] = 'addr_' . uniqid();
        $addresses[] = $new_address;
    }

    // Double check that at least one default address exists
    $has_default = false;
    foreach ( $addresses as $addr ) {
        if ( ! empty( $addr['default'] ) ) {
            $has_default = true;
            break;
        }
    }
    if ( ! $has_default && ! empty( $addresses ) ) {
        $addresses[0]['default'] = true;
    }

    update_user_meta( $user_id, '_jt_shipping_addresses', $addresses );

    // Also sync standard WooCommerce billing/shipping keys if this is default
    if ( $new_address['default'] ) {
        jt_sync_user_primary_address( $user_id, $new_address );
    }

    return $new_address['id'];
}

/**
 * Sync the default address with the user's primary WooCommerce billing & shipping meta
 */
function jt_sync_user_primary_address( $user_id, $address ) {
    $keys = array(
        'first_name',
        'last_name',
        'phone',
        'address_1',
        'address_2',
        'city',
        'state',
        'postcode',
        'country',
    );

    foreach ( $keys as $key ) {
        $val = isset( $address[ $key ] ) ? $address[ $key ] : '';
        update_user_meta( $user_id, 'billing_' . $key, $val );
        update_user_meta( $user_id, 'shipping_' . $key, $val );
    }
}

/**
 * Delete a shipping address for a user
 *
 * @param int    $user_id    User ID.
 * @param string $address_id Address ID.
 * @return bool
 */
function jt_delete_user_shipping_address( $user_id, $address_id ) {
    if ( ! $user_id ) {
        return false;
    }

    $addresses = jt_get_user_shipping_addresses( $user_id );
    $was_default = false;

    foreach ( $addresses as $key => $addr ) {
        if ( $addr['id'] === $address_id ) {
            if ( ! empty( $addr['default'] ) ) {
                $was_default = true;
            }
            unset( $addresses[ $key ] );
            break;
        }
    }

    $addresses = array_values( $addresses ); // Re-index array

    if ( $was_default && ! empty( $addresses ) ) {
        $addresses[0]['default'] = true;
        // Sync new default to primary WC fields
        jt_sync_user_primary_address( $user_id, $addresses[0] );
    }

    update_user_meta( $user_id, '_jt_shipping_addresses', $addresses );
    return true;
}

/**
 * Set an address as default for a user
 *
 * @param int    $user_id    User ID.
 * @param string $address_id Address ID.
 * @return bool
 */
function jt_set_default_shipping_address( $user_id, $address_id ) {
    if ( ! $user_id ) {
        return false;
    }

    $addresses = jt_get_user_shipping_addresses( $user_id );
    $default_address = null;

    foreach ( $addresses as &$addr ) {
        if ( $addr['id'] === $address_id ) {
            $addr['default'] = true;
            $default_address = $addr;
        } else {
            $addr['default'] = false;
        }
    }

    update_user_meta( $user_id, '_jt_shipping_addresses', $addresses );

    if ( $default_address ) {
        jt_sync_user_primary_address( $user_id, $default_address );
    }

    return true;
}

/**
 * Intercept actions inside My Account page
 */
add_action( 'template_redirect', 'jt_process_my_account_address_actions' );
function jt_process_my_account_address_actions() {
    if ( ! is_user_logged_in() || ! is_account_page() ) {
        return;
    }

    $user_id = get_current_user_id();

    // 1. Process Set Default Action
    if ( isset( $_GET['action'] ) && $_GET['action'] === 'set_default' && isset( $_GET['address_id'] ) ) {
        if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'jt_set_default_address_' . $_GET['address_id'] ) ) {
            jt_set_default_shipping_address( $user_id, sanitize_text_field( $_GET['address_id'] ) );
            wc_add_notice( __( 'Alamat utama berhasil diperbarui.', 'jendela-ternak' ), 'success' );
            wp_safe_redirect( wc_get_account_endpoint_url( 'edit-address' ) );
            exit;
        }
    }

    // 2. Process Delete Action
    if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['address_id'] ) ) {
        if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'jt_delete_address_' . $_GET['address_id'] ) ) {
            jt_delete_user_shipping_address( $user_id, sanitize_text_field( $_GET['address_id'] ) );
            wc_add_notice( __( 'Alamat berhasil dihapus.', 'jendela-ternak' ), 'success' );
            wp_safe_redirect( wc_get_account_endpoint_url( 'edit-address' ) );
            exit;
        }
    }

    // 3. Process Add/Edit form submission
    if ( isset( $_POST['jt_save_address_submit'] ) ) {
        if ( ! isset( $_POST['jt_address_nonce'] ) || ! wp_verify_nonce( $_POST['jt_address_nonce'], 'jt_save_address' ) ) {
            wc_add_notice( __( 'Verifikasi keamanan gagal.', 'jendela-ternak' ), 'error' );
            return;
        }

        // Gather form fields
        $address_data = array(
            'id'         => isset( $_POST['address_id'] ) ? sanitize_text_field( $_POST['address_id'] ) : '',
            'first_name' => isset( $_POST['billing_first_name'] ) ? sanitize_text_field( $_POST['billing_first_name'] ) : '',
            'last_name'  => isset( $_POST['billing_last_name'] ) ? sanitize_text_field( $_POST['billing_last_name'] ) : '',
            'phone'      => isset( $_POST['billing_phone'] ) ? sanitize_text_field( $_POST['billing_phone'] ) : '',
            'address_1'  => isset( $_POST['billing_address_1'] ) ? sanitize_text_field( $_POST['billing_address_1'] ) : '',
            'address_2'  => isset( $_POST['billing_address_2'] ) ? sanitize_text_field( $_POST['billing_address_2'] ) : '',
            'city'       => isset( $_POST['billing_city'] ) ? sanitize_text_field( $_POST['billing_city'] ) : '',
            'state'      => isset( $_POST['billing_state'] ) ? sanitize_text_field( $_POST['billing_state'] ) : '',
            'postcode'   => isset( $_POST['billing_postcode'] ) ? sanitize_text_field( $_POST['billing_postcode'] ) : '',
            'default'    => isset( $_POST['billing_default'] ) ? true : false,
        );

        // Basic validation
        if ( empty( $address_data['first_name'] ) || empty( $address_data['phone'] ) || empty( $address_data['address_1'] ) || empty( $address_data['city'] ) || empty( $address_data['state'] ) ) {
            wc_add_notice( __( 'Mohon lengkapi semua bidang yang wajib diisi.', 'jendela-ternak' ), 'error' );
            return;
        }

        jt_save_user_shipping_address( $user_id, $address_data );

        wc_add_notice( __( 'Alamat berhasil disimpan.', 'jendela-ternak' ), 'success' );
        wp_safe_redirect( wc_get_account_endpoint_url( 'edit-address' ) );
        exit;
    }
}

/**
 * Filter default WooCommerce checkout fields to populate from default saved address
 */
add_filter( 'woocommerce_checkout_get_value', 'jt_populate_checkout_fields_from_default_address', 10, 2 );
function jt_populate_checkout_fields_from_default_address( $value, $input ) {
    if ( ! is_user_logged_in() ) {
        return $value;
    }

    $user_id = get_current_user_id();
    $addresses = jt_get_user_shipping_addresses( $user_id );

    if ( empty( $addresses ) ) {
        return $value;
    }

    // Find default address
    $default_address = null;
    foreach ( $addresses as $addr ) {
        if ( ! empty( $addr['default'] ) ) {
            $default_address = $addr;
            break;
        }
    }

    if ( ! $default_address ) {
        $default_address = $addresses[0]; // Fallback to first
    }

    // Map WooCommerce input key to our address keys
    // Inputs are billing_first_name, shipping_first_name, etc.
    $field_key = str_replace( array( 'billing_', 'shipping_' ), '', $input );

    if ( isset( $default_address[ $field_key ] ) ) {
        return $default_address[ $field_key ];
    }

    return $value;
}

/**
 * Intercept order placement to save a newly entered address if requested
 */
add_action( 'woocommerce_checkout_update_order_meta', 'jt_save_checkout_address_to_list', 10, 2 );
function jt_save_checkout_address_to_list( $order_id, $posted_data ) {
    if ( ! is_user_logged_in() ) {
        return;
    }

    // Check if the checkbox to save address is checked
    if ( ! isset( $_POST['jt_save_to_address_list'] ) || '1' !== $_POST['jt_save_to_address_list'] ) {
        return;
    }

    $user_id = get_current_user_id();

    // Determine whether they are using shipping fields or billing fields
    // Jendela Ternak checkout primarily uses billing details for Indonesian addresses
    $prefix = 'billing_';
    if ( isset( $_POST['ship_to_different_address'] ) && '1' === $_POST['ship_to_different_address'] ) {
        $prefix = 'shipping_';
    }

    $address_data = array(
        'first_name' => isset( $_POST[ $prefix . 'first_name' ] ) ? sanitize_text_field( $_POST[ $prefix . 'first_name' ] ) : '',
        'last_name'  => isset( $_POST[ $prefix . 'last_name' ] ) ? sanitize_text_field( $_POST[ $prefix . 'last_name' ] ) : '',
        'phone'      => isset( $_POST['billing_phone'] ) ? sanitize_text_field( $_POST['billing_phone'] ) : '', // phone is billing-only in WC
        'address_1'  => isset( $_POST[ $prefix . 'address_1' ] ) ? sanitize_text_field( $_POST[ $prefix . 'address_1' ] ) : '',
        'address_2'  => isset( $_POST[ $prefix . 'address_2' ] ) ? sanitize_text_field( $_POST[ $prefix . 'address_2' ] ) : '',
        'city'       => isset( $_POST[ $prefix . 'city' ] ) ? sanitize_text_field( $_POST[ $prefix . 'city' ] ) : '',
        'state'      => isset( $_POST[ $prefix . 'state' ] ) ? sanitize_text_field( $_POST[ $prefix . 'state' ] ) : '',
        'postcode'   => isset( $_POST[ $prefix . 'postcode' ] ) ? sanitize_text_field( $_POST[ $prefix . 'postcode' ] ) : '',
        'default'    => false,
    );

    // Basic validation
    if ( empty( $address_data['first_name'] ) || empty( $address_data['address_1'] ) || empty( $address_data['city'] ) ) {
        return;
    }

    $saved_addresses = jt_get_user_shipping_addresses( $user_id );
    $duplicate = false;

    foreach ( $saved_addresses as $addr ) {
        if (
            strcasecmp( $addr['first_name'], $address_data['first_name'] ) === 0 &&
            strcasecmp( $addr['address_1'], $address_data['address_1'] ) === 0 &&
            strcasecmp( $addr['city'], $address_data['city'] ) === 0 &&
            strcasecmp( $addr['phone'], $address_data['phone'] ) === 0
        ) {
            $duplicate = true;
            break;
        }
    }

    if ( ! $duplicate ) {
        jt_save_user_shipping_address( $user_id, $address_data );
    }
}
