<?php
namespace App;

use App\Admin\Admin;
use App\Auth\Auth;
use App\Helper\Helper;
use App\Modals\Modals;
use App\ThemeFields\Icons\IconsProvider;
use App\ThemeFields\ThemeFields;
use App\WC\WC_Custom;
use Automattic\Jetpack\Constants;
use Carbon_Fields\Carbon_Fields;
use Carbon_Field_Icon\Icon_Field;
use stdClass;
// use App\CarbonFieldsOverrides\ExtendAssociationField;

// use App\WC\Checkout;

class ThemeInit
{

    private $theme_fields;
    private $modals;
    private $wc_customizations;
    private $auth_class;
    public $provider_id;
    public $theme_version;
    public $helper;
    protected $admin_customizations;
    // public $checkout;
    public $extend_ssociation_field;

    public function __construct()
    {
        $this->theme_version = wp_get_theme()->Version;
        $this->provider_id = 'bootstrap_5_icons';
        $this->register_bootstrap_5_icon_field_provider();
        $this->theme_fields = new ThemeFields();
        $this->modals = new Modals();
        $this->wc_customizations = new WC_Custom();
        $this->auth_class = new Auth();
        $this->helper = new Helper();
        $this->admin_customizations = new Admin();
        // $this->$extend_ssociation_field = new ExtendAssociationField();

        // $this->checkout = new Checkout();
        $this->speed_up_wp();
        remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
        remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
        add_action('after_setup_theme', [$this, 'theme_support']);
        add_action('wp_enqueue_scripts', [$this, 'theme_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'admin_theme_scripts']);
        add_filter('get_custom_logo', [$this, 'change_logo_class']);
        add_filter('nav_menu_css_class', [$this, 'add_menu_list_item_class'], 1, 3);
        add_filter('nav_menu_link_attributes', [$this, 'add_menu_link_class'], 1, 3);
        // add_filter('use_block_editor_for_post', '__return_false', 10);
        add_filter('wp_get_nav_menu_items', [$this, 'auto_add_cats'], 10, 3);
        add_action('wp_logout', [$this, 'auto_redirect_after_logout']);
        // add_filter( 'show_admin_bar', '__return_false' );

        // remove_filter( 'terms_clauses', [$this, 'replace_inner_with_straight_joins'], 20 );
        // add_filter( 'terms_clauses', [$this, 'replace_inner_with_straight_joins'], 20 );
    }

    public function register_bootstrap_5_icon_field_provider()
    {
        Carbon_Fields::instance()->ioc['icon_field_providers'][$this->provider_id] = function ($container) {
            return new IconsProvider();
        };
        Icon_Field::add_provider([$this->provider_id]);
    }

    public function admin_theme_scripts()
    {
        wp_enqueue_style('admin-custom', get_stylesheet_directory_uri() . '/assets/dist/admin.css', time(), $this->theme_version, 'all');
        wp_enqueue_script('admin-custom', get_stylesheet_directory_uri() . '/assets/dist/admin.js', ['jquery'], $this->theme_version, true);
        wp_localize_script('admin-custom', 'adm', [
            'url' => admin_url('admin-ajax.php'),
        ]);
    }

    public function theme_support()
    {
        // add_theme_support('customize-selective-refresh-widgets');
        // add_theme_support('widgets');
        add_theme_support('custom-logo', array(
            'height' => 70,
            'width' => 130,
            'flex-height' => true,
            'flex-width' => true,
            'unlink-homepage-logo' => false,
            'class' => 'navbar-brand',
        ));
        add_theme_support('woocommerce');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('wp-block-styles');
        add_editor_style('style.css');
        add_theme_support('html5', array('comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script'));
        // add nav menus
        register_nav_menus(array(
            'primary_menu' => __('Primary Menu', 'xvape'),
            'footer_menu' => __('Footer Menu', 'xvape'),
        ));
    }

    public function change_logo_class($html)
    {

        $html = str_replace('custom-logo', 'site-logo', $html);
        $html = str_replace('custom-logo-link', 'navbar-brand', $html);

        return $html;
    }

    public function add_menu_list_item_class($classes, $item, $args)
    {
        if (property_exists($args, 'list_item_class')) {
            $classes[] = $args->list_item_class;
        }
        return $classes;
    }

    public function add_menu_link_class($atts, $item, $args)
    {
        if (property_exists($args, 'link_class')) {
            $atts['class'] = $args->link_class;
        }
        return $atts;
    }

    public function theme_scripts()
    {
        wp_enqueue_style('main', get_stylesheet_directory_uri() . '/assets/dist/main.css', [], $this->theme_version, 'all');
        wp_enqueue_script('main', get_stylesheet_directory_uri() . '/assets/dist/main.js', ['jquery'], $this->theme_version, true);
        wp_localize_script('main', 'main', [
            'url' => admin_url('admin-ajax.php'),
            'wc_ajax_url' => \WC_AJAX::get_endpoint('%%endpoint%%'),
            'update_order_review_nonce' => wp_create_nonce('update-order-review'),
            'apply_coupon_nonce' => wp_create_nonce('apply-coupon'),
            'remove_coupon_nonce' => wp_create_nonce('remove-coupon'),
            'option_guest_checkout' => get_option('woocommerce_enable_guest_checkout'),
            'checkout_url' => \WC_AJAX::get_endpoint('checkout'),
            'is_checkout' => is_checkout() && empty($wp->query_vars['order-pay']) && !isset($wp->query_vars['order-received']) ? 1 : 0,
            'debug_mode' => Constants::is_true('WP_DEBUG'),
            'i18n_checkout_error' => esc_attr__('Error processing checkout. Please try again.', 'woocommerce'),
            'currency_symbol' => get_woocommerce_currency_symbol(),
            'cart_title' => esc_attr__('Cart', 'woocommerce'),
            'added_to_cart' => esc_attr__('added to cart', 'woocommerce'),
            'out_of_stock' => esc_attr__('Out of stock', 'woocommerce'),
            'price_filter' => $this->helper->get_products_prices(),
            'selected_min' => !empty($_GET['mn_p']) ? $_GET['mn_p'] : $this->helper->get_products_prices()['min_price'], 
            'selected_max' => !empty($_GET['mx_p']) ? $_GET['mx_p'] : $this->helper->get_products_prices()['max_price'],
        ]);
    }

    public function speed_up_wp()
    {
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'rel_canonical');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'index_rel_link');
        remove_action('wp_head', 'start_post_rel_link', 10);
        remove_action('wp_head', 'parent_post_rel_link', 10);
        remove_action('wp_head', 'adjacent_posts_rel_link', 10);
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
        remove_action('wp_head', 'feed_links', 2);
        remove_action('wp_head', 'feed_links_extra', 3);
        remove_action('wp_head', 'pagenavi_css');
    }

