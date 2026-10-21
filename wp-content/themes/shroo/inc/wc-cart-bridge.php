<?php
/**
 * ربط سلة الموقع (الدروار وأزرار "أضف للسلة") بسلة ووكومرس الحقيقية + أسعار حزم التوفير.
 *
 * التركيب: حط الملف ده في  shroo/inc/wc-cart-bridge.php
 * وضيف السطر ده في functions.php (بعد سطر acf-bundle-offers.php):
 *   require_once get_template_directory() . '/inc/wc-cart-bridge.php';
 *
 * ومعاه ملف  assets/js/wc-cart.js  (بيتحمّل لوحده من هنا).
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** حد الشحن المجاني اللي بيظهر في شريط الدروار (غيّره بالفلتر لو حبيت). */
function shroo_free_shipping_min() {
    return (float) apply_filters( 'shroo_free_shipping_min', 150 );
}

/** سعر كنص عادي بعملة ووكومرس (من غير HTML). */
function shroo_plain_price( $amount ) {
    return html_entity_decode( wp_strip_all_tags( wc_price( $amount ) ), ENT_QUOTES, 'UTF-8' );
}

/** سعر القطعة الفعلي لعنصر في السلة (سعر الحزمة لو موجود). */
function shroo_cart_item_unit_price( $cart_item ) {
    if ( isset( $cart_item['shroo_unit_price'] ) ) {
        return (float) $cart_item['shroo_unit_price'];
    }
    return (float) $cart_item['data']->get_price();
}

/** بيانات السلة اللي الدروار بيعرضها. */
function shroo_cart_payload() {
    $cart     = function_exists( 'WC' ) ? WC()->cart : null;
    $items    = array();
    $subtotal = 0;

    if ( $cart ) {
        foreach ( $cart->get_cart() as $key => $ci ) {
            $p = isset( $ci['data'] ) ? $ci['data'] : null;
            if ( ! $p ) {
                continue;
            }
            $unit  = shroo_cart_item_unit_price( $ci );
            $qty   = (int) $ci['quantity'];
            $name  = $p->get_name();
            if ( ! empty( $ci['shroo_offer_tag'] ) ) {
                $name .= ' — ' . $ci['shroo_offer_tag'];
            }
            $img = $p->get_image_id() ? wp_get_attachment_image_url( $p->get_image_id(), 'thumbnail' ) : '';

            $items[] = array(
                'key'   => $key,
                'id'    => (int) $ci['product_id'],
                'name'  => $name,
                'img'   => $img ?: wc_placeholder_img_src( 'thumbnail' ),
                'url'   => $p->get_permalink(),
                'qty'   => $qty,
                'price' => shroo_plain_price( $unit ),
            );
            $subtotal += $unit * $qty;
        }
    }

    $left = max( 0, shroo_free_shipping_min() - $subtotal );

    return array(
        'count'        => $cart ? (int) $cart->get_cart_contents_count() : 0,
        'items'        => $items,
        'subtotal'     => $subtotal,
        'subtotalText' => shroo_plain_price( $subtotal ),
        'left'         => $left,
        'leftText'     => shroo_plain_price( $left ),
    );
}

/* ---------- تحميل wc-cart.js + بيانات السلة الحالية ---------- */
add_action( 'wp_enqueue_scripts', function () {
    if ( ! function_exists( 'WC' ) || ! class_exists( 'WC_AJAX' ) ) {
        return;
    }
    $file = get_template_directory() . '/assets/js/wc-cart.js';
    if ( ! file_exists( $file ) ) {
        return;
    }
    wp_enqueue_script(
        'shroo-wc-cart',
        get_template_directory_uri() . '/assets/js/wc-cart.js',
        array(),
        filemtime( $file ),
        true
    );
    wp_localize_script( 'shroo-wc-cart', 'shrooCart', array(
        'endpoint'        => WC_AJAX::get_endpoint( 'shroo_cart' ),
        'checkout'        => wc_get_checkout_url(),
        'freeShippingMin' => shroo_free_shipping_min(),
        'cart'            => shroo_cart_payload(),
    ) );
}, 30 );

