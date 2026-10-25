<?php
/**
 * Single page template.
 *
 * @package themes-j-woocommerce
 */

get_header();
?>
<main id="main" class="wrap <?php echo ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) ? 'content-wide' : 'content'; ?>">
<?php
while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class(); ?>>
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<?php the_content(); ?>
	</article>
<?php endwhile; ?>
</main>
<?php get_footer(); ?>
