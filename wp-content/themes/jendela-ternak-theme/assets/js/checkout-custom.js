/**
 * assets/js/checkout-custom.js
 * Custom checkout interactive logic for Shopee-style layout.
 * Toggles address views, maps form fields, handles inline coupon submission,
 * and updates mobile sticky checkout bar.
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        if ($('.jt-shopee-checkout-wrapper').length === 0) {
            return;
        }

        // --- 1. DYNAMIC ADDRESS SUMMARY SYNC & TOGGLE ---
        function updateAddressSummary() {
            var firstName = $('#billing_first_name').val() || '';
            var lastName = $('#billing_last_name').val() || '';
            var phone = $('#billing_phone').val() || '';
            var addr1 = $('#billing_address_1').val() || '';
            var addr2 = $('#billing_address_2').val() || '';
            var city = $('#billing_city').val() || '';
            var stateVal = $('#billing_state').val() || '';
            var postcode = $('#billing_postcode').val() || '';

            // Resolve state code/text if selectable
            var stateText = stateVal;
            var $stateSelect = $('#billing_state');
            if ($stateSelect.is('select')) {
                stateText = $stateSelect.find('option:selected').text() || stateVal;
            }

            var fullName = $.trim(firstName + ' ' + lastName);
            var fullAddr = $.trim(addr1 + (addr2 ? ', ' + addr2 : '') + ', ' + city + ', ' + stateText + ' ' + postcode);

            if (fullName && phone && addr1) {
                $('#jt-summary-name').text(fullName);
                $('#jt-summary-phone').text('(' + phone + ')');
                $('#jt-summary-text').text(fullAddr);
                
                // Show summary, hide raw fields
                $('#jt-address-summary').show();
                $('#jt-raw-address-fields').hide();
                $('.jt-address-save-action').hide();
            } else {
                // If incomplete, force raw fields to show
                $('#jt-address-summary').hide();
                $('#jt-raw-address-fields').show();
                $('.jt-address-save-action').hide();
            }
        }

        // Initialize address state
        updateAddressSummary();

        // Handle edit address click
        $(document).on('click', '#jt-edit-address-btn', function(e) {
            e.preventDefault();
            if ($('#jt-address-modal').length) {
                // Logged in: open saved addresses modal
                $('#jt-address-modal').fadeIn(200);
                
                // Add active styling to initial checked radio card
                var $checkedRadio = $('input[name="jt_selected_address"]:checked');
                if ($checkedRadio.length) {
                    $('.jt-address-modal-item').removeClass('jt-address-modal-item--active');
                    $checkedRadio.closest('.jt-address-modal-item').addClass('jt-address-modal-item--active');
                }
            } else {
                // Guest fallback: slide down input form
                $('#jt-address-summary').hide();
                $('#jt-raw-address-fields').slideDown(200);
                $('.jt-address-save-action').show();
            }
        });

        // Close modal handlers
        $(document).on('click', '.jt-address-modal-close-btn', function(e) {
            e.preventDefault();
            $('#jt-address-modal').fadeOut(200);
        });

        $(document).on('click', '#jt-address-modal', function(e) {
            if ($(e.target).is('#jt-address-modal')) {
                $('#jt-address-modal').fadeOut(200);
            }
        });

        // Click card in modal to check radio
        $(document).on('click', '.jt-address-modal-item', function(e) {
            // If click was on radio itself, let it process naturally, otherwise trigger it
            var $radio = $(this).find('input[type="radio"]');
            if (!$(e.target).is('input[type="radio"]')) {
                $radio.prop('checked', true);
            }
            $('.jt-address-modal-item').removeClass('jt-address-modal-item--active');
            $(this).addClass('jt-address-modal-item--active');
        });

        // Handle confirm address button in modal
        $(document).on('click', '#jt-address-modal-confirm-btn', function(e) {
            e.preventDefault();
            var $selected = $('input[name="jt_selected_address"]:checked');
            if ($selected.length) {
                var firstName = $selected.data('first_name') || '';
                var lastName = $selected.data('last_name') || '';
                var phone = $selected.data('phone') || '';
                var addr1 = $selected.data('address_1') || '';
                var addr2 = $selected.data('address_2') || '';
                var city = $selected.data('city') || '';
                var state = $selected.data('state') || '';
                var postcode = $selected.data('postcode') || '';

                // Sync billing fields
                $('#billing_first_name').val(firstName);
                $('#billing_last_name').val(lastName);
                $('#billing_phone').val(phone);
                $('#billing_address_1').val(addr1);
                $('#billing_address_2').val(addr2);
                $('#billing_city').val(city);
                $('#billing_state').val(state).trigger('change');
                $('#billing_postcode').val(postcode);

                // Sync shipping fields
                $('#shipping_first_name').val(firstName);
                $('#shipping_last_name').val(lastName);
                $('#shipping_address_1').val(addr1);
                $('#shipping_address_2').val(addr2);
                $('#shipping_city').val(city);
                $('#shipping_state').val(state).trigger('change');
                $('#shipping_postcode').val(postcode);

                // Re-sync address summary view
                updateAddressSummary();

                // Trigger update checkout
                $(document.body).trigger('update_checkout');
            }
            $('#jt-address-modal').fadeOut(200);
        });

        // Handle Add New Address button in modal
        $(document).on('click', '#jt-address-modal-add-btn', function(e) {
            e.preventDefault();
            $('#jt-address-modal').fadeOut(200);

            // Empty checkout fields for fresh address entry
            $('#billing_first_name, #shipping_first_name').val('');
            $('#billing_last_name, #shipping_last_name').val('');
            $('#billing_address_1, #shipping_address_1').val('');
            $('#billing_address_2, #shipping_address_2').val('');
            $('#billing_city, #shipping_city').val('');
            $('#billing_state, #shipping_state').val('').trigger('change');
            $('#billing_postcode, #shipping_postcode').val('');

            // Uncheck the modal select list radio inputs
            $('input[name="jt_selected_address"]').prop('checked', false);
            $('.jt-address-modal-item').removeClass('jt-address-modal-item--active');

            // Slide open fields
            $('#jt-address-summary').hide();
            $('#jt-raw-address-fields').slideDown(200);
            $('.jt-address-save-action').show();
        });

        // Handle save address click
        $(document).on('click', '#jt-save-address-btn', function(e) {
            e.preventDefault();
            updateAddressSummary();
            // Trigger shipping rate recalculation in case address changed
            $(document.body).trigger('update_checkout');
        });

        // Listen for user edits in address fields to dynamically update summary
        $(document).on('change', '#customer_details input, #customer_details select', function() {
            // Keep summary sync'd in background
            var firstName = $('#billing_first_name').val() || '';
            var phone = $('#billing_phone').val() || '';
            var addr1 = $('#billing_address_1').val() || '';
            if (firstName && phone && addr1) {
                // Only auto-collapse if they aren't actively editing
                if ($('#jt-save-address-btn').is(':hidden')) {
                    updateAddressSummary();
                }
            }
        });

        // --- 3. MOBILE STICKY BOTTOM CHECKOUT BAR SYNC ---
        function syncStickyBottomBar() {
            // Scrape total price from WooCommerce order total row
            var $totalRow = $('.order-total td strong');
            if ($totalRow.length) {
                var totalPriceHtml = $totalRow.html();
                $('#jt-sticky-total-price').html(totalPriceHtml);
            }

            // Scrape discount / savings
            var totalSavings = 0;
            $('.cart-discount td').each(function() {
                var amountText = $(this).text().replace(/[^\d]/g, '');
                var amountVal = parseInt(amountText, 10);
                if (!isNaN(amountVal)) {
                    totalSavings += amountVal;
                }
            });

            if (totalSavings > 0) {
                // Format currency
                var formattedSavings = 'Rp' + totalSavings.toLocaleString('id-ID');
                $('#jt-sticky-savings-amount').text(formattedSavings);
                $('#jt-sticky-total-savings').show();
            } else {
                $('#jt-sticky-total-savings').hide();
            }
        }

        // Sync on load
        syncStickyBottomBar();

        // Sync whenever checkout updates via AJAX
        $(document.body).on('updated_checkout', function() {
            syncStickyBottomBar();
            // Also sync address summary if fields were filled via autofill or custom trigger
            if ($('#jt-save-address-btn').is(':hidden')) {
                updateAddressSummary();
            }
        });

        // Handle sticky submit button click
        $(document).on('click', '#jt-sticky-submit-btn', function(e) {
            e.preventDefault();
            
            // Check if address form is open and valid before submitting
            if ($('#jt-raw-address-fields').is(':visible')) {
                var firstName = $('#billing_first_name').val() || '';
                var phone = $('#billing_phone').val() || '';
                var addr1 = $('#billing_address_1').val() || '';
                
                if (firstName && phone && addr1) {
                    updateAddressSummary();
                }
            }

            // Click the native WooCommerce checkout button
            $('#place_order').trigger('click');
        });
    });

})(jQuery);
