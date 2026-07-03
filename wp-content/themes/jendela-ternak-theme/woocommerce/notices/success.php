<?php
/**
 * Show messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/success.php.
 *
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! $notices ) {
	return;
}
?>

<?php foreach ( $notices as $notice ) : ?>
	<div class="jt-toast jt-toast--success" <?php echo wc_get_notice_data_attr( $notice ); ?> role="alert">
		<div class="jt-toast__icon">
			<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" width="20" height="20">
				<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
			</svg>
		</div>
		<div class="jt-toast__content">
			<?php echo wc_kses_notice( $notice['notice'] ); ?>
		</div>
		<button type="button" class="jt-toast__close" aria-label="<?php esc_attr_e( 'Tutup', 'jendela-ternak' ); ?>">&times;</button>
	</div>
<?php endforeach; ?>
