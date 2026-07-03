/**
 * shop-filter.js — Alpine.js Product Filter Component
 *
 * Mengelola state filter kategori, rentang harga, dan rating bintang.
 * Semua filter bersifat "pending" — baru diterapkan setelah klik tombol "Terapkan".
 * Menggunakan Fetch API untuk memuat ulang grid produk secara dinamis.
 */

window.jtShopFilter = function() {
    return {
        loading: false,
        mobileFilterOpen: false,

        // ── Applied (Active) States ─────────────────────────────────────────
        // Nilai ini mencerminkan filter yang sedang aktif di URL / tampilan produk
        category: '',
        minPrice: '',
        maxPrice: '',
        rating: '',

        // ── Pending States ──────────────────────────────────────────────────
        // Nilai ini berubah saat user mengklik filter, belum diterapkan ke URL
        pendingCategory: '',
        pendingMinPrice: '',
        pendingMaxPrice: '',
        pendingRating: '',

        init() {
            this.initFromUrl();

            // Handle back/forward browser navigation
            window.addEventListener('popstate', () => {
                this.initFromUrl();
                this.fetchProducts(window.location.href, false);
            });
        },

        initFromUrl() {
            const params = new URLSearchParams(window.location.search);

            // Sync applied states dari URL
            this.category    = params.get('product_cat')    || '';
            this.minPrice    = params.get('min_price')      || '';
            this.maxPrice    = params.get('max_price')      || '';
            this.rating      = params.get('rating_filter')  || '';

            // Sync pending states agar UI mencerminkan URL saat ini
            this.pendingCategory  = this.category;
            this.pendingMinPrice  = this.minPrice;
            this.pendingMaxPrice  = this.maxPrice;
            this.pendingRating    = this.rating;
        },

        /**
         * Terapkan semua filter pending ke URL dan fetch produk baru.
         * Dipanggil saat klik tombol "Terapkan Filter".
         */
        applyFilters() {
            // Commit pending state ke applied state
            this.category = this.pendingCategory;
            this.minPrice = this.pendingMinPrice;
            this.maxPrice = this.pendingMaxPrice;
            this.rating   = this.pendingRating;

            const baseUrl = window.location.pathname;
            const params  = new URLSearchParams();

            // Pertahankan urutan sortir yang sudah ada
            const currentParams = new URLSearchParams(window.location.search);
            if (currentParams.has('orderby')) {
                params.set('orderby', currentParams.get('orderby'));
            }

            if (this.category)  params.set('product_cat',   this.category);
            if (this.minPrice)  params.set('min_price',     this.minPrice);
            if (this.maxPrice)  params.set('max_price',     this.maxPrice);
            if (this.rating)    params.set('rating_filter', this.rating);

            const queryString = params.toString();
            const fetchUrl    = baseUrl + (queryString ? '?' + queryString : '');
            this.fetchProducts(fetchUrl, true);
        },

        /**
         * Reset semua filter (applied & pending) lalu reload produk.
         */
        resetFilters() {
            this.category         = '';
            this.minPrice         = '';
            this.maxPrice         = '';
            this.rating           = '';
            this.pendingCategory  = '';
            this.pendingMinPrice  = '';
            this.pendingMaxPrice  = '';
            this.pendingRating    = '';

            // Pertahankan orderby jika ada
            const params        = new URLSearchParams();
            const currentParams = new URLSearchParams(window.location.search);
            if (currentParams.has('orderby')) {
                params.set('orderby', currentParams.get('orderby'));
            }

            const queryString = params.toString();
            const fetchUrl    = window.location.pathname + (queryString ? '?' + queryString : '');
            this.fetchProducts(fetchUrl, true);
        },

        /**
         * Fetch halaman produk dan update DOM secara parsial (tanpa full reload).
         */
        fetchProducts(url, shouldPushState = true) {
            this.loading = true;

            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.text();
                })
                .then(html => {
                    const parser = new DOMParser();
                    const doc    = parser.parseFromString(html, 'text/html');

                    // Ganti grid produk
                    const oldGrid = document.getElementById('jt-products-catalog-grid');
                    const newGrid = doc.getElementById('jt-products-catalog-grid');
                    if (oldGrid && newGrid) {
                        oldGrid.innerHTML = newGrid.innerHTML;
                    } else {
                        const oldColumn = document.querySelector('.jt-shop-catalog-column');
                        const newColumn = doc.querySelector('.jt-shop-catalog-column');
                        if (oldColumn && newColumn) {
                            oldColumn.innerHTML = newColumn.innerHTML;
                        }
                    }

                    // Update paginasi
                    const oldPagination = document.querySelector('.jt-catalog-pagination');
                    const newPagination = doc.querySelector('.jt-catalog-pagination');
                    if (oldPagination && newPagination) {
                        oldPagination.innerHTML   = newPagination.innerHTML;
                        oldPagination.style.display = '';
                    } else if (oldPagination && !newPagination) {
                        oldPagination.innerHTML   = '';
                        oldPagination.style.display = 'none';
                    }

                    // Update header katalog (judul & sorting)
                    const oldHeader = document.querySelector('.jt-catalog-header');
                    const newHeader = doc.querySelector('.jt-catalog-header');
                    if (oldHeader && newHeader) {
                        oldHeader.innerHTML = newHeader.innerHTML;
                    }

                    // Update notices
                    const oldNotices = document.querySelector('.jt-catalog-notices');
                    const newNotices = doc.querySelector('.jt-catalog-notices');
                    if (oldNotices && newNotices) {
                        oldNotices.innerHTML = newNotices.innerHTML;
                    }

                    // Scroll smooth ke header katalog
                    const header = document.querySelector('.jt-catalog-header');
                    if (header) {
                        header.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }

                    // Tutup drawer mobile setelah filter berhasil diterapkan
                    this.mobileFilterOpen = false;

                    // Perbarui URL di browser history
                    if (shouldPushState) {
                        history.pushState({}, '', url);
                    }
                })
                .catch(error => {
                    console.error('Fetch products error:', error);
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    };
};

// Register ke Alpine.data
if (window.Alpine) {
    window.Alpine.data('jtShopFilter', window.jtShopFilter);
} else {
    document.addEventListener('alpine:init', () => {
        window.Alpine.data('jtShopFilter', window.jtShopFilter);
    });
}
