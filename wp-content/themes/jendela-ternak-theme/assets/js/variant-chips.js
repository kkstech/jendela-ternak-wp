/**
 * variant-chips.js — WooCommerce Variation Dropdown to Interactive Chips
 *
 * Automatically converts select dropdowns in single product variation forms
 * into clickable styled chips while maintaining WooCommerce core JS compatibility.
 */

(function () {
    'use strict';

    function initVariantChips() {
        const variationsForm = document.querySelector('form.variations_form');
        if (!variationsForm) return;

        const selects = variationsForm.querySelectorAll('.variations select');
        if (!selects.length) return;

        selects.forEach((select) => {
            if (select.dataset.chipsInitialized) return;
            select.dataset.chipsInitialized = 'true';

            // Hide the default select element
            select.style.display = 'none';

            // Create chips container
            const container = document.createElement('div');
            container.className = 'jt-variant-chips';
            select.parentNode.appendChild(container);

            function updateChips() {
                container.innerHTML = '';
                const currentValue = select.value;

            Array.from(select.options).forEach((option) => {
                if (!option.value) return; // Skip placeholder

                const chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'jt-variant-chip';
                
                // Add color indicator circles for color attributes
                const text = option.textContent.trim();
                const lowerText = text.toLowerCase();
                let colorDot = '';
                
                if (lowerText.includes('hitam')) {
                    colorDot = '<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background-color:#000000;margin-right:6px;border:1px solid rgba(0,0,0,0.1);"></span>';
                } else if (lowerText.includes('navy')) {
                    colorDot = '<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background-color:#0f1d3a;margin-right:6px;border:1px solid rgba(0,0,0,0.1);"></span>';
                } else if (lowerText.includes('pink') || lowerText.includes('dusty')) {
                    colorDot = '<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background-color:#db9797;margin-right:6px;border:1px solid rgba(0,0,0,0.1);"></span>';
                } else if (lowerText.includes('mocca') || lowerText.includes('moka')) {
                    colorDot = '<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background-color:#b89f81;margin-right:6px;border:1px solid rgba(0,0,0,0.1);"></span>';
                }
                
                chip.innerHTML = colorDot + '<span>' + text + '</span>';

                // Apply default styles
                chip.style.padding = '6px 14px';
                chip.style.border = '2px solid var(--color-border)';
                chip.style.borderRadius = 'var(--radius-md)';
                chip.style.fontSize = '13px';
                chip.style.fontWeight = '500';
                chip.style.cursor = 'pointer';
                chip.style.backgroundColor = 'var(--color-white)';
                chip.style.color = 'var(--color-text)';
                chip.style.transition = 'all var(--transition-fast)';
                chip.style.display = 'inline-flex';
                chip.style.alignItems = 'center';

                if (option.value === currentValue) {
                    chip.classList.add('selected');
                    chip.style.backgroundColor = 'var(--color-primary)';
                    chip.style.borderColor = 'var(--color-primary)';
                    chip.style.color = 'var(--color-white)';
                }

                    chip.addEventListener('click', (e) => {
                        e.preventDefault();

                        if (select.value === option.value) {
                            // Toggle off selection if clicked again
                            select.value = '';
                        } else {
                            select.value = option.value;
                        }

                        // Trigger native change event so WooCommerce update scripts run
                        const changeEvent = new Event('change', { bubbles: true });
                        select.dispatchEvent(changeEvent);

                        // Trigger WooCommerce jQuery variation update
                        jQuery(select).trigger('change');
                    });

                    container.appendChild(chip);
                });
            }

            // Store callback on select element for external triggers
            select.updateVariantChips = updateChips;

            // Init rendering
            updateChips();

            // Re-render when WooCommerce updates dropdown options dynamically (e.g. out of stock options)
            select.addEventListener('change', updateChips);
            
            jQuery(select).on('focus reload.wc-variation-form', updateChips);
        });

        // Listen for standard WooCommerce reset variation events
        jQuery(variationsForm).on('woocommerce_update_variation_values reset_data', () => {
            setTimeout(() => {
                const selectsToSync = variationsForm.querySelectorAll('.variations select');
                selectsToSync.forEach((sel) => {
                    if (typeof sel.updateVariantChips === 'function') {
                        sel.updateVariantChips();
                    }
                });
            }, 50);
        });
    }

    // Load on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initVariantChips);
    } else {
        initVariantChips();
    }
})();
