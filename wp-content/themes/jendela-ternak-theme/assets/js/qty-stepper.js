/**
 * qty-stepper.js — WooCommerce Quantity Input Enhancement
 * Wraps the native WooCommerce quantity input with styled + / - buttons.
 * Maintains min/max constraints from WooCommerce data attributes.
 */
(function () {
    'use strict';

    function initQtyStepper() {
        var inputs = document.querySelectorAll('form.cart input.qty, form.variations_form input.qty, form.woocommerce-cart-form input.qty');
        inputs.forEach(function (input) {
            if (input.dataset.stepperInit) return;
            input.dataset.stepperInit = 'true';

            var min = parseFloat(input.getAttribute('min')) || 1;
            var max = parseFloat(input.getAttribute('max')) || Infinity;
            var step = parseFloat(input.getAttribute('step')) || 1;

            // Create wrapper
            var wrap = document.createElement('div');
            wrap.className = 'jt-qty-stepper-wrap';

            // Clone input and insert into wrap
            input.parentNode.insertBefore(wrap, input);
            wrap.appendChild(input);

            // Minus button
            var btnMinus = document.createElement('button');
            btnMinus.type = 'button';
            btnMinus.className = 'jt-qty-stepper-btn jt-qty-stepper-btn--minus';
            btnMinus.setAttribute('aria-label', 'Kurangi jumlah');
            btnMinus.innerHTML = '<svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/></svg>';
            wrap.insertBefore(btnMinus, input);

            // Plus button
            var btnPlus = document.createElement('button');
            btnPlus.type = 'button';
            btnPlus.className = 'jt-qty-stepper-btn jt-qty-stepper-btn--plus';
            btnPlus.setAttribute('aria-label', 'Tambah jumlah');
            btnPlus.innerHTML = '<svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>';
            wrap.appendChild(btnPlus);

            function getVal() { return parseFloat(input.value) || min; }
            function setVal(v) {
                var clamped = Math.min(Math.max(v, min), isFinite(max) ? max : 9999);
                input.value = clamped;
                btnMinus.disabled = clamped <= min;
                btnPlus.disabled  = isFinite(max) && clamped >= max;
                // Trigger WooCommerce change events
                input.dispatchEvent(new Event('change', { bubbles: true }));
                if (window.jQuery) jQuery(input).trigger('change');
            }

            // Set initial disabled state
            btnMinus.disabled = getVal() <= min;
            btnPlus.disabled  = isFinite(max) && getVal() >= max;

            btnMinus.addEventListener('click', function () { setVal(getVal() - step); });
            btnPlus.addEventListener('click',  function () { setVal(getVal() + step); });

            // Keep disabled states in sync on manual input
            input.addEventListener('input', function () {
                btnMinus.disabled = getVal() <= min;
                btnPlus.disabled  = isFinite(max) && getVal() >= max;
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initQtyStepper);
    } else {
        initQtyStepper();
    }

    // Re-init after WooCommerce variation updates and cart updates
    if (window.jQuery) {
        jQuery(document).on('found_variation reset_data updated_cart_totals', function () {
            setTimeout(initQtyStepper, 100);
        });
    }
})();
