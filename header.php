<?php
/**
 * Site header: announcement bar, logo, menu, search and cart.
 *
 * @package themes-j-woocommerce
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">ข้ามไปยังเนื้อหา</a>

<?php
$dps_a  = dps_mod( 'dps_announce', '🛒 สั่งซื้อออนไลน์ได้ที่ Shopee: petchsiam_official_store' );
$dps_au = dps_mod( 'dps_announce_url', 'https://shopee.co.th/petchsiam_official_store' );
if ( $dps_a ) :
	?>
	<div class="announce"><?php echo $dps_au ? '<a href="' . esc_url( $dps_au ) . '">' . esc_html( $dps_a ) . '</a>' : esc_html( $dps_a ); ?></div>
<?php endif; ?>

<header class="site-header">
	<div class="wrap header-in">
		<button class="icon-btn menu-toggle" aria-expanded="false" aria-label="เมนู"><?php dps_the_icon( 'menu' ); ?></button>
		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
			<?php
			if ( has_custom_logo() ) {
				echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full' );
			} else {
				bloginfo( 'name' ); }
			?>
		</a>
		<nav class="main-nav" aria-label="เมนูหลัก">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'fallback_cb'    => 'dps_fallback_menu',
				)
			);
			?>
		</nav>
		<div class="header-tools">
			<button class="icon-btn search-toggle" aria-expanded="false" aria-label="ค้นหา"><?php dps_the_icon( 'search' ); ?></button>
			<?php if ( dps_has_wc() ) : ?>
				<a class="icon-btn" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" aria-label="บัญชี"><?php dps_the_icon( 'user' ); ?></a>
				<a class="icon-btn cart-toggle" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="ตะกร้า" aria-controls="mini-cart">
					<?php dps_the_icon( 'cart' ); ?><span class="cart-count"><?php echo (int) WC()->cart->get_cart_contents_count(); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
	<div class="search-panel"><div class="wrap">
		<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="s">ค้นหา</label>
			<input type="search" id="s" name="s" placeholder="ค้นหาสินค้า…" value="<?php echo esc_attr( get_search_query() ); ?>">
			<?php
			if ( dps_has_wc() ) :
				?>
				<input type="hidden" name="post_type" value="product"><?php endif; ?>
			<button type="submit">ค้นหา</button>
		</form>
	</div></div>
</header>
