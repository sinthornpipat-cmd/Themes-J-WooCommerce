<?php
/**
 * WooCommerce integration.
 *
 * @package j-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/* ---- Compatibility declarations (HPOS + Cart/Checkout blocks) ---- */
/**
 * Declare HPOS and Cart/Checkout Blocks compatibility.
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', get_template_directory() . '/style.css', true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', get_template_directory() . '/style.css', true );
		}
	}
);

/* ---- Assets ---- */
/**
 * Enqueue WooCommerce styles/scripts and pass cart settings to JS.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'jshop-woo', get_template_directory_uri() . '/assets/css/woocommerce.css', array( 'jshop-style' ), JSHOP_VER );
		wp_enqueue_script( 'jshop-woo', get_template_directory_uri() . '/assets/js/woo.js', array( 'jquery' ), JSHOP_VER, true );
		wp_localize_script(
			'jshop-woo',
			'jshopWoo',
			array(
				'openOnAdd' => (bool) get_theme_mod( 'jshop_open_mini_cart', true ),
				'isCart'    => is_cart() || is_checkout(),
				'ajax'      => admin_url( 'admin-ajax.php' ),
				'wcAjax'    => class_exists( 'WC_AJAX' ) ? WC_AJAX::get_endpoint( 'jshop_add_to_cart' ) : '',
				'wcBase'    => class_exists( 'WC_AJAX' ) ? WC_AJAX::get_endpoint( '%%endpoint%%' ) : '',
				'nonce'     => wp_create_nonce( 'jshop_cart' ),
				'cur'       => array(
					'sym'  => html_entity_decode( get_woocommerce_currency_symbol(), ENT_QUOTES, 'UTF-8' ),
					'dec'  => wc_get_price_decimals(),
					'dsep' => wc_get_price_decimal_separator(),
					'tsep' => wc_get_price_thousand_separator(),
					'fmt'  => get_woocommerce_price_format(),
				),
				'i18n'      => array(
					'max'    => 'สินค้าคงเหลือไม่พอ ปรับเป็นจำนวนสูงสุดที่สั่งได้แล้ว',
					'err'    => 'เกิดข้อผิดพลาด กรุณาลองใหม่',
					'coupon' => 'ใช้คูปองสำเร็จ',
				),
			)
		);
	},
	20
);

/* ---- Shop behaviour ---- */
/**
 * Set products per page on shop archives.
 */
add_filter(
	'loop_shop_per_page',
	function () {
		return (int) get_theme_mod( 'jshop_per_page', 12 );
	}
);
/**
 * Set cross-sell columns.
 */
add_filter(
	'woocommerce_cross_sells_columns',
	function () {
		return 4;
	}
);
/**
 * Set up-sell columns and count.
 */
add_filter(
	'woocommerce_upsell_display_args',
	function ( $a ) {
		$a['columns']        = 4;
		$a['posts_per_page'] = 4;
		return $a;
	}
);
/**
 * Customise breadcrumb markup and delimiter.
 */
add_filter(
	'woocommerce_breadcrumb_defaults',
	function ( $d ) {
		$d['delimiter']   = ' / ';
		$d['wrap_before'] = '<nav class="woocommerce-breadcrumb" aria-label="breadcrumb">';
		return $d;
	}
);
/**
 * Use simple arrows for pagination.
 */
add_filter(
	'woocommerce_pagination_args',
	function ( $a ) {
		$a['prev_text'] = '‹';
		$a['next_text'] = '›';
		return $a;
	}
);

/* Sidebar: remove default get_sidebar() and use our own layout */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
/**
 * Register the shop filter sidebar.
 */
