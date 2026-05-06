<?php
/**
 * Template Name: Halal Certification
 */
get_header();
?>

<div class="page-hero">
    <div class="container">
        <h1><?php esc_html_e( 'ハラール認証について / Halal Certification', 'halal-shop-pro' ); ?></h1>
        <p><?php esc_html_e( 'Our commitment to quality, safety, and Islamic compliance', 'halal-shop-pro' ); ?></p>
    </div>
</div>

<main>

    <!-- What is Halal? -->
    <section class="section">
        <div class="container" style="max-width:900px">
            <h2 class="section-title"><?php esc_html_e( 'ハラールとは？ / What is Halal?', 'halal-shop-pro' ); ?></h2>
            <p class="section-subtitle"><?php esc_html_e( 'Understanding the Islamic dietary guidelines', 'halal-shop-pro' ); ?></p>

            <div style="background:var(--color-primary-pale);border-left:4px solid var(--color-primary);padding:1.5rem 2rem;border-radius:0 12px 12px 0;margin-bottom:2rem">
                <p style="font-size:1.125rem;color:var(--color-text);line-height:1.8;margin:0">
                    <?php esc_html_e( '「ハラール（حلال）」はアラビア語で「許されたもの・合法なもの」を意味します。イスラム法（シャリーア）に基づき、ムスリムが摂取・使用することが許可された食品・製品を指します。反対に「ハラーム（حرام）」は禁止されたものを指し、豚肉・アルコール・適切な処理を受けていない肉などが含まれます。', 'halal-shop-pro' ); ?>
                </p>
            </div>

            <div class="grid grid-2" style="gap:1.5rem;margin-bottom:3rem">
                <div style="padding:1.5rem;border:1px solid var(--color-primary);border-radius:12px">
                    <h3 style="color:var(--color-primary);margin-bottom:.75rem">✅ <?php esc_html_e( 'ハラール（許可）/ Halal (Permitted)', 'halal-shop-pro' ); ?></h3>
                    <ul style="color:var(--color-text-light);line-height:2;padding-left:1.25rem">
                        <li><?php esc_html_e( '適切なと畜処理を受けた牛・鶏・羊肉', 'halal-shop-pro' ); ?></li>
                        <li><?php esc_html_e( '魚介類（鱗のある魚）', 'halal-shop-pro' ); ?></li>
                        <li><?php esc_html_e( '野菜・果物・穀物', 'halal-shop-pro' ); ?></li>
                        <li><?php esc_html_e( 'アルコール不使用の飲料', 'halal-shop-pro' ); ?></li>
                    </ul>
                </div>
                <div style="padding:1.5rem;border:1px solid var(--color-danger);border-radius:12px">
                    <h3 style="color:var(--color-danger);margin-bottom:.75rem">❌ <?php esc_html_e( 'ハラーム（禁止）/ Haram (Prohibited)', 'halal-shop-pro' ); ?></h3>
                    <ul style="color:var(--color-text-light);line-height:2;padding-left:1.25rem">
                        <li><?php esc_html_e( '豚肉・豚由来成分', 'halal-shop-pro' ); ?></li>
                        <li><?php esc_html_e( 'アルコール・酒類', 'halal-shop-pro' ); ?></li>
                        <li><?php esc_html_e( 'ハラール処理されていない肉', 'halal-shop-pro' ); ?></li>
                        <li><?php esc_html_e( '血液・血液製品', 'halal-shop-pro' ); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Certification Bodies -->
    <section class="section section--gray">
        <div class="container">
            <h2 class="section-title"><?php esc_html_e( '認証機関 / Certification Bodies', 'halal-shop-pro' ); ?></h2>
            <p class="section-subtitle"><?php esc_html_e( 'We work with globally recognized Halal certification organizations', 'halal-shop-pro' ); ?></p>

            <div class="cert-cards">
                <?php
                $certs = [
                    [ 'name' => 'JHFA', 'full' => '日本ハラール認証機構', 'country' => '🇯🇵 Japan', 'desc' => '日本で最も権威ある食品ハラール認証機関。日本国内の食品・飲料・調味料の認証を担当。' ],
                    [ 'name' => 'MUI', 'full' => 'Majelis Ulama Indonesia', 'country' => '🇮🇩 Indonesia', 'desc' => 'インドネシアのイスラム学者評議会による認証。世界最大のムスリム人口を持つ国の権威ある認証機関。' ],
                    [ 'name' => 'JAKIM', 'full' => 'Jabatan Kemajuan Islam Malaysia', 'country' => '🇲🇾 Malaysia', 'desc' => 'マレーシア政府公認のハラール認証機関。国際的に高い信頼性を持つ。' ],
                ];
                foreach ( $certs as $cert ) : ?>
                <div class="cert-card">
                    <div class="cert-card__name"><?php echo esc_html( $cert['name'] ); ?></div>
                    <div style="font-size:.875rem;color:var(--color-text-light);margin-bottom:.5rem"><?php echo esc_html( $cert['full'] ); ?></div>
                    <div style="margin-bottom:1rem"><?php echo esc_html( $cert['country'] ); ?></div>
                    <div class="cert-card__desc"><?php echo esc_html( $cert['desc'] ); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Our Process -->
    <section class="section">
        <div class="container" style="max-width:800px">
            <h2 class="section-title"><?php esc_html_e( '品質保証プロセス / Quality Assurance Process', 'halal-shop-pro' ); ?></h2>
            <div style="position:relative">
                <?php
                $steps = [
                    [ '1', '仕入れ先審査', 'すべてのサプライヤーはハラール認証取得済みであることを確認します。', 'Supplier Verification — We verify all suppliers hold valid Halal certifications.' ],
                    [ '2', '成分確認', '原材料のひとつひとつを専門家がハラール適合性を審査します。', 'Ingredient Review — Every ingredient is reviewed by Halal experts for compliance.' ],
                    [ '3', '認証確認', '認証番号・有効期限を定期的にチェックし、最新の状態を維持します。', 'Certificate Monitoring — We regularly verify certificate numbers and expiry dates.' ],
                    [ '4', '商品掲載', '認証確認済みの商品のみ、詳細な認証情報と共に掲載します。', 'Product Listing — Only verified products are listed, with full certification details.' ],
                ];
                foreach ( $steps as $step ) : ?>
                <div style="display:flex;gap:1.5rem;margin-bottom:2rem;align-items:flex-start">
                    <div style="width:40px;height:40px;background:var(--color-primary);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0">
                        <?php echo esc_html( $step[0] ); ?>
                    </div>
                    <div>
                        <h3 style="margin-bottom:.5rem;color:var(--color-primary)"><?php echo esc_html( $step[1] ); ?></h3>
                        <p style="color:var(--color-text-light);margin:0"><?php echo esc_html( $step[2] ); ?><br><small><?php echo esc_html( $step[3] ); ?></small></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <section class="section section--gray">
        <div class="container" style="max-width:900px">
            <?php the_content(); ?>
        </div>
    </section>
    <?php endwhile; endif; ?>

</main>

<?php get_footer(); ?>
