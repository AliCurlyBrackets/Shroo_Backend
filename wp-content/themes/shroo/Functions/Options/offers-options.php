<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register the Offers option page (top-level menu, no submenu)
add_action( 'acf/init', function () {

    // 1) Register the option page shown directly under the WordPress admin menu
    acf_add_options_page( array(
        'page_title'  => __( 'Offers Settings', 'shroo' ),
        'menu_title'  => __( 'Offers', 'shroo' ),
        'menu_slug'   => 'shroo-offers-settings',
        'capability'  => 'edit_posts',
        'icon_url'    => 'dashicons-sale',
        'position'    => 41,
        'redirect'    => true,
    ) );

    // 2) Register the field group
    if ( function_exists( 'acf_add_local_field_group' ) ) {
        acf_add_local_field_group( array(
            'key'   => 'group_shroo_offers_section',
            'title' => __( 'Offers Section', 'shroo' ),
            'fields' => array(

                // Enable / disable toggle
                array(
                    'key'         => 'field_shroo_offers_enabled',
                    'label'       => __( 'Enable Offers Section', 'shroo' ),
                    'name'        => 'offers_enabled',
                    'type'        => 'true_false',
                    'default_value' => 1,
                ),

                // Section heading
                array(
                    'key'         => 'field_shroo_offers_heading',
                    'label'       => __( 'Section Title', 'shroo' ),
                    'name'        => 'offers_heading',
                    'type'        => 'text',
                    'default_value' => __( 'عروض الباقات الخاصة', 'shroo' ),
                ),

                // Section subtitle / description
                array(
                    'key'         => 'field_shroo_offers_subtitle',
                    'label'       => __( 'Section Subtitle', 'shroo' ),
                    'name'        => 'offers_subtitle',
                    'type'        => 'textarea',
                    'rows'        => 3,
                    'default_value' => __( 'عناية متكاملة لإشراقة أكبر وبقيمة أفضل.', 'shroo' ),
                ),

                // Button text
                array(
                    'key'         => 'field_shroo_offers_button_text',
                    'label'       => __( 'Button Text', 'shroo' ),
                    'name'        => 'offers_button_text',
                    'type'        => 'text',
                    'default_value' => __( 'تسوقي الباقات', 'shroo' ),
                ),

                // Button URL
                array(
                    'key'         => 'field_shroo_offers_button_url',
                    'label'       => __( 'Button URL', 'shroo' ),
                    'name'        => 'offers_button_url',
                    'type'        => 'url',
                    'default_value' => '',
                ),

                // Background image
                array(
                    'key'         => 'field_shroo_offers_bg_image',
                    'label'       => __( 'Background Image', 'shroo' ),
                    'name'        => 'offers_bg_image',
                    'type'        => 'image',
                    'return_format' => 'id',
                    'preview_size'  => 'medium',
                    'instructions'  => __( 'Fallback: assets/img/bundle-products.jpg', 'shroo' ),
                ),

                // Repeater of offer features
                array(
                    'key'         => 'field_shroo_offers_items',
                    'label'       => __( 'Offer Features', 'shroo' ),
                    'name'        => 'offers_items',
                    'type'        => 'repeater',
                    'button_label' => __( 'Add Feature', 'shroo' ),
                    'min'         => 1,
                    'max'         => 6,
                    'layout'      => 'block',
                    'sub_fields'  => array(

                        // Feature icon (image upload)
                        array(
                            'key'  => 'field_shroo_offers_item_icon',
                            'label' => __( 'Icon Image', 'shroo' ),
                            'name'  => 'icon',
                            'type'  => 'image',
                            'return_format' => 'id',
                            'preview_size'  => 'medium',
                            'instructions'  => __( 'Upload an image icon for this feature.', 'shroo' ),
                        ),

                        // Feature bold label
                        array(
                            'key'   => 'field_shroo_offers_item_label',
                            'label' => __( 'Label (Bold)', 'shroo' ),
                            'name'  => 'label',
                            'type'  => 'text',
                            'placeholder' => __( 'e.g. وفّري أكثر', 'shroo' ),
                        ),

                        // Feature description
                        array(
                            'key'   => 'field_shroo_offers_item_desc',
                            'label' => __( 'Description', 'shroo' ),
                            'name'  => 'description',
                            'type'  => 'text',
                            'placeholder' => __( 'e.g. خصم حتى 20%', 'shroo' ),
                        ),
                    ),
                ),

            ),
            'location' => array(
                array(
                    array(
                        'param'    => 'options_page',
                        'operator' => '==',
                        'value'    => 'shroo-offers-settings',
                    ),
                ),
            ),
        ) );
    }
} );


