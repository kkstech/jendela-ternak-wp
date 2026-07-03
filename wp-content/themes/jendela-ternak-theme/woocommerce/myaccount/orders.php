<?php
/**
 * woocommerce/myaccount/orders.php
 * Customized My Account Orders dashboard template with horizontal status filter tabs and card list layout.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>

<?php if ( $has_orders ) : ?>

    <?php
    $default_tab = isset( $_GET['order_status'] ) ? sanitize_text_field( $_GET['order_status'] ) : 'all';
    $valid_tabs  = array( 'all', 'pending', 'processing', 'shipping', 'completed' );
    if ( ! in_array( $default_tab, $valid_tabs, true ) ) {
        $default_tab = 'all';
    }
    ?>
    <div x-data="{ currentTab: '<?php echo esc_attr( $default_tab ); ?>' }" id="jt-account-orders-root">

        <!-- ── Shopee-style Horizontal Status Tabs ── -->
        <nav class="jt-order-status-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Status Pesanan', 'jendela-ternak' ); ?>">
            <button class="jt-order-status-tab" :class="{ 'active': currentTab === 'all' }" @click="currentTab = 'all'" role="tab" :aria-selected="currentTab === 'all'">
                <?php esc_html_e( 'Semua', 'jendela-ternak' ); ?>
            </button>
            <button class="jt-order-status-tab" :class="{ 'active': currentTab === 'pending' }" @click="currentTab = 'pending'" role="tab" :aria-selected="currentTab === 'pending'">
                <?php esc_html_e( 'Belum Bayar', 'jendela-ternak' ); ?>
            </button>
            <button class="jt-order-status-tab" :class="{ 'active': currentTab === 'processing' }" @click="currentTab = 'processing'" role="tab" :aria-selected="currentTab === 'processing'">
                <?php esc_html_e( 'Dikemas', 'jendela-ternak' ); ?>
            </button>
            <button class="jt-order-status-tab" :class="{ 'active': currentTab === 'shipping' }" @click="currentTab = 'shipping'" role="tab" :aria-selected="currentTab === 'shipping'">
                <?php esc_html_e( 'Dikirim', 'jendela-ternak' ); ?>
            </button>
            <button class="jt-order-status-tab" :class="{ 'active': currentTab === 'completed' }" @click="currentTab = 'completed'" role="tab" :aria-selected="currentTab === 'completed'">
                <?php esc_html_e( 'Selesai', 'jendela-ternak' ); ?>
            </button>
        </nav>

        <!-- ── Card List Layout (Shopee-style) ── -->
        <div class="jt-account-orders-list">
            <?php
            foreach ( $customer_orders->orders as $customer_order ) {
                $order      = wc_get_order( $customer_order );
                $order_id   = $order->get_id();
                $status     = $order->get_status();
                $date       = wc_format_datetime( $order->get_date_created() );
                $total      = $order->get_formatted_order_total();
                $items      = $order->get_items();
                $items_count = count( $items );
                
                // Get first item
                $first_item = reset( $items );
                $first_product = $first_item ? $first_item->get_product() : null;
                $first_name = $first_item ? $first_item->get_name() : __( 'Produk', 'jendela-ternak' );
                $first_qty  = $first_item ? $first_item->get_quantity() : 1;
                
                $img_url = '';
                if ( $first_product ) {
                    $img_id  = $first_product->get_image_id();
                    $img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'thumbnail' ) : wc_placeholder_img_src();
                } else {
                    $img_url = wc_placeholder_img_src();
                }

                // Check for Biteship waybill number
                $biteship_track = get_post_meta( $order_id, '_biteship_waybill_id', true ) ?: get_post_meta( $order_id, '_biteship_tracking_code', true );

                // Map order status to simplified tabs
                $tab_category = 'all';
                if ( in_array( $status, [ 'pending', 'on-hold' ], true ) ) {
                    $tab_category = 'pending';
                } elseif ( $status === 'processing' ) {
                    $tab_category = $biteship_track ? 'shipping' : 'processing';
                } elseif ( $status === 'completed' ) {
                    $tab_category = 'completed';
                }

                // WhatsApp trigger
                $wa_number = jt_get_setting( 'whatsapp_number', '6281234567890' );
                $wa_msg    = urlencode( sprintf( 'Halo CS Jendela Ternak, saya ingin bertanya tentang pesanan #%s. Status saat ini: %s.', $order->get_order_number(), $status ) );
                $wa_url    = 'https://wa.me/' . esc_attr( $wa_number ) . '?text=' . $wa_msg;
                ?>
                <div 
                    class="jt-account-order-card status-<?php echo esc_attr( $status ); ?>" 
                    x-show="currentTab === 'all' || currentTab === '<?php echo esc_attr( $tab_category ); ?>'"
                    x-transition
                    style="background:#fff;border-radius:12px;padding:16px;box-shadow:var(--shadow-sm);margin-bottom:14px;border-top:3px solid var(--color-border);"
                >
                    <!-- Header -->
                    <div class="jt-account-order-header">
                        <div>
                            <strong>#<?php echo esc_html( $order->get_order_number() ); ?></strong>
                            <span style="color:var(--color-text-muted);margin-left:8px;"><?php echo esc_html( $date ); ?></span>
                        </div>
                        <div>
                            <span class="jt-badge <?php echo $status === 'completed' ? 'jt-badge--original' : 'jt-badge--discount'; ?>">
                                <?php echo esc_html( wc_get_order_status_name( $status ) ); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="jt-account-order-body">
                        <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $first_name ); ?>" style="width:64px;height:64px;object-fit:cover;border-radius:8px;background:var(--color-light-bg);">
                        <div style="flex:1;">
                            <h4 style="font-size:14px;font-weight:600;color:var(--color-text);margin-bottom:4px;"><?php echo esc_html( $first_name ); ?></h4>
                            <p style="font-size:12px;color:var(--color-text-muted);">
                                <?php echo esc_html( $first_qty ); ?>x <?php if ( $items_count > 1 ) : ?>
                                    + <?php echo esc_html( $items_count - 1 ); ?> <?php esc_html_e( 'produk lainnya', 'jendela-ternak' ); ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="jt-account-order-footer">
                        <div>
                            <span style="font-size:12px;color:var(--color-text-muted);"><?php esc_html_e( 'Total:', 'jendela-ternak' ); ?></span>
                            <strong style="color:var(--color-red);font-size:15px;margin-left:4px;"><?php echo wp_kses_post( $total ); ?></strong>
                        </div>
                        
                        <div style="display:flex;gap:8px;">
                            <a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" class="jt-btn jt-btn--ghost" style="padding:6px 12px;font-size:12px;">
                                <i class="fa-solid fa-comment-dots" style="margin-right: 4px;"></i> CS
                            </a>

                            <?php if ( $status === 'pending' ) : ?>
                                <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="jt-btn jt-btn--accent" style="padding:6px 12px;font-size:12px;">
                                    <i class="fa-solid fa-wallet" style="margin-right: 4px;"></i> Bayar
                                </a>
                            <?php endif; ?>

                            <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="jt-btn jt-btn--outline" style="padding:6px 12px;font-size:12px;">
                                <i class="fa-solid fa-magnifying-glass" style="margin-right: 4px;"></i> Detail
                            </a>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>

    </div>

<?php else : ?>
    <div class="jt-no-products" style="background:#fff;border-radius:12px;box-shadow:var(--shadow-sm);">
        <span style="font-size:48px;display:block;margin-bottom:12px;color:var(--color-text-muted);" role="img" aria-label="Inbox"><i class="fa-solid fa-inbox"></i></span>
        <p><?php esc_html_e( 'Anda belum memiliki riwayat pesanan.', 'jendela-ternak' ); ?></p>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="jt-btn jt-btn--primary" style="margin-top:16px;">
            <?php esc_html_e( 'Mulai Belanja', 'jendela-ternak' ); ?>
        </a>
    </div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
