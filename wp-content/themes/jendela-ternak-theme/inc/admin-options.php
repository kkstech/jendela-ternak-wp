<?php
/**
 * inc/admin-options.php
 * Custom theme settings dashboard panel for Jendela Ternak Malang.
 * This is the SINGLE SOURCE OF TRUTH for all theme settings.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

/**
 * Helper to normalize local development URLs (127.0.0.1:8000, localhost, etc)
 * to the current live site URL dynamically. Prevents Mixed Content and 404 image errors.
 */
function jt_normalize_theme_setting_url( $value ) {
    if ( ! is_string( $value ) || empty( $value ) ) {
        return $value;
    }

    $local_patterns = array(
        'http://127.0.0.1:8000',
        'http://127.0.0.1',
        'http://localhost:8000',
        'http://localhost',
    );

    // Get the current live site URL
    $current_home_url = rtrim( home_url(), '/' );

    // Replace local dev origin with current live site origin
    foreach ( $local_patterns as $pattern ) {
        if ( strpos( $value, $pattern ) !== false ) {
            $value = str_replace( $pattern, $current_home_url, $value );
            break;
        }
    }

    // Upgrade http → https when on HTTPS and the URL points to the same host
    if ( is_ssl() ) {
        $host = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '';
        if ( $host && strpos( $value, 'http://' . $host ) === 0 ) {
            $value = str_replace( 'http://' . $host, 'https://' . $host, $value );
        }
    }

    return $value;
}

/**
 * Helper to recursively normalize URLs in array settings (e.g. slides array).
 */
function jt_normalize_theme_setting_array_urls( $array ) {
    if ( ! is_array( $array ) ) {
        return $array;
    }
    foreach ( $array as $key => $val ) {
        if ( is_string( $val ) ) {
            $array[ $key ] = jt_normalize_theme_setting_url( $val );
        } elseif ( is_array( $val ) ) {
            $array[ $key ] = jt_normalize_theme_setting_array_urls( $val );
        }
    }
    return $array;
}

/**
 * Central helper — get one theme setting by key.
 * All theme files must use this instead of get_theme_mod().
 *
 * @param  string $key     Setting key inside jt_theme_settings.
 * @param  mixed  $default Default value if key not set.
 * @return mixed
 */
function jt_get_setting( string $key, $default = null ) {
    static $cache = null;
    if ( null === $cache ) {
        $cache = get_option( 'jt_theme_settings', array() );
    }
    $value = isset( $cache[ $key ] ) && $cache[ $key ] !== '' ? $cache[ $key ] : $default;
    
    if ( is_string( $value ) && ! empty( $value ) ) {
        $value = jt_normalize_theme_setting_url( $value );
    } elseif ( is_array( $value ) ) {
        $value = jt_normalize_theme_setting_array_urls( $value );
    }
    
    return $value;
}

// Register admin menu
add_action( 'admin_menu', 'jt_add_theme_options_menu' );
function jt_add_theme_options_menu() {
    add_menu_page(
        __( 'Pengaturan Tema Jendela Ternak', 'jendela-ternak' ),
        __( 'Pengaturan Tema', 'jendela-ternak' ),
        'manage_options',
        'jt-theme-settings',
        'jt_render_theme_settings_page',
        'dashicons-admin-appearance',
        59
    );
}

// Load WordPress Media, Tailwind, and Alpine on the settings page only
add_action( 'admin_enqueue_scripts', 'jt_enqueue_admin_theme_settings_assets' );
function jt_enqueue_admin_theme_settings_assets( $hook ) {
    if ( 'toplevel_page_jt-theme-settings' === $hook ) {
        wp_enqueue_media();
        wp_enqueue_script( 'tailwind-cdn', 'https://cdn.tailwindcss.com', [], null, false );
        wp_enqueue_script(
            'alpinejs-admin',
            'https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js',
            [],
            '3.14.1',
            true
        );
        // Load Font Awesome 6 for theme panel icons
        wp_enqueue_style(
            'font-awesome-6-admin',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css',
            [],
            '6.6.0'
        );
    }
}

