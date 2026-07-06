<?php
/**
 * woocommerce/single-product/review.php
 * Custom review card template for each review comment.
 *
 * @package JendelaTernakMalang
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit;

$rating  = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
$author  = get_comment_author( $comment );
$date    = get_comment_date( get_option( 'date_format' ), $comment );
$content = get_comment_text( $comment );
$avatar  = get_avatar( $comment, 38, '', $author );
// First letter fallback
$initial = strtoupper( mb_substr( strip_tags( $author ), 0, 1 ) );

// Verified buyer?
$verified = wc_review_is_from_verified_owner( $comment->comment_ID );
?>

<li <?php comment_class( 'jt-review-card' ); ?> id="li-comment-<?php comment_ID(); ?>">

    <div class="jt-review-card__header">
        <div class="jt-review-card__avatar">
            <?php if ( $avatar ) : ?>
                <?php echo $avatar; ?>
            <?php else : ?>
                <?php echo esc_html( $initial ); ?>
            <?php endif; ?>
        </div>
        <div class="jt-review-card__meta">
            <div class="jt-review-card__author"><?php echo esc_html( $author ); ?></div>
            <div class="jt-review-card__date"><?php echo esc_html( $date ); ?></div>
        </div>
    </div>

    <!-- Stars -->
    <?php if ( $rating ) : ?>
    <div class="jt-review-card__stars" aria-label="<?php echo esc_attr( sprintf( __( 'Rated %d out of 5', 'woocommerce' ), $rating ) ); ?>">
        <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
            <svg class="<?php echo $i <= $rating ? '' : 'empty'; ?>" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

    <!-- Review Text -->
    <?php if ( $content ) : ?>
    <div class="jt-review-card__text">
        <?php echo wp_kses_post( $content ); ?>
    </div>
    <?php endif; ?>

    <!-- Verified Badge -->
    <?php if ( $verified ) : ?>
    <span class="jt-review-card__verified">
        <svg width="10" height="10" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        Pembeli Terverifikasi
    </span>
    <?php endif; ?>

</li>
