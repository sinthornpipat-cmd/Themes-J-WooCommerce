<?php
/**
 * Mini-cart (drawer) — overrides woocommerce/cart/mini-cart.php
 * Card layout, +/- quantity, per-line totals, coupon, estimated total.
 *
 * @package j-shop
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- WooCommerce template override: core hook names and template-scoped variables.

do_action( 'woocommerce_before_mini_cart' );

if ( ! WC()->cart->is_empty() ) :
	$excl    = 'excl' === get_option( 'woocommerce_tax_display_cart' );
	$sub_raw = (float) WC()->cart->get_displayed_subtotal();
	$est_raw = (float) WC()->cart->get_cart_contents_total() + ( $excl ? 0 : (float) WC()->cart->get_cart_contents_tax() );
	$disc    = (float) WC()->cart->get_discount_total() + ( $excl ? 0 : (float) WC()->cart->get_discount_tax() );
	?>
	<div class="mc-body">
		<ul class="woocommerce-mini-cart mc-list">
			<?php
			do_action( 'woocommerce_before_mini_cart_contents' );

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					continue;
				}
				$name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
				$thumb     = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
				$permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				$qty       = (int) $cart_item['quantity'];
				$max       = (int) $_product->get_max_purchase_quantity(); // -1 = unlimited
				$unit_raw  = (float) wc_get_price_to_display( $_product );
				$unit_html = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				$line_html = apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $qty ), $cart_item, $cart_item_key );
				$single    = ( 1 === $max );
				?>
				<li class="mc-item <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>"
					data-key="<?php echo esc_attr( $cart_item_key ); ?>" data-unit="<?php echo esc_attr( $unit_raw ); ?>">
					<?php if ( $permalink ) : ?>
						<a class="mc-thumb" href="<?php echo esc_url( $permalink ); ?>" tabindex="-1"><?php echo wp_kses_post( $thumb ); ?></a>
					<?php else : ?>
						<span class="mc-thumb"><?php echo wp_kses_post( $thumb ); ?></span>
					<?php endif; ?>
					<div class="mc-info">
						<?php if ( $permalink ) : ?>
							<a class="mc-name" href="<?php echo esc_url( $permalink ); ?>"><?php echo wp_kses_post( $name ); ?></a>
						<?php else : ?>
							<span class="mc-name"><?php echo wp_kses_post( $name ); ?></span>
						<?php endif; ?>
						<?php echo wp_kses_post( wc_get_formatted_cart_item_data( $cart_item ) ); ?>
						<span class="mc-unit"><?php echo wp_kses_post( $unit_html ); ?></span>
						<?php if ( ! $single ) : ?>
						<div class="mc-qty" role="group" aria-label="<?php esc_attr_e( 'Quantity', 'j-shop' ); ?>">
							<button type="button" class="mc-qty-btn" data-d="-1" aria-label="<?php esc_attr_e( 'Decrease quantity', 'j-shop' ); ?>">−</button>
							<input type="number" class="mc-qty-input" value="<?php echo esc_attr( $qty ); ?>" min="1" <?php echo $max > 0 ? 'max="' . esc_attr( $max ) . '"' : ''; ?> step="1" inputmode="numeric" aria-label="<?php esc_attr_e( 'Product quantity', 'j-shop' ); ?>">
							<button type="button" class="mc-qty-btn" data-d="1" aria-label="<?php esc_attr_e( 'Increase quantity', 'j-shop' ); ?>">+</button>
						</div>
						<?php else : ?>
						<span class="mc-single"><?php esc_html_e( 'Limit 1 per order', 'j-shop' ); ?></span>
						<?php endif; ?>
					</div>
					<div class="mc-side">
						<a href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>" class="mc-remove" data-key="<?php echo esc_attr( $cart_item_key ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: product name. */ __( 'Remove %s from cart', 'j-shop' ), wp_strip_all_tags( $name ) ) ); ?>">&times;</a>
						<span class="mc-line"><?php echo wp_kses_post( $line_html ); ?></span>
					</div>
				</li>
				<?php
			}

			do_action( 'woocommerce_mini_cart_contents' );
			?>
		</ul>
	</div>

	<div class="mc-foot" data-sub="<?php echo esc_attr( $sub_raw ); ?>" data-est="<?php echo esc_attr( $est_raw ); ?>">
		<?php if ( wc_coupons_enabled() ) : ?>
			<div class="mc-coupons">
				<?php foreach ( WC()->cart->get_applied_coupons() as $code ) : ?>
					<span class="mc-chip"><?php echo esc_html( strtoupper( $code ) ); ?><button type="button" class="mc-chip-x" data-coupon="<?php echo esc_attr( $code ); ?>" aria-label="<?php esc_attr_e( 'Remove coupon', 'j-shop' ); ?>">&times;</button></span>
				<?php endforeach; ?>
				<button type="button" class="mc-coupon-toggle" aria-expanded="false"><?php esc_html_e( 'Have a coupon?', 'j-shop' ); ?> <span aria-hidden="true">›</span></button>
			</div>
			<form class="mc-coupon-form" hidden>
				<label class="screen-reader-text" for="mc-coupon"><?php esc_html_e( 'Coupon code', 'j-shop' ); ?></label>
				<input id="mc-coupon" type="text" placeholder="<?php esc_attr_e( 'Enter coupon code', 'j-shop' ); ?>" autocomplete="off">
				<button type="submit" class="btn"><?php esc_html_e( 'Apply', 'j-shop' ); ?></button>
			</form>
		<?php endif; ?>

		<dl class="mc-totals">
			<div class="mc-row"><dt><?php esc_html_e( 'Subtotal', 'j-shop' ); ?></dt><dd class="mc-sub-val"><?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></dd></div>
			<?php if ( $disc > 0 ) : ?>
				<div class="mc-row mc-disc"><dt><?php esc_html_e( 'Discount', 'j-shop' ); ?></dt><dd>−<?php echo wp_kses_post( wc_price( $disc ) ); ?></dd></div>
			<?php endif; ?>
			<div class="mc-row mc-total"><dt><?php esc_html_e( 'Estimated total', 'j-shop' ); ?></dt><dd class="mc-est-val"><?php echo wp_kses_post( wc_price( $est_raw ) ); ?></dd></div>
		</dl>
		<p class="mc-note"><?php esc_html_e( 'Shipping and taxes (if any) are calculated at checkout.', 'j-shop' ); ?></p>

		<div class="mc-buttons">
			<a class="btn btn-ghost" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'View cart', 'j-shop' ); ?></a>
			<a class="btn btn-checkout" href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><?php esc_html_e( 'Checkout', 'j-shop' ); ?></a>
		</div>
	</div>

<?php else : ?>
	<div class="mc-empty">
		<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 8h14l-1 12H6L5 8Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/></svg>
		<p><?php esc_html_e( 'Your cart is empty.', 'j-shop' ); ?></p>
		<a class="btn" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Start shopping', 'j-shop' ); ?></a>
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
