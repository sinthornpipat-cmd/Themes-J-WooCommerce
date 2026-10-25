<?php
/**
 * Home page template: hero, trust strip, categories, products, ideas, reviews, FAQ.
 *
 * @package petchsiam-shop
 */
?>
<?php get_header(); ?>
<main id="main">

<?php
/* Hero */
$slides = [];
for ( $i = 1; $i <= 5; $i++ ) {
	$img = dps_mod( "dps_hero_$i" );
	if ( $img ) {
		$slides[] = [ $img, dps_mod( "dps_hero_url_$i" ) ];
	}
}
?>
<section class="hero" aria-roledescription="carousel" aria-label="แบนเนอร์โปรโมชัน">
<?php if ( $slides ) : $shop_url = dps_has_wc() ? wc_get_page_permalink( 'shop' ) : ''; ?>
	<div class="hero-track">
		<?php foreach ( $slides as $i => $s ) :
			$sample = false !== strpos( $s[0], '/assets/img/hero-' );
			$link   = $s[1] ? $s[1] : ( $sample ? $shop_url : '' );
			$img    = '<img src="' . esc_url( $s[0] ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" ' . ( $i ? 'loading="lazy"' : 'fetchpriority="high"' ) . '>'; ?>
			<div class="hero-slide">
				<?php echo $link ? '<a href="' . esc_url( $link ) . '">' . $img . '</a>' : $img; // phpcs:ignore ?>
				<?php if ( $link && ! $sample ) : ?><a class="btn hero-cta" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( dps_mod( 'dps_hero_cta', 'ดูสินค้า' ) ); ?></a><?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="hero-dots"></div>
<?php else : ?>
	<div class="hero-fallback wrap">
		<h1><?php bloginfo( 'name' ); ?></h1>
		<p><?php bloginfo( 'description' ); ?></p>
		<?php if ( dps_has_wc() ) : ?><a class="btn" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">ดูสินค้า</a><?php endif; ?>
	</div>
<?php endif; ?>
</section>

<?php if ( dps_mod( 'dps_show_trust', true ) ) : ?>
<div class="wrap"><div class="trust">
	<?php
	$td = [ [ 'ผู้ผลิตของใช้พลาสติกในบ้าน', 'รายใหญ่ของประเทศไทย' ], [ 'Thailand Trust Mark', 'รางวัลประจำปี 2566' ], [ 'สินค้ากว่า 240 รายการ', '10 หมวดหมู่ ครอบคลุมทุกการใช้งาน' ], [ 'สั่งซื้อผ่าน Shopee', 'petchsiam_official_store' ] ];
	for ( $i = 1; $i <= 4; $i++ ) :
		$t = dps_mod( "dps_trust_t_$i", $td[ $i - 1 ][0] ); $s = dps_mod( "dps_trust_s_$i", $td[ $i - 1 ][1] );
		if ( ! $t ) continue; ?>
		<div><strong><?php echo esc_html( $t ); ?></strong><?php echo esc_html( $s ); ?></div>
	<?php endfor; ?>
</div></div>
<?php endif; ?>

<?php
/* Category tiles */
if ( dps_has_wc() && dps_mod( 'dps_show_cats', true ) ) :
	$cats = get_terms( [ 'taxonomy' => 'product_cat', 'parent' => 0, 'hide_empty' => true, 'exclude' => [ (int) get_option( 'default_product_cat' ) ], 'number' => 8, 'orderby' => 'name' ] );
	if ( ! is_wp_error( $cats ) && $cats ) : ?>
	<section class="section"><div class="wrap">
		<div class="section-head"><h2><?php echo esc_html( dps_mod( 'dps_cats_title', 'หมวดหมู่สินค้า' ) ); ?></h2>
			<a class="view-all" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">ดูสินค้าทั้งหมด</a></div>
		<div class="cat-grid">
		<?php foreach ( $cats as $c ) :
			$tid = (int) get_term_meta( $c->term_id, 'thumbnail_id', true ); ?>
			<a class="cat-tile" href="<?php echo esc_url( get_term_link( $c ) ); ?>">
				<?php echo $tid ? wp_get_attachment_image( $tid, 'dps-card', false, [ 'loading' => 'lazy' ] ) : '<img src="' . esc_url( dps_img_url( 'placeholder.jpg' ) ) . '" alt="" loading="lazy">'; // phpcs:ignore ?>
				<span class="cat-name"><?php echo esc_html( $c->name ); ?></span>
				<small><?php echo esc_html( sprintf( '%d รายการ', $c->count ) ); ?></small>
			</a>
		<?php endforeach; ?>
		</div>
	</div></section>
<?php endif; endif; ?>

<?php
if ( ! dps_has_wc() ) {
	echo '<div class="wrap section"><p>กรุณาติดตั้งและเปิดใช้งานปลั๊กอิน WooCommerce เพื่อแสดงสินค้า</p></div>';
} else {
	for ( $n = 1; $n <= 5; $n++ ) {
		get_template_part( 'template-parts/product-section', null, [ 'n' => $n ] );
	}
}

/* Home ideas feed */
if ( dps_mod( 'dps_show_feed', true ) ) :
	$feed = new WP_Query( [ 'post_type' => 'dps_feed', 'no_found_rows' => true, 'update_post_term_cache' => false, 'posts_per_page' => 6, 'orderby' => 'menu_order date', 'order' => 'ASC' ] );
	if ( $feed->have_posts() ) : ?>
	<section class="section"><div class="wrap">
		<div class="section-head"><h2><?php echo esc_html( dps_mod( 'dps_feed_title', 'ไอเดียจัดบ้าน' ) ); ?></h2></div>
		<div class="feed">
		<?php while ( $feed->have_posts() ) : $feed->the_post();
			$pid = (int) get_post_meta( get_the_ID(), '_dps_product', true );
			$url = $pid ? get_permalink( $pid ) : '#';
			$h   = get_post_meta( get_the_ID(), '_dps_handle', true ); ?>
			<a class="feed-item" href="<?php echo esc_url( $url ); ?>">
				<?php the_post_thumbnail( 'large', [ 'loading' => 'lazy' ] ); ?>
				<div class="feed-meta"><?php if ( $h ) echo '<b>' . esc_html( $h ) . '</b>'; ?><?php if ( $pid ) echo esc_html( get_the_title( $pid ) ); ?></div>
			</a>
		<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div></section>
<?php endif; endif;

/* Reviews */
if ( dps_mod( 'dps_show_reviews', true ) ) :
	$rev = new WP_Query( [ 'post_type' => 'dps_review', 'no_found_rows' => true, 'update_post_term_cache' => false, 'posts_per_page' => 8, 'orderby' => 'menu_order date', 'order' => 'ASC' ] );
	if ( $rev->have_posts() ) : ?>
	<section class="section reviews"><div class="wrap">
		<div class="section-head"><h2><?php echo esc_html( dps_mod( 'dps_reviews_title', 'เสียงจากลูกค้า' ) ); ?></h2></div>
		<div class="review-track">
		<?php while ( $rev->have_posts() ) : $rev->the_post();
			$pid   = (int) get_post_meta( get_the_ID(), '_dps_product', true );
			$stars = max( 1, min( 5, (int) ( get_post_meta( get_the_ID(), '_dps_stars', true ) ? get_post_meta( get_the_ID(), '_dps_stars', true ) : 5 ) ) ); ?>
			<article class="review">
				<div class="review-who">
					<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'thumbnail' ); else echo '<span class="ph">' . esc_html( mb_substr( get_the_title(), 0, 1 ) ) . '</span>'; ?>
					<div><strong><?php the_title(); ?></strong><small>Verified Buyer</small></div>
				</div>
				<div class="stars" aria-label="<?php echo esc_attr( $stars ); ?> ดาว"><?php echo str_repeat( '★', $stars ) . str_repeat( '☆', 5 - $stars ); ?></div>
				<p><?php echo esc_html( wp_strip_all_tags( get_the_content() ) ); ?></p>
				<?php if ( $pid ) : ?><a class="review-prod" href="<?php echo esc_url( get_permalink( $pid ) ); ?>"><?php echo esc_html( get_the_title( $pid ) ); ?></a><?php endif; ?>
			</article>
		<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div></section>
<?php endif; endif;

/* FAQ */
if ( dps_mod( 'dps_show_faq', true ) ) :
	$faq = new WP_Query( [ 'post_type' => 'dps_faq', 'no_found_rows' => true, 'update_post_term_cache' => false, 'posts_per_page' => 12, 'orderby' => 'menu_order date', 'order' => 'ASC' ] );
	if ( $faq->have_posts() ) : ?>
	<section class="section"><div class="wrap">
		<div class="section-head" style="justify-content:center"><h2><?php echo esc_html( dps_mod( 'dps_faq_title', 'คำถามที่พบบ่อย' ) ); ?></h2></div>
		<div class="faq">
		<?php while ( $faq->have_posts() ) : $faq->the_post(); ?>
			<details><summary><?php the_title(); ?></summary><div class="ans"><?php the_content(); ?></div></details>
		<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div></section>
<?php endif; endif; ?>

</main>
<?php get_footer(); ?>
