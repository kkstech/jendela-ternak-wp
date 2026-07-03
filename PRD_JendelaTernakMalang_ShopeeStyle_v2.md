# PRD — Custom WordPress Theme "Jendela Ternak Malang" (Shopee-Inspired UX)
**Project Owner:** Yoga Nanda Saputra (Kitaweb)
**Klien/Brand:** Jendela Ternak Malang (jendelaternakmalang.com)
**Tanggal:** 1 Juli 2026
**Versi:** 2.0 (hasil interview)

## 1. Latar Belakang & Tujuan
Membangun tema custom WordPress + WooCommerce untuk toko peternakan online (pakan, obat hewan, alat ternak) dengan pola UX marketplace ala Shopee, tetapi mempertahankan identitas visual brand hijau-kuning Jendela Ternak Malang. Fokus utama: mempermudah navigasi katalog ratusan SKU, mempercepat konversi mobile, dan mendukung operasional omnichannel via BigSeller.

## 2. Brand Identity & Color Palette
Diambil dari materi visual banner resmi brand.

| Warna | Hex | Peran |
|---|---|---|
| Hijau tua | #0B5E34 | Primary brand, header, teks judul, tombol utama |
| Hijau medium | #4CAF50 | Aksen sekunder, hover state |
| Kuning-hijau gradient | #C8D400 | Background hero, badge promo |
| Kuning emas | #D4B106 | Ikon hexagon, highlight, badge diskon |
| Putih | #FFFFFF | Card background, kontras teks |
| Abu terang | #F2F2F2 | Section background netral |

Tipografi: sans-serif bold untuk judul (mengikuti gaya banner: tegas, rounded), reguler untuk body text. Elemen bentuk hexagon dari banner bisa dipakai berulang sebagai badge kategori/ikon fitur.

## 3. Target Pengguna
- Buyer: peternak/pemilik hewan, mayoritas akses via mobile, butuh navigasi cepat ke kategori spesifik (pakan ayam, obat kambing, dll).
- Admin: kelola ratusan SKU, promo terjadwal, dan sinkronisasi stok lintas platform via BigSeller.

## 4. Tech Stack
| Layer | Teknologi |
|---|---|
| CMS | WordPress + WooCommerce |
| Theme base | Custom theme (starter theme) + Tailwind CSS |
| Payment | Midtrans (Snap) |
| Shipping | Biteship (30+ kurir) |
| Inventory Sync | BigSeller API (omnichannel: WooCommerce ↔ Shopee/Lazada/TikTok Shop) |
| Bulk discount & flash sale | WooCommerce Dynamic Pricing / YayPricing / Smart Sale Scheduler |
| Rating + foto ulasan | Custom review meta box atau plugin review foto (extend WooCommerce native review) |
| Live chat | WhatsApp widget / Tawk.to terintegrasi di halaman produk |
| Frontend interactivity | Alpine.js/Vue.js untuk cart drawer, bottom nav produk, wishlist |
| Blog | Native WordPress post + custom template blog |
| Hosting/Deploy | VPS + Coolify/Docker |

## 5. Struktur Katalog
- Kategori utama: Pakan Ternak (Ayam, Kambing, dll), Obat & Vitamin, Alat & Wadah Ternak, Kandang & Perlengkapan, kategori lain sesuai kebutuhan.
- Varian produk: berat (kg/gram), jenis kemasan, jenis hewan target — ditampilkan sebagai chip selector di halaman produk.
- Volume: ratusan SKU aktif, single-seller (bukan multi-vendor), stok disinkronkan omnichannel via BigSeller.

## 6. Referensi UI Shopee → Fitur Website (Disesuaikan Brand)

### 6.1 Homepage
- Header: logo, search bar besar, ikon wishlist, cart, akun.
- Hero banner: carousel promo dengan gradient hijau-kuning brand, bukan oranye Shopee.
- Kategori grid ikon hexagon (mengikuti elemen visual banner) — 8-10 kategori utama.
- Section Flash Sale: countdown timer, badge diskon kuning, grid produk horizontal scroll (mobile) / grid 5 kolom (desktop).
- Section Rekomendasi: grid produk 2 kolom (mobile) / 4-5 kolom (desktop), infinite scroll.
- Trust badges dari banner: Promo Menarik, Produk Original, Respon Cepat, Lebih Terpercaya — ditampilkan sebagai strip horizontal di homepage.
- CS WhatsApp bar sticky/footer, sesuai info di banner (jam operasional 08.00-17.00).

### 6.2 Product Card
- Gambar produk rasio 1:1, badge diskon %, badge "Produk Original" (mengambil trust badge brand).
- Nama produk (max 2 baris), harga coret + harga promo.
- Rating bintang + jumlah terjual + kota asal.
- Progress bar stok untuk item flash sale.

