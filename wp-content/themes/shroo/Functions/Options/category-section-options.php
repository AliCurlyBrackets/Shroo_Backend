<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Load category-section config (field group + option page) if ACF is active
add_action( 'acf/init', function () {
    // Register the option page that controls the Category section (top-level menu)
    acf_add_options_page( array(
        'page_title'  => __( 'Category Section Settings', 'shroo' ),
        'menu_title'  => __( 'Category', 'shroo' ),
        'menu_slug'   => 'shroo-category-settings',
        'capability'  => 'edit_posts',
        'icon_url'    => 'dashicons-category',
        'position'    => 21,
    ) );

    // 2) Register the field group
    if ( function_exists( 'acf_add_local_field_group' ) ) {
        acf_add_local_field_group( array(
            'key'  => 'group_shroo_category_section',
            'title' => __( 'Category Section', 'shroo' ),
            'fields' => array(

                // Enable / disable toggle
                array(
                    'key'   => 'field_shroo_category_enabled',
                    'label' => __( 'Enable Category Section', 'shroo' ),
                    'name'  => 'category_enabled',
                    'type'  => 'true_false',
                    'default_value' => 1,
                ),

                // Section heading
                array(
                    'key'   => 'field_shroo_category_heading',
                    'label' => __( 'Section Title', 'shroo' ),
                    'name'  => 'category_heading',
                    'type'  => 'text',
                    'default_value' => __( 'تسوقي حسب القسم', 'shroo' ),
                ),

                // "View all" link
                array(
                    'key'   => 'field_shroo_category_view_all_link',
                    'label' => __( 'View All Link', 'shroo' ),
                    'name'  => 'category_view_all_link',
                    'type'  => 'url',
                    'default_value' => '',
                ),

                // Repeater of WooCommerce categories
                array(
                    'key'   => 'field_shroo_category_items',
                    'label' => __( 'Category Items', 'shroo' ),
                    'name'  => 'category_items',
                    'type'  => 'repeater',
                    'button_label' => __( 'Add Category', 'shroo' ),
                    'min' => 1,
                    'max' => 12,
                    'layout' => 'block',
                    'sub_fields' => array(

                        // Pick a WooCommerce product category
                        array(
                            'key'   => 'field_shroo_category_select',
                            'label' => __( 'Product Category', 'shroo' ),
                            'name'  => 'product_category',
                            'type'  => 'taxonomy',
                            'taxonomy' => 'product_cat',
                            'return_format' => 'id',
                            'field_type' => 'select',
                            'allow_null' => 1,
                            'multiple' => 0,
                            'add_term' => 0,
                            'save_terms' => 0,
                            'load_terms' => 0,
                            'placeholder' => __( 'Select a product category', 'shroo' ),
                            'instructions' => __( 'Select a WooCommerce product category for this item.', 'shroo' ),
                        ),

                        // Override image (optional, falls back to category image / term meta)
                        array(
                            'key'   => 'field_shroo_category_image',
                            'label' => __( 'Category Image', 'shroo' ),
                            'name'  => 'image',
                            'type'  => 'image',
                            'return_format' => 'id',
                            'preview_size' => 'medium',
                            'instructions' => __( 'If left empty, the category image from WooCommerce will be used.', 'shroo' ),
                        ),

                        // Override title
                        array(
                            'key'   => 'field_shroo_category_title',
                            'label' => __( 'Category Title', 'shroo' ),
                            'name'  => 'title',
                            'type'  => 'text',
                            'instructions' => __( 'Leave blank to use the category name.', 'shroo' ),
                        ),

                        // Override subtitle
                        array(
                            'key'   => 'field_shroo_category_subtitle',
                            'label' => __( 'Category Subtitle', 'shroo' ),
                            'name'  => 'subtitle',
                            'type'  => 'textarea',
                            'rows' => 3,
                            'instructions' => __( 'Optional short description.', 'shroo' ),
                        ),

                        // Position for the overlay copy box
                        array(
                            'key'   => 'field_shroo_category_copy_position',
                            'label' => __( 'Copy Position', 'shroo' ),
                            'name'  => 'copy_position',
                            'type'  => 'number',
                            'default_value' => '62',
                            'append' => '%',
                            'min' => 0,
                            'max' => 100,
                            'instructions' => __( 'Horizontal percentage for the title/subtitle overlay.', 'shroo' ),
                        ),

                    ),
                ),

            ),
            'location' => array(
                array(
                    array(
                        'param'  => 'options_page',
                        'operator' => '==',
                        'value'  => 'shroo-category-settings',
                    ),
                ),
            ),
        ) );
    }
});
