<?php
/**
 * inc/customizer.php
 *
 * The Customizer is intentionally left minimal.
 * ALL theme settings (colors, WhatsApp, logo, sliders, banners, fonts)
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
    $primary             = jt_get_setting( 'color_primary',           '#0B5E34' );
    $secondary           = jt_get_setting( 'color_secondary',         '#4CAF50' );
    $accent              = jt_get_setting( 'color_accent',            '#D4B106' );
    $promo_bg            = jt_get_setting( 'color_promo_bg',          '#C8D400' );
    $header_bg           = jt_get_setting( 'header_bg_color',         $primary );
    $font_heading        = jt_get_setting( 'font_heading',            'Inter' );
    $font_body           = jt_get_setting( 'font_body',               'Inter' );
    $ann_bg              = jt_get_setting( 'announcement_bg_color',   '#D4B106' );
    $ann_txt             = jt_get_setting( 'announcement_txt_color',  '#000000' );
    ?>
    <style id="jt-theme-css">
        :root {
            --color-primary:        <?php echo esc_attr( $primary );   ?>;
            --color-secondary:      <?php echo esc_attr( $secondary ); ?>;
            --color-accent:         <?php echo esc_attr( $accent );    ?>;
            --color-promo-bg:       <?php echo esc_attr( $promo_bg );  ?>;
            --color-header-bg:      <?php echo esc_attr( $header_bg ); ?>;
            --gradient-hero:        linear-gradient(135deg, <?php echo esc_attr( $secondary ); ?> 0%, <?php echo esc_attr( $promo_bg ); ?> 100%);
            --gradient-header:      linear-gradient(180deg, <?php echo esc_attr( $primary ); ?> 0%, <?php echo esc_attr( jt_adjustBrightness( $primary, 15 ) ); ?> 100%);
            --font-heading:         '<?php echo esc_attr( $font_heading ); ?>', sans-serif;
            --font-body:            '<?php echo esc_attr( $font_body ); ?>', sans-serif;
            --announcement-bg:      <?php echo esc_attr( $ann_bg );  ?>;
            --announcement-color:   <?php echo esc_attr( $ann_txt ); ?>;
        }

        /* Apply fonts globally */
        body {
            font-family: var(--font-body);
        }
        h1, h2, h3, h4, h5, h6,
        .jt-section-title,
        .jt-site-title,
        .jt-product-card__name,
        .jt-flash-sale__label span {
            font-family: var(--font-heading);
        }

        /* Apply header background color */
        .jt-header {
            background: var(--gradient-header);
        }

        /* Announcement bar */
        .jt-announcement-bar {
            background: var(--announcement-bg);
            color: var(--announcement-color);
        }

        /* Sticky Header settings */
        <?php if ( jt_get_setting( 'toggle_sticky_header', '0' ) === '1' ) : ?>
        #jt-cart-wrapper {
            display: contents;
        }
        .jt-header {
            position: sticky !important;
            top: 0 !important;
            z-index: 100 !important;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.12) !important;
        }
        <?php else : ?>
        .jt-header {
            position: relative !important;
        }
        <?php endif; ?>
    </style>
    <?php
}

/**
 * Load Google Fonts for the selected heading and body fonts.
 * Runs in wp_head with priority 4 (before CSS output at priority 5).
 */
add_action( 'wp_head', 'jt_load_google_fonts', 4 );
function jt_load_google_fonts() {
    $font_heading = jt_get_setting( 'font_heading', 'Inter' );
    $font_body    = jt_get_setting( 'font_body', 'Inter' );

    $fonts_to_load = array_unique( [ $font_heading, $font_body ] );
    $font_families = array();
    foreach ( $fonts_to_load as $font ) {
        $font_families[] = str_replace( ' ', '+', $font ) . ':ital,wght@0,400;0,600;0,700;0,800;1,400';
    }

    $google_fonts_url = 'https://fonts.googleapis.com/css2?family=' . implode( '&family=', $font_families ) . '&display=swap';
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?php echo esc_url( $google_fonts_url ); ?>">
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
