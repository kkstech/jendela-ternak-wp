<?php
/**
 * Branded pay-for-order page.
 *
 * @package JendelaTernakMalang
 * @version 10.9.0
 */

defined( 'ABSPATH' ) || exit;

$totals = $order->get_order_item_totals();
?>
<div class="jt-order-pay">
    <div class="jt-order-pay__header">
        <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" aria-label="<?php esc_attr_e( 'Kembali ke pesanan', 'jendela-ternak' ); ?>">&larr;</a>
        <div>
            <p><?php esc_html_e( 'Selesaikan pembayaran', 'jendela-ternak' ); ?></p>
            <h1><?php printf( esc_html__( 'Pesanan #%s', 'jendela-ternak' ), esc_html( $order->get_order_number() ) ); ?></h1>
        </div>
    </div>

    <form id="order_review" method="post" class="jt-order-pay__card">
        <table class="shop_table jt-order-pay__items">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Produk', 'jendela-ternak' ); ?></th>
                    <th><?php esc_html_e( 'Jumlah', 'jendela-ternak' ); ?></th>
                    <th><?php esc_html_e( 'Total', 'jendela-ternak' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $order->get_items() as $item_id => $item ) : ?>
                    <?php if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) { continue; } ?>
                    <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
                        <td>
                            <?php
                            echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );
                            do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );
                            wc_display_item_meta( $item );
                            do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
                            ?>
                        </td>
                        <td><?php echo wp_kses_post( apply_filters( 'woocommerce_order_item_quantity_html', '&times;&nbsp;' . $item->get_quantity(), $item ) ); ?></td>
                        <td><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <?php foreach ( $totals as $total ) : ?>
                    <tr>
                        <th colspan="2"><?php echo wp_kses_post( $total['label'] ); ?></th>
                        <td><?php echo wp_kses_post( $total['value'] ); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tfoot>
        </table>

        <?php do_action( 'woocommerce_pay_order_before_payment' ); ?>
        <div id="payment" class="jt-checkout-payment-card">
            <?php if ( $order->needs_payment() ) : ?>
                <h2><?php esc_html_e( 'Pilih metode pembayaran', 'jendela-ternak' ); ?></h2>
                <ul class="wc_payment_methods payment_methods methods" aria-label="<?php esc_attr_e( 'Metode pembayaran', 'jendela-ternak' ); ?>">
                    <?php if ( $available_gateways ) : ?>
                        <?php foreach ( $available_gateways as $gateway ) : ?>
                            <?php wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) ); ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li><?php wc_print_notice( esc_html__( 'Tidak ada metode pembayaran yang tersedia. Hubungi kami untuk bantuan.', 'jendela-ternak' ), 'notice' ); ?></li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>

            <div class="form-row place-order">
                <input type="hidden" name="woocommerce_pay" value="1" />
                <?php wc_get_template( 'checkout/terms.php' ); ?>
                <?php do_action( 'woocommerce_pay_order_before_submit' ); ?>
                <?php echo wp_kses_post( apply_filters( 'woocommerce_pay_order_button_html', '<button type="submit" class="button alt" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ) ); ?>
                <?php do_action( 'woocommerce_pay_order_after_submit' ); ?>
                <?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
            </div>
        </div>
    </form>
</div>
