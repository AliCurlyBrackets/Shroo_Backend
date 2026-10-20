<?php
/**
 * Default page template (checkout, cart, my account, and any normal page).
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

    <main class="container" style="padding: 32px 0 48px;">
      <?php
      while ( have_posts() ) :
          the_post();
          the_content();
      endwhile;
      ?>
    </main>

<?php get_footer(); ?>