/* ---------- AJAX: إضافة / تعديل كمية / حذف ---------- */
add_action( 'wc_ajax_shroo_cart', 'shroo_cart_ajax' );
function shroo_cart_ajax() {
    if ( null === WC()->cart && function_exists( 'wc_load_cart' ) ) {
        wc_load_cart();
    }
    $cart = WC()->cart;
    $op   = isset( $_POST['op'] ) ? sanitize_key( wp_unslash( $_POST['op'] ) ) : 'get';

    if ( 'add' === $op ) {
        $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
        $product    = wc_get_product( $product_id );

        if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
            wp_send_json_error( array( 'message' => 'المنتج غير متاح حالياً' ) );
        }
        if ( $product->is_type( 'variable' ) ) {
            wp_send_json_error( array( 'message' => 'اختاري الخيارات من صفحة المنتج', 'redirect' => $product->get_permalink() ) );
        }

        $qty       = isset( $_POST['qty'] ) ? max( 1, absint( $_POST['qty'] ) ) : 1;
        $item_data = array();
        $offer_no  = isset( $_POST['offer'] ) ? absint( $_POST['offer'] ) : 0;

        // سعر الحزمة بيتحسب هنا من ACF (مش من المتصفح)
        if ( $offer_no && function_exists( 'shroo_get_bundle_offers' ) ) {
            $offers = shroo_get_bundle_offers( $product );
            if ( isset( $offers[ $offer_no - 1 ] ) ) {
                $o   = $offers[ $offer_no - 1 ];
                $qty = (int) $o['qty'];

                $item_data['shroo_offer_qty']  = (int) $o['qty'];
                $item_data['shroo_unit_price'] = $o['total'] / $o['qty'];
                $item_data['shroo_offer_tag']  = $o['tag'] ? $o['tag'] : $o['title'];

                $picks = isset( $_POST['picks'] ) ? (array) wp_unslash( $_POST['picks'] ) : array();
                $picks = array_values( array_filter( array_map( 'sanitize_text_field', array_slice( $picks, 0, $o['qty'] ) ) ) );
                if ( $picks && function_exists( 'get_field' ) ) {
                    $item_data['shroo_picks']       = $picks;
                    $item_data['shroo_picks_label'] = get_field( 'offers_options_label', $product_id ) ?: 'الاختيار';
                }
            }
        }

        $key = $cart->add_to_cart( $product_id, $qty, 0, array(), $item_data );
        if ( ! $key ) {
            $errors = wc_get_notices( 'error' );
            wc_clear_notices();
            $msg = $errors ? wp_strip_all_tags( is_array( $errors[0] ) ? $errors[0]['notice'] : $errors[0] ) : 'تعذّرت الإضافة للسلة';
            wp_send_json_error( array( 'message' => $msg ) );
        }
    } elseif ( 'step' === $op ) {
        $key = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
        $d   = isset( $_POST['d'] ) ? (int) $_POST['d'] : 0;
        $ci  = $cart->get_cart_item( $key );
        if ( $ci ) {
            // عنصر الحزمة بيزيد ويقل بعدد قطع الحزمة
            $step = ! empty( $ci['shroo_offer_qty'] ) ? (int) $ci['shroo_offer_qty'] : 1;
            $cart->set_quantity( $key, max( $step, (int) $ci['quantity'] + ( $d * $step ) ) );
        }
    } elseif ( 'remove' === $op ) {
        $key = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
        $cart->remove_cart_item( $key );
    }

    $cart->calculate_totals();
    wc_clear_notices();
    wp_send_json_success( shroo_cart_payload() );
}

/* ---------- سعر الحزمة في السلة والدفع ---------- */
add_action( 'woocommerce_before_calculate_totals', function ( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
        return;
    }
    foreach ( $cart->get_cart() as $ci ) {
        if ( isset( $ci['shroo_unit_price'] ) ) {
            $ci['data']->set_price( (float) $ci['shroo_unit_price'] );
        }
    }
}, 20 );

/* ---------- اسم العرض والاختيارات تحت المنتج في السلة والدفع ---------- */
add_filter( 'woocommerce_get_item_data', function ( $data, $ci ) {
    if ( ! empty( $ci['shroo_offer_tag'] ) ) {
        $data[] = array( 'key' => 'العرض', 'value' => $ci['shroo_offer_tag'] );
    }
    if ( ! empty( $ci['shroo_picks'] ) ) {
        $label = ! empty( $ci['shroo_picks_label'] ) ? $ci['shroo_picks_label'] : 'الاختيار';
        $parts = array();
        foreach ( $ci['shroo_picks'] as $i => $pick ) {
            $parts[] = '#' . ( $i + 1 ) . ': ' . $pick;
        }
        $data[] = array( 'key' => $label, 'value' => implode( '، ', $parts ) );
    }
    return $data;
}, 10, 2 );

/* ---------- حفظ العرض والاختيارات في الطلب (بتظهر في لوحة التحكم) ---------- */
add_action( 'woocommerce_checkout_create_order_line_item', function ( $item, $cart_item_key, $values ) {
    if ( ! empty( $values['shroo_offer_tag'] ) ) {
        $item->add_meta_data( 'العرض', $values['shroo_offer_tag'] );
    }
    if ( ! empty( $values['shroo_picks'] ) ) {
        $label = ! empty( $values['shroo_picks_label'] ) ? $values['shroo_picks_label'] : 'الاختيار';
        foreach ( $values['shroo_picks'] as $i => $pick ) {
            $item->add_meta_data( $label . ' #' . ( $i + 1 ), $pick );
        }
    }
}, 10, 3 );