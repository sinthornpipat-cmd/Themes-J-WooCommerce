<?php
/**
 * Custom post types (reviews, FAQ, home ideas) and their meta boxes.
 *
 * @package petchsiam-shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Review, FAQ and Home Ideas post types.
 */
function dps_register_cpts() {
	$common = [ 'public' => false, 'show_ui' => true, 'show_in_rest' => true, 'menu_position' => 25, 'has_archive' => false ];
	register_post_type( 'dps_review', $common + [
		'labels' => [ 'name' => 'รีวิวลูกค้า', 'singular_name' => 'รีวิว', 'add_new_item' => 'เพิ่มรีวิว', 'menu_name' => 'รีวิวลูกค้า' ],
		'menu_icon' => 'dashicons-star-filled', 'supports' => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
	] );
	register_post_type( 'dps_faq', $common + [
		'labels' => [ 'name' => 'คำถามที่พบบ่อย', 'singular_name' => 'FAQ', 'add_new_item' => 'เพิ่มคำถาม', 'menu_name' => 'FAQ' ],
		'menu_icon' => 'dashicons-editor-help', 'supports' => [ 'title', 'editor', 'page-attributes' ],
	] );
	register_post_type( 'dps_feed', $common + [
		'labels' => [ 'name' => 'ไอเดียจัดบ้าน', 'singular_name' => 'Feed', 'add_new_item' => 'เพิ่มไอเดียจัดบ้าน', 'menu_name' => 'ไอเดียจัดบ้าน' ],
		'menu_icon' => 'dashicons-instagram', 'supports' => [ 'title', 'thumbnail', 'page-attributes' ],
	] );
}
add_action( 'init', 'dps_register_cpts' );

/**
 * Add the extra-details meta box to Review and Home Ideas editors.
 */
add_action( 'add_meta_boxes', function () {
	add_meta_box( 'dps_meta', 'รายละเอียดเพิ่มเติม', 'dps_meta_box', [ 'dps_review', 'dps_feed' ], 'side' );
} );
/**
 * Render the extra-details meta box.
 *
 * @param WP_Post $post Current post.
 * @return void
 */
function dps_meta_box( $post ) {
	wp_nonce_field( 'dps_meta', 'dps_nonce' );
	$pid = get_post_meta( $post->ID, '_dps_product', true );
	echo '<p><label>รหัสสินค้า (Product ID)<br><input type="number" name="dps_product" value="' . esc_attr( $pid ) . '" style="width:100%"></label></p>';
	if ( 'dps_feed' === $post->post_type ) {
		$h = get_post_meta( $post->ID, '_dps_handle', true );
		echo '<p><label>คำบรรยายสั้น (แสดงบนการ์ด)<br><input type="text" name="dps_handle" value="' . esc_attr( $h ) . '" style="width:100%"></label></p>';
	} else {
		$s = get_post_meta( $post->ID, '_dps_stars', true );
		$s = $s ? $s : 5;
		echo '<p><label>ดาว (1-5)<br><input type="number" min="1" max="5" name="dps_stars" value="' . esc_attr( $s ) . '" style="width:100%"></label></p>';
	}
}
/**
 * Save meta box fields (product ID, caption, star rating).
 */
add_action( 'save_post', function ( $id ) {
	if ( ! isset( $_POST['dps_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dps_nonce'] ) ), 'dps_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $id ) ) {
		return;
	}
	foreach ( [ 'dps_product' => '_dps_product', 'dps_handle' => '_dps_handle', 'dps_stars' => '_dps_stars' ] as $f => $m ) {
		if ( isset( $_POST[ $f ] ) ) {
			update_post_meta( $id, $m, sanitize_text_field( wp_unslash( $_POST[ $f ] ) ) );
		}
	}
} );
