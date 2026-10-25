<?php
/**
 * Footer: every text is editable in Customizer (click the pencil icon in the live preview),
 * plus 4 widget areas (one under each column).
 *
 * @package themes-j-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enable selective refresh for widgets in the Customizer.
 */
add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'customize-selective-refresh-widgets' );
	}
);

/* ---------- Defaults ---------- */
/**
 * Default values for every footer setting.
 *
 * @return array<string,mixed>
 */
function dps_ft_defaults() {
	return array(
		'dps_ft_show_1'         => true,
		'dps_ft_show_2'         => true,
		'dps_ft_show_3'         => true,
		'dps_ft_show_4'         => true,
		'dps_ft_company'        => '',
		'dps_address'           => '4 ซอยพระยามนธาตุ แยก 9 แขวงคลองบางบอน เขตบางบอน กรุงเทพมหานคร 10150',
		'dps_ft_phone_label'    => 'โทร:',
		'dps_phone'             => '(+66) 2415 8987, 2416 1040',
		'dps_ft_fax_label'      => 'แฟกซ์:',
		'dps_fax'               => '(+66) 2415-8981',
		'dps_ft_email_label'    => 'อีเมล:',
		'dps_email'             => '',
		'dps_ft_menu_title'     => 'เมนู',
		'dps_ft_shop_title'     => 'สั่งซื้อออนไลน์',
		'dps_shop_label'        => 'Shopee',
		'dps_shop_url'          => 'https://shopee.co.th/',
		'dps_ft_all_label'      => 'ดูสินค้าทั้งหมดในเว็บไซต์',
		'dps_ft_follow_title'   => 'ติดตามข่าวสาร',
		'dps_newsletter_t'      => 'รับข่าวสารและโปรโมชันจากเรา กรอกอีเมลของคุณ',
		'dps_newsletter_action' => '',
		'dps_ft_news_btn'       => 'สมัคร',
		'dps_ft_news_ph'        => 'Email',
		'dps_soc_facebook'      => '',
		'dps_soc_x'             => '',
		'dps_soc_instagram'     => '',
		'dps_soc_tiktok'        => '',
		'dps_soc_youtube'       => '',
		'dps_ft_copy'           => '© {year} {site}.',
		'dps_ft_credit_show'    => true,
		'dps_ft_credit'         => 'ขับเคลื่อนโดย <a href="https://wordpress.org" rel="noopener">WordPress</a>',
		'dps_ft_bg'             => '',
		'dps_ft_text'           => '',
	);
}
/**
 * Get a footer setting with its default.
 *
 * @param string $key Setting ID.
 * @return mixed
 */
function dps_ft( $key ) {
	$d = dps_ft_defaults();
	return get_theme_mod( $key, isset( $d[ $key ] ) ? $d[ $key ] : '' );
}
/**
 * Social network settings and labels.
 *
 * @return array<string,string> Setting ID => label.
 */
function dps_ft_socials() {
	return array(
		'dps_soc_facebook'  => 'Facebook',
		'dps_soc_x'         => 'X (Twitter)',
		'dps_soc_instagram' => 'Instagram',
		'dps_soc_tiktok'    => 'TikTok',
		'dps_soc_youtube'   => 'YouTube',
	);
}

/* ---------- Render one footer element (used by footer.php AND by Customizer partial refresh) ---------- */
/**
 * Render one footer element as a string (used by Customizer partial refresh).
 *
 * @param string $id Element ID, e.g. address, phone, socials.
 * @return string HTML.
 */
function dps_ft_render( $id ) {
	ob_start();
	dps_ft_output( $id );
	return ob_get_clean();
}

/**
 * Print one footer element. Every value is escaped as it is printed.
 *
 * @param string $id Element ID, e.g. address, phone, socials.
 * @return void
 */
