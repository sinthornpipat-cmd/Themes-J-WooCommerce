<?php
/**
 * One-time copy of Customizer settings from the old theme (Themes J WooCommerce,
 * and before that Petchsiam Shop) to J Shop, renaming the dps_ setting IDs to jshop_.
 *
 * Runs only while J Shop is the active theme, never overwrites a J Shop setting
 * that already has a value, and records that it ran so it happens once.
 *
 * @package dps-store-extras
 */

defined( 'ABSPATH' ) || exit;

/**
 * Copy the old theme's Customizer settings into J Shop.
 *
 * @return void
 */
function dps_se_migrate_theme_mods() {
	if ( get_option( 'dps_se_migrated_j_shop' ) || 'j-shop' !== get_stylesheet() || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$old = array();
	foreach ( array( 'theme_mods_petchsiam-shop', 'theme_mods_themes-j-woocommerce' ) as $option ) {
		$mods = get_option( $option );
		if ( is_array( $mods ) ) {
			$old = array_merge( $old, $mods );
		}
	}
	update_option( 'dps_se_migrated_j_shop', 1, false );
	if ( ! $old ) {
		return;
	}

	$new    = get_option( 'theme_mods_j-shop', array() );
	$new    = is_array( $new ) ? $new : array();
	$copied = 0;
	foreach ( $old as $key => $value ) {
		$new_key = $key;
		// Checkout labels and the fast-cart switch belong to this plugin and keep their IDs.
		if ( 0 === strpos( $key, 'dps_' ) && 0 !== strpos( $key, 'dps_co_' ) && 'dps_fast_cart' !== $key ) {
			$new_key = 'jshop_' . substr( $key, 4 );
		}
		if ( ! array_key_exists( $new_key, $new ) ) {
			$new[ $new_key ] = $value;
			++$copied;
		}
	}
	update_option( 'theme_mods_j-shop', $new );
	set_transient( 'dps_se_migrated_notice', $copied, DAY_IN_SECONDS );
}
add_action( 'admin_init', 'dps_se_migrate_theme_mods' );

/**
 * Tell the admin once that the settings were copied.
 *
 * @return void
 */
function dps_se_migrated_notice() {
	$copied = get_transient( 'dps_se_migrated_notice' );
	if ( false === $copied || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	delete_transient( 'dps_se_migrated_notice' );
	printf(
		'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
		esc_html( sprintf( 'DPS Store Extras: คัดลอกค่า Customizer จากธีมเดิมมาที่ J Shop แล้ว (%d รายการ)', (int) $copied ) )
	);
}
add_action( 'admin_notices', 'dps_se_migrated_notice' );
