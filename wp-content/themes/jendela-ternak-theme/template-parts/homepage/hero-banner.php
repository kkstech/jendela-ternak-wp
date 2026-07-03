<?php
/**
 * template-parts/homepage/hero-banner.php
 * Dynamic Hero Slider Banner using AlpineJS & custom Admin Options.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

// Retrieve settings options
$settings = get_option( 'jt_theme_settings', array() );

// Use new dynamic slides array format first; fall back to legacy fixed keys
$slides = array();

if ( ! empty( $settings['slides'] ) && is_array( $settings['slides'] ) ) {
    foreach ( $settings['slides'] as $s ) {
        if ( ! empty( $s['title'] ) || ! empty( $s['img'] ) ) {
            $slides[] = array(
                'img'      => $s['img'] ?? '',
                'title'    => $s['title'] ?? '',
                'subtitle' => $s['subtitle'] ?? '',
                'link'     => $s['link'] ?? '',
            );
        }
    }
} else {
    // Legacy fixed key migration
    for ( $i = 1; $i <= 3; $i++ ) {
        $img      = $settings["slide{$i}_img"] ?? '';
        $title    = $settings["slide{$i}_title"] ?? '';
        $subtitle = $settings["slide{$i}_subtitle"] ?? '';
        $link     = $settings["slide{$i}_link"] ?? '';
        if ( ! empty( $title ) || ! empty( $img ) ) {
            $slides[] = compact( 'img', 'title', 'subtitle', 'link' );
        }
    }
}

// Default slide if nothing is configured
if ( empty( $slides ) ) {
    $slides[] = array(
        'img'      => '',
        'title'    => 'Lengkapi Kebutuhan Peternakan Anda',
        'subtitle' => 'Pakan ternak, obat hewan, dan alat peternakan terlengkap. Pengiriman ke seluruh Indonesia.',
        'link'     => wc_get_page_permalink( 'shop' ),
    );
}
?>

<section class="jt-hero-slider relative overflow-hidden" x-data="{ 
    activeSlide: 0, 
    slidesCount: <?php echo count( $slides ); ?>,
    timer: null,
    next() { this.activeSlide = (this.activeSlide + 1) % this.slidesCount; },
    prev() { this.activeSlide = (this.activeSlide - 1 + this.slidesCount) % this.slidesCount; },
    init() {
        this.timer = setInterval(() => { this.next(); }, 6000);
    }
}" aria-label="<?php esc_attr_e( 'Promo Utama', 'jendela-ternak' ); ?>">

    <!-- Slider Wrapper -->
    <div class="relative w-full h-[320px] md:h-[420px] bg-[#0B5E34] overflow-hidden rounded-2xl shadow-sm border border-gray-100">
        <!-- Slides -->
        <div class="flex transition-transform duration-700 ease-out h-full" :style="{ transform: 'translateX(-' + (activeSlide * 100) + '%)', width: (slidesCount * 100) + '%' }">
            <?php foreach ( $slides as $slide ) : ?>
                <div class="w-full flex-shrink-0 h-full relative flex items-center" style="width: <?php echo 100 / count( $slides ); ?>%;">
                    <!-- Background Image or Gradient -->
                    <?php if ( ! empty( $slide['img'] ) ) : ?>
                        <div class="absolute inset-0 z-0 bg-cover bg-center" style="background-image: url('<?php echo esc_url( $slide['img'] ); ?>');"></div>
                    <?php else : ?>
                        <div class="absolute inset-0 z-0 bg-gradient-to-r from-[#0B5E34] to-[#4CAF50]"></div>
                    <?php endif; ?>

                    <!-- Content -->
                    <div class="jt-container relative z-10 w-full text-white px-8 md:px-16">
                        <div class="max-w-xl space-y-4">
                            <h2 class="text-2xl md:text-4xl font-extrabold leading-tight">
                                <?php echo esc_html( $slide['title'] ); ?>
                            </h2>
                            <?php if ( ! empty( $slide['subtitle'] ) ) : ?>
                                <p class="text-xs md:text-sm text-gray-200 leading-relaxed max-w-lg">
                                    <?php echo esc_html( $slide['subtitle'] ); ?>
                                </p>
                            <?php endif; ?>
                            <?php if ( ! empty( $slide['link'] ) ) : ?>
                                <div class="pt-1">
                                    <a href="<?php echo esc_url( $slide['link'] ); ?>" class="inline-flex items-center gap-2 bg-[#D4B106] text-black hover:bg-[#bda005] px-5 py-2.5 rounded-xl font-bold text-xs transition-all shadow-md">
                                        <?php esc_html_e( 'Belanja Sekarang', 'jendela-ternak' ); ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                        </svg>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Navigation Dots -->
        <?php if ( count( $slides ) > 1 ) : ?>
            <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-2 z-20">
                <?php for ( $i = 0; $i < count( $slides ); $i++ ) : ?>
                    <button type="button" @click="activeSlide = <?php echo $i; ?>; clearInterval(timer);" 
                        :class="activeSlide === <?php echo $i; ?> ? 'bg-[#D4B106] w-5' : 'bg-white/50 w-2 hover:bg-white'" 
                        class="h-2 rounded-full transition-all duration-300 focus:outline-none"></button>
                <?php endfor; ?>
            </div>
            
            <!-- Arrows -->
            <button type="button" @click="prev(); clearInterval(timer);" class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white w-8 h-8 rounded-full flex items-center justify-center transition-all z-20 focus:outline-none">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button type="button" @click="next(); clearInterval(timer);" class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white w-8 h-8 rounded-full flex items-center justify-center transition-all z-20 focus:outline-none">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        <?php endif; ?>
    </div>
</section>
