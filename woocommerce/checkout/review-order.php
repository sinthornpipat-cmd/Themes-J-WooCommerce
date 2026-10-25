<?php
/**
 * Checkout order review (classic checkout) — Shopify-style summary.
 *
 * Overrides woocommerce/checkout/review-order.php. Shows a thumbnail with a
 * quantity badge for each line, an inline coupon field, then the totals.
 *
 * @package j-shop
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- WooCommerce template override: core hook names and template-scoped variables.
?>
<div class="woocommerce-checkout-review-order-table dps-co-summary">
	<ul class="dps-co-items">
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

			if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				continue;
			}
			?>
			<li class="dps-co-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
				<span class="dps-co-thumb">
					<?php echo wp_kses_post( $_product->get_image( 'woocommerce_gallery_thumbnail' ) ); ?>
					<span class="dps-co-qty"><?php echo esc_html( $cart_item['quantity'] ); ?></span>
				</span>
				<span class="dps-co-name">
					<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?>
					<?php echo wp_kses_post( wc_get_formatted_cart_item_data( $cart_item ) ); ?>
				</span>
				<span class="dps-co-price">
					<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ) ); ?>
				</span>
			</li>
			<?php
		}

		do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</ul>

	<?php if ( wc_coupons_enabled() ) : ?>
		<div class="dps-co-coupon">
			<input type="text" id="dps-co-coupon-code" class="input-text" placeholder="<?php esc_attr_e( 'Discount code', 'j-shop' ); ?>" autocomplete="off" />
			<button type="button" class="dps-co-coupon-btn"><?php esc_html_e( 'Apply', 'j-shop' ); ?></button>
		</div>
	<?php endif; ?>

	<table class="dps-co-totals">
		<tbody>
			<tr class="cart-subtotal">
				<th><?php esc_html_e( 'Subtotal', 'j-shop' ); ?></th>
				<td><?php wc_cart_totals_subtotal_html(); ?></td>
			</tr>

			<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
				<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
					<th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
					<td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
				</tr>
			<?php endforeach; ?>

			<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
				<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
				<?php wc_cart_totals_shipping_html(); ?>
				<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
			<?php endif; ?>

			<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
				<tr class="fee">
					<th><?php echo esc_html( $fee->name ); ?></th>
					<td><?php wc_cart_totals_fee_html( $fee ); ?></td>
				</tr>
			<?php endforeach; ?>

			<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
				<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
					<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax_total ) : ?>
						<tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
							<th><?php echo esc_html( $tax_total->label ); ?></th>
							<td><?php echo wp_kses_post( $tax_total->formatted_amount ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr class="tax-total">
						<th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
						<td><?php wc_cart_totals_taxes_total_html(); ?></td>
					</tr>
				<?php endif; ?>
			<?php endif; ?>

			<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

			<tr class="order-total">
				<th><?php esc_html_e( 'Total', 'j-shop' ); ?></th>
				<td><?php wc_cart_totals_order_total_html(); ?></td>
			</tr>

			<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
		</tbody>
	</table>
</div>
