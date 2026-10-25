<?php
/**
 * Sample images + one-click demo content.
 * - Bundled images are used as Customizer defaults (they disappear once you set / clear your own).
 * - Appearance → ข้อมูลตัวอย่าง (Demo): import / remove sample products, feed items and reviews.
 *
 * @package themes-j-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------- Bundled image defaults ---------- */
/**
 * Get a URL to a bundled image.
 *
 * @param string $file File name inside assets/img.
 * @return string
 */
function dps_img_url( $file ) {
	return get_template_directory_uri() . '/assets/img/' . $file;
}
/**
 * Get the bundled sample image URL for an image setting key.
 *
 * @param string $key Customizer setting ID.
 * @return string URL or empty string.
 */
function dps_default_img( $key ) {
	static $map = null;
	if ( null === $map ) {
		$map = array(
			'dps_hero_1'       => 'hero-1.jpg',
			'dps_hero_2'       => 'hero-2.jpg',
			'dps_hero_3'       => 'hero-3.jpg',
			'dps_sec_2_banner' => 'banner-2.jpg',
			'dps_sec_3_banner' => 'banner-3.jpg',
			'dps_sec_4_banner' => 'banner-4.jpg',
			'dps_sec_5_banner' => 'banner-5.jpg',
			'dps_popup_img'    => 'popup.png',
		);
	}
	return isset( $map[ $key ] ) ? dps_img_url( $map[ $key ] ) : '';
}

// Product without a photo → themed placeholder.
/**
 * Use the themed placeholder for products without an image.
 */
add_filter(
	'woocommerce_placeholder_img_src',
	function () {
		return dps_img_url( 'placeholder.jpg' );
	}
);

/* ---------- Admin page ---------- */
/**
 * Add the Appearance > Demo data admin page.
 */
add_action(
	'admin_menu',
	function () {
		add_theme_page( 'ข้อมูลตัวอย่าง', 'ข้อมูลตัวอย่าง (Demo)', 'manage_options', 'dps-demo', 'dps_demo_page' );
	}
);
/**
 * Flag that the demo-data notice should be shown after activation.
 */
add_action(
	'after_switch_theme',
	function () {
		set_transient( 'dps_demo_notice', 1, DAY_IN_SECONDS );
	}
);
/**
 * Show an admin notice that offers to import demo data.
 */
