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
?>

<footer class="jt-footer" role="contentinfo">
    <div class="jt-container">
        <div class="jt-footer__grid">

            <!-- Brand Column -->
            <div class="jt-footer__col">
                <?php if ( $_footer_logo_url ) : ?>
                    <div style="margin-bottom:12px;">
                        <img src="<?php echo $_footer_logo_url; ?>" alt="<?php bloginfo( 'name' ); ?>" style="height:<?php echo $_footer_logo_height; ?>px;width:auto;filter:brightness(0) invert(1);">
                    </div>
                <?php elseif ( has_custom_logo() ) : ?>
                    <div style="margin-bottom:12px;filter:brightness(0) invert(1);"><?php the_custom_logo(); ?></div>
                <?php else : ?>
                    <div style="font-size:20px;font-weight:800;color:#fff;margin-bottom:8px;"><?php bloginfo( 'name' ); ?></div>
                <?php endif; ?>
                <p style="font-size:13px;color:rgba(255,255,255,0.7);line-height:1.7;margin-bottom:16px;">
                    <?php esc_html_e( 'Toko lengkap pakan ternak, obat hewan, dan alat peternakan terpercaya di Malang.', 'jendela-ternak' ); ?>
                </p>
                <!-- Social Media -->
                <div style="display:flex;gap:10px;">
                    <a href="#" aria-label="Instagram" style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.8);transition:background 0.2s;" onmouseenter="this.style.background='rgba(255,255,255,0.25)'" onmouseleave="this.style.background='rgba(255,255,255,0.1)'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" style="width:36px;height:36px;border-radius:50%;background:#25D366;display:flex;align-items:center;justify-content:center;color:#fff;">
                        <svg width="18" height="18" viewBox="0 0 32 32" fill="currentColor"><path d="M16 0C7.163 0 0 7.163 0 16c0 2.82.737 5.47 2.028 7.773L0 32l8.467-2.018A15.943 15.943 0 0016 32c8.837 0 16-7.163 16-16S24.837 0 16 0zm8.27 22.27c-.34.96-2.01 1.84-2.76 1.96-.75.12-1.68.17-2.71-.17-1.03-.34-2.35-.8-4.03-1.54-3.39-1.5-5.59-4.89-5.76-5.12-.17-.23-1.38-1.84-1.38-3.51s.87-2.49 1.18-2.83c.31-.34.67-.43.9-.43s.45.004.64.012c.2.008.47-.076.74.57.28.66.95 2.31 1.03 2.48.08.17.13.37.03.59-.1.22-.15.35-.29.54-.14.19-.3.43-.43.57-.14.14-.28.3-.12.59.16.29.72 1.19 1.55 1.93 1.07.95 1.97 1.25 2.26 1.39.29.14.46.12.63-.07.17-.19.73-.85.93-1.14.2-.29.4-.24.67-.14.27.1 1.72.81 2.01.96.29.15.48.22.55.34.07.12.07.7-.27 1.66z"/></svg>
                    </a>
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

            <!-- Info Column -->
            <div class="jt-footer__col">
                <h3 class="jt-footer__col-title"><?php esc_html_e( 'Informasi', 'jendela-ternak' ); ?></h3>
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/tentang-kami/' ) ); ?>"><?php esc_html_e( 'Tentang Kami', 'jendela-ternak' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Blog', 'jendela-ternak' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/kebijakan-privasi/' ) ); ?>"><?php esc_html_e( 'Kebijakan Privasi', 'jendela-ternak' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/syarat-ketentuan/' ) ); ?>"><?php esc_html_e( 'Syarat & Ketentuan', 'jendela-ternak' ); ?></a></li>
                    <li><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Akun Saya', 'jendela-ternak' ); ?></a></li>
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
                &copy; <?php echo date( 'Y' ); ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:var(--color-accent);"><?php bloginfo( 'name' ); ?></a>.
                <?php esc_html_e( 'Semua hak dilindungi. Dibuat oleh', 'jendela-ternak' ); ?>
                <a href="https://kitaweb.id" target="_blank" rel="noopener noreferrer" style="color:var(--color-accent);">Kitaweb</a>.
            </p>
        </div>
    </div><!-- .jt-container -->
</footer><!-- .jt-footer -->
