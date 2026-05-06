<section class="hero-section" aria-label="<?php esc_attr_e( 'Hero Banner', 'halal-shop-pro' ); ?>">
    <div class="hero-inner">
        <div class="hero-content">
            <div class="hero-badge">
                🕌 <?php esc_html_e( 'ハラール認証取得 | Halal Certified', 'halal-shop-pro' ); ?>
            </div>

            <h1 class="hero-title">
                <?php echo nl2br( esc_html( get_theme_mod( 'hero_title', "ハラールフードの\n安心・安全な\nオンラインショップ" ) ) ); ?>
            </h1>

            <p class="hero-subtitle">
                <?php echo esc_html( get_theme_mod( 'hero_subtitle', 'ムスリムフレンドリーな食品を全国にお届け。厳選されたハラール認証食品を取り揃えています。\nFor Muslim residents and visitors in Japan — your trusted Halal food destination.' ) ); ?>
            </p>

            <div class="hero-actions">
                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="btn btn-secondary btn-lg">
                    🛒 <?php esc_html_e( '商品を見る / Shop Now', 'halal-shop-pro' ); ?>
                </a>
                <?php endif; ?>
                <a href="<?php echo esc_url( home_url( '/halal-certification' ) ); ?>" class="btn btn-outline-white btn-lg">
                    <?php esc_html_e( 'Halal認証とは？', 'halal-shop-pro' ); ?>
                </a>
            </div>

            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat__number">500+</div>
                    <div class="hero-stat__label"><?php esc_html_e( 'ハラール商品', 'halal-shop-pro' ); ?></div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat__number">10,000+</div>
                    <div class="hero-stat__label"><?php esc_html_e( '顧客数', 'halal-shop-pro' ); ?></div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat__number">5</div>
                    <div class="hero-stat__label"><?php esc_html_e( '対応言語', 'halal-shop-pro' ); ?></div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat__number">翌日</div>
                    <div class="hero-stat__label"><?php esc_html_e( '配送対応', 'halal-shop-pro' ); ?></div>
                </div>
            </div>
        </div>

        <div class="hero-image">
            <?php $hero_img = get_theme_mod( 'hero_image', '' ); ?>
            <?php if ( $hero_img ) : ?>
                <img src="<?php echo esc_url( $hero_img ); ?>" alt="<?php esc_attr_e( 'Halal Food Collection', 'halal-shop-pro' ); ?>" loading="eager">
            <?php else : ?>
                <!-- Placeholder hero image -->
                <div style="width:480px;height:360px;background:rgba(255,255,255,0.15);border-radius:24px;display:flex;align-items:center;justify-content:center;font-size:8rem;">🍱</div>
            <?php endif; ?>
        </div>
    </div>
</section>
