<?php
/**
 * front-page.php — Homepage template
 * Restructured with custom slider hero, horizontal scroll sections, and inline banners.
 *
 * @package JendelaTernakMalang
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( get_option( 'show_on_front' ) === 'page' ) {
    while ( have_posts() ) :
        the_post();
        the_content();
    endwhile;
} else {
    echo do_shortcode( '[jt_homepage]' );
}

get_footer();
