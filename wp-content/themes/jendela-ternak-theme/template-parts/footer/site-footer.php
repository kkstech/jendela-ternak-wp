<?php
/**
 * template-parts/footer/site-footer.php
 *
 * @package JendelaTernakMalang
 */
defined( 'ABSPATH' ) || exit;

$wa_number = jt_get_setting( 'whatsapp_number', '6281234567890' );
$wa_msg    = urlencode( jt_get_setting( 'whatsapp_message', 'Halo, saya ingin bertanya.' ) );
$wa_url    = 'https://wa.me/' . esc_attr( $wa_number ) . '?text=' . $wa_msg;
$_footer_logo_url    = esc_url( jt_get_setting( 'logo_img', '' ) );
$_footer_logo_height = (int) jt_get_setting( 'logo_height', 40 );

// New dynamic settings
$_footer_description = jt_get_setting( 'footer_description', 'Toko lengkap pakan ternak, obat hewan, dan alat peternakan terpercaya di Malang.' );
$_footer_copyright   = jt_get_setting( 'footer_copyright', '' );
$_footer_links       = jt_get_setting( 'footer_links_json', array() );
// Fallback default links if none configured
if ( empty( $_footer_links ) ) {
    $_footer_links = array(
        array( 'label' => __( 'Tentang Kami', 'jendela-ternak' ),     'url' => home_url( '/tentang-kami/' ) ),
        array( 'label' => __( 'Blog', 'jendela-ternak' ),              'url' => home_url( '/blog/' ) ),
        array( 'label' => __( 'Kebijakan Privasi', 'jendela-ternak' ), 'url' => home_url( '/kebijakan-privasi/' ) ),
        array( 'label' => __( 'Syarat & Ketentuan', 'jendela-ternak' ),'url' => home_url( '/syarat-ketentuan/' ) ),
        array( 'label' => __( 'Akun Saya', 'jendela-ternak' ),         'url' => wc_get_page_permalink( 'myaccount' ) ),
    );
}
?>

