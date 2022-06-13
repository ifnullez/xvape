<div class="product_categories_grid container">
    <div class="row">
    <?php if(!empty($args['fields']['product_categories_grid'])){ ?>
        <?php foreach($args['fields']['product_categories_grid'] as $category){ 
            $category = get_term($category['id']); 
            if(!empty($category)){
            $thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true ); ?>
                <div class="category_block_item col-12 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                        <a href="<?php echo get_term_link($category); ?>" class="d-flex position-relative">
                            <?php echo wp_get_attachment_image($thumbnail_id, 'full'); ?>
                            <div class="btn btn-dark section_button"><i class="bi bi-caret-right"></i><span>
                                    <?php _e('More', 'xvape'); ?>
                                </span>
                            </div>
                        </a>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } ?>
    </div>
</div><!-- /.block -->