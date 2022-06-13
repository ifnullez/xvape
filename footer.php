</div>
</main>
    <footer id="site-footer">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <div class="col-12 col-md-3 col-lg-3 col-xl-3 col-xxl-3 p-3 d-flex flex-column">
                    <div class="site_info">
                        <?php echo carbon_get_theme_option('footer_site_info'); ?>
                    </div>
                    <div class="site_info_description">
                        <?php echo carbon_get_theme_option('footer_site_info_description'); ?>
                    </div>
                </div>
                <div class="col-12 col-md-5 col-lg-5 col-xl-5 col-xxl-5 p-3">
                    <?php if(has_nav_menu("footer_menu")){
                        wp_nav_menu([
                            'theme_location'    => "footer_menu",
                            'menu_class'        => "navbar-nav",
                            'menu_id'           => "footer_menu",
                            'container'         => false,
                            'list_item_class'   => 'nav-item',
                            'link_class'        => 'nav-link menu-item',
                        ]);
                    } ?>
                </div>
                <div class="col-12 col-md-3 col-lg-3 col-xl-3 col-xxl-3 p-3">
                    <div class="footer_contact_info d-flex flex-column">
                        <?php echo carbon_get_theme_option('footer_contact_info'); ?>
                    </div>
                    <?php get_template_part('template-parts/social', 'icons'); ?>
                </div>
                <div class="footer__bottom col-12 d-flex justify-content-between p-3">
                    <span>Copyright &copy; xvape.in.ua <?php echo date('Y'); ?></span>
                    <span>Developed by Yevhen Zakarliuka</span>
                </div>
                <!-- <div class="loader"></div> -->
            </div>
        </div>
        <?php wp_footer(); ?>
    </footer>
</body>
</html>