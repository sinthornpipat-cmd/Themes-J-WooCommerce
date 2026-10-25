<?php
/**
 * WooCommerce wrapper template: shop, categories and single product.
 *
 * The <main> element is printed here (not via WooCommerce hooks) so that every
 * WooCommerce view gets the same centred container and side gutters.
 *
 * @package j-shop
 */

get_header();
?>
<main id="main" class="wrap content-wide">
	<?php woocommerce_content(); ?>
</main>
<?php
get_footer();
