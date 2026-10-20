<?php
$slider_enabled = get_option( 'option_slider_enabled', 1 ) ? get_field( 'slider_enabled', 'option' ) : '';
$slider_enabled = $slider_enabled === '' ? 1 : $slider_enabled;

if ( empty( $slider_enabled ) ) {
    return;
}

$slides      = get_field( 'slider_slides', 'option' ) ?: array();
$slide_count = count( $slides );

if ( $slide_count === 0 ) {
    return;
}
?>
      <!-- السلايدر الرئيسي -->
      <section aria-label="العروض الرئيسية" aria-robedescription="carousel" class="hero" id="hero">
        <h1 class="sr-only">شرو راديانت تشارم — العناية بالشعر والبشرة</h1>
        <?php foreach ( $slides as $index => $slide ) :
            $image_id = $slide['image'] ?? '';
            $image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : get_template_directory_uri() . '/assets/img/hero-home.jpg';
            $image_alt = $image_id ? get_post_field( 'post_excerpt', $image_id ) : 'Hero Slide';
            $title     = $slide['title'] ?? '';
            $subtitle  = $slide['subtitle'] ?? '';
            $tagline   = $slide['tagline'] ?? '';
            $btn_text  = $slide['button_text'] ?? '';
            $btn_url   = $slide['button_url'] ?? '';
            $features  = $slide['features'] ?? array();
            $is_active = $index === 0 ? ' active' : '';
        ?>
        <div aria-hidden="<?php echo esc_attr( $index !== 0 ? 'true' : 'false' ); ?>" aria-robedescription="slide" class="slide s<?php echo ( $index + 1 ); echo $is_active; ?>">
          <img alt="<?php echo esc_attr( $image_alt ); ?>" src="<?php echo esc_url( $image_url ); ?>" />
          <?php if ( $title || $subtitle || $tagline || $btn_text ) : ?>
          <div class="ov s-copy">
            <?php if ( $title ) : ?>
            <p class="h-title<?php echo $index === 0 ? ' m-only' : ''; ?>"><?php echo esc_html( $title ); ?></p>
            <?php endif; ?>
            <?php if ( $subtitle ) : ?>
            <p class="h-sub"><?php echo esc_html( $subtitle ); ?></p>
            <?php endif; ?>
            <?php if ( $tagline ) : ?>
            <p class="h-tag"><?php echo esc_html( $tagline ); ?></p>
            <?php endif; ?>
            <?php if ( $btn_text ) : ?>
            <a class="hero-btn" href="<?php echo esc_url( $btn_url ); ?>">
              <?php echo esc_html( $btn_text ); ?> <svg aria-hidden="true" class="ic ic-arrow"><use href="#i-arrow"></use></svg>
            </a>
            <?php endif; ?>
          </div>
          <?php endif; ?>
          <?php $features = is_array( $features ) ? $features : array(); ?>
          <?php foreach ( $features as $feature ) : ?>
          <span class="ov hero-feat" style="left: <?php echo esc_attr( ( $feature['position'] ?? 50 ) ); ?>%"><?php echo esc_html( $feature['text'] ?? '' ); ?></span>
          <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        <button aria-label="التالي" class="hero-arrow l" data-dir="1">
          <svg aria-hidden="true" class="ic ic-chevRight"><use href="#i-chevRight"></use></svg>
        </button>
        <button aria-label="السابق" class="hero-arrow r" data-dir="-1">
          <svg aria-hidden="true" class="ic ic-chevLeft"><use href="#i-chevLeft"></use></svg>
        </button>
        <div class="dots">
          <?php for ( $i = 0; $i < $slide_count; $i++ ) : ?>
          <button aria-label="الشريحة <?php echo ( $i + 1 ); ?>" class="<?php echo $i === 0 ? 'on' : ''; ?>"></button>
          <?php endfor; ?>
        </div>
      </section>
