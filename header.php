<?php
/**
 * Site header: announcement bar, logo, menu, search and cart.
 *
 * @package j-shop
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
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'j-shop' ); ?></a>

<?php
$jshop_a  = jshop_mod( 'jshop_announce', __( 'Welcome to our store. Edit this text in the Customizer.', 'j-shop' ) );
$jshop_au = jshop_mod( 'jshop_announce_url', '' );
if ( $jshop_a ) :
	?>
	<div class="announce"><?php echo $jshop_au ? '<a href="' . esc_url( $jshop_au ) . '">' . esc_html( $jshop_a ) . '</a>' : esc_html( $jshop_a ); ?></div>
<?php endif; ?>

<header class="site-header">
	<div class="wrap header-in">
		<button class="icon-btn menu-toggle" aria-expanded="false" aria-label="<?php esc_attr_e( 'Menu', 'j-shop' ); ?>"><?php jshop_the_icon( 'menu' ); ?></button>
		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
			<?php
			if ( has_custom_logo() ) {
				echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full' );
			} else {
				bloginfo( 'name' ); }
			?>
		</a>
		<nav class="main-nav" aria-label="<?php esc_attr_e( 'Primary menu', 'j-shop' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'fallback_cb'    => 'jshop_fallback_menu',
				)
			);
			?>
		</nav>
		<div class="header-tools">
			<button class="icon-btn search-toggle" aria-expanded="false" aria-label="<?php esc_attr_e( 'Search', 'j-shop' ); ?>"><?php jshop_the_icon( 'search' ); ?></button>
			<?php if ( jshop_has_wc() ) : ?>
				<a class="icon-btn" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" aria-label="<?php esc_attr_e( 'My account', 'j-shop' ); ?>"><?php jshop_the_icon( 'user' ); ?></a>
				<a class="icon-btn cart-toggle" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'Cart', 'j-shop' ); ?>" aria-controls="mini-cart">
					<?php jshop_the_icon( 'cart' ); ?><span class="cart-count"><?php echo (int) WC()->cart->get_cart_contents_count(); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
	<div class="search-panel"><div class="wrap">
		<?php get_search_form( array( 'jshop_products' => true ) ); ?>
	</div></div>
</header>
