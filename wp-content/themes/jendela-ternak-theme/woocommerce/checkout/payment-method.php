<?php
/**
 * Output a single payment method
 *
 * Customized layout for a single payment method selection block.
 *
 * @package JendelaTernakMalang
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?> font-sans border border-gray-200 hover:border-green-300 rounded-lg p-3 transition duration-150 bg-white">
    <div class="flex items-center gap-3">
        <input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio rounded-full text-green-700 focus:ring-green-600 w-4 h-4 cursor-pointer" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />

        <label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>" class="flex items-center justify-between flex-1 font-bold text-xs text-green-900 cursor-pointer select-none">
            <span class="payment-title"><?php echo $gateway->get_title(); /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></span>
            <span class="payment-icon text-right"><?php echo $gateway->get_icon(); /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></span>
        </label>
    </div>
    
    <?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
        <!-- Collaspible Payment Description Box -->
        <div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?> mt-3 bg-gray-50 border border-gray-150 p-4 rounded-lg text-xs text-gray-500 leading-relaxed font-medium" <?php if ( ! $gateway->chosen ) : ?>style="display:none;"<?php endif; ?>>
            <?php $gateway->payment_fields(); ?>
        </div>
    <?php endif; ?>
</li>
