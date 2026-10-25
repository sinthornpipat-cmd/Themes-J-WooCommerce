<?php
/**
 * Checkout page tweaks (Shopify-style two-column layout).
 *
 * @package j-shop
 */

defined( 'ABSPATH' ) || exit;

/**
 * Remove WooCommerce's "Have a coupon? Click here" notice from the top of the
 * classic checkout; the coupon field now lives in the order summary.
 *
 * @return void
 */
function jshop_checkout_remove_coupon_toggle() {
	remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
}
add_action( 'init', 'jshop_checkout_remove_coupon_toggle' );

/**
 * Add a body class on the checkout page (not the thank-you page) so the
 * stylesheet can switch the page to the two-column, full-bleed layout.
 *
 * @param string[] $classes Existing body classes.
 * @return string[] Filtered body classes.
 */
function jshop_checkout_body_class( $classes ) {
	if ( function_exists( 'is_checkout' ) && is_checkout() && ! is_wc_endpoint_url( 'order-received' ) ) {
		$classes[] = 'dps-checkout';
	}
	return $classes;
}
add_filter( 'body_class', 'jshop_checkout_body_class' );
