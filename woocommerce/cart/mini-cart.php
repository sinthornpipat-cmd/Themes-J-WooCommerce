<?php
/**
 * Mini-cart (drawer) — overrides woocommerce/cart/mini-cart.php
 * Card layout, +/- quantity, per-line totals, coupon, estimated total.
 *
 * @package petchsiam-shop
 */
defined( 'ABSPATH' ) || exit;

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
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
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
					<?php echo $permalink ? '<a class="mc-thumb" href="' . esc_url( $permalink ) . '" tabindex="-1">' . $thumb . '</a>' : '<span class="mc-thumb">' . $thumb . '</span>'; // phpcs:ignore ?>
					<div class="mc-info">
						<?php echo $permalink ? '<a class="mc-name" href="' . esc_url( $permalink ) . '">' . wp_kses_post( $name ) . '</a>' : '<span class="mc-name">' . wp_kses_post( $name ) . '</span>'; // phpcs:ignore ?>
						<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore ?>
						<span class="mc-unit"><?php echo wp_kses_post( $unit_html ); ?></span>
						<?php if ( ! $single ) : ?>
						<div class="mc-qty" role="group" aria-label="จำนวน">
							<button type="button" class="mc-qty-btn" data-d="-1" aria-label="ลดจำนวน">−</button>
							<input type="number" class="mc-qty-input" value="<?php echo esc_attr( $qty ); ?>" min="1" <?php echo $max > 0 ? 'max="' . esc_attr( $max ) . '"' : ''; ?> step="1" inputmode="numeric" aria-label="จำนวนสินค้า">
							<button type="button" class="mc-qty-btn" data-d="1" aria-label="เพิ่มจำนวน">+</button>
						</div>
						<?php else : ?>
						<span class="mc-single">จำกัด 1 ชิ้น</span>
						<?php endif; ?>
					</div>
					<div class="mc-side">
						<a href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>" class="mc-remove" data-key="<?php echo esc_attr( $cart_item_key ); ?>" aria-label="<?php echo esc_attr( sprintf( 'ลบ %s ออกจากตะกร้า', wp_strip_all_tags( $name ) ) ); ?>">&times;</a>
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
					<span class="mc-chip"><?php echo esc_html( strtoupper( $code ) ); ?><button type="button" class="mc-chip-x" data-coupon="<?php echo esc_attr( $code ); ?>" aria-label="เอาคูปองออก">&times;</button></span>
				<?php endforeach; ?>
				<button type="button" class="mc-coupon-toggle" aria-expanded="false">มีคูปองส่วนลด? <span aria-hidden="true">›</span></button>
			</div>
			<form class="mc-coupon-form" hidden>
				<label class="screen-reader-text" for="mc-coupon">รหัสคูปอง</label>
				<input id="mc-coupon" type="text" placeholder="กรอกรหัสคูปอง" autocomplete="off">
				<button type="submit" class="btn">ใช้คูปอง</button>
			</form>
		<?php endif; ?>

		<dl class="mc-totals">
			<div class="mc-row"><dt>ยอดรวมสินค้า</dt><dd class="mc-sub-val"><?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></dd></div>
			<?php if ( $disc > 0 ) : ?>
				<div class="mc-row mc-disc"><dt>ส่วนลด</dt><dd>−<?php echo wp_kses_post( wc_price( $disc ) ); ?></dd></div>
			<?php endif; ?>
			<div class="mc-row mc-total"><dt>ยอดรวมโดยประมาณ</dt><dd class="mc-est-val"><?php echo wp_kses_post( wc_price( $est_raw ) ); ?></dd></div>
		</dl>
		<p class="mc-note">ค่าจัดส่งและภาษี (ถ้ามี) คำนวณในขั้นตอนชำระเงิน</p>

		<div class="mc-buttons">
			<a class="btn btn-ghost" href="<?php echo esc_url( wc_get_cart_url() ); ?>">ดูตะกร้า</a>
			<a class="btn btn-checkout" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">ชำระเงิน</a>
		</div>
	</div>

<?php else : ?>
	<div class="mc-empty">
		<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 8h14l-1 12H6L5 8Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/></svg>
		<p><?php esc_html_e( 'ตะกร้าของคุณยังว่างอยู่', 'petchsiam-shop' ); ?></p>
		<a class="btn" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">เลือกซื้อสินค้า</a>
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