### 6.3 Halaman Produk (PDP)
- Galeri gambar + chip selector varian (berat/jenis).
- Info stok real-time (sinkron BigSeller).
- Tab: Deskripsi, Spesifikasi, Ulasan (rating + foto dari pembeli, seperti Shopee).
- Widget live chat WhatsApp mengambang di halaman produk.
- **Mobile bottom navigation khusus PDP:** 3 tombol — Wishlist (icon), Tambah ke Keranjang, Beli Langsung (checkout langsung tanpa ke cart).

### 6.4 Cart & Checkout
- Cart drawer slide-in untuk quick review.
- Opsi checkout ganda: "Tambah Keranjang" (lanjut belanja) vs "Beli Langsung" (skip cart, langsung ke checkout).
- Estimasi ongkir otomatis dari Biteship.
- Ringkasan sticky: subtotal, ongkir, diskon (termasuk bulk discount jika berlaku), total.

### 6.5 Promo & Discount Engine
- Flash sale berjadwal dengan countdown timer per produk/kategori.
- Bulk discount: bisa diterapkan untuk semua produk (storewide) atau produk terpilih (kategori/SKU spesifik).
- Badge promo otomatis muncul di product card saat aturan aktif.

### 6.6 Rating & Review
- Pembeli bisa upload foto saat memberi ulasan.
- Tampilan galeri foto ulasan di tab produk, mirip pola "Ulasan Pembeli" Shopee.

### 6.7 Live Chat
- Widget chat (WhatsApp Business API atau live chat plugin) muncul persist di semua halaman produk.
- Opsional: quick-reply template untuk pertanyaan umum (stok, pengiriman, cara pakai obat).

### 6.8 Blog
- Menggunakan struktur WordPress native (Posts), custom template agar konsisten dengan tema utama (header/footer sama).
- Kategori blog: edukasi ternak, tips kesehatan hewan, promo event.

## 7. Struktur Halaman (Sitemap)
1. Homepage
2. Kategori/Listing produk (filter: kategori, harga, rating)
3. Halaman Detail Produk (dengan bottom nav mobile khusus)
4. Cart (drawer + halaman)
5. Checkout (dengan opsi Beli Langsung)
6. Payment status (Midtrans)
7. Akun: Profil, Alamat, Pesanan, Wishlist
8. Blog & Artikel Edukasi
9. Halaman Promo/Flash Sale khusus

## 8. Integrasi Teknis Utama
| Integrasi | Fungsi | Catatan |
|---|---|---|
| Midtrans Snap | Payment gateway | Popup modal, tanpa redirect keluar situs |
| Biteship | Shipping rate & tracking real-time | 30+ kurir, estimasi otomatis di checkout |
| BigSeller | Sinkronisasi stok omnichannel | Hubungkan via Consumer Key/Secret WooCommerce REST API, atur push rule stok |
| WhatsApp/Live chat | Customer service | Terhubung ke nomor CS di halaman produk |

## 9. Non-Functional Requirements
- Mobile-first, breakpoint 375px/768px/1024px+.
- Page speed: LCP < 2.5s, lazy-load gambar, WebP.
- SEO: schema.org Product markup, slug SEO-friendly, blog untuk content marketing.
- Keamanan: hardening WP-Admin, validasi webhook Midtrans/Biteship/BigSeller, HTTPS wajib.

## 10. Milestone & Timeline (Estimasi)
| Fase | Deliverable | Estimasi |
|---|---|---|
| 1. Discovery & Wireframe | Wireframe semua halaman utama | 4-6 hari |
| 2. UI Design (Figma) | Mockup mobile+desktop, sesuai brand palette | 6-8 hari |
| 3. Theme Development | Coding tema + komponen (bottom nav, cart drawer) | 12-18 hari |
| 4. Integrasi WooCommerce | Katalog, varian, cart, checkout dual-mode | 4-6 hari |
| 5. Integrasi Midtrans | Payment gateway + webhook | 2-3 hari |
| 6. Integrasi Biteship | Shipping rate + tracking | 2-3 hari |
| 7. Integrasi BigSeller | Sinkronisasi stok omnichannel | 3-5 hari |
| 8. Fitur Promo & Review | Flash sale, bulk discount, review foto | 4-6 hari |
| 9. QA & Testing | Cross-browser, mobile testing | 4-5 hari |
| 10. Deployment | Setup VPS, Docker/Coolify, go-live | 1-2 hari |

## 11. Success Metrics
- Peningkatan konversi checkout mobile (opsi Beli Langsung).
- Penurunan cart abandonment.
- Konsistensi stok lintas channel (BigSeller sync error rate rendah).
- Peningkatan trust signal (jumlah ulasan bergambar).

## 12. Risiko & Mitigasi
- Konflik antar plugin (Midtrans, Biteship, BigSeller, discount engine) → gunakan hook WooCommerce standar, testing bertahap per integrasi.
- Volume SKU besar berdampak ke performa listing → gunakan caching, indexing produk yang efisien, dan pagination/infinite scroll yang dioptimasi.
- Ketergantungan API pihak ketiga (BigSeller) → pantau uptime dan siapkan fallback manual update stok.