add_action(
	'widgets_init',
	function () {
		register_sidebar(
			array(
				'name'          => 'ตัวกรองสินค้า (Shop)',
				'id'            => 'shop-sidebar',
				'description'   => 'แสดงในหน้าร้านค้า/หมวดหมู่สินค้า เช่น Filter by price, Product categories',
				'before_widget' => '<section class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}
);
/**
 * Whether the shop sidebar should be displayed on this request.
 *
 * @return bool
 */
function jshop_shop_has_sidebar() {
	return ( is_shop() || is_product_taxonomy() ) && is_active_sidebar( 'shop-sidebar' );
}
/**
 * Open the shop layout (sidebar + main) before archive content.
 */
add_action(
	'woocommerce_before_main_content',
	function () {
		if ( ! jshop_shop_has_sidebar() ) {
			return;
		}
		echo '<div class="shop-layout"><aside class="shop-side" aria-label="ตัวกรองสินค้า">';
		dynamic_sidebar( 'shop-sidebar' );
		echo '</aside><div class="shop-main">';
	},
	25
);
/**
 * Close the shop layout after archive content.
 */
add_action(
	'woocommerce_after_main_content',
	function () {
		if ( jshop_shop_has_sidebar() ) {
			echo '</div></div>';
		}
	},
	5
);

/* Category banner / title spacing handled in CSS. Hide the default sale flash text swap -> keep % off */
/**
 * Show a percentage on the sale badge.
 */
add_filter(
	'woocommerce_sale_flash',
	function ( $html, $post, $product ) {
		if ( $product->is_type( 'variable' ) || ! $product->get_regular_price() || ! (float) $product->get_regular_price() ) {
			return '<span class="onsale">' . esc_html__( 'ลดราคา', 'j-shop' ) . '</span>';
		}
		$pct = round( ( 1 - (float) $product->get_price() / (float) $product->get_regular_price() ) * 100 );
		return '<span class="onsale">-' . (int) $pct . '%</span>';
	},
	10,
	3
);

/* ---- Quantity +/- buttons ---- */
/**
 * Print the minus button before the quantity input.
 */
add_action(
	'woocommerce_before_quantity_input_field',
	function () {
		echo '<button type="button" class="qty-btn" data-d="-1" aria-label="ลดจำนวน">−</button>';
	}
);
/**
 * Print the plus button after the quantity input.
 */
add_action(
	'woocommerce_after_quantity_input_field',
	function () {
		echo '<button type="button" class="qty-btn" data-d="1" aria-label="เพิ่มจำนวน">+</button>';
	}
);

/* ---- Free shipping progress ---- */
/**
 * Free-shipping threshold from the Customizer.
 *
 * @return float 0 disables the bar.
 */
function jshop_free_ship_threshold() {
	return (float) get_theme_mod( 'jshop_free_ship', 0 );
}
/**
 * Build the free-shipping progress bar markup.
 *
 * @return string HTML.
 */
function jshop_ship_bar_html() {
	$t = jshop_free_ship_threshold();
	if ( $t <= 0 || ! WC()->cart ) {
		return '<div class="dps-ship-bar"></div>';
	}
	$sub  = (float) WC()->cart->get_displayed_subtotal();
	$left = max( 0, $t - $sub );
	$pct  = min( 100, $t ? ( $sub / $t ) * 100 : 100 );
	$msg  = $sub <= 0 ? sprintf( 'ซื้อครบ %s ส่งฟรี', wc_price( $t ) )
		: ( $left > 0 ? sprintf( 'ซื้อเพิ่มอีก %s เพื่อรับส่งฟรี', wc_price( $left ) ) : '🎉 คุณได้รับสิทธิ์ส่งฟรีแล้ว' );
	return '<div class="dps-ship-bar"><p>' . wp_kses_post( $msg ) . '</p><div class="bar"><span style="width:' . esc_attr( round( $pct ) ) . '%"></span></div></div>';
}
/**
 * Refresh the free-shipping bar via cart fragments.
 */
add_filter(
	'woocommerce_add_to_cart_fragments',
	function ( $f ) {
		$f['div.dps-ship-bar'] = jshop_ship_bar_html();
		return $f;
	}
);
// Also show the bar on the cart page.
/**
 * Show the free-shipping bar above the cart table.
 */
add_action(
	'woocommerce_before_cart_table',
	function () {
		echo wp_kses_post( jshop_ship_bar_html() );
	}
);

/* ---- Sticky add-to-cart bar on single product ---- */
/**
 * Print the sticky add-to-cart bar on single products.
 */
add_action(
	'wp_footer',
	function () {
		if ( ! is_product() ) {
			return;
		}
		global $product;
		if ( ! $product instanceof WC_Product || ! $product->is_purchasable() ) {
			return;
		}
		?>
	<div class="sticky-atc" aria-hidden="true">
		<div class="wrap">
			<div class="sa-info"><strong><?php echo esc_html( $product->get_name() ); ?></strong><span><?php echo wp_kses_post( $product->get_price_html() ); ?></span></div>
			<button type="button" class="btn sa-btn"><?php echo esc_html( $product->is_type( 'simple' ) ? __( 'หยิบใส่ตะกร้า', 'j-shop' ) : __( 'เลือกตัวเลือก', 'j-shop' ) ); ?></button>
		</div>
	</div>
		<?php
	}
);

/* ---- Mini cart drawer (printed in footer.php) ---- */
/**
 * Print the mini-cart drawer in the footer.
 */
add_action(
	'wp_footer',
	function () {
		if ( is_cart() || is_checkout() ) {
			return;
		}
		get_template_part( 'template-parts/mini-cart' );
	},
	5
);

/* ---- Customizer: WooCommerce options ---- */
/**
 * Register WooCommerce-related Customizer controls.
 */
add_action(
	'customize_register',
	function ( $wp ) {
		$wp->add_section(
			'jshop_woo',
			array(
				'title' => 'WooCommerce (ร้านค้า)',
				'panel' => 'jshop',
			)
		);
		$wp->add_setting(
			'jshop_free_ship',
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
			)
		);
		$wp->add_control(
			'jshop_free_ship',
			array(
				'label'   => 'ยอดขั้นต่ำส่งฟรี (บาท) สำหรับแถบความคืบหน้า — 0 = ซ่อน',
				'section' => 'jshop_woo',
				'type'    => 'number',
			)
		);
		$wp->add_setting(
			'jshop_per_page',
			array(
				'default'           => 12,
				'sanitize_callback' => 'absint',
			)
		);
		$wp->add_control(
			'jshop_per_page',
			array(
				'label'   => 'จำนวนสินค้าต่อหน้า (หน้าร้านค้า)',
				'section' => 'jshop_woo',
				'type'    => 'number',
			)
		);
		$wp->add_setting(
			'jshop_open_mini_cart',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
			)
		);
		$wp->add_control(
			'jshop_open_mini_cart',
			array(
				'label'   => 'เปิดมินิคาร์ททันทีเมื่อเพิ่มสินค้า',
				'section' => 'jshop_woo',
				'type'    => 'checkbox',
			)
		);
	}
);


/* ---- Mini-cart: item count in drawer title (fragment) ---- */
/**
 * Refresh the mini-cart item count via cart fragments.
 */
add_filter(
	'woocommerce_add_to_cart_fragments',
	function ( $f ) {
		$f['span.mc-count'] = '<span class="mc-count">(' . (int) WC()->cart->get_cart_contents_count() . ')</span>';
		return $f;
	}
);

/* ---- Mini-cart AJAX: change quantity / remove / coupons ---- */
/**
 * Verify the AJAX nonce and make sure the cart is loaded.
 *
 * @return void
 */
function jshop_ajax_guard() {
	if ( ! check_ajax_referer( 'jshop_cart', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => 'หมดเวลา กรุณารีเฟรชหน้าแล้วลองใหม่' ) );
	}
	if ( ! WC()->cart ) {
		wc_load_cart();
	}
}
/**
 * Recalculate totals and return mini-cart fragments as a JSON success response.
 *
 * @param array $extra Extra data to merge into the response.
 * @return void
 */
