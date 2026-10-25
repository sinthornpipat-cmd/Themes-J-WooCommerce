<?php
/**
 * Store behaviour that is plugin territory for a theme: trimming core head
 * output, skipping WooCommerce cart fragments, forcing AJAX add to cart.
 *
 * @package dps-store-extras
 */

defined( 'ABSPATH' ) || exit;

/**
 * Force AJAX add-to-cart on shop loops (the theme opens its mini-cart drawer).
 */
add_filter(
	'pre_option_woocommerce_enable_ajax_add_to_cart',
	function () {
		return 'yes';
	}
);

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
 * (WooCommerce → ความเร็ว) if a full-page cache shows a stale cart count.
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
	$wp->add_section(
		'dps_se_performance',
		array(
			'title' => 'ความเร็ว (DPS Store Extras)',
			'panel' => 'woocommerce',
		)
	);
	$wp->add_setting(
		'dps_fast_cart',
		array(
			'default'           => true,
			'sanitize_callback' => 'wp_validate_boolean',
		)
	);
	$wp->add_control(
		'dps_fast_cart',
		array(
			'label'       => 'โหมดเร็ว: ไม่โหลดข้อมูลตะกร้าซ้ำทุกหน้า',
			'description' => 'ลดคำขอเซิร์ฟเวอร์ 1 ครั้งต่อหน้า ถ้าใช้แคชทั้งหน้าแล้วเลขตะกร้าไม่ตรง ให้ปิดข้อนี้',
			'section'     => 'dps_se_performance',
			'type'        => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'dps_performance_customize', 20 );
