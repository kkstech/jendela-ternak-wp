<?php
/**
 * Show error messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/error.php.
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
	<div class="jt-toast jt-toast--error" <?php echo wc_get_notice_data_attr( $notice ); ?> role="alert">
		<div class="jt-toast__icon">
			<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" width="20" height="20">
				<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
			</svg>
		</div>
		<div class="jt-toast__content">
			<?php echo wc_kses_notice( $notice['notice'] ); ?>
		</div>
		<button type="button" class="jt-toast__close" aria-label="<?php esc_attr_e( 'Tutup', 'jendela-ternak' ); ?>">&times;</button>
	</div>
<?php endforeach; ?>
