<?php
/**
 * 404 template.
 *
 * @package petchsiam-shop
 */
?>
<?php get_header(); ?>
<main id="main" class="wrap content" style="text-align:center">
	<h1 class="entry-title">ไม่พบหน้าที่คุณต้องการ</h1>
	<p>ลองค้นหาสินค้า หรือกลับไปหน้าแรก</p>
	<a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">กลับหน้าแรก</a>
</main>
<?php get_footer(); ?>
