<?php
/**
 * woocommerce/myaccount/dashboard.php
 * Customized My Account Dashboard landing page with user greeting, Shopee-style order status counts, and action cards.
 *
 * @package JendelaTernakMalang
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$customer_id  = $current_user->ID;

// Query all customer orders to count statuses
$customer_orders = wc_get_orders( array(
    'customer' => $customer_id,
    'limit'    => -1,
) );

$count_pending   = 0; // Belum Bayar
$count_packing   = 0; // Dikemas (Processing, no Biteship track)
$count_shipping  = 0; // Dikirim (Processing with track, or shipping status)
$count_completed = 0; // Selesai

foreach ( $customer_orders as $order ) {
    $status = $order->get_status();
    
    // Biteship tracking check
    $biteship_track = get_post_meta( $order->get_id(), '_biteship_waybill_id', true ) ?: get_post_meta( $order->get_id(), '_biteship_tracking_code', true );

    if ( in_array( $status, array( 'pending', 'on-hold' ), true ) ) {
        $count_pending++;
    } elseif ( $status === 'processing' ) {
        if ( $biteship_track ) {
            $count_shipping++;
        } else {
            $count_packing++;
        }
    } elseif ( $status === 'shipping' ) {
        $count_shipping++;
    } elseif ( $status === 'completed' ) {
        $count_completed++;
    }
}

$orders_url    = esc_url( wc_get_endpoint_url( 'orders' ) );
$address_url   = esc_url( wc_get_endpoint_url( 'edit-address' ) );
$account_url   = esc_url( wc_get_endpoint_url( 'edit-account' ) );

// Fetch cart items count
$cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
?>

<div class="jt-myaccount-dashboard-wrapper">
    
    <!-- Cart Reminder Banner -->
    <?php if ( $cart_count > 0 ) : ?>
        <div class="jt-cart-reminder-banner">
            <div class="jt-cart-reminder-info">
                <span class="jt-cart-reminder-icon"><i class="fa-solid fa-cart-shopping"></i></span>
                <span class="jt-cart-reminder-text">
                    <?php
                    printf(
                        /* translators: 1: item count */
                        _n( 'Anda memiliki <strong>%1$d produk</strong> di keranjang belanja.', 'Anda memiliki <strong>%1$d produk</strong> di keranjang belanja.', $cart_count, 'jendela-ternak' ),
                        $cart_count
                    );
                    ?>
                    <?php esc_html_e( ' Segera selesaikan transaksi Anda.', 'jendela-ternak' ); ?>
                </span>
            </div>
            <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="jt-cart-reminder-btn">
                <?php esc_html_e( 'Checkout Sekarang ❯', 'jendela-ternak' ); ?>
            </a>
        </div>
    <?php endif; ?>
    <!-- Welcome Greeting Header -->
    <div class="jt-myaccount-welcome-card">
        <h2 class="jt-welcome-title">
            <?php
            printf(
                /* translators: 1: user display name */
                esc_html__( 'Halo %1$s!', 'jendela-ternak' ),
                '<strong>' . esc_html( $current_user->display_name ) . '</strong>'
            );
            ?>
        </h2>
        <p class="jt-welcome-text">
            <?php esc_html_e( 'Selamat datang di panel akun Jendela Ternak Malang Anda. Di sini Anda dapat melacak pengiriman, melihat riwayat transaksi, serta mengatur alamat pengiriman Anda dengan mudah.', 'jendela-ternak' ); ?>
        </p>
    </div>

    <!-- Shopee-style Horizontal Order Status Counter -->
    <div class="jt-dashboard-status-section">
        <h3 class="jt-section-title">
            <span><i class="fa-solid fa-box" style="margin-right: 6px;"></i><?php esc_html_e( 'Status Pesanan Saya', 'jendela-ternak' ); ?></span>
            <a href="<?php echo $orders_url; ?>" class="jt-title-link"><?php esc_html_e( 'Lihat Semua Riwayat ❯', 'jendela-ternak' ); ?></a>
        </h3>
        
        <div class="jt-dashboard-status-ribbon">
            <!-- Belum Bayar -->
            <a href="<?php echo add_query_arg( 'order_status', 'pending', $orders_url ); ?>" class="jt-status-card">
                <div class="jt-status-icon-wrapper">
                    <span class="jt-status-icon"><i class="fa-solid fa-credit-card"></i></span>
                    <?php if ( $count_pending > 0 ) : ?>
                        <span class="jt-status-badge"><?php echo esc_html( $count_pending ); ?></span>
                    <?php endif; ?>
                </div>
                <div class="jt-status-label"><?php esc_html_e( 'Belum Bayar', 'jendela-ternak' ); ?></div>
            </a>

            <!-- Dikemas -->
            <a href="<?php echo add_query_arg( 'order_status', 'processing', $orders_url ); ?>" class="jt-status-card">
                <div class="jt-status-icon-wrapper">
                    <span class="jt-status-icon"><i class="fa-solid fa-box"></i></span>
                    <?php if ( $count_packing > 0 ) : ?>
                        <span class="jt-status-badge"><?php echo esc_html( $count_packing ); ?></span>
                    <?php endif; ?>
                </div>
                <div class="jt-status-label"><?php esc_html_e( 'Dikemas', 'jendela-ternak' ); ?></div>
            </a>

            <!-- Dikirim -->
            <a href="<?php echo add_query_arg( 'order_status', 'shipping', $orders_url ); ?>" class="jt-status-card">
                <div class="jt-status-icon-wrapper">
                    <span class="jt-status-icon"><i class="fa-solid fa-truck"></i></span>
                    <?php if ( $count_shipping > 0 ) : ?>
                        <span class="jt-status-badge"><?php echo esc_html( $count_shipping ); ?></span>
                    <?php endif; ?>
                </div>
                <div class="jt-status-label"><?php esc_html_e( 'Dikirim', 'jendela-ternak' ); ?></div>
            </a>

            <!-- Selesai -->
            <a href="<?php echo add_query_arg( 'order_status', 'completed', $orders_url ); ?>" class="jt-status-card">
                <div class="jt-status-icon-wrapper">
                    <span class="jt-status-icon"><i class="fa-solid fa-star"></i></span>
                    <?php if ( $count_completed > 0 ) : ?>
                        <span class="jt-status-badge"><?php echo esc_html( $count_completed ); ?></span>
                    <?php endif; ?>
                </div>
                <div class="jt-status-label"><?php esc_html_e( 'Selesai', 'jendela-ternak' ); ?></div>
            </a>
        </div>
    </div>

    <!-- Quick Navigation Cards Grid -->
    <div class="jt-dashboard-quick-nav">
        <h3 class="jt-section-title"><i class="fa-solid fa-toolbox" style="margin-right: 6px;"></i><?php esc_html_e( 'Pintasan Menu', 'jendela-ternak' ); ?></h3>
        
        <div class="jt-quick-grid">
            <a href="<?php echo $orders_url; ?>" class="jt-quick-card">
                <div class="jt-quick-emoji"><i class="fa-solid fa-clipboard-list"></i></div>
                <div class="jt-quick-info">
                    <h4><?php esc_html_e( 'Pesanan Saya', 'jendela-ternak' ); ?></h4>
                    <p><?php esc_html_e( 'Cek riwayat pembelanjaan, lacak resi, & cetak invoice.', 'jendela-ternak' ); ?></p>
                </div>
            </a>
            
            <a href="<?php echo $address_url; ?>" class="jt-quick-card">
                <div class="jt-quick-emoji"><i class="fa-solid fa-location-dot"></i></div>
                <div class="jt-quick-info">
                    <h4><?php esc_html_e( 'Alamat Pengiriman', 'jendela-ternak' ); ?></h4>
                    <p><?php esc_html_e( 'Atur alamat utama pengiriman hewan dan pakan ternak.', 'jendela-ternak' ); ?></p>
                </div>
            </a>
            
            <a href="<?php echo $account_url; ?>" class="jt-quick-card">
                <div class="jt-quick-emoji"><i class="fa-solid fa-user-gear"></i></div>
                <div class="jt-quick-info">
                    <h4><?php esc_html_e( 'Detail Akun', 'jendela-ternak' ); ?></h4>
                    <p><?php esc_html_e( 'Ubah nama tampilan, email login, dan perbarui sandi.', 'jendela-ternak' ); ?></p>
                </div>
            </a>
        </div>
    </div>
</div>
