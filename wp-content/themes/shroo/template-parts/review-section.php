<?php
/**
 * Review section template.
 *
 * Relies on ACF options registered in Functions/Options/reviews-options.php
 * (option page slug: shroo-reviews-settings).
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$enabled  = get_field( 'reviews_enabled', 'option' );
$heading  = get_field( 'reviews_heading', 'option' ) ?: __( 'أشخاص حقيقيون، نتائج حقيقية', 'shroo' );
$subtitle = get_field( 'reviews_subtitle', 'option' ) ?: __( 'موثوق من آلاف العميلات السعيدات.', 'shroo' );
$view_all_url = get_field( 'reviews_view_all_link', 'option' );
$items   = get_field( 'reviews_items', 'option' );

if ( ! $enabled ) {
    return;
}
?>

      <!-- التقييمات -->
      <section class="sec container">
        <div class="sec-head">
          <div>
            <h2><?php echo esc_html( $heading ); ?></h2>
            <?php if ( $subtitle ) : ?>
              <p><?php echo esc_html( $subtitle ); ?></p>
            <?php endif; ?>
          </div>
          <?php if ( ! empty( $view_all_url ) ) : ?>
            <a class="link-more" href="<?php echo esc_url( $view_all_url ); ?>">
              <?php esc_html_e( 'عرض كل التقييمات', 'shroo' ); ?>
              <svg aria-hidden="true" class="ic ic-arrow"><use href="#i-arrow"></use></svg>
            </a>
          <?php endif; ?>
        </div>
        <?php if ( $items ) : ?>
          <div class="grid3" id="home-reviews">
            <?php foreach ( $items as $item ) : ?>
              <div class="review">
                <?php
                $avatar_id = ! empty( $item['avatar'] ) ? $item['avatar'] : '';
                $avatar_url = $avatar_id
                    ? wp_get_attachment_image_url( $avatar_id, 'full' )
                    : get_template_directory_uri() . '/assets/img/avatar1.jpg';
                $alt_name = ! empty( $item['name'] ) ? $item['name'] : '';
                ?>
                <img alt="<?php echo esc_attr( $alt_name ); ?>" src="<?php echo esc_url( $avatar_url ); ?>" />
                <div>
                  <?php
                  $rating = isset( $item['rating'] ) ? (int) $item['rating'] : 5;
                  $rating = max( 1, min( 5, $rating ) );
                  $percent = ( $rating / 5 ) * 100;
                  ?>
                  <span aria-label="<?php echo esc_attr( $rating . ' من 5' ); ?> ★" class="stars">
                    <?php for ( $s = 1; $s <= 5; $s++ ) : ?>
                      <span class="star" style="--f: <?php echo $s <= $rating ? '100' : '0'; ?>%;"><?php echo $s <= $rating ? '★' : '☆'; ?></span>
                    <?php endfor; ?>
                  </span>
                  <?php if ( ! empty( $item['text'] ) ) : ?>
                    <p><?php echo esc_html( $item['text'] ); ?></p>
                  <?php endif; ?>
                  <span class="who">
                    <b><?php echo esc_html( $alt_name ); ?></b>
                    <svg aria-hidden="true" class="ic ic-check vb"><use href="#i-check"></use></svg>
                    <?php esc_html_e( 'مشترية موثّقة', 'shroo' ); ?>
                  </span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>
