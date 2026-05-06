<section class="products-section section" aria-labelledby="fp-heading">
    <div class="container">
        <h2 class="section-title" id="fp-heading"><?php esc_html_e( '人気商品 / Featured Products', 'halal-shop-pro' ); ?></h2>
        <p class="section-subtitle"><?php esc_html_e( 'Top picks loved by our Muslim customers worldwide', 'halal-shop-pro' ); ?></p>

        <?php if ( class_exists( 'WooCommerce' ) ) :
            $featured = wc_get_products( [
                'status'   => 'publish',
                'limit'    => 8,
                'featured' => true,
                'orderby'  => 'popularity',
            ] );

            if ( empty( $featured ) ) {
                $featured = wc_get_products( [ 'status' => 'publish', 'limit' => 8, 'orderby' => 'popularity' ] );
            }
        ?>
        <div class="product-grid">
            <?php foreach ( $featured as $product ) :
                $is_certified = get_post_meta( $product->get_id(), '_is_halal_certified', true );
            ?>
            <div class="product-card">
                <div class="product-card__image">
                    <a href="<?php echo esc_url( $product->get_permalink() ); ?>">
                        <?php echo $product->get_image( 'halal-product' ); ?>
                    </a>
                    <div class="product-card__badges">
                        <?php if ( $is_certified === 'yes' ) : ?>
                        <span class="badge badge-halal">✓ Halal</span>
                        <?php endif; ?>
                        <?php if ( $product->is_on_sale() ) : ?>
                        <span class="badge badge-sale">SALE</span>
                        <?php elseif ( $product->is_featured() ) : ?>
                        <span class="badge badge-new">NEW</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-card__actions">
                        <button class="product-action-btn" title="<?php esc_attr_e( 'Add to Wishlist', 'halal-shop-pro' ); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                        </button>
                        <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="product-action-btn" title="<?php esc_attr_e( 'Quick View', 'halal-shop-pro' ); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </div>
                </div>

                <div class="product-card__body">
                    <?php $cats = $product->get_category_ids();
                    if ( ! empty( $cats ) ) :
                        $cat_obj = get_term( $cats[0], 'product_cat' ); ?>
                    <div class="product-card__category"><?php echo esc_html( $cat_obj->name ?? '' ); ?></div>
                    <?php endif; ?>

                    <h3 class="product-card__name">
                        <a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
                    </h3>

                    <?php if ( $product->get_rating_count() > 0 ) : ?>
                    <div class="product-card__rating">
                        <div class="stars">
                            <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="<?php echo $i <= round( $product->get_average_rating() ) ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-count">(<?php echo $product->get_rating_count(); ?>)</span>
                    </div>
                    <?php endif; ?>

                    <div class="product-card__footer">
                        <div class="product-card__price">
                            <?php if ( $product->is_on_sale() ) : ?>
                            <span class="price-original"><?php echo wc_price( $product->get_regular_price() ); ?></span>
                            <?php endif; ?>
                            <span class="price-current"><?php echo $product->get_price_html(); ?></span>
                        </div>
                        <?php if ( $product->is_in_stock() ) : ?>
                        <button
                            class="add-to-cart-btn"
                            data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
                            data-product-url="<?php echo esc_url( $product->add_to_cart_url() ); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                            <?php esc_html_e( 'カートへ', 'halal-shop-pro' ); ?>
                        </button>
                        <?php else : ?>
                        <span class="badge badge-popular"><?php esc_html_e( '在庫なし', 'halal-shop-pro' ); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center" style="margin-top:2.5rem">
            <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="btn btn-outline btn-lg">
                <?php esc_html_e( 'すべての商品を見る / View All Products', 'halal-shop-pro' ); ?>
            </a>
        </div>

        <?php else : ?>
        <p class="text-center text-muted"><?php esc_html_e( 'WooCommerce is required to display products.', 'halal-shop-pro' ); ?></p>
        <?php endif; ?>
    </div>
</section>
