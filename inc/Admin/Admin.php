<?php
namespace App\Admin;

class Admin {
    public function __construct(){
        add_action( 'admin_menu', [$this, 'add_admin_menu_pages'] );
    }

    public function add_admin_menu_pages(){
        add_menu_page(
            __( 'WC Options', 'xvape' ),
            'WC Options',
            'manage_options',
            'wc_c_options',
            [$this, 'wc_c_options_page'],
            'dashicons-screenoptions',
            82,82734687
        );
    }

    public function wc_c_options_page(){
        get_template_part('inc/Admin/pages/wc-options', 'page');
    }
}