<?php
/**
 * WooCommerce taxonomy archive template (product category pages).
 * Uses shop settings from Dashboard › WooCommerce › Shop Settings
 */
get_header();

$shop_settings = get_option( 'shroo_shop_settings_options' );
$shop_settings = $shop_settings ?: array();

$hero_enabled   = isset( $shop_settings['shop_hero_enabled'] ) ? (bool) $shop_settings['shop_hero_enabled'] : true;
$hero_title     = isset( $shop_settings['shop_hero_title'] ) ? $shop_settings['shop_hero_title'] : 'منتجاتنا';
$hero_subtitle  = isset( $shop_settings['shop_hero_subtitle'] ) ? $shop_settings['shop_hero_subtitle'] : 'عناية فاخرة لجمالك الطبيعي';
$hero_desc      = isset( $shop_settings['shop_hero_description'] ) ? $shop_settings['shop_hero_description'] : 'اكتشفي مجموعتنا الكاملة من حلول العناية بالشعر والبشرة، المصنوعة بمكونات فعّالة لنتائج حقيقية.';
$hero_image_id  = isset( $shop_settings['shop_hero_image'] ) ? $shop_settings['shop_hero_image'] : '';
$hero_image_url = $hero_image_id ? wp_get_attachment_image_url( $hero_image_id, 'full' ) : get_template_directory_uri() . '/assets/img/shop-hero.jpg';
$badges_json    = isset( $shop_settings['shop_hero_badges'] ) ? $shop_settings['shop_hero_badges'] : '';

// Parse badges from JSON
$badges = array();
if ( $badges_json ) {
    $badges = json_decode( $badges_json, true );
    if ( ! is_array( $badges ) ) {
        $badges = array();
    }
}

// Default badges if none configured
if ( empty( $badges ) ) {
    $badges = array(
        array( 'icon' => 'ic-leaf',    'label' => 'جودة فاخرة',         'description' => 'تركيبات آمنة وفعّالة' ),
        array( 'icon' => 'ic-truck',   'label' => 'شحن سريع وموثوق',    'description' => 'لجميع مناطق المملكة' ),
        array( 'icon' => 'ic-shield',  'label' => 'دفع آمن',             'description' => 'حماية 100%' ),
        array( 'icon' => 'ic-headset', 'label' => 'دعم متخصص',           'description' => 'نحن هنا لمساعدتك' ),
    );
}

// Get current term info
$term = get_queried_object();
$term_name = $term ? $term->name : $hero_title;
$term_description = $term ? $term->description : '';
?>

    <main>
      <?php if ( $hero_enabled ) : ?>
      <section class="shop-hero">
        <img alt="" src="<?php echo esc_url( $hero_image_url ); ?>" />
        <div class="ov shop-copy">
          <nav aria-label="مسار التنقل" class="crumb">
            <a href="index.html">الرئيسية</a><svg aria-hidden="true" class="ic ic-chevRight"><use href="#i-chevRight"></use></svg
            ><a href="shop.html">المتجر</a><svg aria-hidden="true" class="ic ic-chevRight"><use href="#i-chevRight"></use></svg><b><?php echo esc_html( $term_name ); ?></b>
          </nav>
          <h1><?php echo esc_html( $term_name ); ?></h1>
          <h2><?php echo esc_html( $hero_subtitle ); ?></h2>
          <?php if ( $term_description ) : ?>
            <p><?php echo esc_html( $term_description ); ?></p>
          <?php else : ?>
            <p><?php echo esc_html( $hero_desc ); ?></p>
          <?php endif; ?>
          <div class="sf">
            <?php foreach ( $badges as $badge ) : ?>
              <div>
                <svg aria-hidden="true" class="ic <?php echo esc_attr( $badge['icon'] ); ?>"><use href="#<?php echo esc_attr( $badge['icon'] ); ?>"></use></svg><b><?php echo esc_html( $badge['label'] ); ?></b><span><?php echo esc_html( $badge['description'] ); ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
      <?php endif; ?>

      <div class="container shop-layout">
        <div id="primary" class="content-area">
          <?php
          woocommerce_content();
          ?>
        </div>
      </div>
    </main>

    <?php get_footer(); ?>
