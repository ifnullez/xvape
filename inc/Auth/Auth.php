<?php
namespace App\Auth;

class Auth {
    public function __construct(){
        // login
        add_action('wp_ajax_login', [$this, 'custom_login']);
        add_action('wp_ajax_nopriv_login', [$this, 'custom_login']);
        // registration
        add_action('wp_ajax_register', [$this, 'custom_registration']);
        add_action('wp_ajax_nopriv_register', [$this, 'custom_registration']);
    }

    public function custom_login(){
        $login_args = [];
        if(!empty($_POST['login_data'])){
            foreach($_POST['login_data'] as $data){
                $login_args[$data['name']] = sanitize_text_field($data['value']);
            }
        }

        if(!wp_verify_nonce($login_args['_wpnonce'], 'login_register_nonce_security')){
            wp_send_json_error([
                'loggedin' => false,
                'message'=> __('Something went wrong', 'woocommerce')
            ]);
        } else {
            $is_signed = wp_signon( $login_args, true );
            if(is_wp_error($is_signed)){
                wp_send_json_error([
                    'loggedin' => false,
                    'message' => $is_signed->get_error_message()
                ]);
            } else {
                wp_send_json_success([
                    'loggedin' => true
                ]);
            }
        }
    }

    public function custom_registration(){
        $register_args = [];
        if(!empty($_POST['register_data'])){
            foreach($_POST['register_data'] as $data){
                $register_args[$data['name']] = sanitize_text_field($data['value']);
            }
            $register_args['user_login'] = substr($register_args['user_email'], 0, strpos($register_args['user_email'], '@'));
        }
        if(wp_verify_nonce($register_args['_wpnonce'], 'register_nonce_security')){
            if(!filter_var($register_args['user_email'], FILTER_VALIDATE_EMAIL)){
                wp_send_json_error([
                    'registered' => false,
                    'invalid_field' => 'user_email',
                    'message' => __('Please check your email')
                ]);
            }
            $user_id = wp_insert_user($register_args);
            if(is_wp_error($user_id)){
                wp_send_json_error([
                    'registered' => false,
                    'invalid_field' => false,
                    'message' => $user_id->get_error_message()
                ]);
            } else {
                $login_args = [
                    'user_login' => $register_args['user_login'],
                    'user_password' => $register_args['user_pass'],
                    'remember'      => true
                ];
                $is_signed = wp_signon( $login_args, true );
                wp_send_json_success([
                    'registered' => true,
                    'invalid_field' => false,
                    'message' => __('Registration success', 'woocommerce')
                ]);
            }
        }
        wp_send_json_error([
            'registered' => false,
            'invalid_field' => false,
            'message' => __('Something went wrong')
        ]);
    }
}