<?php
/**
 * woocommerce/myaccount/navigation.php
 * Override WooCommerce My Account menu list to display vertical lists with matching emojis.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="jt-myaccount-navigation" aria-label="<?php esc_attr_e( 'Menu Akun', 'jendela-ternak' ); ?>">
    <ul class="jt-myaccount-navigation__list">
        <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : 
            $url = esc_url( wc_get_account_endpoint_url( $endpoint ) );
            $classes = wc_get_account_menu_item_classes( $endpoint );
            $class_string = is_array( $classes ) ? implode( ' ', $classes ) : $classes;
            
            // Map custom Font Awesome icons to Shopee-style links
            $icon_class = 'fa-solid fa-file-lines';
            switch ( $endpoint ) {
                case 'dashboard':
                    $icon_class = 'fa-solid fa-house';
                    break;
                case 'orders':
                    $icon_class = 'fa-solid fa-box';
                    break;
                case 'edit-address':
                    $icon_class = 'fa-solid fa-location-dot';
                    break;
                case 'wishlist':
                    $icon_class = 'fa-solid fa-heart';
                    break;
                case 'edit-account':
                    $icon_class = 'fa-solid fa-user-gear';
                    break;
                case 'customer-logout':
                    $icon_class = 'fa-solid fa-right-from-bracket';
                    break;
            }
        ?>
            <li class="jt-myaccount-navigation__item <?php echo esc_attr( $class_string ); ?>">
                <a href="<?php echo $url; ?>" class="jt-myaccount-navigation__link">
                    <span class="jt-myaccount-navigation__icon"><i class="<?php echo esc_attr( $icon_class ); ?>"></i></span>
                    <span class="jt-myaccount-navigation__label"><?php echo esc_html( $label ); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
