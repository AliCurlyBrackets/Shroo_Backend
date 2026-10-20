<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register the Reviews option page (top-level menu, no submenu)
add_action( 'acf/init', function () {

    // 1) Register the option page shown directly under the WordPress admin menu
    acf_add_options_page( array(
        'page_title'  => __( 'Reviews Settings', 'shroo' ),
        'menu_title'  => __( 'Reviews', 'shroo' ),
        'menu_slug'   => 'shroo-reviews-settings',
        'capability'  => 'edit_posts',
        'icon_url'    => 'dashicons-format-chat',
        'position'    => 43,
        'redirect'    => true,
    ) );

    // 2) Register the field group
    if ( function_exists( 'acf_add_local_field_group' ) ) {
        acf_add_local_field_group( array(
            'key'   => 'group_shroo_reviews_section',
            'title' => __( 'Reviews Section', 'shroo' ),
            'fields' => array(

                // Enable / disable toggle
                array(
                    'key'         => 'field_shroo_reviews_enabled',
                    'label'       => __( 'Enable Reviews Section', 'shroo' ),
                    'name'        => 'reviews_enabled',
                    'type'        => 'true_false',
                    'default_value' => 1,
                ),

                // Section heading
                array(
                    'key'         => 'field_shroo_reviews_heading',
                    'label'       => __( 'Section Title', 'shroo' ),
                    'name'        => 'reviews_heading',
                    'type'        => 'text',
                    'default_value' => __( 'أشخاص حقيقيون، نتائج حقيقية', 'shroo' ),
                ),

                // Section subtitle / description
                array(
                    'key'         => 'field_shroo_reviews_subtitle',
                    'label'       => __( 'Section Description', 'shroo' ),
                    'name'        => 'reviews_subtitle',
                    'type'        => 'textarea',
                    'rows'        => 3,
                    'default_value' => __( 'موثوق من آلاف العميلات السعيدات.', 'shroo' ),
                ),

                // "View all" link
                array(
                    'key'         => 'field_shroo_reviews_view_all_link',
                    'label'       => __( 'View All Link', 'shroo' ),
                    'name'        => 'reviews_view_all_link',
                    'type'        => 'url',
                    'default_value' => '',
                    'instructions'  => __( 'Text shown: "عرض كل التقييمات". Leave empty to hide the link.', 'shroo' ),
                ),

                // Repeater of reviews
                array(
                    'key'         => 'field_shroo_reviews_items',
                    'label'       => __( 'Reviews', 'shroo' ),
                    'name'        => 'reviews_items',
                    'type'        => 'repeater',
                    'button_label' => __( 'Add Review', 'shroo' ),
                    'min'         => 1,
                    'max'         => 12,
                    'layout'      => 'block',
                    'sub_fields'  => array(

                        // Reviewer avatar image
                        array(
                            'key'  => 'field_shroo_reviews_item_avatar',
                            'label' => __( 'Avatar Image', 'shroo' ),
                            'name'  => 'avatar',
                            'type'  => 'image',
                            'return_format' => 'id',
                            'preview_size'  => 'medium',
                            'instructions'  => __( 'Avatar photo of the reviewer.', 'shroo' ),
                        ),

                        // Reviewer name
                        array(
                            'key'  => 'field_shroo_reviews_item_name',
                            'label' => __( 'Reviewer Name', 'shroo' ),
                            'name'  => 'name',
                            'type'  => 'text',
                            'default_value' => 'سارة أ.',
                        ),

                        // Rating (1–5)
                        array(
                            'key'  => 'field_shroo_reviews_item_rating',
                            'label' => __( 'Rating', 'shroo' ),
                            'name'  => 'rating',
                            'type'  => 'number',
                            'default_value' => 5,
                            'min' => 1,
                            'max' => 5,
                            'step' => 1,
                            'append' => '★',
                        ),

                        // Review text
                        array(
                            'key'  => 'field_shroo_reviews_item_text',
                            'label' => __( 'Review Text', 'shroo' ),
                            'name'  => 'text',
                            'type'  => 'textarea',
                            'rows' => 4,
                            'default_value' => 'شعري صار أكثف وأصح بعد أسابيع قليلة بس. أحب شه!',
                        ),

                    ),
                ),

            ),
            'location' => array(
                array(
                    array(
                        'param'    => 'options_page',
                        'operator' => '==',
                        'value'    => 'shroo-reviews-settings',
                    ),
                ),
            ),
        ) );
    }
} );
