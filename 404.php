<?php
/**
 * 404 template.
 *
 * @package j-shop
 */

get_header();
?>
<main id="main" class="wrap content" style="text-align:center">
	<h1 class="entry-title"><?php esc_html_e( 'Page not found', 'j-shop' ); ?></h1>
	<p><?php esc_html_e( 'Try searching for a product, or go back to the home page.', 'j-shop' ); ?></p>
	<a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'j-shop' ); ?></a>
</main>
<?php get_footer(); ?>
