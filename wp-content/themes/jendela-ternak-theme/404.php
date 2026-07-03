<?php
/**
 * 404.php — Template for 404 Not Found Page
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

get_header(); ?>

<main id="main-content" class="jt-main flex items-center justify-center min-h-[70vh] bg-[#F2F2F2] py-12 px-4">
    <div class="max-w-lg w-full bg-white rounded-3xl shadow-xl p-8 md:p-12 text-center border border-gray-100 transform transition-all duration-300 hover:shadow-2xl">
        
        <!-- Animated 404 Graphic -->
        <div class="relative flex items-center justify-center mb-6">
            <div class="absolute w-24 h-24 bg-[#4CAF50] opacity-10 rounded-full animate-ping"></div>
            <div class="relative w-20 h-20 bg-gradient-to-tr from-[#0B5E34] to-[#4CAF50] rounded-2xl flex items-center justify-center shadow-lg transform rotate-6 hover:rotate-0 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- 404 Heading -->
        <h1 class="text-7xl font-extrabold text-[#0B5E34] mb-2 tracking-tight">404</h1>
        <h2 class="text-2xl font-bold text-gray-800 mb-4"><?php esc_html_e( 'Halaman Tidak Ditemukan', 'jendela-ternak' ); ?></h2>
        
        <p class="text-gray-500 text-sm leading-relaxed mb-8 max-w-sm mx-auto">
            <?php esc_html_e( 'Maaf, halaman yang Anda cari tidak dapat ditemukan, telah dihapus, atau sedang dalam perbaikan.', 'jendela-ternak' ); ?>
        </p>

        <!-- Search Form on 404 -->
        <div class="mb-8">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-3"><?php esc_html_e( 'Coba cari di sini', 'jendela-ternak' ); ?></p>
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center bg-[#F9F9F9] border border-gray-200 rounded-full p-1.5 focus-within:ring-2 focus-within:ring-[#0B5E34] focus-within:border-transparent transition-all">
                <input
                    type="search"
                    name="s"
                    placeholder="<?php esc_attr_e( 'Cari produk atau artikel...', 'jendela-ternak' ); ?>"
                    class="flex-1 bg-transparent border-none outline-none px-4 py-2 text-sm text-gray-700 placeholder-gray-400"
                    autocomplete="off"
                    required
                >
                <input type="hidden" name="post_type" value="product">
                <button type="submit" class="bg-[#0B5E34] hover:bg-[#4CAF50] text-white p-2 rounded-full transition-colors flex items-center justify-center w-10 h-10 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Quick Links -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-[#0B5E34] hover:bg-[#073d22] text-white font-bold text-sm rounded-full transition-all shadow-md hover:shadow-lg gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <?php esc_html_e( 'Kembali ke Beranda', 'jendela-ternak' ); ?>
            </a>
            
            <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border-2 border-[#0B5E34] text-[#0B5E34] hover:bg-[#0b5e340a] font-bold text-sm rounded-full transition-all gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <?php esc_html_e( 'Belanja Sekarang', 'jendela-ternak' ); ?>
            </a>
        </div>

    </div>
</main>

<?php get_footer();
