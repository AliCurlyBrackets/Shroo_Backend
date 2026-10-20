<?php
function shroo_render_wc_product_card( $product, $show_badge = false, $badge_text = '' ) {
    $product_id = $product->get_id();
    $permalink  = get_permalink( $product_id );
    $title      = get_the_title( $product_id );
    $image_id   = $product->get_image_id();
    $image_url  = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : get_template_directory_uri() . '/assets/img/placeholder-product.jpg';
    $price_html = $product->get_price_html();
    $rating     = $product->get_average_rating();
    $review_count = (int) $product->get_review_count();
    $rating_html = '';

    if ( $rating && $review_count ) {
        $rating_html = '<div class="rating">';
        $stars = (int) round( $rating );
        for ( $i = 1; $i <= 5; $i++ ) {
            $percent = ( $stars >= $i ) ? 100 : ( ( $stars - $i + 1 ) > 0 ? ( $stars - $i + 1 ) * 100 : 0 );
            $percent = max( 0, min( 100, $percent ) );
            $rating_html .= '<span class="star" style="--f: ' . $percent . '%">&starf;</span>';
        }
        $rating_html .= '</div>';
    }

    $add_to_cart_link = esc_url( add_query_arg( 'add-to-cart', $product_id, wc_get_cart_url() ) );
    ?>
    <article class="pcard" data-id="<?php echo esc_attr( $product_id ); ?>">
        <a class="pcard-img" href="<?php echo esc_url( $permalink ); ?>">
            <img alt="<?php echo esc_attr( $title ); ?>" loading="lazy" src="<?php echo esc_url( $image_url ); ?>" />
        </a>
        <?php if ( $show_badge && $badge_text ) : ?>
        <span class="badge"><?php echo esc_html( $badge_text ); ?></span>
        <?php elseif ( $product->is_featured() ) : ?>
        <span class="badge">الأكثر حباً</span>
        <?php endif; ?>
        <button aria-label="أضف للمفضلة" class="wish" data-wish="<?php echo esc_attr( $product_id ); ?>">
            <svg aria-hidden="true" class="ic ic-heart"><use href="#i-heart"></use></svg>
        </button>
        <div class="pcard-body">
            <h3><a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a></h3>
            <?php if ( $subtitle = '' ) : ?>
            <p class="sub"><?php echo esc_html( $subtitle ); ?></p>
            <?php endif; ?>
            <?php echo $rating_html; ?>
            <?php if ( $price_html ) : ?>
            <div class="price"><?php echo $price_html; ?></div>
            <?php endif; ?>
            <button
                class="btn btn-primary btn-block btn-sm"
                data-add="<?php echo esc_attr( $product_id ); ?>"
                data-img="<?php echo esc_url( $image_url ); ?>"
                data-name="<?php echo esc_attr( $title ); ?>"
                data-price="<?php echo esc_attr( $product->get_price() ?: '0' ); ?>"
                data-sub="<?php echo esc_attr( '' ); ?>"
            >
                <svg aria-hidden="true" class="ic ic-cart"><use href="#i-cart"></use></svg> أضف للسلة
            </button>
        </div>
    </article>
    <?php
}

function shroo_render_category_tabs( $categories ) {
    ?>
    <div class="tabs-pill" id="bs-tabs">
        <button class="active" data-cat="all">الكل</button>
        <?php foreach ( $categories as $cat ) :
            $cat_slug = $cat->slug;
            // Use the category name as the label
            $label = $cat->name;
            ?>
            <button data-cat="<?php echo esc_attr( $cat_slug ); ?>"><?php echo esc_html( $label ); ?></button>
            <?php
        endforeach; ?>
    </div>
    <?php
}

// Fetch WooCommerce product categories (all non-empty ones with products)
$product_categories = get_terms( array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
) );

