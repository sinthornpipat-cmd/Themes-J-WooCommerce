<?php
/**
 * Footer: every text is editable in Customizer (click the pencil icon in the live preview),
 * plus 4 widget areas (one under each column).
 *
 * @package j-shop
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
function jshop_ft_defaults() {
	return array(
		'jshop_ft_show_1'         => true,
		'jshop_ft_show_2'         => true,
		'jshop_ft_show_3'         => true,
		'jshop_ft_show_4'         => true,
		'jshop_ft_company'        => '',
		'jshop_address'           => '',
		'jshop_ft_phone_label'    => __( 'Phone:', 'j-shop' ),
		'jshop_phone'             => '',
		'jshop_ft_fax_label'      => __( 'Fax:', 'j-shop' ),
		'jshop_fax'               => '',
		'jshop_ft_email_label'    => __( 'Email:', 'j-shop' ),
		'jshop_email'             => '',
		'jshop_ft_menu_title'     => __( 'Menu', 'j-shop' ),
		'jshop_ft_shop_title'     => __( 'Shop online', 'j-shop' ),
		'jshop_shop_label'        => '',
		'jshop_shop_url'          => '',
		'jshop_ft_all_label'      => __( 'View all products', 'j-shop' ),
		'jshop_ft_follow_title'   => __( 'Stay in touch', 'j-shop' ),
		'jshop_newsletter_t'      => __( 'Get news and offers from us. Enter your email.', 'j-shop' ),
		'jshop_newsletter_action' => '',
		'jshop_ft_news_btn'       => __( 'Subscribe', 'j-shop' ),
		'jshop_ft_news_ph'        => 'Email',
		'jshop_soc_facebook'      => '',
		'jshop_soc_x'             => '',
		'jshop_soc_instagram'     => '',
		'jshop_soc_tiktok'        => '',
		'jshop_soc_youtube'       => '',
		'jshop_ft_copy'           => '© {year} {site}.',
		'jshop_ft_credit_show'    => true,
		'jshop_ft_credit'         => sprintf( /* translators: %s: WordPress link. */ __( 'Powered by %s', 'j-shop' ), '<a href="https://wordpress.org" rel="noopener">WordPress</a>' ),
		'jshop_ft_bg'             => '',
		'jshop_ft_text'           => '',
	);
}
/**
 * Get a footer setting with its default.
 *
 * @param string $key Setting ID.
 * @return mixed
 */
function jshop_ft( $key ) {
	$d = jshop_ft_defaults();
	return get_theme_mod( $key, isset( $d[ $key ] ) ? $d[ $key ] : '' );
}
/**
 * Social network settings and labels.
 *
 * @return array<string,string> Setting ID => label.
 */
function jshop_ft_socials() {
	return array(
		'jshop_soc_facebook'  => 'Facebook',
		'jshop_soc_x'         => 'X (Twitter)',
		'jshop_soc_instagram' => 'Instagram',
		'jshop_soc_tiktok'    => 'TikTok',
		'jshop_soc_youtube'   => 'YouTube',
	);
}

/* ---------- Render one footer element (used by footer.php AND by Customizer partial refresh) ---------- */
/**
 * Render one footer element as a string (used by Customizer partial refresh).
 *
 * @param string $id Element ID, e.g. address, phone, socials.
 * @return string HTML.
 */
function jshop_ft_render( $id ) {
	ob_start();
	jshop_ft_output( $id );
	return ob_get_clean();
}

/**
 * Print one footer element. Every value is escaped as it is printed.
 *
 * @param string $id Element ID, e.g. address, phone, socials.
 * @return void
 */
