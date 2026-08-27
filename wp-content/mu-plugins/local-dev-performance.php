<?php
/**
 * Plugin Name: Local Dev Performance
 * Description: Skips heavy plugins on the PHP built-in server to keep local load fast.
 */

if ( ! function_exists( 'jendelaternak_is_local_dev' ) ) {
	function jendelaternak_is_local_dev() {
		if ( php_sapi_name() === 'cli-server' ) {
			return true;
		}

		$host = $_SERVER['HTTP_HOST'] ?? '';
		if ( $host === '' ) {
			return false;
		}

		$host = strtolower( preg_replace( '/:\d+$/', '', $host ) );

		return in_array( $host, array( '127.0.0.1', 'localhost', '::1' ), true );
	}
}

if ( jendelaternak_is_local_dev() ) {
	add_filter(
		'option_active_plugins',
		static function ( $plugins ) {
			if ( ! is_array( $plugins ) ) {
				return $plugins;
			}

			return array_values(
				array_filter(
					$plugins,
					static function ( $plugin ) {
						return $plugin !== 'wordfence/wordfence.php';
					}
				)
			);
		}
	);

	if ( ! defined( 'DISABLE_WP_CRON' ) ) {
		define( 'DISABLE_WP_CRON', true );
	}
}
