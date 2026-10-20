<?php
/**
 * Newsletter section template.
 *
 * Relies on ACF options registered in Functions/Options/newsletter-options.php
 * (option page slug: shroo-newsletter-settings).
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$enabled     = get_field( 'newsletter_enabled', 'option' );
$heading     = get_field( 'newsletter_heading', 'option' ) ?: __( 'انضمي لمجتمع الجمال', 'shroo' );
$subtitle    = get_field( 'newsletter_subtitle', 'option' ) ?: __( 'كوني أول من يعرف عن المنتجات الجديدة والعروض الحصرية ونصائح الجمال.', 'shroo' );
$button_text = get_field( 'newsletter_button_text', 'option' ) ?: __( 'اشتراك', 'shroo' );
$placeholder = get_field( 'newsletter_placeholder', 'option' ) ?: __( 'بريدك الإلكتروني', 'shroo' );
$form_url    = get_field( 'newsletter_form_url', 'option' );

if ( ! $enabled ) {
    return;
}
?>

    <section class="newsletter">
      <div class="container nl-in">
        <div class="nl-text">
          <svg aria-hidden="true" class="ic ic-sparkle nl-ic"><use href="#i-sparkle"></use></svg>
          <div>
            <h3><?php echo esc_html( $heading ); ?></h3>
            <?php if ( $subtitle ) : ?>
              <p><?php echo esc_html( $subtitle ); ?></p>
            <?php endif; ?>
          </div>
        </div>
        <form class="nl-form"<?php echo ! empty( $form_url ) ? ' action="' . esc_url( $form_url ) . '"' : ''; ?>>
          <span><svg aria-hidden="true" class="ic ic-mail"><use href="#i-mail"></use></svg></span>
          <input placeholder="<?php echo esc_attr( $placeholder ); ?>" required="" type="email" />
          <button class="btn btn-primary"><?php echo esc_html( $button_text ); ?></button>
        </form>
      </div>
    </section>
