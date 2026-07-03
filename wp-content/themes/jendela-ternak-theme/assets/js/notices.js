/**
 * assets/js/notices.js
 * Handles custom WooCommerce toast notifications with auto-dismiss and positioning.
 *
 * @package JendelaTernakMalang
 */

jQuery(document).ready(function($) {
    // Ensure we have a global toast container enqueued in the body
    let $container = $('#jt-toast-container');
    if ($container.length === 0) {
        $container = $('<div id="jt-toast-container" class="jt-toast-container"></div>').appendTo('body');
    }

    /**
     * Move and initialize a toast notice
     * @param {jQuery} $toast 
     */
    function handleToast($toast) {
        if ($toast.data('jt-toast-processed')) {
            return;
        }
        $toast.data('jt-toast-processed', true);

        // Move to our global top-center container so it stacks nicely
        $toast.appendTo($container);

        // Setup close button click
        $toast.find('.jt-toast__close').on('click', function() {
            dismissToast($toast);
        });

        // Setup auto-dismiss for non-errors
        const isError = $toast.hasClass('jt-toast--error');
        if (!isError) {
            setTimeout(function() {
                dismissToast($toast);
            }, 3500);
        }
    }

    /**
     * Dismiss and remove a toast from DOM with animation
     * @param {jQuery} $toast 
     */
    function dismissToast($toast) {
        $toast.addClass('jt-toast--out');
        $toast.one('transitionend webkitTransitionEnd oTransitionEnd', function() {
            $toast.slideUp(200, function() {
                $toast.remove();
            });
        });
        // Fallback to guarantee removal if browser transition events fail
        setTimeout(function() {
            if ($toast.parent().length) {
                $toast.remove();
            }
        }, 600);
    }

    /**
     * Fallback converter for raw WooCommerce notice containers
     * (e.g. from third-party plugins that output raw message/error/info divs)
     */
    function convertRawNotices() {
        $('.woocommerce-message, .woocommerce-error, .woocommerce-info').each(function() {
            const $raw = $(this);
            // Skip if it's already a jt-toast, inside our container, or already processed
            if ($raw.hasClass('jt-toast') || $raw.closest('#jt-toast-container').length) {
                return;
            }

            let typeClass = 'jt-toast--info';
            let iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 11.517 1.282l-.04.02-.041.02a.75.75 0 01-.517-1.282l.04-.02zM12 9a.75.75 0 100-1.5.75.75 0 000 1.5zm0 11.25a9 9 0 100-18 9 9 0 000 18z"/></svg>';

            if ($raw.hasClass('woocommerce-message')) {
                typeClass = 'jt-toast--success';
                iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>';
            } else if ($raw.hasClass('woocommerce-error')) {
                typeClass = 'jt-toast--error';
                iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>';
            }

            // Extract the notice contents
            let contentHtml = $raw.html();

            // Create new toast markup
            const $newToast = $(`
                <div class="jt-toast ${typeClass}" role="alert">
                    <div class="jt-toast__icon">${iconSvg}</div>
                    <div class="jt-toast__content">${contentHtml}</div>
                    <button type="button" class="jt-toast__close" aria-label="Tutup">&times;</button>
                </div>
            `);

            // Replace the raw notice and handle the new toast
            $raw.remove();
            handleToast($newToast);
        });
    }

    // Initial check on page load
    $('.jt-toast').each(function() {
        handleToast($(this));
    });
    convertRawNotices();

    // Setup MutationObserver on document body to monitor for newly added elements
    const observer = new MutationObserver(function(mutationsList) {
        let shouldRunConvert = false;
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        const $node = $(node);

                        // If it's a jt-toast or contains a jt-toast, handle it
                        if ($node.hasClass('jt-toast')) {
                            handleToast($node);
                        } else {
                            $node.find('.jt-toast').each(function() {
                                handleToast($(this));
                            });
                        }

                        // Check if raw WooCommerce notices were injected
                        if ($node.hasClass('woocommerce-message') || $node.hasClass('woocommerce-error') || $node.hasClass('woocommerce-info') ||
                            $node.find('.woocommerce-message, .woocommerce-error, .woocommerce-info').length) {
                            shouldRunConvert = true;
                        }
                    }
                });
            }
        }
        if (shouldRunConvert) {
            convertRawNotices();
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // WooCommerce standard jQuery AJAX events listener
    $(document).on('added_to_cart removed_from_cart updated_cart_totals updated_checkout checkout_error', function() {
        // Small delay to let WooCommerce finish appending notices to the DOM
        setTimeout(function() {
            $('.jt-toast').each(function() {
                handleToast($(this));
            });
            convertRawNotices();
        }, 50);
    });
});
