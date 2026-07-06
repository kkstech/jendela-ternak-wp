<?php
/**
 * woocommerce/checkout/form-checkout.php
 * Custom Shopee-Style single/double column responsive checkout form template.
 *
 * @package JendelaTernakMalang
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
    echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
    return;
}
?>

<div class="jt-shopee-checkout-wrapper">
    <!-- Back arrow & page title (Shopee native style) -->
    <div class="jt-checkout-header-bar">
        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="jt-checkout-back-link">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <h1 class="jt-checkout-title"><?php esc_html_e( 'Checkout', 'jendela-ternak' ); ?></h1>
    </div>

    <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">
        
        <div class="jt-checkout-grid">
            <!-- Left Column: Address and Product Items -->
            <div class="jt-checkout-col-left">
                
                <!-- 1. ALAMAT PENGIRIMAN (Shipping Address Card) -->
                <div class="jt-checkout-card jt-checkout-address-card">
                    <!-- Shopee border line -->
                    <div class="jt-shopee-border-line"></div>
                    
                    <div class="jt-checkout-card-header">
                        <span class="jt-card-icon"><i class="fa-solid fa-location-dot"></i></span>
                        <h3><?php esc_html_e( 'Alamat Pengiriman', 'jendela-ternak' ); ?></h3>
                    </div>
                    
                    <div class="jt-checkout-card-body">
                        <!-- Address Summary Block (Shopee-Style) -->
                        <div id="jt-address-summary" class="jt-address-summary-container" style="display: none;">
                            <div class="jt-address-summary-content">
                                <div class="jt-address-summary-meta">
                                    <span class="jt-address-summary-name" id="jt-summary-name"></span>
                                    <span class="jt-address-summary-phone" id="jt-summary-phone"></span>
                                </div>
                                <div class="jt-address-summary-text" id="jt-summary-text"></div>
                            </div>
                            <button type="button" id="jt-edit-address-btn" class="jt-address-summary-edit-btn">
                                Ubah
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 inline-block ml-1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>

                        <!-- Real WooCommerce address fields wrapper -->
                        <div id="jt-raw-address-fields" class="jt-raw-address-fields-container">
                            <?php if ( $checkout->get_checkout_fields() ) : ?>
                                <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
                                
                                <div id="customer_details">
                                    <!-- Billing Form Fields -->
                                    <?php do_action( 'woocommerce_checkout_billing' ); ?>
                                    
                                    <!-- Shipping Form Fields (if enabled) -->
                                    <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                                </div>
                                
                                <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
                            <?php endif; ?>
                            
                            <!-- Checkbox Save to Address List (Only for logged-in users) -->
                            <?php if ( is_user_logged_in() ) : ?>
                                <div class="jt-checkout-save-address-checkbox mt-3 mb-1">
                                    <label class="checkbox flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700">
                                        <input type="checkbox" name="jt_save_to_address_list" id="jt_save_to_address_list" value="1" checked />
                                        <span><?php esc_html_e( 'Simpan alamat ini ke Daftar Alamat', 'jendela-ternak' ); ?></span>
                                    </label>
                                </div>
                            <?php endif; ?>

                            <!-- Save/Close Address Button -->
                            <div class="jt-address-save-action mt-4" style="display: none;">
                                <button type="button" id="jt-save-address-btn" class="jt-btn jt-btn--secondary py-2 px-4 rounded-lg font-bold text-xs bg-green-700 text-white hover:bg-green-800 transition">
                                    Simpan & Gunakan Alamat
                                </button>
                            </div>
                        </div>

                        <!-- Additional fields / Notes -->
                        <?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
                    </div>
                </div>

                <!-- 2. PRODUK DIPESAN (Products List Card) -->
                <div class="jt-checkout-card jt-checkout-products-card">
                    <div class="jt-checkout-card-header">
                        <span class="jt-card-icon"><i class="fa-solid fa-box"></i></span>
                        <h3><?php esc_html_e( 'Produk Dipesan', 'jendela-ternak' ); ?></h3>
                    </div>
                    
                    <div class="jt-checkout-card-body" id="order_review">
                        <?php 
                        // Outputs review-order.php (cart items list and shipping carrier selection)
                        woocommerce_order_review(); 
                        ?>
                    </div>

                    <!-- Custom order notes field here (outside #order_review to prevent AJAX refresh focus loss) -->
                    <div class="jt-checkout-order-notes-row">
                        <div class="jt-order-notes-label">
                            <span class="jt-notes-icon"><i class="fa-solid fa-pen-to-square"></i></span>
                            <span class="jt-notes-title">Pesan untuk Penjual:</span>
                        </div>
                        <div class="jt-order-notes-input-wrapper">
                            <input type="text" name="order_comments" class="input-text jt-order-comments-input" id="order_comments" placeholder="Tinggalkan pesan ke penjual..." value="<?php echo esc_attr( WC()->checkout->get_value( 'order_comments' ) ); ?>">
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Vouchers and Payment -->
            <div class="jt-checkout-col-right">
                

                <!-- 4. METODE PEMBAYARAN (Payment Methods Card) -->
                <div class="jt-checkout-card jt-checkout-payment-card">
                    <div class="jt-checkout-card-header">
                        <span class="jt-card-icon"><i class="fa-solid fa-credit-card"></i></span>
                        <h3><?php esc_html_e( 'Metode Pembayaran', 'jendela-ternak' ); ?></h3>
                    </div>
                    <div class="jt-checkout-card-body">
                        <?php 
                        // Outputs payment.php (payment methods list and place order submit button)
                        woocommerce_checkout_payment(); 
                        ?>
                    </div>
                </div>

            </div>
        </div>

    </form>
</div>

<!-- Mobile Sticky Bottom Bar (Shopee Style) -->
<div class="jt-mobile-sticky-checkout-bar">
    <div class="jt-mobile-sticky-left">
        <div class="jt-sticky-total-wrapper">
            <span class="jt-sticky-total-label">Total Pembayaran</span>
            <span class="jt-sticky-total-price" id="jt-sticky-total-price">Rp0</span>
        </div>
        <div class="jt-sticky-savings" id="jt-sticky-total-savings" style="display: none;">
            Hemat <span id="jt-sticky-savings-amount">Rp0</span>
        </div>
    </div>
    <div class="jt-mobile-sticky-right">
        <button type="button" id="jt-sticky-submit-btn" class="jt-sticky-checkout-btn">
            Pesan Sekarang
        </button>
    </div>
</div>

<?php if ( is_user_logged_in() ) : 
    $user_id = get_current_user_id();
    $saved_addresses = jt_get_user_shipping_addresses( $user_id );
?>
    <!-- Shopee-Style Saved Addresses Selection Modal -->
    <div id="jt-address-modal" class="jt-address-modal-overlay" style="display: none;">
        <div class="jt-address-modal-content">
            <div class="jt-address-modal-header">
                <h3><?php esc_html_e( 'Alamat Saya', 'jendela-ternak' ); ?></h3>
                <button type="button" class="jt-address-modal-close-btn">&times;</button>
            </div>
            
            <div class="jt-address-modal-body">
                <?php if ( empty( $saved_addresses ) ) : ?>
                    <div class="jt-modal-empty-addresses">
                        <i class="fa-solid fa-location-dot text-gray-300 text-4xl mb-2 block text-center"></i>
                        <p class="text-xs text-gray-500 text-center"><?php esc_html_e( 'Belum ada alamat tersimpan.', 'jendela-ternak' ); ?></p>
                    </div>
                <?php else : ?>
                    <div class="jt-address-modal-list">
                        <?php foreach ( $saved_addresses as $addr ) :
                            $full_name = trim( $addr['first_name'] . ' ' . $addr['last_name'] );
                            
                            $state_code = $addr['state'];
                            $states = WC()->countries->get_states( 'ID' );
                            $state_name = isset( $states[ $state_code ] ) ? $states[ $state_code ] : $state_code;
                            
                            $full_address = trim( $addr['address_1'] . ( $addr['address_2'] ? ', ' . $addr['address_2'] : '' ) . ', ' . $addr['city'] . ', ' . $state_name . ' ' . $addr['postcode'] );
                        ?>
                            <div class="jt-address-modal-item <?php echo $addr['default'] ? 'jt-address-modal-item--default' : ''; ?>">
                                <input type="radio" name="jt_selected_address" id="jt_addr_radio_<?php echo esc_attr( $addr['id'] ); ?>" value="<?php echo esc_attr( $addr['id'] ); ?>" <?php checked( $addr['default'], true ); ?>
                                       data-first_name="<?php echo esc_attr( $addr['first_name'] ); ?>"
                                       data-last_name="<?php echo esc_attr( $addr['last_name'] ); ?>"
                                       data-phone="<?php echo esc_attr( $addr['phone'] ); ?>"
                                       data-address_1="<?php echo esc_attr( $addr['address_1'] ); ?>"
                                       data-address_2="<?php echo esc_attr( $addr['address_2'] ); ?>"
                                       data-city="<?php echo esc_attr( $addr['city'] ); ?>"
                                       data-state="<?php echo esc_attr( $addr['state'] ); ?>"
                                       data-postcode="<?php echo esc_attr( $addr['postcode'] ); ?>"
                                       class="jt-address-modal-radio"
                                />
                                <label for="jt_addr_radio_<?php echo esc_attr( $addr['id'] ); ?>" class="jt-address-modal-item-info">
                                    <div class="jt-address-modal-item-meta">
                                        <span class="jt-address-modal-item-name"><?php echo esc_html( $full_name ); ?></span>
                                        <span class="jt-address-modal-item-divider">|</span>
                                        <span class="jt-address-modal-item-phone"><?php echo esc_html( $addr['phone'] ); ?></span>
                                    </div>
                                    <div class="jt-address-modal-item-text"><?php echo esc_html( $full_address ); ?></div>
                                    <?php if ( $addr['default'] ) : ?>
                                        <span class="jt-address-modal-item-badge"><?php esc_html_e( 'Utama', 'jendela-ternak' ); ?></span>
                                    <?php endif; ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="jt-address-modal-footer">
                <button type="button" class="jt-btn jt-btn--outline jt-address-modal-close-btn"><?php esc_html_e( 'Batal', 'jendela-ternak' ); ?></button>
                <button type="button" id="jt-address-modal-add-btn" class="jt-btn jt-btn--outline jt-address-modal-add-new-btn"><i class="fa-solid fa-plus mr-1"></i> <?php esc_html_e( 'Tambah Alamat Baru', 'jendela-ternak' ); ?></button>
                <button type="button" id="jt-address-modal-confirm-btn" class="jt-btn jt-btn--primary"><?php esc_html_e( 'Konfirmasi', 'jendela-ternak' ); ?></button>
            </div>
        </div>
    </div>
<?php endif; ?>