    public function _custom_nav_menu_item($title, $url, $order, $parent = 0)
    {
        $item = new stdClass();
        $item->ID = 1000000 + $order + $parent;
        $item->db_id = $item->ID;
        $item->title = $title;
        $item->url = $url;
        $item->menu_order = $order;
        $item->menu_item_parent = $parent;
        $item->type = '';
        $item->object = '';
        $item->object_id = '';
        $item->classes = array();
        $item->target = '';
        $item->attr_title = '';
        $item->description = '';
        $item->xfn = '';
        $item->status = '';
        return $item;
    }
    // TODO: Rewrite this method Because it\'s bad decision 
    public function auto_add_cats($items, $menu, $args)
    {
        $theme_locations = get_nav_menu_locations();
        if ($menu->term_id != $theme_locations['primary_menu']) {
            return $items;
        }

        if (is_admin()) {
            return $items;
        }
        $ctr = ($items[sizeof($items) - 1]->ID) + 1;

        foreach ($items as $index => $i) {
            // print_r($i->object);
            // if ("product_cat" !== $i->object) {
            //     continue;
            // }
            // $menu_parent = $i->ID;

            $terms = get_terms([
                'taxonomy' => 'product_cat',
                'hide_empty' => true,
                'parent' => 0,
                'orderby' => 'name',
                'order' => 'ASC',
            ]);
            if (!empty($terms) && $index == 0) {
                foreach ($terms as $term) {
                    if ($term->slug != 'uncategorized') {
                        $new_item = $this->_custom_nav_menu_item($term->name, get_term_link($term), $ctr); // last parameter needed to apped categories to some menu item with id
                        $items[] = $new_item;
                        $ctr++;
                        $terms_child = get_terms(array('taxonomy' => 'product_cat', 'parent' => $term->term_id));
                        if (!empty($terms_child)) {
                            foreach ($terms_child as $term_child) {
                                $new_child = $this->_custom_nav_menu_item($term_child->name, get_term_link($term_child), $ctr, $new_item->ID);
                                $items[] = $new_child;
                                $ctr++;
                                $third_child = get_terms(array('taxonomy' => 'product_cat', 'parent' => $term_child->term_id));
                                if (!empty($third_child)) {
                                    foreach ($third_child as $child) {
                                        $third_new_child = $this->_custom_nav_menu_item($child->name, get_term_link($child), $ctr, $new_child->ID);
                                        $items[] = $third_new_child;
                                        $ctr++;
                                        $fourth_child = get_terms(array('taxonomy' => 'product_cat', 'parent' => $child->term_id));
                                        if (!empty($fourth_child)) {
                                            foreach ($fourth_child as $fr_child) {
                                                $fourth_new_child = $this->_custom_nav_menu_item($fr_child->name, get_term_link($fr_child), $ctr, $third_new_child->ID);
                                                $items[] = $fourth_new_child;
                                                $ctr++;
                                                $fifth_child = get_terms(array('taxonomy' => 'product_cat', 'parent' => $fr_child->term_id));
                                                if (!empty($fifth_child)) {
                                                    foreach ($fifth_child as $fv_child) {
                                                        $fifth_new_child = $this->_custom_nav_menu_item($fv_child->name, get_term_link($fv_child), $ctr, $fourth_new_child->ID);
                                                        $items[] = $fifth_new_child;
                                                        $ctr++;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $items;
    }

    public function auto_redirect_after_logout()
    {
        wp_safe_redirect(home_url());
        exit;
    }

    // public function replace_inner_with_straight_joins( $pieces, $taxonomies = null, $args = null ) {
    //   global $wpdb;

    //   $s = 'INNER JOIN ' . $wpdb->prefix;
    //   $r = 'STRAIGHT_JOIN ' . $wpdb->prefix;
    //   $pieces['join'] = str_replace( $s, $r, $pieces['join'] );

    //   return $pieces;
    // }
}
