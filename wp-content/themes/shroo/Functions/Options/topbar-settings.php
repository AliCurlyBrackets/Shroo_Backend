<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Only run if ACF is available - this file requires ACF functions
if ( ! function_exists( 'acf_add_options_page' ) || ! function_exists( 'acf_add_local_field_group' ) ) {
    return;
}

acf_add_options_page( array(
    'page_title'  => __( 'Top Bar Settings', 'shroo' ),
    'menu_title'  => __( 'Top Bar', 'shroo' ),
    'menu_slug'   => 'shroo-topbar-settings',
    'capability'  => 'edit_posts',
    'icon_url'    => 'dashicons-visibility',
    'position'    => 22,
    'redirect'    => true,
) );

acf_add_local_field_group( array(
    'key'   => 'group_shroo_topbar_settings',
    'title' => __( 'Top Bar Settings', 'shroo' ),
    'fields' => array(

        array(
            'key'         => 'field_shroo_topbar_enabled',
            'label'       => __( 'Enable Top Bar', 'shroo' ),
            'name'        => 'topbar_enabled',
            'type'        => 'true_false',
            'default_value' => 1,
            'instructions' => 'Enable or disable the top bar above the header.',
        ),

        array(
            'key'         => 'field_shroo_topbar_notice_group',
            'label'       => __( 'Top Bar Notices', 'shroo' ),
            'name'        => 'topbar_notices',
            'type'        => 'group',
            'sub_fields'  => array(

                array(
                    'key'         => 'field_shroo_topbar_notice_1',
                    'label'       => __( 'First Notice', 'shroo' ),
                    'name'        => 'notice_1',
                    'type'        => 'text',
                    'placeholder' => 'e.g. شحن مجاني للطلبات فوق 150 ر.س',
                    'default_value' => 'شحن مجاني للطلبات فوق 150 ر.س',
                ),

                array(
                    'key'         => 'field_shroo_topbar_notice_2',
                    'label'       => __( 'Second Notice', 'shroo' ),
                    'name'        => 'notice_2',
                    'type'        => 'text',
                    'placeholder' => 'e.g. منتجات أصلية',
                    'default_value' => 'منتجات أصلية',
                ),

                array(
                    'key'         => 'field_shroo_topbar_notice_3',
                    'label'       => __( 'Third Notice', 'shroo' ),
                    'name'        => 'notice_3',
                    'type'        => 'text',
                    'placeholder' => 'e.g. جمال مشرق كل يوم',
                    'default_value' => 'جمال مشرق كل يوم',
                ),

            ),
        ),

        
       

    ),
    'location' => array(
        array(
            array(
                'param'    => 'options_page',
                'operator' => '==',
                'value'    => 'shroo-topbar-settings',
            ),
        ),
    ),
) );
