<?php
/**
 * header.php — loads the site header template part
 *
 * @package JendelaTernakMalang
 */
defined( 'ABSPATH' ) || exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?> x-data>
<?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/header/site-header' ); ?>
