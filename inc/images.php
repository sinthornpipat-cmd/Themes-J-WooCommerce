<?php
/**
 * Bundled sample images used as Customizer defaults and as the product placeholder.
 *
 * The images disappear from the front end once you upload your own or clear the
 * setting in the Customizer.
 *
 * @package j-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------- Bundled image defaults ---------- */
/**
 * Get a URL to a bundled image.
 *
 * @param string $file File name inside assets/img.
 * @return string
 */
function jshop_img_url( $file ) {
	return get_template_directory_uri() . '/assets/img/' . $file;
}
/**
 * Get the bundled sample image URL for an image setting key.
 *
 * @param string $key Customizer setting ID.
 * @return string URL or empty string.
 */
function jshop_default_img( $key ) {
	static $map = null;
	if ( null === $map ) {
		$map = array(
			'jshop_hero_1'    => 'hero-1.jpg',
			'jshop_hero_2'    => 'hero-2.jpg',
			'jshop_hero_3'    => 'hero-3.jpg',
			'jshop_popup_img' => 'popup.png',
		);
	}
	return isset( $map[ $key ] ) ? jshop_img_url( $map[ $key ] ) : '';
}

// Product without a photo → themed placeholder.
/**
 * Use the themed placeholder for products without an image.
 */
add_filter(
	'woocommerce_placeholder_img_src',
	function () {
		return jshop_img_url( 'placeholder.jpg' );
	}
);
