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
            
            // Map custom emojis to Shopee-style links
            $emoji = '📄';
            switch ( $endpoint ) {
                case 'dashboard':
                    $emoji = '🏠';
                    break;
                case 'orders':
                    $emoji = '📦';
                    break;
                case 'edit-address':
                    $emoji = '📍';
                    break;
                case 'edit-account':
                    $emoji = '⚙️';
                    break;
                case 'customer-logout':
                    $emoji = '🚪';
                    break;
            }
        ?>
            <li class="jt-myaccount-navigation__item <?php echo esc_attr( $class_string ); ?>">
                <a href="<?php echo $url; ?>" class="jt-myaccount-navigation__link">
                    <span class="jt-myaccount-navigation__icon"><?php echo esc_html( $emoji ); ?></span>
                    <span class="jt-myaccount-navigation__label"><?php echo esc_html( $label ); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
