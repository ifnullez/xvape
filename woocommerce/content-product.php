<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;
global $product;
// print_r($product->is_visible());
// $product = $args;
// Ensure visibility.
if ( empty( $product ) ) {
	return;
}

$wc_custom = new App\WC\WC_Custom();
$helper = new App\Helper\Helper();
?>
<div class="product col-12 col-md-6 col-lg-4 col-xl-3 col-xxl-3 mb-4">
    <div class="card position-relative shadow-sm">
        <button type="button" class="btn btn-dark whish-product-button">
            <i class="bi bi-bookmark-star-fill"></i>
            <!-- <div class="whish-product-button__description visually-hidden"><?php //_e("Add to whish List", 'woocommerce'); ?></div> -->
        </button>
        <div class="product-rating">
            <?php echo $wc_custom->get_product_star_rating($product->get_average_rating()); ?>
        </div>
        <a class="card_img_wrapper" href="<?php echo get_the_permalink($product->get_id()); ?>">
            <?php if(has_post_thumbnail($product->get_id())){ ?>
            <?php echo wp_get_attachment_image(get_post_thumbnail_id($product->get_id()), 'woocommerce_thumbnail', false, [
					'class' => 'card-img-top'
				]); ?>
            <?php } else { ?>
            <img src="<?php echo wc_placeholder_img_src($product->get_id()); ?>" alt="product-placeholder"
                class="card-img-top">
            <?php } ?>
        </a>
        <div class="card-body">
            <a href="<?php echo get_the_permalink($product->get_id()); ?>">
                <h5 class="card-title"><?php the_title(); ?></h5>
            </a>
        </div>
        <div class="card-footer justify-content-between d-flex align-items-center">
            <?php if(!$product->is_in_stock()){ ?>
            <?php echo wc_get_stock_html($product); ?>
            <?php } else { ?>
            <span class="product-price">
                <?php if(!empty($product->get_price_html())){ ?>
                <?php echo $product->get_price_html(); ?>
                <?php } else { ?>
                <a href="<?php echo $product->get_permalink(); ?>" class="btn btn-outline-success">
                    <i class="bi bi-collection"></i>
                </a>
                <?php } ?>
            </span>
            <?php } ?>
            <?php if($product->is_in_stock() && !$product->is_type( 'variable' )){ ?>
            <button type="button"
                id="to_cart" 
                data-quantity="1"
                data-product_id="<?php echo $product->get_id(); ?>"
                data-product_sku="<?php echo $product->get_sku(); ?>"
                class="btn btn-success 
                <?php echo $product->is_in_stock() && !$product->is_type( 'variable' ) ? '' : 'disabled'; ?>">
                <?php _e('Купить', 'woocommerce'); ?>
                    <i class="bi bi-bag-plus"></i>
            </button>
            <?php } else { ?>
                <a href="<?php echo $product->get_permalink(); ?>" class="btn btn-success">
                    <?php _e('Купить', 'woocommerce'); ?>
                    <i class="bi bi-bag-plus"></i>
                </a>
            <?php } ?>
        </div>
    </div>
</div>