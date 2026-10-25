<?php
/**
 * Plugin Name:       DPS Store Extras
 * Description:       Store features for the J Shop theme that WordPress.org does not allow inside a theme: customer reviews, FAQ and home-ideas post types, one-click demo content, editable checkout labels and a few speed tweaks.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            n2npay
 * Author URI:        https://n2npay.freedev.app/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       dps-store-extras
 *
 * @package dps-store-extras
 */

defined( 'ABSPATH' ) || exit;

define( 'DPS_SE_DIR', plugin_dir_path( __FILE__ ) );
define( 'DPS_SE_URL', plugin_dir_url( __FILE__ ) );

require DPS_SE_DIR . 'includes/post-types.php';
require DPS_SE_DIR . 'includes/demo.php';
require DPS_SE_DIR . 'includes/checkout-labels.php';
require DPS_SE_DIR . 'includes/performance.php';
require DPS_SE_DIR . 'includes/migrate.php';

/**
 * On activation: register the post types, seed the default FAQ once and
 * flag the demo-data notice.
 *
 * @return void
 */
function dps_se_activate() {
	dps_register_cpts();
	if ( 0 === (int) wp_count_posts( 'dps_faq' )->publish ) {
		$faq = array(
			'สั่งซื้อสินค้าได้ที่ไหน?'                   => 'สั่งซื้อออนไลน์ได้ที่ Shopee หรือติดต่อฝ่ายขายโดยตรงทางอีเมล / โทร (+66) 2415 8987, 2416 1040',
			'สินค้ามีหมวดหมู่อะไรบ้าง?'                  => 'มีทั้งกล่องเก็บของ ตู้ลิ้นชัก ตะกร้าผ้าและของใช้ซักรีด ของใช้ในครัว ถังและกะละมัง ชั้นวาง เฟอร์นิเจอร์พลาสติก และสินค้างานหนัก (Heavy Duty)',
			'สั่งซื้อจำนวนมากหรือสั่งทำพิเศษได้หรือไม่?' => 'สอบถามราคาและเงื่อนไขสำหรับการสั่งซื้อจำนวนมากได้ที่ฝ่ายขาย ผ่านอีเมลหรือโทรศัพท์ตามข้อมูลด้านล่างของเว็บไซต์',
			'ที่อยู่สำนักงานอยู่ที่ไหน?'                 => '4 ซอยพระยามนธาตุ แยก 9 แขวงคลองบางบอน เขตบางบอน กรุงเทพมหานคร 10150',
		);
		$o   = 0;
		foreach ( $faq as $q => $a ) {
			wp_insert_post(
				array(
					'post_type'    => 'dps_faq',
					'post_status'  => 'publish',
					'post_title'   => $q,
					'post_content' => $a,
					'menu_order'   => $o++,
				)
			);
		}
	}
	set_transient( 'dps_demo_notice', 1, DAY_IN_SECONDS );
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'dps_se_activate' );
