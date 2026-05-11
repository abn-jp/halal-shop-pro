<?php
/**
 * WooCommerce Product Archive (Shop Page)
 * Custom template — WC default wrappers removed via inc/woocommerce.php
 */
defined( 'ABSPATH' ) || exit;
get_header();

do_action( 'woocommerce_before_main_content' );
?>

<!-- Shop Hero -->
<div class="page-hero">
    <div class="container">
        <h1 class="page-hero__title"><?php woocommerce_page_title(); ?></h1>
        <?php if ( is_product_category() ) :
            $cat_desc = term_description();
            if ( $cat_desc ) echo '<p class="page-hero__desc">' . wp_kses_post( $cat_desc ) . '</p>';
        endif; ?>
    </div>
</div>

<!-- Shop Body -->
<section class="shop-section">
    <div class="container">

        <!-- Toolbar: breadcrumb + sort + count -->
        <div class="shop-toolbar">
            <div class="shop-breadcrumb"><?php woocommerce_breadcrumb(); ?></div>
            <div class="shop-toolbar-right">
                <?php woocommerce_catalog_ordering(); ?>
                <?php woocommerce_result_count(); ?>
            </div>
        </div>

        <!-- Two-column layout: sidebar + products -->
        <div class="shop-layout">

            <!-- Sidebar: category filter -->
            <aside class="shop-sidebar" aria-label="<?php esc_attr_e( 'Shop Filters', 'halal-shop-pro' ); ?>">
                <div class="shop-filter-box">
                    <h3 class="shop-filter-title"><?php esc_html_e( 'カテゴリ / Categories', 'halal-shop-pro' ); ?></h3>
                    <?php wp_list_categories( [
                        'taxonomy'   => 'product_cat',
                        'show_count' => true,
                        'hide_empty' => false,
                        'title_li'   => '',
                        'orderby'    => 'name',
                    ] ); ?>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="shop-products">
                <?php if ( woocommerce_product_loop() ) : ?>
                    <?php woocommerce_product_loop_start(); ?>
                    <?php while ( have_posts() ) : the_post(); ?>
                        <?php wc_get_template_part( 'content', 'product' ); ?>
                    <?php endwhile; ?>
                    <?php woocommerce_product_loop_end(); ?>
                    <?php woocommerce_after_shop_loop(); ?>
                <?php else : ?>
                    <?php do_action( 'woocommerce_no_products_found' ); ?>
                <?php endif; ?>
            </div>

        </div><!-- .shop-layout -->
    </div><!-- .container -->
</section>

<?php do_action( 'woocommerce_after_main_content' ); ?>
<?php get_footer(); ?>
