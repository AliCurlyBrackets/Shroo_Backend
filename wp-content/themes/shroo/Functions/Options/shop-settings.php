<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register the Shop Settings option page (submenu under WooCommerce)
add_action( 'admin_menu', function () {
    // Only add submenu if WooCommerce is active
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    add_submenu_page(
        'woocommerce',                              // Parent slug (WooCommerce)
        __( 'Shop Settings', 'shroo' ),             // Page title
        __( 'Shop Settings', 'shroo' ),             // Menu title
        'manage_options',                           // Capability
        'shroo-shop-settings',                      // Menu slug
        'shroo_render_shop_settings_page'          // Callback function
    );
} );

/**
 * Render the Shop Settings admin page
 */
function shroo_render_shop_settings_page() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        echo '<div class="notice notice-warning"><p>' . esc_html__( 'ACF is required for these settings to work.', 'shroo' ) . '</p></div>';
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Shop Settings', 'shroo' ); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'shroo_shop_settings' );
            do_settings_sections( 'shroo_shop_settings' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register the Settings and Fields
add_action( 'admin_init', function () {
    if ( ! function_exists( 'register_setting' ) ) {
        return;
    }

    register_setting( 'shroo_shop_settings', 'shroo_shop_settings_options' );

    add_settings_section(
        'shroo_shop_hero_section',
        __( 'Shop Hero Banner Settings', 'shroo' ),
        null,
        'shroo_shop_settings'
    );

    // Hero Banner Settings
    $options = get_option( 'shroo_shop_settings_options' );
    $options = $options ?: array();

    add_settings_field(
        'shop_hero_enabled',
        __( 'Enable Hero Banner', 'shroo' ),
        'shroo_shop_hero_enabled_field',
        'shroo_shop_settings',
        'shroo_shop_hero_section'
    );

    add_settings_field(
        'shop_hero_title',
        __( 'Hero Title', 'shroo' ),
        'shroo_shop_hero_title_field',
        'shroo_shop_settings',
        'shroo_shop_hero_section'
    );

    add_settings_field(
        'shop_hero_subtitle',
        __( 'Hero Subtitle', 'shroo' ),
        'shroo_shop_hero_subtitle_field',
        'shroo_shop_settings',
        'shroo_shop_hero_section'
    );

    add_settings_field(
        'shop_hero_description',
        __( 'Hero Description', 'shroo' ),
        'shroo_shop_hero_description_field',
        'shroo_shop_settings',
        'shroo_shop_hero_section'
    );

    add_settings_field(
        'shop_hero_image',
        __( 'Hero Background Image', 'shroo' ),
        'shroo_shop_hero_image_field',
        'shroo_shop_settings',
        'shroo_shop_hero_section'
    );

    add_settings_field(
        'shop_hero_view_all_link',
        __( 'View All Link', 'shroo' ),
        'shroo_shop_hero_view_all_link_field',
        'shroo_shop_settings',
        'shroo_shop_hero_section'
    );

    add_settings_field(
        'shop_hero_badge_items',
        __( 'Hero Badge Items', 'shroo' ),
        'shroo_shop_hero_badge_items_field',
        'shroo_shop_settings',
        'shroo_shop_hero_section'
    );
} );

/**
 * Field rendering functions
 */
function shroo_shop_hero_enabled_field() {
    $options = get_option( 'shroo_shop_settings_options' );
    $enabled = isset( $options['shop_hero_enabled'] ) ? (bool) $options['shop_hero_enabled'] : true;
    echo '<input type="checkbox" name="shroo_shop_settings_options[shop_hero_enabled]" value="1" ' . checked( 1, $enabled, false ) . ' />';
}

function shroo_shop_hero_title_field() {
    $options = get_option( 'shroo_shop_settings_options' );
    $value = isset( $options['shop_hero_title'] ) ? $options['shop_hero_title'] : 'منتجاتنا';
    echo '<input type="text" name="shroo_shop_settings_options[shop_hero_title]" value="' . esc_attr( $value ) . '" class="regular-text" />';
}

function shroo_shop_hero_subtitle_field() {
    $options = get_option( 'shroo_shop_settings_options' );
    $value = isset( $options['shop_hero_subtitle'] ) ? $options['shop_hero_subtitle'] : 'عناية فاخرة لجمالك الطبيعي';
    echo '<input type="text" name="shroo_shop_settings_options[shop_hero_subtitle]" value="' . esc_attr( $value ) . '" class="regular-text" />';
}

function shroo_shop_hero_description_field() {
    $options = get_option( 'shroo_shop_settings_options' );
    $value = isset( $options['shop_hero_description'] ) ? $options['shop_hero_description'] : 'اكتشفي مجموعتنا الكاملة من حلول العناية بالشعر والبشرة، المصنوعة بمكونات فعّالة لنتائج حقيقية.';
    echo '<textarea name="shroo_shop_settings_options[shop_hero_description]" rows="4" class="large-text">' . esc_textarea( $value ) . '</textarea>';
}

function shroo_shop_hero_image_field() {
    $options = get_option( 'shroo_shop_settings_options' );
    $image_id = isset( $options['shop_hero_image'] ) ? $options['shop_hero_image'] : '';
    $image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';
    $value = isset( $options['shop_hero_image'] ) ? $options['shop_hero_image'] : '';
    echo '<div style="display:flex;align-items:center;gap:10px;">';
    if ( $image_url ) {
        echo '<img src="' . esc_url( $image_url ) . '" style="max-width:150px;" />';
    }
    echo '<input type="hidden" name="shroo_shop_settings_options[shop_hero_image]" id="shop_hero_image_id" value="' . esc_attr( $value ) . '" />';
    echo '<button type="button" class="button" id="shop_hero_image_button">' . ( $image_url ? 'Change Image' : 'Upload Image' ) . '</button>';
    echo '</div>';
    ?>
    <script>
    jQuery(document).ready(function($) {
        $('#shop_hero_image_button').on('click', function(e) {
            e.preventDefault();
            var customUploader = wp.media({
                title: 'Select Image',
                button: { text: 'Use this image' },
                multiple: false
            });
            customUploader.on('select', function(e) {
                var attachment = customUploader.state().get('selection').first().toJSON();
                $('#shop_hero_image_id').val(attachment.id);
                // Update preview
                var thumb = '<img src="' + (attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url) + '" style="max-width:150px;">';
                $('#shop_hero_image_button').before(thumb);
            });
            customUploader.open();
        });
    });
    </script>
    <?php
}

function shroo_shop_hero_view_all_link_field() {
    $options = get_option( 'shroo_shop_settings_options' );
    $value = isset( $options['shop_hero_view_all_link'] ) ? $options['shop_hero_view_all_link'] : 'shop.html';
    echo '<input type="url" name="shroo_shop_settings_options[shop_hero_view_all_link]" value="' . esc_attr( $value ) . '" class="regular-text" />';
}

function shroo_shop_hero_badge_items_field() {
    $options = get_option( 'shroo_shop_settings_options' );
    $items = isset( $options['shop_hero_badges'] ) ? $options['shop_hero_badges'] : '';
    // We'll render this as a simple textarea with JSON format
    echo '<p>' . esc_html__( 'Add badge items in JSON format (array of {icon, label, description}):', 'shroo' ) . '</p>';
    $default_json = json_encode( array(
        array( 'icon' => 'ic-leaf',    'label' => 'جودة فاخرة', 'description' => 'تركيبات آمنة وفعّالة' ),
        array( 'icon' => 'ic-truck',   'label' => 'شحن سريع وموثوق', 'description' => 'لجميع مناطق المملكة' ),
        array( 'icon' => 'ic-shield',  'label' => 'دفع آمن', 'description' => 'حماية 100%' ),
        array( 'icon' => 'ic-headset', 'label' => 'دعم متخصص', 'description' => 'نحن هنا لمساعدتك' ),
    ), JSON_UNESCAPED_UNICODE );
    echo '<textarea name="shroo_shop_settings_options[shop_hero_badges]" rows="6" class="large-text">' . esc_textarea( $items ?: $default_json ) . '</textarea>';
    echo '<p class="description">' . esc_html__( 'Example: [{"icon":"ic-leaf","label":"جودة فاخرة","description":"تركيبات آمنة وفعّالة"},...]', 'shroo' ) . '</p>';
}

// Enqueue media uploader script
add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( $hook !== 'woocommerce_page_shroo-shop-settings' ) {
        return;
    }
    wp_enqueue_media();
} );
