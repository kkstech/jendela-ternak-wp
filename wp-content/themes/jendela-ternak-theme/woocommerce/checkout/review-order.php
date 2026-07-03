<?php
/**
 * woocommerce/checkout/review-order.php
 * Custom WooCommerce checkout review order template override.
 * Displays product thumbnail images and clean Shopee-style table headers.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;
?>
<table class="shop_table woocommerce-checkout-review-order-table jt-checkout-review-table">
    <thead>
        <tr>
            <th class="product-name"><?php esc_html_e( 'Produk', 'jendela-ternak' ); ?></th>
            <th class="product-price"><?php esc_html_e( 'Harga Satuan', 'jendela-ternak' ); ?></th>
            <th class="product-quantity"><?php esc_html_e( 'Jumlah', 'jendela-ternak' ); ?></th>
            <th class="product-total"><?php esc_html_e( 'Subtotal', 'jendela-ternak' ); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        do_action( 'woocommerce_review_order_before_cart_contents' );

        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                $thumbnail = $_product->get_image( array( 52, 52 ), array( 'class' => 'jt-checkout-product-img' ) );
                ?>
                <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                    <!-- Column 1: Image & Name -->
                    <td class="product-name">
                        <div class="jt-checkout-product-details">
                            <div class="jt-checkout-product-thumb">
                                <?php echo $thumbnail; ?>
                            </div>
                            <div class="jt-checkout-product-info">
                                <span class="jt-checkout-product-title">
                                    <?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?>
                                </span>
                                <?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
                            </div>
                        </div>
                    </td>
                    
                    <!-- Column 2: Unit Price -->
                    <td class="product-price">
                        <?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?>
                    </td>
                    
                    <!-- Column 3: Quantity -->
                    <td class="product-quantity">
                        <?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', 'x' . $cart_item['quantity'], $cart_item, $cart_item_key ); ?>
                    </td>
                    
                    <!-- Column 4: Subtotal -->
                    <td class="product-total">
                        <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
                    </td>
                </tr>
                <?php
            }
        }

        do_action( 'woocommerce_review_order_after_cart_contents' );
        ?>
    </tbody>
    
    <tfoot>
        <!-- Subtotal -->
        <tr class="cart-subtotal">
            <th colspan="3"><?php esc_html_e( 'Subtotal Produk', 'jendela-ternak' ); ?></th>
            <td><?php wc_cart_totals_subtotal_html(); ?></td>
        </tr>

        <!-- Coupons -->
        <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
            <tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                <th colspan="3"><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
                <td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
            </tr>
        <?php endforeach; ?>

        <!-- Shipping selector (Biteship) -->
        <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
            <?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
            <?php wc_cart_totals_shipping_html(); ?>
            <?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
        <?php endif; ?>

        <!-- Fees -->
        <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
            <tr class="fee">
                <th colspan="3"><?php echo esc_html( $fee->name ); ?></th>
                <td><?php wc_cart_totals_fee_html( $fee ); ?></td>
            </tr>
        <?php endforeach; ?>

        <!-- Taxes -->
        <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
            <?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
                <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
                    <tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                        <th colspan="3"><?php echo esc_html( $tax->label ); ?></th>
                        <td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr class="tax-total">
                    <th colspan="3"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
                    <td><?php wc_cart_totals_taxes_total_html(); ?></td>
                </tr>
            <?php endif; ?>
        <?php endif; ?>

        <?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

        <!-- Order Total -->
        <tr class="order-total">
            <th colspan="3"><?php esc_html_e( 'Total Pembayaran', 'jendela-ternak' ); ?></th>
            <td><strong><?php wc_cart_totals_order_total_html(); ?></strong></td>
        </tr>

        <?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
    </tfoot>
</table>
