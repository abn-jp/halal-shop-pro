<?php
/**
 * Template Name: FAQ
 */
get_header();
?>

<div class="page-hero">
    <div class="container">
        <h1><?php esc_html_e( 'よくある質問 / FAQ', 'halal-shop-pro' ); ?></h1>
        <p><?php esc_html_e( 'Frequently Asked Questions about our Halal products and services', 'halal-shop-pro' ); ?></p>
    </div>
</div>

<main class="section">
    <div class="container">
        <div class="faq-accordion">

            <?php
            $faqs = [
                [
                    'q' => '商品はすべてハラール認証を受けていますか？ / Are all products Halal certified?',
                    'a' => 'はい。当店で販売するすべての食品はJHFA、MUI、JAKIMなどの公認ハラール認証機関による認証を取得しています。各商品ページで認証番号・認証機関名をご確認いただけます。Yes, all food products sold in our store are certified by recognized Halal certification bodies such as JHFA (Japan), MUI (Indonesia), and JAKIM (Malaysia).',
                ],
                [
                    'q' => '送料はいくらですか？ / What are the shipping fees?',
                    'a' => '¥5,000以上のご注文は全国送料無料です。¥5,000未満の場合、ヤマト運輸または佐川急便で配送地域により¥600〜¥1,200の送料がかかります。Overseas shipping rates vary by destination — please check the shipping information page.',
                ],
                [
                    'q' => '支払い方法は何がありますか？ / What payment methods do you accept?',
                    'a' => 'クレジットカード（Visa, Mastercard, AMEX）、PayPay、銀行振込に対応しています。決済はすべてStripeによる安全な暗号化処理です。We accept credit cards (Visa, Mastercard, Amex), PayPay, and bank transfer. All payments are secured by Stripe.',
                ],
                [
                    'q' => '豚肉・アルコール不使用ですか？ / Are products free from pork and alcohol?',
                    'a' => 'はい。ハラール認証商品はすべて豚肉・アルコール・ハラーム成分不使用です。また製造工程でも交差汚染防止措置が取られています。Yes, all Halal certified products are free from pork, alcohol, and other Haram ingredients. Manufacturing processes also prevent cross-contamination.',
                ],
                [
                    'q' => '海外への発送はできますか？ / Do you ship internationally?',
                    'a' => '現在、日本国内への配送を主としていますが、一部商品については国際配送も承っております。お問い合わせフォームよりご相談ください。We primarily ship within Japan, but international shipping is available for select products. Please contact us for details.',
                ],
                [
                    'q' => 'アラビア語・インドネシア語でのサポートはありますか？ / Do you offer support in Arabic/Indonesian?',
                    'a' => 'はい。日本語・英語・インドネシア語・アラビア語・マレー語に対応したカスタマーサポートをご用意しています。メール・お問い合わせフォームにてご連絡ください。Yes! We offer customer support in Japanese, English, Indonesian, Arabic, and Malay. Contact us via email or the contact form.',
                ],
                [
                    'q' => '返品・交換はできますか？ / What is your return policy?',
                    'a' => '商品到着後7日以内であれば、未開封・未使用の商品に限り返品・交換を承ります。食品の性質上、開封済みの商品は返品をお受けできません。Returns and exchanges are accepted within 7 days of delivery for unopened/unused items. Food items cannot be returned once opened.',
                ],
                [
                    'q' => 'ムスリム向けの成分情報はどこで確認できますか？ / Where can I find ingredient information?',
                    'a' => '各商品ページの「ハラール認証」タブおよび「成分・原材料」タブに詳細な情報を記載しています。不明な点はお気軽にお問い合わせください。Detailed ingredient information is available on each product page under the "Halal Certification" and "Ingredients" tabs.',
                ],
            ];

            foreach ( $faqs as $i => $faq ) : ?>
            <div class="faq-item">
                <button class="faq-question" aria-expanded="false" aria-controls="faq-answer-<?php echo $i; ?>">
                    <?php echo esc_html( $faq['q'] ); ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="faq-answer" id="faq-answer-<?php echo $i; ?>" role="region">
                    <p><?php echo esc_html( $faq['a'] ); ?></p>
                </div>
            </div>
            <?php endforeach; ?>

        </div>

        <div style="text-align:center;margin-top:3rem;padding:2rem;background:var(--color-primary-pale);border-radius:16px">
            <h3><?php esc_html_e( 'まだ質問がありますか？ / Still have questions?', 'halal-shop-pro' ); ?></h3>
            <p style="color:var(--color-text-light);margin:.5rem 0 1.5rem"><?php esc_html_e( 'Our multilingual support team is happy to help!', 'halal-shop-pro' ); ?></p>
            <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn btn-primary">
                <?php esc_html_e( 'お問い合わせ / Contact Us', 'halal-shop-pro' ); ?>
            </a>
        </div>

        <?php the_content(); ?>
    </div>
</main>

<?php get_footer(); ?>
