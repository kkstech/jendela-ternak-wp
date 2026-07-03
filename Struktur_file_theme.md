jendela-ternak-theme/
├── style.css                    → theme header + variabel CSS palette
├── functions.php                → registrasi hook utama, enqueue asset
├── screenshot.png
├── assets/
│   ├── css/tailwind-output.css
│   ├── js/
│   │   ├── cart-drawer.js       → Alpine.js komponen cart drawer
│   │   ├── bottom-nav.js        → Alpine.js komponen bottom nav PDP
│   │   └── countdown.js         → flash sale timer
│   └── images/icons/
├── inc/
│   ├── theme-setup.php          → menu, sidebar, image size, theme support
│   ├── woocommerce-hooks.php    → semua override/hook WooCommerce
│   ├── customizer.php           → opsi warna & logo di WP Customizer
│   └── enqueue-assets.php
├── template-parts/
│   ├── header/site-header.php
│   ├── footer/site-footer.php
│   ├── product/product-card.php
│   ├── product/bottom-nav.php   → bottom nav khusus single product
│   └── homepage/hero-banner.php, flash-sale.php, category-grid.php
├── woocommerce/                 → override template WooCommerce (child dari plugin)
│   ├── single-product.php
│   ├── content-single-product.php
│   ├── cart/cart.php
│   └── checkout/form-checkout.php
├── page-blog.php                → template custom untuk arsip blog
└── functions/                   → (opsional, split logic besar)