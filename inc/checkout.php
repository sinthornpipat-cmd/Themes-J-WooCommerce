<?php
/**
 * Checkout page tweaks (Shopify-style two-column layout).
 *
 * @package themes-j-woocommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Remove WooCommerce's "Have a coupon? Click here" notice from the top of the
 * classic checkout; the coupon field now lives in the order summary.
 *
 * @return void
 */
function dps_checkout_remove_coupon_toggle() {
	remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
}
add_action( 'init', 'dps_checkout_remove_coupon_toggle' );

/**
 * Add a body class on the checkout page (not the thank-you page) so the
 * stylesheet can switch the page to the two-column, full-bleed layout.
 *
 * @param string[] $classes Existing body classes.
 * @return string[] Filtered body classes.
 */
function dps_checkout_body_class( $classes ) {
	if ( function_exists( 'is_checkout' ) && is_checkout() && ! is_wc_endpoint_url( 'order-received' ) ) {
		$classes[] = 'dps-checkout';
	}
	return $classes;
}
add_filter( 'body_class', 'dps_checkout_body_class' );

/**
 * List of checkout fields whose labels can be edited in the Customizer.
 *
 * @return array Field key (without billing_/shipping_ prefix) => default label.
 */
function dps_checkout_label_defaults() {
	return array(
		'email'      => 'อีเมล',
		'first_name' => 'ชื่อ',
		'last_name'  => 'นามสกุล',
		'company'    => 'บริษัท (ไม่บังคับ)',
		'country'    => 'ประเทศ/ภูมิภาค',
		'address_1'  => 'ที่อยู่',
		'address_2'  => 'อพาร์ตเมนต์ ห้อง ฯลฯ (ไม่บังคับ)',
		'city'       => 'เขต/อำเภอ',
		'state'      => 'จังหวัด',
		'postcode'   => 'รหัสไปรษณีย์',
		'phone'      => 'เบอร์โทรศัพท์',
	);
}

/**
 * Register the "ข้อความหน้า Checkout" Customizer section: one text box per field label,
 * plus the section headings and the order-notes label. Blank = WooCommerce default.
 *
 * @param WP_Customize_Manager $wp Customizer manager.
 * @return void
 */
function dps_checkout_customize( $wp ) {
	$wp->add_section(
		'dps_checkout',
		array(
			'title'       => 'ข้อความหน้า Checkout',
			'description' => 'แก้ไขชื่อช่องกรอก (label) ในหน้าชำระเงิน เว้นว่าง = ใช้ค่าเดิมของ WooCommerce (ใช้กับ Checkout แบบคลาสสิก)',
			'panel'       => 'dps',
		)
	);
	$items                   = dps_checkout_label_defaults();
	$items['order_comments'] = 'หมายเหตุคำสั่งซื้อ';
	foreach ( $items as $key => $default ) {
		$id = 'dps_co_' . $key;
		$wp->add_setting(
			$id,
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp->add_control(
			$id,
			array(
				'section'     => 'dps_checkout',
				'type'        => 'text',
				'label'       => $default,
				'description' => 'ค่าเริ่มต้นของธีม: ' . $default,
			)
		);
	}
	$headings = array(
		'dps_co_h_billing'  => 'หัวข้อ: รายละเอียดการเรียกเก็บเงิน',
		'dps_co_h_shipping' => 'หัวข้อ: จัดส่งไปยังที่อยู่อื่น',
		'dps_co_h_summary'  => 'หัวข้อ: สรุปคำสั่งซื้อ',
	);
	foreach ( $headings as $id => $label ) {
		$wp->add_setting(
			$id,
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp->add_control(
			$id,
			array(
				'section' => 'dps_checkout',
				'type'    => 'text',
				'label'   => $label,
			)
		);
	}
}
add_action( 'customize_register', 'dps_checkout_customize' );

/**
 * Apply the Customizer labels to the classic checkout fields. Text-like fields
 * also get the label as placeholder; the email field is moved to the top.
 *
 * @param array $fields Checkout fields grouped by section.
 * @return array Filtered fields.
 */
function dps_checkout_fields( $fields ) {
	if ( isset( $fields['billing']['billing_email'] ) ) {
		$fields['billing']['billing_email']['priority'] = 5;
		$fields['billing']['billing_email']['class']    = array( 'form-row-wide' );
	}
	foreach ( array_keys( dps_checkout_label_defaults() ) as $key ) {
		$label = trim( (string) get_theme_mod( 'dps_co_' . $key, '' ) );
		if ( '' === $label ) {
			continue;
		}
		foreach ( array( 'billing', 'shipping' ) as $group ) {
			$id = $group . '_' . $key;
			if ( ! isset( $fields[ $group ][ $id ] ) ) {
				continue;
			}
			$fields[ $group ][ $id ]['label'] = $label;
			$type                             = isset( $fields[ $group ][ $id ]['type'] ) ? $fields[ $group ][ $id ]['type'] : 'text';
			if ( in_array( $type, array( 'text', 'email', 'tel' ), true ) ) {
				$fields[ $group ][ $id ]['placeholder'] = $label;
			}
		}
	}
	$notes = trim( (string) get_theme_mod( 'dps_co_order_comments', '' ) );
	if ( '' !== $notes && isset( $fields['order']['order_comments'] ) ) {
		$fields['order']['order_comments']['label']       = $notes;
		$fields['order']['order_comments']['placeholder'] = $notes;
	}
	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'dps_checkout_fields', 20 );

/**
 * Replace a WooCommerce checkout heading with the Customizer text, if one is set.
 *
 * @param string $text   Translated text.
 * @param string $source Original English text.
 * @param string $domain Text domain.
 * @return string Filtered text.
 */
function dps_checkout_headings( $text, $source, $domain ) {
	if ( 'woocommerce' !== $domain || ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
		return $text;
	}
	$map = array(
		'Billing details'              => 'dps_co_h_billing',
		'Ship to a different address?' => 'dps_co_h_shipping',
		'Your order'                   => 'dps_co_h_summary',
	);
	if ( isset( $map[ $source ] ) ) {
		$custom = trim( (string) get_theme_mod( $map[ $source ], '' ) );
		if ( '' !== $custom ) {
			return $custom;
		}
	}
	return $text;
}
add_filter( 'gettext', 'dps_checkout_headings', 20, 3 );
