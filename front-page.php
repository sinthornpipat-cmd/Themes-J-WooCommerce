<?php
/**
 * Home page template: hero, trust strip, categories, products, ideas, reviews, FAQ.
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

<?php if ( jshop_mod( 'jshop_show_trust', true ) ) : ?>
<div class="wrap"><div class="trust">
	<?php
	$jshop_td = jshop_trust_defaults();
	for ( $jshop_i = 1; $jshop_i <= 4; $jshop_i++ ) :
		$jshop_trust_title = jshop_mod( "jshop_trust_t_$jshop_i", $jshop_td[ $jshop_i - 1 ][0] );
		$jshop_trust_sub   = jshop_mod( "jshop_trust_s_$jshop_i", $jshop_td[ $jshop_i - 1 ][1] );
		if ( ! $jshop_trust_title ) {
			continue;
		}
		?>
		<div><strong><?php echo esc_html( $jshop_trust_title ); ?></strong><?php echo esc_html( $jshop_trust_sub ); ?></div>
	<?php endfor; ?>
</div></div>
<?php endif; ?>

<?php
/* Category tiles */
if ( jshop_has_wc() && jshop_mod( 'jshop_show_cats', true ) ) :
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
		<div class="section-head"><h2><?php echo esc_html( jshop_mod( 'jshop_cats_title', __( 'Shop by category', 'j-shop' ) ) ); ?></h2>
			<a class="view-all" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'View all products', 'j-shop' ); ?></a></div>
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
} else {
	for ( $jshop_n = 1; $jshop_n <= 5; $jshop_n++ ) {
		get_template_part( 'template-parts/product-section', null, array( 'n' => $jshop_n ) );
	}
}

/* Home ideas feed */
if ( jshop_mod( 'jshop_show_feed', true ) ) :
	$jshop_feed = new WP_Query(
		array(
			'post_type'              => 'dps_feed',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'posts_per_page'         => 6,
			'orderby'                => 'menu_order date',
			'order'                  => 'ASC',
		)
	);
	if ( $jshop_feed->have_posts() ) :
		?>
	<section class="section"><div class="wrap">
		<div class="section-head"><h2><?php echo esc_html( jshop_mod( 'jshop_feed_title', __( 'Home ideas', 'j-shop' ) ) ); ?></h2></div>
		<div class="feed">
		<?php
		while ( $jshop_feed->have_posts() ) :
			$jshop_feed->the_post();
			$jshop_pid = (int) get_post_meta( get_the_ID(), '_dps_product', true );
			$jshop_url = $jshop_pid ? get_permalink( $jshop_pid ) : '#';
			$jshop_h   = get_post_meta( get_the_ID(), '_dps_handle', true );
			?>
			<a class="feed-item" href="<?php echo esc_url( $jshop_url ); ?>">
				<?php the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); ?>
				<div class="feed-meta">
				<?php
				if ( $jshop_h ) {
					echo '<b>' . esc_html( $jshop_h ) . '</b>';}
				?>
				<?php
				if ( $jshop_pid ) {
					echo esc_html( get_the_title( $jshop_pid ) );}
				?>
</div>
			</a>
			<?php
		endwhile;
		wp_reset_postdata();
		?>
		</div>
	</div></section>
		<?php
endif;
endif;

/* Reviews */
if ( jshop_mod( 'jshop_show_reviews', true ) ) :
	$jshop_rev = new WP_Query(
		array(
			'post_type'              => 'dps_review',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'posts_per_page'         => 8,
			'orderby'                => 'menu_order date',
			'order'                  => 'ASC',
		)
	);
	if ( $jshop_rev->have_posts() ) :
		?>
	<section class="section reviews"><div class="wrap">
		<div class="section-head"><h2><?php echo esc_html( jshop_mod( 'jshop_reviews_title', __( 'What our customers say', 'j-shop' ) ) ); ?></h2></div>
		<div class="review-track">
		<?php
		while ( $jshop_rev->have_posts() ) :
			$jshop_rev->the_post();
			$jshop_pid   = (int) get_post_meta( get_the_ID(), '_dps_product', true );
			$jshop_stars = max( 1, min( 5, (int) ( get_post_meta( get_the_ID(), '_dps_stars', true ) ? get_post_meta( get_the_ID(), '_dps_stars', true ) : 5 ) ) );
			?>
			<article class="review">
				<div class="review-who">
					<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail( 'thumbnail' );
					} else {
						echo '<span class="ph">' . esc_html( mb_substr( get_the_title(), 0, 1 ) ) . '</span>';
					}
					?>
					<div><strong><?php the_title(); ?></strong><small>Verified Buyer</small></div>
				</div>
				<div class="stars" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: star rating 1-5. */ _n( '%d star', '%d stars', $jshop_stars, 'j-shop' ), $jshop_stars ) ); ?>"><?php echo esc_html( str_repeat( '★', $jshop_stars ) . str_repeat( '☆', 5 - $jshop_stars ) ); ?></div>
				<p><?php echo esc_html( wp_strip_all_tags( get_the_content() ) ); ?></p>
				<?php
				if ( $jshop_pid ) :
					?>
					<a class="review-prod" href="<?php echo esc_url( get_permalink( $jshop_pid ) ); ?>"><?php echo esc_html( get_the_title( $jshop_pid ) ); ?></a><?php endif; ?>
			</article>
			<?php
		endwhile;
		wp_reset_postdata();
		?>
		</div>
	</div></section>
		<?php
endif;
endif;

/* FAQ */
if ( jshop_mod( 'jshop_show_faq', true ) ) :
	$jshop_faq = new WP_Query(
		array(
			'post_type'              => 'dps_faq',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'posts_per_page'         => 12,
			'orderby'                => 'menu_order date',
			'order'                  => 'ASC',
		)
	);
	if ( $jshop_faq->have_posts() ) :
		?>
	<section class="section"><div class="wrap">
		<div class="section-head" style="justify-content:center"><h2><?php echo esc_html( jshop_mod( 'jshop_faq_title', __( 'Frequently asked questions', 'j-shop' ) ) ); ?></h2></div>
		<div class="faq">
		<?php
		while ( $jshop_faq->have_posts() ) :
			$jshop_faq->the_post();
			?>
			<details><summary><?php the_title(); ?></summary><div class="ans"><?php the_content(); ?></div></details>
			<?php
		endwhile;
		wp_reset_postdata();
		?>
		</div>
	</div></section>
		<?php
endif;
endif;
?>

</main>
<?php get_footer(); ?>
