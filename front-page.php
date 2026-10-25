<?php
/**
 * Home page template: hero, trust strip, categories, products, ideas, reviews, FAQ.
 *
 * @package themes-j-woocommerce
 */

get_header();
?>
<main id="main">

<?php
/* Hero */
$dps_slides = array();
for ( $dps_i = 1; $dps_i <= 5; $dps_i++ ) {
	$dps_img = dps_mod( "dps_hero_$dps_i" );
	if ( $dps_img ) {
		$dps_slides[] = array( $dps_img, dps_mod( "dps_hero_url_$dps_i" ) );
	}
}
?>
<section class="hero" aria-roledescription="carousel" aria-label="แบนเนอร์โปรโมชัน">
<?php
if ( $dps_slides ) :
	$dps_shop_url = dps_has_wc() ? wc_get_page_permalink( 'shop' ) : '';
	?>
	<div class="hero-track">
		<?php
		foreach ( $dps_slides as $dps_i => $dps_slide ) :
			$dps_sample     = false !== strpos( $dps_slide[0], '/assets/img/hero-' );
			$dps_slide_link = $dps_slide[1] ? $dps_slide[1] : ( $dps_sample ? $dps_shop_url : '' );
			?>
			<div class="hero-slide">
				<?php if ( $dps_slide_link ) : ?>
					<a href="<?php echo esc_url( $dps_slide_link ); ?>">
				<?php endif; ?>
				<img src="<?php echo esc_url( $dps_slide[0] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" <?php echo $dps_i ? 'loading="lazy"' : 'fetchpriority="high"'; ?>>
				<?php if ( $dps_slide_link ) : ?>
					</a>
				<?php endif; ?>
				<?php if ( $dps_slide_link && ! $dps_sample ) : ?>
					<a class="btn hero-cta" href="<?php echo esc_url( $dps_slide_link ); ?>"><?php echo esc_html( dps_mod( 'dps_hero_cta', 'ดูสินค้า' ) ); ?></a>
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
		if ( dps_has_wc() ) :
			?>
			<a class="btn" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">ดูสินค้า</a><?php endif; ?>
	</div>
<?php endif; ?>
</section>

<?php if ( dps_mod( 'dps_show_trust', true ) ) : ?>
<div class="wrap"><div class="trust">
	<?php
	$dps_td = array( array( 'ผู้ผลิตของใช้พลาสติกในบ้าน', 'รายใหญ่ของประเทศไทย' ), array( 'Thailand Trust Mark', 'รางวัลประจำปี 2566' ), array( 'สินค้ากว่า 240 รายการ', '10 หมวดหมู่ ครอบคลุมทุกการใช้งาน' ), array( 'สั่งซื้อผ่าน Shopee', 'petchsiam_official_store' ) );
	for ( $dps_i = 1; $dps_i <= 4; $dps_i++ ) :
		$dps_trust_title = dps_mod( "dps_trust_t_$dps_i", $dps_td[ $dps_i - 1 ][0] );
		$dps_trust_sub   = dps_mod( "dps_trust_s_$dps_i", $dps_td[ $dps_i - 1 ][1] );
		if ( ! $dps_trust_title ) {
			continue;
		}
		?>
		<div><strong><?php echo esc_html( $dps_trust_title ); ?></strong><?php echo esc_html( $dps_trust_sub ); ?></div>
	<?php endfor; ?>
</div></div>
<?php endif; ?>

<?php
/* Category tiles */
if ( dps_has_wc() && dps_mod( 'dps_show_cats', true ) ) :
	$dps_cats = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => 0,
			'hide_empty' => true,
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
			'number'     => 8,
			'orderby'    => 'name',
		)
	);
	if ( ! is_wp_error( $dps_cats ) && $dps_cats ) :
		?>
	<section class="section"><div class="wrap">
		<div class="section-head"><h2><?php echo esc_html( dps_mod( 'dps_cats_title', 'หมวดหมู่สินค้า' ) ); ?></h2>
			<a class="view-all" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">ดูสินค้าทั้งหมด</a></div>
		<div class="cat-grid">
		<?php
		foreach ( $dps_cats as $dps_c ) :
			$dps_tid = (int) get_term_meta( $dps_c->term_id, 'thumbnail_id', true );
			?>
			<a class="cat-tile" href="<?php echo esc_url( get_term_link( $dps_c ) ); ?>">
				<?php if ( $dps_tid ) : ?>
					<?php echo wp_get_attachment_image( $dps_tid, 'dps-card', false, array( 'loading' => 'lazy' ) ); ?>
				<?php else : ?>
					<img src="<?php echo esc_url( dps_img_url( 'placeholder.jpg' ) ); ?>" alt="" loading="lazy">
				<?php endif; ?>
				<span class="cat-name"><?php echo esc_html( $dps_c->name ); ?></span>
				<small><?php echo esc_html( sprintf( '%d รายการ', $dps_c->count ) ); ?></small>
			</a>
		<?php endforeach; ?>
		</div>
	</div></section>
		<?php
