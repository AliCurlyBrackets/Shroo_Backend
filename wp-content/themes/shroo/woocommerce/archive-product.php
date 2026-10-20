<?php
/**
 * WooCommerce archive product template.
 * Uses shop settings from Dashboard › WooCommerce › Shop Settings
 */
get_header();

// صفحة قسم؟ (/product-category/...)
$current_term = is_product_category() ? get_queried_object() : null;

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

// Get dynamic WooCommerce data
$product_categories = array();
$total_product_count = 0;

if ( class_exists( 'WooCommerce' ) ) {
    // Get all product categories (excluding 'uncategorized')
    $product_categories = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'exclude'    => array( get_cat_ID( 'uncategorized' ) ),
    ) );

    if ( is_wp_error( $product_categories ) || empty( $product_categories ) ) {
        $product_categories = array();
    }

    // Get total product count
    $total_product_count = wp_count_posts( 'product' )->publish;
}

// Get products from WooCommerce query
$products = array();
if ( class_exists( 'WooCommerce' ) && have_posts() ) {
    $products = array();
    while ( have_posts() ) {
        the_post();
        $products[] = $GLOBALS['product'];
    }
    rewind_posts();
}
?>

    <main>
      <?php if ( $hero_enabled ) : ?>
      <section class="shop-hero">
        <img alt="" src="<?php echo esc_url( $hero_image_url ); ?>" />
        <div class="ov shop-copy">
          <nav aria-label="مسار التنقل" class="crumb">
            <a href="index.html">الرئيسية</a><svg aria-hidden="true" class="ic ic-chevRight"><use href="#i-chevRight"></use></svg><b><?php echo esc_html( $hero_title ); ?></b>
          </nav>
          <h1><?php echo esc_html( $hero_title ); ?></h1>
          <h2><?php echo esc_html( $hero_subtitle ); ?></h2>
          <p><?php echo esc_html( $hero_desc ); ?></p>
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
        <aside class="shop-side">
          <?php if ( ! empty( $product_categories ) ) : ?>
          <div class="panel">
            <h3><?php esc_html_e( 'تسوقي حسب القسم', 'shroo' ); ?></h3>
            <div id="cat-list">
              <?php if ( $current_term ) : ?>
              <button data-cat="shop" onclick="event.stopPropagation();location.href='<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>'">
              <?php else : ?>
              <button class="active" data-cat="all">
              <?php endif; ?>
                <svg aria-hidden="true" class="ic ic-grid"><use href="#i-grid"></use></svg><span><?php esc_html_e( 'كل المنتجات', 'shroo' ); ?></span><em><?php echo esc_html( $total_product_count ); ?></em>
              </button>
              <?php foreach ( $product_categories as $cat ) : ?>
                <?php $count = isset( $cat->count ) ? $cat->count : 0; ?>
                <?php if ( $current_term && (int) $current_term->term_id === (int) $cat->term_id ) : ?>
                <button class="active" data-cat="all">
                <?php elseif ( $current_term ) : ?>
                <button data-cat="<?php echo esc_attr( $cat->slug ); ?>" onclick="event.stopPropagation();location.href='<?php echo esc_url( get_term_link( $cat ) ); ?>'">
                <?php else : ?>
                <button data-cat="<?php echo esc_attr( $cat->slug ); ?>">
                <?php endif; ?>
                  <svg aria-hidden="true" class="ic ic-grid"><use href="#i-grid"></use></svg><span><?php echo esc_html( $cat->name ); ?></span><em><?php echo esc_html( $count ); ?></em>
                </button>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>
           <div class="panel">
            <h3>نوع البشرة</h3>
            <?php
            // Get product attributes for "skin type" filter dynamically
            $skin_types = array();
            if ( taxonomy_exists( 'pa_skin-type' ) ) {
                $skin_types = get_terms( array(
                    'taxonomy'   => 'pa_skin-type',
                    'hide_empty' => true,
                ) );
                if ( is_wp_error( $skin_types ) ) {
                    $skin_types = array();
                }
            }
            if ( ! empty( $skin_types ) ) :
                foreach ( $skin_types as $skin_term ) :
                    $count = isset( $skin_term->count ) ? $skin_term->count : 0;
            ?>
              <label class="chk">
                <input data-f="skin" type="checkbox" value="<?php echo esc_attr( $skin_term->slug ); ?>" />
                <span class="box"><svg aria-hidden="true" class="ic ic-check"><use href="#i-check"></use></svg></span>
                <?php echo esc_html( $skin_term->name ); ?><em>(<?php echo esc_html( $count ); ?>)</em>
              </label>
            <?php endforeach; endif; ?>
          </div>
          <?php if ( ! empty( $product_categories ) ) : ?>
            <div class="panel">
              <h3><?php esc_html_e( 'الفوائد', 'shroo' ); ?></h3>
              <div id="benefit-list">
                <?php
                // Get product attributes for "benefits" filter dynamically
                $benefits = array();
                if ( taxonomy_exists( 'pa_benefits' ) ) {
                    $benefits = get_terms( array(
                        'taxonomy'   => 'pa_benefits',
                        'hide_empty' => true,
                    ) );
                    if ( is_wp_error( $benefits ) ) {
                        $benefits = array();
                    }
                }
                if ( ! empty( $benefits ) ) :
                    foreach ( $benefits as $benefit_term ) :
                        $count = isset( $benefit_term->count ) ? $benefit_term->count : 0;
                ?>
                  <label class="chk">
                    <input data-f="benefits" type="checkbox" value="<?php echo esc_attr( $benefit_term->slug ); ?>" />
                    <span class="box"><svg aria-hidden="true" class="ic ic-check"><use href="#i-check"></use></svg></span>
                    <?php echo esc_html( $benefit_term->name ); ?><em>(<?php echo esc_html( $count ); ?>)</em>
                  </label>
                <?php endforeach; endif; ?>
              </div>
            </div>
          <?php endif; ?>
          <button class="clear-btn" id="clear" style="margin-top: 12px">
            <svg aria-hidden="true" class="ic ic-refresh"><use href="#i-refresh"></use></svg> <?php esc_html_e( 'مسح كل الفلاتر', 'shroo' ); ?>
          </button>
        </aside>
        <section>
          <div class="toolbar">
            <span id="count"><?php echo esc_html( $total_product_count ); ?> <?php esc_html_e( 'منتج', 'shroo' ); ?></span>
            <label for="sort"><?php esc_html_e( 'ترتيب حسب:', 'shroo' ); ?></label>
            <form method="GET" class="select">
              <?php
              // Preserve existing query vars
              foreach ( $_GET as $key => $value ) {
                  if ( $key !== 'orderby' ) {
                      echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" />';
                  }
              }
              ?>
              <select id="sort" name="orderby" onchange="this.form.submit()">
                <option value="menu_order" <?php selected( isset( $_GET['orderby'] ) ? $_GET['orderby'] : '', 'menu_order' ); ?>><?php esc_html_e( 'الافتراضية', 'shroo' ); ?></option>
                <option value="popularity" <?php selected( isset( $_GET['orderby'] ) ? $_GET['orderby'] : '', 'popularity' ); ?>><?php esc_html_e( 'الأكثر شيوعاً', 'shroo' ); ?></option>
                <option value="rating" <?php selected( isset( $_GET['orderby'] ) ? $_GET['orderby'] : '', 'rating' ); ?>><?php esc_html_e( 'الأعلى تقييماً', 'shroo' ); ?></option>
                <option value="date" <?php selected( isset( $_GET['orderby'] ) ? $_GET['orderby'] : '', 'date' ); ?>><?php esc_html_e( 'الأحدث', 'shroo' ); ?></option>
                <option value="price" <?php selected( isset( $_GET['orderby'] ) ? $_GET['orderby'] : '', 'price' ); ?>><?php esc_html_e( 'السعر: من الأقل للأعلى', 'shroo' ); ?></option>
                <option value="price-desc" <?php selected( isset( $_GET['orderby'] ) ? $_GET['orderby'] : '', 'price-desc' ); ?>><?php esc_html_e( 'السعر: من الأعلى للأقل', 'shroo' ); ?></option>
              </select>
              <svg aria-hidden="true" class="ic ic-chevDown"><use href="#i-chevDown"></use></svg>
            </form>
            <div class="view-toggle">
              <button aria-label="عرض شبكي" class="active" data-view="grid">
                <svg aria-hidden="true" class="ic ic-grid"><use href="#i-grid"></use></svg></button>
              <button aria-label="عرض قائمة" data-view="list">
                <svg aria-hidden="true" class="ic ic-list"><use href="#i-list"></use></svg>
              </button>
            </div>
          </div>
          <div id="shop-grid">
            <?php if ( $products ) : ?>
              <?php foreach ( $products as $product ) : ?>
                <?php
                // Get product categories for filtering
                $product_cats = wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'slugs' ) );
                $cat_data = ( ! is_wp_error( $product_cats ) && ! empty( $product_cats ) ) ? implode( ' ', $product_cats ) : '';
                // Get product attributes for filtering (check if taxonomy exists first)
                $product_benefits = taxonomy_exists( 'pa_benefits' ) ? wp_get_post_terms( $product->get_id(), 'pa_benefits', array( 'fields' => 'slugs' ) ) : array();
                $benefit_data = ( ! is_wp_error( $product_benefits ) && ! empty( $product_benefits ) ) ? implode( ' ', $product_benefits ) : '';
                $product_skin = taxonomy_exists( 'pa_skin-type' ) ? wp_get_post_terms( $product->get_id(), 'pa_skin-type', array( 'fields' => 'slugs' ) ) : array();
                $skin_data = ( ! is_wp_error( $product_skin ) && ! empty( $product_skin ) ) ? implode( ' ', $product_skin ) : '';
                ?>
                <article class="pcard" data-id="<?php echo esc_attr( $product->get_id() ); ?>" data-cat="<?php echo esc_attr( $cat_data ); ?>" data-benefits="<?php echo esc_attr( $benefit_data ); ?>" data-skin="<?php echo esc_attr( $skin_data ); ?>" data-price="<?php echo esc_attr( $product->get_price() ?: 0 ); ?>" data-rating="<?php echo esc_attr( $product->get_average_rating() ?: 0 ); ?>" data-reviews="<?php echo esc_attr( $product->get_review_count() ?: 0 ); ?>">
                  <a class="pcard-img" href="<?php echo esc_url( $product->get_permalink() ); ?>">
                    <?php if ( $product->get_image_id() ) : ?>
                      <img alt="<?php echo esc_attr( $product->get_title() ); ?>" loading="lazy" src="<?php echo esc_url( wp_get_attachment_image_url( $product->get_image_id(), 'full' ) ); ?>" />
                    <?php else : ?>
                      <img alt="<?php echo esc_attr( $product->get_title() ); ?>" loading="lazy" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/placeholder.jpg' ); ?>" />
                    <?php endif; ?>
                  </a>
                  <?php if ( $product->is_featured() ) : ?>
                    <span class="badge"><?php esc_html_e( 'المميزة', 'shroo' ); ?></span>
                  <?php endif; ?>
                  <?php if ( $product->is_on_sale() ) : ?>
                    <?php
                    $regular_price = $product->get_regular_price();
                    $sale_price = $product->get_price();
                    if ( $regular_price && $sale_price ) {
                        $discount = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
                    } else {
                        $discount = 0;
                    }
                    ?>
                    <span class="badge"><?php echo esc_html( 'وفّر ' . $discount . '%' ); ?></span>
                  <?php endif; ?>
                  <button aria-label="أضف للمفضلة" class="wish" data-wish="<?php echo esc_attr( $product->get_id() ); ?>">
                    <svg aria-hidden="true" class="ic ic-heart"><use href="#i-heart"></use></svg>
                  </button>
                  <div class="pcard-body">
                    <h3><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_title() ); ?></a></h3>
                    <?php if ( $product->get_short_description() ) : ?>
                      <p class="sub"><?php echo esc_html( wp_strip_all_tags( $product->get_short_description() ) ); ?></p>
                    <?php endif; ?>
                    <div class="rating">
                      <?php
                      $rating = $product->get_average_rating();
                      $stars = (int) round( $rating );
                      for ( $i = 1; $i <= 5; $i++ ) :
                          $percent = ( $stars >= $i ) ? 100 : 0;
                      ?>
                        <span class="star" style="--f: <?php echo esc_attr( $percent ); ?>%;"><?php echo $stars >= $i ? '★' : '☆'; ?></span>
                      <?php endfor; ?>
                      <span>(<?php echo esc_html( $product->get_review_count() ); ?>)</span>
                    </div>
                    <div class="price">
                      <?php echo $product->get_price_html(); ?>
                    </div>
                    <button class="btn btn-primary btn-block btn-sm" data-add="<?php echo esc_attr( $product->get_id() ); ?>" data-img="<?php echo esc_url( $product->get_image_id() ? wp_get_attachment_image_url( $product->get_image_id(), 'full' ) : get_template_directory_uri() . '/assets/img/placeholder.jpg' ); ?>" data-name="<?php echo esc_attr( $product->get_title() ); ?>" data-price="<?php echo esc_attr( $product->get_price() ?: 0 ); ?>" data-size="<?php echo esc_attr( $product->get_attribute( 'pa_size' ) ?: '' ); ?>" data-sub="<?php echo esc_attr( $product->get_short_description() ? wp_strip_all_tags( $product->get_short_description() ) : '' ); ?>">
                      <svg aria-hidden="true" class="ic ic-cart"><use href="#i-cart"></use></svg> <?php esc_html_e( 'أضف للسلة', 'shroo' ); ?>
                    </button>
                  </div>
                </article>
              <?php endforeach; ?>
              <div class="empty shop-empty" hidden="">
                <svg aria-hidden="true" class="ic ic-search"><use href="#i-search"></use></svg>
                <p><?php esc_html_e( 'لا توجد منتجات تطابق اختياراتك.', 'shroo' ); ?></p>
              </div>
            <?php else : ?>
              <p><?php esc_html_e( 'لا توجد منتجات متاحة حالياً.', 'shroo' ); ?></p>
            <?php endif; ?>
          </div>
        </section>
      </div>
      <?php get_template_part( 'template-parts/offers-section' ); ?>
      <!-- Features Row -->
      <div class="container features-row">
        <div class="feat">
          <svg aria-hidden="true" class="ic ic-leaf"><use href="#i-leaf"></use></svg>
          <div><b><?php esc_html_e( 'جودة فاخرة', 'shroo' ); ?></b><span><?php esc_html_e( 'تركيبات آمنة وفعّالة', 'shroo' ); ?></span></div>
        </div>
        <div class="feat">
          <svg aria-hidden="true" class="ic ic-truck"><use href="#i-truck"></use></svg>
          <div><b><?php esc_html_e( 'شحن سريع وموثوق', 'shroo' ); ?></b><span><?php esc_html_e( 'لجميع مناطق المملكة', 'shroo' ); ?></span></div>
        </div>
        <div class="feat">
          <svg aria-hidden="true" class="ic ic-shield"><use href="#i-shield"></use></svg>
          <div><b><?php esc_html_e( 'دفع آمن', 'shroo' ); ?></b><span><?php esc_html_e( 'حماية 100%', 'shroo' ); ?></span></div>
        </div>
        <div class="feat">
          <svg aria-hidden="true" class="ic ic-headset"><use href="#i-headset"></use></svg>
          <div><b><?php esc_html_e( 'دعم متخصص', 'shroo' ); ?></b><span><?php esc_html_e( 'نحن هنا لمساعدتك', 'shroo' ); ?></span></div>
        </div>
      </div>
    </main>

    <?php get_footer(); ?>