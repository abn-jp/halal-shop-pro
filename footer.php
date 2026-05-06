    </div><!-- #content -->

    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-inner">
                <h2 class="newsletter-title"><?php esc_html_e( '最新情報をお届けします', 'halal-shop-pro' ); ?></h2>
                <p class="newsletter-subtitle"><?php esc_html_e( 'Subscribe for new arrivals, exclusive deals & Halal updates', 'halal-shop-pro' ); ?></p>
                <form class="newsletter-form" id="newsletterForm" method="post" action="#">
                    <?php wp_nonce_field( 'halal-newsletter', 'newsletter_nonce' ); ?>
                    <input type="email" name="newsletter_email" placeholder="<?php esc_attr_e( 'メールアドレス / Email address', 'halal-shop-pro' ); ?>" required aria-label="Email">
                    <button type="submit" class="btn btn-secondary"><?php esc_html_e( '登録 / Subscribe', 'halal-shop-pro' ); ?></button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="site-footer" role="contentinfo">
        <div class="container">
            <div class="footer-top">
                <div class="footer-grid">

                    <!-- Column 1: About -->
                    <div class="footer-col footer-col--about">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-logo">
                            <span class="footer-logo__text">Halal<span>Shop</span></span>
                        </a>
                        <p class="footer-desc"><?php echo esc_html( get_theme_mod( 'footer_about', 'ハラールショップは、日本に住むムスリムの方々や訪日旅行者の皆様に、安心・安全なハラール食品をお届けするオンラインショップです。' ) ); ?></p>
                        <div class="footer-social">
                            <?php
                            $socials = [
                                'instagram' => ['Instagram', '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>'],
                                'facebook'  => ['Facebook', '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>'],
                                'twitter'   => ['Twitter / X', '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.835L1.254 2.25H8.08l4.259 5.632zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>'],
                                'youtube'   => ['YouTube', '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>'],
                            ];
                            foreach ( $socials as $key => $data ) :
                                $url = get_theme_mod( 'social_' . $key, '' );
                                if ( $url ) :
                            ?>
                            <a href="<?php echo esc_url( $url ); ?>" class="social-btn" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $data[0] ); ?>">
                                <?php echo $data[1]; ?>
                            </a>
                            <?php endif; endforeach; ?>
                        </div>
                        <!-- Certification Logos -->
                        <div class="footer-certifications" style="margin-top:1rem">
                            <span class="cert-badge">🕌 JHFA認証</span>
                            <span class="cert-badge">🛡 MUI認証</span>
                            <span class="cert-badge">✓ JAKIM</span>
                        </div>
                    </div>

                    <!-- Column 2: Products -->
                    <div class="footer-col">
                        <h3 class="footer-heading"><?php esc_html_e( '商品カテゴリ', 'halal-shop-pro' ); ?></h3>
                        <ul class="footer-links">
                            <?php
                            $cats = [ 'meat-poultry' => '肉・肉加工品', 'seasonings' => '調味料・ソース', 'frozen-foods' => '冷凍食品', 'snacks' => 'お菓子・スナック', 'beverages' => '飲料', 'instant-foods' => 'インスタント食品' ];
                            foreach ( $cats as $slug => $name ) :
                                $term = get_term_by( 'slug', $slug, 'product_cat' );
                                $url  = $term ? get_term_link( $term ) : home_url( '/shop' );
                            ?>
                            <li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $name ); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Column 3: Company -->
                    <div class="footer-col">
                        <h3 class="footer-heading"><?php esc_html_e( '会社情報', 'halal-shop-pro' ); ?></h3>
                        <ul class="footer-links">
                            <li><a href="<?php echo esc_url( home_url( '/about' ) ); ?>"><?php esc_html_e( '会社概要', 'halal-shop-pro' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/halal-certification' ) ); ?>"><?php esc_html_e( 'ハラール認証について', 'halal-shop-pro' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/faq' ) ); ?>"><?php esc_html_e( 'よくある質問 (FAQ)', 'halal-shop-pro' ); ?></a></li>
                            <li><a href="<?php echo esc_url( home_url( '/contact' ) ); ?>"><?php esc_html_e( 'お問い合わせ', 'halal-shop-pro' ); ?></a></li>
                            <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                            <li><a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>"><?php esc_html_e( 'マイアカウント', 'halal-shop-pro' ); ?></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Column 4: Contact -->
                    <div class="footer-col">
                        <h3 class="footer-heading"><?php esc_html_e( 'お問い合わせ', 'halal-shop-pro' ); ?></h3>
                        <div class="footer-contact-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span><?php echo esc_html( get_theme_mod( 'company_address', '東京都〇〇区〇〇 X-X-X' ) ); ?></span>
                        </div>
                        <div class="footer-contact-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.82 19.79 19.79 0 01.07 2.18 2 2 0 012.03 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.18 6.18l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14.92v2z"/></svg>
                            <span><?php echo esc_html( get_theme_mod( 'company_phone', '03-XXXX-XXXX' ) ); ?></span>
                        </div>
                        <div class="footer-contact-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <a href="mailto:<?php echo esc_attr( get_theme_mod( 'company_email', 'info@halalshop.example.com' ) ); ?>"><?php echo esc_html( get_theme_mod( 'company_email', 'info@halalshop.example.com' ) ); ?></a>
                        </div>
                        <div class="footer-contact-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span><?php echo esc_html( get_theme_mod( 'company_hours', '月–金 9:00–18:00 (JST)' ) ); ?></span>
                        </div>
                    </div>

                </div><!-- .footer-grid -->
            </div><!-- .footer-top -->

            <div class="footer-bottom">
                <p class="footer-copyright"><?php echo esc_html( get_theme_mod( 'footer_copyright', '© ' . date('Y') . ' Halal Shop Pro. All rights reserved.' ) ); ?></p>
                <nav class="footer-legal" aria-label="<?php esc_attr_e( 'Legal', 'halal-shop-pro' ); ?>">
                    <a href="<?php echo esc_url( home_url( '/privacy-policy' ) ); ?>"><?php esc_html_e( 'プライバシーポリシー', 'halal-shop-pro' ); ?></a>
                    <a href="<?php echo esc_url( home_url( '/terms' ) ); ?>"><?php esc_html_e( '利用規約', 'halal-shop-pro' ); ?></a>
                    <a href="<?php echo esc_url( home_url( '/tokushoho' ) ); ?>"><?php esc_html_e( '特定商取引法', 'halal-shop-pro' ); ?></a>
                    <a href="<?php echo esc_url( home_url( '/shipping' ) ); ?>"><?php esc_html_e( '配送について', 'halal-shop-pro' ); ?></a>
                </nav>
                <div class="footer-payment" aria-label="<?php esc_attr_e( 'Payment methods', 'halal-shop-pro' ); ?>">
                    <span class="payment-icon">VISA</span>
                    <span class="payment-icon">MC</span>
                    <span class="payment-icon">AMEX</span>
                    <span class="payment-icon">PayPay</span>
                    <span class="payment-icon">銀振</span>
                </div>
            </div><!-- .footer-bottom -->
        </div><!-- .container -->
    </footer><!-- .site-footer -->

</div><!-- #page -->

<!-- Back to Top -->
<button class="back-to-top" id="backToTop" aria-label="<?php esc_attr_e( 'Back to top', 'halal-shop-pro' ); ?>">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="18 15 12 9 6 15"/></svg>
</button>

<?php wp_footer(); ?>
</body>
</html>
