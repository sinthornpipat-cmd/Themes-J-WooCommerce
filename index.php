<?php
/**
 * Fallback template for blog/archive/search.
 *
 * @package petchsiam-shop
 */
?>
<?php get_header(); ?>
<main id="main" class="wrap content-wide">
	<?php if ( is_home() && ! is_front_page() || is_archive() || is_search() ) : ?>
		<h1 class="entry-title"><?php echo is_search() ? sprintf( 'ผลการค้นหา: %s', esc_html( get_search_query() ) ) : esc_html( get_the_archive_title() ? get_the_archive_title() : single_post_title( '', false ) ); ?></h1>
	<?php endif; ?>
	<?php if ( have_posts() ) : ?>
		<div class="post-grid">
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'post-card' ); ?>>
				<?php if ( has_post_thumbnail() ) : ?><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'large' ); ?></a><?php endif; ?>
				<div class="in">
					<h2 style="font-size:20px"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				</div>
			</article>
		<?php endwhile; ?>
		</div>
		<?php the_posts_pagination( [ 'mid_size' => 1 ] ); ?>
	<?php else : ?>
		<p>ไม่พบเนื้อหา</p>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