// Render settings page
function jt_render_theme_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // ─── SAVE LOGIC ────────────────────────────────────────────────────────────
    if ( isset( $_POST['jt_save_settings'] ) && check_admin_referer( 'jt_settings_nonce_action', 'jt_settings_nonce' ) ) {

        // Handle dynamic slides JSON
        $raw_slides = array();
        if ( ! empty( $_POST['slides_json'] ) ) {
            $decoded = json_decode( stripslashes( $_POST['slides_json'] ), true );
            if ( is_array( $decoded ) ) {
                foreach ( $decoded as $slide ) {
                    $raw_slides[] = array(
                        'img'      => esc_url_raw( $slide['img'] ?? '' ),
                        'title'    => sanitize_text_field( $slide['title'] ?? '' ),
                        'subtitle' => sanitize_text_field( $slide['subtitle'] ?? '' ),
                        'link'     => esc_url_raw( $slide['link'] ?? '' ),
                    );
                }
            }
        }

        // Handle footer custom links JSON
        $raw_footer_links = array();
        if ( ! empty( $_POST['footer_links_json'] ) ) {
            $decoded_links = json_decode( stripslashes( $_POST['footer_links_json'] ), true );
            if ( is_array( $decoded_links ) ) {
                foreach ( $decoded_links as $link_item ) {
                    $raw_footer_links[] = array(
                        'label' => sanitize_text_field( $link_item['label'] ?? '' ),
                        'url'   => esc_url_raw( $link_item['url'] ?? '' ),
                    );
                }
            }
        }

        $settings = array(
            // ── Tab 1: Umum & Warna ──────────────────────
            'color_primary'          => sanitize_hex_color( $_POST['color_primary'] ),
            'color_secondary'        => sanitize_hex_color( $_POST['color_secondary'] ),
            'color_accent'           => sanitize_hex_color( $_POST['color_accent'] ),
            'color_promo_bg'         => sanitize_hex_color( $_POST['color_promo_bg'] ),
            'toggle_flash_sale'      => isset( $_POST['toggle_flash_sale'] ) ? '1' : '0',
            'toggle_banners'         => isset( $_POST['toggle_banners'] ) ? '1' : '0',
            'toggle_announcement'    => isset( $_POST['toggle_announcement'] ) ? '1' : '0',

            // ── Tab 2: Logo & Identitas ──────────────────
            'logo_img'               => esc_url_raw( $_POST['logo_img'] ?? '' ),
            'logo_height'            => absint( $_POST['logo_height'] ?? 50 ),
            'logo_tagline_override'  => sanitize_text_field( $_POST['logo_tagline_override'] ?? '' ),

            // ── Tab 3: Header & Navigasi ─────────────────
            'toggle_sticky_header'   => isset( $_POST['toggle_sticky_header'] ) ? '1' : '0',
            'header_bg_color'        => sanitize_hex_color( $_POST['header_bg_color'] ),
            'announcement_text'      => sanitize_text_field( $_POST['announcement_text'] ?? '' ),
            'announcement_link'      => esc_url_raw( $_POST['announcement_link'] ?? '' ),
            'announcement_bg_color'  => sanitize_hex_color( $_POST['announcement_bg_color'] ),
            'announcement_txt_color' => sanitize_hex_color( $_POST['announcement_txt_color'] ),

            // ── Tab 4: Footer ────────────────────────────
            'footer_description'     => sanitize_textarea_field( $_POST['footer_description'] ?? '' ),
            'footer_copyright'       => sanitize_text_field( $_POST['footer_copyright'] ?? '' ),
            'footer_links_json'      => $raw_footer_links,

            // ── Tab 5: Beranda & Produk ──────────────────
            'section_title_bestseller'  => sanitize_text_field( $_POST['section_title_bestseller'] ?? '' ),
            'section_title_offers'      => sanitize_text_field( $_POST['section_title_offers'] ?? '' ),
            'section_title_categories'  => sanitize_text_field( $_POST['section_title_categories'] ?? '' ),
            'products_per_row'          => absint( $_POST['products_per_row'] ?? 4 ),
            'products_per_page'         => absint( $_POST['products_per_page'] ?? 12 ),
            'btn_add_to_cart_text'      => sanitize_text_field( $_POST['btn_add_to_cart_text'] ?? '' ),
            'btn_buy_now_text'          => sanitize_text_field( $_POST['btn_buy_now_text'] ?? '' ),
            'font_heading'              => sanitize_text_field( $_POST['font_heading'] ?? 'Inter' ),
            'font_body'                 => sanitize_text_field( $_POST['font_body'] ?? 'Inter' ),

            // ── Tab 6: Slider & Banner ───────────────────
            'slides'                 => $raw_slides,
            'banner1_img'            => esc_url_raw( $_POST['banner1_img'] ),
            'banner1_link'           => esc_url_raw( $_POST['banner1_link'] ),
            'banner2_img'            => esc_url_raw( $_POST['banner2_img'] ),
            'banner2_link'           => esc_url_raw( $_POST['banner2_link'] ),

            // ── Tab 7: Media Sosial & WA ─────────────────
            'whatsapp_number'        => sanitize_text_field( $_POST['whatsapp_number'] ),
            'whatsapp_message'       => sanitize_text_field( $_POST['whatsapp_message'] ),
            'flash_sale_end'         => sanitize_text_field( $_POST['flash_sale_end'] ),
            'social_instagram'       => esc_url_raw( $_POST['social_instagram'] ?? '' ),
            'social_tiktok'          => esc_url_raw( $_POST['social_tiktok'] ?? '' ),
            'social_shopee'          => esc_url_raw( $_POST['social_shopee'] ?? '' ),
            'social_facebook'        => esc_url_raw( $_POST['social_facebook'] ?? '' ),
            'social_tokopedia'       => esc_url_raw( $_POST['social_tokopedia'] ?? '' ),
            'social_lazada'          => esc_url_raw( $_POST['social_lazada'] ?? '' ),
        );

        update_option( 'jt_theme_settings', $settings );

        echo '<div class="notice notice-success is-dismissible" style="margin: 15px 15px 0 0;"><p><strong><i class="fa-solid fa-circle-check text-green-600 mr-1.5"></i> Pengaturan Tema Berhasil Disimpan!</strong></p></div>';
    }

    // ─── LOAD CURRENT OPTIONS ──────────────────────────────────────────────────
    $settings = get_option( 'jt_theme_settings', array() );

    // Tab 1: Umum & Warna
    $color_primary          = $settings['color_primary']          ?? '#0B5E34';
    $color_secondary        = $settings['color_secondary']        ?? '#4CAF50';
    $color_accent           = $settings['color_accent']           ?? '#D4B106';
    $color_promo_bg         = $settings['color_promo_bg']         ?? '#C8D400';
    $toggle_flash_sale      = $settings['toggle_flash_sale']      ?? '1';
    $toggle_banners         = $settings['toggle_banners']         ?? '1';
    $toggle_announcement    = $settings['toggle_announcement']    ?? '0';

    // Tab 2: Logo & Identitas
    $logo_img               = $settings['logo_img']               ?? '';
    $logo_height            = $settings['logo_height']            ?? '50';
    $logo_tagline_override  = $settings['logo_tagline_override']  ?? '';

    // Tab 3: Header & Navigasi
    $toggle_sticky_header   = $settings['toggle_sticky_header']   ?? '0';
    $header_bg_color        = $settings['header_bg_color']        ?? '#0B5E34';
    $announcement_text      = $settings['announcement_text']      ?? '';
    $announcement_link      = $settings['announcement_link']      ?? '';
    $announcement_bg_color  = $settings['announcement_bg_color']  ?? '#D4B106';
    $announcement_txt_color = $settings['announcement_txt_color'] ?? '#000000';

    // Tab 4: Footer
    $footer_description     = $settings['footer_description']     ?? 'Toko lengkap pakan ternak, obat hewan, dan alat peternakan terpercaya di Malang.';
    $footer_copyright       = $settings['footer_copyright']       ?? '';
    $footer_links_raw       = $settings['footer_links_json']      ?? array();
    if ( empty( $footer_links_raw ) ) {
        $footer_links_raw = array(
            array( 'label' => 'Tentang Kami', 'url' => '/tentang-kami/' ),
            array( 'label' => 'Blog', 'url' => '/blog/' ),
            array( 'label' => 'Kebijakan Privasi', 'url' => '/kebijakan-privasi/' ),
        );
    }
    $footer_links_json_escaped = esc_attr( json_encode( $footer_links_raw ) );

    // Tab 5: Beranda & Produk
    $section_title_bestseller  = $settings['section_title_bestseller']  ?? 'Produk Terlaris';
    $section_title_offers      = $settings['section_title_offers']      ?? 'Penawaran Terbaik';
    $section_title_categories  = $settings['section_title_categories']  ?? 'Kategori Pilihan';
    $products_per_row          = $settings['products_per_row']          ?? 4;
    $products_per_page         = $settings['products_per_page']         ?? 12;
    $btn_add_to_cart_text      = $settings['btn_add_to_cart_text']      ?? 'Masukkan Keranjang';
    $btn_buy_now_text          = $settings['btn_buy_now_text']          ?? 'Beli Sekarang';
    $font_heading              = $settings['font_heading']              ?? 'Inter';
    $font_body                 = $settings['font_body']                 ?? 'Inter';

    // Tab 6: Slider & Banner
    $saved_slides = $settings['slides'] ?? null;
    if ( ! $saved_slides ) {
        $saved_slides = array();
        for ( $i = 1; $i <= 3; $i++ ) {
            if ( ! empty( $settings["slide{$i}_title"] ) || ! empty( $settings["slide{$i}_img"] ) ) {
                $saved_slides[] = array(
                    'img'      => $settings["slide{$i}_img"] ?? '',
                    'title'    => $settings["slide{$i}_title"] ?? '',
                    'subtitle' => $settings["slide{$i}_subtitle"] ?? '',
                    'link'     => $settings["slide{$i}_link"] ?? '',
                );
            }
        }
    }
    if ( empty( $saved_slides ) ) {
        $saved_slides = array(
            array( 'img' => '', 'title' => 'Lengkapi Kebutuhan Peternakan Anda', 'subtitle' => 'Pakan ternak, obat hewan, dan alat peternakan terlengkap.', 'link' => '' ),
        );
    }
    $slides_json_escaped = esc_attr( json_encode( $saved_slides ) );
    $banner1_img  = $settings['banner1_img']  ?? '';
    $banner1_link = $settings['banner1_link'] ?? '';
    $banner2_img  = $settings['banner2_img']  ?? '';
    $banner2_link = $settings['banner2_link'] ?? '';

    // Tab 7: Media Sosial & WA
    $whatsapp_number  = $settings['whatsapp_number']  ?? '6281234567890';
    $whatsapp_message = $settings['whatsapp_message'] ?? 'Halo, saya ingin bertanya tentang produk Jendela Ternak Malang.';
    $flash_sale_end   = $settings['flash_sale_end']   ?? '';
    $social_instagram = $settings['social_instagram'] ?? '';
    $social_tiktok    = $settings['social_tiktok']    ?? '';
    $social_shopee    = $settings['social_shopee']    ?? '';
    $social_facebook  = $settings['social_facebook']  ?? '';
    $social_tokopedia = $settings['social_tokopedia'] ?? '';
    $social_lazada    = $settings['social_lazada']    ?? '';

    // Available Google Fonts
    $google_fonts = [
        'Inter', 'Roboto', 'Poppins', 'Nunito', 'Plus Jakarta Sans',
        'Outfit', 'Lato', 'Montserrat', 'Raleway', 'Open Sans',
        'DM Sans', 'Figtree', 'Noto Sans', 'Source Sans 3',
    ];
    ?>

    <div class="mr-6 my-6 font-sans" x-data="{ tab: 'general' }">

        <!-- ── DASHBOARD HEADER ─────────────────────────────────────── -->
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm mb-1 p-5 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 bg-[#0B5E34] rounded-xl flex items-center justify-center shadow-sm flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-extrabold text-gray-900 leading-tight">Pengaturan Tema Jendela Ternak</h1>
                    <p class="text-xs text-gray-400 mt-0.5">Kelola seluruh tampilan dan konfigurasi toko Anda dari satu panel.</p>
                </div>
            </div>
            <span class="bg-[#0B5E34]/10 text-[#0B5E34] text-xs font-black px-3 py-1.5 rounded-full">v1.2.0</span>
        </div>

        <!-- ── TAB NAVIGATION ──────────────────────────────────────── -->
        <div class="bg-white border-x border-b border-t-0 border-gray-200 rounded-b-none flex px-4 py-0 overflow-x-auto gap-1 shadow-sm">
            <?php
            $tabs = [
                'general'  => [ 'icon' => '<i class="fa-solid fa-palette mr-1.5"></i>', 'label' => 'Umum & Warna' ],
                'logo'     => [ 'icon' => '<i class="fa-solid fa-tags mr-1.5"></i>', 'label' => 'Logo & Identitas' ],
                'header'   => [ 'icon' => '<i class="fa-solid fa-window-maximize mr-1.5"></i>', 'label' => 'Header & Navigasi' ],
                'footer'   => [ 'icon' => '<i class="fa-solid fa-copyright mr-1.5"></i>', 'label' => 'Footer' ],
                'homepage' => [ 'icon' => '<i class="fa-solid fa-house mr-1.5"></i>', 'label' => 'Beranda & Produk' ],
                'slider'   => [ 'icon' => '<i class="fa-solid fa-images mr-1.5"></i>', 'label' => 'Slider & Banner' ],
                'social'   => [ 'icon' => '<i class="fa-solid fa-share-nodes mr-1.5"></i>', 'label' => 'Sosial & WhatsApp' ],
            ];
            foreach ( $tabs as $key => $tab ) :
            ?>
            <button type="button"
                @click="tab = '<?php echo $key; ?>'"
                :class="tab === '<?php echo $key; ?>'
                    ? 'border-[#0B5E34] text-[#0B5E34] bg-[#0B5E34]/5'
                    : 'border-transparent text-gray-400 hover:text-gray-600 hover:bg-gray-50'"
                class="px-4 py-3.5 border-b-2 font-semibold text-xs transition-all focus:outline-none whitespace-nowrap flex items-center gap-1.5">
                <?php echo $tab['icon']; ?> <?php echo $tab['label']; ?>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- ── FORM BODY ───────────────────────────────────────────── -->
        <form method="POST" action="" class="bg-white p-8 rounded-b-2xl shadow-sm border border-t-0 border-gray-200">
            <?php wp_nonce_field( 'jt_settings_nonce_action', 'jt_settings_nonce' ); ?>

            <!-- ═══════════════════════════════════════════════════════
                 TAB 1: UMUM & WARNA
            ════════════════════════════════════════════════════════ -->
            <div x-show="tab === 'general'" class="space-y-8">

                <!-- Skema Warna Brand -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-6">
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-palette text-[#0B5E34]"></i> Skema Warna Brand</h2>
                        <p class="text-xs text-gray-400 mt-1">Atur palet warna utama untuk header, tombol, bintang, dan badge promo.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Primary -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Hijau Tua (Primary)</label>
                            <div class="flex items-center gap-2">
                                <input type="color" value="<?php echo esc_attr( $color_primary ); ?>" class="w-10 h-10 border border-gray-200 rounded-lg cursor-pointer flex-shrink-0">
                                <input type="text" name="color_primary" value="<?php echo esc_attr( $color_primary ); ?>" class="border border-gray-200 rounded-lg px-3 py-2 text-xs font-mono text-gray-600 w-full focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                            </div>
                        </div>
                        <!-- Secondary -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Hijau Medium (Secondary)</label>
                            <div class="flex items-center gap-2">
                                <input type="color" value="<?php echo esc_attr( $color_secondary ); ?>" class="w-10 h-10 border border-gray-200 rounded-lg cursor-pointer flex-shrink-0">
                                <input type="text" name="color_secondary" value="<?php echo esc_attr( $color_secondary ); ?>" class="border border-gray-200 rounded-lg px-3 py-2 text-xs font-mono text-gray-600 w-full focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                            </div>
                        </div>
                        <!-- Accent -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Kuning Emas (Accent)</label>
                            <div class="flex items-center gap-2">
                                <input type="color" value="<?php echo esc_attr( $color_accent ); ?>" class="w-10 h-10 border border-gray-200 rounded-lg cursor-pointer flex-shrink-0">
                                <input type="text" name="color_accent" value="<?php echo esc_attr( $color_accent ); ?>" class="border border-gray-200 rounded-lg px-3 py-2 text-xs font-mono text-gray-600 w-full focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                            </div>
                        </div>
                        <!-- Promo Bg -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Kuning-Hijau Promo</label>
                            <div class="flex items-center gap-2">
                                <input type="color" value="<?php echo esc_attr( $color_promo_bg ); ?>" class="w-10 h-10 border border-gray-200 rounded-lg cursor-pointer flex-shrink-0">
                                <input type="text" name="color_promo_bg" value="<?php echo esc_attr( $color_promo_bg ); ?>" class="border border-gray-200 rounded-lg px-3 py-2 text-xs font-mono text-gray-600 w-full focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Toggle Fitur -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-6">
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-toggle-on text-[#0B5E34]"></i> Toggle Fitur Global</h2>
                        <p class="text-xs text-gray-400 mt-1">Aktifkan atau nonaktifkan fitur-fitur tertentu di seluruh situs.</p>
                    </div>
                    <div class="space-y-4">
                        <!-- Toggle Flash Sale -->
                        <label class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer">
                            <div>
                                <span class="font-semibold text-sm text-gray-800"><i class="fa-solid fa-bolt text-amber-500 mr-1"></i> Flash Sale & Countdown Timer</span>
                                <p class="text-xs text-gray-400 mt-0.5">Tampilkan seksi Flash Sale dengan countdown waktu di beranda.</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="toggle_flash_sale" value="0">
                                <input type="checkbox" name="toggle_flash_sale" value="1" <?php checked( $toggle_flash_sale, '1' ); ?> class="jt-toggle-check sr-only" id="tog-flash-sale">
                                <label for="tog-flash-sale" class="jt-toggle-pill cursor-pointer"></label>
                            </div>
                        </label>
                        <!-- Toggle Banners -->
                        <label class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer">
                            <div>
                                <span class="font-semibold text-sm text-gray-800"><i class="fa-solid fa-image text-gray-500 mr-1"></i> Banner Sisipan (Banner 1 & 2)</span>
                                <p class="text-xs text-gray-400 mt-0.5">Tampilkan banner promosi di antara seksi produk di beranda.</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="toggle_banners" value="0">
                                <input type="checkbox" name="toggle_banners" value="1" <?php checked( $toggle_banners, '1' ); ?> class="jt-toggle-check sr-only" id="tog-banners">
                                <label for="tog-banners" class="jt-toggle-pill cursor-pointer"></label>
                            </div>
                        </label>
                        <!-- Toggle Announcement Bar -->
                        <label class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer">
                            <div>
                                <span class="font-semibold text-sm text-gray-800"><i class="fa-solid fa-bullhorn text-gray-500 mr-1"></i> Announcement Bar (Baris Pengumuman)</span>
                                <p class="text-xs text-gray-400 mt-0.5">Tampilkan baris teks pengumuman di bagian atas header. Atur teks di tab Header.</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="toggle_announcement" value="0">
                                <input type="checkbox" name="toggle_announcement" value="1" <?php checked( $toggle_announcement, '1' ); ?> class="jt-toggle-check sr-only" id="tog-announcement">
                                <label for="tog-announcement" class="jt-toggle-pill cursor-pointer"></label>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════
                 TAB 2: LOGO & IDENTITAS
            ════════════════════════════════════════════════════════ -->
            <div x-show="tab === 'logo'" class="space-y-8" style="display:none;">
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-6">
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-tags text-[#0B5E34]"></i> Logo Toko</h2>
                        <p class="text-xs text-gray-400 mt-1">Upload logo toko yang akan muncul di header. Disarankan format PNG transparan, tinggi minimal 60px.</p>
                    </div>
                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        <!-- Logo Preview -->
                        <div class="w-full md:w-60 flex-shrink-0">
                            <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 bg-gray-50 flex flex-col items-center justify-center gap-3 min-h-[140px]">
                                <div id="jt-logo-preview" class="flex items-center justify-center w-full">
                                    <?php if ( $logo_img ) : ?>
                                        <img src="<?php echo esc_url( $logo_img ); ?>" alt="Logo" class="max-h-24 max-w-full object-contain" id="jt-logo-img-preview">
                                    <?php else : ?>
                                        <div id="jt-logo-img-preview" class="text-center">
                                            <div class="text-4xl mb-2 text-gray-300"><i class="fa-solid fa-store"></i></div>
                                            <p class="text-xs text-gray-400">Belum ada logo</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-2 text-center">Preview logo header</p>
                        </div>
                        <!-- Logo Controls -->
                        <div class="flex-1 space-y-5">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">URL Gambar Logo</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" name="logo_img" id="jt-logo-img-input" value="<?php echo esc_attr( $logo_img ); ?>" class="border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-600 w-full focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30" placeholder="https://...">
                                    <button type="button" class="jt-upload-logo-btn bg-[#0B5E34] hover:bg-[#073c21] text-white px-4 py-2.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap shadow-sm">📁 Pilih</button>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Tinggi Logo (px)</label>
                                <input type="number" name="logo_height" value="<?php echo esc_attr( $logo_height ); ?>" min="20" max="200" class="border border-gray-200 rounded-lg px-3 py-2.5 text-sm w-36 focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                                <p class="text-[11px] text-gray-400">Default: 50px</p>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Tagline Toko (override)</label>
                                <input type="text" name="logo_tagline_override" value="<?php echo esc_attr( $logo_tagline_override ); ?>" placeholder="Contoh: Terpercaya Sejak 2015" class="border border-gray-200 rounded-lg px-3 py-2.5 text-sm w-full focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                                <p class="text-[11px] text-gray-400">Jika diisi, teks ini akan menggantikan tagline default WordPress di header & footer.</p>
                            </div>
                            <?php if ( $logo_img ) : ?>
                            <div class="p-3 bg-green-50 border border-green-200 rounded-xl">
                                <p class="text-xs font-bold text-green-700"><i class="fa-solid fa-circle-check mr-1.5"></i> Logo aktif terpasang di header</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════
                 TAB 3: HEADER & NAVIGASI
            ════════════════════════════════════════════════════════ -->
            <div x-show="tab === 'header'" class="space-y-8" style="display:none;">
                <!-- Sticky Header -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-6">
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-thumbtack text-[#0B5E34]"></i> Pengaturan Header</h2>
                        <p class="text-xs text-gray-400 mt-1">Konfigurasi tampilan dan perilaku header situs.</p>
                    </div>
                    <div class="space-y-4">
                        <!-- Toggle Sticky Header -->
                        <label class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer">
                            <div>
                                <span class="font-semibold text-sm text-gray-800"><i class="fa-solid fa-anchor text-gray-500 mr-1"></i> Sticky Header (Header Mengambang)</span>
                                <p class="text-xs text-gray-400 mt-0.5">Header tetap terlihat saat pengguna menggulir halaman ke bawah.</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="toggle_sticky_header" value="0">
                                <input type="checkbox" name="toggle_sticky_header" value="1" <?php checked( $toggle_sticky_header, '1' ); ?> class="jt-toggle-check sr-only" id="tog-sticky">
                                <label for="tog-sticky" class="jt-toggle-pill cursor-pointer"></label>
                            </div>
                        </label>
                    </div>
                    <div class="mt-5 space-y-2">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Warna Background Header</label>
                        <div class="flex items-center gap-2">
                            <input type="color" value="<?php echo esc_attr( $header_bg_color ); ?>" class="w-10 h-10 border border-gray-200 rounded-lg cursor-pointer flex-shrink-0">
                            <input type="text" name="header_bg_color" value="<?php echo esc_attr( $header_bg_color ); ?>" class="border border-gray-200 rounded-lg px-3 py-2 text-xs font-mono text-gray-600 w-64 focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                        </div>
                    </div>
                </div>

                <!-- Announcement Bar -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-6">
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-bullhorn text-[#0B5E34]"></i> Baris Pengumuman (Announcement Bar)</h2>
                        <p class="text-xs text-gray-400 mt-1">Tampil di semua halaman, di atas header. Aktifkan toggle-nya di tab <strong>Umum & Warna</strong>.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Teks Pengumuman</label>
                            <textarea name="announcement_text" rows="2" placeholder="Contoh: 🚛 Gratis ongkir min. order Rp200rb!" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30"><?php echo esc_html( $announcement_text ); ?></textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Link Pengumuman (opsional)</label>
                            <input type="text" name="announcement_link" value="<?php echo esc_attr( $announcement_link ); ?>" placeholder="https://..." class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                            <p class="text-[11px] text-gray-400">Jika diisi, seluruh baris pengumuman bisa diklik.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Warna Background Bar</label>
                            <div class="flex items-center gap-2">
                                <input type="color" value="<?php echo esc_attr( $announcement_bg_color ); ?>" class="w-10 h-10 border border-gray-200 rounded-lg cursor-pointer flex-shrink-0">
                                <input type="text" name="announcement_bg_color" value="<?php echo esc_attr( $announcement_bg_color ); ?>" class="border border-gray-200 rounded-lg px-3 py-2 text-xs font-mono text-gray-600 w-full focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Warna Teks Bar</label>
                            <div class="flex items-center gap-2">
                                <input type="color" value="<?php echo esc_attr( $announcement_txt_color ); ?>" class="w-10 h-10 border border-gray-200 rounded-lg cursor-pointer flex-shrink-0">
                                <input type="text" name="announcement_txt_color" value="<?php echo esc_attr( $announcement_txt_color ); ?>" class="border border-gray-200 rounded-lg px-3 py-2 text-xs font-mono text-gray-600 w-full focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                            </div>
                        </div>
                    </div>

                    <!-- Live preview -->
                    <div class="mt-4 rounded-xl overflow-hidden border border-gray-200">
                        <p class="text-[11px] text-gray-400 px-3 pt-2 pb-1">Preview Baris Pengumuman:</p>
                        <div id="jt-announcement-preview" style="background:<?php echo esc_attr($announcement_bg_color); ?>;color:<?php echo esc_attr($announcement_txt_color); ?>;" class="text-center text-xs font-semibold py-2 px-4">
                            <?php echo esc_html( $announcement_text ?: 'Teks pengumuman Anda akan tampil di sini' ); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════
                 TAB 4: FOOTER
            ════════════════════════════════════════════════════════ -->
            <div x-show="tab === 'footer'" class="space-y-8" style="display:none;">
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-6">
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-copyright text-[#0B5E34]"></i> Konten Footer</h2>
                        <p class="text-xs text-gray-400 mt-1">Kustomisasi teks dan link yang tampil di bagian bawah situs.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Deskripsi Singkat Toko</label>
                            <textarea name="footer_description" rows="3" placeholder="Deskripsi singkat tentang toko Anda..." class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30"><?php echo esc_html( $footer_description ); ?></textarea>
                            <p class="text-[11px] text-gray-400">Tampil di kolom brand footer, di bawah logo.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Teks Copyright</label>
                            <input type="text" name="footer_copyright" value="<?php echo esc_attr( $footer_copyright ); ?>" placeholder="Contoh: © 2025 Jendela Ternak Malang. Semua hak dilindungi." class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                            <p class="text-[11px] text-gray-400">Tampil di baris paling bawah footer. Kosongkan untuk menggunakan teks default.</p>
                        </div>
                    </div>
                </div>

                <!-- Custom Footer Links (Kolom Informasi) -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-5">
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-link text-[#0B5E34]"></i> Link Kolom "Informasi" Footer</h2>
                        <p class="text-xs text-gray-400 mt-1">Kustomisasi daftar link pada kolom informasi di footer. Maks 6 link.</p>
                    </div>

                    <input type="hidden" name="footer_links_json" id="jt-footer-links-json" value="<?php echo $footer_links_json_escaped; ?>">

                    <div x-data="jtFooterLinks(<?php echo $footer_links_json_escaped; ?>)" x-init="syncJson()">
                        <div class="space-y-3">
                            <template x-for="(item, index) in links" :key="index">
                                <div class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl bg-gray-50/50">
                                    <span class="text-xs font-bold text-gray-400 w-5 text-center" x-text="index + 1 + '.'"></span>
                                    <input type="text" x-model="item.label" @input="syncJson()" placeholder="Label link" class="border border-gray-200 rounded-lg px-3 py-2 text-xs w-40 flex-shrink-0 focus:outline-none focus:ring-1 focus:ring-[#0B5E34]">
                                    <input type="text" x-model="item.url" @input="syncJson()" placeholder="https://... atau /slug/" class="border border-gray-200 rounded-lg px-3 py-2 text-xs w-full focus:outline-none focus:ring-1 focus:ring-[#0B5E34]">
                                    <button type="button" x-show="links.length > 1" @click="removeLink(index)" class="text-red-400 hover:text-red-600 text-xs font-bold px-2 py-1 rounded-lg hover:bg-red-50 transition-all flex-shrink-0"><i class="fa-solid fa-trash-can"></i></button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="addLink()" x-show="links.length < 6"
                            class="mt-4 w-full py-3 border-2 border-dashed border-[#0B5E34]/30 text-[#0B5E34] hover:border-[#0B5E34] hover:bg-green-50 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-plus mr-1.5"></i> Tambah Link
                        </button>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════
                 TAB 5: BERANDA & PRODUK
            ════════════════════════════════════════════════════════ -->
            <div x-show="tab === 'homepage'" class="space-y-8" style="display:none;">

                <!-- Shortcode Info -->
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex gap-3 shadow-sm">
                    <div class="text-emerald-700 text-xl"><i class="fa-solid fa-code"></i></div>
                    <div>
                        <h4 class="text-sm font-bold text-emerald-900">Shortcode Halaman Beranda</h4>
                        <p class="text-xs text-emerald-700 mt-1">Anda sekarang dapat menggunakan shortcode <code class="bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded font-mono font-semibold">[jt_homepage]</code> di halaman mana saja (misalnya halaman <strong>Home</strong>) untuk merender konten beranda default ini.</p>
                    </div>
                </div>

                <!-- Judul Seksi -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-6">
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-pen-to-square text-[#0B5E34]"></i> Judul Seksi Beranda</h2>
                        <p class="text-xs text-gray-400 mt-1">Kustomisasi teks judul untuk setiap seksi di halaman beranda.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">🔥 Judul Seksi Terlaris</label>
                            <input type="text" name="section_title_bestseller" value="<?php echo esc_attr( $section_title_bestseller ); ?>" placeholder="Produk Terlaris" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide"><i class="fa-solid fa-bolt text-amber-500 mr-1"></i> Judul Seksi Penawaran</label>
                            <input type="text" name="section_title_offers" value="<?php echo esc_attr( $section_title_offers ); ?>" placeholder="Penawaran Terbaik" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide"><i class="fa-solid fa-folder text-yellow-500 mr-1"></i> Judul Seksi Kategori</label>
                            <input type="text" name="section_title_categories" value="<?php echo esc_attr( $section_title_categories ); ?>" placeholder="Kategori Pilihan" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                        </div>
                    </div>
                </div>

                <!-- Pengaturan Produk -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-6">
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-cart-shopping text-[#0B5E34]"></i> Pengaturan Tampilan Produk</h2>
                        <p class="text-xs text-gray-400 mt-1">Atur tampilan grid produk dan teks tombol aksi.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Produk per Baris (Desktop)</label>
                            <select name="products_per_row" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30 bg-white">
                                <?php foreach ([3, 4, 5] as $n) : ?>
                                    <option value="<?php echo $n; ?>" <?php selected( $products_per_row, $n ); ?>><?php echo $n; ?> kolom</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Produk per Halaman (Shop)</label>
                            <input type="number" name="products_per_page" value="<?php echo esc_attr( $products_per_page ); ?>" min="4" max="100" class="border border-gray-200 rounded-lg px-3 py-2.5 text-sm w-full focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                            <p class="text-[11px] text-gray-400">Jumlah produk yang ditampilkan di halaman katalog.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Teks Tombol "Tambah Keranjang"</label>
                            <input type="text" name="btn_add_to_cart_text" value="<?php echo esc_attr( $btn_add_to_cart_text ); ?>" placeholder="Masukkan Keranjang" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Teks Tombol "Beli Sekarang"</label>
                            <input type="text" name="btn_buy_now_text" value="<?php echo esc_attr( $btn_buy_now_text ); ?>" placeholder="Beli Sekarang" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                        </div>
                    </div>
                </div>

                <!-- Tipografi Google Fonts -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-6">
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-font text-[#0B5E34]"></i> Tipografi (Google Fonts)</h2>
                        <p class="text-xs text-gray-400 mt-1">Pilih font yang akan digunakan untuk judul (heading) dan teks isi (body) di seluruh situs.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Font Heading (Judul)</label>
                            <select name="font_heading" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30 bg-white">
                                <?php foreach ( $google_fonts as $font ) : ?>
                                    <option value="<?php echo esc_attr( $font ); ?>" <?php selected( $font_heading, $font ); ?>><?php echo esc_html( $font ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                                <p class="text-[11px] text-gray-400 mb-1">Preview:</p>
                                <p id="jt-font-heading-preview" class="font-bold text-lg text-gray-800" style="font-family: '<?php echo esc_attr($font_heading); ?>', sans-serif;">Jendela Ternak Malang</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Font Body (Teks Isi)</label>
                            <select name="font_body" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30 bg-white">
                                <?php foreach ( $google_fonts as $font ) : ?>
                                    <option value="<?php echo esc_attr( $font ); ?>" <?php selected( $font_body, $font ); ?>><?php echo esc_html( $font ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                                <p class="text-[11px] text-gray-400 mb-1">Preview:</p>
                                <p id="jt-font-body-preview" class="text-sm text-gray-600 leading-relaxed" style="font-family: '<?php echo esc_attr($font_body); ?>', sans-serif;">Pakan ternak, obat hewan, dan alat peternakan terlengkap. Kami hadir untuk memenuhi semua kebutuhan peternakan Anda.</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                        <p class="text-xs text-blue-600 font-medium">💡 Font Google akan dimuat secara otomatis dari Google Fonts CDN dan diterapkan ke seluruh halaman situs.</p>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════
                 TAB 6: SLIDER & BANNER
            ════════════════════════════════════════════════════════ -->
            <div x-show="tab === 'slider'" class="space-y-8" style="display:none;">
                <!-- Hero Slider -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-5">
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-images text-[#0B5E34]"></i> Slide Hero Beranda</h2>
                        <p class="text-xs text-gray-400 mt-1">Atur slide bergulir di bagian utama halaman beranda. Bisa ditambah lebih dari 3.</p>
                    </div>

                    <input type="hidden" name="slides_json" id="jt-slides-json" value="<?php echo $slides_json_escaped; ?>">

                    <div x-data="jtSlider(<?php echo $slides_json_escaped; ?>)" x-init="syncJson()">
                        <div class="space-y-5" id="jt-slides-wrapper">
                            <template x-for="(slide, index) in slides" :key="index">
                                <div class="p-5 border border-gray-200 rounded-2xl bg-gray-50/50 space-y-4">
                                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                                        <h3 class="font-bold text-sm text-[#0B5E34]" x-text="'Slide #' + (index + 1)"></h3>
                                        <button type="button" x-show="slides.length > 1" @click="removeSlide(index)" class="text-red-400 hover:text-red-600 text-xs font-bold px-2 py-1 rounded-lg hover:bg-red-50 transition-all"><i class="fa-solid fa-trash-can mr-1"></i> Hapus</button>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase">Gambar Slide</label>
                                        <div class="flex items-center gap-2">
                                            <input type="text" x-model="slide.img" @input="syncJson()" class="jt-slide-img border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-600 w-full focus:outline-none focus:ring-1 focus:ring-[#0B5E34]" placeholder="https://...">
                                            <button type="button" @click="openMedia($event)" class="jt-upload-slide-btn bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-xs font-bold transition-all border border-gray-200 whitespace-nowrap">📁 Pilih</button>
                                        </div>
                                        <div x-show="slide.img" class="mt-1">
                                            <img :src="slide.img" class="h-20 rounded-lg object-cover border border-gray-200" @error="$el.style.display='none'">
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase">Judul Utama</label>
                                        <input type="text" x-model="slide.title" @input="syncJson()" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-[#0B5E34] focus:outline-none" placeholder="Judul banner slide">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase">Sub-Judul / Deskripsi</label>
                                        <textarea x-model="slide.subtitle" @input="syncJson()" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-[#0B5E34] focus:outline-none" placeholder="Deskripsi singkat slide"></textarea>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[11px] font-bold text-gray-600 uppercase">Link Tujuan</label>
                                        <input type="text" x-model="slide.link" @input="syncJson()" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-[#0B5E34] focus:outline-none" placeholder="https://...">
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="mt-5">
                            <button type="button" @click="addSlide()" class="w-full py-3 border-2 border-dashed border-[#0B5E34]/40 text-[#0B5E34] hover:border-[#0B5E34] hover:bg-green-50 rounded-2xl text-sm font-bold transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-plus mr-1.5"></i> Tambah Slide Baru
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Banners -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-5">
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-image text-[#0B5E34]"></i> Banner Sisipan Beranda</h2>
                        <p class="text-xs text-gray-400 mt-1">2 banner horizontal di antara seksi produk. Toggle on/off di tab Umum & Warna.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Banner 1 -->
                        <div class="p-5 border border-gray-200 rounded-2xl bg-gray-50/50 space-y-4">
                            <h3 class="font-bold text-sm text-[#0B5E34] border-b border-gray-100 pb-2">Banner 1 (Di Bawah Produk Terlaris)</h3>
                            <div class="space-y-2">
                                <label class="block text-[11px] font-bold text-gray-600 uppercase">Gambar Banner 1</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" name="banner1_img" value="<?php echo esc_attr( $banner1_img ); ?>" class="border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-600 w-full focus:outline-none focus:ring-1 focus:ring-[#0B5E34]">
                                    <button type="button" class="jt-upload-button bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-xs font-bold transition-all border border-gray-200 whitespace-nowrap">Pilih</button>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[11px] font-bold text-gray-600 uppercase">Link Banner 1</label>
                                <input type="text" name="banner1_link" value="<?php echo esc_attr( $banner1_link ); ?>" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-[#0B5E34] focus:outline-none" placeholder="https://...">
                            </div>
                        </div>
                        <!-- Banner 2 -->
                        <div class="p-5 border border-gray-200 rounded-2xl bg-gray-50/50 space-y-4">
                            <h3 class="font-bold text-sm text-[#0B5E34] border-b border-gray-100 pb-2">Banner 2 (Di Bawah Penawaran Terbaik)</h3>
                            <div class="space-y-2">
                                <label class="block text-[11px] font-bold text-gray-600 uppercase">Gambar Banner 2</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" name="banner2_img" value="<?php echo esc_attr( $banner2_img ); ?>" class="border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-600 w-full focus:outline-none focus:ring-1 focus:ring-[#0B5E34]">
                                    <button type="button" class="jt-upload-button bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-xs font-bold transition-all border border-gray-200 whitespace-nowrap">Pilih</button>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[11px] font-bold text-gray-600 uppercase">Link Banner 2</label>
                                <input type="text" name="banner2_link" value="<?php echo esc_attr( $banner2_link ); ?>" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-[#0B5E34] focus:outline-none" placeholder="https://...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════
                 TAB 7: MEDIA SOSIAL & WHATSAPP
            ════════════════════════════════════════════════════════ -->
            <div x-show="tab === 'social'" class="space-y-8" style="display:none;">
                <!-- WhatsApp CS -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-6">
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2"><i class="fa-brands fa-whatsapp text-green-500"></i> WhatsApp Customer Service</h2>
                        <p class="text-xs text-gray-400 mt-1">Konfigurasi nomor dan pesan bawaan untuk tombol chat mengambang.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Nomor WhatsApp CS</label>
                            <input type="text" name="whatsapp_number" value="<?php echo esc_attr( $whatsapp_number ); ?>" placeholder="e.g. 628123456789" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                            <span class="text-[10px] text-gray-400 block">Format kode negara tanpa tanda + (contoh: 62 untuk Indonesia).</span>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Pesan Bawaan Chat</label>
                            <textarea name="whatsapp_message" rows="3" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30"><?php echo esc_html( $whatsapp_message ); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Flash Sale Timer -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-5">
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-clock text-amber-500"></i> Waktu Berakhir Flash Sale</h2>
                        <p class="text-xs text-gray-400 mt-1">Atur waktu hitung mundur countdown Flash Sale di beranda. Toggle on/off di tab Umum & Warna.</p>
                    </div>
                    <div class="w-full md:w-1/2 space-y-2">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide">Tanggal & Waktu Selesai</label>
                        <input type="datetime-local" name="flash_sale_end" value="<?php echo esc_attr( $flash_sale_end ); ?>" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                    </div>
                </div>

                <!-- Social Media & Marketplace Links -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-6">
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-share-nodes text-[#0B5E34]"></i> Link Media Sosial & Marketplace</h2>
                        <p class="text-xs text-gray-400 mt-1">Link yang tampil di header dan footer situs. Kosongkan untuk menyembunyikan ikon.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php
                        $social_fields = [
                            'social_instagram' => [ 'label' => 'Instagram URL', 'icon' => '<i class="fa-brands fa-instagram text-pink-600 mr-1.5"></i>', 'ph' => 'https://instagram.com/username', 'val' => $social_instagram ],
                            'social_tiktok'    => [ 'label' => 'TikTok URL', 'icon' => '<i class="fa-brands fa-tiktok text-black mr-1.5"></i>', 'ph' => 'https://tiktok.com/@username', 'val' => $social_tiktok ],
                            'social_shopee'    => [ 'label' => 'Shopee Shop URL', 'icon' => '<i class="fa-solid fa-bag-shopping text-orange-500 mr-1.5"></i>', 'ph' => 'https://shopee.co.id/username', 'val' => $social_shopee ],
                            'social_tokopedia' => [ 'label' => 'Tokopedia URL', 'icon' => '<i class="fa-solid fa-store text-green-600 mr-1.5"></i>', 'ph' => 'https://tokopedia.com/username', 'val' => $social_tokopedia ],
                            'social_facebook'  => [ 'label' => 'Facebook URL', 'icon' => '<i class="fa-brands fa-facebook text-blue-600 mr-1.5"></i>', 'ph' => 'https://facebook.com/pagename', 'val' => $social_facebook ],
                            'social_lazada'    => [ 'label' => 'Lazada URL', 'icon' => '<i class="fa-solid fa-cart-shopping text-sky-500 mr-1.5"></i>', 'ph' => 'https://lazada.co.id/shop/...', 'val' => $social_lazada ],
                        ];
                        foreach ( $social_fields as $name => $field ) : ?>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide"><?php echo $field['icon']; ?> <?php echo $field['label']; ?></label>
                            <input type="text" name="<?php echo $name; ?>" value="<?php echo esc_attr( $field['val'] ); ?>" placeholder="<?php echo esc_attr( $field['ph'] ); ?>" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B5E34]/30">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- ── SUBMIT BUTTON ───────────────────────────────────── -->
            <div class="pt-8 border-t border-gray-100 flex justify-between items-center mt-4">
                <p class="text-xs text-gray-400">Pengaturan disimpan ke database WordPress dan berlaku segera setelah halaman di-refresh.</p>
                <button type="submit" name="jt_save_settings"
                    class="bg-[#0B5E34] text-white hover:bg-[#073c21] px-8 py-3 rounded-xl font-bold text-sm shadow-sm hover:shadow-md transition-all duration-150 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Simpan Semua Pengaturan
                </button>
            </div>
        </form>
    </div>

    <style>
        /* Toggle Switch Pill */
        .jt-toggle-pill {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
            background: #d1d5db;
            border-radius: 999px;
            transition: background 0.2s;
            flex-shrink: 0;
        }
        .jt-toggle-pill::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 18px;
            height: 18px;
            background: white;
            border-radius: 50%;
            transition: transform 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .jt-toggle-check:checked + .jt-toggle-pill {
            background: #0B5E34;
        }
        .jt-toggle-check:checked + .jt-toggle-pill::after {
            transform: translateX(20px);
        }
    </style>

    <script>
        // ─── Color picker sync ───────────────────────────────────────────────
        document.querySelectorAll('div input[type="color"]').forEach(colorInput => {
            const textInput = colorInput.nextElementSibling;
            if (!textInput || textInput.type === 'checkbox') return;
            colorInput.addEventListener('input', function() {
                textInput.value = this.value.toUpperCase();
                // Update announcement preview if applicable
                if (textInput.name === 'announcement_bg_color') {
                    const preview = document.getElementById('jt-announcement-preview');
                    if (preview) preview.style.background = this.value;
                }
                if (textInput.name === 'announcement_txt_color') {
                    const preview = document.getElementById('jt-announcement-preview');
                    if (preview) preview.style.color = this.value;
                }
            });
            textInput.addEventListener('input', function() {
                let val = this.value.trim();
                if (val && !val.startsWith('#')) val = '#' + val;
                if (/^#[0-9A-F]{6}$/i.test(val)) {
                    colorInput.value = val;
                    colorInput.dispatchEvent(new Event('input'));
                }
            });
        });

        // ─── Announcement preview live update ────────────────────────────────
        const announcementTextEl = document.querySelector('textarea[name="announcement_text"]');
        if (announcementTextEl) {
            announcementTextEl.addEventListener('input', function() {
                const preview = document.getElementById('jt-announcement-preview');
                if (preview) preview.textContent = this.value || '📣 Teks pengumuman Anda akan tampil di sini';
            });
        }

        // ─── Font preview live update ─────────────────────────────────────────
        const fontHeadingSelect = document.querySelector('select[name="font_heading"]');
        const fontBodySelect    = document.querySelector('select[name="font_body"]');
        function loadGoogleFontPreview(family, previewId) {
            const preview = document.getElementById(previewId);
            if (!preview) return;
            const linkId = 'gfont-preview-' + previewId;
            let link = document.getElementById(linkId);
            if (!link) {
                link = document.createElement('link');
                link.id = linkId;
                link.rel = 'stylesheet';
                document.head.appendChild(link);
            }
            link.href = 'https://fonts.googleapis.com/css2?family=' + encodeURIComponent(family) + ':wght@400;700&display=swap';
            preview.style.fontFamily = "'" + family + "', sans-serif";
        }
        if (fontHeadingSelect) {
            fontHeadingSelect.addEventListener('change', function() {
                loadGoogleFontPreview(this.value, 'jt-font-heading-preview');
            });
        }
        if (fontBodySelect) {
            fontBodySelect.addEventListener('change', function() {
                loadGoogleFontPreview(this.value, 'jt-font-body-preview');
            });
        }

        // ─── Logo upload button ───────────────────────────────────────────────
        document.querySelectorAll('.jt-upload-logo-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const input   = document.getElementById('jt-logo-img-input');
                const preview = document.getElementById('jt-logo-img-preview');
                const uploader = wp.media({
                    title: 'Pilih Logo Toko',
                    button: { text: 'Gunakan Gambar' },
                    multiple: false
                }).on('select', function() {
                    const att = uploader.state().get('selection').first().toJSON();
                    input.value = att.url;
                    if (preview && preview.tagName === 'IMG') {
                        preview.src = att.url;
                    } else if (preview) {
                        const img = document.createElement('img');
                        img.src = att.url;
                        img.alt = 'Logo';
                        img.className = 'max-h-24 max-w-full object-contain';
                        img.id = 'jt-logo-img-preview';
                        preview.replaceWith(img);
                    }
                }).open();
            });
        });

        // ─── Banner upload buttons ────────────────────────────────────────────
        document.querySelectorAll('.jt-upload-button').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const input = this.previousElementSibling;
                if (!input || input.tagName !== 'INPUT') return;
                const uploader = wp.media({
                    title: 'Pilih Gambar Banner',
                    button: { text: 'Gunakan Gambar' },
                    multiple: false
                }).on('select', function() {
                    const att = uploader.state().get('selection').first().toJSON();
                    input.value = att.url;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }).open();
            });
        });

        // ─── Alpine.js Slider Manager ─────────────────────────────────────────
        function jtSlider(initialSlides) {
            return {
                slides: JSON.parse(JSON.stringify(initialSlides)),
                syncJson() {
                    const hidden = document.getElementById('jt-slides-json');
                    if (hidden) hidden.value = JSON.stringify(this.slides);
                },
                addSlide() {
                    this.slides.push({ img: '', title: '', subtitle: '', link: '' });
                    this.syncJson();
                },
                removeSlide(index) {
                    if (this.slides.length > 1) {
                        this.slides.splice(index, 1);
                        this.syncJson();
                    }
                },
                openMedia(event) {
                    const btn = event.currentTarget;
                    const imgInput = btn.closest('.space-y-2').querySelector('.jt-slide-img');
                    const uploader = wp.media({
                        title: 'Pilih Gambar Slide',
                        button: { text: 'Gunakan Gambar' },
                        multiple: false
                    }).on('select', () => {
                        const att = uploader.state().get('selection').first().toJSON();
                        imgInput.value = att.url;
                        imgInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }).open();
                }
            };
        }

        // ─── Alpine.js Footer Links Manager ───────────────────────────────────
        function jtFooterLinks(initialLinks) {
            return {
                links: JSON.parse(JSON.stringify(initialLinks)),
                syncJson() {
                    const hidden = document.getElementById('jt-footer-links-json');
                    if (hidden) hidden.value = JSON.stringify(this.links);
                },
                addLink() {
                    if (this.links.length < 6) {
                        this.links.push({ label: '', url: '' });
                        this.syncJson();
                    }
                },
                removeLink(index) {
                    if (this.links.length > 1) {
                        this.links.splice(index, 1);
                        this.syncJson();
                    }
                }
            };
        }
    </script>
    <?php
}
