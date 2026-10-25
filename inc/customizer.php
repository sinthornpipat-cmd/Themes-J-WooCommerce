<?php
/**
 * Customizer settings for the theme.
 *
 * @package themes-j-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default values for the five home page product sections.
 *
 * Shared by the Customizer and the front end, so the sections show on a fresh
 * install before the Customizer has ever been saved.
 *
 * @return array<int,array<string,string>> Section number => title, cat, count, sub.
 */
function dps_section_defaults() {
	return array(
		1 => array(
			'title' => 'กล่องเก็บของ',
			'cat'   => 'storage-box',
			'count' => '8',
			'sub'   => 'กล่องเก็บของหลายแบบ หลายขนาด เลือกใช้ได้ตามต้องการ',
		),
		2 => array(
			'title' => 'ตู้ลิ้นชัก & ชั้นวาง',
			'cat'   => 'drawers-cabinet',
			'count' => '4',
			'sub'   => 'ใช้ได้ทั้งบ้านและออฟฟิศ จัดเก็บง่าย สะดวก',
		),
		3 => array(
			'title' => 'ตะกร้าผ้า & ซักรีด',
			'cat'   => 'laundry',
			'count' => '4',
			'sub'   => 'หลายแบบ หลายขนาด สำหรับใช้งานในชีวิตประจำวัน',
		),
		4 => array(
			'title' => 'ครัว',
			'cat'   => 'kitchen',
			'count' => '4',
			'sub'   => 'จัดเก็บของใช้ในบ้านให้หยิบใช้ง่ายและสะดวก',
		),
		5 => array(
			'title' => 'Heavy Duty',
			'cat'   => 'heavy-duty',
			'count' => '4',
			'sub'   => 'สินค้าสำหรับงานหนัก แข็งแรง ทนทาน',
		),
	);
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
					'default'           => dps_default_img( $id ),
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
						'section' => 'dps_style',
					)
				)
			);
		};

		$wp->add_panel(
			'dps',
			array(
				'title'    => 'Themes J WooCommerce',
				'priority' => 30,
			)
		);

		// Style.
		$wp->add_section(
			'dps_style',
			array(
				'title' => 'สีธีม',
				'panel' => 'dps',
			)
		);
		$color( 'dps_brand', 'สีหลัก (Brand)', '#1456c8' );
		$color( 'dps_accent', 'สีเน้น (ป้าย/ราคาลดราคา)', '#d9480f' );
		$color( 'dps_ink', 'สีตัวอักษร/ส่วนท้าย', '#14213d' );

		// Announcement + badge.
		$wp->add_section(
			'dps_top',
			array(
				'title' => 'แถบประกาศ & ป้ายสินค้า',
				'panel' => 'dps',
			)
		);
		$text( 'dps_announce', 'ข้อความแถบประกาศ', '🛒 สั่งซื้อออนไลน์ได้ที่ Shopee', 'dps_top' );
		$text( 'dps_announce_url', 'ลิงก์แถบประกาศ', 'https://shopee.co.th/', 'dps_top', 'url' );
		$text( 'dps_badge', 'ข้อความป้ายบนการ์ดสินค้า เช่น “ลดสูงสุด 50%” (เว้นว่าง = ไม่แสดง)', '', 'dps_top' );

		// Hero.
		$wp->add_section(
			'dps_hero',
			array(
				'title' => 'สไลด์แบนเนอร์หน้าแรก',
				'panel' => 'dps',
			)
		);
		for ( $i = 1; $i <= 5; $i++ ) {
			$img( "dps_hero_$i", "แบนเนอร์ $i (แนะนำ 1920×700)", 'dps_hero' );
			$text( "dps_hero_url_$i", "ลิงก์แบนเนอร์ $i", '', 'dps_hero', 'url' );
		}
		$text( 'dps_hero_cta', 'ข้อความปุ่มบนแบนเนอร์ (แสดงเมื่อใช้รูปของคุณเอง)', 'ดูสินค้า', 'dps_hero' );

		// Trust strip.
		$wp->add_section(
			'dps_trust',
			array(
				'title' => 'แถบจุดขาย 4 ช่อง',
				'panel' => 'dps',
			)
		);
		$default_values = array(
			array( 'ผู้ผลิตของใช้พลาสติกในบ้าน', 'รายใหญ่ของประเทศไทย' ),
			array( 'Thailand Trust Mark', 'รางวัลประจำปี 2566' ),
			array( 'สินค้ากว่า 240 รายการ', '10 หมวดหมู่ ครอบคลุมทุกการใช้งาน' ),
			array( 'สั่งซื้อผ่าน Shopee', 'สะดวก รวดเร็ว ปลอดภัย' ),
		);
		foreach ( $default_values as $k => $d ) {
			$n = $k + 1;
			$text( "dps_trust_t_$n", "ช่อง $n หัวข้อ", $d[0], 'dps_trust' );
			$text( "dps_trust_s_$n", "ช่อง $n คำอธิบาย", $d[1], 'dps_trust' );
		}

		// Product sections.
		foreach ( dps_section_defaults() as $n => $d ) {
			$sec = "dps_sec_$n";
			$wp->add_section(
				$sec,
				array(
					'title'       => "เซกชันสินค้า $n",
					'panel'       => 'dps',
					'description' => 'ใส่ slug ของหมวดหมู่สินค้า WooCommerce (Products → Categories) เว้นว่างเพื่อซ่อนเซกชัน',
				)
			);
			$text( "{$sec}_title", 'หัวข้อ', $d['title'], $sec );
			$text( "{$sec}_sub", 'คำโปรย (ไม่บังคับ)', $d['sub'], $sec );
			$text( "{$sec}_cat", 'Slug หมวดหมู่สินค้า', $d['cat'], $sec );
			$text( "{$sec}_count", 'จำนวนสินค้าที่แสดง', $d['count'], $sec );
			$img( "{$sec}_banner", 'รูปแบนเนอร์เหนือสินค้า (ไม่บังคับ)', $sec );
		}

		// Sections toggle.
		$wp->add_section(
			'dps_blocks',
			array(
				'title' => 'เปิด/ปิดบล็อกหน้าแรก',
				'panel' => 'dps',
			)
		);
		foreach ( array(
			'trust'   => 'แถบจุดขาย',
			'cats'    => 'ตารางหมวดหมู่สินค้า',
			'feed'    => 'ไอเดียจัดบ้าน',
			'reviews' => 'รีวิวลูกค้า',
			'faq'     => 'FAQ',
		) as $k => $l ) {
			$wp->add_setting(
				"dps_show_$k",
				array(
					'default'           => true,
					'sanitize_callback' => 'wp_validate_boolean',
				)
			);
			$wp->add_control(
				"dps_show_$k",
				array(
					'label'   => "แสดง: $l",
					'section' => 'dps_blocks',
					'type'    => 'checkbox',
				)
			);
		}
		$text( 'dps_cats_title', 'หัวข้อหมวดหมู่สินค้า', 'หมวดหมู่สินค้า', 'dps_blocks' );
		$text( 'dps_reviews_title', 'หัวข้อรีวิว', 'เสียงจากลูกค้า', 'dps_blocks' );
		$text( 'dps_feed_title', 'หัวข้อไอเดียจัดบ้าน', 'ไอเดียจัดบ้าน', 'dps_blocks' );
		$text( 'dps_faq_title', 'หัวข้อ FAQ', 'คำถามที่พบบ่อย', 'dps_blocks' );

		// Popup.
		$wp->add_section(
			'dps_popup',
			array(
				'title' => 'ป๊อปอัปโค้ดส่วนลด',
				'panel' => 'dps',
			)
		);
		$wp->add_setting(
			'dps_popup_on',
			array(
				'default'           => false,
				'sanitize_callback' => 'wp_validate_boolean',
			)
		);
		$wp->add_control(
			'dps_popup_on',
			array(
				'label'   => 'เปิดใช้งาน',
				'section' => 'dps_popup',
				'type'    => 'checkbox',
			)
		);
		$text( 'dps_popup_title', 'หัวข้อ', 'รับส่วนลดพิเศษ', 'dps_popup' );
		$text( 'dps_popup_sub', 'เงื่อนไข', 'แก้ข้อความและเงื่อนไขได้ใน Customizer', 'dps_popup' );
		$text( 'dps_popup_code', 'โค้ดส่วนลด', 'SAMPLE10', 'dps_popup' );
		$text( 'dps_popup_minutes', 'นับถอยหลัง (นาที)', '5', 'dps_popup' );
		$img( 'dps_popup_img', 'รูปประกอบ (PNG โปร่งใส)', 'dps_popup' );

		// Mobile nav.
		$wp->add_section(
			'dps_mnav',
			array(
				'title' => 'เมนูล่างบนมือถือ',
				'panel' => 'dps',
			)
		);
		$text( 'dps_mnav_2_t', 'ปุ่มที่ 2 ข้อความ', 'สินค้า', 'dps_mnav' );
		$text( 'dps_mnav_2_u', 'ปุ่มที่ 2 ลิงก์ (เว้นว่าง = หน้าร้านค้า)', '', 'dps_mnav', 'url' );
		$text( 'dps_mnav_3_t', 'ปุ่มที่ 3 ข้อความ', 'Shopee', 'dps_mnav' );
		$text( 'dps_mnav_3_u', 'ปุ่มที่ 3 ลิงก์', 'https://shopee.co.th/', 'dps_mnav', 'url' );
	}
);
