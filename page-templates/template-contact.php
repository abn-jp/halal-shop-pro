<?php
/**
 * Template Name: Contact
 */
get_header();
?>

<div class="page-hero">
    <div class="container">
        <h1><?php esc_html_e( 'お問い合わせ / Contact Us', 'halal-shop-pro' ); ?></h1>
        <p><?php esc_html_e( 'We respond in Japanese, English, Indonesian, Arabic & Malay', 'halal-shop-pro' ); ?></p>
    </div>
</div>

<main class="section">
    <div class="container">
        <div class="contact-grid">

            <!-- Contact Form -->
            <div>
                <h2 style="color:var(--color-primary);margin-bottom:1.5rem"><?php esc_html_e( 'メッセージを送る / Send Message', 'halal-shop-pro' ); ?></h2>

                <?php if ( isset( $_GET['sent'] ) && $_GET['sent'] === '1' ) : ?>
                <div style="background:var(--color-primary-pale);border:1px solid var(--color-primary);border-radius:8px;padding:1rem;margin-bottom:1.5rem;color:var(--color-primary)">
                    ✅ <?php esc_html_e( 'お問い合わせありがとうございます。24時間以内にご返答いたします。/ Thank you! We will reply within 24 hours.', 'halal-shop-pro' ); ?>
                </div>
                <?php endif; ?>

                <!-- Use Contact Form 7 shortcode if available, otherwise fallback form -->
                <?php if ( function_exists( 'wpcf7_contact_form' ) ) :
                    echo do_shortcode( '[contact-form-7 id="1" title="Contact Form"]' );
                else : ?>
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <?php wp_nonce_field( 'halal-contact', 'contact_nonce' ); ?>
                    <input type="hidden" name="action" value="halal_contact_form">

                    <div class="form-group">
                        <label class="form-label" for="contact_name"><?php esc_html_e( 'お名前 / Name *', 'halal-shop-pro' ); ?></label>
                        <input class="form-control" type="text" id="contact_name" name="contact_name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact_email"><?php esc_html_e( 'メールアドレス / Email *', 'halal-shop-pro' ); ?></label>
                        <input class="form-control" type="email" id="contact_email" name="contact_email" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact_language"><?php esc_html_e( '対応言語 / Preferred Language', 'halal-shop-pro' ); ?></label>
                        <select class="form-control" id="contact_language" name="contact_language">
                            <option value="ja">日本語</option>
                            <option value="en">English</option>
                            <option value="id">Bahasa Indonesia</option>
                            <option value="ar">العربية</option>
                            <option value="ms">Bahasa Melayu</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact_subject"><?php esc_html_e( '件名 / Subject *', 'halal-shop-pro' ); ?></label>
                        <select class="form-control" id="contact_subject" name="contact_subject" required>
                            <option value=""><?php esc_html_e( '選択してください / Please select', 'halal-shop-pro' ); ?></option>
                            <option value="order"><?php esc_html_e( 'ご注文について / About my order', 'halal-shop-pro' ); ?></option>
                            <option value="halal"><?php esc_html_e( 'ハラール認証について / Halal certification', 'halal-shop-pro' ); ?></option>
                            <option value="shipping"><?php esc_html_e( '配送について / Shipping inquiry', 'halal-shop-pro' ); ?></option>
                            <option value="product"><?php esc_html_e( '商品について / Product inquiry', 'halal-shop-pro' ); ?></option>
                            <option value="other"><?php esc_html_e( 'その他 / Other', 'halal-shop-pro' ); ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact_message"><?php esc_html_e( 'メッセージ / Message *', 'halal-shop-pro' ); ?></label>
                        <textarea class="form-control" id="contact_message" name="contact_message" rows="6" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-full">
                        <?php esc_html_e( '送信する / Send Message', 'halal-shop-pro' ); ?>
                    </button>
                </form>
                <?php endif; ?>
            </div>

            <!-- Contact Info -->
            <div>
                <h2 style="color:var(--color-primary);margin-bottom:1.5rem"><?php esc_html_e( '連絡先 / Contact Info', 'halal-shop-pro' ); ?></h2>

                <div style="display:flex;flex-direction:column;gap:1.5rem">
                    <?php
                    $items = [
                        [ '📍', '住所 / Address', get_theme_mod( 'company_address', '東京都〇〇区〇〇 X-X-X' ) ],
                        [ '📞', '電話 / Phone', get_theme_mod( 'company_phone', '03-XXXX-XXXX' ) ],
                        [ '✉️', 'Email', get_theme_mod( 'company_email', 'info@halalshop.example.com' ) ],
                        [ '🕐', '営業時間 / Hours', get_theme_mod( 'company_hours', '月–金 9:00–18:00 (JST)' ) ],
                    ];
                    foreach ( $items as $item ) : ?>
                    <div style="display:flex;gap:1rem;align-items:flex-start;padding:1.25rem;background:var(--color-gray-100);border-radius:12px">
                        <span style="font-size:1.5rem"><?php echo esc_html( $item[0] ); ?></span>
                        <div>
                            <strong style="color:var(--color-primary);display:block;margin-bottom:.25rem"><?php echo esc_html( $item[1] ); ?></strong>
                            <span style="color:var(--color-text)"><?php echo esc_html( $item[2] ); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Language support badges -->
                <div style="margin-top:2rem;padding:1.5rem;border:1px solid var(--color-border);border-radius:12px">
                    <h3 style="margin-bottom:1rem;font-size:1rem"><?php esc_html_e( '対応言語 / Languages Supported', 'halal-shop-pro' ); ?></h3>
                    <div style="display:flex;flex-wrap:wrap;gap:.5rem">
                        <?php foreach ( [ '🇯🇵 日本語', '🇬🇧 English', '🇮🇩 Indonesia', '🇸🇦 العربية', '🇲🇾 Melayu' ] as $lang ) : ?>
                        <span style="background:var(--color-primary-pale);color:var(--color-primary);padding:.25rem .75rem;border-radius:999px;font-size:.875rem;font-weight:500">
                            <?php echo esc_html( $lang ); ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>

        <?php the_content(); ?>
    </div>
</main>

<?php get_footer(); ?>
