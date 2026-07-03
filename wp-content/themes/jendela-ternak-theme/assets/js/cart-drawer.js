/**
 * cart-drawer.js — Alpine.js Cart Drawer Component
 *
 * Listens to WooCommerce AJAX add-to-cart events and slides in the cart drawer.
 * Uses wc-cart-fragments to keep cart data fresh without full page reload.
 */

window.cartDrawer = function() {
    return {
        open: false,
        loading: false,
        itemCount: 0,
        mobileMenuOpen: false,

        init() {
            // Sync initial count from DOM badge
            const badge = document.querySelector('[data-jt-cart-count]');
            if (badge) {
                this.itemCount = parseInt(badge.textContent.trim(), 10) || 0;
            }

            // Listen for WooCommerce fragment refresh (add to cart events)
            jQuery(document.body).on('wc_fragments_refreshed wc_fragments_loaded', () => {
                this.refreshCount();
            });

            // Listen for add-to-cart AJAX success to open drawer
            jQuery(document.body).on('added_to_cart', (event, fragments, cart_hash, $button) => {
                this.refreshCount();
                this.open = true;
            });

            // Listen for removed_from_cart
            jQuery(document.body).on('removed_from_cart', () => {
                this.refreshCount();
            });

            // Close on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (this.open) this.open = false;
                    if (this.mobileMenuOpen) this.mobileMenuOpen = false;
                }
            });
        },

        refreshCount() {
            const badge = document.querySelector('[data-jt-cart-count]');
            if (badge) {
                this.itemCount = parseInt(badge.textContent.trim(), 10) || 0;
            }
        },

        toggle() {
            this.open = !this.open;
            if (this.open) this.mobileMenuOpen = false; // close mobile menu when cart opens
        },

        close() {
            this.open = false;
        },

        toggleMobileMenu() {
            this.mobileMenuOpen = !this.mobileMenuOpen;
            if (this.mobileMenuOpen) this.open = false; // close cart when mobile menu opens
        },

        closeMobileMenu() {
            this.mobileMenuOpen = false;
        },
    };
};

// Register under Alpine.data as well
if (window.Alpine) {
    window.Alpine.data('cartDrawer', window.cartDrawer);
} else {
    document.addEventListener('alpine:init', () => {
        window.Alpine.data('cartDrawer', window.cartDrawer);
    });
}
