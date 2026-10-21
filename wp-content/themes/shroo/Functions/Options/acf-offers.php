<?php
/**
 * حزم التوفير (Bundle Offers) — حقول ACF لكل منتج + تحميل ملف الـ JS في صفحة المنتج.
 *
 * التركيب: حط الملف ده في  shroo/inc/acf-bundle-offers.php
 * وضيف السطر ده في functions.php:
 *   require_once get_template_directory() . '/inc/acf-bundle-offers.php';
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ---------- حقول ACF في صفحة تعديل المنتج ---------- */
add_action( 'acf/init', function () {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( array(
        'key'      => 'group_shroo_bundle_offers',
        'title'    => 'حزم التوفير',
        'position' => 'normal',
        'style'    => 'default',
        'fields'   => array(

            array(
                'key'           => 'field_shroo_offers_enabled',
                'label'         => 'تفعيل حزم التوفير',
                'name'          => 'offers_enabled',
                'type'          => 'true_false',
                'ui'            => 1,
                'default_value' => 0,
            ),

            array(
                'key'               => 'field_shroo_offers_heading',
                'label'             => 'عنوان السيكشن',
                'name'              => 'offers_heading',
                'type'              => 'text',
                'default_value'     => 'اختاري حزمة التوفير',
                'conditional_logic' => array( array( array( 'field' => 'field_shroo_offers_enabled', 'operator' => '==', 'value' => '1' ) ) ),
            ),

            array(
                'key'               => 'field_shroo_offers_options_label',
                'label'             => 'اسم الاختيار لكل قطعة',
                'name'              => 'offers_options_label',
                'type'              => 'text',
                'instructions'      => 'مثال: الحجم، اللون، الرائحة. سيبه فاضي لو مفيش اختيارات.',
                'default_value'     => 'الحجم',
                'wrapper'           => array( 'width' => '40' ),
                'conditional_logic' => array( array( array( 'field' => 'field_shroo_offers_enabled', 'operator' => '==', 'value' => '1' ) ) ),
            ),

            array(
                'key'               => 'field_shroo_offers_options_list',
                'label'             => 'الاختيارات',
                'name'              => 'offers_options_list',
                'type'              => 'textarea',
                'rows'              => 4,
                'new_lines'         => '',
                'instructions'      => 'كل اختيار في سطر لوحده. مثال:<br>150 مل<br>250 مل',
                'wrapper'           => array( 'width' => '60' ),
                'conditional_logic' => array( array( array( 'field' => 'field_shroo_offers_enabled', 'operator' => '==', 'value' => '1' ) ) ),
            ),

            array(
                'key'               => 'field_shroo_offers_items',
                'label'             => 'العروض',
                'name'              => 'offers_items',
                'type'              => 'repeater',
                'layout'            => 'block',
                'button_label'      => 'إضافة عرض',
                'min'               => 1,
                'max'               => 6,
                'collapsed'         => 'field_shroo_offer_tag',
                'conditional_logic' => array( array( array( 'field' => 'field_shroo_offers_enabled', 'operator' => '==', 'value' => '1' ) ) ),
                'sub_fields'        => array(
                    array(
                        'key'           => 'field_shroo_offer_tag',
                        'label'         => 'اسم العرض',
                        'name'          => 'tag',
                        'type'          => 'text',
                        'instructions'  => 'الشريط اللي فوق الكارت. مثال: عرض اليوم، الأكثر طلباً، عرض الصحاب',
                        'wrapper'       => array( 'width' => '34' ),
                    ),
                    array(
                        'key'           => 'field_shroo_offer_qty',
                        'label'         => 'عدد القطع',
                        'name'          => 'qty',
                        'type'          => 'number',
                        'required'      => 1,
                        'min'           => 1,
                        'max'           => 20,
                        'default_value' => 1,
                        'wrapper'       => array( 'width' => '22' ),
                    ),
                    array(
                        'key'          => 'field_shroo_offer_total',
                        'label'        => 'سعر العرض (الإجمالي)',
                        'name'         => 'total',
                        'type'         => 'number',
                        'required'     => 1,
                        'min'          => 0,
                        'step'         => '0.01',
                        'instructions' => 'سعر كل القطع مع بعض',
                        'wrapper'      => array( 'width' => '22' ),
                    ),
                    array(
                        'key'          => 'field_shroo_offer_old',
                        'label'        => 'السعر قبل الخصم',
                        'name'         => 'old',
                        'type'         => 'number',
                        'min'          => 0,
                        'step'         => '0.01',
                        'instructions' => 'فاضي = سعر المنتج العادي × العدد',
                        'wrapper'      => array( 'width' => '22' ),
                    ),
                    array(
                        'key'          => 'field_shroo_offer_title',
                        'label'        => 'نص العرض',
                        'name'         => 'title',
                        'type'         => 'text',
                        'instructions' => 'فاضي = يتكتب تلقائياً. مثال: قطعتين بسعر 160 بدلاً من 240',
                        'wrapper'      => array( 'width' => '78' ),
                    ),
                    array(
                        'key'     => 'field_shroo_offer_default',
                        'label'   => 'مختار تلقائياً',
                        'name'    => 'is_default',
                        'type'    => 'true_false',
                        'ui'      => 1,
                        'wrapper' => array( 'width' => '22' ),
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array( 'param' => 'post_type', 'operator' => '==', 'value' => 'product' ),
            ),
        ),
    ) );
} );

/* ---------- تحميل bundle-offers.js في صفحة المنتج بس ---------- */
add_action( 'wp_enqueue_scripts', function () {
    if ( ! function_exists( 'is_product' ) || ! is_product() ) {
        return;
    }
    $file = get_template_directory() . '/assets/js/bundle-offers.js';
    if ( file_exists( $file ) ) {
        wp_enqueue_script(
            'shroo-bundle-offers',
            get_template_directory_uri() . '/assets/js/bundle-offers.js',
            array(),
            filemtime( $file ),
            true
        );
    }
}, 20 );

/* ---------- Helper: يجهّز بيانات العروض للقالب ---------- */
if ( ! function_exists( 'shroo_get_bundle_offers' ) ) {
    function shroo_get_bundle_offers( $product ) {
        if ( ! function_exists( 'get_field' ) || ! $product ) {
            return array();
        }
        $pid = $product->get_id();
        if ( ! get_field( 'offers_enabled', $pid ) ) {
            return array();
        }
        $rows = get_field( 'offers_items', $pid );
        if ( empty( $rows ) || ! is_array( $rows ) ) {
            return array();
        }

        $unit_regular = (float) ( $product->get_regular_price() ?: $product->get_price() );
        $offers       = array();
        $has_default  = false;

        foreach ( $rows as $row ) {
            $qty   = max( 1, (int) ( isset( $row['qty'] ) ? $row['qty'] : 1 ) );
            $total = (float) ( isset( $row['total'] ) ? $row['total'] : 0 );
            if ( $total <= 0 ) {
                continue;
            }
            $old = ( isset( $row['old'] ) && '' !== $row['old'] ) ? (float) $row['old'] : $unit_regular * $qty;

            $title = isset( $row['title'] ) ? trim( $row['title'] ) : '';
            if ( '' === $title ) {
                $count = ( 1 === $qty ) ? 'قطعة واحدة' : ( 2 === $qty ? 'قطعتين' : $qty . ' قطع' );
                $title = $count . ' بسعر ' . wc_format_localized_price( $total );
                if ( $old > $total ) {
                    $title .= ' بدلاً من ' . wc_format_localized_price( $old );
                }
            }

            $is_default = ! empty( $row['is_default'] ) && ! $has_default;
            if ( $is_default ) {
                $has_default = true;
            }

            $offers[] = array(
                'tag'        => isset( $row['tag'] ) ? $row['tag'] : '',
                'qty'        => $qty,
                'total'      => $total,
                'old'        => $old,
                'title'      => $title,
                'is_default' => $is_default,
            );
        }

        if ( $offers && ! $has_default ) {
            $offers[0]['is_default'] = true;
        }
        return $offers;
    }
}