add_action(
	'admin_notices',
	function () {
		if ( ! get_transient( 'dps_demo_notice' ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( get_option( 'dps_demo_imported' ) ) {
			delete_transient( 'dps_demo_notice' );
			return; }
		echo '<div class="notice notice-info is-dismissible"><p><strong>Themes J WooCommerce:</strong> ต้องการสินค้า/รีวิว/ไอเดียจัดบ้านตัวอย่างหรือไม่? ไปที่ <a href="' . esc_url( admin_url( 'themes.php?page=dps-demo' ) ) . '">Appearance → ข้อมูลตัวอย่าง (Demo)</a> (รูปแบนเนอร์ตัวอย่างแสดงให้อัตโนมัติแล้ว)</p></div>';
	}
);

/**
 * Render the Demo data admin page.
 *
 * @return void
 */
function dps_demo_page() {
	$done = get_option( 'dps_demo_imported' );
	$msg  = isset( $_GET['dps_msg'] ) ? sanitize_text_field( wp_unslash( $_GET['dps_msg'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only displays a status message after our own redirect.
	echo '<div class="wrap"><h1>ข้อมูลตัวอย่าง (Demo)</h1>';
	if ( $msg ) {
		echo '<div class="notice notice-success"><p>' . esc_html( $msg ) . '</p></div>';
	}
	echo '<p>สร้างข้อมูลตัวอย่างพร้อมรูปภาพ เพื่อให้หน้าแรกแสดงผลครบตามดีไซน์ — ทุกรายการที่สร้างจะถูกทำเครื่องหมายไว้ และ<strong>ลบทิ้งได้ในคลิกเดียว</strong> โดยไม่กระทบข้อมูลจริงของคุณ</p>';
	echo '<ul style="list-style:disc;margin-left:20px"><li>หมวดสินค้า 8 หมวด (storage-box, drawers-cabinet, laundry, kitchen, heavy-duty, bin-bucket, furniture, storage-shelf) × สินค้าหมวดละ 4 ชิ้น พร้อมรูปหมวดหมู่ ' . ( class_exists( 'WooCommerce' ) ? '' : '<em>(ต้องติดตั้ง WooCommerce ก่อน)</em>' ) . '</li><li>ไอเดียจัดบ้าน 6 รายการ</li><li>รีวิวลูกค้า 4 รายการ</li></ul>';
	foreach ( array(
		'import' => array( 'สร้างข้อมูลตัวอย่าง', 'primary', ! $done ),
		'remove' => array( 'ลบข้อมูลตัวอย่างทั้งหมด', 'secondary', (bool) $done ),
	) as $op => $o ) {
		if ( ! $o[2] ) {
			continue;
		}
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="display:inline-block;margin-right:10px">';
		wp_nonce_field( 'dps_demo', 'dps_demo_nonce' );
		echo '<input type="hidden" name="action" value="dps_demo"><input type="hidden" name="op" value="' . esc_attr( $op ) . '">';
		submit_button( $o[0], $o[1], 'submit', false );
		echo '</form>';
	}
	echo '</div>';
}

/**
 * Handle the import/remove demo data form submission.
 */
add_action(
	'admin_post_dps_demo',
	function () {
		if ( ! current_user_can( 'manage_options' ) || ! isset( $_POST['dps_demo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dps_demo_nonce'] ) ), 'dps_demo' ) ) {
			wp_die( 'Not allowed' );
		}
		$op  = isset( $_POST['op'] ) ? sanitize_key( wp_unslash( $_POST['op'] ) ) : '';
		$msg = 'import' === $op ? dps_demo_import() : dps_demo_remove();
		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => 'dps-demo',
					'dps_msg' => rawurlencode( $msg ),
				),
				admin_url( 'themes.php' )
			)
		);
		exit;
	}
);

/* ---------- Helpers ---------- */
/**
 * Copy a bundled image into the media library (cached per request).
 *
 * @param string $file File name inside assets/img.
 * @return int Attachment ID or 0.
 */
