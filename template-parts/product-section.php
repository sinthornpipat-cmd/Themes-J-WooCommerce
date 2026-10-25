<?php
/**
 * Home page product section (banner, heading and a grid of products from one category).
 *
 * Expects $args['n'], the section index used in the Customizer setting IDs.
 *
 * @package j-shop
 */

$jshop_n        = (int) $args['n'];
$jshop_key      = "jshop_sec_$jshop_n";
$jshop_defaults = jshop_section_defaults();
$jshop_def      = isset( $jshop_defaults[ $jshop_n ] ) ? $jshop_defaults[ $jshop_n ] : array(
	'title' => '',
	'cat'   => '',
	'count' => '8',
	'sub'   => '',
);
$jshop_cat      = jshop_mod( "{$jshop_key}_cat", $jshop_def['cat'] );
if ( ! $jshop_cat || ! jshop_has_wc() ) {
	return;
}

$jshop_ids    = wc_get_products(
	array(
		'status'   => 'publish',
		'limit'    => max( 1, (int) jshop_mod( "{$jshop_key}_count", $jshop_def['count'] ) ),
		'category' => array( $jshop_cat ),
		'return'   => 'ids',
	)
);
$jshop_title  = jshop_mod( "{$jshop_key}_title", $jshop_def['title'] );
$jshop_sub    = jshop_mod( "{$jshop_key}_sub", $jshop_def['sub'] );
$jshop_banner = jshop_mod( "{$jshop_key}_banner" );
$jshop_link   = get_term_link( $jshop_cat, 'product_cat' );

if ( empty( $jshop_ids ) ) {
	if ( current_user_can( 'edit_theme_options' ) ) {
		echo '<section class="section"><div class="wrap"><p style="background:var(--mint);padding:14px;border-radius:12px">';
		printf(
			/* translators: 1: section title, 2: product category slug. */
			esc_html__( '[Admin] Section “%1$s”: no products found in the category with slug %2$s', 'j-shop' ),
			esc_html( $jshop_title ),
			'<code>' . esc_html( $jshop_cat ) . '</code>'
		);
		echo '</p></div></section>';
	}
	return;
}

$jshop_products = new WP_Query(
	array(
		'post_type'              => 'product',
		'post__in'               => $jshop_ids,
		'orderby'                => 'post__in',
		'posts_per_page'         => count( $jshop_ids ),
		'no_found_rows'          => true,
		'ignore_sticky_posts'    => true,
		'update_post_term_cache' => false,
	)
);
?>
<section class="section" id="sec-<?php echo esc_attr( $jshop_n ); ?>">
	<div class="wrap">
		<?php if ( $jshop_banner ) : ?>
			<div class="section-banner"><a href="<?php echo is_wp_error( $jshop_link ) ? '#' : esc_url( $jshop_link ); ?>"><img src="<?php echo esc_url( $jshop_banner ); ?>" alt="<?php echo esc_attr( $jshop_title ); ?>" loading="lazy"></a></div>
		<?php endif; ?>
		<div class="section-head">
			<div>
				<h2><?php echo esc_html( $jshop_title ); ?></h2>
				<?php if ( $jshop_sub ) : ?>
					<p><?php echo esc_html( $jshop_sub ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( ! is_wp_error( $jshop_link ) ) : ?>
				<a class="view-all" href="<?php echo esc_url( $jshop_link ); ?>"><?php esc_html_e( 'View all', 'j-shop' ); ?></a>
			<?php endif; ?>
		</div>
		<ul class="p-grid">
			<?php
			// the_post() also sets up the global $product through WooCommerce.
			while ( $jshop_products->have_posts() ) {
				$jshop_products->the_post();
				get_template_part( 'template-parts/product-card' );
			}
			wp_reset_postdata();
			?>
		</ul>
	</div>
</section>
