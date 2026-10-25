<?php
/**
 * Product card used on the home page sections. Expects the global $product.
 *
 * @package themes-j-woocommerce
 */

global $product;
if ( ! $product ) {
	return;
}
$dps_badge = dps_mod( 'dps_badge', '' );
?>
<li class="p-card">
	<?php if ( $dps_badge ) : ?>
		<span class="p-badge"><?php echo esc_html( $dps_badge ); ?></span>
	<?php endif; ?>
	<a class="p-thumb" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
		<?php echo wp_kses_post( $product->get_image( 'dps-card', array( 'loading' => 'lazy' ) ) ); ?>
	</a>
	<h3 class="p-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	<span class="p-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
	<?php woocommerce_template_loop_add_to_cart(); ?>
</li>
