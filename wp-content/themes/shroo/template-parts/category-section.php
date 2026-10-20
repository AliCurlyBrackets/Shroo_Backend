<?php
if ( ! function_exists( 'shroo_term_id' ) ) {
    function shroo_term_id( $value ) {
        if ( is_int( $value ) ) {
            return $value;
        }
        if ( is_array( $value ) ) {
            $value = reset( $value );
        }
        if ( $value instanceof WP_Term ) {
            return (int) $value->term_id;
        }
        if ( is_object( $value ) && isset( $value->term_id ) ) {
            return (int) $value->term_id;
        }
        return (int) $value;
    }
}

if ( ! function_exists( 'shroo_get_wc_category_image' ) ) {
    function shroo_get_wc_category_image( $term_id ) {
        $image_id = (int) get_term_meta( $term_id, 'thumbnail_id', true );
        if ( ! $image_id ) {
            $image_id = (int) get_term_meta( $term_id, '_thumbnail_id', true );
        }
        if ( ! $image_id ) {
            return get_template_directory_uri() . '/assets/img/placeholder-cat.jpg';
        }
        $url = wp_get_attachment_image_url( $image_id, 'full' );
        return $url ? $url : get_template_directory_uri() . '/assets/img/placeholder-cat.jpg';
    }
}

$category_enabled = get_field( 'category_enabled', 'option' );
if ( null !== $category_enabled && ! $category_enabled ) {
    return;
}

$section_heading = get_field( 'category_heading', 'option' );
$section_heading = $section_heading ? $section_heading : 'تسوقي حسب القسم';

$view_all_link = get_field( 'category_view_all_link', 'option' );
if ( empty( $view_all_link ) ) {
    $view_all_link = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( 'shop' );
}

$category_items  = get_field( 'category_items', 'option' ) ?: array();
?>
      <!-- الأقسام -->
      <section class="sec container">
        <div class="sec-head">
          <h2><?php echo esc_html( $section_heading ); ?></h2>
          <a class="link-more" href="<?php echo esc_url( $view_all_link ); ?>">
            <?php esc_html_e( 'عرض كل الأقسام', 'shroo' ); ?>
            <svg aria-hidden="true" class="ic ic-arrow"><use href="#i-arrow"></use></svg>
          </a>
        </div>
        <div class="cats">
          <?php foreach ( $category_items as $item ) :
              $term_value = $item['product_category'] ?? 0;
              $term_value = shroo_term_id( $term_value );
              if ( ! $term_value ) {
                  continue;
              }

              $term      = get_term( $term_value, 'product_cat' );
              if ( ! $term || is_wp_error( $term ) ) {
                  continue;
              }

              $term_id   = $term->term_id;
              $term_link = get_term_link( $term );
              if ( is_wp_error( $term_link ) ) {
                  continue;
              }

              // Resolve override fields
              $title         = ! empty( $item['title'] ) ? $item['title'] : $term->name;
              $subtitle      = ! empty( $item['subtitle'] ) ? $item['subtitle'] : $term->description;
              $subtitle      = $subtitle ? $subtitle : '';
              $image_id      = ! empty( $item['image'] ) ? $item['image'] : 0;
              $copy_position = ! empty( $item['copy_position'] ) ? $item['copy_position'] : 62;
              $copy_position = ( is_numeric( $copy_position ) ? (int) $copy_position : 62 );

              // Resolve image
              if ( $image_id ) {
                  $image_url = wp_get_attachment_image_url( $image_id, 'full' );
              } else {
                  $image_url = shroo_get_wc_category_image( $term_id );
              }
              if ( ! $image_url ) {
                  $image_url = get_template_directory_uri() . '/assets/img/placeholder-cat.jpg';
              }
          ?>
          <a href="<?php echo esc_url( $term_link ); ?>">
              <img alt="<?php echo esc_attr( $title ); ?>" src="<?php echo esc_url( $image_url ); ?>" />
              <?php if ( $title || $subtitle ) : ?>
              <div class="ov cat-copy" style="left: <?php echo esc_attr( $copy_position ); ?>%">
                <b><?php echo esc_html( $title ); ?></b>
                <?php if ( $subtitle ) : ?>
                <span><?php echo nl2br( esc_html( $subtitle ) ); ?></span>
                <?php endif; ?>
              </div>
              <?php endif; ?>
              <span class="ov cat-btn" style="left: <?php echo esc_attr( $copy_position ); ?>%; top: 69%; width: 30.6%">
                <?php esc_html_e( 'تسوقي الآن', 'shroo' ); ?>
                <svg aria-hidden="true" class="ic ic-arrow"><use href="#i-arrow"></use></svg>
              </span>
          </a>
          <?php endforeach; ?>
        </div>
      </section>
