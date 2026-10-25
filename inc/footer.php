<?php
/**
 * Footer: every text is editable in Customizer (click the pencil icon in the live preview),
 * plus 2 widget areas (one under each block).
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
		'jshop_ft_contact_title' => __( 'Get in Touch', 'j-shop' ),
		'jshop_ft_contact_text'  => '',
		'jshop_ft_contact_btn'   => '',
		'jshop_ft_contact_url'   => '',
		'jshop_ft_address_title' => __( 'Address', 'j-shop' ),
		'jshop_address'          => '',
		'jshop_ft_legal_label'   => __( 'Terms of Use & Privacy Policy', 'j-shop' ),
		'jshop_ft_legal_url'     => '',
		'jshop_ft_copy'          => '© {year} {site}.',
		'jshop_ft_credit_show'   => true,
		'jshop_ft_credit'        => sprintf( /* translators: %s: WordPress link. */ __( 'Powered by %s', 'j-shop' ), '<a href="https://wordpress.org" rel="noopener">WordPress</a>' ),
		'jshop_ft_bg'            => '',
		'jshop_ft_text'          => '',
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
/* ---------- Render one footer element (used by footer.php AND by Customizer partial refresh) ---------- */
/**
 * Render one footer element as a string (used by Customizer partial refresh).
 *
 * @param string $id Element ID, e.g. address, contact_btn, legal.
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
 * @param string $id Element ID, e.g. address, contact_btn, legal.
 * @return void
 */
function jshop_ft_output( $id ) {
	switch ( $id ) {
		case 'contact_title':
			echo esc_html( jshop_ft( 'jshop_ft_contact_title' ) );
			break;
		case 'contact_text':
			echo wp_kses_post( nl2br( jshop_ft( 'jshop_ft_contact_text' ) ) );
			break;
		case 'contact_btn':
			$l = jshop_ft( 'jshop_ft_contact_btn' );
			$u = jshop_ft( 'jshop_ft_contact_url' );
			if ( $l && $u ) {
				echo '<a class="btn ft-btn" href="' . esc_url( $u ) . '" target="_blank" rel="noopener">' . esc_html( $l ) . '</a>';
			}
			break;
		case 'address_title':
			echo esc_html( jshop_ft( 'jshop_ft_address_title' ) );
			break;
		case 'address':
			echo wp_kses_post( nl2br( jshop_ft( 'jshop_address' ) ) );
			break;
		case 'legal':
			$l = jshop_ft( 'jshop_ft_legal_label' );
			$u = jshop_ft( 'jshop_ft_legal_url' );
			$u = $u ? $u : get_privacy_policy_url();
			if ( $l && $u ) {
				echo '<a href="' . esc_url( $u ) . '">' . esc_html( $l ) . '</a>';
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
 * Output a footer widget area when it has widgets.
 *
 * @param int $n Widget area number 1-2.
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
 * Register two footer widget areas, one for each footer block.
 */
add_action(
	'widgets_init',
	function () {
		$areas = array(
			1 => array( __( 'Footer: get in touch', 'j-shop' ), __( 'Widgets added here appear below the contact text.', 'j-shop' ) ),
			2 => array( __( 'Footer: address', 'j-shop' ), __( 'Widgets added here appear below the address.', 'j-shop' ) ),
		);
		foreach ( $areas as $i => $area ) {
			register_sidebar(
				array(
					'name'          => $area[0],
					'id'            => "footer-$i",
					'description'   => $area[1],
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
			$css .= '.site-footer,.site-footer h3,.site-footer h4{color:' . sanitize_hex_color( $tx ) . '}';
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
					'transport'         => 'color' === $type ? 'refresh' : 'postMessage',
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

		$sec( 'jshop_ft1', __( 'Footer: get in touch', 'j-shop' ) );
		$add( 'jshop_ft_contact_title', __( 'Heading', 'j-shop' ), 'jshop_ft1' );
		$add( 'jshop_ft_contact_text', __( 'Contact text', 'j-shop' ), 'jshop_ft1', 'textarea' );
		$add( 'jshop_ft_contact_btn', __( 'Button text (empty = hide)', 'j-shop' ), 'jshop_ft1' );
		$add( 'jshop_ft_contact_url', __( 'Button link', 'j-shop' ), 'jshop_ft1', 'url' );

		$sec( 'jshop_ft2', __( 'Footer: address', 'j-shop' ) );
		$add( 'jshop_ft_address_title', __( 'Heading', 'j-shop' ), 'jshop_ft2' );
		$add( 'jshop_address', __( 'Address', 'j-shop' ), 'jshop_ft2', 'textarea' );

		$sec( 'jshop_ft5', __( 'Footer: copyright, credit & terms link', 'j-shop' ), __( 'Use {year} for the current year and {site} for the site name.', 'j-shop' ) );
		$add( 'jshop_ft_copy', __( 'Copyright text', 'j-shop' ), 'jshop_ft5' );
		$add( 'jshop_ft_credit_show', __( 'Show the credit text on the right', 'j-shop' ), 'jshop_ft5', 'checkbox' );
		$add( 'jshop_ft_credit', __( 'Credit text (HTML links allowed)', 'j-shop' ), 'jshop_ft5', 'textarea' );
		$add( 'jshop_ft_legal_label', __( 'Terms link text (empty = hide)', 'j-shop' ), 'jshop_ft5' );
		$add( 'jshop_ft_legal_url', __( 'Terms link (empty = privacy policy page)', 'j-shop' ), 'jshop_ft5', 'url' );

		$sec( 'jshop_ft6', __( 'Footer: colors', 'j-shop' ), __( 'Leave empty to use the theme colors.', 'j-shop' ) );
		$add( 'jshop_ft_bg', __( 'Background color', 'j-shop' ), 'jshop_ft6', 'color' );
		$add( 'jshop_ft_text', __( 'Text color', 'j-shop' ), 'jshop_ft6', 'color' );

		// Click-to-edit partials (pencil icon in preview).
		if ( ! isset( $wp->selective_refresh ) ) {
			return;
		}
		$parts = array(
			'contact_title' => array( '.ft-contact-title', array( 'jshop_ft_contact_title' ) ),
			'contact_text'  => array( '.ft-contact-text', array( 'jshop_ft_contact_text' ) ),
			'contact_btn'   => array( '.ft-contact-btn', array( 'jshop_ft_contact_btn', 'jshop_ft_contact_url' ) ),
			'address_title' => array( '.ft-address-title', array( 'jshop_ft_address_title' ) ),
			'address'       => array( '.ft-address', array( 'jshop_address' ) ),
			'legal'         => array( '.ft-legal', array( 'jshop_ft_legal_label', 'jshop_ft_legal_url' ) ),
			'copy'          => array( '.ft-copy', array( 'jshop_ft_copy' ) ),
			'credit'        => array( '.ft-credit', array( 'jshop_ft_credit_show', 'jshop_ft_credit' ) ),
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