endif;
endif;
?>

<?php
if ( ! dps_has_wc() ) {
	echo '<div class="wrap section"><p>กรุณาติดตั้งและเปิดใช้งานปลั๊กอิน WooCommerce เพื่อแสดงสินค้า</p></div>';
} else {
	for ( $dps_n = 1; $dps_n <= 5; $dps_n++ ) {
		get_template_part( 'template-parts/product-section', null, array( 'n' => $dps_n ) );
	}
}

/* Home ideas feed */
if ( dps_mod( 'dps_show_feed', true ) ) :
	$dps_feed = new WP_Query(
		array(
			'post_type'              => 'dps_feed',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'posts_per_page'         => 6,
			'orderby'                => 'menu_order date',
			'order'                  => 'ASC',
		)
	);
	if ( $dps_feed->have_posts() ) :
		?>
	<section class="section"><div class="wrap">
		<div class="section-head"><h2><?php echo esc_html( dps_mod( 'dps_feed_title', 'ไอเดียจัดบ้าน' ) ); ?></h2></div>
		<div class="feed">
		<?php
		while ( $dps_feed->have_posts() ) :
			$dps_feed->the_post();
			$dps_pid = (int) get_post_meta( get_the_ID(), '_dps_product', true );
			$dps_url = $dps_pid ? get_permalink( $dps_pid ) : '#';
			$dps_h   = get_post_meta( get_the_ID(), '_dps_handle', true );
			?>
			<a class="feed-item" href="<?php echo esc_url( $dps_url ); ?>">
				<?php the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); ?>
				<div class="feed-meta">
				<?php
				if ( $dps_h ) {
					echo '<b>' . esc_html( $dps_h ) . '</b>';}
				?>
				<?php
				if ( $dps_pid ) {
					echo esc_html( get_the_title( $dps_pid ) );}
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
if ( dps_mod( 'dps_show_reviews', true ) ) :
	$dps_rev = new WP_Query(
		array(
			'post_type'              => 'dps_review',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'posts_per_page'         => 8,
			'orderby'                => 'menu_order date',
			'order'                  => 'ASC',
		)
	);
	if ( $dps_rev->have_posts() ) :
		?>
	<section class="section reviews"><div class="wrap">
		<div class="section-head"><h2><?php echo esc_html( dps_mod( 'dps_reviews_title', 'เสียงจากลูกค้า' ) ); ?></h2></div>
		<div class="review-track">
		<?php
		while ( $dps_rev->have_posts() ) :
			$dps_rev->the_post();
			$dps_pid   = (int) get_post_meta( get_the_ID(), '_dps_product', true );
			$dps_stars = max( 1, min( 5, (int) ( get_post_meta( get_the_ID(), '_dps_stars', true ) ? get_post_meta( get_the_ID(), '_dps_stars', true ) : 5 ) ) );
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
				<div class="stars" aria-label="<?php echo esc_attr( $dps_stars ); ?> ดาว"><?php echo esc_html( str_repeat( '★', $dps_stars ) . str_repeat( '☆', 5 - $dps_stars ) ); ?></div>
				<p><?php echo esc_html( wp_strip_all_tags( get_the_content() ) ); ?></p>
				<?php
				if ( $dps_pid ) :
					?>
					<a class="review-prod" href="<?php echo esc_url( get_permalink( $dps_pid ) ); ?>"><?php echo esc_html( get_the_title( $dps_pid ) ); ?></a><?php endif; ?>
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
if ( dps_mod( 'dps_show_faq', true ) ) :
	$dps_faq = new WP_Query(
		array(
			'post_type'              => 'dps_faq',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'posts_per_page'         => 12,
			'orderby'                => 'menu_order date',
			'order'                  => 'ASC',
		)
	);
	if ( $dps_faq->have_posts() ) :
		?>
	<section class="section"><div class="wrap">
		<div class="section-head" style="justify-content:center"><h2><?php echo esc_html( dps_mod( 'dps_faq_title', 'คำถามที่พบบ่อย' ) ); ?></h2></div>
		<div class="faq">
		<?php
		while ( $dps_faq->have_posts() ) :
			$dps_faq->the_post();
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
