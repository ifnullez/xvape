<?php 
$helper = new App\Helper\Helper();
$fields = $args['fields'];

$most = new WP_Query([
    'post_type'         => 'product',
    'posts_per_page'    => '12',
    'post__in'          => $helper->get_most_rated_products(),
    'orderby'           => 'post__in',
    'order'             => 'DESC',
    'meta_query'        => [
        [
            'key'     => '_stock_status',
            'value'   => 'instock',
            'compare' => '='
        ]
    ]
]);

if(!empty($helper->get_most_rated_products()) && $most->have_posts()){ ?>
<div class="container overflow-hidden mt-3">
    <div class="most-commented-carousel d-flex flex-wrap justify-content-between align-items-center mb-2">
        <h5 class="most-commented-carousel__title"><?php echo $fields['most_commented_section_title']; ?></h5>
        <div class="most-commented-carousel__buttons">
            <button type="button" id="most-commented-prev" class="btn btn-outline-dark">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button type="button" id="most-commented-next" class="btn btn-outline-dark">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>
    <div class="row most-commented-carousel-block">
        <?php while($most->have_posts()){
            $most->the_post();
            get_template_part('/template-parts/woocommerce/product', 'card', $post);
        }
        wp_reset_postdata(); ?>
    </div>
</div>
<?php }