<?php
/**
 * WooCommerce single product template.
 * Uses dynamic data from the global $product object.
 */
get_header();

global $product, $post;

// Ensure we have a valid WC_Product object
if (!$product || !is_a($product, 'WC_Product')) {
    $product = wc_get_product($post->ID);
}

if (!$product) {
    echo '<p>' . esc_html__('المنتج غير متاح.', 'shroo') . '</p>';
    get_footer();
    return;
}

// Get product data dynamically
$product_id = $product->get_id();
$product_title = $product->get_title();
$product_permalink = $product->get_permalink();
$product_price = $product->get_price();
$regular_price = $product->get_regular_price();
$sale_price = $product->get_price();
$on_sale = $product->is_on_sale();
$stock_status = $product->get_stock_status();
$in_stock = $product->is_in_stock();
$featured = $product->is_featured();
$total_sales = $product->get_total_sales();
$rating = $product->get_average_rating();
$review_count = $product->get_review_count();
$short_desc = $product->get_short_description();
$full_desc = $product->get_description();
$image_id = $product->get_image_id();
$image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : get_template_directory_uri() . '/assets/img/product-placeholder.jpg';
$gallery_images = $product->get_gallery_image_ids();
$add_to_cart_url = $product->add_to_cart_url();
$add_to_cart_text = $product->add_to_cart_text();

// Get product categories
$product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'all'));
if (is_wp_error($product_categories)) $product_categories = array();

// Get product attributes
$product_attributes = $product->get_attributes();

// السمات الظاهرة (نوع البشرة، الفوائد...) مع قيمها — اللي ملهاش قيم مش بتظهر
$attr_tabs = array();
foreach ($product_attributes as $attr_name => $attr) {
    if (strpos($attr_name, 'pa_') !== 0 || !$attr->get_visible()) continue;
    $options = array();
    if ($attr->is_taxonomy()) {
        $terms = $attr->get_terms();
        if (is_array($terms)) {
            foreach ($terms as $term) {
                $options[] = is_object($term) ? $term->name : $term;
            }
        }
    } else {
        $options = $attr->get_options();
    }
    if (empty($options)) continue;
    $attr_tabs[str_replace('pa_', '', $attr_name)] = array(
        'label'   => wc_attribute_label($attr_name),
        'options' => $options,
    );
}

// Get upsell products
$upsells = $product->get_upsell_ids();

