<?php
/**
 * WooCommerce Product Archive (Shop Page)
 */
defined( 'ABSPATH' ) || exit;
get_header( 'shop' );
?>

<?php do_action( 'woocommerce_before_main_content' ); ?>

<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
<div class="page-hero" style="padding:3rem 0">
    <div class="container">
        <h1><?php woocommerce_page_title(); ?></h1>
        <?php if ( is_product_category() ) : ?>
        <?php $cat_desc = term_description(); if ( $cat_desc ) echo '<p>' . wp_kses_post( $cat_desc ) . '</p>'; ?>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<main class="section section--gray">
    <div class="container">

        <!-- Breadcrumb + Filter Bar -->
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem">
            <div><?php woocommerce_breadcrumb(); ?></div>
            <div style="display:flex;align-items:center;gap:1rem">
                <?php woocommerce_catalog_ordering(); ?>
                <?php woocommerce_result_count(); ?>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:240px 1fr;gap:2rem;align-items:start">

            <!-- Sidebar / Filters -->
            <aside class="shop-sidebar">
                <?php dynamic_sidebar( 'sidebar-shop' ); ?>

                <?php if ( ! is_active_sidebar( 'sidebar-shop' ) ) : ?>
                <!-- Default filters when no widgets set -->
                <div style="background:#fff;border-radius:12px;padding:1.5rem;box-shadow:var(--shadow-sm);margin-bottom:1.5rem">
                    <h3 style="color:var(--color-primary);margin-bottom:1rem;font-size:1rem"><?php esc_html_e( 'カテゴリ / Categories', 'halal-shop-pro' ); ?></h3>
                    <?php wp_list_categories( [ 'taxonomy' => 'product_cat', 'show_count' => true, 'hide_empty' => false, 'title_li' => '', 'walker' => '' ] ); ?>
                </div>
                <?php endif; ?>
            </aside>

            <!-- Products Grid -->
            <div>
                <?php if ( woocommerce_product_loop() ) : ?>
                <div class="product-grid">
                    <?php
                    woocommerce_product_loop_start();
                    while ( have_posts() ) {
                        the_post();
                        wc_get_template_part( 'content', 'product' );
                    }
                    woocommerce_product_loop_end();
                    ?>
                </div>
                <?php woocommerce_after_shop_loop(); ?>
                <?php else : ?>
                <?php do_action( 'woocommerce_no_products_found' ); wc_get_template( 'loop/no-products-found.php' ); ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</main>

<?php do_action( 'woocommerce_after_main_content' ); ?>

<?php get_footer( 'shop' ); ?>