<footer class="jt-footer" role="contentinfo">
    <div class="jt-container">
        <div class="jt-footer__grid">

            <!-- Brand Column -->
            <div class="jt-footer__col">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="display:flex; align-items:center; gap:12px; text-decoration:none; margin-bottom:16px;">
                    <?php if ( $_footer_logo_url ) : ?>
                        <img src="<?php echo $_footer_logo_url; ?>" alt="<?php bloginfo( 'name' ); ?>" style="height:<?php echo $_footer_logo_height; ?>px; width:auto; display:block;">
                    <?php elseif ( has_custom_logo() ) : ?>
                        <div class="jt-footer__custom-logo">
                            <?php the_custom_logo(); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div style="display:flex; flex-direction:column;">
                        <span style="font-size:18px; font-weight:800; color:#fff; line-height:1.2;"><?php bloginfo( 'name' ); ?></span>
                        <span style="font-size:10px; color:rgba(255,255,255,0.7);"><?php bloginfo( 'description' ); ?></span>
                    </div>
                </a>
                <p style="font-size:13px;color:rgba(255,255,255,0.7);line-height:1.7;margin-bottom:16px;">
                    <?php echo esc_html( $_footer_description ); ?>
                </p>
                <!-- Social Media -->
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <?php
                    $social_instagram = jt_get_setting( 'social_instagram', '' );
                    $social_tiktok    = jt_get_setting( 'social_tiktok', '' );
                    $social_shopee    = jt_get_setting( 'social_shopee', '' );
                    $social_facebook  = jt_get_setting( 'social_facebook', '' );
                    $social_tokopedia = jt_get_setting( 'social_tokopedia', '' );
                    $social_lazada    = jt_get_setting( 'social_lazada', '' );

                    if ( $social_instagram ) : ?>
                        <a href="<?php echo esc_url( $social_instagram ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram" style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.8);transition:background 0.2s;" onmouseenter="this.style.background='rgba(255,255,255,0.25)'" onmouseleave="this.style.background='rgba(255,255,255,0.1)'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    <?php endif;
                    if ( $social_tiktok ) : ?>
                        <a href="<?php echo esc_url( $social_tiktok ); ?>" target="_blank" rel="noopener noreferrer" aria-label="TikTok" style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.8);transition:background 0.2s;" onmouseenter="this.style.background='rgba(255,255,255,0.25)'" onmouseleave="this.style.background='rgba(255,255,255,0.1)'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.17-2.86-.74-3.99-1.72-.08-.07-.17-.17-.25-.25v6.07c0 4.19-2.82 8.35-7.46 8.86-5.23.58-10.11-3.37-10.19-8.63-.09-5.46 4.79-10.42 10.23-9.87.83.08 1.65.31 2.4.67V2.62c-1.39-.46-2.88-.36-4.2.27-.47.23-.88.54-1.25.9-.09.08-.18.17-.26.27v4.03c1.39-.77 3.03-.89 4.5-.32.61.24 1.15.63 1.58 1.12v-9.1c.14.07.28.14.42.21z"/></svg>
                        </a>
                    <?php endif;
                    if ( $social_shopee ) : ?>
                        <a href="<?php echo esc_url( $social_shopee ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Shopee" style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.8);transition:background 0.2s;" onmouseenter="this.style.background='rgba(255,255,255,0.25)'" onmouseleave="this.style.background='rgba(255,255,255,0.1)'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M15.9414 17.9633c.229-1.879-.981-3.077-4.1758-4.0969-1.548-.528-2.277-1.22-2.26-2.1719.065-1.056 1.048-1.825 2.352-1.85a5.2898 5.2898 0 0 1 2.8838.89c.116.072.197.06.263-.039.09-.145.315-.494.39-.62.051-.081.061-.187-.068-.281-.185-.1369-.704-.4149-.983-.5319a6.4697 6.4697 0 0 0-2.5118-.514c-1.909.008-3.4129 1.215-3.5389 2.826-.082 1.1629.494 2.1078 1.73 2.8278.262.152 1.6799.716 2.2438.892 1.774.552 2.695 1.5419 2.478 2.6969-.197 1.047-1.299 1.7239-2.818 1.7439-1.2039-.046-2.2878-.537-3.1278-1.19l-.141-.11c-.104-.08-.218-.075-.287.03-.05.077-.376.547-.458.67-.077.108-.035.168.045.234.35.293.817.613 1.134.775a6.7097 6.7097 0 0 0 2.8289.727 4.9048 4.9048 0 0 0 2.0759-.354c1.095-.465 1.8029-1.394 1.9449-2.554zM11.9986 1.4009c-2.068 0-3.7539 1.95-3.8329 4.3899h7.6657c-.08-2.44-1.765-4.3899-3.8328-4.3899zm7.8516 22.5981-.08.001-15.7843-.002c-1.074-.04-1.863-.91-1.971-1.991l-.01-.195L1.298 6.2858a.459.459 0 0 1 .45-.494h4.9748C6.8448 2.568 9.1607 0 11.9996 0c2.8388 0 5.1537 2.5689 5.2757 5.7898h4.9678a.459.459 0 0 1 .458.483l-.773 15.5883-.007.131c-.094 1.094-.979 1.9769-2.0709 2.0059z"/></svg>
                        </a>
                    <?php endif;
                    if ( $social_tokopedia ) : ?>
                        <a href="<?php echo esc_url( $social_tokopedia ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Tokopedia" style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.8);transition:background 0.2s;" onmouseenter="this.style.background='rgba(255,255,255,0.25)'" onmouseleave="this.style.background='rgba(255,255,255,0.1)'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20.09 5.71c-1.27 0-2.54-.07-3.81.04-.32-2.08-2.1-3.67-4.27-3.67S8.06 3.66 7.72 5.73c-1.28-.1-2.54-.01-3.82-.01h-.7v16.21h11.75c3.21 0 5.83-2.62 5.83-5.83V5.71h-.7Zm-8.08-2.46c1.55 0 2.84 1.13 3.11 2.61h-.02c-1.03.09-2.17.34-3.1.81-.99-.5-2-.77-3.11-.85.3-1.46 1.57-2.57 3.12-2.57Zm8.08 12.49v.36c0 2.82-2.31 5.13-5.13 5.13H3.91V6.41c1.2 0 2.5-.05 3.77.01l1.16.09c1.19.13 2.21.4 3.16.95.87-.5 2.02-.77 3.16-.91l1.16-.1c1.24-.07 2.54-.04 3.77-.04v9.32Zm-4.3-7.3a4.12 4.12 0 0 0-3.8 2.54 4.11 4.11 0 1 0-3.8 5.68c.59 0 1.14-.13 1.64-.35l.26.28 1.52 1.61.37.4.37-.4 1.52-1.61.26-.28c.5.22 1.06.35 1.64.35a4.11 4.11 0 1 0 0-8.22Zm-3.32 6.53c-.16-.03-.32-.06-.48-.06s-.32.03-.48.06c.19-.26.36-.54.48-.84.13.3.29.58.48.84Zm-4.28 1c-1.89 0-3.41-1.53-3.41-3.41s1.53-3.41 3.41-3.41 3.41 1.53 3.41 3.41-1.53 3.41-3.41 3.41Zm3.8 1.89-1.52-1.61c.32-.55.92-.83 1.52-.82.6 0 1.2.27 1.52.82l-1.52 1.61Zm3.8-1.89c-1.88 0-3.41-1.53-3.41-3.41s1.53-3.41 3.41-3.41 3.41 1.53 3.41 3.41-1.53 3.41-3.41 3.41Zm-4.96-3.33c0 1.22-.99 2.2-2.2 2.2s-2.2-.99-2.2-2.2c0-.41.11-.79.3-1.11a1.06 1.06 0 0 0 2.04-.4c0-.26-.1-.5-.25-.69h.11c1.22 0 2.2.99 2.2 2.2Zm4.53-2.2h-.11c.16.18.25.42.25.69 0 .59-.47 1.06-1.06 1.06-.44 0-.83-.27-.98-.66a2.2 2.2 0 0 0 1.9 3.31c1.21 0 2.2-.99 2.2-2.2s-.99-2.2-2.2-2.2Z"/></svg>
                        </a>
                    <?php endif;
                    if ( $social_lazada ) : ?>
                        <a href="<?php echo esc_url( $social_lazada ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Lazada" style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.8);transition:background 0.2s;" onmouseenter="this.style.background='rgba(255,255,255,0.25)'" onmouseleave="this.style.background='rgba(255,255,255,0.1)'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                        </a>
                    <?php endif;
                    if ( $social_facebook ) : ?>
                        <a href="<?php echo esc_url( $social_facebook ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook" style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.8);transition:background 0.2s;" onmouseenter="this.style.background='rgba(255,255,255,0.25)'" onmouseleave="this.style.background='rgba(255,255,255,0.1)'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                    <?php endif;

                    if ( $wa_url ) : ?>
                        <a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" style="width:36px;height:36px;border-radius:50%;background:#25D366;display:flex;align-items:center;justify-content:center;color:#fff;">
                            <svg width="18" height="18" viewBox="0 0 32 32" fill="currentColor"><path d="M16 0C7.163 0 0 7.163 0 16c0 2.82.737 5.47 2.028 7.773L0 32l8.467-2.018A15.943 15.943 0 0016 32c8.837 0 16-7.163 16-16S24.837 0 16 0zm8.27 22.27c-.34.96-2.01 1.84-2.76 1.96-.75.12-1.68.17-2.71-.17-1.03-.34-2.35-.8-4.03-1.54-3.39-1.5-5.59-4.89-5.76-5.12-.17-.23-1.38-1.84-1.38-3.51s.87-2.49 1.18-2.83c.31-.34.67-.43.9-.43s.45.004.64.012c.2.008.47-.076.74.57.28.66.95 2.31 1.03 2.48.08.17.13.37.03.59-.1.22-.15.35-.29.54-.14.19-.3.43-.43.57-.14.14-.28.3-.12.59.16.29.72 1.19 1.55 1.93 1.07.95 1.97 1.25 2.26 1.39.29.14.46.12.63-.07.17-.19.73-.85.93-1.14.2-.29.4-.24.67-.14.27.1 1.72.81 2.01.96.29.15.48.22.55.34.07.12.07.7-.27 1.66z"/></svg>
                        </a>
                    <?php endif; ?>
                </div>
                <!-- WA Hours -->
                <p style="font-size:12px;color:rgba(255,255,255,0.5);margin-top:12px;">
                    <?php esc_html_e( 'CS WhatsApp: 08.00–17.00 WIB', 'jendela-ternak' ); ?>
                </p>
            </div>

            <!-- Kategori Column -->
            <div class="jt-footer__col">
                <h3 class="jt-footer__col-title"><?php esc_html_e( 'Kategori', 'jendela-ternak' ); ?></h3>
                <ul>
                    <?php
                    $cat_args = [
                        'taxonomy'   => 'product_cat',
                        'number'     => 6,
                        'hide_empty' => true,
                        'orderby'    => 'count',
                        'order'      => 'DESC',
                        'exclude'    => [ get_option( 'default_product_cat' ) ],
                    ];
                    $categories = get_terms( $cat_args );
                    if ( ! is_wp_error( $categories ) ) :
                        foreach ( $categories as $cat ) : ?>
                            <li><a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a></li>
                        <?php endforeach;
                    endif;
                    ?>
                </ul>
            </div>

            <!-- Info Column (dynamic from admin panel) -->
            <div class="jt-footer__col">
                <h3 class="jt-footer__col-title"><?php esc_html_e( 'Informasi', 'jendela-ternak' ); ?></h3>
                <ul>
                    <?php foreach ( $_footer_links as $link_item ) :
                        if ( empty( $link_item['label'] ) ) continue;
                        $link_url = ! empty( $link_item['url'] ) ? $link_item['url'] : '#';
                        // Support both relative paths and full URLs
                        if ( strpos( $link_url, 'http' ) !== 0 ) {
                            $link_url = home_url( $link_url );
                        }
                    ?>
                        <li><a href="<?php echo esc_url( $link_url ); ?>"><?php echo esc_html( $link_item['label'] ); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Bantuan Column -->
            <div class="jt-footer__col">
                <h3 class="jt-footer__col-title"><?php esc_html_e( 'Bantuan', 'jendela-ternak' ); ?></h3>
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/cara-pemesanan/' ) ); ?>"><?php esc_html_e( 'Cara Pemesanan', 'jendela-ternak' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/pengiriman/' ) ); ?>"><?php esc_html_e( 'Pengiriman', 'jendela-ternak' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/pengembalian/' ) ); ?>"><?php esc_html_e( 'Pengembalian', 'jendela-ternak' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'jendela-ternak' ); ?></a></li>
                    <li><a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Hubungi CS', 'jendela-ternak' ); ?></a></li>
                </ul>
            </div>

        </div><!-- .jt-footer__grid -->

        <div class="jt-footer__bottom">
            <p>
                <?php
                if ( $_footer_copyright ) {
                    echo wp_kses_post( $_footer_copyright );
                } else {
                    echo '&copy; ' . date( 'Y' ) . ' <a href="' . esc_url( home_url( '/' ) ) . '" style="color:var(--color-accent);">' . esc_html( get_bloginfo( 'name' ) ) . '</a>. ';
                    esc_html_e( 'Semua hak dilindungi. Dibuat oleh', 'jendela-ternak' );
                    echo ' <a href="https://kitaweb.id" target="_blank" rel="noopener noreferrer" style="color:var(--color-accent);">Kitaweb</a>.';
                }
                ?>
            </p>
        </div>
    </div><!-- .jt-container -->
</footer><!-- .jt-footer -->