function dps_demo_sideload( $file ) {
	static $cache = array();
	if ( isset( $cache[ $file ] ) ) {
		return $cache[ $file ];
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$src = get_template_directory() . '/assets/img/' . $file;
	if ( ! file_exists( $src ) ) {
		return 0;
	}
	$tmp = wp_tempnam( $file );
	if ( ! $tmp || ! copy( $src, $tmp ) ) {
		return 0;
	}
	$id = media_handle_sideload(
		array(
			'name'     => $file,
			'tmp_name' => $tmp,
		),
		0
	);
	if ( is_wp_error( $id ) ) {
		wp_delete_file( $tmp );
		return 0;
	}
	update_post_meta( $id, '_dps_demo', 1 );
	$cache[ $file ] = (int) $id;
	return $cache[ $file ];
}

/**
 * Create demo categories, products, feed items and reviews.
 *
 * @return string Result message.
 */
function dps_demo_import() {
	if ( get_option( 'dps_demo_imported' ) ) {
		return 'นำเข้าไปแล้ว';
	}
	$n           = array(
		'products' => 0,
		'feed'     => 0,
		'reviews'  => 0,
	);
	$product_ids = array();

	if ( class_exists( 'WooCommerce' ) ) {
		$cats  = array(
			'storage-box'     => array( 'Storage Box', 1, array( 'กล่องเก็บของมีฝา 48 ลิตร', 'กล่องสไลด์ 30 ลิตร', 'กล่องเก็บของใส 20 ลิตร', 'กล่องเก็บของล้อเลื่อน 60 ลิตร' ) ),
			'drawers-cabinet' => array( 'Drawers & Cabinet', 2, array( 'ตู้ลิ้นชัก 4 ชั้น', 'ตู้ลิ้นชัก 3 ชั้น', 'ตู้ลิ้นชักตั้งโต๊ะ', 'ตู้ลิ้นชักล้อเลื่อน' ) ),
			'laundry'         => array( 'Laundry', 3, array( 'ตะกร้าผ้าทรงกลม', 'ตะกร้าผ้ามีฝาปิด', 'ตะกร้าซักผ้ามีหูหิ้ว', 'ตะกร้าผ้าทรงเหลี่ยม' ) ),
			'kitchen'         => array( 'Kitchen', 4, array( 'กล่องใส่อาหาร ชุด 3 ใบ', 'ที่คว่ำจาน 2 ชั้น', 'ตะกร้าอเนกประสงค์', 'กล่องเก็บอาหารฝาล็อก' ) ),
			'heavy-duty'      => array( 'Heavy Duty', 8, array( 'ลังพลาสติกงานหนัก', 'ลังซ้อนได้ ขนาดใหญ่', 'กล่องงานหนักมีฝา', 'ลังมีหูหิ้ว' ) ),
			'bin-bucket'      => array( 'Bin & Bucket', 5, array( 'ถังน้ำมีหูหิ้ว 10 ลิตร', 'ถังขยะฝาเปิด', 'ถังขยะเหยียบ 20 ลิตร', 'กะละมังกลม' ) ),
			'furniture'       => array( 'Furniture', 6, array( 'เก้าอี้บันได 2 ชั้น', 'เก้าอี้พลาสติก', 'สตูลเตี้ย', 'โต๊ะพับอเนกประสงค์' ) ),
			'storage-shelf'   => array( 'Storage shelf', 7, array( 'ชั้นวาง 4 ชั้น', 'ชั้นวาง 3 ชั้น', 'ชั้นวางรองเท้า', 'ชั้นวางอเนกประสงค์' ) ),
		);
		$price = array( 89, 129, 159, 199, 259, 349, 459, 599, 890 );
		$i     = 0;
		foreach ( $cats as $slug => $c ) {
			$term = term_exists( $slug, 'product_cat' );
			if ( ! $term ) {
				$term = wp_insert_term( $c[0], 'product_cat', array( 'slug' => $slug ) );
				if ( ! is_wp_error( $term ) ) {
					add_term_meta( $term['term_id'], '_dps_demo', 1, true );
				}
			}
			if ( is_wp_error( $term ) ) {
				continue;
			}
			$term_id = (int) ( is_array( $term ) ? $term['term_id'] : $term );
			$imgid   = dps_demo_sideload( 'product-' . $c[1] . '.jpg' );
			if ( $imgid ) {
				update_term_meta( $term_id, 'thumbnail_id', $imgid );
			}
			foreach ( $c[2] as $k => $name ) {
				$p = new WC_Product_Simple();
				$p->set_name( 'ตัวอย่าง: ' . $name );
				$p->set_status( 'publish' );
				$reg = $price[ ( $i++ ) % count( $price ) ];
				$p->set_regular_price( (string) $reg );
				if ( 1 === $k ) {
					$p->set_sale_price( (string) round( $reg * 0.8 ) );
				}
				$p->set_short_description( 'สินค้าตัวอย่างสำหรับทดสอบธีม — แก้ไขหรือลบได้ที่ Products' );
				$p->set_category_ids( array( $term_id ) );
				if ( $imgid ) {
					$p->set_image_id( $imgid );
				}
				$pid = $p->save();
				if ( $pid ) {
					update_post_meta( $pid, '_dps_demo', 1 );
					$product_ids[] = $pid;
					++$n['products']; }
			}
		}
	}

	for ( $i = 1; $i <= 6; $i++ ) {
		$id = wp_insert_post(
			array(
				'post_type'   => 'dps_feed',
				'post_status' => 'publish',
				'post_title'  => 'ไอเดียจัดบ้านตัวอย่าง ' . $i,
				'menu_order'  => $i,
			)
		);
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		update_post_meta( $id, '_dps_demo', 1 );
		update_post_meta( $id, '_dps_handle', 'ไอเดียจัดบ้าน #' . $i );
		if ( $product_ids ) {
			update_post_meta( $id, '_dps_product', $product_ids[ ( $i * 3 ) % count( $product_ids ) ] );
		}
		$t = dps_demo_sideload( 'feed-' . $i . '.jpg' );
		if ( $t ) {
			set_post_thumbnail( $id, $t );
		}
		++$n['feed'];
	}

	$reviews = array(
		array( 'ลูกค้าตัวอย่าง A.', 'รีวิวตัวอย่าง — กล่องแข็งแรง ฝาปิดสนิท ใส่ของได้เยอะ ซ้อนเก็บประหยัดพื้นที่ แก้ข้อความนี้ได้ที่เมนู รีวิวลูกค้า' ),
		array( 'ลูกค้าตัวอย่าง B.', 'รีวิวตัวอย่าง — ตู้ลิ้นชักประกอบง่าย ลิ้นชักเลื่อนลื่น สีสวย ใช้ในห้องนอนได้ดี' ),
		array( 'ลูกค้าตัวอย่าง C.', 'รีวิวตัวอย่าง — ตะกร้าผ้าขนาดกำลังดี น้ำหนักเบา มีรูระบายอากาศ ใช้ทุกวันไม่มีปัญหา' ),
		array( 'ลูกค้าตัวอย่าง D.', 'รีวิวตัวอย่าง — ซื้อไปใช้ที่ออฟฟิศหลายชิ้น ได้ของตรงปก จัดส่งไว แพ็กมาดี' ),
	);
	foreach ( $reviews as $i => $r ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'dps_review',
				'post_status'  => 'publish',
				'post_title'   => $r[0],
				'post_content' => $r[1],
				'menu_order'   => $i,
			)
		);
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		update_post_meta( $id, '_dps_demo', 1 );
		update_post_meta( $id, '_dps_stars', 5 );
		if ( $product_ids ) {
			update_post_meta( $id, '_dps_product', $product_ids[ $i * 4 % count( $product_ids ) ] );
		}
		$t = dps_demo_sideload( 'avatar-' . ( $i + 1 ) . '.jpg' );
		if ( $t ) {
			set_post_thumbnail( $id, $t );
		}
		++$n['reviews'];
	}

	update_option( 'dps_demo_imported', 1 );
	delete_transient( 'dps_demo_notice' );
	return sprintf( 'สร้างแล้ว: สินค้า %d, Feed %d, รีวิว %d', $n['products'], $n['feed'], $n['reviews'] );
}

/**
 * Delete everything created by the demo importer.
 *
 * @return string Result message.
 */
function dps_demo_remove() {
	$ids = get_posts(
		array(
			'post_type'      => array( 'product', 'dps_feed', 'dps_review', 'attachment' ),
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => '_dps_demo', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Runs once, from the admin Demo page.
			'meta_value'     => 1, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	$c   = 0;
	foreach ( $ids as $id ) {
		if ( wp_delete_post( $id, true ) ) {
			++$c;
		}
	}
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'meta_key'   => '_dps_demo', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Runs once, from the admin Demo page.
			'meta_value' => 1, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $t ) {
			wp_delete_term( $t->term_id, 'product_cat' );
		}
	}
	delete_option( 'dps_demo_imported' );
	return sprintf( 'ลบข้อมูลตัวอย่างแล้ว (%d รายการ)', $c );
}
