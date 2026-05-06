<?php
/**
 * Template Name: About Us
 */
get_header();
?>

<div class="page-hero">
    <div class="container">
        <h1><?php esc_html_e( '会社概要 / About Us', 'halal-shop-pro' ); ?></h1>
        <p><?php esc_html_e( 'Dedicated to bringing Halal food and trust to every Muslim in Japan and beyond', 'halal-shop-pro' ); ?></p>
    </div>
</div>

<main class="section">
    <div class="container">

        <!-- Mission -->
        <div style="max-width:800px;margin:0 auto 4rem;text-align:center">
            <h2 style="color:var(--color-primary);margin-bottom:1rem"><?php esc_html_e( '私たちのミッション', 'halal-shop-pro' ); ?></h2>
            <p style="font-size:1.125rem;line-height:1.8;color:var(--color-text-light)">
                <?php esc_html_e( 'ハラールショップは、日本在住のムスリム・訪日ムスリム旅行者・海外在住ムスリムの皆様が、安心して食品を購入できる場を提供することを使命としています。すべての商品は権威ある認証機関によってハラール認証を受けており、品質と信頼を最優先にしています。', 'halal-shop-pro' ); ?>
            </p>
        </div>

        <!-- Values Grid -->
        <div class="grid grid-3" style="margin-bottom:4rem">
            <?php
            $values = [
                [ 'icon' => '🕌', 'title' => '信頼 / Trust', 'desc' => '完全なハラール認証と透明性のある原材料情報を提供します。' ],
                [ 'icon' => '🌍', 'title' => '多様性 / Diversity', 'desc' => '日本語・英語・インドネシア語・アラビア語・マレー語に対応。' ],
                [ 'icon' => '❤️', 'title' => 'コミュニティ / Community', 'desc' => '日本のムスリムコミュニティを支援し、共に成長します。' ],
            ];
            foreach ( $values as $v ) : ?>
            <div style="text-align:center;padding:2rem;background:var(--color-primary-pale);border-radius:16px">
                <div style="font-size:3rem;margin-bottom:1rem"><?php echo esc_html( $v['icon'] ); ?></div>
                <h3 style="color:var(--color-primary);margin-bottom:.5rem"><?php echo esc_html( $v['title'] ); ?></h3>
                <p style="color:var(--color-text-light);font-size:.9rem"><?php echo esc_html( $v['desc'] ); ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Company Info Table -->
        <div style="max-width:700px;margin:0 auto">
            <h2 style="color:var(--color-primary);margin-bottom:1.5rem"><?php esc_html_e( '会社情報', 'halal-shop-pro' ); ?></h2>
            <table style="width:100%;border-collapse:collapse">
                <?php
                $info = [
                    [ '会社名 / Company', 'ハラールショップ株式会社 / Halal Shop Co., Ltd.' ],
                    [ '設立 / Founded', '2020年' ],
                    [ '所在地 / Address', get_theme_mod( 'company_address', '東京都〇〇区〇〇 X-X-X' ) ],
                    [ '電話 / Phone', get_theme_mod( 'company_phone', '03-XXXX-XXXX' ) ],
                    [ 'メール / Email', get_theme_mod( 'company_email', 'info@halalshop.example.com' ) ],
                    [ '営業時間 / Hours', get_theme_mod( 'company_hours', '月–金 9:00–18:00 (JST)' ) ],
                    [ 'ハラール認証 / Cert', 'JHFA, MUI (Indonesia), JAKIM (Malaysia)' ],
                ];
                foreach ( $info as $row ) : ?>
                <tr style="border-bottom:1px solid var(--color-border)">
                    <th style="text-align:left;padding:1rem;background:var(--color-gray-100);width:35%;font-weight:600;color:var(--color-primary)"><?php echo esc_html( $row[0] ); ?></th>
                    <td style="padding:1rem;color:var(--color-text)"><?php echo esc_html( $row[1] ); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <?php the_content(); ?>

    </div>
</main>

<?php get_footer(); ?>