function jshop_ajax_cart_response( $extra = array() ) {
	WC()->cart->calculate_totals();
	ob_start();
	woocommerce_mini_cart();
	$mini      = ob_get_clean();
	$fragments = apply_filters(
		'woocommerce_add_to_cart_fragments', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook.
		array(
			'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini . '</div>',
		)
	);
	wp_send_json_success(
		array_merge(
			array(
				'fragments' => $fragments,
				'cart_hash' => WC()->cart->get_cart_hash(),
			),
			$extra
		)
	);
}

add_action( 'wp_ajax_jshop_cart_qty', 'jshop_ajax_cart_qty' );
add_action( 'wp_ajax_nopriv_jshop_cart_qty', 'jshop_ajax_cart_qty' );
add_action( 'wc_ajax_jshop_cart_qty', 'jshop_ajax_cart_qty' ); // Lighter than admin-ajax.php.
/**
 * AJAX: set or remove a cart item quantity.
 *
 * @return void
 */
function jshop_ajax_cart_qty() {
	jshop_ajax_guard();
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce checked in jshop_ajax_guard().
	$key = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$qty = isset( $_POST['qty'] ) ? wc_stock_amount( sanitize_text_field( wp_unslash( $_POST['qty'] ) ) ) : 0;
	// phpcs:enable WordPress.Security.NonceVerification.Missing
	$item = WC()->cart->get_cart_item( $key );
	if ( ! $item ) {
		jshop_ajax_cart_response();
	}
	$extra = array();
	if ( $qty <= 0 ) {
		WC()->cart->remove_cart_item( $key );
	} else {
		$max = (int) $item['data']->get_max_purchase_quantity();
		if ( $max > 0 && $qty > $max ) {
			$qty             = $max;
			$extra['capped'] = true;
		}
		WC()->cart->set_quantity( $key, $qty, true );
	}
	jshop_ajax_cart_response( $extra );
}

