<?php
/**
 * Site footer and promo popup.
 *
 * @package j-shop
 */

$jshop_preview = is_customize_preview();
$jshop_contact = $jshop_preview || jshop_ft( 'jshop_ft_contact_text' ) || jshop_ft( 'jshop_ft_contact_url' ) || is_active_sidebar( 'footer-1' );
$jshop_addr    = $jshop_preview || jshop_ft( 'jshop_address' ) || is_active_sidebar( 'footer-2' );
?>
<footer class="site-footer">
	<div class="wrap">
		<?php if ( $jshop_contact || $jshop_addr ) : ?>
		<div class="ft-top">
			<?php if ( $jshop_contact ) : ?>
			<div class="ft-block">
				<svg class="ft-icon" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" aria-hidden="true"><path d="M12 2.5 3 21l9-4.5 9 4.5z"/><path d="M12 9v4"/></svg>
				<div>
					<h4 class="ft-contact-title"><?php jshop_ft_output( 'contact_title' ); ?></h4>
					<p class="ft-contact-text"><?php jshop_ft_output( 'contact_text' ); ?></p>
					<p class="ft-contact-btn"><?php jshop_ft_output( 'contact_btn' ); ?></p>
					<?php jshop_ft_widgets( 1 ); ?>
				</div>
			</div>
			<?php endif; ?>
			<?php if ( $jshop_addr ) : ?>
			<div class="ft-block">
				<svg class="ft-icon" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 21.5s-7-6.2-7-12a7 7 0 0 1 14 0c0 5.8-7 12-7 12z"/><circle cx="12" cy="9.5" r="2.6"/></svg>
				<div>
					<h4 class="ft-address-title"><?php jshop_ft_output( 'address_title' ); ?></h4>
					<p class="ft-address"><?php jshop_ft_output( 'address' ); ?></p>
					<?php jshop_ft_widgets( 2 ); ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<div class="ft-bottom">
			<div class="ft-news">
				<h3 class="ft-news-title"><?php jshop_ft_output( 'news_title' ); ?></h3>
				<p class="ft-news-text"><?php jshop_ft_output( 'news_text' ); ?></p>
				<?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
					<?php jshop_ft_widgets( 3 ); ?>
				<?php else : ?>
					<div class="ft-news-form"><?php jshop_ft_output( 'news_form' ); ?></div>
				<?php endif; ?>
				<p class="ft-legal"><?php jshop_ft_output( 'legal' ); ?></p>
			</div>
			<div class="ft-about">
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
				<?php jshop_ft_widgets( 4 ); ?>
			</div>
		</div>

		<div class="copy">
			<span class="ft-copy"><?php jshop_ft_output( 'copy' ); ?></span>
			<span class="ft-credit"><?php jshop_ft_output( 'credit' ); ?></span>
		</div>
	</div>
</footer>

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
