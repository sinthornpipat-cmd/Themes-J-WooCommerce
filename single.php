<?php
/**
 * Single post template.
 *
 * @package themes-j-woocommerce
 */
?>
<?php get_header(); ?>
<main id="main" class="wrap content">
<?php while ( have_posts() ) : the_post(); ?>
	<article <?php post_class(); ?>>
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<p style="color:var(--muted)"><?php echo esc_html( get_the_date() ); ?></p>
		<?php if ( has_post_thumbnail() ) the_post_thumbnail( 'large' ); ?>
		<?php the_content(); ?>
		<?php comments_template(); ?>
	</article>
<?php endwhile; ?>
</main>
<?php get_footer(); ?>