add_action( 'wp_ajax_jshop_cart_coupon', 'jshop_ajax_cart_coupon' );
add_action( 'wp_ajax_nopriv_jshop_cart_coupon', 'jshop_ajax_cart_coupon' );
add_action( 'wc_ajax_jshop_cart_coupon', 'jshop_ajax_cart_coupon' ); // Lighter than admin-ajax.php.
/**
 * AJAX: apply or remove a coupon in the mini-cart.
 *
 * @return void
 */
function jshop_ajax_cart_coupon() {
	jshop_ajax_guard();
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce checked in jshop_ajax_guard().
	$code = isset( $_POST['code'] ) ? wc_format_coupon_code( sanitize_text_field( wp_unslash( $_POST['code'] ) ) ) : '';
	$op   = isset( $_POST['op'] ) ? sanitize_key( wp_unslash( $_POST['op'] ) ) : 'apply';
	// phpcs:enable WordPress.Security.NonceVerification.Missing
	if ( '' === $code ) {
		wp_send_json_error( array( 'message' => 'กรุณากรอกรหัสคูปอง' ) );
	}
	if ( 'remove' === $op ) {
		WC()->cart->remove_coupon( $code );
		wc_clear_notices();
		jshop_ajax_cart_response();
	}
	wc_clear_notices();
	if ( WC()->cart->apply_coupon( $code ) ) {
		wc_clear_notices();
		jshop_ajax_cart_response( array( 'applied' => true ) );
	}
	$errors = array_map(
		function ( $n ) {
			return wp_strip_all_tags( is_array( $n ) ? $n['notice'] : $n );
		},
		wc_get_notices( 'error' )
	);
	wc_clear_notices();
	wp_send_json_error( array( 'message' => $errors ? implode( ' ', $errors ) : 'ไม่สามารถใช้คูปองนี้ได้' ) );
}


/* ---- Shop toolbar wrapper (result count + ordering) ---- */
/**
 * Open the shop toolbar wrapper (result count + ordering).
 */
add_action(
	'woocommerce_before_shop_loop',
	function () {
		echo '<div class="shop-toolbar">';
	},
	19
);
/**
 * Close the shop toolbar wrapper.
 */
add_action(
	'woocommerce_before_shop_loop',
	function () {
		echo '</div>';
	},
	31
);


/**
 * Add a product to the cart and return refreshed mini-cart fragments in ONE request.
 *
 * Runs on the lightweight ?wc-ajax= endpoint. Handles simple and variable products
 * (variation ID + attribute_* fields), mirroring WooCommerce's own add-to-cart handler.
 * WooCommerce core's own add_to_cart endpoint does not verify a nonce either.
 *
 * @return void
 */
function jshop_wc_ajax_add_to_cart() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	if ( ! WC()->cart ) {
		wc_load_cart();
	}
	$raw_id     = isset( $_POST['add-to-cart'] ) ? absint( wp_unslash( $_POST['add-to-cart'] ) ) : ( isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0 );
	$product_id = apply_filters( 'woocommerce_add_to_cart_product_id', $raw_id ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce core hook.
	$quantity   = isset( $_POST['quantity'] ) ? wc_stock_amount( sanitize_text_field( wp_unslash( $_POST['quantity'] ) ) ) : 1;
	$quantity   = $quantity > 0 ? $quantity : 1;
	$variation  = array();
	foreach ( $_POST as $key => $value ) {
		if ( 0 === strpos( $key, 'attribute_' ) ) {
			$variation[ sanitize_title( wp_unslash( $key ) ) ] = wc_clean( wp_unslash( $value ) );
		}
	}
	$variation_id = isset( $_POST['variation_id'] ) ? absint( wp_unslash( $_POST['variation_id'] ) ) : 0;
	// phpcs:enable WordPress.Security.NonceVerification.Missing

	$product = $product_id ? wc_get_product( $product_id ) : false;
	if ( ! $product ) {
		wp_send_json_error( array( 'message' => 'ไม่พบสินค้า' ) );
	}

	wc_clear_notices();
	$added = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation );
	if ( ! $added ) {
		$errors = array_map(
			function ( $n ) {
				return wp_strip_all_tags( is_array( $n ) ? $n['notice'] : $n );
			},
			wc_get_notices( 'error' )
		);
		wc_clear_notices();
		wp_send_json_error(
			array(
				'message'     => $errors ? implode( ' ', $errors ) : 'ไม่สามารถเพิ่มสินค้าได้',
				'product_url' => get_permalink( $product_id ),
			)
		);
	}
	wc_clear_notices();
	jshop_ajax_cart_response( array( 'added' => true ) );
}
add_action( 'wc_ajax_jshop_add_to_cart', 'jshop_wc_ajax_add_to_cart' );