// Get related products
$related_ids = $product->get_related();
$related_products = array();
if (!empty($related_ids)) {
    foreach ($related_ids as $rel_id) {
        $rel_product = wc_get_product($rel_id);
        if ($rel_product) $related_products[] = $rel_product;
    }
}
?>
<main class="container">
  <nav aria-label="مسار التنقل" class="crumbs" id="crumbs">
    <a href="<?php echo esc_url(home_url()); ?>"><?php esc_html_e('الرئيسية', 'shroo'); ?></a>
    <svg aria-hidden="true" class="ic ic-chevRight"><use href="#i-chevRight"></use></svg>
    <?php foreach ($product_categories as $cat): ?>
      <a href="<?php echo esc_url(get_term_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a>
      <svg aria-hidden="true" class="ic ic-chevRight"><use href="#i-chevRight"></use></svg>
    <?php endforeach; ?>
    <span><?php echo esc_html($product_title); ?></span>
  </nav>

  <!-- المنتج -->
  <section class="pdp">
    <div class="g-thumbs" id="g-thumbs">
      <?php if (!empty($gallery_images)): ?>
        <?php foreach ($gallery_images as $index => $img_id): ?>
          <?php
          $thumb_url = wp_get_attachment_image_url($img_id, 'full');
          $is_active = ($index === 0) ? ' class="active"' : '';
          ?>
          <button aria-label="صورة <?php echo ($index + 1); ?>"<?php echo $is_active; ?> data-full="<?php echo esc_url($thumb_url); ?>">
            <img alt="" src="<?php echo esc_url(wp_get_attachment_image_url($img_id, 'thumbnail')); ?>" />
          </button>
        <?php endforeach; ?>
      <?php else: ?>
        <button aria-label="صورة 1" class="active" data-full="<?php echo esc_url($image_url); ?>">
          <img alt="" src="<?php echo esc_url($image_url); ?>" />
        </button>
      <?php endif; ?>
    </div>
    <div class="g-main" id="g-main">
      <img alt="<?php echo esc_attr($product_title); ?>" src="<?php echo esc_url($image_url); ?>" />
      <div class="g-circle">
        <svg aria-hidden="true" class="ic ic-leaf"><use href="#i-leaf"></use></svg>
        <?php esc_html_e('منتجات أصلية 100%', 'shroo'); ?>
      </div>
      <button aria-label="الصورة السابقة" class="g-arrow" id="g-prev">
        <svg aria-hidden="true" class="ic ic-chevLeft"><use href="#i-chevLeft"></use></svg>
      </button>
      <button aria-label="الصورة التالية" class="g-arrow" id="g-next">
        <svg aria-hidden="true" class="ic ic-chevRight"><use href="#i-chevRight"></use></svg>
      </button>
    </div>
    <div class="pinfo">
      <div class="pinfo-top">
        <?php if ($featured): ?>
          <span class="pill" id="p-badge"><?php esc_html_e('الأكثر مبيعاً', 'shroo'); ?></span>
        <?php endif; ?>
        <span class="stock"><?php echo $in_stock ? esc_html__('متوفر', 'shroo') : esc_html__('نفد المخزون', 'shroo'); ?></span>
      </div>
      <h1 id="p-title"><?php echo esc_html($product_title); ?></h1>
      <?php if (!empty($product_attributes)): ?>
        <?php foreach ($product_attributes as $attr_name => $attr): ?>
          <?php if (strpos($attr_name, 'pa_') === 0 && $attr->get_visible()): ?>
            <p class="headline" id="p-<?php echo esc_attr(str_replace('pa_', '', $attr_name)); ?>">
              <?php
              // For taxonomy attributes, use get_terms(); for text attributes, use get_options()
              if ($attr->is_taxonomy()) {
                  $terms = $attr->get_terms();
                  $values = array();
                  if (is_array($terms)) {
                      foreach ($terms as $term) {
                          $values[] = is_object($term) ? $term->name : $term;
                      }
                  }
                  echo esc_html(implode(', ', $values));
              } else {
                  echo esc_html(implode(', ', $attr->get_options()));
              }
              ?>
            </p>
          <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>
      <div class="prating" id="p-rating">
        <?php
        $full_stars = floor($rating / 1);
        $fraction = $rating - $full_stars;
        $half = $fraction >= 0.5;
        ?>
        <span aria-label="<?php echo esc_attr($rating . ' من 5'); ?>" class="stars">
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <?php
            $p = ($i <= $full_stars) ? 100 : (($i == $full_stars + 1 && $half) ? 50 : 0);
            ?>
            <span class="star" style="--f: <?php echo $p; ?>%;">★</span>
          <?php endfor; ?>
        </span>
        <b><?php echo esc_html($rating); ?></b>
        <span>(<?php echo esc_html($review_count . ' ' . __('تقييم', 'shroo')); ?>)</span>
      </div>
      <p class="short" id="p-short">
        <?php echo $short_desc ?: esc_html__('لا يوجد وصف مختصر.', 'shroo'); ?>
      </p>
      <div class="pprice" id="p-price">
        <strong><?php echo $product->get_price_html(); ?></strong>
        <?php if ($on_sale && $regular_price && $sale_price): ?>
          <s><?php echo esc_html($regular_price . ' ر.س'); ?></s>
          <?php $discount = round((($regular_price - $sale_price) / $regular_price) * 100); ?>
          <span class="save"><?php echo esc_html__('وفّر ' . $discount . '%', 'shroo'); ?></span>
        <?php endif; ?>
      </div>

      <?php
      // ---------- حزم التوفير (من ACF: حزم التوفير في صفحة تعديل المنتج) ----------
      $bundle_offers = function_exists('shroo_get_bundle_offers') ? shroo_get_bundle_offers($product) : array();
      if (!empty($bundle_offers)):
          $offers_heading = get_field('offers_heading', $product_id) ?: 'اختاري حزمة التوفير';
          $opts_label     = trim((string) get_field('offers_options_label', $product_id));
          $opts_list      = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) get_field('offers_options_list', $product_id))));
          $currency       = html_entity_decode(get_woocommerce_currency_symbol(), ENT_QUOTES, 'UTF-8');
      ?>
      <!-- حزم التوفير -->
      <div class="offers" id="offers" data-currency="<?php echo esc_attr($currency); ?>" data-decimals="<?php echo esc_attr(wc_get_price_decimals()); ?>">
        <div class="offers-head">
          <span class="offers-num">01</span>
          <svg aria-hidden="true" class="ic ic-sparkle"><use href="#i-sparkle"></use></svg>
          <h3><?php echo esc_html($offers_heading); ?></h3>
          <span class="offers-line"></span>
        </div>

        <?php foreach ($bundle_offers as $i => $offer): ?>
          <label class="offer">
            <input type="radio" name="offer" value="<?php echo esc_attr($i + 1); ?>"
              data-qty="<?php echo esc_attr($offer['qty']); ?>"
              data-total="<?php echo esc_attr($offer['total']); ?>"
              data-old="<?php echo esc_attr($offer['old']); ?>"
              <?php checked($offer['is_default']); ?> />
            <?php if ($offer['tag']): ?>
              <span class="offer-tag"><?php echo esc_html($offer['tag']); ?></span>
            <?php endif; ?>
            <span class="offer-row">
              <span class="offer-dot"></span>
              <span class="offer-title"><?php echo esc_html($offer['title']); ?></span>
              <span class="offer-price">
                <strong><?php echo wp_kses_post(wc_price($offer['total'])); ?></strong>
                <?php if ($offer['old'] > $offer['total']): ?>
                  <s><?php echo wp_kses_post(wc_price($offer['old'])); ?></s>
                <?php endif; ?>
              </span>
            </span>
            <?php if ($opts_label && $opts_list): ?>
              <span class="offer-opts">
                <span class="opts-label"><?php echo esc_html($opts_label); ?></span>
                <?php for ($n = 1; $n <= $offer['qty']; $n++): ?>
                  <span class="opt-line">
                    <b>#<?php echo esc_html($n); ?></b>
                    <select aria-label="<?php echo esc_attr($opts_label . ' ' . $n); ?>">
                      <?php foreach ($opts_list as $opt): ?>
                        <option><?php echo esc_html($opt); ?></option>
                      <?php endforeach; ?>
                    </select>
                  </span>
                <?php endfor; ?>
              </span>
            <?php endif; ?>
          </label>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <div class="buy-row">
        <div class="qty">
          <button aria-label="زيادة" data-q="1">
            <svg aria-hidden="true" class="ic ic-plus"><use href="#i-plus"></use></svg>
          </button>
          <input aria-label="الكمية" id="qty" inputmode="numeric" value="1" />
          <button aria-label="تقليل" data-q="-1">
            <svg aria-hidden="true" class="ic ic-minus"><use href="#i-minus"></use></svg>
          </button>
        </div>
        <?php if ($in_stock): ?>
          <button class="btn btn-primary" id="add-main"
            data-id="<?php echo esc_attr($product_id); ?>"
            data-img="<?php echo esc_url($image_url); ?>"
            data-name="<?php echo esc_attr($product_title); ?>"
            data-price="<?php echo esc_attr($product->get_price() ?: $regular_price ?: 0); ?>"
          >
            <svg aria-hidden="true" class="ic ic-cart"><use href="#i-cart"></use></svg>
            <?php esc_html_e('أضف للسلة', 'shroo'); ?>
          </button>
        <?php else: ?>
          <button class="btn btn-primary" disabled>
            <svg aria-hidden="true" class="ic ic-bell"><use href="#i-bell"></use></svg>
            <?php esc_html_e('نفد المخزون', 'shroo'); ?>
          </button>
        <?php endif; ?>
      </div>
      <button class="btn-buy" id="buy-now">
        <svg aria-hidden="true" class="ic ic-bolt"><use href="#i-bolt"></use></svg>
        <?php esc_html_e('اشتري الآن', 'shroo'); ?>
      </button>
      <a class="ship-box" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">
        <svg aria-hidden="true" class="ic ic-truck"><use href="#i-truck"></use></svg>
        <div><b><?php esc_html_e('شحن موثوق', 'shroo'); ?></b><span><?php esc_html_e('يصلك طلبك خلال 2–4 أيام', 'shroo'); ?></span></div>
        <svg aria-hidden="true" class="ic ic-chevRight chev"><use href="#i-chevRight"></use></svg>
      </a>
      <div class="ptrust">
        <div>
          <svg aria-hidden="true" class="ic ic-shield"><use href="#i-shield"></use></svg><?php esc_html_e('منتجات أصلية', 'shroo'); ?>
        </div>
        <div>
          <svg aria-hidden="true" class="ic ic-card"><use href="#i-card"></use></svg><?php esc_html_e('دفع آمن', 'shroo'); ?>
        </div>
        <div>
          <svg aria-hidden="true" class="ic ic-returns"><use href="#i-returns"></use></svg><?php esc_html_e('إرجاع سهل', 'shroo'); ?>
        </div>
        <div>
          <svg aria-hidden="true" class="ic ic-headset"><use href="#i-headset"></use></svg><?php esc_html_e('دعم مخصص', 'shroo'); ?>
        </div>
      </div>
    </div>
  </section>

  <!-- التبويبات -->
  <div class="tabs" role="tablist">
    <button class="active" data-tab="t-desc"><?php esc_html_e('الوصف', 'shroo'); ?></button>
    <?php foreach ($attr_tabs as $slug => $tab): ?>
      <button data-tab="t-<?php echo esc_attr($slug); ?>"><?php echo esc_html($tab['label']); ?></button>
    <?php endforeach; ?>
    <button data-tab="t-how"><?php esc_html_e('طريقة الاستخدام', 'shroo'); ?></button>
    <button data-tab="t-faq"><?php esc_html_e('الأسئلة الشائعة', 'shroo'); ?></button>
    <button data-tab="t-rev" id="p-reviews-tab">
      <?php echo esc_html__('التقييمات (' . $review_count . ')', 'shroo'); ?>
    </button>
  </div>

  <!-- Description Tab -->
  <div class="tab-panel" id="t-desc">
    <div class="desc-grid">
      <div>
        <h2><?php esc_html_e('وصف المنتج', 'shroo'); ?></h2>
        <p id="p-desc"><?php echo $full_desc ?: esc_html__('لا يوجد وصف متاح.', 'shroo'); ?></p>
      </div>
      <div class="ingr">
        <img alt="رسم توضيحي للمنتج" src="<?php echo esc_url($image_url); ?>" />
        <ul>
          <li>
            <svg aria-hidden="true" class="ic ic-check"><use href="#i-check"></use></svg>
            <div><b><?php esc_html_e('جودة عالية', 'shroo'); ?></b><span><?php esc_html_e('مصنوع بمكونات فعّالة', 'shroo'); ?></span></div>
          </li>
          <li>
            <svg aria-hidden="true" class="ic ic-check"><use href="#i-check"></use></svg>
            <div><b><?php esc_html_e('نتائج مثبتة', 'shroo'); ?></b><span><?php esc_html_e('توترع على نتائج طبيعية', 'shroo'); ?></span></div>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Attribute Tabs (نوع البشرة، الفوائد...) -->
  <?php foreach ($attr_tabs as $slug => $tab): ?>
  <div class="tab-panel" hidden="" id="t-<?php echo esc_attr($slug); ?>">
    <div class="desc-grid">
      <div>
        <h2><?php echo esc_html($tab['label']); ?></h2>
      </div>
      <div class="ingr">
        <img alt="<?php echo esc_attr($tab['label']); ?>" src="<?php echo esc_url($image_url); ?>" />
        <ul>
          <?php foreach ($tab['options'] as $option): ?>
            <li>
              <svg aria-hidden="true" class="ic ic-check"><use href="#i-check"></use></svg>
              <div><b><?php echo esc_html($option); ?></b></div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
  <?php endforeach; ?>

  <!-- How to Use Tab -->
