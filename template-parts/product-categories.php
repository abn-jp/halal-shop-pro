<section class="categories-section section" aria-labelledby="cat-heading">
    <div class="container">
        <h2 class="section-title" id="cat-heading"><?php esc_html_e( '商品カテゴリ', 'halal-shop-pro' ); ?></h2>
        <p class="section-subtitle"><?php esc_html_e( 'Browse by Category — すべてハラール認証済み', 'halal-shop-pro' ); ?></p>

        <?php
        $categories = get_terms( [
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'parent'     => 0,
            'number'     => 6,
            'exclude'    => [ get_option( 'default_product_cat' ) ],
        ] );

        $default_cats = [
            [ 'name' => '肉・肉加工品', 'en' => 'Meat & Poultry',    'icon' => '🥩', 'slug' => 'meat-poultry' ],
            [ 'name' => '調味料・ソース', 'en' => 'Seasonings',        'icon' => '🧂', 'slug' => 'seasonings' ],
            [ 'name' => '冷凍食品',       'en' => 'Frozen Foods',      'icon' => '❄️', 'slug' => 'frozen-foods' ],
            [ 'name' => 'お菓子',         'en' => 'Snacks',            'icon' => '🍘', 'slug' => 'snacks' ],
            [ 'name' => '飲料',           'en' => 'Beverages',         'icon' => '🧃', 'slug' => 'beverages' ],
            [ 'name' => 'インスタント',   'en' => 'Instant Foods',     'icon' => '🍜', 'slug' => 'instant-foods' ],
        ];
        ?>

        <div class="category-grid">
            <?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
                foreach ( $categories as $cat ) :
                    $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
                    $icon = '🛒';
                    foreach ( $default_cats as $dc ) {
                        if ( strpos( $cat->slug, $dc['slug'] ) !== false ) { $icon = $dc['icon']; break; }
                    }
            ?>
            <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="category-card">
                <div class="category-card__icon">
                    <?php if ( $thumbnail_id ) : ?>
                        <img src="<?php echo esc_url( wp_get_attachment_image_url( $thumbnail_id, 'halal-thumb' ) ); ?>" alt="<?php echo esc_attr( $cat->name ); ?>">
                    <?php else : ?>
                        <?php echo esc_html( $icon ); ?>
                    <?php endif; ?>
                </div>
                <span class="category-card__name"><?php echo esc_html( $cat->name ); ?></span>
                <span class="category-card__count"><?php echo esc_html( $cat->count ); ?> <?php esc_html_e( '商品', 'halal-shop-pro' ); ?></span>
            </a>
            <?php endforeach;
            else :
                foreach ( $default_cats as $cat ) :
            ?>
            <a href="<?php echo esc_url( home_url( '/product-category/' . $cat['slug'] ) ); ?>" class="category-card">
                <div class="category-card__icon"><?php echo esc_html( $cat['icon'] ); ?></div>
                <span class="category-card__name"><?php echo esc_html( $cat['name'] ); ?></span>
                <span class="category-card__count"><?php echo esc_html( $cat['en'] ); ?></span>
            </a>
            <?php endforeach; endif; ?>
        </div>
    </div>
</section>
