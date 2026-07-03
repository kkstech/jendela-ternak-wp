<?php
/**
 * woocommerce/myaccount/my-account.php
 * Override the main My Account page to display a split Shopee-style sidebar layout.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$edit_profile_url = esc_url( wc_get_endpoint_url( 'edit-account' ) );
$initials = ! empty( $current_user->display_name ) ? strtoupper( substr( $current_user->display_name, 0, 1 ) ) : 'U';

do_action( 'woocommerce_before_my_account' );
?>

<div class="jt-myaccount-layout">
    <!-- Left Column: Sidebar with Profile Card & Navigation Links -->
    <aside class="jt-myaccount-sidebar">
        
        <!-- Profile Card -->
        <div class="jt-myaccount-profile-card">
            <div class="jt-myaccount-avatar">
                <?php 
                $avatar = get_avatar( $current_user->ID, 56 );
                if ( $avatar && strpos( $avatar, 'avatar-default' ) === false ) {
                    echo $avatar;
                } else {
                    echo '<div class="jt-avatar-initials">' . esc_html( $initials ) . '</div>';
                }
                ?>
            </div>
            <div class="jt-myaccount-profile-info">
                <div class="jt-profile-greeting"><?php esc_html_e( 'Halo,', 'jendela-ternak' ); ?></div>
                <h3 class="jt-profile-name text-clamp-1"><?php echo esc_html( $current_user->display_name ); ?></h3>
                <a href="<?php echo $edit_profile_url; ?>" class="jt-profile-edit-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:inline-block; vertical-align:-1px; margin-right:2px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    <?php esc_html_e( 'Ubah Profil', 'jendela-ternak' ); ?>
                </a>
            </div>
        </div>

        <!-- Sidebar Navigation links (loads navigation.php) -->
        <?php do_action( 'woocommerce_account_navigation' ); ?>

    </aside>

    <!-- Right Column: Content Window (Dashboard, Orders, Addresses, etc.) -->
    <div class="jt-myaccount-content">
        <?php
        /**
         * My Account content.
         */
        do_action( 'woocommerce_account_content' );
        ?>
    </div>
</div>
<?php do_action( 'woocommerce_after_my_account' ); ?>
