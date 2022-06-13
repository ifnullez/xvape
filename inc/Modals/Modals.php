<?php
namespace App\Modals;

class Modals {
    public function __construct(){
        // search modal
        add_action('wp_ajax_get_search_modal', [$this, 'search_form']);
        add_action('wp_ajax_nopriv_get_search_modal', [$this, 'search_form']);
        // mini-cart
        add_action('wp_ajax_get_mini_cart_modal', [$this, 'get_mini_cart']);
        add_action('wp_ajax_nopriv_get_mini_cart_modal', [$this, 'get_mini_cart']);
        // login
        add_action('wp_ajax_get_login_modal', [$this, 'get_login_form']);
        add_action('wp_ajax_nopriv_get_login_modal', [$this, 'get_login_form']);
        // register
        add_action('wp_ajax_get_register_modal', [$this, 'get_register_form']);
        add_action('wp_ajax_nopriv_get_register_modal', [$this, 'get_register_form']);
    }
    public function search_form() {
        ob_start(); 
            get_product_search_form();
        wp_send_json_success(ob_get_clean());
    }

    public function get_mini_cart() {
        ob_start();
            woocommerce_mini_cart();
        wp_send_json_success(ob_get_clean());
    }

    public function get_login_form() {
        ob_start();
            get_template_part( 'template-parts/login', 'form');
        wp_send_json_success(ob_get_clean());
    }

    public function get_register_form(){
        ob_start();
            get_template_part( 'template-parts/register', 'form');
        wp_send_json_success(ob_get_clean());
    }
}