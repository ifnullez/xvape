<?php 
$product = wc_get_product($args->ID);
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$wc_custom = new App\WC\WC_Custom();
$helper = new App\Helper\Helper();
?>
<div class="product product-slider col-12 col-md-6 col-lg-4 col-xl-3 col-xxl-3">
    <div class="card position-relative shadow-sm">
        <button type="button" class="btn btn-dark whish-product-button">
            <i class="bi bi-bookmark-star-fill"></i>
            <!-- <div class="whish-product-button__description visually-hidden"><?php //_e("Add to whish List", 'woocommerce'); ?></div> -->
        </button>
        <div class="product-rating">
            <?php echo $wc_custom->get_product_star_rating($product->get_average_rating()); ?>
        </div>
        <a href="<?php echo get_the_permalink($product->get_id()); ?>">
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
            <button type="button"
                id="to_cart" 
                data-quantity="1"
                data-product_id="<?php echo $product->get_id(); ?>"
                data-product_sku="<?php echo $product->get_sku(); ?>"
                class="btn btn-success 
                <?php echo $product->is_in_stock() ? '' : 'disabled'; ?>">
                <?php _e('Купить', 'woocommerce'); ?>
                    <i class="bi bi-bag-plus"></i>
            </button>
        </div>
    </div>
</div>