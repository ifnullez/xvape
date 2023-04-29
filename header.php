<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_site_icon();?>
    <?php wp_head();?>
</head>

<body <?php body_class();?>>
<div class="toast-container position-fixed"></div>
    <?php if (function_exists('wp_body_open')) {
    wp_body_open();
}
?>
    <header id="site-header">
        <nav class="navbar navbar-expand-lg navbar-light bg-light p-0">
        <div class="container-fluid">
            <?php the_custom_logo();?>
            <div id="main_nav" class="offcanvas offcanvas-start" tabindex="-1">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="main_nav_label"><?php _e('Menu', 'xvape'); ?></h5>
                    <button type="button" class="offcanvas-close btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body justify-content-center">
                    <?php if (has_nav_menu("primary_menu")) {
                            wp_nav_menu([
                                'theme_location' => "primary_menu",
                                'menu_class' => "navbar-nav",
                                'menu_id' => "primary_menu",
                                'container' => false,
                                'list_item_class' => 'nav-item',
                                'link_class' => 'nav-link menu-item',
                                'walker' => new App\NavWalker\NavWalker(),
                            ]);
                        }?>
                </div>
            </div>
            <div class="nav_buttons">
                <button type="button" class="btn btn-search" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSearch" aria-controls="offcanvasSearch" title="<?php _e('Search', 'woocommerce');?>">
                    <i class="bi bi-search"></i>
                </button>
                <button type="button"
                    class="widget_shopping_cart btn position-relative shopping-cart-btn" data-bs-toggle="offcanvas" data-bs-target="#miniCartWidget" aria-controls="miniCartWidget" title="<?php _e('Cart', 'woocommerce');?>">
                    <i class="bi bi-bag-check"></i>
                    <span class="cart_count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">
                        <?php echo class_exists( 'WooCommerce' ) ? WC()->cart->get_cart_contents_count() : 0; ?>
                    </span>
                </button>
                <?php if (is_user_logged_in()) {?>
                <div class="btn-group">
                    <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="<?php _e('Dashboard', 'woocommerce');?>">
                        <i class="bi bi-person-bounding-box"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <?php foreach (wc_get_account_menu_items() as $endpoint => $label) { ?>
                            <li>
                                <a class="dropdown-item <?php echo $endpoint == 'customer-logout' ? ' bg-danger text-white' : ''; ?>"
                                    href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>">
                                    <?php echo esc_html($label); ?>
                                </a>
                            </li>
                        <?php }?>
                    </ul>
                </div>
                <?php } else {?>
                    <button type="button" id="login" class="btn" title="<?php _e('Login', 'woocommerce');?>">
                        <i class="bi bi-box-arrow-in-right"></i>
                    </button>
                    <button type="button" id="register" class="btn" title="<?php _e('Registration', 'woocommerce');?>">
                        <i class="bi bi-person-plus"></i>
                    </button>
                <?php }?>
            </div>
            <button id="burger_button" type="button" class="btn" data-bs-toggle="offcanvas" data-bs-target="#main_nav" aria-controls="main_nav" title="<?php _e('Menu', 'woocommerce');?>">
                <span></span>
            </button>
        </div>
    </nav>
    <div id="miniCartWidget" class="offcanvas offcanvas-bottom" tabindex="-1" aria-labelledby="miniCartWidget">
        <div class="offcanvas-header">
            <h5 id="miniCartWidgetLabel">
                <?php _e('Cart', 'woocommerce'); ?>
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="widget_shopping_cart_content">
                <?php woocommerce_mini_cart(); ?>
            </div>
        </div>
    </div>
    <div class="offcanvas offcanvas-top" tabindex="-1" id="offcanvasSearch" aria-labelledby="offcanvasSearchLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasTopLabel"><?php _e('Search', 'woocommerce'); ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <?php echo get_product_search_form(); ?>
        </div>
    </div>
    </header>
    <main class="container site-main">
        <div class="row">