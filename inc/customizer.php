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
 * Default title and caption for the four trust-strip boxes on the home page.
 *
 * @return array<int,array<int,string>> List of [ title, caption ].
 */
function jshop_trust_defaults() {
	return array(
		array( 'จัดส่งรวดเร็ว', 'ส่งทั่วประเทศ' ),
		array( 'ชำระเงินปลอดภัย', 'รองรับหลายช่องทาง' ),
		array( 'สินค้าคุณภาพ', 'คัดสรรมาอย่างดี' ),
		array( 'ติดต่อง่าย', 'ทีมงานพร้อมช่วยเหลือ' ),
	);
}

/**
 * Default values for the five home page product sections.
 *
 * Shared by the Customizer and the front end, so the sections show on a fresh
 * install before the Customizer has ever been saved.
 *
 * @return array<int,array<string,string>> Section number => title, cat, count, sub.
 */
function jshop_section_defaults() {
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
				'title' => 'สีธีม',
				'panel' => 'jshop',
			)
		);
		$color( 'jshop_brand', 'สีหลัก (Brand)', '#1456c8' );
		$color( 'jshop_accent', 'สีเน้น (ป้าย/ราคาลดราคา)', '#d9480f' );
		$color( 'jshop_ink', 'สีตัวอักษร/ส่วนท้าย', '#14213d' );

		// Announcement + badge.
		$wp->add_section(
			'jshop_top',
			array(
				'title' => 'แถบประกาศ & ป้ายสินค้า',
				'panel' => 'jshop',
			)
		);
		$text( 'jshop_announce', 'ข้อความแถบประกาศ', 'ยินดีต้อนรับสู่ร้านของเรา แก้ข้อความนี้ได้ใน Customizer', 'jshop_top' );
		$text( 'jshop_announce_url', 'ลิงก์แถบประกาศ (ไม่บังคับ)', '', 'jshop_top', 'url' );
		$text( 'jshop_badge', 'ข้อความป้ายบนการ์ดสินค้า เช่น “ลดสูงสุด 50%” (เว้นว่าง = ไม่แสดง)', '', 'jshop_top' );

		// Hero.
		$wp->add_section(
			'jshop_hero',
			array(
				'title' => 'สไลด์แบนเนอร์หน้าแรก',
				'panel' => 'jshop',
			)
		);
		for ( $i = 1; $i <= 5; $i++ ) {
			$img( "jshop_hero_$i", "แบนเนอร์ $i (แนะนำ 1920×700)", 'jshop_hero' );
			$text( "jshop_hero_url_$i", "ลิงก์แบนเนอร์ $i", '', 'jshop_hero', 'url' );
		}
		$text( 'jshop_hero_cta', 'ข้อความปุ่มบนแบนเนอร์ (แสดงเมื่อใช้รูปของคุณเอง)', 'ดูสินค้า', 'jshop_hero' );

		// Trust strip.
		$wp->add_section(
			'jshop_trust',
			array(
				'title' => 'แถบจุดขาย 4 ช่อง',
				'panel' => 'jshop',
			)
		);
		foreach ( jshop_trust_defaults() as $k => $d ) {
			$n = $k + 1;
			$text( "jshop_trust_t_$n", "ช่อง $n หัวข้อ", $d[0], 'jshop_trust' );
			$text( "jshop_trust_s_$n", "ช่อง $n คำอธิบาย", $d[1], 'jshop_trust' );
		}

		// Product sections.
		foreach ( jshop_section_defaults() as $n => $d ) {
			$sec = "jshop_sec_$n";
			$wp->add_section(
				$sec,
				array(
					'title'       => "เซกชันสินค้า $n",
					'panel'       => 'jshop',
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
			'jshop_blocks',
			array(
				'title' => 'เปิด/ปิดบล็อกหน้าแรก',
				'panel' => 'jshop',
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
				"jshop_show_$k",
				array(
					'default'           => true,
					'sanitize_callback' => 'wp_validate_boolean',
				)
			);
			$wp->add_control(
				"jshop_show_$k",
				array(
					'label'   => "แสดง: $l",
					'section' => 'jshop_blocks',
					'type'    => 'checkbox',
				)
			);
		}
		$text( 'jshop_cats_title', 'หัวข้อหมวดหมู่สินค้า', 'หมวดหมู่สินค้า', 'jshop_blocks' );
		$text( 'dps_reviews_title', 'หัวข้อรีวิว', 'เสียงจากลูกค้า', 'jshop_blocks' );
		$text( 'dps_feed_title', 'หัวข้อไอเดียจัดบ้าน', 'ไอเดียจัดบ้าน', 'jshop_blocks' );
		$text( 'dps_faq_title', 'หัวข้อ FAQ', 'คำถามที่พบบ่อย', 'jshop_blocks' );

		// Popup.
		$wp->add_section(
			'jshop_popup',
			array(
				'title' => 'ป๊อปอัปโค้ดส่วนลด',
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
				'label'   => 'เปิดใช้งาน',
				'section' => 'jshop_popup',
				'type'    => 'checkbox',
			)
		);
		$text( 'jshop_popup_title', 'หัวข้อ', 'รับส่วนลดพิเศษ', 'jshop_popup' );
		$text( 'jshop_popup_sub', 'เงื่อนไข', 'แก้ข้อความและเงื่อนไขได้ใน Customizer', 'jshop_popup' );
		$text( 'jshop_popup_code', 'โค้ดส่วนลด', 'SAMPLE10', 'jshop_popup' );
		$text( 'jshop_popup_minutes', 'นับถอยหลัง (นาที)', '5', 'jshop_popup' );
		$img( 'jshop_popup_img', 'รูปประกอบ (PNG โปร่งใส)', 'jshop_popup' );

		// Mobile nav.
		$wp->add_section(
			'jshop_mnav',
			array(
				'title' => 'เมนูล่างบนมือถือ',
				'panel' => 'jshop',
			)
		);
		$text( 'jshop_mnav_2_t', 'ปุ่มที่ 2 ข้อความ', 'สินค้า', 'jshop_mnav' );
		$text( 'jshop_mnav_2_u', 'ปุ่มที่ 2 ลิงก์ (เว้นว่าง = หน้าร้านค้า)', '', 'jshop_mnav', 'url' );
		$text( 'jshop_mnav_3_t', 'ปุ่มที่ 3 ข้อความ', 'บัญชี', 'jshop_mnav' );
		$text( 'jshop_mnav_3_u', 'ปุ่มที่ 3 ลิงก์ (เว้นว่าง = หน้าบัญชีลูกค้า)', '', 'jshop_mnav', 'url' );
	}
);
