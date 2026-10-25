<?php
/** Args: n (section index) */
$n   = (int) $args['n'];
$k   = "dps_sec_$n";
$cat = dps_mod( "{$k}_cat" );
if ( ! $cat || ! dps_has_wc() ) {
	return;
}

$ids = wc_get_products( [
	'status'   => 'publish',
	'limit'    => max( 1, (int) dps_mod( "{$k}_count", 8 ) ),
	'category' => [ $cat ],
	'return'   => 'ids',
] );
$title  = dps_mod( "{$k}_title" );
$sub    = dps_mod( "{$k}_sub" );
$banner = dps_mod( "{$k}_banner" );
$link   = get_term_link( $cat, 'product_cat' );

if ( empty( $ids ) ) {
	if ( current_user_can( 'edit_theme_options' ) ) {
		echo '<section class="section"><div class="wrap"><p style="background:var(--mint);padding:14px;border-radius:12px">[ผู้ดูแล] เซกชัน “' . esc_html( $title ) . '”: ไม่พบสินค้าในหมวดหมู่ slug <code>' . esc_html( $cat ) . '</code></p></div></section>';
	}
	return;
}
?>
<section class="section" id="sec-<?php echo esc_attr( $n ); ?>">
	<div class="wrap">
		<?php if ( $banner ) : ?>
			<div class="section-banner"><a href="<?php echo is_wp_error( $link ) ? '#' : esc_url( $link ); ?>"><img src="<?php echo esc_url( $banner ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy"></a></div>
		<?php endif; ?>
		<div class="section-head">
			<div>
				<h2><?php echo esc_html( $title ); ?></h2>
				<?php if ( $sub ) echo '<p>' . esc_html( $sub ) . '</p>'; ?>
			</div>
			<?php if ( ! is_wp_error( $link ) ) : ?><a class="view-all" href="<?php echo esc_url( $link ); ?>">ดูทั้งหมด</a><?php endif; ?>
		</div>
		<ul class="p-grid">
			<?php
			foreach ( $ids as $id ) {
				$GLOBALS['post']    = get_post( $id ); // phpcs:ignore
				setup_postdata( $GLOBALS['post'] );
				$GLOBALS['product'] = wc_get_product( $id ); // phpcs:ignore
				get_template_part( 'template-parts/product-card' );
			}
			wp_reset_postdata();
			?>
		</ul>
	</div>
</section>
