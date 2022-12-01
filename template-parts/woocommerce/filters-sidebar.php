<?php
$helper = new App\Helper\Helper(); 
$terms = get_terms([
	'taxonomy' => 'product_cat',
	'hide_empty' => true,
	'orderby' => 'name',
	'order' => 'ASC'
]);

$unique_props = $helper->get_products_parameters();
$min_max_prices = $helper->get_products_prices(); ?>
<div id="menu_filter" class="offcanvas offcanvas-start">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="menu_filter_label"><?php _e('Filters', 'woocommerce'); ?></h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="filter-form" method="get" action="<?php echo get_post_type_archive_link('product'); ?>" class="position-relative">
            <?php if(!empty($_GET['s'])){ ?>
                <input type="hidden" name="s" value="<?php echo $_GET['s']; ?>" />
            <?php } ?>
            <?php if(is_product_category() && empty($_GET['p_cat'])){ ?>
                <input type="hidden" name="p_cat[]" value="<?php echo get_queried_object()->term_id; ?>" />
            <?php } ?>
            <input type="hidden" name="post_type" value="product" />
            <!-- <div class="form-check form-switch mt-2 mb-2 in_stock">
                <input class="form-check-input" type="checkbox" role="switch" name="in_stock" id="in_stock"
                    <?php //echo !empty($_GET['in_stock']) && $_GET['in_stock'] == 'on' ? 'checked' : ''; ?>>
                <label class="form-check-label" for="in_stock"><?php _e('In stock', 'woocommerce'); ?></label>
            </div> -->
            <div class="selected_prices d-flex flex-nowrap justify-content-between">
                <span class="min"><?php echo !empty($_GET['mn_p']) ? $_GET['mn_p'] : $min_max_prices['min_price'] ?? 0; ?></span>
                <span class="max"><?php echo !empty($_GET['mx_p']) ? $_GET['mx_p'] : $min_max_prices['max_price'] ?? 1000; ?></span>
            </div>
            <div id="price_filter_slider">
            <input type="hidden" id="min_price" name="mn_p"
                        value="<?php echo !empty($_GET['mn_p']) ? $_GET['mn_p'] : $min_max_prices['min_price']; ?>"
                        placeholder="<?php echo esc_attr__( 'От', 'woocommerce' ); ?>" />
            <input type="hidden" id="max_price" name="mx_p"
            value="<?php echo !empty($_GET['mx_p']) ? $_GET['mx_p'] : $min_max_prices['max_price']; ?>"
            placeholder="<?php echo esc_attr__( 'До', 'woocommerce' ); ?>" />
            </div>

            <div class="accordion_wrapper accordion accordion-flush" id="accordionFlush">
                <?php if(!empty($terms)){ ?>
                <div class="accordion-item category-filter">
                    <h2 class="accordion-header" id="cat-heading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#cat-collapse" aria-expanded="false" aria-controls="cat-collapse">
                            <?php _e('Categories', 'woocommerce'); ?>
                        </button>
                    </h2>
                    <div id="cat-collapse" class="accordion-collapse collapse" aria-labelledby="cat-heading"
                        data-bs-parent="#accordionFlush">
                        <div class="accordion-body">
                            <?php foreach($terms as $term){ ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="p_cat[]"
                                    value="<?php echo $term->term_id; ?>" id="<?php echo $term->slug; ?>"
                                    <?php echo !empty($_GET['p_cat']) && in_array($term->term_id, $_GET['p_cat']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="<?php echo $term->slug; ?>">
                                    <?php echo $term->name; ?>
                                </label>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <?php if(!empty($unique_props)){ 
                    $atts_counter = 0;
                    foreach($unique_props as $atts_key => $attrs){ ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="f<?php echo $atts_counter; ?>-heading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#f<?php echo $atts_counter; ?>-collapse" aria-expanded="false"
                                aria-controls="f<?php echo $atts_counter; ?>-collapse">
                                <?php _e($atts_key, 'woocommerce'); ?>
                            </button>
                        </h2>
                        <div id="f<?php echo $atts_counter; ?>-collapse" class="accordion-collapse collapse"
                            aria-labelledby="f<?php echo $atts_counter; ?>-heading" data-bs-parent="#accordionFlush">
                            <div class="accordion-body">
                                <?php if(!empty($attrs)){ 
                                    foreach($attrs as $attr){ ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="at[]"
                                        value="<?php echo $attr .'-'. $atts_key; ?>" id="<?php echo urlencode($atts_key) . urlencode($attr); ?>"
                                        <?php echo !empty($_GET['at']) && in_array($attr .'-'. $atts_key, $_GET['at']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="<?php echo urlencode($atts_key) . urlencode($attr); ?>">
                                        <?php echo $attr; ?>
                                    </label>
                                </div>
                                <?php }
                            } ?>
                            </div>
                        </div>
                    </div>
                    <?php 
                    $atts_counter++;
                } ?>
                    <?php } ?>
            </div>
            <div class="filter_actions d-flex align-items-center justify-content-between flex-nowrap">
                <button type="button" id="clear_filters"
                    class="btn btn-danger position-relative"><?php _e('Очистить', 'woocommerce'); ?></button>
                <button type="submit" id="submit_filters"
                    class="btn btn-success position-relative"><?php _e('Применить', 'woocommerce'); ?></button>
            </div>
        </form>
        </div>
</div>
