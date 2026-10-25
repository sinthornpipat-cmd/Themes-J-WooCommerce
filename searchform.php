<?php
/**
 * Search form. The header passes jshop_products to search products only.
 *
 * @package j-shop
 */

$jshop_products = ! empty( $args['jshop_products'] ) && jshop_has_wc();
$jshop_id       = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $jshop_id ); ?>"><?php esc_html_e( 'Search', 'j-shop' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $jshop_id ); ?>" name="s" placeholder="<?php echo $jshop_products ? esc_attr__( 'Search products…', 'j-shop' ) : esc_attr__( 'Search…', 'j-shop' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
	<?php if ( $jshop_products ) : ?>
		<input type="hidden" name="post_type" value="product">
	<?php endif; ?>
	<button type="submit"><?php esc_html_e( 'Search', 'j-shop' ); ?></button>
</form>
