<?php
/**
 * Plugin Name: Disable Local SSL Verification
 * Description: Disables SSL verification for HTTP requests in local development environment.
 * Author: Antigravity
 */

if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    add_filter( 'https_ssl_verify', '__return_false' );
    add_filter( 'https_local_ssl_verify', '__return_false' );
}
