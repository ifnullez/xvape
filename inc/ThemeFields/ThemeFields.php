<?php
namespace App\ThemeFields;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Block;
use Carbon_Fields\Carbon_Fields;
use App\Helper\Helper;
use App\ThemeFields\Icons\IconsProvider;

class ThemeFields {

    private $helper;
    public $icons_provider;

    public function __construct() {
        add_action( 'carbon_fields_register_fields', [$this, 'crb_attach_theme_options']);
        Carbon_Fields::boot();
        $this->helper = new Helper();
        $this->icons_provider = new IconsProvider();
        $this->custom_gutenberg_blocks();
        $this->product_fields();
    }

    public function crb_attach_theme_options() {
        // theme options
        $theme_options = Container::make( 'theme_options', __( 'Theme Options' ) )
        ->set_icon( 'dashicons-carrot' )
        ->add_fields([
            Field::make( 'complex', 'social_icons', __( 'Social Icons' ) )
            ->add_fields( array(
                Field::make( 'icon', 'icon', __( 'Icon' ) )->add_provider_options( 'bootstrap_5_icons' ),
                Field::make( 'text', 'link', __( 'Link' ) ),
            ) )
        ]);

        // header
        Container::make( 'theme_options', __( 'Header' ) )
        ->set_page_parent( $theme_options )
        ->add_fields( array(
            Field::make( 'header_scripts', 'crb_header_scripts', __( 'Header Scripts' ) ),
        ) );

        //footer
        Container::make( 'theme_options', __( 'Footer' ) )
        ->set_page_parent( $theme_options )
        ->add_fields( array(
            Field::make( 'footer_scripts', 'crb_footer_scripts', __( 'Footer Scripts' ) ),
            Field::make( 'complex', 'before_footer_stripe_info', __( 'Before Footer Stripe' ) )
            ->add_fields( array(
                Field::make( 'icon', 'icon', __( 'Icon' ) ),
                Field::make( 'textarea', 'text', __( 'Text' ) ),
            ) ),
            Field::make( 'rich_text', 'footer_contact_info', __( 'Contact Info ( section 3 )' ) ),
            Field::make( 'rich_text', 'footer_site_info', __( 'Site Info ( section 1 )' ) ),
            Field::make( 'rich_text', 'footer_site_info_description', __( 'Site Info Description ( section 1 )' ) )
        ) );
        // nav menu item
        Container::make( 'nav_menu_item', __( 'Menu Settings' ) )
        ->add_fields( array(
            Field::make( 'image', 'menu_icon', __( 'Icon' ) )
        ));
    }

    public function custom_gutenberg_blocks() {
        // Product Categories Grid
        Block::make( __( 'Product Categories Grid' ) )
	    ->add_fields( array(
            Field::make( 'association', 'product_categories_grid', __( 'Product Categories Grid' ) )
            ->set_types( array(
                array(
                    'type'      => 'term',
                    'taxonomy' => 'product_cat',
                )
            ) )
        ) )
        ->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
            get_template_part('template-parts/blocks/category', 'block', [
                'fields' => $fields,
                'attributes' => $attributes,
                'inner_blocks' => $inner_blocks
            ]);
        } );
        // Banner
        Block::make( __( 'Banner' ) )
	    ->add_fields( array(
            Field::make( 'text', 'banner_title', __( 'Title' ) ),
            // Field::make( 'rich_text', 'banner_description', __( 'Description' ) ),
            Field::make( 'image', 'banner_image', __( 'Image' ) ),
            Field::make( 'text', 'banner_link', __( 'Link' ) )
        ) )
        ->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
            get_template_part('template-parts/blocks/banner', 'block', [
                'fields' => $fields,
                'attributes' => $attributes,
                'inner_blocks' => $inner_blocks
            ]);
        } );

        // Best user scored
        Block::make( __( 'Most commentend products' ) )
	    ->add_fields( array(
            Field::make( 'text', 'most_commented_section_title', __( 'Title' ) )
        ) )
        ->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
            get_template_part('template-parts/blocks/most-commented-slider', 'block', [
                'fields' => $fields,
                'attributes' => $attributes,
                'inner_blocks' => $inner_blocks
            ]);
        } );

        // Newest products
        Block::make( __( 'Newest products Slider' ) )
	    ->add_fields( array(
            Field::make( 'text', 'newest_products_title', __( 'Title' ) )
        ) )
        ->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
            get_template_part('template-parts/blocks/newest-products-slider', 'block', [
                'fields' => $fields,
                'attributes' => $attributes,
                'inner_blocks' => $inner_blocks
            ]);
        } );
    }

    public function product_fields(){
        Container::make( 'post_meta', 'Product Parameters' )
        ->where( 'post_type', '=', 'product' )
        ->set_context ( 'side' )
        ->add_fields( array(
            Field::make( 'complex', 'product_parameters' )->add_fields( array(
                Field::make( 'text', 'parameter_name' ),
                Field::make( 'complex', 'parameter_properties' )
                    ->add_fields( array(
                        Field::make( 'text', 'property_name' )
                    ))
            )),
        ));
    }

}