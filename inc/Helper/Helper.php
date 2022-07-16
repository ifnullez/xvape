<?php
namespace App\Helper;

use WP_Query;

class Helper
{
    public $dist_img_uri;
    private $wpdb;
    private $prefix;
    protected $wc_custom;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->prefix = $this->wpdb->prefix;
        $this->dist_img_uri = get_stylesheet_directory_uri() . '/assets/dist/img';
    }

    public function get_category_with_id(int $id)
    {
        $category = $this->wpdb->get_results($this->wpdb->prepare("SELECT {$this->prefix}term_taxonomy.term_id, {$this->prefix}terms.name
        FROM {$this->prefix}term_relationships
        JOIN {$this->prefix}term_taxonomy ON ({$this->prefix}term_relationships.term_taxonomy_id = {$this->prefix}term_taxonomy.term_taxonomy_id)
        JOIN {$this->prefix}terms ON ({$this->prefix}terms.term_id = {$this->prefix}term_taxonomy.term_taxonomy_id)
        WHERE {$this->prefix}term_taxonomy.taxonomy = 'product_cat'
        AND {$this->prefix}term_taxonomy.term_taxonomy_id = %d
        GROUP BY {$this->prefix}term_taxonomy.term_id
        order by {$this->prefix}terms.name", $id));
        return $category;
    }

    public function breadcrumbs()
    {

        // Check if is front/home page, return
        if (is_front_page()) {
            return;
        }

        // Define
        global $post;
        $custom_taxonomy = 'product_cat'; // If you have custom taxonomy place it here

        $defaults = array(
            'seperator' => '<i class="bi bi-arrow-right-short"></i>',
            'id' => 'breadcrumbs',
            'classes' => 'breadcrumb',
            'home_title' => esc_html__('Home', ''),
        );

        $sep = '<li class="seperator">&nbsp;' . $defaults['seperator'] . '&nbsp;</li>';

        // Start the breadcrumb with a link to your homepage
        echo '<nav aria-label="breadcrumb"><ol id="' . esc_attr($defaults['id']) . '" class="' . esc_attr($defaults['classes']) . '">';

        // Creating home link
        echo '<li class="breadcrumb-item"><a href="' . get_home_url() . '"><i class="bi bi-house-heart-fill"></i></a></li>' . $sep;

        if (is_single()) {

            // Get posts type
            $post_type = get_post_type();

            // If post type is not post
            if ($post_type != 'post') {

                $post_type_object = get_post_type_object($post_type);
                $post_type_link = get_post_type_archive_link($post_type);

                echo '<li class="breadcrumb-item"><a href="' . $post_type_link . '">' . $post_type_object->labels->name . '</a></li>' . $sep;

            }

            // Get categories
            $category = get_the_category($post->ID);

            // If category not empty
            if (!empty($category)) {

                // Arrange category parent to child
                $category_values = array_values($category);
                $get_last_category = end($category_values);
                // $get_last_category    = $category[count($category) - 1];
                $get_parent_category = rtrim(get_category_parents($get_last_category->term_id, true, ','), ',');
                $cat_parent = explode(',', $get_parent_category);

                // Store category in $display_category
                $display_category = '';
                foreach ($cat_parent as $p) {
                    $display_category .= '<li class="breadcrumb-item">' . $p . '</li>' . $sep;
                }

            }

            // If it's a custom post type within a custom taxonomy
            $taxonomy_exists = taxonomy_exists($custom_taxonomy);

            if (empty($get_last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {

                $taxonomy_terms = get_the_terms($post->ID, $custom_taxonomy);
                $cat_id = $taxonomy_terms[0]->term_id;
                $cat_link = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
                $cat_name = $taxonomy_terms[0]->name;

            }

            // Check if the post is in a category
            if (!empty($get_last_category)) {
                echo $display_category;
                echo '<li class="breadcrumb-item">' . get_the_title() . '</li>';
            } else if (!empty($cat_id)) {
                echo '<li class="breadcrumb-item"><a href="' . $cat_link . '">' . $cat_name . '</a></li>' . $sep;
                echo '<li class="breadcrumb-item">' . get_the_title() . '</li>';
            } else {
                echo '<li class="breadcrumb-item">' . get_the_title() . '</li>';
            }

        } else if (is_archive()) {
            if (is_tax()) {
                // Get posts type
                $post_type = get_post_type();

                // If post type is not post
                if ($post_type != 'post') {

                    $post_type_object = get_post_type_object($post_type);
                    $post_type_link = get_post_type_archive_link($post_type);

                    echo '<li class="breadcrumb-item"><a href="' . $post_type_link . '">' . $post_type_object->labels->name . '</a></li>' . $sep;
                }

                $custom_tax_name = get_queried_object()->name;
                echo '<li class="breadcrumb-item">' . $custom_tax_name . '</li>';

            } else if (is_category()) {
                $parent = !empty(get_queried_object()->category_parent) ? get_queried_object()->category_parent : 0;

                if ($parent !== 0) {
                    $parent_category = get_category($parent);
                    $category_link = get_category_link($parent);

                    echo '<li class="breadcrumb-item"><a href="' . esc_url($category_link) . '">' . $parent_category->name . '</a></li>' . $sep;

                } else if (!empty($_GET['cat'])) {
                    $filter_terms = get_terms([
                        'taxonomy' => 'product_cat',
                        'include' => $_GET['p_cat'],
                    ]);
                    if (!empty($filter_terms)) {
                        foreach ($filter_terms as $selected_term) {
                            echo '<li class="breadcrumb-item"><a href="' . esc_url(get_category_link($selected_term)) . '">' . $selected_term->name . '</a></li>';
                        }
                    }
                }

                echo '<li class="breadcrumb-item">' . single_cat_title('', false) . '</li>';

            } else if (is_tag()) {

                // Get tag information
                $term_id = get_query_var('tag_id');
                $taxonomy = 'post_tag';
                $args = 'include=' . $term_id;
                $terms = get_terms($taxonomy, $args);
                $get_term_name = $terms[0]->name;

                // Display the tag name
                echo '<li class="breadcrumb-item">' . $get_term_name . '</li>';

            } else if (is_day()) {

                // Day archive

                // Year link
                echo '<li class="breadcrumb-item"><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . ' Archives</a></li>' . $sep;

                // Month link
                echo '<li class="breadcrumb-item"><a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('M') . ' Archives</a></li>' . $sep;

                // Day display
                echo '<li class="breadcrumb-item">' . get_the_time('jS') . ' ' . get_the_time('M') . ' Archives</li>';

            } else if (is_month()) {

                // Month archive

                // Year link
                echo '<li class="breadcrumb-item"><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . ' Archives</a></li>' . $sep;

                // Month Display
                echo '<li class="breadcrumb-item">' . get_the_time('M') . ' Archives</li>';

            } else if (is_year()) {

                // Year Display
                echo '<li class="breadcrumb-item">' . get_the_time('Y') . ' Archives</li>';

            } else if (is_author()) {

                // Auhor archive

                // Get the author information
                global $author;
                $userdata = get_userdata($author);

                // Display author name
                echo '<li class="breadcrumb-item">' . 'Author: ' . $userdata->display_name . '</li>';

            } else {

                echo '<li class="breadcrumb-item">' . post_type_archive_title() . '</li>';

            }

        } else if (is_page()) {

            // Standard page
            if ($post->post_parent) {

                // If child page, get parents
                $anc = get_post_ancestors($post->ID);

                // Get parents in the right order
                $anc = array_reverse($anc);

                // Parent page loop
                if (!isset($parents)) {
                    $parents = null;
                }

                foreach ($anc as $ancestor) {

                    $parents .= '<li class="breadcrumb-item"><a href="' . get_permalink($ancestor) . '">' . get_the_title($ancestor) . '</a></li>' . $sep;

                }

                // Display parent pages
                echo $parents;

                // Current page
                echo '<li class="breadcrumb-item">' . get_the_title() . '</li>';

            } else {

                // Just display current page if not parents
                echo '<li class="breadcrumb-item">' . get_the_title() . '</li>';

            }

        } else if (is_search()) {

            // Search results page
            echo '<li class="breadcrumb-item">Search results for: ' . get_search_query() . '</li>';

        } else if (is_404()) {

            // 404 page
            echo '<li class="breadcrumb-item">' . 'Error 404' . '</li>';

        }

        // End breadcrumb
        echo '</ol></nav>';

    }

    public function get_most_rated_products()
    {

        $results = $this->wpdb->get_results("SELECT DISTINCT({$this->prefix}comments.comment_post_ID),
      GROUP_CONCAT({$this->prefix}comments.comment_ID separator ', ')
      comment_ids FROM {$this->prefix}comments
      JOIN {$this->prefix}commentmeta ON {$this->prefix}commentmeta.comment_id = {$this->prefix}comments.comment_ID
      JOIN {$this->prefix}posts ON {$this->prefix}posts.ID = {$this->prefix}comments.comment_post_ID
      WHERE {$this->prefix}posts.post_type = 'product'
      GROUP BY {$this->prefix}comments.comment_post_ID", ARRAY_A);

        if (!empty($results)) {
            foreach ($results as $key => $value) {
                $c_post_id = $value['comment_post_ID'];
                $comment_ids = $value['comment_ids'];
                $res = $this->wpdb->get_results("SELECT AVG(`meta_value`) as avg_rate
          FROM {$this->prefix}commentmeta
          WHERE `meta_key` = 'rating'
          AND comment_ID IN ($comment_ids)
          ORDER BY meta_value");
                $results[$key]['avg_rate'] = $res[0]->avg_rate;
            }

            $avg_rate = array_column($results, 'avg_rate');
            array_multisort($avg_rate, SORT_DESC, $results);
            $top_rated = [];

            foreach ($results as $result) {
                if ($result['avg_rate'] && $result['comment_ids']) {
                    $top_rated[] = $result['comment_post_ID'];
                }
            }
            return $top_rated;
        }
        return [];
    }

    public function get_products_attributes()
    {
        $a_r = [];
        $result = [];
        $attrs = $this->wpdb->get_results("SELECT DISTINCT pm.meta_value AS attributes
      FROM {$this->prefix}postmeta AS pm
      WHERE pm.meta_key = '_product_attributes'");
        if (!empty($attrs)) {
            foreach ($attrs as $attr) {
                if (!empty($attr->attributes)) {
                    foreach (unserialize($attr->attributes) as $a_key => $a_val) {
                        if (!empty($a_val['value']) && $a_val['is_visible'] == 1) {
                            $a_r[$a_val['name']][] = explode('|', $a_val['value']);
                        }
                    }
                }
            }
        }
        if (!empty($a_r)) {
            foreach ($a_r as $a_k => $v) {
                $result[$a_k] = array_unique(array_merge(...$a_r[$a_k]));
                rsort($result[$a_k]);
            }
        }
        return $result;
    }
    public function get_products_prices()
    {
        $prices_array = [];
        $sql_price = $this->wpdb->get_results("SELECT DISTINCT pm.meta_value AS price
      FROM {$this->prefix}postmeta AS pm
      WHERE pm.meta_key = '_price' ORDER BY pm.meta_value");
        if (!empty($sql_price) && $sql_price != null) {
            foreach ($sql_price as $price) {
                $prices_array[] = $price->price;
            }
            return [
                'min_price' => min($prices_array),
                'max_price' => max($prices_array),
            ];
        }
	return [
        	'min_price' => 0,
                'max_price' => 1000,
        ];
    }

    public function pagination(\WP_Query$wp_query = null, $echo = true, $params = [])
    {
        if (null === $wp_query) {
            global $wp_query;
        }

        $add_args = [];

        //add query (GET) parameters to generated page URLs
        /*if (isset($_GET[ 'sort' ])) {
        $add_args[ 'sort' ] = (string)$_GET[ 'sort' ];
        }*/

        $pages = paginate_links(array_merge([
            'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $wp_query->max_num_pages,
            'type' => 'array',
            'show_all' => false,
            'end_size' => 3,
            'mid_size' => 1,
            'prev_next' => true,
            'prev_text' => __('« Prev', 'woocommerce'),
            'next_text' => __('Next »', 'woocommerce'),
            'add_args' => $add_args,
            'add_fragment' => '',
        ], $params)
        );

        if (is_array($pages)) {
            //$current_page = ( get_query_var( 'paged' ) == 0 ) ? 1 : get_query_var( 'paged' );
            $pagination = '<nav class="d-flex justify-content-center mt-3" aria-label="Page navigation"><ul class="pagination mb-0">';

            foreach ($pages as $page) {
                $pagination .= '<li class="page-item' . (strpos($page, 'current') !== false ? ' active' : '') . '"> ' . str_replace('page-numbers', 'page-link', $page) . '</li>';
            }

            $pagination .= '</ul></nav>';

            if ($echo) {
                echo $pagination;
            } else {
                return $pagination;
            }
        }

        return null;
    }

    public function format_product_properties(array $properties): array {
        $merged_props = [];
        $unique_props = [];
        
        if(!empty($properties)){
            foreach(array_merge(...$properties) as $property){
                if(!empty($property['parameter_properties'])){
                    foreach($property['parameter_properties'] as $prop_value){
                        $merged_props[$property['parameter_name']][] = $prop_value['property_name'];
                    }
                }
            }
        }

        if(!empty($merged_props)){
            foreach($merged_props as $nu_prop_key => $nu_prop){
                asort($nu_prop);
                $unique_props[$nu_prop_key] = array_unique($nu_prop);

            }
        }
        return $unique_props;
    }

    public function get_products_parameters(){
        $avail_properties = [];

        $properties_args = [
            'post_type'     => 'product',
            'post_status'   => 'publish',
            'posts_per_page'   => '-1'
        ];

        if(is_product_category() && empty($_GET['p_cat'])){
            $properties_args['tax_query'] = [
                'relation' => 'AND',
                [
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => [get_queried_object_id()],
                    'operator' => 'IN'
                ]
            ];
        } else if(!empty($_GET['p_cat'])) {
            $properties_args['tax_query'] = [
                'relation' => 'AND',
                [
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $_GET['p_cat'],
                    'operator' => 'IN'
                ]
            ];
        }

        $properties_query = new WP_Query($properties_args);

        if($properties_query->have_posts()){
            while($properties_query->have_posts()){
                $properties_query->the_post();
                $avail_properties[] = carbon_get_post_meta(get_the_ID(), 'product_parameters');
            }
            wp_reset_postdata();
            wp_reset_query();
        }

        
        return $this->format_product_properties($avail_properties);
    }
    public function get_product_parameters_by_id(int $id): array {
        $formatted_product_properties = [];
        if(!empty($id)){
            $formatted_product_properties[] = carbon_get_post_meta(get_the_ID(), 'product_parameters');
        }
        return $this->format_product_properties($formatted_product_properties);
    }
}
