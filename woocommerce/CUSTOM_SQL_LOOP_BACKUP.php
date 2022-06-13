<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
//do_action( 'woocommerce_before_main_content' );
$helper = new App\Helper\Helper(); 
$wc_custom = new App\WC\WC_Custom(); 
$products = $wc_custom->get_products();
// filters sidebar
get_template_part('/template-parts/woocommerce/filters', 'sidebar'); ?>

<div class="breadcrumb_filter_wrapper d-flex flex-nowrap justify-content-between align-items-center">
    <?php $helper->breadcrumbs(); ?>
    <div class="filter_button_wrapper d-flex flex-nowrap justify-content-center align-items-center">
        <?php _e('Filter', 'woocommerce'); ?>
        <button type="button" id="archive_product_filter">
            <span></span>
        </button>
    </div>
</div>
<?php
if( is_product_category() ){
    $child_cats = get_term_children( get_queried_object()->term_id, 'product_cat' );
    if(!empty($child_cats)){
        foreach($child_cats as $cat){
            get_template_part( 'woocommerce/content-product', 'cat', get_term($cat, 'product_cat'));
        }
    }
}
if(!empty($products['products'])){
    foreach($products['products'] as $product){
        $full_product = wc_get_product($product->ID);
        setup_postdata($full_product);
        get_template_part( 'woocommerce/content', 'product', $full_product);
    } 
    $helper->pagination(NULL, true, [
        'current' => $products['paged'],
        'total' => $products['max_num_pages']    
    ]);
 } else if(empty($child_cats) && empty($products['products'])) { ?>
    <h5 class="text-center"><?php _e('No products were found matching your selection.', 'woocommerce'); ?></h5>
<?php }

// if ( woocommerce_product_loop() ) {

	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 * @hooked woocommerce_result_count - 20
	 * @hooked woocommerce_catalog_ordering - 30
	 */
	// do_action( 'woocommerce_before_shop_loop' );

	// woocommerce_product_loop_start();

	// if ( wc_get_loop_prop( 'total' ) ) {
	// 	while ( have_posts() ) {
	// 		the_post();

	// 		/**
	// 		 * Hook: woocommerce_shop_loop.
	// 		 */
	// 		do_action( 'woocommerce_shop_loop' );

	// 		wc_get_template_part( 'content', 'product' );
	// 	}
	// }

	// woocommerce_product_loop_end();

	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	// do_action( 'woocommerce_after_shop_loop' );
// } else {
	/**
	 * Hook: woocommerce_no_products_found.
	 *
	 * @hooked wc_no_products_found - 10
	 */
// 	do_action( 'woocommerce_no_products_found' );
// }

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
//do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
// do_action( 'woocommerce_sidebar' );

get_footer();