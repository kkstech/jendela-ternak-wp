<?php
/**
 * Branded saved payment methods.
 *
 * @package JendelaTernakMalang
 * @version 8.9.0
 */

defined( 'ABSPATH' ) || exit;

$saved_methods = wc_get_customer_saved_methods_list( get_current_user_id() );
$has_methods   = (bool) $saved_methods;
$columns       = wc_get_account_payment_methods_columns();

do_action( 'woocommerce_before_account_payment_methods', $has_methods );
?>
<section class="jt-payment-methods">
    <div class="jt-payment-methods__header">
        <div>
            <h2><?php esc_html_e( 'Metode Pembayaran', 'jendela-ternak' ); ?></h2>
            <p><?php esc_html_e( 'Kelola metode pembayaran yang tersimpan di akun Anda.', 'jendela-ternak' ); ?></p>
        </div>
        <?php if ( WC()->payment_gateways->get_available_payment_gateways() ) : ?>
            <a class="jt-btn jt-btn--primary" href="<?php echo esc_url( wc_get_endpoint_url( 'add-payment-method' ) ); ?>"><?php esc_html_e( 'Tambah Metode', 'jendela-ternak' ); ?></a>
        <?php endif; ?>
    </div>

    <?php if ( $has_methods ) : ?>
        <div class="jt-payment-methods__list">
            <?php foreach ( $saved_methods as $methods ) : ?>
                <?php foreach ( $methods as $method ) : ?>
                    <article class="jt-payment-method<?php echo ! empty( $method['is_default'] ) ? ' is-default' : ''; ?>">
                        <div class="jt-payment-method__details">
                            <?php foreach ( $columns as $column_id => $column_name ) : ?>
                                <?php if ( 'actions' === $column_id ) { continue; } ?>
                                <div class="jt-payment-method__field">
                                    <span><?php echo esc_html( $column_name ); ?></span>
                                    <strong>
                                        <?php
                                        if ( has_action( 'woocommerce_account_payment_methods_column_' . $column_id ) ) {
                                            do_action( 'woocommerce_account_payment_methods_column_' . $column_id, $method );
                                        } elseif ( 'method' === $column_id ) {
                                            echo esc_html( ! empty( $method['method']['last4'] ) ? sprintf( '%s •••• %s', wc_get_credit_card_type_label( $method['method']['brand'] ), $method['method']['last4'] ) : wc_get_credit_card_type_label( $method['method']['brand'] ) );
                                        } elseif ( 'expires' === $column_id ) {
                                            echo esc_html( $method['expires'] );
                                        }
                                        ?>
                                    </strong>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="jt-payment-method__actions">
                            <?php foreach ( $method['actions'] as $key => $action ) : ?>
                                <a href="<?php echo esc_url( $action['url'] ); ?>" class="jt-btn jt-btn--outline <?php echo esc_attr( sanitize_html_class( $key ) ); ?>"><?php echo esc_html( $action['name'] ); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="jt-payment-methods__empty">
            <i class="fa-regular fa-credit-card" aria-hidden="true"></i>
            <p><?php esc_html_e( 'Belum ada metode pembayaran tersimpan.', 'jendela-ternak' ); ?></p>
        </div>
    <?php endif; ?>
</section>
<?php do_action( 'woocommerce_after_account_payment_methods', $has_methods ); ?>
