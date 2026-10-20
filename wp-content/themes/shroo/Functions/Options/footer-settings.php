<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register the Footer Settings option page (top-level menu, no submenu)
if ( ! function_exists( 'acf_add_options_page' ) || ! function_exists( 'acf_add_local_field_group' ) ) {
    return;
}

add_action( 'acf/init', function () {

    // 1) Register the option page shown directly under the WordPress admin menu
    acf_add_options_page( array(
        'page_title'  => __( 'Footer Settings', 'shroo' ),
        'menu_title'  => __( 'Footer', 'shroo' ),
        'menu_slug'   => 'shroo-footer-settings',
        'capability'  => 'edit_posts',
        'icon_url'    => 'dashicons-admin-site',
        'position'    => 45,
        'redirect'    => true,
    ) );

    // 2) Register the field group
    acf_add_local_field_group( array(
            'key'   => 'group_shroo_footer_settings',
            'title' => __( 'Footer Settings', 'shroo' ),
            'fields' => array(

                // Enable / disable toggle
                array(
                    'key'         => 'field_shroo_footer_enabled',
                    'label'       => __( 'Enable Custom Footer', 'shroo' ),
                    'name'        => 'footer_enabled',
                    'type'        => 'true_false',
                    'default_value' => 1,
                ),

                // Footer brand / logo settings
                array(
                    'key'   => 'field_shroo_footer_brand_group',
                    'label' => __( 'Brand Settings', 'shroo' ),
                    'type'  => 'group',
                    'name'  => 'footer_brand',
                    'sub_fields' => array(

                        // Logo image
                        array(
                            'key'  => 'field_shroo_footer_logo',
                            'label' => __( 'Logo Image', 'shroo' ),
                            'name'  => 'logo',
                            'type'  => 'image',
                            'return_format' => 'id',
                            'preview_size'  => 'medium',
                            'instructions'  => __( 'Footer logo image. Falls back to default assets/img/logo.png', 'shroo' ),
                        ),

                        // Brand description
                        array(
                            'key'   => 'field_shroo_footer_brand_text',
                            'label' => __( 'Brand Description', 'shroo' ),
                            'name'  => 'brand_text',
                            'type'  => 'textarea',
                            'rows'  => 3,
                            'default_value' => __( 'عناية بالشعر والبشرة على يد خبراء يهتمون بكِ. جمال مشرق لإطلالة أكثر إشراقاً.', 'shroo' ),
                        ),
                    ),
                ),

                // Contact Us settings
                array(
                    'key'   => 'field_shroo_footer_contact_group',
                    'label' => __( 'Contact Us', 'shroo' ),
                    'type'  => 'group',
                    'name'  => 'footer_contact',
                    'sub_fields' => array(

                        // Contact heading
                        array(
                            'key'   => 'field_shroo_footer_contact_heading',
                            'label' => __( 'Contact Heading', 'shroo' ),
                            'name'  => 'heading',
                            'type'  => 'text',
                            'default_value' => 'تواصل معنا',
                        ),

                        // Phone number
                        array(
                            'key'  => 'field_shroo_footer_contact_phone',
                            'label' => __( 'Phone Number', 'shroo' ),
                            'name'  => 'phone',
                            'type'  => 'text',
                            'default_value' => '',
                        ),

                        // Email address
                        array(
                            'key'  => 'field_shroo_footer_contact_email',
                            'label' => __( 'Email Address', 'shroo' ),
                            'name'  => 'email',
                            'type'  => 'email',
                            'default_value' => '',
                        ),

                        // Address
                        array(
                            'key'   => 'field_shroo_footer_contact_address',
                            'label' => __( 'Address', 'shroo' ),
                            'name'  => 'address',
                            'type'  => 'textarea',
                            'rows'  => 3,
                            'default_value' => '',
                        ),

                    ),
                ),

                // Footer menus (Footer 1 and Footer 2 are controlled via WP Menus)
                array(
                    'key'   => 'field_shroo_footer_menus_group',
                    'label' => __( 'Footer Menus', 'shroo' ),
                    'type'  => 'group',
                    'name'  => 'footer_menus',
                    'sub_fields' => array(

                        // Footer 1 heading (Quick Links)
                        array(
                            'key'   => 'field_shroo_footer_menu_1_heading',
                            'label' => __( 'Footer Menu 1 Heading', 'shroo' ),
                            'name'  => 'footer_1_heading',
                            'type'  => 'text',
                            'default_value' => 'روابط سريعة',
                            'instructions' => __( 'Heading for the Footer 1 menu (Quick Links).', 'shroo' ),
                        ),

                        // Footer 2 heading (Customer Service)
                        array(
                            'key'   => 'field_shroo_footer_menu_2_heading',
                            'label' => __( 'Footer Menu 2 Heading', 'shroo' ),
                            'name'  => 'footer_2_heading',
                            'type'  => 'text',
                            'default_value' => 'خدمة العملاء',
                            'instructions' => __( 'Heading for the Footer 2 menu (Customer Service).', 'shroo' ),
                        ),

                    ),
                ),

                // Social media links (repeater)
                array(
                    'key'         => 'field_shroo_footer_social_group',
                    'label'       => __( 'Social Media', 'shroo' ),
                    'type'        => 'group',
                    'name'        => 'footer_social',
                    'sub_fields' => array(

                        // Social heading
                        array(
                            'key'   => 'field_shroo_footer_social_heading',
                            'label' => __( 'Social Heading', 'shroo' ),
                            'name'  => 'heading',
                            'type'  => 'text',
                            'default_value' => 'تابعينا',
                        ),

                        // Social handle
                        array(
                            'key'  => 'field_shroo_footer_social_handle',
                            'label' => __( 'Social Handle', 'shroo' ),
                            'name'  => 'handle',
                            'type'  => 'text',
                            'default_value' => '@shroo.radiantcharm',
                            'instructions' => __( 'Display handle text (e.g. @shroo.radiantcharm).', 'shroo' ),
                        ),

                        // Social links repeater
                        array(
                            'key'         => 'field_shroo_footer_social_links',
                            'label'       => __( 'Social Links', 'shroo' ),
                            'name'        => 'links',
                            'type'        => 'repeater',
                            'button_label' => __( 'Add Social Link', 'shroo' ),
                            'min'         => 1,
                            'max'         => 8,
                            'layout'      => 'block',
                            'sub_fields'  => array(

                                // Platform name
                                array(
                                    'key'  => 'field_shroo_footer_social_link_platform',
                                    'label' => __( 'Platform', 'shroo' ),
                                    'name'  => 'platform',
                                    'type'  => 'select',
                                    'choices' => array(
                                        'instagram' => __( 'Instagram', 'shroo' ),
                                        'tiktok'    => __( 'TikTok', 'shroo' ),
                                        'facebook'  => __( 'Facebook', 'shroo' ),
                                        'youtube'   => __( 'YouTube', 'shroo' ),
                                        'twitter'   => __( 'Twitter', 'shroo' ),
                                        'snapchat'  => __( 'Snapchat', 'shroo' ),
                                        'whatsapp'  => __( 'WhatsApp', 'shroo' ),
                                        'telegram'  => __( 'Telegram', 'shroo' ),
                                    ),
                                    'allow_null' => 1,
                                ),

                                // Icon image (upload)
                                array(
                                    'key'  => 'field_shroo_footer_social_link_icon',
                                    'label' => __( 'Icon Image', 'shroo' ),
                                    'name'  => 'icon',
                                    'type'  => 'image',
                                    'return_format' => 'id',
                                    'preview_size'  => 'medium',
                                    'instructions'  => __( 'Upload an icon image for this social link.', 'shroo' ),
                                ),

                                // Link URL
                                array(
                                    'key'  => 'field_shroo_footer_social_link_url',
                                    'label' => __( 'Link URL', 'shroo' ),
                                    'name'  => 'url',
                                    'type'  => 'url',
                                    'placeholder' => 'https://',
                                ),

                            ),
                        ),

                    ),
                ),

                // Payment methods image
                array(
                    'key'  => 'field_shroo_footer_payment_image',
                    'label' => __( 'Payment Methods Image', 'shroo' ),
                    'name'  => 'payment_image',
                    'type'  => 'image',
                    'return_format' => 'id',
                    'preview_size'  => 'medium',
                    'instructions'  => __( 'Image showing accepted payment methods. Falls back to assets/img/payments.jpg', 'shroo' ),
                ),

                // Copyright text
                array(
                    'key'   => 'field_shroo_footer_copyright',
                    'label' => __( 'Copyright Text', 'shroo' ),
                    'name'  => 'copyright',
                    'type'  => 'text',
                    'default_value' => '© 2024 شرو راديانت تشارم. جميع الحقوق محفوظة.',
                ),

                // Sub-copyright text
                array(
                    'key'   => 'field_shroo_footer_sub_copyright',
                    'label' => __( 'Sub Copyright Text', 'shroo' ),
                    'name'  => 'sub_copyright',
                    'type'  => 'text',
                    'default_value' => 'جمال اليوم. غدٌ أكثر إشراقاً.',
                ),

            ),
            'location' => array(
                array(
                    array(
                        'param'    => 'options_page',
                        'operator' => '==',
                        'value'    => 'shroo-footer-settings',
                    ),
                ),
            ),
        ) );

} );
