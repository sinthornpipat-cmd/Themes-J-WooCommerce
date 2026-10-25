<?php
/**
 * Fallback template for blog/archive/search.
 *
 * @package j-shop
 */

get_header();
?>
<main id="main" class="wrap content-wide">
	<?php if ( ( is_home() && ! is_front_page() ) || is_archive() || is_search() ) : ?>
		<h1 class="entry-title">
			<?php
			if ( is_search() ) {
				/* translators: %s: search query. */
				printf( esc_html__( 'ผลการค้นหา: %s', 'j-shop' ), esc_html( get_search_query() ) );
			} elseif ( is_archive() ) {
				the_archive_title();
			} else {
				single_post_title();
			}
			?>
		</h1>
	<?php endif; ?>
	<?php if ( have_posts() ) : ?>
		<div class="post-grid">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'post-card' ); ?>>
				<?php
				if ( has_post_thumbnail() ) :
					?>
					<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'large' ); ?></a><?php endif; ?>
				<div class="in">
					<h2 style="font-size:20px"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				</div>
			</article>
		<?php endwhile; ?>
		</div>
		<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
	<?php else : ?>
		<p>ไม่พบเนื้อหา</p>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
