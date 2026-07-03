<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
define( 'DB_NAME', $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'jendelaternak_wp' );

/** Database username */
define( 'DB_USER', $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'root' );

/** Database password */
define( 'DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '' );

/** Database hostname */
define( 'DB_HOST', $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'X.|Z9A>{G[7M37{0YaZT}&*}>u~pcouguZ?#S P4Mo}4j%puYo%wntGN30.VmD-[');
define('SECURE_AUTH_KEY',  '%7;#,KCn*<igN+|T{+8`-]^}sH>hf2WIm4fa~lUz~:0z&<0u$Ap*&Kzqxi?>oe=A');
define('LOGGED_IN_KEY',    'DaG|x[=~a!)l$hr}%Aw{CJ*sfl).Maiouj#r7|DLLwFQ<OJe@xkQ*Kx48Rj)rv#V');
define('NONCE_KEY',        '^R9Y;y:(U1AI X~gua|(GZUX#EHl/R$:?@;E6*H_N|FB.RK/C+<6`1G(&^77jXI8');
define('AUTH_SALT',        'LRt8^H^a<vIYxH[AiWn!-b<:^0 k8bgqo>E%ermuRWY[K?}Y|{rPG,?hZ3Q+}T*.');
define('SECURE_AUTH_SALT', '<S:jG+Nh<PH?{XJL}Vvr*dfaL-@ /z&VFq2~XYx-Fk^OG5Iu-@*~2s+6@u7 8[Fk');
define('LOGGED_IN_SALT',   '%gA=K8~A8St~>|r8jHOZ8#m_e{2CAoJ-8zU1?eO1U/+I;|0m,X*12giG&g_1:P#5');
define('NONCE_SALT',       '(<#*>:^IbA7G0xu)4 /{AY./I}#dJ:(Qm|?;R*KB-k$WFy4m1||ME13-*-/v>?5-');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */

$http_host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
define( 'WP_HOME', $_ENV['WP_HOME'] ?? getenv('WP_HOME') ?: $protocol . $http_host );
define( 'WP_SITEURL', $_ENV['WP_SITEURL'] ?? getenv('WP_SITEURL') ?: $protocol . $http_host );



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
