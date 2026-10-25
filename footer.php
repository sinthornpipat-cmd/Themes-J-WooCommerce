<?php
/**
 * Site footer, mobile bottom nav and promo popup.
 *
 * @package j-shop
 */

$jshop_shop = jshop_has_wc() ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$jshop_acct = jshop_has_wc() ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );
$jshop_m2u  = jshop_mod( 'jshop_mnav_2_u' );
$jshop_m2u  = $jshop_m2u ? $jshop_m2u : $jshop_shop;
$jshop_m3u  = jshop_mod( 'jshop_mnav_3_u', '' );
$jshop_m3u  = $jshop_m3u ? $jshop_m3u : $jshop_acct;
?>
<footer class="site-footer">
	<div class="wrap">
		<div class="foot-grid">

			<?php if ( jshop_ft( 'jshop_ft_show_1' ) ) : ?>
			<div class="foot-col">
				<h4 class="ft-company"><?php jshop_ft_output( 'company' ); ?></h4>
				<p class="ft-address"><?php jshop_ft_output( 'address' ); ?></p>
				<p class="ft-phone"><?php jshop_ft_output( 'phone' ); ?></p>
				<p class="ft-fax"><?php jshop_ft_output( 'fax' ); ?></p>
				<p class="ft-email"><?php jshop_ft_output( 'email' ); ?></p>
				<?php jshop_ft_widgets( 1 ); ?>
			</div>
			<?php endif; ?>

			<?php if ( jshop_ft( 'jshop_ft_show_2' ) ) : ?>
			<div class="foot-col">
				<h4 class="ft-menu-title"><?php jshop_ft_output( 'menu_title' ); ?></h4>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'fallback_cb'    => 'jshop_fallback_menu',
					)
				);
				?>
				<?php jshop_ft_widgets( 2 ); ?>
			</div>
			<?php endif; ?>

			<?php if ( jshop_ft( 'jshop_ft_show_3' ) ) : ?>
			<div class="foot-col">
				<h4 class="ft-shop-title"><?php jshop_ft_output( 'shop_title' ); ?></h4>
				<p class="ft-shop-link"><?php jshop_ft_output( 'shop_link' ); ?></p>
				<p class="ft-all-link"><?php jshop_ft_output( 'all_link' ); ?></p>
				<?php jshop_ft_widgets( 3 ); ?>
			</div>
			<?php endif; ?>

			<?php if ( jshop_ft( 'jshop_ft_show_4' ) ) : ?>
			<div class="foot-col">
				<h4 class="ft-follow-title"><?php jshop_ft_output( 'follow_title' ); ?></h4>
				<p class="ft-news-text"><?php jshop_ft_output( 'news_text' ); ?></p>
				<div class="ft-news-form"><?php jshop_ft_output( 'news_form' ); ?></div>
				<div class="socials ft-socials"><?php jshop_ft_output( 'socials' ); ?></div>
				<?php jshop_ft_widgets( 4 ); ?>
			</div>
			<?php endif; ?>

		</div>
		<div class="copy">
			<span class="ft-copy"><?php jshop_ft_output( 'copy' ); ?></span>
			<span class="ft-credit"><?php jshop_ft_output( 'credit' ); ?></span>
		</div>
	</div>
</footer>

<nav class="m-nav" aria-label="<?php esc_attr_e( 'Mobile menu', 'j-shop' ); ?>">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'j-shop' ); ?></a>
	<a href="<?php echo esc_url( $jshop_m2u ); ?>"><?php echo esc_html( jshop_mod( 'jshop_mnav_2_t', __( 'Products', 'j-shop' ) ) ); ?></a>
	<a href="<?php echo esc_url( $jshop_m3u ); ?>"><?php echo esc_html( jshop_mod( 'jshop_mnav_3_t', __( 'Account', 'j-shop' ) ) ); ?></a>
	<a class="cart-toggle" href="<?php echo esc_url( jshop_has_wc() ? wc_get_cart_url() : home_url( '/' ) ); ?>"><?php esc_html_e( 'Cart', 'j-shop' ); ?></a>
</nav>

<?php if ( jshop_mod( 'jshop_popup_on', false ) ) : ?>
<div class="promo" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Discount code', 'j-shop' ); ?>">
	<div class="promo-box">
		<button class="promo-close" aria-label="<?php esc_attr_e( 'Close', 'j-shop' ); ?>">&times;</button>
		<?php $jshop_pi = jshop_mod( 'jshop_popup_img' ); ?>
		<?php
		if ( $jshop_pi ) :
			?>
			<img src="<?php echo esc_url( $jshop_pi ); ?>" alt="" loading="lazy" decoding="async"><?php endif; ?>
		<h3><?php echo esc_html( jshop_mod( 'jshop_popup_title', __( 'Get a special discount', 'j-shop' ) ) ); ?></h3>
		<p><?php echo esc_html( jshop_mod( 'jshop_popup_sub', __( 'Edit this text and the conditions in the Customizer.', 'j-shop' ) ) ); ?></p>
		<p style="margin:0"><?php esc_html_e( 'Offer ends in', 'j-shop' ); ?></p>
		<div class="promo-time" aria-live="off">05:00</div>
		<div class="promo-code"><span><?php echo esc_html( jshop_mod( 'jshop_popup_code', 'SAMPLE10' ) ); ?></span><button type="button"><?php esc_html_e( 'Copy code', 'j-shop' ); ?></button></div>
		<small><?php esc_html_e( 'Use the code at checkout.', 'j-shop' ); ?></small>
	</div>
</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
