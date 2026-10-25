<?php
/**
 * Customizer settings for the theme.
 *
 * @package j-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register all Customizer panels, sections and controls for the theme.
 */
add_action(
	'customize_register',
	function ( $wp ) {
		$text  = function ( $id, $label, $default_value, $section, $type = 'text' ) use ( $wp ) {
			$wp->add_setting(
				$id,
				array(
					'default'           => $default_value,
					'sanitize_callback' => 'url' === $type ? 'esc_url_raw' : 'sanitize_text_field',
				)
			);
			$wp->add_control(
				$id,
				array(
					'label'   => $label,
					'section' => $section,
					'type'    => 'url' === $type ? 'url' : 'text',
				)
			);
		};
		$img   = function ( $id, $label, $section ) use ( $wp ) {
			$wp->add_setting(
				$id,
				array(
					'default'           => jshop_default_img( $id ),
					'sanitize_callback' => 'esc_url_raw',
				)
			);
			$wp->add_control(
				new WP_Customize_Image_Control(
					$wp,
					$id,
					array(
						'label'   => $label,
						'section' => $section,
					)
				)
			);
		};
		$color = function ( $id, $label, $default_value ) use ( $wp ) {
			$wp->add_setting(
				$id,
				array(
					'default'           => $default_value,
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);
			$wp->add_control(
				new WP_Customize_Color_Control(
					$wp,
					$id,
					array(
						'label'   => $label,
						'section' => 'jshop_style',
					)
				)
			);
		};

		$wp->add_panel(
			'jshop',
			array(
				'title'    => 'J Shop',
				'priority' => 30,
			)
		);

		// Style.
		$wp->add_section(
			'jshop_style',
			array(
				'title' => __( 'Colors', 'j-shop' ),
				'panel' => 'jshop',
			)
		);
		$color( 'jshop_brand', __( 'Main color (brand)', 'j-shop' ), '#1456c8' );
		$color( 'jshop_accent', __( 'Accent color (badges, sale prices)', 'j-shop' ), '#d9480f' );
		$color( 'jshop_ink', __( 'Text / footer color', 'j-shop' ), '#14213d' );

		// Announcement + badge.
		$wp->add_section(
			'jshop_top',
			array(
				'title' => __( 'Announcement bar & product badge', 'j-shop' ),
				'panel' => 'jshop',
			)
		);
		$text( 'jshop_announce', __( 'Announcement text', 'j-shop' ), __( 'Welcome to our store. Edit this text in the Customizer.', 'j-shop' ), 'jshop_top' );
		$text( 'jshop_announce_url', __( 'Announcement link (optional)', 'j-shop' ), '', 'jshop_top', 'url' );
		$text( 'jshop_badge', __( 'Badge text on product cards, e.g. “Up to 50% off” (empty = hide)', 'j-shop' ), '', 'jshop_top' );

		// Hero.
		$wp->add_section(
			'jshop_hero',
			array(
				'title' => __( 'Home page banner slider', 'j-shop' ),
				'panel' => 'jshop',
			)
		);
		for ( $i = 1; $i <= 5; $i++ ) {
			$img( "jshop_hero_$i", sprintf( /* translators: %d: banner number. */ __( 'Banner %d (recommended 1920×700)', 'j-shop' ), $i ), 'jshop_hero' );
			$text( "jshop_hero_url_$i", sprintf( /* translators: %d: banner number. */ __( 'Banner %d link', 'j-shop' ), $i ), '', 'jshop_hero', 'url' );
		}
		$text( 'jshop_hero_cta', __( 'Banner button text (shown with your own images)', 'j-shop' ), __( 'Shop now', 'j-shop' ), 'jshop_hero' );

		// Trust strip.
		$wp->add_section(
			'jshop_trust',
			array(
				'title'       => __( 'Trust strip (4 boxes)', 'j-shop' ),
				'panel'       => 'jshop',
				'description' => __( 'Each box shows one image that fills the box, with an optional link. Leave the image empty to hide the box.', 'j-shop' ),
			)
		);
		for ( $n = 1; $n <= 4; $n++ ) {
			$img( "jshop_trust_img_$n", sprintf( /* translators: %d: box number. */ __( 'Box %d image (recommended 600×300)', 'j-shop' ), $n ), 'jshop_trust' );
			$text( "jshop_trust_url_$n", sprintf( /* translators: %d: box number. */ __( 'Box %d link (optional)', 'j-shop' ), $n ), '', 'jshop_trust', 'url' );
		}

		// Popup.
		$wp->add_section(
			'jshop_popup',
			array(
				'title' => __( 'Discount code popup', 'j-shop' ),
				'panel' => 'jshop',
			)
		);
		$wp->add_setting(
			'jshop_popup_on',
			array(
				'default'           => false,
				'sanitize_callback' => 'wp_validate_boolean',
			)
		);
		$wp->add_control(
			'jshop_popup_on',
			array(
				'label'   => __( 'Enable', 'j-shop' ),
				'section' => 'jshop_popup',
				'type'    => 'checkbox',
			)
		);
		$text( 'jshop_popup_title', __( 'Heading', 'j-shop' ), __( 'Get a special discount', 'j-shop' ), 'jshop_popup' );
		$text( 'jshop_popup_sub', __( 'Conditions', 'j-shop' ), __( 'Edit this text and the conditions in the Customizer.', 'j-shop' ), 'jshop_popup' );
		$text( 'jshop_popup_code', __( 'Discount code', 'j-shop' ), 'SAMPLE10', 'jshop_popup' );
		$text( 'jshop_popup_minutes', __( 'Countdown (minutes)', 'j-shop' ), '5', 'jshop_popup' );
		$img( 'jshop_popup_img', __( 'Image (transparent PNG)', 'j-shop' ), 'jshop_popup' );
	}
);
