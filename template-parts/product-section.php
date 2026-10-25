<?php
/**
 * Home page product section (banner, heading and a grid of products from one category).
 *
 * Expects $args['n'], the section index used in the Customizer setting IDs.
 *
 * @package themes-j-woocommerce
 */

$dps_n   = (int) $args['n'];
$dps_key = "dps_sec_$dps_n";
$dps_cat = dps_mod( "{$dps_key}_cat" );
if ( ! $dps_cat || ! dps_has_wc() ) {
	return;
}

$dps_ids    = wc_get_products(
	array(
		'status'   => 'publish',
		'limit'    => max( 1, (int) dps_mod( "{$dps_key}_count", 8 ) ),
		'category' => array( $dps_cat ),
		'return'   => 'ids',
	)
);
$dps_title  = dps_mod( "{$dps_key}_title" );
$dps_sub    = dps_mod( "{$dps_key}_sub" );
$dps_banner = dps_mod( "{$dps_key}_banner" );
$dps_link   = get_term_link( $dps_cat, 'product_cat' );

if ( empty( $dps_ids ) ) {
	if ( current_user_can( 'edit_theme_options' ) ) {
		echo '<section class="section"><div class="wrap"><p style="background:var(--mint);padding:14px;border-radius:12px">';
		printf(
			/* translators: 1: section title, 2: product category slug. */
			esc_html__( '[ผู้ดูแล] เซกชัน “%1$s”: ไม่พบสินค้าในหมวดหมู่ slug %2$s', 'themes-j-woocommerce' ),
			esc_html( $dps_title ),
			'<code>' . esc_html( $dps_cat ) . '</code>'
		);
		echo '</p></div></section>';
	}
	return;
}

$dps_products = new WP_Query(
	array(
		'post_type'              => 'product',
		'post__in'               => $dps_ids,
		'orderby'                => 'post__in',
		'posts_per_page'         => count( $dps_ids ),
		'no_found_rows'          => true,
		'ignore_sticky_posts'    => true,
		'update_post_term_cache' => false,
	)
);
?>
<section class="section" id="sec-<?php echo esc_attr( $dps_n ); ?>">
	<div class="wrap">
		<?php if ( $dps_banner ) : ?>
			<div class="section-banner"><a href="<?php echo is_wp_error( $dps_link ) ? '#' : esc_url( $dps_link ); ?>"><img src="<?php echo esc_url( $dps_banner ); ?>" alt="<?php echo esc_attr( $dps_title ); ?>" loading="lazy"></a></div>
		<?php endif; ?>
		<div class="section-head">
			<div>
				<h2><?php echo esc_html( $dps_title ); ?></h2>
				<?php if ( $dps_sub ) : ?>
					<p><?php echo esc_html( $dps_sub ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( ! is_wp_error( $dps_link ) ) : ?>
				<a class="view-all" href="<?php echo esc_url( $dps_link ); ?>"><?php esc_html_e( 'ดูทั้งหมด', 'themes-j-woocommerce' ); ?></a>
			<?php endif; ?>
		</div>
		<ul class="p-grid">
			<?php
			// the_post() also sets up the global $product through WooCommerce.
			while ( $dps_products->have_posts() ) {
				$dps_products->the_post();
				get_template_part( 'template-parts/product-card' );
			}
			wp_reset_postdata();
			?>
		</ul>
	</div>
</section>
