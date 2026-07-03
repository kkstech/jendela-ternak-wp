<?php
/**
 * inc/elementor-support.php
 * Semua integrasi Elementor Free terisolasi di sini.
 * Hanya aktif jika plugin Elementor terpasang.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

// Inisialisasi setelah Elementor dimuat, atau langsung jika sudah dimuat
if ( did_action( 'elementor/loaded' ) ) {
    jt_elementor_init();
} else {
    add_action( 'elementor/loaded', 'jt_elementor_init' );
}

/**
 * Inisialisasi semua hook Elementor setelah plugin dimuat.
 */
function jt_elementor_init() {

    // 1. Register Elementor Theme Locations
    add_action( 'elementor/theme/register_locations', 'jt_register_elementor_locations' );

    // 2. Load CSS kompatibilitas hanya di frontend ketika Elementor aktif
    add_action( 'wp_enqueue_scripts', 'jt_enqueue_elementor_compat_css', 20 );

    // 3. Tambahkan body class untuk halaman Elementor
    add_filter( 'body_class', 'jt_elementor_body_class' );

    // 4. Nonaktifkan default colors/fonts Elementor agar pakai brand Jendela Ternak
    add_action( 'elementor/init', 'jt_disable_elementor_defaults' );
}

/**
 * Daftarkan lokasi tema untuk Elementor.
 *
 * Header & footer TIDAK didaftarkan — tetap menggunakan header/footer tema Jendela Ternak.
 * Hanya 'single' (halaman/post biasa) dan 'archive' yang bisa dikelola Elementor.
 *
 * @param object $elementor_theme_manager
 */
function jt_register_elementor_locations( $elementor_theme_manager ) {
    $elementor_theme_manager->register_location( 'single' );
    $elementor_theme_manager->register_location( 'archive' );
}

/**
 * Enqueue CSS kompatibilitas Elementor — hanya di frontend.
 */
function jt_enqueue_elementor_compat_css() {
    wp_enqueue_style(
        'jt-elementor-compat',
        JT_THEME_URI . '/assets/css/elementor-compat.css',
        [ 'jt-style' ],
        JT_THEME_VERSION
    );
}

/**
 * Tambahkan body class 'jt-elementor-page' pada halaman yang dibangun dengan Elementor.
 *
 * @param array $classes
 * @return array
 */
function jt_elementor_body_class( array $classes ): array {
    if (
        is_singular()
        && class_exists( '\Elementor\Plugin' )
        && isset( \Elementor\Plugin::$instance->db )
        && \Elementor\Plugin::$instance->db->is_built_with_elementor( get_the_ID() )
    ) {
        $classes[] = 'jt-elementor-page';
    }
    return $classes;
}

/**
 * Nonaktifkan default typography & color Elementor agar tema mengontrol tampilan dasar.
 * Ini mencegah Elementor menimpa CSS variables dan brand colors Jendela Ternak.
 */
function jt_disable_elementor_defaults() {
    // Hanya set jika belum pernah dikonfigurasi (tidak menimpa pilihan user)
    if ( ! get_option( 'elementor_disable_color_schemes' ) ) {
        update_option( 'elementor_disable_color_schemes', 'yes' );
    }
    if ( ! get_option( 'elementor_disable_typography_schemes' ) ) {
        update_option( 'elementor_disable_typography_schemes', 'yes' );
    }
}
