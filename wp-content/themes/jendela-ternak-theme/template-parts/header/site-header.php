<?php
/**
 * template-parts/header/site-header.php
 * Full site header with logo, search, cart icon, and cart drawer.
 *
 * @package JendelaTernakMalang
 */
defined( 'ABSPATH' ) || exit;

$wa_number = jt_get_setting( 'whatsapp_number', '6281234567890' );
$wa_msg    = urlencode( jt_get_setting( 'whatsapp_message', 'Halo, saya ingin bertanya tentang produk Jendela Ternak Malang.' ) );
$wa_url    = 'https://wa.me/' . esc_attr( $wa_number ) . '?text=' . $wa_msg;

// Theme-panel logo (from jt_theme_settings)
$_jt_logo_url    = esc_url( jt_get_setting( 'logo_img', '' ) );
$_jt_logo_height = (int) jt_get_setting( 'logo_height', 50 );
?>

<div x-data="cartDrawer" id="jt-cart-wrapper">

    <!-- ── HEADER BAR ───────────────────────────────────────── -->
    <header class="jt-header" role="banner">
        <div class="jt-container">
            <div class="jt-header__inner">

                <!-- Logo -->
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="jt-header__logo" aria-label="<?php bloginfo( 'name' ); ?>">
                    <?php if ( $_jt_logo_url ) : ?>
                        <img
                            src="<?php echo $_jt_logo_url; ?>"
                            alt="<?php bloginfo( 'name' ); ?>"
                            style="height: <?php echo $_jt_logo_height; ?>px; width: auto; display: block;"
                        >
                    <?php elseif ( has_custom_logo() ) : ?>
                        <?php the_custom_logo(); ?>
                    <?php endif; ?>
                    
                    <div class="jt-header__brand-text">
                        <div class="jt-site-title"><?php bloginfo( 'name' ); ?></div>
                        <div class="jt-site-tagline"><?php bloginfo( 'description' ); ?></div>
                    </div>
                </a>

                <!-- Search Bar -->
                <div class="jt-header__search">
                    <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <input
                            type="search"
                            id="jt-search-input"
                            name="s"
                            placeholder="<?php esc_attr_e( 'Cari produk ternak...', 'jendela-ternak' ); ?>"
                            value="<?php echo esc_attr( get_search_query() ); ?>"
                            autocomplete="off"
                        >
                        <input type="hidden" name="post_type" value="product">
                        <button type="submit" aria-label="<?php esc_attr_e( 'Cari', 'jendela-ternak' ); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Icon Buttons -->
                <div class="jt-header__icons">

                    <!-- Katalog Produk -->
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" id="jt-catalog-icon" class="jt-header__icon-btn" aria-label="<?php esc_attr_e( 'Katalog Produk', 'jendela-ternak' ); ?>" title="<?php esc_attr_e( 'Katalog Produk', 'jendela-ternak' ); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </a>

                    <!-- Wishlist -->
                    <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'wishlist' ) ); ?>" id="jt-wishlist-icon" class="jt-header__icon-btn" aria-label="<?php esc_attr_e( 'Wishlist', 'jendela-ternak' ); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </a>

                    <!-- Cart Toggle (opens drawer) -->
                    <button
                        id="jt-cart-icon"
                        class="jt-header__icon-btn"
                        @click="toggle()"
                        aria-label="<?php esc_attr_e( 'Keranjang Belanja', 'jendela-ternak' ); ?>"
                        aria-expanded="open"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="jt-cart-badge" data-jt-cart-count x-text="itemCount > 0 ? itemCount : ''" x-show="itemCount > 0">
                            <?php echo WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?>
                        </span>
                    </button>

                    <!-- My Account -->
                    <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>" id="jt-account-icon" class="jt-header__icon-btn" aria-label="<?php esc_attr_e( 'Akun Saya', 'jendela-ternak' ); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </a>

                    <!-- Mobile Menu Toggle -->
                    <button
                        id="jt-menu-toggle"
                        class="jt-header__icon-btn jt-menu-toggle"
                        @click="toggleMobileMenu()"
                        aria-label="<?php esc_attr_e( 'Menu Utama', 'jendela-ternak' ); ?>"
                        aria-expanded="mobileMenuOpen"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                </div><!-- .jt-header__icons -->
            </div><!-- .jt-header__inner -->
        </div><!-- .jt-container -->
    </header><!-- .jt-header -->

    <!-- ── CART DRAWER OVERLAY ──────────────────────────────── -->
    <div
        x-show="open"
        x-cloak
        class="jt-cart-overlay"
        @click="close()"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        aria-hidden="true"
    ></div>

    <!-- ── CART DRAWER PANEL ─────────────────────────────────── -->
    <aside
        id="jt-cart-drawer"
        class="jt-cart-drawer"
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        role="dialog"
        aria-modal="true"
        aria-label="<?php esc_attr_e( 'Keranjang Belanja', 'jendela-ternak' ); ?>"
        style="transform: translateX(100%);"
        :style="open ? 'transform: translateX(0)' : 'transform: translateX(100%)'"
    >
        <!-- Drawer Header -->
        <div class="jt-cart-drawer__header">
            <h2>
                🛒 <?php esc_html_e( 'Keranjang Belanja', 'jendela-ternak' ); ?>
                <span x-show="itemCount > 0" x-text="'(' + itemCount + ')'"></span>
            </h2>
            <button class="jt-cart-drawer__close" @click="close()" aria-label="<?php esc_attr_e( 'Tutup', 'jendela-ternak' ); ?>">&times;</button>
        </div>

        <!-- Drawer Content (refreshed via WC fragments) -->
        <?php jt_render_cart_drawer(); ?>

    </aside><!-- #jt-cart-drawer -->

    <!-- ── OFF-CANVAS MOBILE MENU ────────────────────────────── -->
    <!-- Overlay -->
    <div
        x-show="mobileMenuOpen"
        x-cloak
        class="jt-cart-overlay"
        @click="closeMobileMenu()"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        aria-hidden="true"
    ></div>

    <!-- Panel Drawer -->
    <aside
        id="jt-mobile-menu"
        class="jt-cart-drawer jt-mobile-menu"
        x-show="mobileMenuOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        role="dialog"
        aria-modal="true"
        aria-label="<?php esc_attr_e( 'Menu Navigasi', 'jendela-ternak' ); ?>"
        style="transform: translateX(100%);"
        :style="mobileMenuOpen ? 'transform: translateX(0)' : 'transform: translateX(100%)'"
    >
        <!-- Drawer Header -->
        <div class="jt-cart-drawer__header">
            <h2>
                ☰ <?php esc_html_e( 'Menu Utama', 'jendela-ternak' ); ?>
            </h2>
            <button class="jt-cart-drawer__close" @click="closeMobileMenu()" aria-label="<?php esc_attr_e( 'Tutup', 'jendela-ternak' ); ?>">&times;</button>
        </div>

        <!-- Drawer Content -->
        <div class="jt-mobile-menu__content p-4 space-y-4 overflow-y-auto flex-1 text-xs">
            <ul class="space-y-1">
                <li>
                    <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>" class="flex items-center gap-3 text-sm font-bold text-gray-700 hover:text-[#0B5E34] transition-colors py-3 border-b border-gray-100">
                        <svg class="w-5 h-5 text-[#0B5E34] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span><?php esc_html_e( 'Akun Saya', 'jendela-ternak' ); ?></span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="flex items-center gap-3 text-sm font-bold text-gray-700 hover:text-[#0B5E34] transition-colors py-3 border-b border-gray-100">
                        <svg class="w-5 h-5 text-[#0B5E34] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span><?php esc_html_e( 'Katalog Produk (Shop)', 'jendela-ternak' ); ?></span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'wishlist' ) ); ?>" class="flex items-center gap-3 text-sm font-bold text-gray-700 hover:text-[#0B5E34] transition-colors py-3 border-b border-gray-100">
                        <svg class="w-5 h-5 text-[#0B5E34] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span><?php esc_html_e( 'Wishlist', 'jendela-ternak' ); ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

</div><!-- #jt-cart-wrapper -->

<!-- ── WHATSAPP FLOATING BUTTON ──────────────────────────────── -->
<a
    id="jt-wa-float"
    href="<?php echo esc_url( $wa_url ); ?>"
    class="jt-wa-float"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="<?php esc_attr_e( 'Chat WhatsApp', 'jendela-ternak' ); ?>"
>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="currentColor">
        <path d="M16 0C7.163 0 0 7.163 0 16c0 2.82.737 5.47 2.028 7.773L0 32l8.467-2.018A15.943 15.943 0 0016 32c8.837 0 16-7.163 16-16S24.837 0 16 0zm8.27 22.27c-.34.96-2.01 1.84-2.76 1.96-.75.12-1.68.17-2.71-.17-1.03-.34-2.35-.8-4.03-1.54-3.39-1.5-5.59-4.89-5.76-5.12-.17-.23-1.38-1.84-1.38-3.51s.87-2.49 1.18-2.83c.31-.34.67-.43.9-.43s.45.004.64.012c.2.008.47-.076.74.57.28.66.95 2.31 1.03 2.48.08.17.13.37.03.59-.1.22-.15.35-.29.54-.14.19-.3.43-.43.57-.14.14-.28.3-.12.59.16.29.72 1.19 1.55 1.93 1.07.95 1.97 1.25 2.26 1.39.29.14.46.12.63-.07.17-.19.73-.85.93-1.14.2-.29.4-.24.67-.14.27.1 1.72.81 2.01.96.29.15.48.22.55.34.07.12.07.7-.27 1.66z"/>
    </svg>
</a>
