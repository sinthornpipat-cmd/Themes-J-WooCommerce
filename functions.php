<?php
/**
 * Theme functions and definitions.
 *
 * @package j-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'JSHOP_VER', wp_get_theme()->get( 'Version' ) );

/**
 * Register theme supports (title, thumbnails, logo, WooCommerce) and nav menus.
 */
add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'j-shop', get_template_directory() . '/languages' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/editor-style.css' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 80,
				'width'       => 260,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
		add_image_size( 'jshop-card', 600, 600, true );
		register_nav_menus(
			array(
				'primary' => __( 'Primary menu', 'j-shop' ),
			)
		);
	}
);

/**
 * Enqueue theme styles/scripts and print Customizer colours as CSS variables.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'jshop-fonts', 'https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600&family=Prompt:wght@500;600;700&display=swap', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google Fonts URL; a version query would break caching.
		wp_enqueue_style( 'jshop-style', get_stylesheet_uri(), array(), JSHOP_VER );
		wp_add_inline_style(
			'jshop-style',
			sprintf(
				':root{--brand:%s;--accent:%s;--ink:%s}',
				esc_html( get_theme_mod( 'jshop_brand', '#1456c8' ) ),
				esc_html( get_theme_mod( 'jshop_accent', '#d9480f' ) ),
				esc_html( get_theme_mod( 'jshop_ink', '#14213d' ) )
			)
		);
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
		wp_enqueue_script( 'jshop-main', get_template_directory_uri() . '/assets/js/main.js', array(), JSHOP_VER, true );
		wp_localize_script(
			'jshop-main',
			'jshopData',
			array(
				'popupMinutes' => (int) get_theme_mod( 'jshop_popup_minutes', 5 ),
				'popupEnabled' => (bool) get_theme_mod( 'jshop_popup_on', false ),
				'copied'       => __( 'Copied', 'j-shop' ),
				'copy'         => __( 'Copy code', 'j-shop' ),
				/* translators: %d: slide number. */
				'slide'        => __( 'Slide %d', 'j-shop' ),
			)
		);
	}
);

/* Helpers */
/**
 * Get a theme mod, falling back to a bundled sample image for image keys.
 *
 * @param string $k Theme mod key.
 * @param mixed  $d Default value.
 * @return mixed
 */
function jshop_mod( $k, $d = '' ) {
	if ( '' === $d && function_exists( 'jshop_default_img' ) ) {
		$d = jshop_default_img( $k );
	}
	return get_theme_mod( $k, $d );
}
/**
 * Whether WooCommerce is active.
 *
 * @return bool
 */
function jshop_has_wc() {
	return class_exists( 'WooCommerce' ); }
/**
 * Return inline SVG markup for a header icon.
 *
 * @param string $name Icon name: search|cart|menu|user.
 * @return string
 */
function jshop_icon( $name ) {
	$i = array(
		'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'cart'   => '<path d="M3 4h2l2.4 11h10.2L20 7H6.2"/><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/>',
		'menu'   => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'user'   => '<circle cx="12" cy="8" r="4"/><path d="M4 21c1-4 4-6 8-6s7 2 8 6"/>',
	);
	return '<svg viewBox="0 0 24 24" aria-hidden="true">' . $i[ $name ] . '</svg>';
}

/**
 * Print an inline SVG icon, allowing only the SVG markup the icons use.
 *
 * @param string $name Icon name: search|cart|menu|user.
 * @return void
 */
function jshop_the_icon( $name ) {
	echo wp_kses(
		jshop_icon( $name ),
		array(
			'svg'    => array(
				'viewbox'     => true,
				'aria-hidden' => true,
			),
			'path'   => array( 'd' => true ),
			'circle' => array(
				'cx' => true,
				'cy' => true,
				'r'  => true,
			),
		)
	);
}

/* Fallback menu */
/**
 * Fallback primary menu when no menu is assigned.
 */
function jshop_fallback_menu() {
	echo '<ul><li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'j-shop' ) . '</a></li>';
	if ( jshop_has_wc() ) {
		echo '<li><a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . esc_html__( 'All products', 'j-shop' ) . '</a></li>';
	}
	echo '</ul>';
}

/* Cart count fragment */
/**
 * Refresh the header cart counter via WooCommerce cart fragments.
 */
add_filter(
	'woocommerce_add_to_cart_fragments',
	function ( $f ) {
		$f['span.cart-count'] = '<span class="cart-count">' . WC()->cart->get_cart_contents_count() . '</span>';
		return $f;
	}
);

/* Woo layout wrappers */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

/*
 * The <main> wrapper is printed by woocommerce.php. WooCommerce does not fire
 * woocommerce_before_main_content on single product pages when it renders
 * through woocommerce_content(), so a hook-based wrapper left them unpadded.
 */
/**
 * Set the number of product columns in shop loops.
 */
add_filter(
	'loop_shop_columns',
	function () {
		return 4;
	}
);
/**
 * Set related products to 4 columns / 4 items.
 */
add_filter(
	'woocommerce_output_related_products_args',
	function ( $a ) {
		$a['columns']        = 4;
		$a['posts_per_page'] = 4;
		return $a;
	}
);

/* Promo badge on archive/single loops (text set in Customizer) */
/**
 * Print the promo badge on product cards in loops.
 */
add_action(
	'woocommerce_before_shop_loop_item_title',
	function () {
		$t = jshop_mod( 'jshop_badge', '' );
		if ( $t ) {
			echo '<span class="p-badge">' . esc_html( $t ) . '</span>';
		}
	},
	5
);

/* Widgets (optional sidebar for blog) */
/**
 * Register the blog sidebar.
 */
add_action(
	'widgets_init',
	function () {
		register_sidebar(
			array(
				'name'          => 'Sidebar',
				'id'            => 'sidebar-1',
				'before_widget' => '<section class="widget">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3>',
				'after_title'   => '</h3>',
			)
		);
	}
);

require get_template_directory() . '/inc/images.php';
require get_template_directory() . '/inc/footer.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/woocommerce.php';
require get_template_directory() . '/inc/checkout.php';
require get_template_directory() . '/inc/performance.php';


/**
 * Flag the document as JS-enabled as early as possible (used to hide the
 * "Update cart" button, which is triggered automatically by woo.js).
 *
 * @return void
 */
function jshop_js_flag() {
	echo "<script>document.documentElement.className+=' dps-js';</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'jshop_js_flag', 1 );
