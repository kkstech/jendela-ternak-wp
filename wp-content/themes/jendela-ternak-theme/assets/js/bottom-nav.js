/**
 * bottom-nav.js — Alpine.js Mobile Bottom Navigation for Single Product Page
 *
 * Provides Wishlist toggle, AJAX Add to Cart, and Buy Now redirect.
 * Only loaded on single product pages (is_singular('product')).
 */

window.bottomNav = function() {
    return {
        wishlisted: false,
        loading: false,
        loadingBuyNow: false,
        message: '',

        init() {
            // Check if product is in wishlist (session/localStorage based — extend with plugin later)
            const productId = this.$el.dataset.productId;
            if (productId) {
                const wishlist = JSON.parse(localStorage.getItem('jt_wishlist') || '[]');
                this.wishlisted = wishlist.includes(String(productId));
            }
        },

        toggleWishlist() {
            const productId = this.$el.dataset.productId;
            if (!productId) return;

            let wishlist = JSON.parse(localStorage.getItem('jt_wishlist') || '[]');

            if (this.wishlisted) {
                wishlist = wishlist.filter(id => id !== String(productId));
                this.wishlisted = false;
                this.showMessage('Dihapus dari Wishlist');
            } else {
                wishlist.push(String(productId));
                this.wishlisted = true;
                this.showMessage('Ditambahkan ke Wishlist ❤️');
            }

            localStorage.setItem('jt_wishlist', JSON.stringify(wishlist));
        },

        /**
         * AJAX Add to Cart
         * Reads product ID from data attribute, calls WC AJAX handler.
         */
        addToCart() {
            const productId = this.$el.dataset.productId;
            if (!productId || this.loading) return;

            this.loading = true;

            jQuery.ajax({
                type: 'POST',
                url: jt_vars.ajax_url,
                data: {
                    action: 'woocommerce_ajax_add_to_cart',
                    product_id: productId,
                    quantity: 1,
                    nonce: jt_vars.nonce,
                },
                success: (response) => {
                    if (response.error) {
                        this.showMessage(response.product_url ? 'Pilih varian produk terlebih dahulu.' : 'Gagal menambahkan ke keranjang.');
                    } else {
                        // Trigger WC fragment refresh so cart drawer updates
                        jQuery(document.body).trigger('wc_fragment_refresh');
                        jQuery(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);
                        this.showMessage('Berhasil ditambahkan ke Keranjang! 🛒');
                    }
                },
                error: () => {
                    this.showMessage('Terjadi kesalahan, silakan coba lagi.');
                },
                complete: () => {
                    this.loading = false;
                },
            });
        },

        /**
         * Buy Now — empties cart, adds product, redirects to checkout.
         * Done via form submission with buy_now flag handled server-side
         * via woocommerce_add_to_cart_redirect filter.
         */
        buyNow() {
            const productId = this.$el.dataset.productId;
            if (!productId || this.loadingBuyNow) return;

            this.loadingBuyNow = true;

            // Create a form that mimics the single product Add to Cart form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = window.location.href;
            form.style.display = 'none';

            const fields = {
                'add-to-cart': productId,
                quantity: 1,
                buy_now: 1,
            };

            Object.entries(fields).forEach(([name, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        },

        showMessage(msg) {
            this.message = msg;
            setTimeout(() => { this.message = ''; }, 3000);
        },
    };
};

if (window.Alpine) {
    window.Alpine.data('bottomNav', window.bottomNav);
    try {
        if (!window.Alpine.store('pdpDrawer')) {
            window.Alpine.store('pdpDrawer', {
                open: false,
                action: 'cart',
                show(action = 'cart') {
                    this.open = true;
                    this.action = action;
                    document.body.style.overflow = 'hidden';
                },
                close() {
                    this.open = false;
                    document.body.style.overflow = '';
                }
            });
        }
    } catch(e) {}
} else {
    document.addEventListener('alpine:init', () => {
        window.Alpine.data('bottomNav', window.bottomNav);
        window.Alpine.store('pdpDrawer', {
            open: false,
            action: 'cart',
            show(action = 'cart') {
                this.open = true;
                this.action = action;
                document.body.style.overflow = 'hidden';
            },
            close() {
                this.open = false;
                document.body.style.overflow = '';
            }
        });
    });
}
