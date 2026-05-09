<?php
/**
 * Halal Wagyu — Category Archive Template
 * Loaded via template_include filter in inc/halal-wagyu.php (priority 99)
 * Covers: halal-wagyu and all halal-wagyu-* subcategory slugs
 */
defined( 'ABSPATH' ) || exit;
get_header( 'shop' );
?>

<?php do_action( 'woocommerce_before_main_content' ); ?>

<?php get_template_part( 'template-parts/wagyu-banner' ); ?>
<?php get_template_part( 'template-parts/wagyu-trust' ); ?>

<main class="wagyu-shop-section">
    <div class="container">

        <!-- Toolbar: breadcrumb + sort + count -->
        <div class="wagyu-toolbar">
            <div class="wagyu-toolbar__breadcrumb"><?php woocommerce_breadcrumb(); ?></div>
            <div class="wagyu-toolbar__controls">
                <?php woocommerce_result_count(); ?>
                <?php woocommerce_catalog_ordering(); ?>
            </div>
        </div>

        <!-- Subcategory tab strip -->
        <?php
        $current_cat = get_queried_object();
        $parent_term = ( $current_cat->parent == 0 ) ? $current_cat : get_term( $current_cat->parent, 'product_cat' );
        $root_term   = $parent_term ?: $current_cat;

        $subcats = get_terms( [
            'taxonomy'   => 'product_cat',
            'parent'     => $root_term->term_id,
            'hide_empty' => false,
            'orderby'    => 'name',
        ] );
        ?>
        <?php if ( ! empty( $subcats ) && ! is_wp_error( $subcats ) ) : ?>
        <nav class="wagyu-subcats" aria-label="<?php esc_attr_e( 'Wagyu subcategories', 'halal-shop-pro' ); ?>">
            <a href="<?php echo esc_url( get_term_link( $root_term ) ); ?>"
               class="wagyu-subcat-btn <?php echo ( $current_cat->slug === $root_term->slug ) ? 'active' : ''; ?>">
                <?php esc_html_e( 'All Wagyu', 'halal-shop-pro' ); ?>
            </a>
            <?php foreach ( $subcats as $sub ) : ?>
            <a href="<?php echo esc_url( get_term_link( $sub ) ); ?>"
               class="wagyu-subcat-btn <?php echo ( $current_cat->slug === $sub->slug ) ? 'active' : ''; ?>">
                <?php echo esc_html( $sub->name ); ?>
                <?php if ( $sub->count ) : ?><span class="wagyu-subcat-count"><?php echo esc_html( $sub->count ); ?></span><?php endif; ?>
            </a>
            <?php endforeach; ?>
        </nav>
        <?php endif; ?>

        <!-- Two-column layout: sidebar + grid -->
        <div class="wagyu-products-wrap">

            <!-- ── SIDEBAR ── -->
            <aside class="wagyu-sidebar" aria-label="<?php esc_attr_e( 'Product filters', 'halal-shop-pro' ); ?>">

                <?php if ( is_active_sidebar( 'sidebar-shop' ) ) : ?>
                    <?php dynamic_sidebar( 'sidebar-shop' ); ?>
                <?php else : ?>

                <!-- Grade filter -->
                <div class="wagyu-filter-box">
                    <h3 class="wagyu-filter-box__title"><?php esc_html_e( 'Filter by Grade', 'halal-shop-pro' ); ?></h3>
                    <div class="wagyu-grade-filters">
                        <?php foreach ( [ 'A5' => 'Supreme (A5)', 'A4' => 'Premium (A4)' ] as $grade => $label ) : ?>
                        <a href="<?php echo esc_url( add_query_arg( 'wagyu_grade', $grade, get_term_link( $root_term ) ) ); ?>"
                           class="wagyu-grade-filter-btn <?php echo ( isset( $_GET['wagyu_grade'] ) && $_GET['wagyu_grade'] === $grade ) ? 'active' : ''; ?>">
                            <span class="wagyu-grade-badge wagyu-grade-<?php echo esc_attr( strtolower( $grade ) ); ?>"><?php echo esc_html( $grade ); ?></span>
                            <?php echo esc_html( $label ); ?>
                        </a>
                        <?php endforeach; ?>
                        <a href="<?php echo esc_url( get_term_link( $current_cat ) ); ?>" class="wagyu-grade-filter-btn">
                            <?php esc_html_e( 'All Grades', 'halal-shop-pro' ); ?>
                        </a>
                    </div>
                </div>

                <!-- Cut filter -->
                <div class="wagyu-filter-box">
                    <h3 class="wagyu-filter-box__title"><?php esc_html_e( 'Cut Type', 'halal-shop-pro' ); ?></h3>
                    <ul class="wagyu-cut-list">
                        <?php
                        $cuts = [
                            'ribeye'     => __( 'Ribeye / リブアイ', 'halal-shop-pro' ),
                            'sirloin'    => __( 'Sirloin / サーロイン', 'halal-shop-pro' ),
                            'tenderloin' => __( 'Tenderloin / ヒレ', 'halal-shop-pro' ),
                            'sliced'     => __( 'Sliced / しゃぶしゃぶしゃぶ', 'halal-shop-pro' ),
                            'mixed'      => __( 'Gift Sets / ギフト', 'halal-shop-pro' ),
                        ];
                        foreach ( $cuts as $key => $label ) : ?>
                        <li>
                            <a href="<?php echo esc_url( add_query_arg( 'wagyu_cut', $key, get_term_link( $root_term ) ) ); ?>"
                               class="<?php echo ( isset( $_GET['wagyu_cut'] ) && $_GET['wagyu_cut'] === $key ) ? 'active' : ''; ?>">
                                <?php echo esc_html( $label ); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Halal cert info box -->
                <div class="wagyu-filter-box wagyu-cert-info">
                    <div class="wagyu-cert-seal">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <p class="wagyu-cert-info__title"><?php esc_html_e( '100% Halal Certified', 'halal-shop-pro' ); ?></p>
                    <p class="wagyu-cert-info__body">
                        <?php esc_html_e( "All Wagyu products are certified by JHFA — Japan's leading Halal certification authority. Certificate copies available on request.", 'halal-shop-pro' ); ?>
                    </p>
                    <a href="<?php echo esc_url( home_url( '/halal-certification' ) ); ?>" class="wagyu-cert-info__link">
                        <?php esc_html_e( 'Learn about our certification →', 'halal-shop-pro' ); ?>
                    </a>
                </div>

                <?php endif; ?>
            </aside>

            <!-- ── PRODUCT GRID ── -->
            <div class="wagyu-products-grid-wrap">
                <?php if ( woocommerce_product_loop() ) : ?>

                <ul class="wagyu-products-grid woocommerce-loop">
                    <?php while ( have_posts() ) : the_post(); global $product; ?>
                    <?php if ( ! $product || ! $product->is_visible() ) continue; ?>

                    <?php
                    $pid        = $product->get_id();
                    $grade      = get_post_meta( $pid, '_wagyu_grade', true );
                    $prefecture = get_post_meta( $pid, '_wagyu_prefecture', true );
                    $is_cert    = get_post_meta( $pid, '_is_halal_certified', true );
                    $is_best    = get_post_meta( $pid, '_is_bestseller', true );
                    $weight     = get_post_meta( $pid, '_wagyu_weight_g', true );
                    $cut        = get_post_meta( $pid, '_wagyu_cut', true );
                    $bms        = get_post_meta( $pid, '_wagyu_bms', true );
                    ?>

                    <li class="wagyu-product-card <?php echo $product->is_on_sale() ? 'is-on-sale' : ''; ?>"
                        itemscope itemtype="http://schema.org/Product">
                        <meta itemprop="name" content="<?php echo esc_attr( $product->get_name() ); ?>">

                        <!-- Image -->
                        <div class="wagyu-product-card__image-wrap">
                            <a href="<?php echo esc_url( $product->get_permalink() ); ?>" tabindex="-1" aria-hidden="true">
                                <?php echo $product->get_image( 'halal-product', [ 'loading' => 'lazy', 'itemprop' => 'image' ] ); ?>
                            </a>

                            <!-- Badges -->
                            <div class="wagyu-product-card__badges">
                                <?php if ( $is_cert === 'yes' ) : ?>
                                <span class="badge badge-halal">&#10003; Halal</span>
                                <?php endif; ?>
                                <?php if ( $grade ) : ?>
                                <span class="badge wagyu-grade-badge wagyu-grade-<?php echo esc_attr( strtolower( $grade ) ); ?>"><?php echo esc_html( $grade ); ?></span>
                                <?php endif; ?>
                                <?php if ( $is_best === 'yes' ) : ?>
                                <span class="badge badge-bestseller">&#9733; Best Seller</span>
                                <?php endif; ?>
                                <?php if ( $product->is_on_sale() ) : woocommerce_show_product_loop_sale_flash(); endif; ?>
                            </div>

                            <!-- Quick View trigger -->
                            <button class="wagyu-quick-view-btn"
                                    data-product-id="<?php echo esc_attr( $pid ); ?>"
                                    aria-label="<?php echo esc_attr( sprintf( __( 'Quick view %s', 'halal-shop-pro' ), $product->get_name() ) ); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <?php esc_html_e( 'Quick View', 'halal-shop-pro' ); ?>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="wagyu-product-card__body">

                            <!-- Meta chips -->
                            <div class="wagyu-product-card__meta">
                                <?php if ( $prefecture ) : ?>
                                <span class="wagyu-meta-origin">&#127471;&#127477; <?php echo esc_html( $prefecture ); ?></span>
                                <?php endif; ?>
                                <?php if ( $weight ) : ?>
                                <span class="wagyu-meta-weight"><?php echo esc_html( number_format( (int) $weight ) ); ?>g</span>
                                <?php endif; ?>
                                <?php if ( $bms ) : ?>
                                <span class="wagyu-meta-bms"><?php echo esc_html( $bms ); ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Title -->
                            <h2 class="wagyu-product-card__title">
                                <a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
                            </h2>

                            <!-- Short description -->
                            <?php $short = $product->get_short_description(); if ( $short ) : ?>
                            <p class="wagyu-product-card__desc"><?php echo wp_kses_post( wp_trim_words( $short, 16 ) ); ?></p>
                            <?php endif; ?>

                            <!-- Rating -->
                            <?php if ( $product->get_rating_count() > 0 ) : ?>
                            <div class="wagyu-product-card__rating">
                                <?php echo wc_get_rating_html( $product->get_average_rating() ); ?>
                                <span class="wagyu-rating-count">(<?php echo esc_html( $product->get_rating_count() ); ?>)</span>
                            </div>
                            <?php endif; ?>

                            <!-- Footer: price + ATC -->
                            <div class="wagyu-product-card__footer">
                                <div class="wagyu-product-card__price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                                    <?php echo $product->get_price_html(); ?>
                                    <meta itemprop="price"         content="<?php echo esc_attr( $product->get_price() ); ?>">
                                    <meta itemprop="priceCurrency" content="<?php echo esc_attr( get_woocommerce_currency() ); ?>">
                                    <link itemprop="availability"  href="<?php echo $product->is_in_stock() ? 'http://schema.org/InStock' : 'http://schema.org/OutOfStock'; ?>">
                                </div>

                                <?php
                                echo apply_filters( 'woocommerce_loop_add_to_cart_link',
                                    sprintf(
                                        '<a href="%s" data-quantity="1" class="%s" %s>%s</a>',
                                        esc_url( $product->add_to_cart_url() ),
                                        esc_attr( implode( ' ', array_filter( [
                                            'wagyu-atc-btn',
                                            'button',
                                            'product_type_' . $product->get_type(),
                                            $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
                                            $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
                                        ] ) ) ),
                                        wc_implode_html_attributes( [
                                            'data-product_id'  => $product->get_id(),
                                            'data-product_sku' => $product->get_sku(),
                                            'aria-label'       => $product->add_to_cart_description(),
                                            'rel'              => 'nofollow',
                                        ] ),
                                        esc_html( $product->add_to_cart_text() )
                                    ),
                                    $product
                                );
                                ?>
                            </div>
                        </div><!-- .wagyu-product-card__body -->
                    </li>

                    <?php endwhile; ?>
                </ul>

                <!-- Pagination -->
                <div class="wagyu-pagination">
                    <?php woocommerce_pagination(); ?>
                </div>

                <?php else : ?>
                <div class="wagyu-no-products">
                    <?php do_action( 'woocommerce_no_products_found' ); wc_get_template( 'loop/no-products-found.php' ); ?>
                </div>
                <?php endif; ?>

                <?php wp_reset_postdata(); ?>
            </div><!-- .wagyu-products-grid-wrap -->

        </div><!-- .wagyu-products-wrap -->

    </div><!-- .container -->
</main>

<!-- ── QUICK VIEW MODAL ── -->
<div class="wagyu-modal-overlay" id="wagyuModalOverlay" aria-hidden="true" role="presentation">
    <div class="wagyu-modal" id="wagyuModal" role="dialog" aria-modal="true"
         aria-label="<?php esc_attr_e( 'Product Quick View', 'halal-shop-pro' ); ?>">
        <button class="wagyu-modal-close" id="wagyuModalClose"
                aria-label="<?php esc_attr_e( 'Close quick view', 'halal-shop-pro' ); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
        <div class="wagyu-modal-body" id="wagyuModalBody">
            <div class="wagyu-modal-loading"><div class="loading-spinner"></div></div>
        </div>
    </div>
</div>

<?php do_action( 'woocommerce_after_main_content' ); ?>

<?php get_footer( 'shop' ); ?>
