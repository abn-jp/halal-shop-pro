<section class="testimonials-section section" aria-labelledby="reviews-heading">
    <div class="container">
        <h2 class="section-title" id="reviews-heading"><?php esc_html_e( 'お客様の声 / Customer Reviews', 'halal-shop-pro' ); ?></h2>
        <p class="section-subtitle"><?php esc_html_e( 'What our Muslim customers around the world say', 'halal-shop-pro' ); ?></p>

        <div class="testimonials-grid">

            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <?php for ( $i = 0; $i < 5; $i++ ) : ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <?php endfor; ?>
                </div>
                <p class="testimonial-text"><?php esc_html_e( '日本で生活して5年、ハラール食品を探すのがとても大変でした。このショップを見つけてから、毎週利用しています。認証もしっかりしていて安心です！', 'halal-shop-pro' ); ?></p>
                <div class="testimonial-author">
                    <div class="testimonial-author__avatar">A</div>
                    <div>
                        <div class="testimonial-author__name">Ahmad Raza</div>
                        <div class="testimonial-author__info"><?php esc_html_e( '東京在住 / Pakistani', 'halal-shop-pro' ); ?></div>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <?php for ( $i = 0; $i < 5; $i++ ) : ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <?php endfor; ?>
                </div>
                <p class="testimonial-text"><?php esc_html_e( 'Saya selalu belanja di sini karena produknya terjamin halal dan berkualitas. Pengiriman cepat dan pelayanan dalam bahasa Indonesia sangat membantu!', 'halal-shop-pro' ); ?></p>
                <div class="testimonial-author">
                    <div class="testimonial-author__avatar">S</div>
                    <div>
                        <div class="testimonial-author__name">Siti Nurhaliza</div>
                        <div class="testimonial-author__info"><?php esc_html_e( '大阪在住 / Indonesian', 'halal-shop-pro' ); ?></div>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <?php for ( $i = 0; $i < 5; $i++ ) : ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <?php endfor; ?>
                </div>
                <p class="testimonial-text"><?php esc_html_e( 'As a tourist visiting Japan, I was worried about finding halal food. This shop saved me! The website in Arabic made ordering so easy. Highly recommended for Muslim travelers!', 'halal-shop-pro' ); ?></p>
                <div class="testimonial-author">
                    <div class="testimonial-author__avatar">M</div>
                    <div>
                        <div class="testimonial-author__name">Mohammed Al-Rashid</div>
                        <div class="testimonial-author__info"><?php esc_html_e( '訪日観光客 / Saudi Arabia', 'halal-shop-pro' ); ?></div>
                    </div>
                </div>
            </div>

        </div>

        <?php if ( class_exists( 'WooCommerce' ) ) : ?>
        <div class="text-center" style="margin-top:2rem">
            <a href="<?php echo esc_url( home_url( '/#reviews' ) ); ?>" class="btn btn-outline">
                <?php esc_html_e( 'すべてのレビューを見る / Read All Reviews', 'halal-shop-pro' ); ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>
