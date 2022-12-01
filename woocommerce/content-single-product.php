<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
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
$wc_custom = new App\WC\WC_Custom();
$helper = new App\Helper\Helper();
// $wc_custom->set_all_products_variable_type();
/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
// do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}

$product_properties = $helper->get_product_parameters_by_id($product->get_id()); 
$availability = $wc_custom->get_product_and_variation_availability($product); ?>
<div id="custom_product-<?php the_ID(); ?>">
    <?php
	/**
	 * Hook: woocommerce_before_single_product_summary.
	 *
	 * @hooked woocommerce_show_product_sale_flash - 10
	 * @hooked woocommerce_show_product_images - 20
	 */
	// do_action( 'woocommerce_before_single_product_summary' );
	?>

    <div class="custom_summary container my-3 p-0">
        <div class="row">
            <div class="custom_summary__product_attachments col-12 col-md-6 col-lg-6 col-xl-6 col-xxl-6 position-relative">
				<div class="stock">
					<?php if($availability){ ?>
						<p class="stock"><?php _e('In stock', 'woocommerce'); ?></p>
					<?php } else { ?>
						<p class="stock out-of-stock"><?php _e('Out of stock', 'woocommerce'); ?></p>
					<?php } ?>
				</div>
				<div class="attachments d-flex flex-wrap">
					<?php if(!empty($product->get_gallery_image_ids())){
						foreach( $product->get_gallery_image_ids() as $attachment_id ) { ?>
					<img src="<?php echo wp_get_attachment_url( $attachment_id ); ?>" data-fancybox="gallery" alt="product-image">
					<?php } 
					} else if(has_post_thumbnail($product->get_id())){
						echo wp_get_attachment_image(get_post_thumbnail_id($product->get_id()), 'full', false, [
							'class' => 'card-img-top',
							'data-fancybox' => 'true'
						]);
					} else { ?>
					<img src="<?php echo wc_placeholder_img_src($product->get_id()); ?>" data-fancybox
						alt="product-placeholder">
					<?php } ?>
				</div>
            </div>
            <div class="custom_summary__product_controls col-12 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                <h1 class="custom_summary__product_controls_title"><?php the_title(); ?></h1>
                <div class="custom_summary__product_controls_add">
					<span class="product-price <?php echo empty($product->get_price_html()) ? 'no-price' : ''; ?>">
						<?php echo $product->get_price_html(); ?>
					</span>
                    <div class="w-25 input-group">
                        <input type="number" name="quantity" id="product_quantity" class="form-control" min="1"
                            value="1">
                        <div class="input-group-append">
                            <button type="button"
								id="to_cart" 
								data-quantity="1"
                                data-product_id="<?php echo $product->get_id(); ?>"
                                data-product_sku="<?php echo $product->get_sku(); ?>"
                                class="btn btn-success  
								<?php echo $wc_custom->product_in_cart($product->get_id()) == true ? "added" : ""; ?> 
								<?php echo $product->is_in_stock() && !$product->is_type( 'variable' ) ? '' : 'disabled'; ?>">
                                <i class="bi bi-bag-plus"></i>
							</button>
                        </div>
                    </div>
                </div>
                <div class="custom_summary__product_meta">
                    <?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
                    <span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'woocommerce' ); ?> <span
                            class="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>
                    <?php endif; ?>

                    <?php echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>

                    <?php echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>
                </div>
				<?php if($product->is_type( 'variable' )){ ?>
					<section class="custom_summary__product_variations">
						<?php get_template_part('template-parts/woocommerce/single/variations', 'selector', $product); ?>
						<input type="hidden" name="variation_id" value="" />
					</section>
				<?php } ?>
				<?php if(!empty(get_the_content())){ ?>
					<div class="custom_summary__product_full_description pt-5">
						<div><?php the_content(); ?></div>
					</div>
				<?php } ?>
				<?php if(!empty($product_properties)){ ?>
					<table class="table properties_table">
						<?php foreach($product_properties as $property_key => $property_value){ ?>
							<tr class="custom_summary__product_properties__property">
								<td class="propery_name"><?php _e($property_key, 'xvape'); ?></td>
								<td class="property_parameters"><?php echo implode(', ', $property_value); ?></td>
							</tr>
						<?php } ?>
					</table>
				<?php } ?>
            </div>
        </div>
        <?php
		/**
		 * Hook: woocommerce_single_product_summary.
		 *
		 * @hooked woocommerce_template_single_title - 5
		 * @hooked woocommerce_template_single_rating - 10
		 * @hooked woocommerce_template_single_price - 10
		 * @hooked woocommerce_template_single_excerpt - 20
		 * @hooked woocommerce_template_single_add_to_cart - 30
		 * @hooked woocommerce_template_single_meta - 40
		 * @hooked woocommerce_template_single_sharing - 50
		 * @hooked WC_Structured_Data::generate_product_data() - 60
		 */
		//  do_action( 'woocommerce_single_product_summary' );
		?>
    </div>
	<?php comments_template( 'woocommerce/single-product-reviews' ); ?>
	<div class="d-flex flex-column">
		<h2><?php _e('Related products', 'woocommerce'); ?></h2>
		<div class="row">
			<?php 
			$related_query = new WP_Query([
				'post_type' 		=> 'product',
				'post_status' 		=> 'publish',
				'posts_per_page' 	=> 4,
				'orderby'        	=> 'rand',
				'order'          	=> 'desc',
				'meta_query'        => [
					[
						'key'     => '_stock_status',
						'value'   => 'instock',
						'compare' => '='
					]
				]
			]);

			if($related_query->have_posts()){
				while($related_query->have_posts()){
					$related_query->the_post();
					$full_product = wc_get_product($post->ID);
					get_template_part( 'woocommerce/content', 'product', $full_product);
				}
				wp_reset_postdata();
				wp_reset_query();
			} ?>
		</div>
	</div>
    <?php
	/**
	 * Hook: woocommerce_after_single_product_summary.
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 * @hooked woocommerce_upsell_display - 15
	 * @hooked woocommerce_output_related_products - 20
	 */
	//do_action( 'woocommerce_after_single_product_summary' );
	?>
</div>
<?php //do_action( 'woocommerce_after_single_product' ); ?>