<?php
/**
 * woocommerce/cart/cart.php
 * Override WooCommerce cart template with custom branded layout.
 *
 * @package JendelaTernakMalang
 */
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<div class="jt-cart-page">

    <!-- Cart Items Column -->
    <div>
        <h2 style="font-size:20px;font-weight:800;color:var(--color-primary);margin-bottom:20px;">
            🛒 <?php esc_html_e( 'Keranjang Belanja', 'jendela-ternak' ); ?>
            <span style="font-size:14px;font-weight:400;color:var(--color-text-muted);">(<?php echo WC()->cart->get_cart_contents_count(); ?> item)</span>
        </h2>

        <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
            <?php do_action( 'woocommerce_before_cart_table' ); ?>

            <table class="jt-cart-table woocommerce-cart-form__contents" cellspacing="0">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Produk', 'jendela-ternak' ); ?></th>
                        <th><?php esc_html_e( 'Harga', 'jendela-ternak' ); ?></th>
                        <th><?php esc_html_e( 'Jumlah', 'jendela-ternak' ); ?></th>
                        <th><?php esc_html_e( 'Total', 'jendela-ternak' ); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php do_action( 'woocommerce_before_cart_contents' ); ?>

                    <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
                        $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                        $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
                        $thumbnail  = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'thumbnail', [ 'style' => 'width:64px;height:64px;object-fit:cover;border-radius:8px;' ] ), $cart_item, $cart_item_key );

                        if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] === 0 || ! apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                            continue;
                        }

                        $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                    ?>
                    <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

                        <td>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <?php if ( ! $product_permalink ) : ?>
                                    <?php echo $thumbnail; // PHPCS: XSS ok. ?>
                                <?php else : ?>
                                    <a href="<?php echo esc_url( $product_permalink ); ?>"><?php echo $thumbnail; // PHPCS: XSS ok. ?></a>
                                <?php endif; ?>
                                <div>
                                    <a href="<?php echo esc_url( $product_permalink ); ?>" style="font-weight:600;font-size:14px;color:var(--color-text);">
                                        <?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?>
                                    </a>
                                    <?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
                                    <?php if ( $_product->is_on_backorder( $cart_item['quantity'] ) ) : ?>
                                        <p class="backorder_notification" style="color:var(--color-accent);font-size:12px;"><?php esc_html_e( 'Available on backorder', 'woocommerce' ); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>

                        <td>
                            <?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok. ?>
                        </td>

                        <td>
                            <?php
                            if ( $_product->is_sold_individually() ) {
                                $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
                            } else {
                                $product_quantity = woocommerce_quantity_input(
                                    [
                                        'input_name'   => "cart[{$cart_item_key}][qty]",
                                        'input_value'  => $cart_item['quantity'],
                                        'max_value'    => $_product->get_max_purchase_quantity(),
                                        'min_value'    => '0',
                                        'product_name' => $_product->get_name(),
                                    ],
                                    $_product,
                                    false
                                );
                            }
                            echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
                            ?>
                        </td>

                        <td style="font-weight:700;color:var(--color-primary);">
                            <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok. ?>
                        </td>

                        <td>
                            <?php
                            echo apply_filters( // PHPCS: XSS ok.
                                'woocommerce_cart_item_remove_link',
                                sprintf(
                                    '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s" style="color:var(--color-red);font-size:20px;line-height:1;">&times;</a>',
                                    esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                    /* translators: %s is product name */
                                    esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $_product->get_name() ) ) ),
                                    esc_attr( $product_id ),
                                    esc_attr( $_product->get_sku() )
                                ),
                                $cart_item_key
                            );
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php do_action( 'woocommerce_cart_contents' ); ?>

                    <tr>
                        <td colspan="5" style="padding:16px;">
                            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                                <?php if ( wc_coupons_enabled() ) : ?>
                                <div style="display:flex;gap:8px;flex:1;min-width:200px;">
                                    <input
                                        type="text"
                                        name="coupon_code"
                                        class="input-text"
                                        id="coupon_code"
                                        value=""
                                        placeholder="<?php esc_attr_e( 'Kode Kupon...', 'jendela-ternak' ); ?>"
                                        style="border:2px solid var(--color-border);border-radius:8px;padding:10px 12px;font-size:14px;font-family:var(--font-sans);outline:none;flex:1;"
                                    >
                                    <button type="submit" class="jt-btn jt-btn--outline" name="apply_coupon" value="<?php esc_attr_e( 'Terapkan', 'jendela-ternak' ); ?>">
                                        <?php esc_html_e( 'Terapkan', 'jendela-ternak' ); ?>
                                    </button>
                                </div>
                                <?php endif; ?>

                                <button type="submit" class="jt-btn jt-btn--ghost" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'jendela-ternak' ); ?>">
                                    <?php esc_html_e( 'Perbarui Keranjang', 'jendela-ternak' ); ?>
                                </button>

                                <?php do_action( 'woocommerce_cart_actions' ); ?>
                                <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
                            </div>
                        </td>
                    </tr>

                    <?php do_action( 'woocommerce_after_cart_contents' ); ?>
                </tbody>
            </table>

            <?php do_action( 'woocommerce_after_cart_table' ); ?>
        </form>

        <?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
    </div><!-- Cart Items Column -->

    <!-- Cart Summary Column -->
    <div class="jt-cart-summary">
        <h3><?php esc_html_e( 'Ringkasan Pesanan', 'jendela-ternak' ); ?></h3>

        <?php do_action( 'woocommerce_cart_collaterals' ); ?>
        <?php // woocommerce_cart_totals is hooked here by WC ?>

        <!-- Quick checkout buttons -->
        <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="jt-btn jt-btn--primary" id="jt-cart-checkout-btn" style="width:100%;text-align:center;margin-top:4px;">
            <?php esc_html_e( 'Lanjut ke Checkout', 'jendela-ternak' ); ?>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </a>

        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="jt-btn jt-btn--ghost" style="width:100%;text-align:center;">
            ← <?php esc_html_e( 'Lanjut Belanja', 'jendela-ternak' ); ?>
        </a>
    </div>

</div><!-- .jt-cart-page -->

<?php do_action( 'woocommerce_after_cart' ); ?>