function dps_ft_output( $id ) {
	switch ( $id ) {
		case 'company':
			echo esc_html( dps_ft( 'dps_ft_company' ) ? dps_ft( 'dps_ft_company' ) : get_bloginfo( 'name' ) );
			break;
		case 'address':
			echo esc_html( dps_ft( 'dps_address' ) );
			break;
		case 'phone':
			$v = dps_ft( 'dps_phone' );
			if ( $v ) {
				$tel = preg_replace( '/[^0-9+]/', '', explode( ',', $v )[0] );
				echo esc_html( dps_ft( 'dps_ft_phone_label' ) ) . ' <a href="tel:' . esc_attr( $tel ) . '">' . esc_html( $v ) . '</a>';
			}
			break;
		case 'fax':
			$v = dps_ft( 'dps_fax' );
			if ( $v ) {
				echo esc_html( dps_ft( 'dps_ft_fax_label' ) . ' ' . $v );
			}
			break;
		case 'email':
			$v = dps_ft( 'dps_email' );
			if ( $v ) {
				echo esc_html( dps_ft( 'dps_ft_email_label' ) ) . ' <a href="mailto:' . esc_attr( $v ) . '">' . esc_html( $v ) . '</a>';
			}
			break;
		case 'menu_title':
			echo esc_html( dps_ft( 'dps_ft_menu_title' ) );
			break;
		case 'shop_title':
			echo esc_html( dps_ft( 'dps_ft_shop_title' ) );
			break;
		case 'shop_link':
			$l = dps_ft( 'dps_shop_label' );
			$u = dps_ft( 'dps_shop_url' );
			if ( $l ) {
				if ( $u ) {
					echo '<a href="' . esc_url( $u ) . '" target="_blank" rel="noopener">' . esc_html( $l ) . '</a>';
				} else {
					echo esc_html( $l );
				}
			}
			break;
		case 'all_link':
			$l = dps_ft( 'dps_ft_all_label' );
			if ( $l && function_exists( 'wc_get_page_permalink' ) ) {
				echo '<a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . esc_html( $l ) . '</a>';
			}
			break;
		case 'follow_title':
			echo esc_html( dps_ft( 'dps_ft_follow_title' ) );
			break;
		case 'news_text':
			echo esc_html( dps_ft( 'dps_newsletter_t' ) );
			break;
		case 'news_form':
			$a = dps_ft( 'dps_newsletter_action' );
			if ( $a ) {
				echo '<form class="news-form" method="post" action="' . esc_url( $a ) . '"><label class="screen-reader-text" for="nl-email">อีเมล</label>'
					. '<input id="nl-email" type="email" name="email" placeholder="' . esc_attr( dps_ft( 'dps_ft_news_ph' ) ) . '" required>'
					. '<button class="btn" type="submit">' . esc_html( dps_ft( 'dps_ft_news_btn' ) ) . '</button></form>';
			}
			break;
		case 'socials':
			foreach ( dps_ft_socials() as $k => $label ) {
				$u = dps_ft( $k );
				if ( $u ) {
					echo '<a href="' . esc_url( $u ) . '" target="_blank" rel="noopener">' . esc_html( $label ) . '</a>';
				}
			}
			break;
		case 'copy':
			echo esc_html( str_replace( array( '{year}', '{site}' ), array( gmdate( 'Y' ), get_bloginfo( 'name' ) ), dps_ft( 'dps_ft_copy' ) ) );
			break;
		case 'credit':
			if ( dps_ft( 'dps_ft_credit_show' ) ) {
				echo wp_kses_post( dps_ft( 'dps_ft_credit' ) );
			}
			break;
	}
}
/**
 * Output a footer column widget area when it has widgets.
 *
 * @param int $n Column number 1-4.
 * @return void
 */
function dps_ft_widgets( $n ) {
	if ( is_active_sidebar( "footer-$n" ) ) {
		echo '<div class="ft-widgets">';
		dynamic_sidebar( "footer-$n" );
		echo '</div>'; }
}

/* ---------- Widget areas ---------- */
/**
 * Register four footer widget areas.
 */
add_action(
	'widgets_init',
	function () {
		for ( $i = 1; $i <= 4; $i++ ) {
			register_sidebar(
				array(
					'name'          => "ส่วนท้าย: คอลัมน์ $i",
					'id'            => "footer-$i",
					'description'   => 'วิดเจ็ตที่เพิ่มจะแสดงใต้เนื้อหาของคอลัมน์นี้',
					'before_widget' => '<section class="widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h4 class="widget-title">',
					'after_title'   => '</h4>',
				)
			);
		}
	}
);

/* ---------- Footer colours ---------- */
/**
 * Apply footer colour overrides from the Customizer.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		$bg  = get_theme_mod( 'dps_ft_bg' );
		$tx  = get_theme_mod( 'dps_ft_text' );
		$css = '';
		if ( $bg ) {
			$css .= '.site-footer{background:' . sanitize_hex_color( $bg ) . '}';
		}
		if ( $tx ) {
			$css .= '.site-footer,.site-footer h4{color:' . sanitize_hex_color( $tx ) . '}';
		}
		if ( $css ) {
			wp_add_inline_style( 'dps-style', $css );
		}
	},
	30
);

/* ---------- Customizer controls + click-to-edit partials ---------- */
/**
 * Register footer Customizer controls and click-to-edit partials.
 */
