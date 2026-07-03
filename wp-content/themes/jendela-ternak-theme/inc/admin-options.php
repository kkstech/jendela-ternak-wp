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
    return isset( $cache[ $key ] ) && $cache[ $key ] !== '' ? $cache[ $key ] : $default;
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
        // Tailwind (layout/utility classes)
        wp_enqueue_script( 'tailwind-cdn', 'https://cdn.tailwindcss.com', [], null, false );
        // Alpine.js (powers tabs and dynamic slide manager)
        wp_enqueue_script(
            'alpinejs-admin',
            'https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js',
            [],
            '3.14.1',
            true  // footer
        );
    }
}

// Render settings page
function jt_render_theme_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Save logic
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

        $settings = array(
            'logo_img'          => esc_url_raw( $_POST['logo_img'] ?? '' ),
            'logo_height'       => absint( $_POST['logo_height'] ?? 50 ),
            'color_primary'     => sanitize_hex_color( $_POST['color_primary'] ),
            'color_secondary'   => sanitize_hex_color( $_POST['color_secondary'] ),
            'color_accent'      => sanitize_hex_color( $_POST['color_accent'] ),
            'color_promo_bg'    => sanitize_hex_color( $_POST['color_promo_bg'] ),
            'whatsapp_number'   => sanitize_text_field( $_POST['whatsapp_number'] ),
            'whatsapp_message'  => sanitize_text_field( $_POST['whatsapp_message'] ),
            'flash_sale_end'    => sanitize_text_field( $_POST['flash_sale_end'] ),
            'slides'            => $raw_slides,
            // Banner fields
            'banner1_img'       => esc_url_raw( $_POST['banner1_img'] ),
            'banner1_link'      => esc_url_raw( $_POST['banner1_link'] ),
            'banner2_img'       => esc_url_raw( $_POST['banner2_img'] ),
            'banner2_link'      => esc_url_raw( $_POST['banner2_link'] ),
        );
        
        update_option( 'jt_theme_settings', $settings );

        echo '<div class="notice notice-success is-dismissible" style="margin: 15px 15px 0 0;"><p><strong>Pengaturan Tema Berhasil Disimpan!</strong></p></div>';
    }

    // Load current options — always from jt_theme_settings (single source of truth)
    $settings         = get_option( 'jt_theme_settings', array() );
    $logo_img         = $settings['logo_img'] ?? '';
    $color_primary    = $settings['color_primary'] ?? '#0B5E34';
    $color_secondary  = $settings['color_secondary'] ?? '#4CAF50';
    $color_accent     = $settings['color_accent'] ?? '#D4B106';
    $color_promo_bg   = $settings['color_promo_bg'] ?? '#C8D400';
    $whatsapp_number  = $settings['whatsapp_number'] ?? '6281234567890';
    $whatsapp_message = $settings['whatsapp_message'] ?? 'Halo, saya ingin bertanya tentang produk Jendela Ternak Malang.';
    $flash_sale_end   = $settings['flash_sale_end'] ?? '';

    // Dynamic slides (stored as array; migrate from legacy fixed keys)
    $saved_slides = $settings['slides'] ?? null;
    if ( ! $saved_slides ) {
        // Build from legacy fixed slide fields if they exist
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

    // Banners
    $banner1_img  = $settings['banner1_img'] ?? '';
    $banner1_link = $settings['banner1_link'] ?? '';
    $banner2_img  = $settings['banner2_img'] ?? '';
    $banner2_link = $settings['banner2_link'] ?? '';
    ?>
    
    <div class="mr-6 my-6 font-sans" x-data="{ tab: 'general' }">
        <!-- Dashboard Header -->
        <div class="bg-[#0B5E34] text-white p-6 rounded-t-2xl flex items-center justify-between shadow-md">
            <div>
                <h1 class="text-2xl font-extrabold tracking-wide">Jendela Ternak Malang</h1>
                <p class="text-sm text-green-100 opacity-90 mt-1">Theme Settings & Style Panel</p>
            </div>
            <div class="bg-[#D4B106] text-black text-xs font-black px-3 py-1.5 rounded-full shadow-sm">
                Versi 1.1.0
            </div>
        </div>

        <!-- Tab Nav -->
        <div class="bg-gray-50 border-x border-gray-100 flex px-6 py-1">
            <button type="button" @click="tab = 'general'" :class="tab === 'general' ? 'border-[#0B5E34] text-[#0B5E34]' : 'border-transparent text-gray-400 hover:text-gray-600'" class="px-5 py-3.5 border-b-2 font-bold text-sm transition-all focus:outline-none">
                ⚙️ Umum & Warna
            </button>
            <button type="button" @click="tab = 'logo'" :class="tab === 'logo' ? 'border-[#0B5E34] text-[#0B5E34]' : 'border-transparent text-gray-400 hover:text-gray-600'" class="px-5 py-3.5 border-b-2 font-bold text-sm transition-all focus:outline-none">
                🏷️ Logo
            </button>
            <button type="button" @click="tab = 'slider'" :class="tab === 'slider' ? 'border-[#0B5E34] text-[#0B5E34]' : 'border-transparent text-gray-400 hover:text-gray-600'" class="px-5 py-3.5 border-b-2 font-bold text-sm transition-all focus:outline-none">
                🖼️ Slider & Banner
            </button>
        </div>

        <!-- Form Body -->
        <form method="POST" action="" class="bg-white p-8 rounded-b-2xl shadow-sm border border-gray-100">
            <?php wp_nonce_field( 'jt_settings_nonce_action', 'jt_settings_nonce' ); ?>

            <!-- Tab 1: General Options -->
            <div x-show="tab === 'general'" class="space-y-8">
                <!-- Skema Warna -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-5">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            🎨 Skema Warna Brand
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Atur palet warna utama untuk elemen header, tombol, bintang, dan badge promo.</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Primary -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase">Hijau Tua (Primary)</label>
                            <div class="flex items-center gap-2">
                                <input type="color" value="<?php echo esc_attr( $color_primary ); ?>" class="w-10 h-10 border border-gray-200 rounded-md cursor-pointer">
                                <input type="text" name="color_primary" value="<?php echo esc_attr( $color_primary ); ?>" class="border border-gray-200 rounded-md px-3 py-2 text-xs font-mono text-gray-600 w-full focus:outline-none focus:ring-1 focus:ring-[#0B5E34]">
                            </div>
                        </div>

                        <!-- Secondary -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase">Hijau Medium (Secondary)</label>
                            <div class="flex items-center gap-2">
                                <input type="color" value="<?php echo esc_attr( $color_secondary ); ?>" class="w-10 h-10 border border-gray-200 rounded-md cursor-pointer">
                                <input type="text" name="color_secondary" value="<?php echo esc_attr( $color_secondary ); ?>" class="border border-gray-200 rounded-md px-3 py-2 text-xs font-mono text-gray-600 w-full focus:outline-none focus:ring-1 focus:ring-[#0B5E34]">
                            </div>
                        </div>

                        <!-- Accent -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase">Kuning Emas (Accent)</label>
                            <div class="flex items-center gap-2">
                                <input type="color" value="<?php echo esc_attr( $color_accent ); ?>" class="w-10 h-10 border border-gray-200 rounded-md cursor-pointer">
                                <input type="text" name="color_accent" value="<?php echo esc_attr( $color_accent ); ?>" class="border border-gray-200 rounded-md px-3 py-2 text-xs font-mono text-gray-600 w-full focus:outline-none focus:ring-1 focus:ring-[#0B5E34]">
                            </div>
                        </div>

                        <!-- Promo Bg -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase">Kuning-Hijau Promo</label>
                            <div class="flex items-center gap-2">
                                <input type="color" value="<?php echo esc_attr( $color_promo_bg ); ?>" class="w-10 h-10 border border-gray-200 rounded-md cursor-pointer">
                                <input type="text" name="color_promo_bg" value="<?php echo esc_attr( $color_promo_bg ); ?>" class="border border-gray-200 rounded-md px-3 py-2 text-xs font-mono text-gray-600 w-full focus:outline-none focus:ring-1 focus:ring-[#0B5E34]">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WhatsApp CS Options -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-5">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            💬 WhatsApp Customer Service
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Konfigurasi nomor WhatsApp dan pesan bawaan untuk tombol chat mengambang.</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase">Nomor WhatsApp CS</label>
                            <input type="text" name="whatsapp_number" value="<?php echo esc_attr( $whatsapp_number ); ?>" placeholder="e.g. 628123456789" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B5E34]">
                            <span class="text-[10px] text-gray-400 block font-medium">Gunakan format kode negara tanpa karakter tambahan (e.g. 62 untuk Indonesia).</span>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase">Pesan Bawaan Chat</label>
                            <textarea name="whatsapp_message" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B5E34]"><?php echo esc_html( $whatsapp_message ); ?></textarea>
                            <span class="text-[10px] text-gray-400 block font-medium">Pesan pembuka otomatis saat pembeli menekan tombol WhatsApp.</span>
                        </div>
                    </div>
                </div>

                <!-- Flash Sale -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-5">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            ⚡ Waktu Berakhir Flash Sale
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Mengatur waktu hitung mundur (countdown) untuk promo Flash Sale di beranda.</p>
                    </div>
                    
                    <div class="w-full md:w-1/2 space-y-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase">Pilih Tanggal & Waktu Selesai</label>
                        <input type="datetime-local" name="flash_sale_end" value="<?php echo esc_attr( $flash_sale_end ); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0B5E34]">
                    </div>
                </div>
            </div>

            <!-- Tab 2: Logo -->
            <div x-show="tab === 'logo'" class="space-y-8" style="display:none;">
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-5">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            🏷️ Logo Toko
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Upload logo toko yang akan muncul di header website. Disarankan format PNG transparan dengan tinggi minimal 60px.</p>
                    </div>

                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        <!-- Logo Preview -->
                        <div class="w-full md:w-64 flex-shrink-0">
                            <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 bg-gray-50 flex flex-col items-center justify-center gap-3 min-h-[140px]">
                                <div id="jt-logo-preview" class="flex items-center justify-center w-full">
                                    <?php if ( $logo_img ) : ?>
                                        <img src="<?php echo esc_url( $logo_img ); ?>" alt="Logo" class="max-h-24 max-w-full object-contain" id="jt-logo-img-preview">
                                    <?php else : ?>
                                        <div id="jt-logo-img-preview" class="text-center">
                                            <div class="text-4xl mb-2">🏪</div>
                                            <p class="text-xs text-gray-400">Belum ada logo</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-2 text-center">Preview logo header</p>
                        </div>

                        <!-- Logo URL input -->
                        <div class="flex-1 space-y-4">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-700 uppercase">URL Gambar Logo</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" name="logo_img" id="jt-logo-img-input" value="<?php echo esc_attr( $logo_img ); ?>" class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-600 w-full focus:outline-none focus:ring-2 focus:ring-[#0B5E34]" placeholder="https://...">
                                    <button type="button" class="jt-upload-logo-btn bg-[#0B5E34] hover:bg-[#073c21] text-white px-4 py-2.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap shadow-sm">📁 Pilih Gambar</button>
                                </div>
                                <p class="text-[11px] text-gray-400">Klik "Pilih Gambar" untuk membuka media library WordPress, atau paste URL gambar secara langsung.</p>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-700 uppercase">Tinggi Logo di Header (px)</label>
                                <input type="number" name="logo_height" value="<?php echo esc_attr( $settings['logo_height'] ?? '50' ); ?>" min="20" max="200" class="border border-gray-300 rounded-lg px-3 py-2.5 text-sm w-40 focus:outline-none focus:ring-2 focus:ring-[#0B5E34]">
                                <p class="text-[11px] text-gray-400">Default: 50px</p>
                            </div>

                            <?php if ( $logo_img ) : ?>
                            <div class="p-3 bg-green-50 border border-green-200 rounded-xl">
                                <p class="text-xs font-bold text-green-700">✅ Logo aktif terpasang di header</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Slider & Banner Configuration -->
            <div x-show="tab === 'slider'" class="space-y-8" style="display:none;">
                <!-- Hero Slider -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-5">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            🎠 Pengaturan Slide Hero (Beranda)
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Atur hingga 3 banner bergulir untuk slide bagian utama beranda.</p>
                    </div>

                    <!-- Hidden field holding the slides JSON -->
                    <input type="hidden" name="slides_json" id="jt-slides-json" value="<?php echo $slides_json_escaped; ?>">

                    <!-- Dynamic slides list managed by Alpine -->
                    <div
                        x-data="jtSlider(<?php echo $slides_json_escaped; ?>)"
                        x-init="syncJson()"
                    >
                        <div class="space-y-5" id="jt-slides-wrapper">
                            <template x-for="(slide, index) in slides" :key="index">
                                <div class="p-5 border border-gray-200 rounded-2xl bg-gray-50/50 space-y-4">
                                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                                        <h3 class="font-bold text-sm text-[#0B5E34]" x-text="'Slide #' + (index + 1)"></h3>
                                        <button
                                            type="button"
                                            x-show="slides.length > 1"
                                            @click="removeSlide(index)"
                                            class="text-red-400 hover:text-red-600 text-xs font-bold px-2 py-1 rounded-lg hover:bg-red-50 transition-all"
                                        >✕ Hapus</button>
                                    </div>

                                    <!-- Gambar -->
                                    <div class="space-y-2">
                                        <label class="block text-[11px] font-bold text-gray-700 uppercase">Gambar Slide</label>
                                        <div class="flex items-center gap-2">
                                            <input
                                                type="text"
                                                x-model="slide.img"
                                                @input="syncJson()"
                                                class="jt-slide-img border border-gray-300 rounded-lg px-3 py-2 text-xs text-gray-600 w-full focus:outline-none focus:ring-1 focus:ring-[#0B5E34]"
                                                placeholder="https://..."
                                            >
                                            <button
                                                type="button"
                                                @click="openMedia($event)"
                                                class="jt-upload-slide-btn bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-2 rounded-lg text-xs font-bold transition-all border border-gray-300 whitespace-nowrap"
                                            >📁 Pilih</button>
                                        </div>
                                        <!-- Thumbnail preview -->
                                        <div x-show="slide.img" class="mt-1">
                                            <img :src="slide.img" class="h-20 rounded-lg object-cover border border-gray-200" @error="$el.style.display='none'">
                                        </div>
                                    </div>

                                    <!-- Judul -->
                                    <div class="space-y-2">
                                        <label class="block text-[11px] font-bold text-gray-700 uppercase">Judul Utama</label>
                                        <input type="text" x-model="slide.title" @input="syncJson()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-[#0B5E34] focus:outline-none" placeholder="Judul banner slide">
                                    </div>

                                    <!-- Subtitle -->
                                    <div class="space-y-2">
                                        <label class="block text-[11px] font-bold text-gray-700 uppercase">Sub-Judul / Deskripsi</label>
                                        <textarea x-model="slide.subtitle" @input="syncJson()" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-[#0B5E34] focus:outline-none" placeholder="Deskripsi singkat slide"></textarea>
                                    </div>

                                    <!-- Link -->
                                    <div class="space-y-2">
                                        <label class="block text-[11px] font-bold text-gray-700 uppercase">Link Tujuan</label>
                                        <input type="text" x-model="slide.link" @input="syncJson()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-[#0B5E34] focus:outline-none" placeholder="https://...">
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Add Slide Button -->
                        <div class="mt-5">
                            <button
                                type="button"
                                @click="addSlide()"
                                class="w-full py-3 border-2 border-dashed border-[#0B5E34]/40 text-[#0B5E34] hover:border-[#0B5E34] hover:bg-green-50 rounded-2xl text-sm font-bold transition-all flex items-center justify-center gap-2"
                            >
                                ＋ Tambah Slide Baru
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Banners Settings -->
                <div>
                    <div class="border-b border-gray-100 pb-3 mb-5">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            📢 Banner Sisipan (Beranda)
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Mengatur tautan dan gambar untuk 2 buah banner horizontal yang disisipkan di antara daftar produk.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Banner 1 -->
                        <div class="p-5 border border-gray-200 rounded-2xl bg-gray-50/50 space-y-4">
                            <h3 class="font-bold text-sm text-[#0B5E34] border-b border-gray-100 pb-2">Banner 1 (Di Bawah Produk Terlaris)</h3>
                            <div class="space-y-2">
                                <label class="block text-[11px] font-bold text-gray-700 uppercase">Gambar Banner 1</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" name="banner1_img" value="<?php echo esc_attr( $banner1_img ); ?>" class="border border-gray-300 rounded-lg px-3 py-2 text-xs text-gray-600 w-full focus:outline-none focus:ring-1 focus:ring-[#0B5E34]">
                                    <button type="button" class="jt-upload-button bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-2 rounded-lg text-xs font-bold transition-all border border-gray-300 whitespace-nowrap">Pilih</button>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[11px] font-bold text-gray-700 uppercase">Link Banner 1</label>
                                <input type="text" name="banner1_link" value="<?php echo esc_attr( $banner1_link ); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-[#0B5E34] focus:outline-none" placeholder="e.g. http://...">
                            </div>
                        </div>

                        <!-- Banner 2 -->
                        <div class="p-5 border border-gray-200 rounded-2xl bg-gray-50/50 space-y-4">
                            <h3 class="font-bold text-sm text-[#0B5E34] border-b border-gray-100 pb-2">Banner 2 (Di Bawah Penawaran Terbaik)</h3>
                            <div class="space-y-2">
                                <label class="block text-[11px] font-bold text-gray-700 uppercase">Gambar Banner 2</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" name="banner2_img" value="<?php echo esc_attr( $banner2_img ); ?>" class="border border-gray-300 rounded-lg px-3 py-2 text-xs text-gray-600 w-full focus:outline-none focus:ring-1 focus:ring-[#0B5E34]">
                                    <button type="button" class="jt-upload-button bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-2 rounded-lg text-xs font-bold transition-all border border-gray-300 whitespace-nowrap">Pilih</button>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[11px] font-bold text-gray-700 uppercase">Link Banner 2</label>
                                <input type="text" name="banner2_link" value="<?php echo esc_attr( $banner2_link ); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-[#0B5E34] focus:outline-none" placeholder="e.g. http://...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-6 border-t border-gray-100 flex justify-end">
                <button type="submit" name="jt_save_settings" class="bg-[#0B5E34] text-white hover:bg-[#073c21] px-6 py-3 rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition-all duration-150 flex items-center gap-2">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
    
    <script>
        // ─── Color picker sync ───────────────────────────────────────────────
        document.querySelectorAll('div.grid input[type="color"]').forEach(colorInput => {
            const textInput = colorInput.nextElementSibling;
            colorInput.addEventListener('input', function() {
                textInput.value = this.value.toUpperCase();
            });
            textInput.addEventListener('input', function() {
                let val = this.value.trim();
                if (val && !val.startsWith('#')) val = '#' + val;
                if (/^#[0-9A-F]{6}$/i.test(val)) colorInput.value = val;
                else if (/^#[0-9A-F]{3}$/i.test(val)) {
                    const e = '#' + val[1]+val[1]+val[2]+val[2]+val[3]+val[3];
                    colorInput.value = e;
                }
            });
        });

    // ─── Logo upload button ──────────────────────────────────────────────
    document.querySelectorAll('.jt-upload-logo-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const input = document.getElementById('jt-logo-img-input');
            const preview = document.getElementById('jt-logo-img-preview');
            const uploader = wp.media({
                title: 'Pilih Logo Toko',
                button: { text: 'Gunakan Gambar' },
                multiple: false
            }).on('select', function() {
                const att = uploader.state().get('selection').first().toJSON();
                input.value = att.url;
                // Update preview
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

    // ─── Banner upload buttons ───────────────────────────────────────────
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

        // ─── Alpine.js Slider Manager ────────────────────────────────────────
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
                    // Find the sibling text input (the .jt-slide-img in the same form row)
                    const btn = event.currentTarget;
                    const imgInput = btn.closest('.space-y-2').querySelector('.jt-slide-img');
                    const uploader = wp.media({
                        title: 'Pilih Gambar Slide',
                        button: { text: 'Gunakan Gambar' },
                        multiple: false
                    }).on('select', () => {
                        const att = uploader.state().get('selection').first().toJSON();
                        imgInput.value = att.url;
                        // Trigger Alpine model update
                        imgInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }).open();
                }
            };
        }

        // Sync JSON before form submit (extra safety)
        document.querySelector('form').addEventListener('submit', function() {
            // The x-model binding already keeps slides_json updated via syncJson()
            // This is a final guard pass — nothing extra needed.
        });
    </script>
    <?php
}
