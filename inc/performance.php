<?php
/**
 * Performance tweaks: non-blocking fonts, deferred scripts, fewer requests.
 *
 * @package petchsiam-shop
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add preconnect hints for Google Fonts so the connection is ready earlier.
 *
 * @param array  $urls          URLs for the relation type.
 * @param string $relation_type Relation type (preconnect, dns-prefetch, ...).
 * @return array Filtered URLs.
 */
function dps_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = [
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		];
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'dps_resource_hints', 10, 2 );

/**
 * Load the Google Fonts stylesheet without blocking first paint
 * (media=print swap trick, with a <noscript> fallback).
 *
 * @param string $tag    Link tag HTML.
 * @param string $handle Style handle.
 * @return string Filtered tag.
 */
function dps_async_fonts( $tag, $handle ) {
	if ( 'dps-fonts' !== $handle ) {
		return $tag;
	}
	$async = str_replace( "media='all'", "media='print' onload=\"this.media='all'\"", $tag );
	return $async . '<noscript>' . $tag . '</noscript>';
}
add_filter( 'style_loader_tag', 'dps_async_fonts', 10, 2 );

/**
 * Defer the theme scripts (WordPress 6.3+ loading strategy; ignored on older versions).
 *
 * @return void
 */
function dps_defer_scripts() {
	foreach ( [ 'dps-main', 'dps-woo' ] as $handle ) {
		if ( wp_script_is( $handle, 'registered' ) ) {
			wp_script_add_data( $handle, 'strategy', 'defer' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'dps_defer_scripts', 30 );

/**
 * Preload the first hero banner on the home page so the largest paint starts sooner.
 *
 * @return void
 */
function dps_preload_hero() {
	if ( ! is_front_page() ) {
		return;
	}
	$img = dps_mod( 'dps_hero_1' );
	if ( $img ) {
		printf( '<link rel="preload" as="image" href="%s" fetchpriority="high" />' . "\n", esc_url( $img ) );
	}
}
add_action( 'wp_head', 'dps_preload_hero', 2 );

/**
 * Remove unused core head output and the emoji/embed scripts (fewer requests).
 *
 * @return void
 */
function dps_trim_head() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
}
add_action( 'init', 'dps_trim_head' );

/**
 * Stop WooCommerce's cart-fragments script from calling the server on every page
 * load. The theme already prints the cart count in PHP and refreshes the drawer
 * from its own add-to-cart / quantity responses. Turn off in the Customizer
 * (WooCommerce section) if a full-page cache shows a stale cart count.
 *
 * @return void
 */
function dps_dequeue_cart_fragments() {
	if ( ! get_theme_mod( 'dps_fast_cart', true ) ) {
		return;
	}
	if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() ) ) {
		return;
	}
	wp_dequeue_script( 'wc-cart-fragments' );
}
add_action( 'wp_enqueue_scripts', 'dps_dequeue_cart_fragments', 99 );

/**
 * Add the "fast cart" checkbox to the Customizer WooCommerce section.
 *
 * @param WP_Customize_Manager $wp Customizer manager.
 * @return void
 */
function dps_performance_customize( $wp ) {
	$wp->add_setting(
		'dps_fast_cart',
		[
			'default'           => true,
			'sanitize_callback' => 'wp_validate_boolean',
		]
	);
	$wp->add_control(
		'dps_fast_cart',
		[
			'label'       => 'โหมดเร็ว: ไม่โหลดข้อมูลตะกร้าซ้ำทุกหน้า',
			'description' => 'ลดคำขอเซิร์ฟเวอร์ 1 ครั้งต่อหน้า ถ้าใช้แคชทั้งหน้าแล้วเลขตะกร้าไม่ตรง ให้ปิดข้อนี้',
			'section'     => 'dps_woo',
			'type'        => 'checkbox',
		]
	);
}
add_action( 'customize_register', 'dps_performance_customize', 20 );
