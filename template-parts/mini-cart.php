<?php
/**
 * Slide-out mini cart drawer. WooCommerce cart fragments refresh .widget_shopping_cart_content.
 *
 * @package themes-j-woocommerce
 */

?>
<div class="mc-overlay" hidden></div>
<aside class="mc-drawer" id="mini-cart" role="dialog" aria-modal="true" aria-label="ตะกร้าสินค้า" aria-hidden="true">
	<header class="mc-head">
		<h2>ตะกร้าสินค้า <span class="mc-count">(<?php echo (int) WC()->cart->get_cart_contents_count(); ?>)</span></h2>
		<button type="button" class="mc-close" aria-label="ปิด">&times;</button>
	</header>
	<?php echo dps_ship_bar_html(); // phpcs:ignore ?>
	<div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
</aside>
