<?php 
namespace App\WC;
use App\Helper\Helper;
use WP_Error;

class WC_Custom {
    private $helper;
    private $wpdb;
    private $prefix;

    public function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->prefix = $this->wpdb->prefix;
        $this->helper = new Helper();
        add_action('wp_enqueue_scripts', [$this, 'override_wc_scripts']);
        add_action('wp_ajax_mini_cart_count', [$this, 'mini_cart_count']);
        add_action('wp_ajax_nopriv_mini_cart_count', [$this, 'mini_cart_count']);
        add_action('wp_ajax_to_cart', [$this, 'ajax_custom_add_to_cart']);
        add_action('wp_ajax_nopriv_to_cart', [$this, 'ajax_custom_add_to_cart']);

        add_action('wp_ajax_find_variation', [$this, 'ajax_find_variation']);
        add_action('wp_ajax_nopriv_find_variation', [$this, 'ajax_find_variation']);
        
        add_filter( 'woocommerce_checkout_fields' , [$this, 'add_woo_checkout_fields'] );
        add_filter( 'woocommerce_default_address_fields' , [$this, 'filter_default_address_fields'], 20, 1 );
        add_filter( 'woocommerce_cart_needs_shipping', [$this, 'filter_cart_needs_shipping'] );
        // override mini-cart buttons
        remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_button_view_cart', 10 );
        remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20 );
        add_action( 'woocommerce_widget_shopping_cart_buttons', [$this, 'custom_woocommerce_widget_shopping_cart_button_view_cart'], 10 );
        add_action( 'woocommerce_widget_shopping_cart_buttons', [$this, 'custom_woocommerce_widget_shopping_cart_proceed_to_checkout'], 20 );
        add_filter('woocommerce_form_field_args',  [$this, 'wc_form_field_args'], 10, 3);
        add_filter('woocommerce_placeholder_img', [$this, 'custom_woocommerce_placeholder_img'], 10, 3);
        add_filter('woocommerce_placeholder_img_src', [$this, 'custom_woocommerce_placeholder_img_src']);
        // remove woocommerce breadcrumbs
        remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
        add_filter( 'woocommerce_product_tabs', array( $this, 'remove_unneeded_single_product_tabs' ) );
        add_filter( 'woocommerce_product_get_rating_html', [$this, 'wc_custom_comment_rating_html'], 10, 3);
        add_action( 'woocommerce_product_query', [$this, 'custom_product_query'] );
        add_action( 'wp_ajax_insert_local_based_attrs', [$this, 'insert_local_based_attrs'] );
        add_action( 'wp_ajax_nopriv_insert_local_based_attrs', [$this, 'insert_local_based_attrs'] );
        // add custom tabs
        add_filter( 'woocommerce_product_data_tabs', [$this, 'product_custom_tab'], 10, 1 );
        add_action( 'woocommerce_product_data_panels', [$this, 'product_custom_parameters_tab_content'], 10, 1 );
    }
    public function mini_cart_count() {
        wp_send_json_success([
            'cart_items_count' => WC()->cart->get_cart_contents_count()
        ]);
    }
    public function product_in_cart($product_id) {
        $product_cart_id = WC()->cart->generate_cart_id( $product_id );
        $in_cart = WC()->cart->find_product_in_cart( $product_cart_id );
        if ( $in_cart ) {
            return true;
        }
        return false;
    }

    public function get_product_star_rating($rating, int $max_rating = 5) {
        $rating_html = '';
        for($i = 1; $i <= $max_rating; $i++){
            if ($i <= $rating) {
                $rating_html .= '<i class="bi bi-star-fill"></i>';
            } elseif ( ( $i - $rating ) < 1) {
                $rating_html .= '<i class="bi bi-star-half"></i>';
            } else {
                $rating_html .= '<i class="bi bi-star"></i>';
            }
        }
        return $rating_html;
    }

    public function filter_cart_needs_shipping( $needs_shipping ) {
        if ( is_cart() ) {
            $needs_shipping = false;
        }
        return $needs_shipping;
    }
    public function filter_default_address_fields( $address_fields ) {
        // Only on checkout page
        if( ! is_checkout() ) return $address_fields;

        // All field keys in this array
        $key_fields = array('country','company','address_1','address_2','city','state','postcode');

        // Loop through each address fields (billing and shipping)
        foreach( $key_fields as $key_field ){
            $address_fields[$key_field]['required'] = false;
        }

        return $address_fields;
    }



    public function add_woo_checkout_fields( $fields ) {
        $fields['billing']['pickup_point'] = array(
            'label' => __('Забрать из магазина', 'woocommerce'),
            'class' => ['shop-addr'],
            'type' => 'select',
            'priority'     => 20,
            'options' => [
                'shop_1' => 'Запорожье, пр. Соборный 177'
            ]
        );
        return $fields;
    }
    public function custom_woocommerce_widget_shopping_cart_button_view_cart() {
        echo '<a href="' . esc_url( wc_get_cart_url() ) . '" class="btn btn-outline-dark">' . esc_html__( 'View cart', 'woocommerce' ) . '</a>';
    }
    public function custom_woocommerce_widget_shopping_cart_proceed_to_checkout() {
        echo '<a href="' . esc_url( wc_get_checkout_url() ) . '" class="btn btn-outline-success">' . esc_html__( 'Checkout', 'woocommerce' ) . '</a>';
    }

    public function wc_form_field_args($args, $key, $value) {
        $args['input_class'] = [ 'form-control' ];
        // $args['placeholder'] = $args['label'];
        return $args;
    }
    
    public function override_wc_scripts() {
        wp_deregister_script('wc-checkout');
        wp_dequeue_script('wc-checkout');
    }

    public function custom_woocommerce_placeholder_img_src( $src ) {
        $src = $this->helper->dist_img_uri . '/vape.png';
        
        return $src;
    }

    public function custom_woocommerce_placeholder_img($image_html, $size, $dimensions){
        $image      = wc_placeholder_img_src( $size );
        $image_html = '<img src="'.$this->helper->dist_img_uri . '/vape.png'.'"' . esc_attr( $image ) . '" alt="' . esc_attr__( 'Placeholder', 'woocommerce' ) . '" width="' . esc_attr( $dimensions['width'] ) . '" class="woocommerce-placeholder wp-post-image card-img-top" height="' . esc_attr( $dimensions['height'] ) . '" />';

        return $image_html;
    }

    public function product_in_stock($product) {
        $stock = $product->get_stock_quantity();
        if ($stock >= 1){
          return true;
        } elseif($stock < 1 && !$product->backorders_allowed() ){
          return false;
        }
    }
    public static function remove_unneeded_single_product_tabs( $tabs ) {
        unset($tabs['additional_information']);
		unset( $tabs['description'] );
		return $tabs;
	}

    public function get_product_variations($product_id = 0, $variation_attributes_values = []) {
        if(!empty($product_id)){
            $product = wc_get_product($product_id);
            $available_variations = $product->get_available_variations();
            foreach($available_variations as $variation){
                if(count(array_intersect($variation_attributes_values, $variation['attributes'])) === count($variation_attributes_values)){
                    return $variation;
                }
            }
            return json_decode($product);
        }
    }

    public function ajax_find_variation(){
        // if(!empty($_POST['variation'])){
            $variations = [];
            $product_id = $_POST['product_id'];
            foreach($_POST['variation'] as $variation){
                $variations[] = $variation['value'];
            }
            wp_send_json_success($this->get_product_variations($product_id, $variations));
        // }
    }

    public static function ajax_custom_add_to_cart() {
        $variations = [];
        if(!empty($_POST['variation'])){
            foreach($_POST['variation'] as $variation){
                $variations[$variation['name']] = $variation['value'];
            }
        }
        if(!empty($_POST['product_id'])){
            $cart_item_key = WC()->cart->add_to_cart($_POST['product_id'], $_POST['quantity'], $_POST['variation_id'], $variations);
        }
        if(!empty($cart_item_key)){
            wp_send_json_success([
                'added' => true
            ]);
        }
        wp_send_json_error([
            'added' => false
        ]);
    }

    function wc_custom_comment_rating_html( $html, $rating, $count ) {
        $html = '<div class="comment_rating">'. $this->get_product_star_rating($rating) .'</div>';
    
        return $html;
    }

    public function custom_product_query( $q ){
        $tax_query = $q->get( 'tax_query' );
        $meta_query = $q->get( 'meta_query' );

        $q->set('orderby', 'meta_value');
        $q->set('meta_key', '_stock_status');
        

        $params = [];
        $attrs_params = [];
        $formatted_parameters = [];
        $cat_params = '';
        $price_params = '';
        $stock_params = '';

        if(!empty($_GET)){
            foreach($_GET as $filter_query_key => $filter_query_value){
                if(!empty($filter_query_value) && is_array($filter_query_value)){
                    foreach($filter_query_value as $filter_val_key => $val){
                        if(!is_array($val)){
                            $params[$filter_query_key][] = urldecode($val);
                        } else {
                            foreach($val as $attr_value_params){
                                $params[$filter_val_key][] = urldecode($attr_value_params);
                            }
                        }
                    }
                } else {
                    $params[urldecode($filter_query_key)] = urldecode($filter_query_value);
                }
            }
        }

        // filter by category
        if(!empty($params['p_cat'])){
            $cat_params = [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $params['p_cat']
            ];
        }
       
        // filter by price
        if(!empty($params['mn_p']) && !empty($params['mx_p'])){
            $price_params = [
                'key' => '_price',
                'value'    => [$params['mn_p'], $params['mx_p']],
                'type' => 'numeric',
                'compare' => 'between'
            ];
        }
        // in_stock
        if( isset($params['in_stock']) && $params['in_stock'] == 'on'){
            $stock_params = [
                'key'     => '_stock_status',
                'value'   => 'instock',
                'compare' => '=',
            ];
        }
        // filter attributes
        if( isset($params['at']) && !empty($params['at'])){
            foreach($params['at'] as $attributes){
                $attr_array = explode('-', $attributes);
                $formatted_parameters[$attr_array[1]][] = $attr_array[0];
            }
            if(!empty($formatted_parameters)){
                foreach($formatted_parameters as $params_key => $params_value){
                    $attrs_params[] = [
                        'relation' => 'AND',
                        [
                            'key'     => 'product_parameters/parameter_name',
                            'value'   => $params_key,
                        ],
                        [
                            'key'     => 'product_parameters/parameter_properties/property_name',
                            'value'   => $params_value,
                        ]
                    ];
                }
            }
        }

        $q->set( 'tax_query', [
            $cat_params
        ]);
        
        $q->set( 'meta_query', [
            $stock_params,
            $price_params,
            [
                'relation' => 'OR',
                $attrs_params
            ]
        ]);

        return $q;
    }

    function create_product_attribute( $label_name ){

        $slug = sanitize_title( $label_name );
    
        if ( strlen( $slug ) >= 32 ) {
            return new WP_Error( 'invalid_product_attribute_slug_too_long', sprintf( __( 'Name "%s" is too long (32 characters max). Shorten it, please.', 'woocommerce' ), $slug ), array( 'status' => 400 ) );
        } elseif ( wc_check_if_attribute_name_is_reserved( $slug ) ) {
            return new WP_Error( 'invalid_product_attribute_slug_reserved_name', sprintf( __( 'Name "%s" is not allowed because it is a reserved term. Change it, please.', 'woocommerce' ), $slug ), array( 'status' => 400 ) );
        } elseif ( taxonomy_exists( wc_attribute_taxonomy_name( $label_name ) ) ) {
            return new WP_Error( 'invalid_product_attribute_slug_already_exists', sprintf( __( 'Name "%s" is already in use. Change it, please.', 'woocommerce' ), $label_name ), array( 'status' => 400 ) );
        }
    
        $data = array(
            'attribute_label'   => $label_name,
            'attribute_name'    => $slug,
            'attribute_type'    => 'select',
            'attribute_orderby' => 'menu_order',
            'attribute_public'  => true,
        );
    
        $results = $this->wpdb->insert( "{$this->wpdb->prefix}woocommerce_attribute_taxonomies", $data );
    
        if ( is_wp_error( $results ) ) {
            return new WP_Error( 'cannot_create_attribute', $results->get_error_message(), array( 'status' => 400 ) );
        }
    
        $id = $this->wpdb->insert_id;
    
        do_action('woocommerce_attribute_added', $id, $data);
    
        wp_schedule_single_event( time(), 'woocommerce_flush_rewrite_rules' );
    
        delete_transient('wc_attribute_taxonomies');
    }

    public function insert_local_based_attrs(){
        $finded_attributes = $this->helper->get_products_attributes();
        if(!empty($finded_attributes)){
            foreach($finded_attributes as $finded_attr_name => $finded_attr_value){
                $this->create_product_attribute($finded_attr_name);
            }
        }
        wp_send_json_success();
    }

    public function get_products(){
        $response = [];
        // pagination parameters
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $post_per_page = intval(get_query_var('posts_per_page'));
        $offset = ($paged - 1) * $post_per_page;
        // filters sql
        $sql_filter_params = "";
        $sql_join_meta = "";

        $sql_join_meta_1 = " INNER JOIN {$this->prefix}postmeta AS pm1 ON pm1.post_id = p.ID ";
        $sql_join_meta_2 = " INNER JOIN {$this->prefix}postmeta AS pm2 ON pm2.post_id = p.ID ";
        $sql_join_meta_3 = " INNER JOIN {$this->prefix}postmeta AS pm3 ON pm3.post_id = p.ID ";
        $sql_join_term_relationships = " INNER JOIN {$this->prefix}term_relationships AS tr ON (p.ID = tr.object_id) ";
        $sql_join_term_taxonomy = " INNER JOIN {$this->prefix}term_taxonomy AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) ";

        // max price
        if(isset($_GET['mx_p']) && !empty($_GET['mx_p'])){
            $sql_join_meta = $sql_join_meta_1;
            $sql_filter_params = " AND pm1.meta_key = '_price' AND pm1.meta_value <= {$_GET['mx_p']} ";
        }
        // min price
        if(isset($_GET['mn_p']) && !empty($_GET['mn_p'])){
            $sql_join_meta = $sql_join_meta_1;
            $sql_filter_params = " AND pm1.meta_key = '_price' AND pm1.meta_value >= {$_GET['mn_p']} ";
        }
        // both prices
        if( ( isset($_GET['mx_p']) && !empty($_GET['mx_p'])) && (isset($_GET['mn_p']) && !empty($_GET['mn_p'])) ){
            $sql_join_meta = $sql_join_meta_1;
            $sql_filter_params = " AND pm1.meta_key = '_price' AND pm1.meta_value BETWEEN {$_GET['mn_p']} AND {$_GET['mx_p']} ";
        }
        
        // in stock filter
        if(isset($_GET['in_stock']) && !empty($_GET['in_stock']) && $_GET['in_stock'] == 'on'){
            $sql_join_meta .= $sql_join_meta_2;
            $sql_filter_params .= " AND pm2.meta_key = '_stock_status' AND pm2.meta_value = 'instock'";
        }

        // category filter constrictor
        if(isset($_GET['p_cat']) && !empty($_GET['p_cat'])){
            $sql_filter_params .= " AND tt.taxonomy = 'product_cat' AND (";
            foreach($_GET['p_cat'] as $cat){
                $sql_filter_params .= " tt.term_id = {$cat} OR ";
            }
            $sql_filter_params = substr($sql_filter_params, 0, -3);
            $sql_filter_params .= " )";
        } else if(is_product_category()){
            $current_cat = get_queried_object();
            $sql_filter_params = " AND tt.taxonomy = 'product_cat' AND tt.term_id = {$current_cat->term_id} ";
        }
        // attributes filter constructor
        if(isset($_GET['at']) && !empty($_GET['at'])){
            $sql_join_meta .= $sql_join_meta_3;
            $sql_filter_params .= " AND pm3.meta_key = '_product_attributes' AND ( ";
            foreach($_GET['at'] as $attr_val){
                $v = explode('-', urldecode($attr_val));
                $key = trim($v[1]);
                $value = trim($v[0]);
                $sql_filter_params .= " (pm3.meta_value LIKE '%{$key}%' AND pm3.meta_value LIKE '%{$value}%') OR ";
            }
            $sql_filter_params = substr($sql_filter_params, 0, -3);
            $sql_filter_params .= " )";
        }

        // send builded request to get products
        $products = $this->wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS(p.ID) 
            FROM {$this->prefix}posts AS p 
            {$sql_join_meta}  
            {$sql_join_term_relationships} 
            {$sql_join_term_taxonomy} 
            WHERE p.post_type = 'product' 
            {$sql_filter_params} 
            GROUP BY p.ID 
            ORDER BY p.post_date DESC
            LIMIT {$offset}, {$post_per_page}");

        $finded_rows = $this->wpdb->get_var("SELECT FOUND_ROWS();");

        if(!empty($products)){
            $response = [
                'products' => $products,
                'paged' => $paged,
                'per_page' => $post_per_page,
                'offset' => $offset,
                'max_num_pages' => ceil( $finded_rows / $post_per_page)
            ];
        }
        return $response;
    }

    public function product_custom_tab( $tabs ) {
        $tabs['product_parameters'] = array(
            'label' => __( 'Parameters', 'woocommerce' ),
            'target' => 'product_custom_parameters_tab_content',
            'priority' => 60,
            'class'   => array()
        );
        return $tabs;
    }
    public function product_custom_parameters_tab_content($param) {
        get_template_part('template-parts/woocommerce/admin/tabs/parameters', 'tab');
    }

    public function get_product_cat_child_cat($main_term = ''): array
    {
        if( empty($main_term) ){
            $main_term = get_queried_object_id();
        }

        $ids = [];
        if( is_product_category() ) {
            $args_query = [
                'taxonomy' => 'product_cat', 
                'hide_empty' => true, 
                'child_of' => $main_term
            ];
            if ( !empty($main_term) && $main_term != 0 ) {
                $terms = get_terms( $args_query );
                foreach ( $terms as $term ) {
                    if( $term->parent == $main_term ) {
                      $ids[] = $term->term_id; 
                    }
                }
            }
        }
        return $ids;
    }

    public function category_childrens()
    {
        $categories = $this->get_product_cat_child_cat();
        if(!empty($categories)){
            foreach($categories as $catID){
                get_template_part('woocommerce/content-product', 'cat', [ 
                    'category' => get_term_by('id', $catID, 'product_cat'),
                    'cat_in_cat' => count($this->get_product_cat_child_cat($catID))
                    ]
                );
            }
        }
    }
    
}