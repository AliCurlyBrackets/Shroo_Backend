<?php
/**
 * Footer template.
 *
 * Relies on ACF options registered in Functions/Options/footer-settings.php
 * (option page slug: shroo-footer-settings).
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Gracefully handle missing ACF
$footer_enabled     = function_exists( 'get_field' ) ? get_field( 'footer_enabled', 'option' ) : false;
$brand              = function_exists( 'get_field' ) ? get_field( 'footer_brand', 'option' ) : array();
$contact            = function_exists( 'get_field' ) ? get_field( 'footer_contact', 'option' ) : array();
$menus              = function_exists( 'get_field' ) ? get_field( 'footer_menus', 'option' ) : array();
$social             = function_exists( 'get_field' ) ? get_field( 'footer_social', 'option' ) : array();
$payment_image_id   = function_exists( 'get_field' ) ? get_field( 'payment_image', 'option' ) : '';
$copyright          = function_exists( 'get_field' ) ? (get_field( 'copyright', 'option' ) ?: '© 2024 شرو راديانت تشارم. جميع الحقوق محفوظة.') : '© 2024 شرو راديانت تشارم. جميع الحقوق محفوظة.';
$sub_copyright      = function_exists( 'get_field' ) ? (get_field( 'sub_copyright', 'option' ) ?: 'جمال اليوم. غدٌ أكثر إشراقاً.') : 'جمال اليوم. غدٌ أكثر إشراقاً.';

// Resolve logo
$logo_id = ! empty( $brand['logo'] ) ? $brand['logo'] : '';
$logo_url = $logo_id
    ? wp_get_attachment_image_url( $logo_id, 'full' )
    : get_template_directory_uri() . '/assets/img/logo.png';

// Resolve payment image
$payment_url = $payment_image_id
    ? wp_get_attachment_image_url( $payment_image_id, 'full' )
    : get_template_directory_uri() . '/assets/img/payments.jpg';


?>

    <footer class="footer">
      <div class="container">
        <div class="f-grid">
          <div class="f-brand">
            <img style="height: auto !important;" alt="شرو راديانت تشارم" src="<?php echo esc_url( $logo_url ); ?>" />
            <?php if ( ! empty( $brand['brand_text'] ) ) : ?>
              <p><?php echo nl2br( esc_html( $brand['brand_text'] ) ); ?></p>
            <?php endif; ?>
          </div>

          <!-- Footer Menu 1 (Quick Links) -->
          <div>
            <h4><?php echo esc_html( ! empty( $menus['footer_1_heading'] ) ? $menus['footer_1_heading'] : 'روابط سريعة' ); ?></h4>
            <?php if ( has_nav_menu( 'footer_menu_1' ) ) : ?>
              <?php
              wp_nav_menu( array(
                  'theme_location' => 'footer_menu_1',
                  'container'      => false,
                  'fallback_cb'    => false,
                  'items_wrap'     => '%3$s',
              ) );
              ?>
            <?php else : ?>
              <a href="<?php echo esc_url( get_home_url() ); ?>"><?php esc_html_e( 'الرئيسية', 'shroo' ); ?></a>
              <a href="<?php echo esc_url( wc_get_shop_page_url() ); ?>"><?php esc_html_e( 'كل المنتجات', 'shroo' ); ?></a>
              <a href="#about"><?php esc_html_e( 'من نحن', 'shroo' ); ?></a>
              <?php if ( ! empty( $contact['phone'] ) ) : ?>
                <a href="tel:<?php echo esc_attr( $contact['phone'] ); ?>"><?php esc_html_e( 'تواصل معنا', 'shroo' ); ?></a>
              <?php else : ?>
                <a href="#"><?php esc_html_e( 'تواصل معنا', 'shroo' ); ?></a>
              <?php endif; ?>
            <?php endif; ?>
          </div>

          <!-- Footer Menu 2 (Customer Service) -->
          <div>
            <h4><?php echo esc_html( ! empty( $menus['footer_2_heading'] ) ? $menus['footer_2_heading'] : 'خدمة العملاء' ); ?></h4>
            <?php if ( has_nav_menu( 'footer_menu_2' ) ) : ?>
              <?php
              wp_nav_menu( array(
                  'theme_location' => 'footer_menu_2',
                  'container'      => false,
                  'fallback_cb'    => false,
                  'items_wrap'     => '%3$s',
              ) );
              ?>
            <?php else : ?>
              <a href="#"><?php esc_html_e( 'سياسة الشحن', 'shroo' ); ?></a>
              <a href="#"><?php esc_html_e( ' الاسترجاع والاستبدال', 'shroo' ); ?></a>
              <a href="#"><?php esc_html_e( 'الأسئلة الشائعة', 'shroo' ); ?></a>
              <a href="#"><?php esc_html_e( 'تتبع الطلب', 'shroo' ); ?></a>
            <?php endif; ?>
          </div>

          <!-- Social Media -->
          <div>
            <h4><?php echo esc_html( ! empty( $social['heading'] ) ? $social['heading'] : 'تابعينا' ); ?></h4>
            <div class="social">
              <?php if ( ! empty( $social['links'] ) ) : ?>
                <?php foreach ( $social['links'] as $link ) : ?>
                  <?php
                  $platform  = ! empty( $link['platform'] ) ? $link['platform'] : 'social';
                  $icon_id   = ! empty( $link['icon'] ) ? $link['icon'] : '';
                  $icon_url  = $icon_id ? wp_get_attachment_image_url( $icon_id, 'medium' ) : '';
                  $url       = ! empty( $link['url'] ) ? $link['url'] : '#';
                  ?>
                  <a aria-label="<?php echo esc_attr( $platform ); ?>" href="<?php echo esc_url( $url ); ?>">
                    <?php if ( $icon_url ) : ?>
                      <img alt="<?php echo esc_attr( $platform ); ?> icon" class="social-icon" src="<?php echo esc_url( $icon_url ); ?>" />
                    <?php endif; ?>
                  </a>
                <?php endforeach; ?>
              <?php else : ?>
                <a aria-label="Instagram" href="#"><svg aria-hidden="true" class="ic ic-instagram"><use href="#i-instagram"></use></svg></a>
                <a aria-label="TikTok" href="#"><svg aria-hidden="true" class="ic ic-tiktok"><use href="#i-tiktok"></use></svg></a>
                <a aria-label="Facebook" href="#"><svg aria-hidden="true" class="ic ic-facebook"><use href="#i-facebook"></use></svg></a>
                <a aria-label="YouTube" href="#"><svg aria-hidden="true" class="ic ic-youtube"><use href="#i-youtube"></use></svg></a>
              <?php endif; ?>
            </div>
            <?php if ( ! empty( $social['handle'] ) ) : ?>
              <p class="handle ltr"><?php echo esc_html( $social['handle'] ); ?></p>
            <?php endif; ?>
          </div>
          <div>
            <h4>دفع آمن</h4>
            <img style="height: auto !important;" alt="فيزا، ماستركارد، مدى، Apple Pay" class="pay-img" src="<?php echo esc_url( $payment_url ); ?>"  />
          </div>
        </div>
        <div class="f-bottom">
          <p><?php echo esc_html( $copyright ); ?></p>
          <p>
            <?php echo esc_html( $sub_copyright ); ?> <svg aria-hidden="true" class="ic ic-heart"><use href="#i-heart"></use></svg>
          </p>
        </div>
      </div>
    </footer>
    <div class="drawer-bg"></div>
    <aside aria-label="سلة التسوق" class="drawer">
      <div class="drawer-head">
        <h3>سلتك</h3>
        <button aria-label="إغلاق" class="drawer-close">
          <svg aria-hidden="true" class="ic ic-close"><use href="#i-close"></use></svg>
        </button>
      </div>
      <div class="drawer-body">
        <div class="empty drawer-empty">
          <svg aria-hidden="true" class="ic ic-cart"><use href="#i-cart"></use></svg>
          <p>سلتك فارغة</p>
          <a class="btn btn-primary" href="<?php echo esc_url( wc_get_page_permalink('shop') ); ?>">تابعي التسوق</a>
        </div>
        <div class="drawer-lines"></div>
      </div>
    
      <div class="drawer-foot" hidden="">
          <?php $free_shipping_threshold = shroo_get_free_shipping_threshold(); ?>
      <?php if ($free_shipping_threshold !== false) : ?>
        <?php if ($free_shipping_threshold > 0) : ?>
        <p class="ship-note ship-more">أضيفي <b class="ship-left"><?php echo esc_html( $free_shipping_threshold ); ?></b> <?php echo esc_html( get_woocommerce_currency_symbol() ); ?> لتحصلي على شحن مجاني</p>
        <?php else : ?>
        <p class="ship-note ship-more">أضيفي منتجات لتحصلي على شحن مجاني</p>
        <?php endif; ?>
        <p class="ship-note ship-free" hidden="">
          <svg aria-hidden="true" class="ic ic-truck"><use href="#i-truck"></use></svg> حصلتِ على <b>شحن مجاني!</b>
        </p>
        <div class="ship-bar"><span style="width: 0%"></span></div>
        <?php endif; ?>
        <div class="row-between"><span>المجموع الفرعي</span><strong class="drawer-sub">0 <?php echo esc_html( get_woocommerce_currency_symbol() ); ?></strong></div>
        <a class="btn btn-primary btn-block" href="<?php echo esc_url( wc_get_checkout_url() ); ?>"
          ><svg aria-hidden="true" class="ic ic-lock"><use href="#i-lock"></use></svg> إتمام الشراء</a
        >
      </div>
      
    </aside>
    <div class="toast"></div>

    <template id="tpl-drawer-line">
      <div class="dline">
        <img alt="" src="" />
        <div class="dl-info">
          <a href=""></a>
          <span class="dl-price"></span>
          <div class="qty sm">
            <button aria-label="تقليل" data-d="-1" type="button">
              <svg aria-hidden="true" class="ic ic-minus"><use href="#i-minus"></use></svg></button
            ><input aria-label="الكمية" readonly="" value="1" /><button aria-label="زيادة" data-d="1" type="button">
              <svg aria-hidden="true" class="ic ic-plus"><use href="#i-plus"></use></svg>
            </button>
          </div>
        </div>
        <button aria-label="حذف" class="dl-rm" type="button">
          <svg aria-hidden="true" class="ic ic-trash"><use href="#i-trash"></use></svg>
        </button>
      </div>
    </template>
    <script>
      window.SHR = {
        checkoutUrl: <?php echo json_encode( wc_get_checkout_url() ); ?>,
        cartUrl: <?php echo json_encode( wc_get_cart_page_id() ? get_permalink( wc_get_cart_page_id() ) : '' ); ?>,
        myAccountUrl: <?php echo json_encode( wc_get_page_permalink( 'myaccount' ) ); ?>,
        currency: {
          symbol: <?php echo json_encode( get_woocommerce_currency_symbol() ); ?>,
          position: <?php echo json_encode( get_option( 'woocommerce_currency_pos', 'right' ) ); ?>
        },
        freeShippingThreshold: <?php echo json_encode( shroo_get_free_shipping_threshold() ); ?>,
        shippingFee: <?php echo json_encode( get_option( 'woocommerce_shipping_flat_cost', 25 ) ); ?>
      };
    </script>
    <script src="<?php echo get_template_directory_uri(  ) . '/assets/js/wc-cart.js' ?>"></script>
    <?php wp_footer() ; ?>
  </body>
</html>