<div class="tab-panel" hidden="" id="t-how">
    <?php
    $usage = get_post_meta($product_id, '_wc_shroo_usage', true);
    if ($usage):
        echo wp_kses_post(nl2br($usage));
    else:
        echo '<ol>';
        echo '<li>' . esc_html__('اتبعي التعليمات على المنتج.', 'shroo') . '</li>';
        echo '<li>' . esc_html__('استخدم المنتج كما هو موصى به.', 'shroo') . '</li>';
        echo '<li>' . esc_html__('للحصول على أفضل النتائج، استخدمه بانتظام.', 'shroo') . '</li>';
        echo '</ol>';
    endif;
    ?>
  </div>

  <!-- FAQ Tab -->
  <div class="tab-panel" hidden="" id="t-faq">
    <div class="faq">
      <?php
      $faqs = get_post_meta($product_id, '_wc_shroo_faqs', true);
      $faqs = $faqs ? json_decode($faqs, true) : array();
      if (!empty($faqs)):
          foreach ($faqs as $faq):
      ?>
        <div class="faq-item">
          <button>
            <?php echo esc_html($faq['question']); ?>
            <svg aria-hidden="true" class="ic ic-chevDown"><use href="#i-chevDown"></use></svg>
          </button>
          <p><?php echo esc_html($faq['answer']); ?></p>
        </div>
      <?php
          endforeach;
      else:
      ?>
        <div class="faq-item">
          <button>
            <?php esc_html_e('كيف أستخدم المنتج؟', 'shroo'); ?>
            <svg aria-hidden="true" class="ic ic-chevDown"><use href="#i-chevDown"></use></svg>
          </button>
          <p><?php esc_html_e('اتبعي التعليمات المذكورة على العبوة واستخدمه يومياً لأفضل النتائج.', 'shroo'); ?></p>
        </div>
        <div class="faq-item">
          <button>
            <?php esc_html_e('هل المنتج آمن؟', 'shroo'); ?>
            <svg aria-hidden="true" class="ic ic-chevDown"><use href="#i-chevDown"></use></svg>
          </button>
          <p><?php esc_html_e('نعم، جميع منتجاتنا آمنة ومختبرة من قبل خبراء.', 'shroo'); ?></p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Reviews Tab -->
  <div class="tab-panel" hidden="" id="t-rev">
    <div class="grid3" id="tab-reviews-list">
      <?php
      $comments = get_comments(array(
        'post_id' => $product_id,
        'status' => 'approve',
        'order' => 'DESC',
        'number' => 10,
      ));
      if (!empty($comments)):
          foreach ($comments as $comment):
              $review_rating = intval(get_comment_meta($comment->comment_ID, 'rating', true));
              $reviewer_avatar = get_avatar_url($comment->user_id ?: $comment->comment_author_email);
      ?>
          <div class="review">
            <img alt="<?php echo esc_attr($comment->comment_author); ?>" src="<?php echo esc_url($reviewer_avatar); ?>" />
            <div>
              <?php
              $full_stars = floor($review_rating);
              $half = ($review_rating - $full_stars) >= 0.5;
              ?>
              <span aria-label="<?php echo esc_attr($review_rating . ' من 5'); ?>" class="stars">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <?php
                  $p = ($i <= $full_stars) ? 100 : (($i == $full_stars + 1 && $half) ? 50 : 0);
                  ?>
                  <span class="star" style="--f: <?php echo $p; ?>%;">★</span>
                <?php endfor; ?>
              </span>
              <p><?php echo esc_html($comment->comment_content); ?></p>
              <span class="who">
                <b><?php echo esc_html($comment->comment_author); ?></b>
                <svg aria-hidden="true" class="ic ic-check vb"><use href="#i-check"></use></svg>
                <?php esc_html_e('مشترية موثّقة', 'shroo'); ?>
              </span>
            </div>
          </div>
      <?php endforeach;
      else: ?>
        <p><?php esc_html_e('لا توجد تقييمات بعد.', 'shroo'); ?></p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Frequently Bought Together -->
  <?php if (!empty($upsells)): ?>
    <section class="fbt">
      <div>
        <h2><?php esc_html_e('يُشترى معاً غالباً', 'shroo'); ?></h2>
        <p><?php esc_html_e('أكملي روتين العناية بشعرك ووفّري أكثر', 'shroo'); ?></p>
        <div id="fbt-items">
          <?php foreach ($upsells as $upsell_id): ?>
            <?php $upsell_product = wc_get_product($upsell_id); if (!$upsell_product) continue; ?>
            <label class="fbt-item">
              <img alt="" src="<?php echo esc_url($upsell_product->get_image_url()); ?>" />
              <div>
                <b><?php echo esc_html($upsell_product->get_title()); ?></b>
                <small><?php echo esc_html(strip_tags($upsell_product->get_short_description() ?: '')); ?></small>
                <strong><?php echo $upsell_product->get_price_html(); ?></strong>
              </div>
              <input type="checkbox" checked
                data-id="<?php echo esc_attr($upsell_id); ?>"
                data-img="<?php echo esc_url($upsell_product->get_image_url()); ?>"
                data-name="<?php echo esc_attr($upsell_product->get_title()); ?>"
                data-price="<?php echo esc_attr($upsell_product->get_price()); ?>"
              />
              <span class="box">
                <svg aria-hidden="true" class="ic ic-check"><use href="#i-check"></use></svg>
              </span>
            </label>
            <span class="fbt-plus">
              <svg aria-hidden="true" class="ic ic-plus"><use href="#i-plus"></use></svg>
            </span>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="fbt-deal">
        <h3><?php esc_html_e('عرض الباقة الخاص', 'shroo'); ?></h3>
        <strong id="fbt-total"><?php esc_html_e('احسب الإجمالي', 'shroo'); ?></strong>
        <button class="btn btn-primary btn-block" id="fbt-add">
          <svg aria-hidden="true" class="ic ic-cart"><use href="#i-cart"></use></svg>
          <?php esc_html_e('أضف الباقة للسلة', 'shroo'); ?>
        </button>
      </div>
    </section>
  <?php endif; ?>

  <!-- You May Also Like / Related Products -->
  <?php if (!empty($related_products)): ?>
    <section class="sec">
      <div class="sec-head">
        <div>
          <h2><?php esc_html_e('أكملي روتينك', 'shroo'); ?></h2>
          <p><?php esc_html_e('اكتشفي المزيد من مجموعة العناية بالشعر', 'shroo'); ?></p>
        </div>
      </div>
      <div class="carousel">
        <button aria-label="السابق" class="c-arrow" id="r-prev">
          <svg aria-hidden="true" class="ic ic-chevLeft"><use href="#i-chevLeft"></use></svg>
        </button>
        <div class="track" id="routine-track">
          <?php foreach ($related_products as $rel_product): ?>
            <article class="pcard" data-id="<?php echo esc_attr($rel_product->get_id()); ?>">
              <a class="pcard-img" href="<?php echo esc_url($rel_product->get_permalink()); ?>">
                <?php if ($rel_product->get_image_id()): ?>
                  <img alt="<?php echo esc_attr($rel_product->get_title()); ?>" loading="lazy" src="<?php echo esc_url(wp_get_attachment_image_url($rel_product->get_image_id(), 'full')); ?>" />
                <?php endif; ?>
              </a>
              <?php if ($rel_product->is_featured()): ?>
                <span class="badge"><?php esc_html_e('مميز', 'shroo'); ?></span>
              <?php endif; ?>
              <button aria-label="أضف للمفضلة" class="wish" data-wish="<?php echo esc_attr($rel_product->get_id()); ?>">
                <svg aria-hidden="true" class="ic ic-heart"><use href="#i-heart"></use></svg>
              </button>
              <div class="pcard-body">
                <h3><a href="<?php echo esc_url($rel_product->get_permalink()); ?>"><?php echo esc_html($rel_product->get_title()); ?></a></h3>
                <?php if ($rel_product->get_short_description()): ?>
                  <p class="sub"><?php echo esc_html(wp_strip_all_tags($rel_product->get_short_description())); ?></p>
                <?php endif; ?>
                <div class="rating">
                  <?php
                  $rel_rating = $rel_product->get_average_rating();
                  $rel_full = floor($rel_rating);
                  $rel_half = ($rel_rating - $rel_full) >= 0.5;
                  ?>
                  <span aria-label="<?php echo esc_attr($rel_rating . ' من 5'); ?>" class="stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                      <?php
                      $p = ($i <= $rel_full) ? 100 : (($i == $rel_full + 1 && $rel_half) ? 50 : 0);
                      ?>
                      <span class="star" style="--f: <?php echo $p; ?>%;">★</span>
                    <?php endfor; ?>
                  </span>
                  <span>(<?php echo esc_html($rel_product->get_review_count()); ?>)</span>
                </div>
                <div class="price"><?php echo $rel_product->get_price_html(); ?></div>
                <button class="btn btn-primary btn-block btn-sm" data-add="<?php echo esc_attr($rel_product->get_id()); ?>" data-img="<?php echo esc_url($rel_product->get_image_id() ? wp_get_attachment_image_url($rel_product->get_image_id(), 'full') : ''); ?>" data-name="<?php echo esc_attr($rel_product->get_title()); ?>" data-price="<?php echo esc_attr($rel_product->get_price() ?: 0); ?>" data-sub="<?php echo esc_attr($rel_product->get_short_description() ? wp_strip_all_tags($rel_product->get_short_description()) : ''); ?>">
                  <svg aria-hidden="true" class="ic ic-cart"><use href="#i-cart"></use></svg>
                  <?php esc_html_e('أضف للسلة', 'shroo'); ?>
                </button>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
        <button aria-label="التالي" class="c-arrow" id="r-next">
          <svg aria-hidden="true" class="ic ic-chevRight"><use href="#i-chevRight"></use></svg>
        </button>
      </div>
    </section>
  <?php endif; ?>

  <!-- Customer Reviews -->
  <section class="sec" id="reviews">
    <div class="rev-head">
     
      <div id="p-reviews-summary">
        <?php if ($review_count > 0): ?>
          <?php
          $rel_full = floor($rating);
          $rel_half = ($rating - $rel_full) >= 0.5;
          ?>
          <span aria-label="<?php echo esc_attr($rating . ' من 5'); ?>" class="stars">
            <?php for ($i = 1; $i <= 5; $i++): ?>
              <?php
              $p = ($i <= $rel_full) ? 100 : (($i == $rel_full + 1 && $rel_half) ? 50 : 0);
              ?>
              <span class="star" style="--f: <?php echo $p; ?>%;">★</span>
            <?php endfor; ?>
          </span>
          <span><?php echo esc_html($rating . ' من 5 (' . $review_count . ' ' . __('تقييم', 'shroo') . ')'); ?></span>
        <?php endif; ?>
      </div>
    </div>
    <div class="grid3" id="p-reviews">
      <?php
      $top_comments = get_comments(array(
        'post_id' => $product_id,
        'status' => 'approve',
        'order' => 'DESC',
        'number' => 6,
      ));
      if (!empty($top_comments)):
          foreach ($top_comments as $comment):
              $review_rating = intval(get_comment_meta($comment->comment_ID, 'rating', true));
              $reviewer_avatar = get_avatar_url($comment->user_id ?: $comment->comment_author_email);
      ?>
          <div class="review">
            <img alt="<?php echo esc_attr($comment->comment_author); ?>" src="<?php echo esc_url($reviewer_avatar); ?>" />
            <div>
              <?php
              $full_stars = floor($review_rating);
              $half = ($review_rating - $full_stars) >= 0.5;
              ?>
              <span aria-label="<?php echo esc_attr($review_rating . ' من 5'); ?>" class="stars">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <?php
                  $p = ($i <= $full_stars) ? 100 : (($i == $full_stars + 1 && $half) ? 50 : 0);
                  ?>
                  <span class="star" style="--f: <?php echo $p; ?>%;">★</span>
                <?php endfor; ?>
              </span>
              <p><?php echo esc_html($comment->comment_content); ?></p>
              <span class="who">
                <b><?php echo esc_html($comment->comment_author); ?></b>
                <svg aria-hidden="true" class="ic ic-check vb"><use href="#i-check"></use></svg>
                <?php esc_html_e('مشترية موثّقة', 'shroo'); ?>
              </span>
            </div>
          </div>
      <?php endforeach;
      endif; ?>
    </div>
  </section>

  <!-- Customer Reviews Section -->
  <?php get_template_part( 'template-parts/review-section' ); ?>
</main>

<?php get_footer(); ?>