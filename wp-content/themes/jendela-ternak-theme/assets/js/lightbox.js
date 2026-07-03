/**
 * lightbox.js — Product Image Lightbox
 * Opens a full-screen overlay with the clicked product image.
 * Uses Alpine.js store for state management.
 */
(function () {
    'use strict';

    // Register Alpine store (before Alpine initializes)
    document.addEventListener('alpine:init', function () {
        Alpine.store('lightbox', {
            open: false,
            src: '',
            show: function (src) {
                this.src = src;
                this.open = true;
                document.body.style.overflow = 'hidden';
            },
            close: function () {
                this.open = false;
                document.body.style.overflow = '';
            }
        });
    });

    // ESC key close
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            var lb = window.Alpine && Alpine.store('lightbox');
            if (lb && lb.open) {
                lb.close();
            }
        }
    });

    // Alpine component helper for Product Detail Page
    window.jtProductDetail = function (initialImg, initialPriceHtml) {
        return {
            mainImg: initialImg,
            priceHtml: initialPriceHtml,
            switchImage: function (url) {
                this.mainImg = url;
            },
            formatRupiah: function (amount) {
                return 'Rp ' + Number(amount).toLocaleString('id-ID');
            },
            init: function () {
                var self = this;
                jQuery(document).on('found_variation', function (e, variation) {
                    if (variation && variation.image && variation.image.src) {
                        self.switchImage(variation.image.src);
                    }
                    if (variation && typeof variation.display_price !== 'undefined') {
                        var salePrice = variation.display_price;
                        var regPrice = variation.display_regular_price;
                        var html = '';
                        if (regPrice && regPrice > salePrice) {
                            var discount = Math.round(((regPrice - salePrice) / regPrice) * 100);
                            html = '<span class="text-xl md:text-2xl font-extrabold text-[#0B5E34]">' + self.formatRupiah(salePrice) + '</span>';
                            html += '<span class="text-gray-400 line-through text-sm">' + self.formatRupiah(regPrice) + '</span>';
                            html += '<span class="discount-hexagon-badge bg-[#D4B106] text-black text-[10px] font-extrabold px-2 py-0.5 rounded-sm shadow-sm">PROMO -' + discount + '%</span>';
                        } else {
                            html = '<span class="text-xl md:text-2xl font-extrabold text-[#0B5E34]">' + self.formatRupiah(salePrice) + '</span>';
                        }
                        self.priceHtml = html;
                    }
                });
                jQuery(document).on('reset_data', function () {
                    self.switchImage(initialImg);
                    self.priceHtml = initialPriceHtml;
                });
            }
        };
    };
})();
