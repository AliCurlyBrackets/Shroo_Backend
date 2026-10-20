<?php

include("Functions/Style_Theme/style_theme.php");
include("Functions/Options/options-init.php");
include("Functions/Options/category-options-init.php");

// Fallback shim so theme templates don't fatally error when ACF is inactive
if (! function_exists('get_field')) {
    function get_field($selector, $post_id = false, $format_value = true)
    {
        return false;
    }
}

// Fallback shim for WooCommerce helpers (used in footer fallbacks)
if (! function_exists('wc_get_shop_page_url')) {
    function wc_get_shop_page_url()
    {
        return home_url('/');
    }
}

// Fallback shims for other WooCommerce functions
if (! function_exists('wc_get_checkout_url')) {
    function wc_get_checkout_url()
    {
        return wc_get_page_permalink('checkout') ?: home_url('/');
    }
}

if (! function_exists('wc_get_cart_page_id')) {
    function wc_get_cart_page_id()
    {
        return false;
    }
}

if (! function_exists('wc_get_page_permalink')) {
    function wc_get_page_permalink($page)
    {
        $mapping = array(
            'shop'      => 'woocommerce_shop_page_id',
            'cart'      => 'woocommerce_cart_page_id',
            'checkout'  => 'woocommerce_checkout_page_id',
            'myaccount' => 'woocommerce_myaccount_page_id',
        );
        if (isset($mapping[$page])) {
            $id = get_option($mapping[$page]);
            if ($id) {
                return get_permalink($id);
            }
        }
        return '';
    }
}

/**
 * Check if the store has free shipping configured (with or without a minimum amount).
 * Returns the minimum amount for free shipping, or false if free shipping is not available.
 */
function shroo_get_free_shipping_threshold() {
    if (!class_exists('WC_Shipping_Zones')) {
        return false;
    }
    $zones = WC_Shipping_Zones::get_zones();
    foreach ($zones as $zone) {
        if (!isset($zone['shipping_methods']) || !is_array($zone['shipping_methods'])) {
            continue;
        }
        foreach ($zone['shipping_methods'] as $method) {
            if (is_a($method, 'WC_Shipping_Method')) {
                if ($method->id === 'free_shipping') {
                    $min = $method->get_option('min_amount', 0);
                    if ($min > 0) {
                        return floatval($min);
                    }
                    // Free shipping method exists but with no minimum
                    return 0;
                }
            } elseif (is_object($method) && isset($method->id) && $method->id === 'free_shipping') {
                // Legacy/shipping zone method
                $min = isset($method->min_amount) ? $method->min_amount : 0;
                return floatval($min);
            }
        }
    }
    // Also check legacy flat rate free shipping settings
    $legacy_min = get_option('woocommerce_free_shipping_min_amount', false);
    return $legacy_min !== false ? floatval($legacy_min) : false;
}

wp_enqueue_script(
    'shroo-main',
    get_template_directory_uri() . '/assets/js/main.js',
    array(),
    filemtime(get_template_directory() . '/assets/js/main.js'),
    true
);

// Register navigation menus
add_action('after_setup_theme', 'shroo_register_menus');
function shroo_register_menus()
{
    register_nav_menus(array(
        'main_menu'     => __('Main Menu', 'shroo'),
        'footer_menu_1' => __('Footer 1', 'shroo'),
        'footer_menu_2' => __('Footer 2', 'shroo'),
    ));
}

/**
 * Fallback menu when no menu is assigned to the main_menu location.
 */
function shroo_main_menu_fallback($args = array()) {
    $shop_url = wc_get_page_permalink('shop') ?: home_url('/');
    $cart_url = wc_get_page_permalink('cart') ?: home_url('/');
    $account_url = wc_get_page_permalink('myaccount') ?: home_url('/');

    $output = '<ul class="nav-menu">';
    $output .= '<li><a class="active" href="' . esc_url(home_url('/')) . '">' . esc_html__('الرئيسية', 'shroo') . '</a></li>';
    $output .= '<li class="has-dd">';
    $output .= '<a href="' . esc_url($shop_url) . '">' . esc_html__('المتجر', 'shroo') . '<svg aria-hidden="true" class="ic ic-chevDown"><use href="#i-chevDown"></use></svg></a>';
    $output .= '<div class="dd">';
    // Get product categories dynamically
    if (class_exists('WC_Product_Category')) {
        $categories = get_terms(array('taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => 6));
        foreach ($categories as $cat) {
            $cat_url = get_term_link($cat);
            $output .= '<a href="' . esc_url($cat_url) . '">' . esc_html($cat->name) . '</a>';
        }
    }
    $output .= '</div>';
    $output .= '</li>';
    $output .= '<li><a href="' . esc_url($account_url) . '">' . esc_html__('حسابي', 'shroo') . '</a></li>';
    $output .= '<li><a href="' . esc_url($cart_url) . '">' . esc_html__('السلة', 'shroo') . '</a></li>';
    $output .= '</ul>';
    return $output;
}


add_action('after_setup_theme', function () {
    add_theme_support('woocommerce');
});

function shroo_page_key()
{
    if (is_front_page()) {
        return 'home';
    }
    if (function_exists('is_shop') && (is_shop() || is_product_taxonomy())) {
        return 'shop';
    }
    if (function_exists('is_product') && is_product()) {
        return 'product';
    }
    return '';
}



// صفحات الأقسام تستخدم نفس قالب صفحة المتجر
add_filter( 'template_include', function ( $template ) {
    if ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) {
        $shop_template = locate_template( 'woocommerce/archive-product.php' );
        if ( $shop_template ) {
            return $shop_template;
        }
    }
    return $template;
}, 99 );

// صفحة المفضلة (?wish=1): كل المنتجات في صفحة واحدة عشان الفلتر يلاقيهم
add_filter( 'loop_shop_per_page', function ( $per_page ) {
    return isset( $_GET['wish'] ) ? -1 : $per_page;
}, 20 );