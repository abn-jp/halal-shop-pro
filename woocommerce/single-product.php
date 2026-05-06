<?php
/**
 * WooCommerce Single Product Page
 */
defined( 'ABSPATH' ) || exit;
get_header( 'shop' );
?>

<?php do_action( 'woocommerce_before_main_content' ); ?>

<div class="woocommerce-breadcrumb-wrap" style="background:var(--color-gray-100);padding:.75rem 0">
    <div class="container"><?php woocommerce_breadcrumb(); ?></div>
</div>

<main class="section">
    <div class="container">
        <?php while ( have_posts() ) : the_post(); ?>
        <?php wc_get_template_part( 'content', 'single-product' ); ?>
        <?php endwhile; ?>
    </div>
</main>

<?php do_action( 'woocommerce_after_main_content' ); ?>

<?php get_footer( 'shop' ); ?>
