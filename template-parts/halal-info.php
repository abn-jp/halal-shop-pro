<section class="halal-info-section section" aria-labelledby="halal-info-heading">
    <div class="container">
        <div class="halal-info-inner">

            <div class="halal-info__content">
                <h2 class="section-title" id="halal-info-heading"><?php esc_html_e( 'ハラールとは？', 'halal-shop-pro' ); ?></h2>
                <p class="halal-info__text">
                    <?php esc_html_e( 'ハラール（Halal）とは、アラビア語で「許されたもの」を意味し、イスラム法（シャリーア）に従って生産・加工された食品や製品のことです。', 'halal-shop-pro' ); ?>
                </p>
                <p class="halal-info__text">
                    <?php esc_html_e( '当店のすべての商品は、権威ある認証機関によってハラール認証を取得しており、ムスリムの皆様が安心してお買い求めいただけます。', 'halal-shop-pro' ); ?>
                </p>

                <div class="halal-features">
                    <div class="halal-feature-item">
                        <div class="halal-feature-item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <div>
                            <div class="halal-feature-item__title"><?php esc_html_e( '厳格な原材料管理', 'halal-shop-pro' ); ?></div>
                            <div class="halal-feature-item__desc"><?php esc_html_e( 'Strict ingredient control — no pork or alcohol', 'halal-shop-pro' ); ?></div>
                        </div>
                    </div>

                    <div class="halal-feature-item">
                        <div class="halal-feature-item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div>
                            <div class="halal-feature-item__title"><?php esc_html_e( '公式認証機関認定', 'halal-shop-pro' ); ?></div>
                            <div class="halal-feature-item__desc"><?php esc_html_e( 'Certified by JHFA, MUI, JAKIM & more', 'halal-shop-pro' ); ?></div>
                        </div>
                    </div>

                    <div class="halal-feature-item">
                        <div class="halal-feature-item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </div>
                        <div>
                            <div class="halal-feature-item__title"><?php esc_html_e( '完全な透明性', 'halal-shop-pro' ); ?></div>
                            <div class="halal-feature-item__desc"><?php esc_html_e( 'Full ingredient transparency on every product', 'halal-shop-pro' ); ?></div>
                        </div>
                    </div>

                    <div class="halal-feature-item">
                        <div class="halal-feature-item__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                        </div>
                        <div>
                            <div class="halal-feature-item__title"><?php esc_html_e( 'ムスリム専門スタッフ', 'halal-shop-pro' ); ?></div>
                            <div class="halal-feature-item__desc"><?php esc_html_e( 'Muslim staff available for support', 'halal-shop-pro' ); ?></div>
                        </div>
                    </div>
                </div>

                <a href="<?php echo esc_url( home_url( '/halal-certification' ) ); ?>" class="btn btn-primary">
                    <?php esc_html_e( '認証の詳細を見る / Learn More', 'halal-shop-pro' ); ?>
                </a>
            </div>

            <div class="halal-info__image">
                <div style="width:100%;aspect-ratio:4/3;background:linear-gradient(135deg,#e8f5e9,#c8e6c9);border-radius:24px;display:flex;align-items:center;justify-content:center;font-size:6rem;position:relative;">
                    🕌
                    <div class="halal-cert-badge">
                        <div class="halal-cert-badge__icon">🏆</div>
                        <div class="halal-cert-badge__text">
                            <strong><?php esc_html_e( 'Halal Certified', 'halal-shop-pro' ); ?></strong>
                            <span><?php esc_html_e( 'JHFA・MUI・JAKIM', 'halal-shop-pro' ); ?></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
