<?php
/**
 * Offers section template.
 *
 * Relies on ACF options registered in Functions/Options/offers-options.php
 * (option page slug: shroo-offers-settings).
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$enabled       = get_field( 'offers_enabled', 'option' );
$heading       = get_field( 'offers_heading', 'option' ) ?: __( 'عروض الباقات الخاصة', 'shroo' );
$subtitle      = get_field( 'offers_subtitle', 'option' ) ?: __( 'عناية متكاملة لإشراقة أكبر وبقيمة أفضل.', 'shroo' );
$button_text   = get_field( 'offers_button_text', 'option' ) ?: __( 'تسوقي الباقات', 'shroo' );
$button_url    = get_field( 'offers_button_url', 'option' ) ?: '';
$bg_image_id   = get_field( 'offers_bg_image', 'option' );
$items         = get_field( 'offers_items', 'option' );

// Resolve the background image (fallback to theme default asset)
if ( $bg_image_id ) {
    $bg_image_url = wp_get_attachment_image_url( $bg_image_id, 'full' );
} else {
    $bg_image_url = get_template_directory_uri() . '/assets/img/bundle-products.jpg';
}

if ( ! $enabled ) {
    return;
}
?>

      <!-- عروض الباقات -->
      <section class="container">
        <div class="bundle">
          <div class="bundle-text">
            <h2><?php echo esc_html( $heading ); ?></h2>
            <?php if ( $subtitle ) : ?>
              <p><?php echo esc_html( $subtitle ); ?></p>
            <?php endif; ?>
            <?php if ( $button_text ) : ?>
              <a class="btn btn-primary btn-sm" href="<?php echo esc_url( $button_url ?: home_url( '/' ) ); ?>?cat=bundles" style="border-radius: 999px; height: 38px; padding: 0 34px">
                <?php echo esc_html( $button_text ); ?> <svg aria-hidden="true" class="ic ic-arrow"><use href="#i-arrow"></use></svg>
              </a>
            <?php endif; ?>
          </div>
          <div class="bundle-img"><img alt="باقات منتجات شرو" src="<?php echo esc_url( $bg_image_url ); ?>" /></div>
          <?php if ( $items ) : ?>
            <div class="bundle-list">
              <?php foreach ( $items as $item ) : ?>
                <div>
                  <?php if ( ! empty( $item['icon'] ) ) : ?>
                    <?php
                    $icon_id = $item['icon'];
                    $icon_url = wp_get_attachment_image_url( $icon_id, 'medium' );
                    ?>
                    <img class="feature-icon" alt="<?php echo esc_attr( $item['label'] ); ?>" src="<?php echo esc_url( $icon_url ); ?>" />
                  <?php endif; ?>
                  <p>
                    <?php if ( ! empty( $item['label'] ) ) : ?>
                      <b><?php echo esc_html( $item['label'] ); ?></b>
                    <?php endif; ?>
                    <?php if ( ! empty( $item['description'] ) ) : ?>
                      <span><?php echo esc_html( $item['description'] ); ?></span>
                    <?php endif; ?>
                  </p>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </section>
