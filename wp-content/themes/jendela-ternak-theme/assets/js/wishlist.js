/**
 * assets/js/wishlist.js
 * Alpine.js component for Wishlist Page operations
 *
 * @package JendelaTernakMalang
 */

window.wishlistPage = function() {
    return {
        wishlistIds: [],
        productsHtml: '',
        loading: false,
        isEmpty: false,
        message: '',

        initPage() {
            this.loadWishlist();
        },

        loadWishlist() {
            const wishlist = JSON.parse(localStorage.getItem('jt_wishlist') || '[]');
            this.wishlistIds = wishlist.map(String);
            
            if (this.wishlistIds.length === 0) {
                this.isEmpty = true;
                this.productsHtml = '';
                return;
            }

            this.isEmpty = false;
            this.fetchProducts();
        },

        fetchProducts() {
            this.loading = true;
            jQuery.ajax({
                type: 'POST',
                url: jt_vars.ajax_url,
                data: {
                    action: 'jt_get_wishlist_products',
                    product_ids: this.wishlistIds
                },
                success: (response) => {
                    if (response.success) {
                        this.productsHtml = response.data.html;
                        this.isEmpty = response.data.count === 0;
                    } else {
                        this.productsHtml = '';
                        this.isEmpty = true;
                    }
                },
                error: () => {
                    this.productsHtml = '';
                    this.isEmpty = true;
                    this.showMessage('Gagal memuat produk favorit.');
                },
                complete: () => {
                    this.loading = false;
                }
            });
        },

        removeFromWishlist(productId, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            // Animate card removal
            const cardWrapper = document.querySelector(`.jt-wishlist-card-wrapper[data-product-id="${productId}"]`);
            if (cardWrapper) {
                cardWrapper.classList.add('jt-fade-out');
                
                setTimeout(() => {
                    this.wishlistIds = this.wishlistIds.filter(id => id !== String(productId));
                    localStorage.setItem('jt_wishlist', JSON.stringify(this.wishlistIds));
                    
                    if (this.wishlistIds.length === 0) {
                        this.isEmpty = true;
                        this.productsHtml = '';
                    } else {
                        cardWrapper.remove();
                    }
                    this.showMessage('Dihapus dari Wishlist');
                }, 300);
            } else {
                this.wishlistIds = this.wishlistIds.filter(id => id !== String(productId));
                localStorage.setItem('jt_wishlist', JSON.stringify(this.wishlistIds));
                if (this.wishlistIds.length === 0) {
                    this.isEmpty = true;
                    this.productsHtml = '';
                }
                this.showMessage('Dihapus dari Wishlist');
            }
        },

        clearAllWishlist() {
            if (confirm('Apakah Anda yakin ingin menghapus semua produk dari wishlist?')) {
                localStorage.setItem('jt_wishlist', JSON.stringify([]));
                this.wishlistIds = [];
                this.isEmpty = true;
                this.productsHtml = '';
                this.showMessage('Semua produk dihapus dari Wishlist');
            }
        },

        quickAddToCart(productId, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
                
                const btn = event.currentTarget;
                btn.disabled = true;
                btn.innerHTML = '⌛ Memproses...';
            }

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
                        jQuery(document.body).trigger('wc_fragment_refresh');
                        jQuery(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);
                        this.showMessage('Berhasil ditambahkan ke Keranjang! 🛒');
                    }
                },
                error: () => {
                    this.showMessage('Terjadi kesalahan, silakan coba lagi.');
                },
                complete: () => {
                    if (event) {
                        const btn = event.currentTarget;
                        btn.disabled = false;
                        btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg> Masukkan Keranjang`;
                    }
                }
            });
        },

        wishlistCountText() {
            return `Terdapat ${this.wishlistIds.length} produk pilihan Anda:`;
        },

        showMessage(msg) {
            this.message = msg;
            setTimeout(() => { this.message = ''; }, 3000);
        }
    };
};

if (window.Alpine) {
    window.Alpine.data('wishlistPage', window.wishlistPage);
} else {
    document.addEventListener('alpine:init', () => {
        window.Alpine.data('wishlistPage', window.wishlistPage);
    });
}
