<?php
/**
 * Jendela Ternak Malang — functions.php
 * Loads all modular inc/ files.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

// Define theme constants
define( 'JT_THEME_VERSION', '1.0.0' );
define( 'JT_THEME_DIR', get_template_directory() );
define( 'JT_THEME_URI', get_template_directory_uri() );

// Load modular inc files
require_once JT_THEME_DIR . '/inc/theme-setup.php';
require_once JT_THEME_DIR . '/inc/enqueue-assets.php';
require_once JT_THEME_DIR . '/inc/woocommerce-hooks.php';
require_once JT_THEME_DIR . '/inc/saved-addresses.php';
require_once JT_THEME_DIR . '/inc/customizer.php';
require_once JT_THEME_DIR . '/inc/admin-options.php';
require_once JT_THEME_DIR . '/inc/elementor-support.php';
