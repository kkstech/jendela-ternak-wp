<?php
/**
 * woocommerce/content-single-product.php
 * Custom layout: Shopee-Inspired 3-Column Layout (Gallery + Details + Recommended Sidebar)
 * Enhanced: Lightbox, Stock Badge, Qty Stepper, Share Strip, Collapsible Desc, Spec Icons, Review Redesign
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
    echo get_the_password_form();
    return;
}

// Get gallery image IDs
$attachment_ids = $product->get_gallery_image_ids();
$main_image_id  = $product->get_image_id();
if ( $main_image_id ) {
    array_unshift( $attachment_ids, $main_image_id );
}
$attachment_ids = array_unique( $attachment_ids );

$main_img_url = $main_image_id
    ? wp_get_attachment_image_url( $main_image_id, 'large' )
    : wc_placeholder_img_src( 'large' );

// Stock info
$in_stock       = $product->is_in_stock();
$managing_stock = $product->managing_stock();
$stock_qty      = $product->get_stock_quantity();

// Initial price HTML inside the container
$price_inner_html = '';
if ( $product->is_type( 'variable' ) ) {
    $prices = $product->get_variation_prices( true );
    $min_price = ! empty( $prices['price'] ) ? min( $prices['price'] ) : 0;
    $max_price = ! empty( $prices['price'] ) ? max( $prices['price'] ) : 0;
    if ( $min_price !== $max_price ) {
        $price_inner_html .= '<span class="text-xl md:text-2xl font-extrabold text-[#0B5E34]">Rp ' . number_format( $min_price, 0, ',', '.' ) . ' - Rp ' . number_format( $max_price, 0, ',', '.' ) . '</span>';
    } else {
        $price_inner_html .= '<span class="text-xl md:text-2xl font-extrabold text-[#0B5E34]">Rp ' . number_format( $min_price, 0, ',', '.' ) . '</span>';
    }
} else {
    $reg_price = $product->get_regular_price();
    $sale_price = $product->get_sale_price();
    if ( $sale_price && $reg_price > $sale_price ) {
        $discount = round( ( ( $reg_price - $sale_price ) / $reg_price ) * 100 );
        $price_inner_html .= '<span class="text-xl md:text-2xl font-extrabold text-[#0B5E34]">Rp ' . number_format( $sale_price, 0, ',', '.' ) . '</span>';
        $price_inner_html .= '<span class="text-gray-400 line-through text-sm">Rp ' . number_format( $reg_price, 0, ',', '.' ) . '</span>';
        $price_inner_html .= '<span class="discount-hexagon-badge bg-[#D4B106] text-black text-[10px] font-extrabold px-2 py-0.5 rounded-sm shadow-sm">PROMO -' . $discount . '%</span>';
    } else {
        $price_inner_html .= '<span class="text-xl md:text-2xl font-extrabold text-[#0B5E34]">Rp ' . number_format( $product->get_price() ?: 0, 0, ',', '.' ) . '</span>';
    }
}

// Review data
$avg_rating   = (float) $product->get_average_rating();
$review_count = (int)   $product->get_review_count();
$rating_count = (int)   $product->get_rating_count();

// Build rating distribution
$rating_counts_by_star = array();
for ( $s = 5; $s >= 1; $s-- ) {
    $rating_counts_by_star[ $s ] = 0;
}
if ( $rating_count > 0 ) {
    $counts = $product->get_rating_counts();
    foreach ( $counts as $star => $count ) {
        if ( isset( $rating_counts_by_star[ $star ] ) ) {
            $rating_counts_by_star[ $star ] = (int) $count;
        }
    }
}

// SVG icon helpers
function jt_icon_star( $class = '' ) {
    return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
}

function jt_spec_icon( $type ) {
    $icons = array(
        'kategori' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>',
        'merek'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>',
        'bahan'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>',
        'berat'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>',
        'default'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>',
        'volume'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>',
        'komposisi'=> '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>',
    );
    $key = strtolower( trim( $type ) );
    return isset( $icons[ $key ] ) ? $icons[ $key ] : $icons['default'];
}

// Current page URL
$product_url = get_permalink();
$product_name = $product->get_name();
?>

<article id="product-<?php the_ID(); ?>" <?php wc_product_class( 'jt-single-product-wrapper-new max-w-7xl mx-auto md:px-4 md:py-4 px-0 py-0', $product ); ?>>

    <!-- Lightbox Overlay (Alpine.js) -->
    <div
        x-data
        x-show="$store.lightbox.open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="jt-lightbox-overlay"
        @click.self="$store.lightbox.close()"
        role="dialog"
        aria-modal="true"
        aria-label="Perbesar foto"
    >
        <button class="jt-lightbox-close" @click="$store.lightbox.close()" aria-label="Tutup">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <img class="jt-lightbox-img" :src="$store.lightbox.src" alt="<?php echo esc_attr( $product_name ); ?>">
    </div>

    <!-- Main 3-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        <!-- Column 1 & 2: Gallery & Detail & Shop Info & Specs (~75% width on desktop) -->
        <div class="lg:col-span-3 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-12 md:gap-6 gap-0 bg-transparent md:bg-white md:p-6 p-0 md:rounded-2xl rounded-none md:shadow-sm shadow-none md:border border-none" 
                 x-data="jtProductDetail('<?php echo esc_attr( $main_img_url ); ?>', '<?php echo esc_attr( $price_inner_html ); ?>')">

                <!-- Left: Gallery Subcolumn (5 cols) -->
                <div class="md:col-span-5 flex flex-col">
                    <!-- Main Image with Lightbox -->
                    <div class="jt-gallery-wrap aspect-square w-full overflow-hidden md:rounded-xl rounded-none bg-gray-50 md:border border-0 flex items-center justify-center mb-3"
                         @click="$store.lightbox.show(mainImg)">
                        <img :src="mainImg"
                             alt="<?php echo esc_attr( $product_name ); ?>"
                             class="w-full h-full object-cover transition-all duration-300 hover:scale-105">
                        <div class="jt-gallery-zoom-hint">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 8v6M8 11h6"/></svg>
                            Perbesar
                        </div>
                    </div>

                    <!-- Thumbnails -->
                    <?php if ( count( $attachment_ids ) > 1 ) : ?>
                    <div class="flex gap-2 overflow-x-auto pb-1" role="list" aria-label="<?php esc_attr_e( 'Galeri Produk', 'jendela-ternak' ); ?>">
                        <?php foreach ( $attachment_ids as $idx => $att_id ) :
                            $thumb_url = wp_get_attachment_image_url( $att_id, 'thumbnail' );
                            $large_url = wp_get_attachment_image_url( $att_id, 'large' );
                            $alt       = get_post_meta( $att_id, '_wp_attachment_image_alt', true ) ?: $product_name;
                        ?>
                        <button
                            type="button"
                            class="w-16 h-16 flex-shrink-0 rounded-md overflow-hidden border-2 border-transparent transition-all hover:border-[#0B5E34] focus:outline-none"
                            :class="mainImg === '<?php echo esc_js( $large_url ); ?>' ? 'border-[#0B5E34]' : 'border-transparent'"
                            @click="switchImage('<?php echo esc_js( $large_url ); ?>')"
                            role="listitem"
                        >
                            <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" class="w-full h-full object-cover">
                        </button>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Right: Product Detail Info Subcolumn (7 cols) -->
                <div class="md:col-span-7 flex flex-col justify-between bg-white md:bg-transparent p-4 md:p-0 md:rounded-none rounded-none md:shadow-none shadow-none md:border border-none" itemscope itemtype="https://schema.org/Product">
                    <meta itemprop="name" content="<?php echo esc_attr( $product_name ); ?>">

                    <div>
                        <!-- Stock Badge -->
                        <?php if ( $in_stock ) : ?>
                            <span class="jt-stock-badge jt-stock-badge--in">
                                <span class="jt-stock-badge__dot"></span>
                                <?php if ( $managing_stock && $stock_qty !== null ) : ?>
                                    Tersedia &mdash; <?php echo esc_html( $stock_qty ); ?> stok
                                <?php else : ?>
                                    Tersedia
                                <?php endif; ?>
                            </span>
                        <?php else : ?>
                            <span class="jt-stock-badge jt-stock-badge--out">
                                <span class="jt-stock-badge__dot"></span>
                                Stok Habis
                            </span>
                        <?php endif; ?>

                        <!-- Title -->
                        <h1 class="text-2xl font-extrabold text-[#0B5E34] leading-tight mb-2">
                            <?php the_title(); ?>
                        </h1>

                        <!-- Rating & Reviews -->
                        <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-4 pb-3 border-b border-gray-100">
                            <!-- Star Rating -->
                            <div class="flex items-center">
                                <span class="font-bold text-sm mr-1 <?php echo $avg_rating > 0 ? 'text-gray-700' : 'text-gray-400'; ?>">
                                    <?php echo esc_html( $avg_rating > 0 ? number_format( $avg_rating, 1 ) : '0.0' ); ?>
                                </span>
                                <div class="flex gap-0.5">
                                    <?php 
                                    $rounded_rating = $avg_rating > 0 ? round( $avg_rating ) : 0;
                                    for ( $i = 1; $i <= 5; $i++ ) : 
                                        $star_color = $i <= $rounded_rating ? 'text-[#D4B106]' : 'text-gray-200';
                                    ?>
                                        <svg class="w-3.5 h-3.5 fill-current <?php echo $star_color; ?>" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <span class="text-gray-300">|</span>
                            <span><strong class="text-gray-700"><?php echo esc_html( $rating_count > 0 ? $rating_count : '0' ); ?></strong> Penilaian</span>
                            <span class="text-gray-300">|</span>
                            <span><strong class="text-gray-700"><?php echo esc_html( $review_count > 0 ? $review_count : '0' ); ?></strong> Ulasan</span>
                        </div>

                        <!-- Price Block -->
                        <div class="flex items-center gap-3 bg-[#F9F9F9] p-4 rounded-xl border border-gray-100 mb-4 flex-wrap" x-html="priceHtml">
                            <?php echo $price_inner_html; ?>
                        </div>

                    </div>

                    <!-- WooCommerce Add to Cart & Variations -->
                    <div class="woocommerce-cart-form-wrapper" :class="{ 'is-open': $store.pdpDrawer.open, 'show-cart': $store.pdpDrawer.action === 'cart', 'show-buy': $store.pdpDrawer.action === 'buy' }">
                        <!-- Mobile Drawer Header -->
                        <div class="flex md:hidden items-start gap-4 border-b border-gray-100 pb-4 mb-4 relative">
                            <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-50 border border-gray-100 flex-shrink-0">
                                <img :src="mainImg" alt="<?php echo esc_attr( $product_name ); ?>" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 pr-6 text-left">
                                <h4 class="text-sm font-bold text-gray-800 line-clamp-2"><?php the_title(); ?></h4>
                                <div class="mt-1">
                                    <div class="text-[#0B5E34] font-extrabold text-base">
                                        <?php echo $product->get_price_html(); ?>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <?php if ( $managing_stock ) : ?>
                                        Stok: <span class="font-semibold text-gray-700"><?php echo esc_html( $stock_qty ); ?></span>
                                    <?php else : ?>
                                        Stok: <span class="font-semibold text-gray-700"><?php echo $in_stock ? 'Tersedia' : 'Habis'; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- Close Button -->
                            <button type="button" @click="$store.pdpDrawer.close()" class="absolute top-0 right-0 w-6 h-6 flex items-center justify-center text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Tutup">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <?php woocommerce_template_single_add_to_cart(); ?>
                    </div>

                    <!-- Share Strip -->
                    <div class="jt-share-strip" x-data="{ copied: false }">
                        <span class="jt-share-label">Bagikan:</span>
                        <!-- WhatsApp -->
                        <a
                            href="<?php echo esc_url( 'https://wa.me/?text=' . rawurlencode( $product_name . ' - ' . $product_url ) ); ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="jt-share-btn jt-share-btn--wa"
                            title="Bagikan ke WhatsApp"
                            aria-label="Bagikan ke WhatsApp"
                        >
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                        <!-- Telegram -->
                        <a
                            href="<?php echo esc_url( 'https://t.me/share/url?url=' . rawurlencode( $product_url ) . '&text=' . rawurlencode( $product_name ) ); ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="jt-share-btn jt-share-btn--telegram"
                            title="Bagikan ke Telegram"
                            aria-label="Bagikan ke Telegram"
                        >
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.017c.242-.216-.053-.337-.375-.122l-6.872 4.327-2.96-.924c-.643-.204-.657-.643.136-.953l11.57-4.46c.536-.196.997.12.828.932z"/></svg>
                        </a>
                        <!-- Threads -->
                        <a
                            href="<?php echo esc_url( 'https://threads.net/intent/post?text=' . rawurlencode( $product_name . ' - ' . $product_url ) ); ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="jt-share-btn jt-share-btn--threads"
                            title="Bagikan ke Threads"
                            aria-label="Bagikan ke Threads"
                        >
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 12c-2.5 0-3-1.5-3-2.5s.5-2.5 3-2.5 3 1.5 3 2.5-.5 2.5-3 2.5z"/><path d="M15 12v1.5a2.5 2.5 0 0 0 5 0V11a8 8 0 1 0-5.3 7.6"/></svg>
                        </a>
                        <!-- Facebook -->
                        <a
                            href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $product_url ) ); ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="jt-share-btn jt-share-btn--facebook"
                            title="Bagikan ke Facebook"
                            aria-label="Bagikan ke Facebook"
                        >
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <!-- Salin Link -->
                        <button
                            type="button"
                            class="jt-share-btn jt-share-btn--copy"
                            :class="{ 'copied': copied }"
                            @click="navigator.clipboard.writeText('<?php echo esc_js( $product_url ); ?>').then(() => { copied = true; setTimeout(() => copied = false, 2000); })"
                            title="Salin Link Produk"
                            aria-label="Salin link produk"
                        >
                            <svg x-show="!copied" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                            <svg x-show="copied" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                    </div>

                </div>

            </div>

            <!-- Spesifikasi Produk -->
            <div class="bg-white md:p-6 p-4 md:rounded-2xl rounded-none md:shadow-sm shadow-none md:border border-y border-gray-100 specs-card">
                <div class="border-b border-gray-100 pb-3 mb-4">
                    <h3 class="text-md font-extrabold text-[#0B5E34] uppercase tracking-wider">Spesifikasi Produk</h3>
                </div>
                <div class="space-y-0">
                    <!-- Kategori -->
                    <div class="jt-spec-row">
                        <div class="jt-spec-icon"><?php echo jt_spec_icon( 'kategori' ); ?></div>
                        <span class="jt-spec-key">Kategori</span>
                        <span class="jt-spec-val">
                            <?php echo wp_kses_post( wc_get_product_category_list( $product->get_id(), ' › ' ) ?: 'Umum' ); ?>
                        </span>
                    </div>
                    <!-- Merek -->
                    <div class="jt-spec-row">
                        <div class="jt-spec-icon"><?php echo jt_spec_icon( 'merek' ); ?></div>
                        <span class="jt-spec-key">Merek</span>
                        <span class="jt-spec-val">
                            <?php
                            $brand = get_post_meta( $product->get_id(), '_brand', true );
                            if ( ! $brand ) {
                                $brand_terms = wp_get_post_terms( $product->get_id(), 'product_brand', array( 'fields' => 'names' ) );
                                $brand = ! is_wp_error( $brand_terms ) && ! empty( $brand_terms ) ? implode( ', ', $brand_terms ) : 'Jendela Ternak Malang';
                            }
                            echo esc_html( $brand );
                            ?>
                        </span>
                    </div>

                    <!-- Dynamic Attributes -->
                    <?php
                    $attributes = $product->get_attributes();
                    foreach ( $attributes as $attribute ) :
                        if ( ! $attribute->get_visible() ) continue;
                        $label = wc_attribute_label( $attribute->get_name() );
                        $values = $attribute->is_taxonomy()
                            ? implode( ', ', wp_get_post_terms( $product->get_id(), $attribute->get_name(), [ 'fields' => 'names' ] ) )
                            : implode( ', ', $attribute->get_options() );
                        if ( empty( $values ) ) continue;

                        // Choose icon based on attribute name
                        $attr_key_lower = strtolower( $attribute->get_name() );
                        if ( strpos( $attr_key_lower, 'bahan' ) !== false || strpos( $attr_key_lower, 'material' ) !== false ) {
                            $icon_key = 'bahan';
                        } elseif ( strpos( $attr_key_lower, 'berat' ) !== false || strpos( $attr_key_lower, 'weight' ) !== false ) {
                            $icon_key = 'berat';
                        } elseif ( strpos( $attr_key_lower, 'volume' ) !== false || strpos( $attr_key_lower, 'isi' ) !== false ) {
                            $icon_key = 'volume';
                        } elseif ( strpos( $attr_key_lower, 'komposisi' ) !== false || strpos( $attr_key_lower, 'kandungan' ) !== false ) {
                            $icon_key = 'komposisi';
                        } else {
                            $icon_key = 'default';
                        }
                    ?>
                    <div class="jt-spec-row">
                        <div class="jt-spec-icon"><?php echo jt_spec_icon( $icon_key ); ?></div>
                        <span class="jt-spec-key"><?php echo esc_html( $label ); ?></span>
                        <span class="jt-spec-val"><?php echo esc_html( $values ); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Deskripsi Produk (Collapsible) -->
            <?php
            $has_long_desc = $product->get_description() && strlen( strip_tags( $product->get_description() ) ) > 300;
            $default_expanded = ! $has_long_desc;
            ?>
            <div class="bg-white md:p-6 p-4 md:rounded-2xl rounded-none md:shadow-sm shadow-none md:border border-y border-gray-100 description-card"
                 x-data="{ expanded: <?php echo $default_expanded ? 'true' : 'false'; ?> }">
                <div class="border-b border-gray-100 pb-3 mb-4">
                    <h3 class="text-md font-extrabold text-[#0B5E34] uppercase tracking-wider">Deskripsi Produk</h3>
                </div>
                <div class="jt-desc-fade<?php echo $default_expanded ? ' expanded' : ''; ?>" :class="expanded ? 'expanded' : ''">
                    <div class="jt-desc-body text-xs text-gray-700 leading-relaxed space-y-3 <?php echo $default_expanded ? 'expanded' : 'collapsed'; ?>"
                         :class="expanded ? 'expanded' : 'collapsed'">
                        <?php if ( $product->get_description() ) : ?>
                            <?php echo wp_kses_post( wpautop( $product->get_description() ) ); ?>
                        <?php else : ?>
                            <p><?php esc_html_e( 'Belum ada deskripsi untuk produk ini.', 'jendela-ternak' ); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ( $has_long_desc ) : ?>
                <button
                    type="button"
                    class="jt-desc-toggle-btn"
                    :class="expanded ? 'expanded' : ''"
                    @click="expanded = !expanded"
                    aria-expanded="false"
                    :aria-expanded="expanded ? 'true' : 'false'"
                >
                    <span x-text="expanded ? 'Lebih Sedikit' : 'Lihat Selengkapnya'">Lihat Selengkapnya</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <?php endif; ?>
            </div>

            <!-- Ulasan Pembeli -->
            <div class="bg-white md:p-6 p-4 md:rounded-2xl rounded-none md:shadow-sm shadow-none md:border border-y border-gray-100 jt-reviews-card reviews-card">
                <div class="border-b border-gray-100 pb-3 mb-4">
                    <h3 class="text-md font-extrabold text-[#0B5E34] uppercase tracking-wider">Ulasan Pembeli</h3>
                </div>

                <?php if ( $review_count > 0 || $rating_count > 0 ) : ?>
                <!-- Rating Summary -->
                <div class="jt-review-summary">
                    <!-- Score Box -->
                    <div class="jt-review-score-box">
                        <div class="jt-review-score-num"><?php echo esc_html( $avg_rating > 0 ? number_format( $avg_rating, 1 ) : '5.0' ); ?></div>
                        <div class="jt-review-score-stars">
                            <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                                <?php echo jt_icon_star(); ?>
                            <?php endfor; ?>
                        </div>
                        <div class="jt-review-score-label">dari 5<br>(<?php echo esc_html( max( $review_count, $rating_count ) ); ?> ulasan)</div>
                    </div>

                    <!-- Bar Chart -->
                    <div class="jt-review-bars">
                        <?php for ( $star = 5; $star >= 1; $star-- ) :
                            $star_count = $rating_counts_by_star[ $star ] ?? 0;
                            $total      = array_sum( $rating_counts_by_star );
                            $pct        = $total > 0 ? round( ( $star_count / $total ) * 100 ) : 0;
                        ?>
                        <div class="jt-review-bar-row">
                            <div class="jt-review-bar-label">
                                <?php echo esc_html( $star ); ?>
                                <svg viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </div>
                            <div class="jt-review-bar-track">
                                <div class="jt-review-bar-fill" style="width: <?php echo esc_attr( $pct ); ?>%"></div>
                            </div>
                            <div class="jt-review-bar-count"><?php echo esc_html( $star_count ); ?></div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Review Cards (via WooCommerce comments_template with custom styling) -->
                <div class="text-xs">
                    <?php
                    if ( comments_open() ) {
                        comments_template();
                    } else {
                        echo '<div class="jt-reviews-card"><p class="woocommerce-noreviews">' . esc_html__( 'Ulasan dinonaktifkan untuk produk ini.', 'jendela-ternak' ) . '</p></div>';
                    }
                    ?>
                </div>
            </div>

        </div>

        <!-- Column 3: Sidebar — Upsell Products -->
        <div class="lg:col-span-1 space-y-6">

            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 sidebar-card sticky top-24">
                <div class="border-b border-gray-100 pb-2 mb-3">
                    <h3 class="text-xs font-extrabold text-[#0B5E34] uppercase tracking-wider">Produk Pilihan Toko</h3>
                </div>

                <?php
                // Get upsell product IDs set in product admin
                $upsell_ids = $product->get_upsell_ids();

                // Fallback: if no upsells, query products from same category (excluding current)
                if ( empty( $upsell_ids ) ) {
                    $term_ids = wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
                    $fallback_args = array(
                        'post_type'      => 'product',
                        'posts_per_page' => 3,
                        'post_status'    => 'publish',
                        'post__not_in'   => array( $product->get_id() ),
                        'orderby'        => 'meta_value_num',
                        'meta_key'       => 'total_sales',
                        'order'          => 'DESC',
                    );
                    if ( ! empty( $term_ids ) ) {
                        $fallback_args['tax_query'] = array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field'    => 'term_id',
                                'terms'    => $term_ids,
                            ),
                        );
                    }
                    $fallback_query = new WP_Query( $fallback_args );
                    if ( $fallback_query->have_posts() ) :
                        echo '<div class="space-y-3">';
                        while ( $fallback_query->have_posts() ) :
                            $fallback_query->the_post();
                            $up = wc_get_product( get_the_ID() );
                            if ( ! $up || ! $up->is_visible() ) continue;
                            $up_link = get_permalink();
                            $up_name = get_the_title();
                            $up_img_id  = $up->get_image_id();
                            $up_img_url = $up_img_id ? wp_get_attachment_image_url( $up_img_id, 'thumbnail' ) : wc_placeholder_img_src( 'thumbnail' );
                            $up_sale    = $up->get_sale_price();
                            $up_reg     = $up->get_regular_price();
                            $up_price   = $up->get_price();
                            $up_pct     = ( $up_sale && $up_reg > $up_sale ) ? round( ( ( $up_reg - $up_sale ) / $up_reg ) * 100 ) : 0;
                            ?>
                            <a href="<?php echo esc_url( $up_link ); ?>" class="flex gap-3 group block">
                                <div class="w-14 h-14 rounded-lg overflow-hidden flex-shrink-0 bg-gray-50 border border-gray-100 relative">
                                    <img src="<?php echo esc_url( $up_img_url ); ?>" alt="<?php echo esc_attr( $up_name ); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <?php if ( $up_pct > 0 ) : ?>
                                        <span class="absolute top-0 right-0 bg-[#D4B106] text-black text-[8px] font-extrabold px-1 rounded-bl-md shadow-sm">-<?php echo esc_html( $up_pct ); ?>%</span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex flex-col justify-between py-0.5 min-w-0">
                                    <h4 class="text-[#0B5E34] text-[11px] font-bold line-clamp-2 leading-snug group-hover:underline"><?php echo esc_html( $up_name ); ?></h4>
                                    <div>
                                        <span class="text-[#0B5E34] text-xs font-extrabold block leading-none"><?php echo wp_kses_post( wc_price( $up_price ) ); ?></span>
                                        <?php if ( $up_pct > 0 ) : ?>
                                            <span class="text-gray-400 text-[9px] line-through leading-none"><?php echo wp_kses_post( wc_price( $up_reg ) ); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                        echo '</div>';
                    else :
                        echo '<p class="text-xs text-gray-400 text-center py-4">Belum ada produk pilihan.</p>';
                    endif;

                } else {
                    // Render upsell products
                    echo '<div class="space-y-3">';
                    $shown = 0;
                    foreach ( array_slice( $upsell_ids, 0, 4 ) as $upsell_id ) :
                        $up = wc_get_product( $upsell_id );
                        if ( ! $up || ! $up->is_visible() ) continue;
                        $shown++;
                        $up_link = $up->get_permalink();
                        $up_name = $up->get_name();
                        $up_img_id  = $up->get_image_id();
                        $up_img_url = $up_img_id ? wp_get_attachment_image_url( $up_img_id, 'thumbnail' ) : wc_placeholder_img_src( 'thumbnail' );
                        $up_sale    = $up->get_sale_price();
                        $up_reg     = $up->get_regular_price();
                        $up_price   = $up->get_price();
                        $up_pct     = ( $up_sale && $up_reg > $up_sale ) ? round( ( ( $up_reg - $up_sale ) / $up_reg ) * 100 ) : 0;
                        ?>
                        <a href="<?php echo esc_url( $up_link ); ?>" class="flex gap-3 group block">
                            <div class="w-14 h-14 rounded-lg overflow-hidden flex-shrink-0 bg-gray-50 border border-gray-100 relative">
                                <img src="<?php echo esc_url( $up_img_url ); ?>" alt="<?php echo esc_attr( $up_name ); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <?php if ( $up_pct > 0 ) : ?>
                                    <span class="absolute top-0 right-0 bg-[#D4B106] text-black text-[8px] font-extrabold px-1 rounded-bl-md shadow-sm">-<?php echo esc_html( $up_pct ); ?>%</span>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-col justify-between py-0.5 min-w-0">
                                <h4 class="text-[#0B5E34] text-[11px] font-bold line-clamp-2 leading-snug group-hover:underline"><?php echo esc_html( $up_name ); ?></h4>
                                <div>
                                    <span class="text-[#0B5E34] text-xs font-extrabold block leading-none"><?php echo wp_kses_post( wc_price( $up_price ) ); ?></span>
                                    <?php if ( $up_pct > 0 ) : ?>
                                        <span class="text-gray-400 text-[9px] line-through leading-none"><?php echo wp_kses_post( wc_price( $up_reg ) ); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                        <?php
                    endforeach;
                    if ( $shown === 0 ) {
                        echo '<p class="text-xs text-gray-400 text-center py-4">Belum ada produk pilihan.</p>';
                    }
                    echo '</div>';
                }
                ?>

                <!-- Link to shop -->
                <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="block mt-4 text-center text-[11px] font-bold text-[#0B5E34] hover:underline">
                    Lihat Semua Produk &rarr;
                </a>
            </div>

        </div>

    </div><!-- /.grid 3-column -->

    <!-- ═══════════════════════════════════════════════════════
         PRODUK TERKAIT — From same category, horizontal scroll
         ═══════════════════════════════════════════════════════ -->
    <?php
    // Get category term IDs of current product
    $cat_term_ids = wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'ids' ) );

    $related_args = array(
        'post_type'      => 'product',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'post__not_in'   => array( $product->get_id() ),
        'orderby'        => 'rand',
    );
    if ( ! empty( $cat_term_ids ) ) {
        $related_args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $cat_term_ids,
                'operator' => 'IN',
            ),
        );
    }
    $related_query = new WP_Query( $related_args );
    ?>

    <?php if ( $related_query->have_posts() ) : ?>
    <section class="bg-white md:p-6 p-4 md:rounded-2xl rounded-none md:shadow-sm shadow-none md:border border-y border-gray-100 mt-6" aria-labelledby="jt-related-heading">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-5">
            <div class="flex items-center gap-2">
                <span class="text-xl">🛒</span>
                <h2 class="text-md font-extrabold text-[#0B5E34] uppercase tracking-wider" id="jt-related-heading">
                    Produk Terkait
                </h2>
            </div>
            <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="text-xs font-bold text-[#0B5E34] hover:underline">
                Lihat Semua &rarr;
            </a>
        </div>
        <div class="jt-horizontal-scroll">
            <?php
            while ( $related_query->have_posts() ) :
                $related_query->the_post();
                get_template_part( 'template-parts/product/product-card' );
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
    </section>
    <?php endif; ?>

    <?php do_action( 'woocommerce_after_single_product_summary' ); ?>
    <?php do_action( 'woocommerce_after_single_product' ); ?>

    <!-- Mobile Drawer Overlay -->
    <div class="jt-drawer-overlay md:hidden fixed inset-0 bg-black/50 z-[200]" x-show="$store.pdpDrawer.open" @click="$store.pdpDrawer.close()" x-cloak x-transition></div>

</article>
