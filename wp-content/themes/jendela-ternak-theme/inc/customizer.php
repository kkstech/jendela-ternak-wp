<?php
/**
 * inc/customizer.php
 *
 * The Customizer is intentionally left minimal.
 * ALL theme settings (colors, WhatsApp, logo, sliders, banners)
 * are managed exclusively via the custom "Pengaturan Tema" admin panel.
 * See: admin-options.php → jt_get_setting()
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output CSS custom properties from jt_theme_settings into <head>.
 * This is the only CSS-injection point — driven by jt_get_setting().
 */
add_action( 'wp_head', 'jt_customizer_css_output', 5 );
function jt_customizer_css_output() {
    $primary   = jt_get_setting( 'color_primary',   '#0B5E34' );
    $secondary = jt_get_setting( 'color_secondary', '#4CAF50' );
    $accent    = jt_get_setting( 'color_accent',    '#D4B106' );
    $promo_bg  = jt_get_setting( 'color_promo_bg',  '#C8D400' );
    ?>
    <style id="jt-theme-css">
        :root {
            --color-primary:    <?php echo esc_attr( $primary ); ?>;
            --color-secondary:  <?php echo esc_attr( $secondary ); ?>;
            --color-accent:     <?php echo esc_attr( $accent ); ?>;
            --color-promo-bg:   <?php echo esc_attr( $promo_bg ); ?>;
            --gradient-hero:    linear-gradient(135deg, <?php echo esc_attr( $secondary ); ?> 0%, <?php echo esc_attr( $promo_bg ); ?> 100%);
            --gradient-header:  linear-gradient(180deg, <?php echo esc_attr( $primary ); ?> 0%, <?php echo esc_attr( jt_adjustBrightness( $primary, 15 ) ); ?> 100%);
        }
    </style>
    <?php
}

/**
 * Slightly adjust hex color brightness.
 */
function jt_adjustBrightness( string $hex, int $steps ): string {
    $hex = ltrim( $hex, '#' );
    $r   = max( 0, min( 255, hexdec( substr( $hex, 0, 2 ) ) + $steps ) );
    $g   = max( 0, min( 255, hexdec( substr( $hex, 2, 2 ) ) + $steps ) );
    $b   = max( 0, min( 255, hexdec( substr( $hex, 4, 2 ) ) + $steps ) );
    return sprintf( '#%02x%02x%02x', $r, $g, $b );
}
