<?php
/**
 * woocommerce/checkout/thankyou.php
 * Customized checkout Thank You (Order Received) page template.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

if ( ! $order ) {
    ?>
    <div class="jt-thankyou-card text-center" style="padding:40px 20px;">
        <p style="font-size:16px;color:var(--color-text-muted);"><?php esc_html_e( 'Terima kasih. Pesanan Anda telah diterima.', 'jendela-ternak' ); ?></p>
    </div>
    <?php
    return;
}

$order_id       = $order->get_id();
$order_number   = $order->get_order_number();
$status         = $order->get_status();
$date           = wc_format_datetime( $order->get_date_created() );
$total          = $order->get_formatted_order_total();
$payment_method = $order->get_payment_method_title();
$wa_number      = jt_get_setting( 'whatsapp_number', '6281234567890' );
$wa_msg         = urlencode( sprintf( 'Halo CS Jendela Ternak, saya ingin mengonfirmasi pesanan #%s. Status saat ini: %s.', $order_number, $status ) );
$wa_url         = 'https://wa.me/' . esc_attr( $wa_number ) . '?text=' . $wa_msg;
?>

<div class="jt-thankyou-layout" id="jt-thankyou-root">

    <!-- Branded Banner Message -->
    <div class="jt-thankyou-card" style="text-align:center;padding:48px 20px;">
        <span style="font-size:48px;display:block;margin-bottom:12px;color:var(--color-primary);" role="img" aria-label="Celebration"><i class="fa-solid fa-circle-check"></i></span>
        <h2 style="font-size:24px;font-weight:800;color:var(--color-primary);margin-bottom:8px;">
            <?php esc_html_e( 'Pesanan Diterima!', 'jendela-ternak' ); ?>
        </h2>
        <p style="color:var(--color-text-muted);font-size:14px;margin-bottom:24px;">
            <?php esc_html_e( 'Terima kasih telah berbelanja di Jendela Ternak Malang.', 'jendela-ternak' ); ?>
        </p>

        <!-- WhatsApp CS Button -->
        <a 
            id="jt-wa-confirm-btn"
            href="<?php echo esc_url( $wa_url ); ?>" 
            target="_blank" 
            rel="noopener noreferrer" 
            class="jt-btn" 
            style="background-color:#25D366;color:#fff;border-color:#25D366;padding:12px 28px;"
        >
            <svg style="width:18px;height:18px;margin-right:8px;display:inline-block;vertical-align:middle;" viewBox="0 0 32 32" fill="currentColor">
                <path d="M16 0C7.163 0 0 7.163 0 16c0 2.82.737 5.47 2.028 7.773L0 32l8.467-2.018A15.943 15.943 0 0016 32c8.837 0 16-7.163 16-16S24.837 0 16 0zm8.27 22.27c-.34.96-2.01 1.84-2.76 1.96-.75.12-1.68.17-2.71-.17-1.03-.34-2.35-.8-4.03-1.54-3.39-1.5-5.59-4.89-5.76-5.12-.17-.23-1.38-1.84-1.38-3.51s.87-2.49 1.18-2.83c.31-.34.67-.43.9-.43s.45.004.64.012c.2.008.47-.076.74.57.28.66.95 2.31 1.03 2.48.08.17.13.37.03.59-.1.22-.15.35-.29.54-.14.19-.3.43-.43.57-.14.14-.28.3-.12.59.16.29.72 1.19 1.55 1.93 1.07.95 1.97 1.25 2.26 1.39.29.14.46.12.63-.07.17-.19.73-.85.93-1.14.2-.29.4-.24.67-.14.27.1 1.72.81 2.01.96.29.15.48.22.55.34.07.12.07.7-.27 1.66z"/>
            </svg>
            <span style="vertical-align:middle;"><?php esc_html_e( 'Konfirmasi Pesanan via WhatsApp', 'jendela-ternak' ); ?></span>
        </a>
    </div>

    <!-- Branded Order Info Summary Card -->
    <div class="jt-thankyou-card">
        <h2><i class="fa-solid fa-file-invoice" style="margin-right: 8px;"></i><?php esc_html_e( 'Ringkasan Pesanan', 'jendela-ternak' ); ?></h2>
        
        <div class="jt-thankyou-meta">
            <div class="jt-thankyou-meta-item">
                <span><?php esc_html_e( 'Nomor Pesanan', 'jendela-ternak' ); ?></span>
                <strong><?php echo esc_html( $order_number ); ?></strong>
            </div>
            <div class="jt-thankyou-meta-item">
                <span><?php esc_html_e( 'Tanggal', 'jendela-ternak' ); ?></span>
                <strong><?php echo esc_html( $date ); ?></strong>
            </div>
            <div class="jt-thankyou-meta-item">
                <span><?php esc_html_e( 'Total', 'jendela-ternak' ); ?></span>
                <strong><?php echo wp_kses_post( $total ); ?></strong>
            </div>
            <div class="jt-thankyou-meta-item">
                <span><?php esc_html_e( 'Metode Pembayaran', 'jendela-ternak' ); ?></span>
                <strong><?php echo esc_html( $payment_method ); ?></strong>
            </div>
        </div>

        <?php if ( $order->get_payment_method() === 'cod' ) : ?>
            <p style="font-size:13px;color:var(--color-text-muted);line-height:1.6;margin-top:12px;">
                <i class="fa-solid fa-lightbulb" style="margin-right: 6px;color:var(--color-accent);"></i> <em><?php esc_html_e( 'Metode pembayaran di tempat (COD). Harap persiapkan uang tunai pas saat kurir Biteship tiba untuk mengantar barang.', 'jendela-ternak' ); ?></em>
            </p>
        <?php endif; ?>
    </div>

    <!-- Biteship Shipping details -->
    <?php
    $biteship_track = get_post_meta( $order_id, '_biteship_waybill_id', true ) ?: get_post_meta( $order_id, '_biteship_tracking_code', true );
    $courier_name   = get_post_meta( $order_id, '_biteship_courier_name', true ) ?: $order->get_shipping_method();
    ?>
    <div class="jt-thankyou-card">
        <h2><i class="fa-solid fa-truck" style="margin-right: 8px;"></i><?php esc_html_e( 'Detail Pengiriman', 'jendela-ternak' ); ?></h2>
        <div style="font-size:13px;line-height:1.7;">
            <p style="margin-bottom:8px;">
                <strong><?php esc_html_e( 'Jasa Pengiriman:', 'jendela-ternak' ); ?></strong> <?php echo esc_html( $courier_name ); ?>
            </p>
            <p style="margin-bottom:8px;">
                <strong><?php esc_html_e( 'Nomor Resi (Biteship):', 'jendela-ternak' ); ?></strong> 
                <?php if ( $biteship_track ) : ?>
                    <span style="font-family:monospace;font-weight:700;color:var(--color-primary);background:var(--color-light-bg);padding:2px 8px;border-radius:4px;"><?php echo esc_html( $biteship_track ); ?></span>
                <?php else : ?>
                    <span style="color:var(--color-text-muted);font-style:italic;"><?php esc_html_e( 'Nomor resi pengiriman Anda akan diperbarui oleh admin setelah barang diserahkan ke kurir.', 'jendela-ternak' ); ?></span>
                <?php endif; ?>
            </p>
            <p>
                <strong><?php esc_html_e( 'Alamat Pengiriman:', 'jendela-ternak' ); ?></strong><br>
                <?php echo wp_kses_post( $order->get_formatted_shipping_address() ?: $order->get_formatted_billing_address() ); ?>
            </p>
        </div>
    </div>

    <!-- Items detail list table -->
    <div class="jt-thankyou-card" style="overflow-x:auto;">
        <h2><i class="fa-solid fa-cart-shopping" style="margin-right: 8px;"></i><?php esc_html_e( 'Daftar Produk', 'jendela-ternak' ); ?></h2>
        <table class="jt-cart-table" style="width:100%;">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Nama Produk', 'jendela-ternak' ); ?></th>
                    <th><?php esc_html_e( 'Jumlah', 'jendela-ternak' ); ?></th>
                    <th><?php esc_html_e( 'Subtotal', 'jendela-ternak' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $order->get_items() as $item_id => $item ) :
                    $name    = $item->get_name();
                    $qty     = $item->get_quantity();
                    $subtotal = $order->get_formatted_line_subtotal( $item );
                ?>
                <tr>
                    <td>
                        <span style="font-weight:600;color:var(--color-text);"><?php echo esc_html( $name ); ?></span>
                    </td>
                    <td><?php echo esc_html( $qty ); ?></td>
                    <td style="font-weight:700;color:var(--color-primary);"><?php echo wp_kses_post( $subtotal ); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="2" style="text-align:right;font-weight:600;"><?php esc_html_e( 'Biaya Pengiriman:', 'jendela-ternak' ); ?></td>
                    <td style="font-weight:700;"><?php echo wp_kses_post( $order->get_shipping_to_display() ); ?></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:right;font-weight:600;color:var(--color-primary);"><?php esc_html_e( 'Total Akhir:', 'jendela-ternak' ); ?></td>
                    <td style="font-weight:800;color:var(--color-red);font-size:18px;"><?php echo wp_kses_post( $total ); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
