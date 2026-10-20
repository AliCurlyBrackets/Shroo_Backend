<?php
/**
 * Roten section template.
 *
 * Relies on ACF options registered in Functions/Options/roten-options.php
 * (option page slug: shroo-roten-settings).
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$enabled      = get_field( 'roten_enabled', 'option' );
$heading      = get_field( 'roten_heading', 'option' ) ?: __( 'أكملي روتينك', 'shroo' );
$subtitle     = get_field( 'roten_subtitle', 'option' ) ?: __( 'روتين بسيط لشعر أصح وبشرة أكثر إشراقاً.', 'shroo' );
$view_all_url = get_field( 'roten_view_all_link', 'option' );
$items        = get_field( 'roten_items', 'option' );

if ( ! $enabled ) {
    return;
}
?>

      <!-- الروتين -->
      <section class="sec container" id="about">
        <div class="sec-head">
          <div>
            <h2><?php echo esc_html( $heading ); ?></h2>
            <?php if ( $subtitle ) : ?>
              <p><?php echo esc_html( $subtitle ); ?></p>
            <?php endif; ?>
          </div>
          <?php if ( ! empty( $view_all_url ) ) : ?>
            <a class="link-more" href="<?php echo esc_url( $view_all_url ); ?>">
              <?php esc_html_e( 'عرض الروتين كاملاً', 'shroo' ); ?>
              <svg aria-hidden="true" class="ic ic-arrow"><use href="#i-arrow"></use></svg>
            </a>
          <?php endif; ?>
        </div>
        <?php if ( $items ) : ?>
          <div class="steps">
            <?php foreach ( $items as $i => $item ) : ?>
              <a class="step" href="<?php echo esc_url( ! empty( $item['link'] ) ? $item['link'] : '#' ); ?>">
                <?php if ( ! empty( $item['image'] ) ) : ?>
                  <?php $img_url = wp_get_attachment_image_url( $item['image'], 'full' ); ?>
                  <img alt="<?php echo esc_attr( $item['title_text'] ); ?>" src="<?php echo esc_url( $img_url ); ?>" />
                <?php else : ?>
                  <img alt="" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/step' . ( $i + 1 ) . '.jpg' ); ?>" />
                <?php endif; ?>
                <div>
                  <?php if ( ! empty( $item['step'] ) ) : ?>
                    <small><?php echo esc_html( $item['step'] ); ?></small>
                  <?php endif; ?>
                  <?php if ( ! empty( $item['title_text'] ) ) : ?>
                    <b><?php echo esc_html( $item['title_text'] ); ?></b>
                  <?php endif; ?>
                  <?php if ( ! empty( $item['description'] ) ) : ?>
                    <p><?php echo nl2br( esc_html( $item['description'] ) ); ?></p>
                  <?php endif; ?>
                </div>
              </a>
              <?php if ( $i < count( $items ) - 1 ) : ?>
                <span class="step-arrow">
                  <svg aria-hidden="true" class="ic ic-arrow"><use href="#i-arrow"></use></svg>
                </span>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>
