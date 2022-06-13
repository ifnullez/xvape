<?php
$fields = $args['fields'];
$recent_args = new WP_Query([
    'post_type'         => 'product',
    'posts_per_page'    => '12',
    'orderby'           => 'date',
    'order'             => 'DESC'
]);
if($recent_args->have_posts()){ ?>
<div class="container overflow-hidden mt-3">
    <div class="newest-carousel d-flex flex-wrap justify-content-between align-items-center mb-2">
        <h5 class="newest-carousel__title"><?php echo $fields['newest_products_title']; ?></h5>
        <div class="newest-carousel__buttons">
            <button type="button" id="newest-prev" class="btn btn-outline-dark">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button type="button" id="newest-next" class="btn btn-outline-dark">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>
    <div class="row newest-carousel-block">
        <?php while($recent_args->have_posts()){
            $recent_args->the_post();
            get_template_part('/template-parts/woocommerce/product', 'card', $post);
        }
        wp_reset_postdata(); ?>
    </div>
</div>
<?php }