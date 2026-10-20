<?php
// Get Top Bar options from ACF with fallbacks
$topbar_enabled = get_field('topbar_enabled', 'option');
$topbar_enabled = $topbar_enabled === false ? 1 : $topbar_enabled;

if (!$topbar_enabled) {
    return;
}

$notice_1 = get_field('notice_1', 'option') ?: 'شحن مجاني للطلبات فوق 150 ر.س';
$notice_2 = get_field('notice_2', 'option') ?: 'منتجات أصلية';
$notice_3 = get_field('notice_3', 'option') ?: 'جمال مشرق كل يوم';

$lang_enabled = get_field('topbar_language_enabled', 'option');
$lang_enabled = $lang_enabled === false ? 1 : $lang_enabled;
$lang_text = get_field('topbar_language_text', 'option') ?: 'English';
$lang_url = get_field('topbar_language_url', 'option') ?: '#';

$cur_enabled = get_field('topbar_currency_enabled', 'option');
$cur_enabled = $cur_enabled === false ? 1 : $cur_enabled;
$cur_symbol = get_field('topbar_currency_symbol', 'option') ?: get_woocommerce_currency_symbol();
if (!$cur_symbol) {
    $cur_symbol = 'ر.س';
}
?>

<div class="topbar">
  <div class="container topbar-in">
    <p>
      <?php if ($notice_1) : ?>
      <span><svg aria-hidden="true" class="ic ic-truck"><use href="#i-truck"></use></svg> <?php echo esc_html($notice_1); ?></span>
      <?php if ($notice_2) : ?><i>|</i><?php endif; ?>
      <?php endif; ?>

      <?php if ($notice_2) : ?>
      <span><svg aria-hidden="true" class="ic ic-sparkle"><use href="#i-sparkle"></use></svg> <?php echo esc_html($notice_2); ?></span>
      <?php if ($notice_3) : ?><i>|</i><?php endif; ?>
      <?php endif; ?>

      <?php if ($notice_3) : ?>
      <span><svg aria-hidden="true" class="ic ic-bolt"><use href="#i-bolt"></use></svg> <?php echo esc_html($notice_3); ?></span>
      <?php endif; ?>
    </p>
    
  </div>
</div>