<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register the Newsletter option page (top-level menu, no submenu)
add_action( 'acf/init', function () {

    // 1) Register the option page shown directly under the WordPress admin menu
    acf_add_options_page( array(
        'page_title'  => __( 'Newsletter Settings', 'shroo' ),
        'menu_title'  => __( 'Newsletter', 'shroo' ),
        'menu_slug'   => 'shroo-newsletter-settings',
        'capability'  => 'edit_posts',
        'icon_url'    => 'dashicons-email',
        'position'    => 44,
        'redirect'    => true,
    ) );

    // 2) Register the field group
    if ( function_exists( 'acf_add_local_field_group' ) ) {
        acf_add_local_field_group( array(
            'key'   => 'group_shroo_newsletter_section',
            'title' => __( 'Newsletter Section', 'shroo' ),
            'fields' => array(

                // Enable / disable toggle
                array(
                    'key'         => 'field_shroo_newsletter_enabled',
                    'label'       => __( 'Enable Newsletter Section', 'shroo' ),
                    'name'        => 'newsletter_enabled',
                    'type'        => 'true_false',
                    'default_value' => 1,
                ),

                // Section heading
                array(
                    'key'         => 'field_shroo_newsletter_heading',
                    'label'       => __( 'Section Title', 'shroo' ),
                    'name'        => 'newsletter_heading',
                    'type'        => 'text',
                    'default_value' => __( 'انضمي لمجتمع الجمال', 'shroo' ),
                ),

                // Section subtitle / description
                array(
                    'key'         => 'field_shroo_newsletter_subtitle',
                    'label'       => __( 'Section Description', 'shroo' ),
                    'name'        => 'newsletter_subtitle',
                    'type'        => 'textarea',
                    'rows'        => 3,
                    'default_value' => __( 'كوني أول من يعرف عن المنتجات الجديدة والعروض الحصرية ونصائح الجمال.', 'shroo' ),
                ),

                // Form button text
                array(
                    'key'         => 'field_shroo_newsletter_button_text',
                    'label'       => __( 'Button Text', 'shroo' ),
                    'name'        => 'newsletter_button_text',
                    'type'        => 'text',
                    'default_value' => __( 'اشتراك', 'shroo' ),
                ),

                // Email input placeholder
                array(
                    'key'         => 'field_shroo_newsletter_placeholder',
                    'label'       => __( 'Email Placeholder', 'shroo' ),
                    'name'        => 'newsletter_placeholder',
                    'type'        => 'text',
                    'default_value' => __( 'بريدك الإلكتروني', 'shroo' ),
                ),

                // Form action URL
                array(
                    'key'         => 'field_shroo_newsletter_form_url',
                    'label'       => __( 'Form Action URL', 'shroo' ),
                    'name'        => 'newsletter_form_url',
                    'type'        => 'url',
                    'default_value' => '',
                    'instructions'  => __( 'URL where the subscription form submits. Leave empty to use the current page (for default handling).', 'shroo' ),
                ),

            ),
            'location' => array(
                array(
                    array(
                        'param'    => 'options_page',
                        'operator' => '==',
                        'value'    => 'shroo-newsletter-settings',
                    ),
                ),
            ),
        ) );
    }
} );
