<?php
/**
 * Home page template: hero, trust strip and category grid.
 *
 * @package j-shop
 */

get_header();
?>
<main id="main">

<?php
/* Hero */
$jshop_slides = array();
for ( $jshop_i = 1; $jshop_i <= 5; $jshop_i++ ) {
	$jshop_img = jshop_mod( "jshop_hero_$jshop_i" );
	if ( $jshop_img ) {
		$jshop_slides[] = array( $jshop_img, jshop_mod( "jshop_hero_url_$jshop_i" ) );
	}
}
?>
<section class="hero" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Promotional banners', 'j-shop' ); ?>">
<?php
if ( $jshop_slides ) :
	$jshop_shop_url = jshop_has_wc() ? wc_get_page_permalink( 'shop' ) : '';
	?>
	<div class="hero-track">
		<?php
		foreach ( $jshop_slides as $jshop_i => $jshop_slide ) :
			$jshop_sample     = false !== strpos( $jshop_slide[0], '/assets/img/hero-' );
			$jshop_slide_link = $jshop_slide[1] ? $jshop_slide[1] : ( $jshop_sample ? $jshop_shop_url : '' );
			?>
			<div class="hero-slide">
				<?php if ( $jshop_slide_link ) : ?>
					<a href="<?php echo esc_url( $jshop_slide_link ); ?>">
				<?php endif; ?>
				<img src="<?php echo esc_url( $jshop_slide[0] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" <?php echo $jshop_i ? 'loading="lazy"' : 'fetchpriority="high"'; ?>>
				<?php if ( $jshop_slide_link ) : ?>
					</a>
				<?php endif; ?>
				<?php if ( $jshop_slide_link && ! $jshop_sample ) : ?>
					<a class="btn hero-cta" href="<?php echo esc_url( $jshop_slide_link ); ?>"><?php echo esc_html( jshop_mod( 'jshop_hero_cta', __( 'Shop now', 'j-shop' ) ) ); ?></a>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="hero-dots"></div>
<?php else : ?>
	<div class="hero-fallback wrap">
		<h1><?php bloginfo( 'name' ); ?></h1>
		<p><?php bloginfo( 'description' ); ?></p>
		<?php
		if ( jshop_has_wc() ) :
			?>
			<a class="btn" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Shop now', 'j-shop' ); ?></a><?php endif; ?>
	</div>
<?php endif; ?>
</section>

<?php
$jshop_boxes = array();
for ( $jshop_i = 1; $jshop_i <= 4; $jshop_i++ ) {
	$jshop_trust_img = jshop_mod( "jshop_trust_img_$jshop_i" );
	if ( $jshop_trust_img ) {
		$jshop_boxes[] = array( $jshop_trust_img, jshop_mod( "jshop_trust_url_$jshop_i" ) );
	}
}
if ( $jshop_boxes ) :
	?>
<div class="wrap"><div class="trust">
	<?php foreach ( $jshop_boxes as $jshop_box ) : ?>
		<?php if ( $jshop_box[1] ) : ?>
			<a class="trust-box" href="<?php echo esc_url( $jshop_box[1] ); ?>"><img src="<?php echo esc_url( $jshop_box[0] ); ?>" alt="" loading="lazy" decoding="async"></a>
		<?php else : ?>
			<div class="trust-box"><img src="<?php echo esc_url( $jshop_box[0] ); ?>" alt="" loading="lazy" decoding="async"></div>
		<?php endif; ?>
	<?php endforeach; ?>
</div></div>
<?php endif; ?>

<?php
/* Category tiles */
if ( jshop_has_wc() ) :
	$jshop_cats = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => 0,
			'hide_empty' => true,
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
			'number'     => 8,
			'orderby'    => 'name',
		)
	);
	if ( ! is_wp_error( $jshop_cats ) && $jshop_cats ) :
		?>
	<section class="section"><div class="wrap">
		<?php $jshop_cats_title = jshop_mod( 'jshop_cats_title', __( 'Shop by category', 'j-shop' ) ); ?>
		<?php if ( $jshop_cats_title ) : ?>
			<h2 class="cats-title"><?php echo esc_html( $jshop_cats_title ); ?></h2>
		<?php endif; ?>
		<div class="cat-grid">
		<?php
		foreach ( $jshop_cats as $jshop_c ) :
			$jshop_tid = (int) get_term_meta( $jshop_c->term_id, 'thumbnail_id', true );
			?>
			<a class="cat-tile" href="<?php echo esc_url( get_term_link( $jshop_c ) ); ?>">
				<?php if ( $jshop_tid ) : ?>
					<?php echo wp_get_attachment_image( $jshop_tid, 'jshop-card', false, array( 'loading' => 'lazy' ) ); ?>
				<?php else : ?>
					<img src="<?php echo esc_url( jshop_img_url( 'placeholder.jpg' ) ); ?>" alt="" loading="lazy">
				<?php endif; ?>
				<span class="cat-name"><?php echo esc_html( $jshop_c->name ); ?></span>
				<small><?php echo esc_html( sprintf( /* translators: %d: number of products. */ _n( '%d item', '%d items', $jshop_c->count, 'j-shop' ), $jshop_c->count ) ); ?></small>
			</a>
		<?php endforeach; ?>
		</div>
	</div></section>
		<?php
endif;
endif;
?>

<?php
if ( ! jshop_has_wc() ) {
	echo '<div class="wrap section"><p>' . esc_html__( 'Install and activate the WooCommerce plugin to show products here.', 'j-shop' ) . '</p></div>';
}
?>

</main>
<?php get_footer(); ?>