// Show ALL product categories as tabs
$tabs_to_show = array();
if ( ! is_wp_error( $product_categories ) ) {
    foreach ( $product_categories as $cat ) {
        // Skip the default "Uncategorized" category
        if ( $cat->slug === 'uncategorized' ) {
            continue;
        }
        $tabs_to_show[ $cat->slug ] = $cat;
    }
}

// Fetch best selling products
$best_selling_query = new WC_Product_Query( array(
    'limit'    => 6,
    'status'   => 'publish',
    'orderby'  => 'popularity',
    'order'    => 'DESC',
    'return'   => 'products',
) );
$best_selling_products = $best_selling_query->get_products();

// Group products by category for tabs — dynamically built from actual product categories
$products_by_category = array(
    'all' => $best_selling_products,
);

if ( ! empty( $tabs_to_show ) ) {
    $cat_slugs = array_keys( $tabs_to_show );
    // Initialize an empty product array for every category that has tabs
    foreach ( $cat_slugs as $slug ) {
        $products_by_category[ $slug ] = array();
    }
    // Assign each best-selling product to the matching category tabs
    foreach ( $best_selling_products as $product ) {
        $cats = wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'slugs' ) );
        foreach ( $cat_slugs as $slug ) {
            if ( in_array( $slug, $cats ) ) {
                $products_by_category[ $slug ][] = $product;
            }
        }
    }
}
?>
      <!-- الأكثر مبيعاً -->
      <section class="sec container">
        <div class="sec-head">
          <h2>الأكثر مبيعاً</h2>
          <?php if ( ! empty( $tabs_to_show ) ) : ?>
          <?php shroo_render_category_tabs( $tabs_to_show ); ?>
          <?php endif; ?>
        </div>
        <div id="bestsellers">
          <?php
          // Build a unified tab list: 'all' first, then every category from $tabs_to_show
          $tab_items = array();
          $tab_items['all'] = __( 'الكل', 'shroo' ); // 'all' = everything

          foreach ( $tabs_to_show as $slug => $cat ) {
              // Use the category name as the tab label (falls back to slug)
              $tab_items[ $slug ] = $cat->name ? $cat->name : $cat->slug;
          }

          foreach ( $tab_items as $cat_key => $tab_label ) :
              $products = isset( $products_by_category[ $cat_key ] ) ? $products_by_category[ $cat_key ] : array();
          ?>
          <div class="grid4 bs-grid" data-cat="<?php echo esc_attr( $cat_key ); ?>" <?php echo $cat_key === 'all' ? '' : 'hidden=""'; ?>>
              <?php if ( ! empty( $products ) ) : ?>
                  <?php
                  foreach ( $products as $i => $product ) :
                      $show_badge = false;
                      $badge_text = '';

                      // Badges for the "all" tab
                      if ( $cat_key === 'all' && $i === 0 ) {
                          $show_badge = true;
                          $badge_text = 'الأكثر مبيعاً';
                      }

                      if ( $cat_key === 'all' && $i === 1 ) {
                          $show_badge = true;
                          $badge_text = 'الأكثر حباً';
                      }

                      // Optional: percentage savings badge for the first product of each category
                      if ( $cat_key !== 'all' && $i === 0 && $product->is_on_sale() ) {
                          $regular = $product->get_regular_price();
                          $sale    = $product->get_price();
                          if ( $regular && $sale ) {
                              $percentage = round( ( ( $regular - $sale ) / $regular ) * 100 );
                              if ( $percentage > 0 ) {
                                  $show_badge = true;
                                  $badge_text = 'وفّر ' . $percentage . '%';
                              }
                          }
                      }

                      shroo_render_wc_product_card( $product, $show_badge, $badge_text );
                  endforeach; ?>
              <?php else : ?>
                  <p class="no-products"><?php esc_html_e( 'لا توجد منتجات بهذه الفئة حالياً.', 'shroo' ); ?></p>
              <?php endif; ?>
          </div>
          <?php
          endforeach; ?>
        </div>
      </section>
