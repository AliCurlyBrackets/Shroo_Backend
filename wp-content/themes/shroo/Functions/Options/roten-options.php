<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register the Roten Steps option page (top-level menu, no submenu)
add_action( 'acf/init', function () {

    // 1) Register the option page shown directly under the WordPress admin menu
    acf_add_options_page( array(
        'page_title'  => __( 'Roten Steps Settings', 'shroo' ),
        'menu_title'  => __( 'Roten Steps', 'shroo' ),
        'menu_slug'   => 'shroo-roten-settings',
        'capability'  => 'edit_posts',
        'icon_url'    => 'dashicons-excerpt-view',
        'position'    => 42,
        'redirect'    => true,
    ) );

    // 2) Register the field group
    if ( function_exists( 'acf_add_local_field_group' ) ) {
        acf_add_local_field_group( array(
            'key'   => 'group_shroo_roten_section',
            'title' => __( 'Roten Section', 'shroo' ),
            'fields' => array(

                // Enable / disable toggle
                array(
                    'key'         => 'field_shroo_roten_enabled',
                    'label'       => __( 'Enable Roten Section', 'shroo' ),
                    'name'        => 'roten_enabled',
                    'type'        => 'true_false',
                    'default_value' => 1,
                ),

                // Section heading
                array(
                    'key'         => 'field_shroo_roten_heading',
                    'label'       => __( 'Section Title', 'shroo' ),
                    'name'        => 'roten_heading',
                    'type'        => 'text',
                    'default_value' => __( 'أكملي روتينك', 'shroo' ),
                ),

                // Section subtitle / description
                array(
                    'key'         => 'field_shroo_roten_subtitle',
                    'label'       => __( 'Section Description', 'shroo' ),
                    'name'        => 'roten_subtitle',
                    'type'        => 'textarea',
                    'rows'        => 3,
                    'default_value' => __( 'روتين بسيط لشعر أصح وبشرة أكثر إشراقاً.', 'shroo' ),
                ),

                // "View all" link
                array(
                    'key'         => 'field_shroo_roten_view_all_link',
                    'label'       => __( 'View All Link', 'shroo' ),
                    'name'        => 'roten_view_all_link',
                    'type'        => 'url',
                    'default_value' => '',
                    'instructions'  => __( 'Text: "عرض الروتين كاملاً" — leave empty to hide the link.', 'shroo' ),
                ),

                // Repeater of Roten steps
                array(
                    'key'         => 'field_shroo_roten_items',
                    'label'       => __( 'Roten Steps', 'shroo' ),
                    'name'        => 'roten_items',
                    'type'        => 'repeater',
                    'button_label' => __( 'Add Step', 'shroo' ),
                    'min'         => 1,
                    'max'         => 8,
                    'layout'      => 'block',
                    'sub_fields'  => array(

                        // Step image
                        array(
                            'key'  => 'field_shroo_roten_item_image',
                            'label' => __( 'Step Image', 'shroo' ),
                            'name'  => 'image',
                            'type'  => 'image',
                            'return_format' => 'id',
                            'preview_size'  => 'medium',
                            'instructions'  => __( 'Image shown for this step.', 'shroo' ),
                        ),

                        // Step number (label)
                        array(
                            'key'   => 'field_shroo_roten_item_step',
                            'label' => __( 'Step Number', 'shroo' ),
                            'name'  => 'step',
                            'type'  => 'text',
                            'default_value' => __( 'الخطوة 1', 'shroo' ),
                        ),

                        // Step title (bold)
                        array(
                            'key'   => 'field_shroo_roten_item_title',
                            'label' => __( 'Step Title', 'shroo' ),
                            'name'  => 'title_text',
                            'type'  => 'text',
                            'default_value' => __( 'العناية بالشعر', 'shroo' ),
                        ),

                        // Step description
                        array(
                            'key'   => 'field_shroo_roten_item_desc',
                            'label' => __( 'Step Description', 'shroo' ),
                            'name'  => 'description',
                            'type'  => 'textarea',
                            'rows' => 3,
                            'default_value' => __( 'تقوية الجذور
وتقليل التساقط.', 'shroo' ),
                        ),

                        // Product link
                        array(
                            'key'  => 'field_shroo_roten_item_link',
                            'label' => __( 'Step Link', 'shroo' ),
                            'name'  => 'link',
                            'type'  => 'url',
                            'instructions' => __( 'Link for the step card. Leave empty for current product placeholder.', 'shroo' ),
                        ),

                    ),
                ),

            ),
            'location' => array(
                array(
                    array(
                        'param'    => 'options_page',
                        'operator' => '==',
                        'value'    => 'shroo-roten-settings',
                    ),
                ),
            ),
        ) );
    }
} );
