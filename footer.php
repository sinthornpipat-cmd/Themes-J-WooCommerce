<?php
/**
 * Site footer, mobile bottom nav and promo popup.
 *
 * @package themes-j-woocommerce
 */

$dps_shop   = dps_has_wc() ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$dps_shopee = dps_ft( 'dps_shop_url' );
$dps_m2u    = dps_mod( 'dps_mnav_2_u' );
$dps_m2u    = $dps_m2u ? $dps_m2u : $dps_shop;
$dps_m3u    = dps_mod( 'dps_mnav_3_u', $dps_shopee );
$dps_m3u    = $dps_m3u ? $dps_m3u : $dps_shop;
?>
<footer class="site-footer">
	<div class="wrap">
		<div class="foot-grid">

			<?php if ( dps_ft( 'dps_ft_show_1' ) ) : ?>
			<div class="foot-col">
				<h4 class="ft-company"><?php dps_ft_output( 'company' ); ?></h4>
				<p class="ft-address"><?php dps_ft_output( 'address' ); ?></p>
				<p class="ft-phone"><?php dps_ft_output( 'phone' ); ?></p>
				<p class="ft-fax"><?php dps_ft_output( 'fax' ); ?></p>
				<p class="ft-email"><?php dps_ft_output( 'email' ); ?></p>
				<?php dps_ft_widgets( 1 ); ?>
			</div>
			<?php endif; ?>

			<?php if ( dps_ft( 'dps_ft_show_2' ) ) : ?>
			<div class="foot-col">
				<h4 class="ft-menu-title"><?php dps_ft_output( 'menu_title' ); ?></h4>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'fallback_cb'    => 'dps_fallback_menu',
					)
				);
				?>
				<?php dps_ft_widgets( 2 ); ?>
			</div>
			<?php endif; ?>

			<?php if ( dps_ft( 'dps_ft_show_3' ) ) : ?>
			<div class="foot-col">
				<h4 class="ft-shop-title"><?php dps_ft_output( 'shop_title' ); ?></h4>
				<p class="ft-shop-link"><?php dps_ft_output( 'shop_link' ); ?></p>
				<p class="ft-all-link"><?php dps_ft_output( 'all_link' ); ?></p>
				<?php dps_ft_widgets( 3 ); ?>
			</div>
			<?php endif; ?>

			<?php if ( dps_ft( 'dps_ft_show_4' ) ) : ?>
			<div class="foot-col">
				<h4 class="ft-follow-title"><?php dps_ft_output( 'follow_title' ); ?></h4>
				<p class="ft-news-text"><?php dps_ft_output( 'news_text' ); ?></p>
				<div class="ft-news-form"><?php dps_ft_output( 'news_form' ); ?></div>
				<div class="socials ft-socials"><?php dps_ft_output( 'socials' ); ?></div>
				<?php dps_ft_widgets( 4 ); ?>
			</div>
			<?php endif; ?>

		</div>
		<div class="copy">
			<span class="ft-copy"><?php dps_ft_output( 'copy' ); ?></span>
			<span class="ft-credit"><?php dps_ft_output( 'credit' ); ?></span>
		</div>
	</div>
</footer>

<nav class="m-nav" aria-label="เมนูมือถือ">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>">หน้าแรก</a>
	<a href="<?php echo esc_url( $dps_m2u ); ?>"><?php echo esc_html( dps_mod( 'dps_mnav_2_t', 'สินค้า' ) ); ?></a>
	<a href="<?php echo esc_url( $dps_m3u ); ?>"><?php echo esc_html( dps_mod( 'dps_mnav_3_t', 'Shopee' ) ); ?></a>
	<a class="cart-toggle" href="<?php echo esc_url( dps_has_wc() ? wc_get_cart_url() : home_url( '/' ) ); ?>">ตะกร้า</a>
</nav>

<?php if ( dps_mod( 'dps_popup_on', false ) ) : ?>
<div class="promo" role="dialog" aria-modal="true" aria-label="โค้ดส่วนลด">
	<div class="promo-box">
		<button class="promo-close" aria-label="ปิด">&times;</button>
		<?php $dps_pi = dps_mod( 'dps_popup_img' ); ?>
		<?php
		if ( $dps_pi ) :
			?>
			<img src="<?php echo esc_url( $dps_pi ); ?>" alt="" loading="lazy" decoding="async"><?php endif; ?>
		<h3><?php echo esc_html( dps_mod( 'dps_popup_title', 'รับส่วนลดพิเศษ' ) ); ?></h3>
		<p><?php echo esc_html( dps_mod( 'dps_popup_sub', 'แก้ข้อความและเงื่อนไขได้ใน Customizer' ) ); ?></p>
		<p style="margin:0">ตัดสินใจช้อปภายใน</p>
		<div class="promo-time" aria-live="off">05:00</div>
		<div class="promo-code"><span><?php echo esc_html( dps_mod( 'dps_popup_code', 'SAMPLE10' ) ); ?></span><button type="button">คัดลอกโค้ด</button></div>
		<small>นำโค้ดไปใช้ตอน Checkout ได้เลย</small>
	</div>
</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
