<?php
/**
 * Checkout coupon form
 *
 * Customized modern layout for checkout coupon/voucher code input.
 *
 * @package JendelaTernakMalang
 * @version 9.8.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! wc_coupons_enabled() ) {
    return;
}
?>

<div class="jt-checkout-coupon-section font-sans mb-4">
    <!-- Trigger Banner -->
    <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-3 text-xs flex items-center justify-between gap-3 text-yellow-800">
        <div class="flex items-center gap-2 font-semibold">
            <span class="text-sm text-yellow-600"><i class="fa-solid fa-ticket"></i></span>
            <span><?php esc_html_e( 'Punya kode kupon atau voucher belanja?', 'jendela-ternak' ); ?></span>
        </div>
        <a href="#" class="showcoupon text-green-700 hover:text-green-800 font-bold underline transition" role="button" aria-controls="woocommerce-checkout-form-coupon">
            <?php esc_html_e( 'Masukkan Kode', 'jendela-ternak' ); ?>
        </a>
    </div>

    <!-- Hidden Coupon Input Form -->
    <form class="checkout_coupon woocommerce-form-coupon mt-3 bg-white p-4 rounded-xl border border-gray-150 shadow-sm space-y-3" method="post" style="display:none" id="woocommerce-checkout-form-coupon">
        
        <p class="text-xs text-gray-400 font-semibold mb-2">
            <?php esc_html_e( 'Masukkan kode kupon Anda di bawah ini untuk mendapatkan potongan harga langsung.', 'jendela-ternak' ); ?>
        </p>

        <div class="flex gap-2">
            <div class="flex-1">
                <label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Kupon:', 'jendela-ternak' ); ?></label>
                <input type="text" name="coupon_code" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-600 font-semibold text-sm transition" placeholder="<?php esc_attr_e( 'Kode Kupon / Voucher', 'jendela-ternak' ); ?>" id="coupon_code" value="" />
            </div>

            <div>
                <button type="submit" class="jt-btn jt-btn--primary bg-green-700 hover:bg-green-800 text-white font-bold py-2 px-4 rounded-lg text-xs transition h-full" name="apply_coupon" value="<?php esc_attr_e( 'Gunakan Kupon', 'jendela-ternak' ); ?>">
                    <?php esc_html_e( 'Gunakan', 'jendela-ternak' ); ?>
                </button>
            </div>
        </div>

        <div class="clear"></div>
    </form>
</div>
