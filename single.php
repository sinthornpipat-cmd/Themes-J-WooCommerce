<?php
/**
 * Single post template.
 *
 * @package j-shop
 */

get_header();
?>
<main id="main" class="wrap content">
<?php
while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class(); ?>>
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<p style="color:var(--muted)"><?php echo esc_html( get_the_date() ); ?></p>
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'large' );}
		?>
		<?php the_content(); ?>
		<?php
		wp_link_pages(
			array(
				'before' => '<nav class="page-links">' . esc_html__( 'Pages:', 'j-shop' ),
				'after'  => '</nav>',
			)
		);
		?>
		<?php the_tags( '<p class="tags-links">' . esc_html__( 'Tags:', 'j-shop' ) . ' ', ', ', '</p>' ); ?>
		<?php comments_template(); ?>
	</article>
<?php endwhile; ?>
</main>
<?php get_footer(); ?>