add_action(
	'customize_register',
	function ( $wp ) {
		$d    = dps_ft_defaults();
		$hint = 'ในหน้าตัวอย่างด้านขวา คลิกไอคอนดินสอข้างข้อความ เพื่อกระโดดมาที่ช่องแก้ไขนี้';

		$add = function ( $id, $label, $section, $type = 'text' ) use ( $wp, $d ) {
			$cb = array(
				'url'      => 'esc_url_raw',
				'textarea' => 'wp_kses_post',
				'checkbox' => 'wp_validate_boolean',
				'color'    => 'sanitize_hex_color',
			)[ $type ] ?? 'sanitize_text_field';
			$wp->add_setting(
				$id,
				array(
					'default'           => isset( $d[ $id ] ) ? $d[ $id ] : '',
					'sanitize_callback' => $cb,
					'transport'         => ( 'color' === $type || 0 === strpos( $id, 'dps_ft_show_' ) ) ? 'refresh' : 'postMessage',
				)
			);
			if ( 'color' === $type ) {
				$wp->add_control(
					new WP_Customize_Color_Control(
						$wp,
						$id,
						array(
							'label'   => $label,
							'section' => $section,
						)
					)
				);
			} else {
				$wp->add_control(
					$id,
					array(
						'label'   => $label,
						'section' => $section,
						'type'    => $type,
					)
				);
			}
		};
		$sec = function ( $id, $title, $desc = '' ) use ( $wp, $hint ) {
			$wp->add_section(
				$id,
				array(
					'title'       => $title,
					'panel'       => 'dps',
					'description' => trim( $desc . ' ' . $hint ),
				)
			);
		};

		$sec( 'dps_ft1', 'ส่วนท้าย: คอลัมน์ 1 (ข้อมูลติดต่อ)' );
		$add( 'dps_ft_show_1', 'แสดงคอลัมน์นี้', 'dps_ft1', 'checkbox' );
		$add( 'dps_ft_company', 'หัวข้อ / ชื่อบริษัท (เว้นว่าง = ใช้ชื่อเว็บไซต์)', 'dps_ft1' );
		$add( 'dps_address', 'ที่อยู่', 'dps_ft1', 'textarea' );
		$add( 'dps_ft_phone_label', 'ป้ายกำกับ: โทรศัพท์', 'dps_ft1' );
		$add( 'dps_phone', 'เบอร์โทรศัพท์ (คั่นหลายเบอร์ด้วย , — เบอร์แรกใช้เป็นลิงก์โทร)', 'dps_ft1' );
		$add( 'dps_ft_fax_label', 'ป้ายกำกับ: แฟกซ์', 'dps_ft1' );
		$add( 'dps_fax', 'เบอร์แฟกซ์ (เว้นว่าง = ซ่อน)', 'dps_ft1' );
		$add( 'dps_ft_email_label', 'ป้ายกำกับ: อีเมล', 'dps_ft1' );
		$add( 'dps_email', 'อีเมล (เว้นว่าง = ซ่อน)', 'dps_ft1' );

		$sec( 'dps_ft2', 'ส่วนท้าย: คอลัมน์ 2 (เมนู)', 'รายการเมนูแก้ที่ Appearance → Menus (ตำแหน่ง "เมนูส่วนท้าย")' );
		$add( 'dps_ft_show_2', 'แสดงคอลัมน์นี้', 'dps_ft2', 'checkbox' );
		$add( 'dps_ft_menu_title', 'หัวข้อคอลัมน์', 'dps_ft2' );

		$sec( 'dps_ft3', 'ส่วนท้าย: คอลัมน์ 3 (สั่งซื้อออนไลน์)' );
		$add( 'dps_ft_show_3', 'แสดงคอลัมน์นี้', 'dps_ft3', 'checkbox' );
		$add( 'dps_ft_shop_title', 'หัวข้อคอลัมน์', 'dps_ft3' );
		$add( 'dps_shop_label', 'ข้อความลิงก์ช่องทางสั่งซื้อ', 'dps_ft3' );
		$add( 'dps_shop_url', 'ลิงก์ช่องทางสั่งซื้อ', 'dps_ft3', 'url' );
		$add( 'dps_ft_all_label', 'ข้อความลิงก์ไปหน้าสินค้าทั้งหมด (เว้นว่าง = ซ่อน)', 'dps_ft3' );

		$sec( 'dps_ft4', 'ส่วนท้าย: คอลัมน์ 4 (ติดตามข่าวสาร)' );
		$add( 'dps_ft_show_4', 'แสดงคอลัมน์นี้', 'dps_ft4', 'checkbox' );
		$add( 'dps_ft_follow_title', 'หัวข้อคอลัมน์', 'dps_ft4' );
		$add( 'dps_newsletter_t', 'ข้อความเชิญชวน', 'dps_ft4', 'textarea' );
		$add( 'dps_newsletter_action', 'URL รับฟอร์มอีเมล (เว้นว่าง = ซ่อนฟอร์ม)', 'dps_ft4', 'url' );
		$add( 'dps_ft_news_ph', 'ข้อความในช่องกรอกอีเมล', 'dps_ft4' );
		$add( 'dps_ft_news_btn', 'ข้อความปุ่มสมัคร', 'dps_ft4' );
		foreach ( dps_ft_socials() as $k => $label ) {
			$add( $k, "ลิงก์ $label (เว้นว่าง = ซ่อน)", 'dps_ft4', 'url' );
		}

		$sec( 'dps_ft5', 'ส่วนท้าย: ลิขสิทธิ์ & เครดิต', 'ใช้ {year} แทนปีปัจจุบัน และ {site} แทนชื่อเว็บไซต์ได้' );
		$add( 'dps_ft_copy', 'ข้อความลิขสิทธิ์', 'dps_ft5' );
		$add( 'dps_ft_credit_show', 'แสดงข้อความเครดิตด้านขวา', 'dps_ft5', 'checkbox' );
		$add( 'dps_ft_credit', 'ข้อความเครดิต (ใส่ลิงก์ HTML ได้)', 'dps_ft5', 'textarea' );

		$sec( 'dps_ft6', 'ส่วนท้าย: สี', 'เว้นว่างเพื่อใช้สีเริ่มต้นของธีม' );
		$add( 'dps_ft_bg', 'สีพื้นหลัง', 'dps_ft6', 'color' );
		$add( 'dps_ft_text', 'สีตัวอักษร', 'dps_ft6', 'color' );

		// Click-to-edit partials (pencil icon in preview).
		if ( ! isset( $wp->selective_refresh ) ) {
			return;
		}
		$parts = array(
			'company'      => array( '.ft-company', array( 'dps_ft_company' ) ),
			'address'      => array( '.ft-address', array( 'dps_address' ) ),
			'phone'        => array( '.ft-phone', array( 'dps_ft_phone_label', 'dps_phone' ) ),
			'fax'          => array( '.ft-fax', array( 'dps_ft_fax_label', 'dps_fax' ) ),
			'email'        => array( '.ft-email', array( 'dps_ft_email_label', 'dps_email' ) ),
			'menu_title'   => array( '.ft-menu-title', array( 'dps_ft_menu_title' ) ),
			'shop_title'   => array( '.ft-shop-title', array( 'dps_ft_shop_title' ) ),
			'shop_link'    => array( '.ft-shop-link', array( 'dps_shop_label', 'dps_shop_url' ) ),
			'all_link'     => array( '.ft-all-link', array( 'dps_ft_all_label' ) ),
			'follow_title' => array( '.ft-follow-title', array( 'dps_ft_follow_title' ) ),
			'news_text'    => array( '.ft-news-text', array( 'dps_newsletter_t' ) ),
			'news_form'    => array( '.ft-news-form', array( 'dps_newsletter_action', 'dps_ft_news_ph', 'dps_ft_news_btn' ) ),
			'socials'      => array( '.ft-socials', array_keys( dps_ft_socials() ) ),
			'copy'         => array( '.ft-copy', array( 'dps_ft_copy' ) ),
			'credit'       => array( '.ft-credit', array( 'dps_ft_credit_show', 'dps_ft_credit' ) ),
		);
		foreach ( $parts as $id => $p ) {
			$wp->selective_refresh->add_partial(
				'dps_ft_' . $id,
				array(
					'selector'            => $p[0],
					'settings'            => $p[1],
					'container_inclusive' => false,
					'render_callback'     => function () use ( $id ) {
						return dps_ft_render( $id ); },
				)
			);
		}
	}
);
