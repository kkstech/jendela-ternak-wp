<?php
/**
 * woocommerce/checkout/form-checkout.php
 * Custom Shopee-Style single column vertical checkout form template.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
    echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
    return;
}
?>

<div class="jt-shopee-checkout woocommerce">
    <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

        <!-- 1. ALAMAT PENGIRIMAN (Shipping Address Card) -->
        <div class="jt-checkout-card jt-checkout-address-card">
            <!-- Shopee border line -->
            <div class="jt-shopee-border-line"></div>
            
            <div class="jt-checkout-card-header">
                <span class="jt-card-icon">📍</span>
                <h3><?php esc_html_e( 'Alamat Pengiriman', 'jendela-ternak' ); ?></h3>
            </div>
            
            <div class="jt-checkout-card-body">
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
                
                <!-- Additional fields / Notes -->
                <?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
            </div>
        </div>

        <!-- 2. PRODUK DIPESAN (Products List Card) -->
        <div class="jt-checkout-card jt-checkout-products-card">
            <div class="jt-checkout-card-header">
                <span class="jt-card-icon">📦</span>
                <h3><?php esc_html_e( 'Produk Dipesan', 'jendela-ternak' ); ?></h3>
            </div>
            <div class="jt-checkout-card-body" id="order_review">
                <?php 
                // Outputs review-order.php (cart items list and shipping carrier selection)
                woocommerce_order_review(); 
                ?>
            </div>
        </div>

        <!-- 3. METODE PEMBAYARAN (Payment Methods Card) -->
        <div class="jt-checkout-card jt-checkout-payment-card">
            <div class="jt-checkout-card-header">
                <span class="jt-card-icon">💳</span>
                <h3><?php esc_html_e( 'Metode Pembayaran', 'jendela-ternak' ); ?></h3>
            </div>
            <div class="jt-checkout-card-body">
                <?php 
                // Outputs payment.php (payment methods list and place order submit button)
                woocommerce_checkout_payment(); 
                ?>
            </div>
        </div>

    </form>
</div>
