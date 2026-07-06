<?php
/**
 * Show info messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/notice.php.
 *
 * @package WooCommerce\Templates
 * @version 10.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! $notices ) {
	return;
}
?>

<?php foreach ( $notices as $notice ) : ?>
	<div class="jt-toast jt-toast--info" <?php echo wc_get_notice_data_attr( $notice ); ?> role="alert">
		<div class="jt-toast__icon">
			<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" width="20" height="20">
				<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 11.517 1.282l-.04.02-.041.02a.75.75 0 01-.517-1.282l.04-.02zM12 9a.75.75 0 100-1.5.75.75 0 000 1.5zm0 11.25a9 9 0 100-18 9 9 0 000 18z" />
			</svg>
		</div>
		<div class="jt-toast__content">
			<?php echo wc_kses_notice( $notice['notice'] ); ?>
		</div>
		<button type="button" class="jt-toast__close" aria-label="<?php esc_attr_e( 'Tutup', 'jendela-ternak' ); ?>">&times;</button>
	</div>
<?php endforeach; ?>
