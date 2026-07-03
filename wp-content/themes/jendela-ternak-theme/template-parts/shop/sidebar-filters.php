<?php
/**
 * template-parts/shop/sidebar-filters.php
 * Custom Product Filters Sidebar — Kategori, Rentang Harga, dan Penilaian Rating.
 * Semua filter baru diterapkan setelah klik tombol "Terapkan".
 * Loaded within Alpine.js jtShopFilter component scope.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

// Query Categories
$categories = get_terms( array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'parent'     => 0,
) );
?>

<div class="jt-filter-sidebar-wrapper">

    <!-- ── 1. PRODUCT CATEGORIES ── -->
    <?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
        <div class="jt-widget jt-filter-widget">
            <h3 class="jt-widget__title"><?php esc_html_e( 'Kategori Produk', 'jendela-ternak' ); ?></h3>
            <ul class="jt-widget__list jt-filter-category-list">
                <?php foreach ( $categories as $cat ) :
                    if ( $cat->slug === 'uncategorized' ) continue;
                ?>
                    <li>
                        <button
                            type="button"
                            class="jt-filter-cat-btn"
                            :class="pendingCategory === '<?php echo esc_attr( $cat->slug ); ?>' ? 'active' : ''"
                            @click="pendingCategory = (pendingCategory === '<?php echo esc_js( $cat->slug ); ?>') ? '' : '<?php echo esc_js( $cat->slug ); ?>'"
                        >
                            <span class="jt-filter-cat-name"><?php echo esc_html( $cat->name ); ?></span>
                            <span class="jt-filter-cat-count"><?php echo esc_html( $cat->count ); ?></span>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- ── 2. PRICE RANGE ── -->
    <div class="jt-widget jt-filter-widget">
        <h3 class="jt-widget__title"><?php esc_html_e( 'Rentang Harga', 'jendela-ternak' ); ?></h3>
        <div class="jt-filter-price-inputs">
            <div class="jt-filter-price-row">
                <div class="jt-filter-price-field">
                    <span class="jt-price-prefix">Rp</span>
                    <input
                        type="number"
                        placeholder="<?php esc_attr_e( 'Minimum', 'jendela-ternak' ); ?>"
                        x-model.number="pendingMinPrice"
                        @keyup.enter="applyFilters()"
                        aria-label="<?php esc_attr_e( 'Harga Minimum', 'jendela-ternak' ); ?>"
                    />
                </div>
                <div class="jt-price-divider">–</div>
                <div class="jt-filter-price-field">
                    <span class="jt-price-prefix">Rp</span>
                    <input
                        type="number"
                        placeholder="<?php esc_attr_e( 'Maksimum', 'jendela-ternak' ); ?>"
                        x-model.number="pendingMaxPrice"
                        @keyup.enter="applyFilters()"
                        aria-label="<?php esc_attr_e( 'Harga Maksimum', 'jendela-ternak' ); ?>"
                    />
                </div>
            </div>
        </div>
    </div>

    <!-- ── 3. RATINGS / PENILAIAN (Horizontal Rows, Pilih Minimum Bintang) ── -->
    <div class="jt-widget jt-filter-widget">
        <h3 class="jt-widget__title"><?php esc_html_e( 'Penilaian Pembeli', 'jendela-ternak' ); ?></h3>
        <div class="jt-filter-rating-rows">
            <?php for ( $r = 5; $r >= 1; $r-- ) : ?>
                <button
                    type="button"
                    class="jt-filter-rating-row-btn"
                    :class="pendingRating === '<?php echo $r; ?>' ? 'active' : ''"
                    @click="pendingRating = (pendingRating === '<?php echo $r; ?>') ? '' : '<?php echo $r; ?>'"
                    aria-label="<?php echo esc_attr( sprintf( 'Rating minimum %d bintang', $r ) ); ?>"
                >
                    <span class="jt-stars-row">
                        <?php
                        for ( $i = 1; $i <= 5; $i++ ) {
                            echo $i <= $r ? '<span class="jt-star jt-star--filled">★</span>' : '<span class="jt-star jt-star--empty">★</span>';
                        }
                        ?>
                    </span>
                    <?php if ( $r < 5 ) : ?>
                        <span class="jt-rating-label"><?php echo esc_html( sprintf( '%d ke atas', $r ) ); ?></span>
                    <?php else : ?>
                        <span class="jt-rating-label"><?php esc_html_e( '5 bintang', 'jendela-ternak' ); ?></span>
                    <?php endif; ?>
                </button>
            <?php endfor; ?>
        </div>
    </div>

    <!-- ── TOMBOL TERAPKAN GLOBAL ── -->
    <div class="jt-filter-apply-action">
        <button
            type="button"
            class="jt-btn jt-btn--primary jt-filter-apply-btn"
            @click="applyFilters()"
        >
            <?php esc_html_e( 'Terapkan Filter', 'jendela-ternak' ); ?>
        </button>
    </div>

    <!-- ── RESET FILTER BUTTON ── -->
    <div
        class="jt-filter-reset-action"
        x-show="category || minPrice || maxPrice || rating"
        x-cloak
    >
        <button
            type="button"
            class="jt-btn jt-btn--outline jt-filter-reset-btn"
            @click="resetFilters()"
        >
            🗑️ <?php esc_html_e( 'Hapus Semua Filter', 'jendela-ternak' ); ?>
        </button>
    </div>

</div>
