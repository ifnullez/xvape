<?php
/**
 * The template for displaying product category thumbnails within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product-cat.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// $category = $args;
?>
<div class="product_category col-12 col-md-6 col-lg-4 col-xl-3 col-xxl-3 mb-4 first">
	<div class="card position-relative shadow-sm">
		<a href="<?php echo get_term_link($category->term_id); ?>">
            <?php 
			if(!empty(get_term_meta( $category->term_id, 'thumbnail_id', true ))){ ?>
            <?php echo wp_get_attachment_image(get_term_meta( $category->term_id, 'thumbnail_id', true ), 'woocommerce_thumbnail', false, [
					'class' => 'card-img-top'
				]); ?>
            <?php } else { ?>
            <img src="<?php echo wc_placeholder_img_src($category->term_id); ?>" alt="product-placeholder"
                class="card-img-top">
            <?php } ?>
        </a>
		<div class="card-body">
			<a href="<?php echo get_term_link($category->term_id); ?>">
				<h5 class="card-title">
					<?php echo $category->name; ?>
				</h5>
			</a>
		</div>
		<div class="card-footer d-flex justify-content-between align-items-center">
			<span class="in_category d-flex justify-content-center align-items-center flex-nowrap">
				<i class="bi bi-list-ol me-1"></i>
				<span class="count badge rounded-pill bg-dark">
					<?php echo $category->count; ?>
				</span>
			</span>
			<a href="<?php echo get_term_link($category->term_id); ?>" class="btn btn-success">
				<i class="bi bi-collection"></i>
			</a>
		</div>
	<?php
	/**
	 * The woocommerce_before_subcategory hook.
	 *
	 * @hooked woocommerce_template_loop_category_link_open - 10
	 */
	//do_action( 'woocommerce_before_subcategory', $category );

	/**
	 * The woocommerce_before_subcategory_title hook.
	 *
	 * @hooked woocommerce_subcategory_thumbnail - 10
	 */
	//do_action( 'woocommerce_before_subcategory_title', $category );

	/**
	 * The woocommerce_shop_loop_subcategory_title hook.
	 *
	 * @hooked woocommerce_template_loop_category_title - 10
	 */
	//do_action( 'woocommerce_shop_loop_subcategory_title', $category );

	/**
	 * The woocommerce_after_subcategory_title hook.
	 */
	//do_action( 'woocommerce_after_subcategory_title', $category );

	/**
	 * The woocommerce_after_subcategory hook.
	 *
	 * @hooked woocommerce_template_loop_category_link_close - 10
	 */
	//do_action( 'woocommerce_after_subcategory', $category );
	?>
	</div>
</div>
