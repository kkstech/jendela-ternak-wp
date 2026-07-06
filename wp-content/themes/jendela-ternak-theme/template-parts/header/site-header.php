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

// Announcement bar
$_jt_announcement_on   = jt_get_setting( 'toggle_announcement', '0' ) === '1';
$_jt_announcement_text = jt_get_setting( 'announcement_text', '' );
$_jt_announcement_link = jt_get_setting( 'announcement_link', '' );

// Sticky header
$_jt_sticky = jt_get_setting( 'toggle_sticky_header', '0' ) === '1';

// Tagline override
$_jt_tagline = jt_get_setting( 'logo_tagline_override', '' );
?>

<div x-data="cartDrawer" id="jt-cart-wrapper">

    <!-- ── ANNOUNCEMENT BAR ─────────────────────────────────── -->
    <?php if ( $_jt_announcement_on && $_jt_announcement_text ) : ?>
    <div class="jt-announcement-bar" role="note">
        <?php if ( $_jt_announcement_link ) : ?>
            <a href="<?php echo esc_url( $_jt_announcement_link ); ?>" class="jt-announcement-bar__link"><?php echo esc_html( $_jt_announcement_text ); ?></a>
        <?php else : ?>
            <span><?php echo esc_html( $_jt_announcement_text ); ?></span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ── HEADER BAR ───────────────────────────────────────── -->
    <header class="jt-header<?php echo $_jt_sticky ? ' jt-header--sticky' : ''; ?>" role="banner">
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
                        <div class="jt-site-tagline"><?php echo esc_html( $_jt_tagline ?: get_bloginfo( 'description' ) ); ?></div>
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

                    <!-- Social Media Links -->
                    <?php
                    $social_instagram = jt_get_setting( 'social_instagram', '' );
                    $social_tiktok    = jt_get_setting( 'social_tiktok', '' );
                    $social_shopee    = jt_get_setting( 'social_shopee', '' );
                    $social_facebook  = jt_get_setting( 'social_facebook', '' );
                    $social_tokopedia = jt_get_setting( 'social_tokopedia', '' );
                    $social_lazada    = jt_get_setting( 'social_lazada', '' );

                    if ( $social_instagram || $social_tiktok || $social_shopee || $social_facebook || $social_tokopedia || $social_lazada ) : ?>
                        <div x-data="{ open: false }" class="jt-social-dropdown jt-header__social-icon" @click.away="open = false" style="position: relative; display: flex; align-items: center;">
                            <button 
                                @click="open = !open" 
                                class="jt-header__icon-btn" 
                                aria-label="<?php esc_attr_e( 'Media Sosial', 'jendela-ternak' ); ?>" 
                                title="<?php esc_attr_e( 'Hubungi & Ikuti Kami', 'jendela-ternak' ); ?>"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                </svg>
                            </button>
                            
                            <div 
                                x-show="open" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="jt-social-dropdown__menu"
                                style="display: none; position: absolute; right: 0; top: 100%; margin-top: 8px; background: #fff; border: 1px solid var(--color-border); border-radius: var(--radius-md); box-shadow: var(--shadow-md); z-index: 210; min-width: 160px; padding: 6px 0;"
                            >
                                <?php if ( $social_instagram ) : ?>
                                    <a href="<?php echo esc_url( $social_instagram ); ?>" target="_blank" rel="noopener noreferrer" class="jt-social-dropdown__item" style="display: flex; align-items: center; gap: 10px; padding: 10px 16px; font-size: 13px; color: var(--color-text); transition: background 0.15s; font-weight: 500;" onmouseenter="this.style.background='#F9FAFB'" onmouseleave="this.style.background='transparent'">
                                        <span style="color: #E1306C; display: flex; align-items: center;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                        </span>
                                        <span>Instagram</span>
                                    </a>
                                <?php endif; ?>
                                <?php if ( $social_tiktok ) : ?>
                                    <a href="<?php echo esc_url( $social_tiktok ); ?>" target="_blank" rel="noopener noreferrer" class="jt-social-dropdown__item" style="display: flex; align-items: center; gap: 10px; padding: 10px 16px; font-size: 13px; color: var(--color-text); transition: background 0.15s; font-weight: 500;" onmouseenter="this.style.background='#F9FAFB'" onmouseleave="this.style.background='transparent'">
                                        <span style="color: #000000; display: flex; align-items: center;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.17-2.86-.74-3.99-1.72-.08-.07-.17-.17-.25-.25v6.07c0 4.19-2.82 8.35-7.46 8.86-5.23.58-10.11-3.37-10.19-8.63-.09-5.46 4.79-10.42 10.23-9.87.83.08 1.65.31 2.4.67V2.62c-1.39-.46-2.88-.36-4.2.27-.47.23-.88.54-1.25.9-.09.08-.18.17-.26.27v4.03c1.39-.77 3.03-.89 4.5-.32.61.24 1.15.63 1.58 1.12v-9.1c.14.07.28.14.42.21z"/></svg>
                                        </span>
                                        <span>TikTok</span>
                                    </a>
                                <?php endif; ?>
                                <?php if ( $social_shopee ) : ?>
                                    <a href="<?php echo esc_url( $social_shopee ); ?>" target="_blank" rel="noopener noreferrer" class="jt-social-dropdown__item" style="display: flex; align-items: center; gap: 10px; padding: 10px 16px; font-size: 13px; color: var(--color-text); transition: background 0.15s; font-weight: 500;" onmouseenter="this.style.background='#F9FAFB'" onmouseleave="this.style.background='transparent'">
                                        <span style="color: #EE4D2D; display: flex; align-items: center;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M15.9414 17.9633c.229-1.879-.981-3.077-4.1758-4.0969-1.548-.528-2.277-1.22-2.26-2.1719.065-1.056 1.048-1.825 2.352-1.85a5.2898 5.2898 0 0 1 2.8838.89c.116.072.197.06.263-.039.09-.145.315-.494.39-.62.051-.081.061-.187-.068-.281-.185-.1369-.704-.4149-.983-.5319a6.4697 6.4697 0 0 0-2.5118-.514c-1.909.008-3.4129 1.215-3.5389 2.826-.082 1.1629.494 2.1078 1.73 2.8278.262.152 1.6799.716 2.2438.892 1.774.552 2.695 1.5419 2.478 2.6969-.197 1.047-1.299 1.7239-2.818 1.7439-1.2039-.046-2.2878-.537-3.1278-1.19l-.141-.11c-.104-.08-.218-.075-.287.03-.05.077-.376.547-.458.67-.077.108-.035.168.045.234.35.293.817.613 1.134.775a6.7097 6.7097 0 0 0 2.8289.727 4.9048 4.9048 0 0 0 2.0759-.354c1.095-.465 1.8029-1.394 1.9449-2.554zM11.9986 1.4009c-2.068 0-3.7539 1.95-3.8329 4.3899h7.6657c-.08-2.44-1.765-4.3899-3.8328-4.3899zm7.8516 22.5981-.08.001-15.7843-.002c-1.074-.04-1.863-.91-1.971-1.991l-.01-.195L1.298 6.2858a.459.459 0 0 1 .45-.494h4.9748C6.8448 2.568 9.1607 0 11.9996 0c2.8388 0 5.1537 2.5689 5.2757 5.7898h4.9678a.459.459 0 0 1 .458.483l-.773 15.5883-.007.131c-.094 1.094-.979 1.9769-2.0709 2.0059z"/></svg>
                                        </span>
                                        <span>Shopee</span>
                                    </a>
                                <?php endif; ?>
                                <?php if ( $social_tokopedia ) : ?>
                                    <a href="<?php echo esc_url( $social_tokopedia ); ?>" target="_blank" rel="noopener noreferrer" class="jt-social-dropdown__item" style="display: flex; align-items: center; gap: 10px; padding: 10px 16px; font-size: 13px; color: var(--color-text); transition: background 0.15s; font-weight: 500;" onmouseenter="this.style.background='#F9FAFB'" onmouseleave="this.style.background='transparent'">
                                        <span style="color: #03AC0E; display: flex; align-items: center;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20.09 5.71c-1.27 0-2.54-.07-3.81.04-.32-2.08-2.1-3.67-4.27-3.67S8.06 3.66 7.72 5.73c-1.28-.1-2.54-.01-3.82-.01h-.7v16.21h11.75c3.21 0 5.83-2.62 5.83-5.83V5.71h-.7Zm-8.08-2.46c1.55 0 2.84 1.13 3.11 2.61h-.02c-1.03.09-2.17.34-3.1.81-.99-.5-2-.77-3.11-.85.3-1.46 1.57-2.57 3.12-2.57Zm8.08 12.49v.36c0 2.82-2.31 5.13-5.13 5.13H3.91V6.41c1.2 0 2.5-.05 3.77.01l1.16.09c1.19.13 2.21.4 3.16.95.87-.5 2.02-.77 3.16-.91l1.16-.1c1.24-.07 2.54-.04 3.77-.04v9.32Zm-4.3-7.3a4.12 4.12 0 0 0-3.8 2.54 4.11 4.11 0 1 0-3.8 5.68c.59 0 1.14-.13 1.64-.35l.26.28 1.52 1.61.37.4.37-.4 1.52-1.61.26-.28c.5.22 1.06.35 1.64.35a4.11 4.11 0 1 0 0-8.22Zm-3.32 6.53c-.16-.03-.32-.06-.48-.06s-.32.03-.48.06c.19-.26.36-.54.48-.84.13.3.29.58.48.84Zm-4.28 1c-1.89 0-3.41-1.53-3.41-3.41s1.53-3.41 3.41-3.41 3.41 1.53 3.41 3.41-1.53 3.41-3.41 3.41Zm3.8 1.89-1.52-1.61c.32-.55.92-.83 1.52-.82.6 0 1.2.27 1.52.82l-1.52 1.61Zm3.8-1.89c-1.88 0-3.41-1.53-3.41-3.41s1.53-3.41 3.41-3.41 3.41 1.53 3.41 3.41-1.53 3.41-3.41 3.41Zm-4.96-3.33c0 1.22-.99 2.2-2.2 2.2s-2.2-.99-2.2-2.2c0-.41.11-.79.3-1.11a1.06 1.06 0 0 0 2.04-.4c0-.26-.1-.5-.25-.69h.11c1.22 0 2.2.99 2.2 2.2Zm4.53-2.2h-.11c.16.18.25.42.25.69 0 .59-.47 1.06-1.06 1.06-.44 0-.83-.27-.98-.66a2.2 2.2 0 0 0 1.9 3.31c1.21 0 2.2-.99 2.2-2.2s-.99-2.2-2.2-2.2Z"/></svg>
                                        </span>
                                        <span>Tokopedia</span>
                                    </a>
                                <?php endif; ?>
                                <?php if ( $social_lazada ) : ?>
                                    <a href="<?php echo esc_url( $social_lazada ); ?>" target="_blank" rel="noopener noreferrer" class="jt-social-dropdown__item" style="display: flex; align-items: center; gap: 10px; padding: 10px 16px; font-size: 13px; color: var(--color-text); transition: background 0.15s; font-weight: 500;" onmouseenter="this.style.background='#F9FAFB'" onmouseleave="this.style.background='transparent'">
                                        <span style="color: #FE4A49; display: flex; align-items: center;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                                        </span>
                                        <span>Lazada</span>
                                    </a>
                                <?php endif; ?>
                                <?php if ( $social_facebook ) : ?>
                                    <a href="<?php echo esc_url( $social_facebook ); ?>" target="_blank" rel="noopener noreferrer" class="jt-social-dropdown__item" style="display: flex; align-items: center; gap: 10px; padding: 10px 16px; font-size: 13px; color: var(--color-text); transition: background 0.15s; font-weight: 500;" onmouseenter="this.style.background='#F9FAFB'" onmouseleave="this.style.background='transparent'">
                                        <span style="color: #1877F2; display: flex; align-items: center;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                        </span>
                                        <span>Facebook</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

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

            <!-- Mobile Social Media Links -->
            <?php if ( $social_instagram || $social_tiktok || $social_shopee || $social_facebook || $social_tokopedia || $social_lazada || ! empty( $wa_number ) ) : ?>
                <div class="pt-6 mt-6 border-t border-gray-100">
                    <p class="text-center font-bold text-gray-400 mb-3 text-[10px] uppercase tracking-wider"><?php esc_html_e( 'Ikuti & Hubungi Kami', 'jendela-ternak' ); ?></p>
                    <div class="flex justify-center gap-3 flex-wrap">
                        <?php if ( $social_instagram ) : ?>
                            <a href="<?php echo esc_url( $social_instagram ); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center text-gray-600 hover:bg-[#E1306C] hover:text-white transition-all duration-300 shadow-sm" aria-label="Instagram">
                                <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                        <?php endif; ?>
                        <?php if ( $social_tiktok ) : ?>
                            <a href="<?php echo esc_url( $social_tiktok ); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center text-gray-600 hover:bg-black hover:text-white transition-all duration-300 shadow-sm" aria-label="TikTok">
                                <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.17-2.86-.74-3.99-1.72-.08-.07-.17-.17-.25-.25v6.07c0 4.19-2.82 8.35-7.46 8.86-5.23.58-10.11-3.37-10.19-8.63-.09-5.46 4.79-10.42 10.23-9.87.83.08 1.65.31 2.4.67V2.62c-1.39-.46-2.88-.36-4.2.27-.47.23-.88.54-1.25.9-.09.08-.18.17-.26.27v4.03c1.39-.77 3.03-.89 4.5-.32.61.24 1.15.63 1.58 1.12v-9.1c.14.07.28.14.42.21z"/></svg>
                            </a>
                        <?php endif; ?>
                        <?php if ( $social_shopee ) : ?>
                            <a href="<?php echo esc_url( $social_shopee ); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center text-gray-600 hover:bg-[#EE4D2D] hover:text-white transition-all duration-300 shadow-sm" aria-label="Shopee">
                                <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="currentColor"><path d="M15.9414 17.9633c.229-1.879-.981-3.077-4.1758-4.0969-1.548-.528-2.277-1.22-2.26-2.1719.065-1.056 1.048-1.825 2.352-1.85a5.2898 5.2898 0 0 1 2.8838.89c.116.072.197.06.263-.039.09-.145.315-.494.39-.62.051-.081.061-.187-.068-.281-.185-.1369-.704-.4149-.983-.5319a6.4697 6.4697 0 0 0-2.5118-.514c-1.909.008-3.4129 1.215-3.5389 2.826-.082 1.1629.494 2.1078 1.73 2.8278.262.152 1.6799.716 2.2438.892 1.774.552 2.695 1.5419 2.478 2.6969-.197 1.047-1.299 1.7239-2.818 1.7439-1.2039-.046-2.2878-.537-3.1278-1.19l-.141-.11c-.104-.08-.218-.075-.287.03-.05.077-.376.547-.458.67-.077.108-.035.168.045.234.35.293.817.613 1.134.775a6.7097 6.7097 0 0 0 2.8289.727 4.9048 4.9048 0 0 0 2.0759-.354c1.095-.465 1.8029-1.394 1.9449-2.554zM11.9986 1.4009c-2.068 0-3.7539 1.95-3.8329 4.3899h7.6657c-.08-2.44-1.765-4.3899-3.8328-4.3899zm7.8516 22.5981-.08.001-15.7843-.002c-1.074-.04-1.863-.91-1.971-1.991l-.01-.195L1.298 6.2858a.459.459 0 0 1 .45-.494h4.9748C6.8448 2.568 9.1607 0 11.9996 0c2.8388 0 5.1537 2.5689 5.2757 5.7898h4.9678a.459.459 0 0 1 .458.483l-.773 15.5883-.007.131c-.094 1.094-.979 1.9769-2.0709 2.0059z"/></svg>
                            </a>
                        <?php endif; ?>
                        <?php if ( $social_tokopedia ) : ?>
                            <a href="<?php echo esc_url( $social_tokopedia ); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center text-gray-600 hover:bg-[#03AC0E] hover:text-white transition-all duration-300 shadow-sm" aria-label="Tokopedia">
                                <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="currentColor"><path d="M20.09 5.71c-1.27 0-2.54-.07-3.81.04-.32-2.08-2.1-3.67-4.27-3.67S8.06 3.66 7.72 5.73c-1.28-.1-2.54-.01-3.82-.01h-.7v16.21h11.75c3.21 0 5.83-2.62 5.83-5.83V5.71h-.7Zm-8.08-2.46c1.55 0 2.84 1.13 3.11 2.61h-.02c-1.03.09-2.17.34-3.1.81-.99-.5-2-.77-3.11-.85.3-1.46 1.57-2.57 3.12-2.57Zm8.08 12.49v.36c0 2.82-2.31 5.13-5.13 5.13H3.91V6.41c1.2 0 2.5-.05 3.77.01l1.16.09c1.19.13 2.21.4 3.16.95.87-.5 2.02-.77 3.16-.91l1.16-.1c1.24-.07 2.54-.04 3.77-.04v9.32Zm-4.3-7.3a4.12 4.12 0 0 0-3.8 2.54 4.11 4.11 0 1 0-3.8 5.68c.59 0 1.14-.13 1.64-.35l.26.28 1.52 1.61.37.4.37-.4 1.52-1.61.26-.28c.5.22 1.06.35 1.64.35a4.11 4.11 0 1 0 0-8.22Zm-3.32 6.53c-.16-.03-.32-.06-.48-.06s-.32.03-.48.06c.19-.26.36-.54.48-.84.13.3.29.58.48.84Zm-4.28 1c-1.89 0-3.41-1.53-3.41-3.41s1.53-3.41 3.41-3.41 3.41 1.53 3.41 3.41-1.53 3.41-3.41 3.41Zm3.8 1.89-1.52-1.61c.32-.55.92-.83 1.52-.82.6 0 1.2.27 1.52.82l-1.52 1.61Zm3.8-1.89c-1.88 0-3.41-1.53-3.41-3.41s1.53-3.41 3.41-3.41 3.41 1.53 3.41 3.41-1.53 3.41-3.41 3.41Zm-4.96-3.33c0 1.22-.99 2.2-2.2 2.2s-2.2-.99-2.2-2.2c0-.41.11-.79.3-1.11a1.06 1.06 0 0 0 2.04-.4c0-.26-.1-.5-.25-.69h.11c1.22 0 2.2.99 2.2 2.2Zm4.53-2.2h-.11c.16.18.25.42.25.69 0 .59-.47 1.06-1.06 1.06-.44 0-.83-.27-.98-.66a2.2 2.2 0 0 0 1.9 3.31c1.21 0 2.2-.99 2.2-2.2s-.99-2.2-2.2-2.2Z"/></svg>
                            </a>
                        <?php endif; ?>
                        <?php if ( $social_lazada ) : ?>
                            <a href="<?php echo esc_url( $social_lazada ); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center text-gray-600 hover:bg-[#FE4A49] hover:text-white transition-all duration-300 shadow-sm" aria-label="Lazada">
                                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                            </a>
                        <?php endif; ?>
                        <?php if ( $social_facebook ) : ?>
                            <a href="<?php echo esc_url( $social_facebook ); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center text-gray-600 hover:bg-[#1877F2] hover:text-white transition-all duration-300 shadow-sm" aria-label="Facebook">
                                <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                        <?php endif; ?>
                        <?php if ( $wa_url ) : ?>
                            <a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center text-gray-600 hover:bg-[#25D366] hover:text-white transition-all duration-300 shadow-sm" aria-label="WhatsApp">
                                <svg class="w-4.5 h-4.5" viewBox="0 0 32 32" fill="currentColor"><path d="M16 0C7.163 0 0 7.163 0 16c0 2.82.737 5.47 2.028 7.773L0 32l8.467-2.018A15.943 15.943 0 0016 32c8.837 0 16-7.163 16-16S24.837 0 16 0zm8.27 22.27c-.34.96-2.01 1.84-2.76 1.96-.75.12-1.68.17-2.71-.17-1.03-.34-2.35-.8-4.03-1.54-3.39-1.5-5.59-4.89-5.76-5.12-.17-.23-1.38-1.84-1.38-3.51s.87-2.49 1.18-2.83c.31-.34.67-.43.9-.43s.45.004.64.012c.2.008.47-.076.74.57.28.66.95 2.31 1.03 2.48.08.17.13.37.03.59-.1.22-.15.35-.29.54-.14.19-.3.43-.43.57-.14.14-.28.3-.12.59.16.29.72 1.19 1.55 1.93 1.07.95 1.97 1.25 2.26 1.39.29.14.46.12.63-.07.17-.19.73-.85.93-1.14.2-.29.4-.24.67-.14.27.1 1.72.81 2.01.96.29.15.48.22.55.34.07.12.07.7-.27 1.66z"/></svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
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
