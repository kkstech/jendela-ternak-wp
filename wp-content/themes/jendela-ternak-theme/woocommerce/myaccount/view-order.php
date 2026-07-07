<?php
/**
 * My Account - View Order
 *
 * Customized Shopee-style order details view template with timeline updates and clean styling.
 *
 * @package JendelaTernakMalang
 * @version 10.6.0
 */

defined( 'ABSPATH' ) || exit;

$order_id       = $order->get_id();
$status         = $order->get_status();
$status_name    = wc_get_order_status_name( $status );
$date           = wc_format_datetime( $order->get_date_created() );
$notes          = $order->get_customer_order_notes();

// Back link URL
$orders_url = wc_get_endpoint_url( 'orders' );
?>

<div class="jt-view-order-wrapper font-sans pb-10">
    <!-- Header Page Navigation -->
    <div class="flex items-center gap-3 mb-6">
        <a href="<?php echo esc_url( $orders_url ); ?>" class="text-gray-500 hover:text-green-800 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <h2 class="text-xl font-bold text-gray-800 m-0"><?php esc_html_e( 'Detail Pesanan', 'jendela-ternak' ); ?></h2>
    </div>

    <!-- Status Card Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <div class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-1">ID Pesanan</div>
                <div class="text-lg font-extrabold text-green-900">#<?php echo esc_html( $order->get_order_number() ); ?></div>
                <div class="text-xs text-gray-500 mt-1">Dibuat pada <?php echo esc_html( $date ); ?></div>
            </div>
            
            <div class="text-right">
                <div class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-1">Status Saat Ini</div>
                <span class="inline-block px-3 py-1.5 rounded-full text-xs font-bold <?php echo $status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                    <?php echo esc_html( $status_name ); ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Timeline Status / Order Updates -->
    <?php if ( $notes ) : ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="text-sm font-bold text-green-900 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <?php esc_html_e( 'Catatan & Pembaruan Pesanan', 'jendela-ternak' ); ?>
            </h3>
            
            <div class="relative border-l-2 border-green-50 pl-6 ml-3 space-y-6">
                <?php foreach ( $notes as $note ) : 
                    $note_date = date_i18n( esc_html__( 'j M Y, H:i', 'jendela-ternak' ), strtotime( $note->comment_date ) );
                ?>
                    <div class="relative">
                        <!-- Timeline bullet marker -->
                        <span class="absolute -left-[31px] top-1 bg-white border-2 border-green-600 rounded-full w-4 h-4 z-10 flex items-center justify-center">
                            <span class="bg-green-600 rounded-full w-1.5 h-1.5"></span>
                        </span>
                        
                        <div class="text-xs text-gray-400 font-semibold mb-1"><?php echo esc_html( $note_date ); ?></div>
                        <div class="text-sm text-gray-700 leading-relaxed font-medium bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <?php echo wp_kses_post( wpautop( wptexturize( $note->comment_content ) ) ); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main WooCommerce Order Details Card -->
    <div class="jt-order-details-card-outer bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <?php do_action( 'woocommerce_view_order', $order_id ); ?>
    </div>
</div>
