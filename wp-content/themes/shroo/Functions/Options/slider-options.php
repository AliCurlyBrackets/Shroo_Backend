<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'shroo_register_slider_option_page' ) ) {
    function shroo_register_slider_option_page() {
        acf_add_options_page( array(
            'page_title'  => __( 'Slider Settings', 'shroo' ),
            'menu_title'   => __( 'Slider', 'shroo' ),
            'menu_slug'     => 'shroo-slider-settings',
            'capability'    => 'edit_posts',
            'icon_url'      => 'dashicons-images-alt2',
            'position'      => 6,
            'redirect'      => true,
        ) );
    }
    add_action( 'acf/init', 'shroo_register_slider_option_page' );
}

if ( ! function_exists( 'shroo_register_slider_field_group' ) ) {
    function shroo_register_slider_field_group() {
        acf_add_local_field_group( array(
            'key'   => 'group_shroo_slider_options',
            'title' => __( 'Slider Hero Settings', 'shroo' ),
            'fields' => array(
                array(
                    'key'   => 'field_shroo_slider_enabled',
                    'label' => __( 'Enable Slider', 'shroo' ),
                    'name'  => 'slider_enabled',
                    'type'  => 'true_false',
                    'message' => __( 'Enable the hero slider on the homepage.', 'shroo' ),
                    'default_value' => 1,
                ),
                array(
                    'key'   => 'field_shroo_slider_slides',
                    'label' => __( 'Slider Slides', 'shroo' ),
                    'name'  => 'slider_slides',
                    'type'  => 'repeater',
                    'instructions' => __( 'Add, edit, or reorder your hero slides.', 'shroo' ),
                    'button_label' => __( 'Add Slide', 'shroo' ),
                    'min' => 1,
                    'max' => 10,
                    'layout' => 'block',
                    'sub_fields' => array(
                        array(
                            'key'   => 'field_shroo_slide_image',
                            'label' => __( 'Background Image', 'shroo' ),
                            'name'  => 'image',
                            'type'  => 'image',
                            'instructions' => __( 'Select or upload a background image for this slide.', 'shroo' ),
                            'return_format' => 'id',
                            'preview_size' => 'medium',
                        ),
                        array(
                            'key'   => 'field_shroo_slide_title',
                            'label' => __( 'Title', 'shroo' ),
                            'name'  => 'title',
                            'type'  => 'text',
                            'instructions' => __( 'Main heading text displayed on the slide.', 'shroo' ),
                        ),
                        array(
                            'key'   => 'field_shroo_slide_subtitle',
                            'label' => __( 'Subtitle', 'shroo' ),
                            'name'  => 'subtitle',
                            'type'  => 'text',
                            'instructions' => __( 'Sub-heading text displayed under the title.', 'shroo' ),
                        ),
                        array(
                            'key'   => 'field_shroo_slide_tagline',
                            'label' => __( 'Tagline', 'shroo' ),
                            'name'  => 'tagline',
                            'type'  => 'text',
                            'instructions' => __( 'Short tagline or description text.', 'shroo' ),
                        ),
                        array(
                            'key'   => 'field_shroo_slide_button_text',
                            'label' => __( 'Button Text', 'shroo' ),
                            'name'  => 'button_text',
                            'type'  => 'text',
                            'instructions' => __( 'Text for the call-to-action button.', 'shroo' ),
                        ),
                        array(
                            'key'   => 'field_shroo_slide_button_url',
                            'label' => __( 'Button URL', 'shroo' ),
                            'name'  => 'button_url',
                            'type'  => 'url',
                            'instructions' => __( 'Link for the call-to-action button.', 'shroo' ),
                        ),
                        array(
                            'key'   => 'field_shroo_slide_features',
                            'label' => __( 'Feature Bullets', 'shroo' ),
                            'name'  => 'features',
                            'type'  => 'repeater',
                            'instructions' => __( 'Add up to 4 feature text items with position.', 'shroo' ),
                            'button_label' => __( 'Add Feature', 'shroo' ),
                            'max' => 4,
                            'layout' => 'block',
                            'sub_fields' => array(
                                array(
                                    'key'   => 'field_shroo_slide_feature_text',
                                    'label' => __( 'Feature Text', 'shroo' ),
                                    'name'  => 'text',
                                    'type'  => 'text',
                                ),
                                array(
                                    'key'   => 'field_shroo_slide_feature_position',
                                    'label' => __( 'Position (%)', 'shroo' ),
                                    'name'  => 'position',
                                    'type'  => 'number',
                                    'default_value' => 50,
                                    'min' => 0,
                                    'max' => 100,
                                    'append' => '%',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param'  => 'options_page',
                        'operator' => '==',
                        'value'  => 'shroo-slider-settings',
                    ),
                ),
            ),
        ) );
    }
    add_action( 'acf/init', 'shroo_register_slider_field_group' );
}