function jshop_ft_output( $id ) {
	switch ( $id ) {
		case 'company':
			echo esc_html( jshop_ft( 'jshop_ft_company' ) ? jshop_ft( 'jshop_ft_company' ) : get_bloginfo( 'name' ) );
			break;
		case 'address':
			echo esc_html( jshop_ft( 'jshop_address' ) );
			break;
		case 'phone':
			$v = jshop_ft( 'jshop_phone' );
			if ( $v ) {
				$tel = preg_replace( '/[^0-9+]/', '', explode( ',', $v )[0] );
				echo esc_html( jshop_ft( 'jshop_ft_phone_label' ) ) . ' <a href="tel:' . esc_attr( $tel ) . '">' . esc_html( $v ) . '</a>';
			}
			break;
		case 'fax':
			$v = jshop_ft( 'jshop_fax' );
			if ( $v ) {
				echo esc_html( jshop_ft( 'jshop_ft_fax_label' ) . ' ' . $v );
			}
			break;
		case 'email':
			$v = jshop_ft( 'jshop_email' );
			if ( $v ) {
				echo esc_html( jshop_ft( 'jshop_ft_email_label' ) ) . ' <a href="mailto:' . esc_attr( $v ) . '">' . esc_html( $v ) . '</a>';
			}
			break;
		case 'menu_title':
			echo esc_html( jshop_ft( 'jshop_ft_menu_title' ) );
			break;
		case 'shop_title':
			echo esc_html( jshop_ft( 'jshop_ft_shop_title' ) );
			break;
		case 'shop_link':
			$l = jshop_ft( 'jshop_shop_label' );
			$u = jshop_ft( 'jshop_shop_url' );
			if ( $l ) {
				if ( $u ) {
					echo '<a href="' . esc_url( $u ) . '" target="_blank" rel="noopener">' . esc_html( $l ) . '</a>';
				} else {
					echo esc_html( $l );
				}
			}
			break;
		case 'all_link':
			$l = jshop_ft( 'jshop_ft_all_label' );
			if ( $l && function_exists( 'wc_get_page_permalink' ) ) {
				echo '<a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . esc_html( $l ) . '</a>';
			}
			break;
		case 'follow_title':
			echo esc_html( jshop_ft( 'jshop_ft_follow_title' ) );
			break;
		case 'news_text':
			echo esc_html( jshop_ft( 'jshop_newsletter_t' ) );
			break;
		case 'news_form':
			$a = jshop_ft( 'jshop_newsletter_action' );
			if ( $a ) {
				echo '<form class="news-form" method="post" action="' . esc_url( $a ) . '"><label class="screen-reader-text" for="nl-email">' . esc_html__( 'Email', 'j-shop' ) . '</label>'
					. '<input id="nl-email" type="email" name="email" placeholder="' . esc_attr( jshop_ft( 'jshop_ft_news_ph' ) ) . '" required>'
					. '<button class="btn" type="submit">' . esc_html( jshop_ft( 'jshop_ft_news_btn' ) ) . '</button></form>';
			}
			break;
		case 'socials':
			foreach ( jshop_ft_socials() as $k => $label ) {
				$u = jshop_ft( $k );
				if ( $u ) {
					echo '<a href="' . esc_url( $u ) . '" target="_blank" rel="noopener">' . esc_html( $label ) . '</a>';
				}
			}
			break;
		case 'copy':
			echo esc_html( str_replace( array( '{year}', '{site}' ), array( gmdate( 'Y' ), get_bloginfo( 'name' ) ), jshop_ft( 'jshop_ft_copy' ) ) );
			break;
		case 'credit':
			if ( jshop_ft( 'jshop_ft_credit_show' ) ) {
				echo wp_kses_post( jshop_ft( 'jshop_ft_credit' ) );
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
function jshop_ft_widgets( $n ) {
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
					'name'          => sprintf( /* translators: %d: footer column number. */ __( 'Footer column %d', 'j-shop' ), $i ),
					'id'            => "footer-$i",
					'description'   => __( 'Widgets added here appear below the content of this column.', 'j-shop' ),
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
		$bg  = get_theme_mod( 'jshop_ft_bg' );
		$tx  = get_theme_mod( 'jshop_ft_text' );
		$css = '';
		if ( $bg ) {
			$css .= '.site-footer{background:' . sanitize_hex_color( $bg ) . '}';
		}
		if ( $tx ) {
			$css .= '.site-footer,.site-footer h4{color:' . sanitize_hex_color( $tx ) . '}';
		}
		if ( $css ) {
			wp_add_inline_style( 'jshop-style', $css );
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
		$d    = jshop_ft_defaults();
		$hint = __( 'In the preview, click the pencil icon next to a text to jump to its field.', 'j-shop' );

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
					'transport'         => ( 'color' === $type || 0 === strpos( $id, 'jshop_ft_show_' ) ) ? 'refresh' : 'postMessage',
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
					'panel'       => 'jshop',
					'description' => trim( $desc . ' ' . $hint ),
				)
			);
		};

		$sec( 'jshop_ft1', __( 'Footer: column 1 (contact)', 'j-shop' ) );
		$add( 'jshop_ft_show_1', __( 'Show this column', 'j-shop' ), 'jshop_ft1', 'checkbox' );
		$add( 'jshop_ft_company', __( 'Heading / company name (empty = site title)', 'j-shop' ), 'jshop_ft1' );
		$add( 'jshop_address', __( 'Address', 'j-shop' ), 'jshop_ft1', 'textarea' );
		$add( 'jshop_ft_phone_label', __( 'Label: phone', 'j-shop' ), 'jshop_ft1' );
		$add( 'jshop_phone', __( 'Phone numbers (separate with commas; the first one becomes a call link)', 'j-shop' ), 'jshop_ft1' );
		$add( 'jshop_ft_fax_label', __( 'Label: fax', 'j-shop' ), 'jshop_ft1' );
		$add( 'jshop_fax', __( 'Fax (empty = hide)', 'j-shop' ), 'jshop_ft1' );
		$add( 'jshop_ft_email_label', __( 'Label: email', 'j-shop' ), 'jshop_ft1' );
		$add( 'jshop_email', __( 'Email (empty = hide)', 'j-shop' ), 'jshop_ft1' );

		$sec( 'jshop_ft2', __( 'Footer: column 2 (menu)', 'j-shop' ), __( 'Edit the menu items in Appearance → Menus (location “Footer menu”).', 'j-shop' ) );
		$add( 'jshop_ft_show_2', __( 'Show this column', 'j-shop' ), 'jshop_ft2', 'checkbox' );
		$add( 'jshop_ft_menu_title', __( 'Column heading', 'j-shop' ), 'jshop_ft2' );

		$sec( 'jshop_ft3', __( 'Footer: column 3 (shop online)', 'j-shop' ) );
		$add( 'jshop_ft_show_3', __( 'Show this column', 'j-shop' ), 'jshop_ft3', 'checkbox' );
		$add( 'jshop_ft_shop_title', __( 'Column heading', 'j-shop' ), 'jshop_ft3' );
		$add( 'jshop_shop_label', __( 'Online store link text', 'j-shop' ), 'jshop_ft3' );
		$add( 'jshop_shop_url', __( 'Online store link', 'j-shop' ), 'jshop_ft3', 'url' );
		$add( 'jshop_ft_all_label', __( '“All products” link text (empty = hide)', 'j-shop' ), 'jshop_ft3' );

		$sec( 'jshop_ft4', __( 'Footer: column 4 (stay in touch)', 'j-shop' ) );
		$add( 'jshop_ft_show_4', __( 'Show this column', 'j-shop' ), 'jshop_ft4', 'checkbox' );
		$add( 'jshop_ft_follow_title', __( 'Column heading', 'j-shop' ), 'jshop_ft4' );
		$add( 'jshop_newsletter_t', __( 'Invitation text', 'j-shop' ), 'jshop_ft4', 'textarea' );
		$add( 'jshop_newsletter_action', __( 'Newsletter form URL (empty = hide the form)', 'j-shop' ), 'jshop_ft4', 'url' );
		$add( 'jshop_ft_news_ph', __( 'Email field placeholder', 'j-shop' ), 'jshop_ft4' );
		$add( 'jshop_ft_news_btn', __( 'Subscribe button text', 'j-shop' ), 'jshop_ft4' );
		foreach ( jshop_ft_socials() as $k => $label ) {
			$add( $k, sprintf( /* translators: %s: social network name. */ __( '%s link (empty = hide)', 'j-shop' ), $label ), 'jshop_ft4', 'url' );
		}

		$sec( 'jshop_ft5', __( 'Footer: copyright & credit', 'j-shop' ), __( 'Use {year} for the current year and {site} for the site name.', 'j-shop' ) );
		$add( 'jshop_ft_copy', __( 'Copyright text', 'j-shop' ), 'jshop_ft5' );
		$add( 'jshop_ft_credit_show', __( 'Show the credit text on the right', 'j-shop' ), 'jshop_ft5', 'checkbox' );
		$add( 'jshop_ft_credit', __( 'Credit text (HTML links allowed)', 'j-shop' ), 'jshop_ft5', 'textarea' );

		$sec( 'jshop_ft6', __( 'Footer: colors', 'j-shop' ), __( 'Leave empty to use the theme colors.', 'j-shop' ) );
		$add( 'jshop_ft_bg', __( 'Background color', 'j-shop' ), 'jshop_ft6', 'color' );
		$add( 'jshop_ft_text', __( 'Text color', 'j-shop' ), 'jshop_ft6', 'color' );

		// Click-to-edit partials (pencil icon in preview).
		if ( ! isset( $wp->selective_refresh ) ) {
			return;
		}
		$parts = array(
			'company'      => array( '.ft-company', array( 'jshop_ft_company' ) ),
			'address'      => array( '.ft-address', array( 'jshop_address' ) ),
			'phone'        => array( '.ft-phone', array( 'jshop_ft_phone_label', 'jshop_phone' ) ),
			'fax'          => array( '.ft-fax', array( 'jshop_ft_fax_label', 'jshop_fax' ) ),
			'email'        => array( '.ft-email', array( 'jshop_ft_email_label', 'jshop_email' ) ),
			'menu_title'   => array( '.ft-menu-title', array( 'jshop_ft_menu_title' ) ),
			'shop_title'   => array( '.ft-shop-title', array( 'jshop_ft_shop_title' ) ),
			'shop_link'    => array( '.ft-shop-link', array( 'jshop_shop_label', 'jshop_shop_url' ) ),
			'all_link'     => array( '.ft-all-link', array( 'jshop_ft_all_label' ) ),
			'follow_title' => array( '.ft-follow-title', array( 'jshop_ft_follow_title' ) ),
			'news_text'    => array( '.ft-news-text', array( 'jshop_newsletter_t' ) ),
			'news_form'    => array( '.ft-news-form', array( 'jshop_newsletter_action', 'jshop_ft_news_ph', 'jshop_ft_news_btn' ) ),
			'socials'      => array( '.ft-socials', array_keys( jshop_ft_socials() ) ),
			'copy'         => array( '.ft-copy', array( 'jshop_ft_copy' ) ),
			'credit'       => array( '.ft-credit', array( 'jshop_ft_credit_show', 'jshop_ft_credit' ) ),
		);
		foreach ( $parts as $id => $p ) {
			$wp->selective_refresh->add_partial(
				'jshop_ft_' . $id,
				array(
					'selector'            => $p[0],
					'settings'            => $p[1],
					'container_inclusive' => false,
					'render_callback'     => function () use ( $id ) {
						return jshop_ft_render( $id ); },
				)
			);
		}
	}
);
