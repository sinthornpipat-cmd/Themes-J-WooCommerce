<?php
/**
 * Performance tweaks: non-blocking fonts, deferred scripts, fewer requests.
 *
 * @package j-shop
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add preconnect hints for Google Fonts so the connection is ready earlier.
 *
 * @param array  $urls          URLs for the relation type.
 * @param string $relation_type Relation type (preconnect, dns-prefetch, ...).
 * @return array Filtered URLs.
 */
function jshop_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'jshop_resource_hints', 10, 2 );

/**
 * Load the Google Fonts stylesheet without blocking first paint
 * (media=print swap trick, with a <noscript> fallback).
 *
 * @param string $tag    Link tag HTML.
 * @param string $handle Style handle.
 * @return string Filtered tag.
 */
function jshop_async_fonts( $tag, $handle ) {
	if ( 'jshop-fonts' !== $handle ) {
		return $tag;
	}
	$async = str_replace( "media='all'", "media='print' onload=\"this.media='all'\"", $tag );
	return $async . '<noscript>' . $tag . '</noscript>';
}
add_filter( 'style_loader_tag', 'jshop_async_fonts', 10, 2 );

/**
 * Defer the theme scripts (WordPress 6.3+ loading strategy; ignored on older versions).
 *
 * @return void
 */
function jshop_defer_scripts() {
	foreach ( array( 'jshop-main', 'jshop-woo' ) as $handle ) {
		if ( wp_script_is( $handle, 'registered' ) ) {
			wp_script_add_data( $handle, 'strategy', 'defer' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'jshop_defer_scripts', 30 );

/**
 * Preload the first hero banner on the home page so the largest paint starts sooner.
 *
 * @return void
 */
function jshop_preload_hero() {
	if ( ! is_front_page() ) {
		return;
	}
	$img = jshop_mod( 'jshop_hero_1' );
	if ( $img ) {
		printf( '<link rel="preload" as="image" href="%s" fetchpriority="high" />' . "\n", esc_url( $img ) );
	}
}
add_action( 'wp_head', 'jshop_preload_hero', 2 );
