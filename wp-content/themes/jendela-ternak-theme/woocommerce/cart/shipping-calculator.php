<?php
/**
 * Branded shipping calculator.
 *
 * @package JendelaTernakMalang
 * @version 9.7.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_shipping_calculator' );
?>
<form class="woocommerce-shipping-calculator jt-shipping-calculator" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
    <a href="#" class="shipping-calculator-button" aria-expanded="false" aria-controls="shipping-calculator-form" role="button">
        <?php echo esc_html( ! empty( $button_text ) ? $button_text : __( 'Atur alamat pengiriman', 'jendela-ternak' ) ); ?>
    </a>

    <section class="shipping-calculator-form" id="shipping-calculator-form" style="display:none;">
        <?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_country', true ) ) : ?>
            <p class="form-row form-row-wide" id="calc_shipping_country_field">
                <label for="calc_shipping_country"><?php esc_html_e( 'Negara / wilayah', 'jendela-ternak' ); ?></label>
                <select name="calc_shipping_country" id="calc_shipping_country" class="country_to_state country_select" rel="calc_shipping_state">
                    <option value="default"><?php esc_html_e( 'Pilih negara / wilayah', 'jendela-ternak' ); ?></option>
                    <?php foreach ( WC()->countries->get_shipping_countries() as $key => $value ) : ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( WC()->customer->get_shipping_country(), $key ); ?>><?php echo esc_html( $value ); ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
        <?php endif; ?>

        <?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_state', true ) ) : ?>
            <?php $states = WC()->countries->get_states( WC()->customer->get_shipping_country() ); ?>
            <p class="form-row form-row-wide" id="calc_shipping_state_field">
                <label for="calc_shipping_state"><?php esc_html_e( 'Provinsi', 'jendela-ternak' ); ?></label>
                <?php if ( is_array( $states ) && empty( $states ) ) : ?>
                    <input type="hidden" name="calc_shipping_state" id="calc_shipping_state" />
                <?php elseif ( is_array( $states ) ) : ?>
                    <select name="calc_shipping_state" class="state_select" id="calc_shipping_state">
                        <option value=""><?php esc_html_e( 'Pilih provinsi', 'jendela-ternak' ); ?></option>
                        <?php foreach ( $states as $key => $value ) : ?>
                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( WC()->customer->get_shipping_state(), $key ); ?>><?php echo esc_html( $value ); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else : ?>
                    <input type="text" class="input-text" value="<?php echo esc_attr( WC()->customer->get_shipping_state() ); ?>" name="calc_shipping_state" id="calc_shipping_state" />
                <?php endif; ?>
            </p>
        <?php endif; ?>

        <?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_city', true ) ) : ?>
            <p class="form-row form-row-wide" id="calc_shipping_city_field">
                <label for="calc_shipping_city"><?php esc_html_e( 'Kota / kabupaten', 'jendela-ternak' ); ?></label>
                <input type="text" class="input-text" value="<?php echo esc_attr( WC()->customer->get_shipping_city() ); ?>" name="calc_shipping_city" id="calc_shipping_city" />
            </p>
        <?php endif; ?>

        <?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_postcode', true ) ) : ?>
            <p class="form-row form-row-wide" id="calc_shipping_postcode_field">
                <label for="calc_shipping_postcode"><?php esc_html_e( 'Kode pos', 'jendela-ternak' ); ?></label>
                <input type="text" class="input-text" value="<?php echo esc_attr( WC()->customer->get_shipping_postcode() ); ?>" name="calc_shipping_postcode" id="calc_shipping_postcode" />
            </p>
        <?php endif; ?>

        <p><button type="submit" name="calc_shipping" value="1" class="jt-btn jt-btn--outline"><?php esc_html_e( 'Perbarui ongkir', 'jendela-ternak' ); ?></button></p>
        <?php wp_nonce_field( 'woocommerce-shipping-calculator', 'woocommerce-shipping-calculator-nonce' ); ?>
    </section>
</form>
<?php do_action( 'woocommerce_after_shipping_calculator' ); ?>
