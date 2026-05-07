<?php
/**
 * Hero Banner — language-aware
 *
 * Uses halal_mod() (from inc/multilingual.php) so Polylang String Translations
 * can override every piece of text, and the inline gettext filter handles it
 * automatically when no .mo file is present.
 */

// Resolve shop URL in the active language (Polylang translates WC page IDs)
$shop_url = class_exists( 'WooCommerce' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/' );

// Resolve halal-cert page URL for the active language
$cert_slug = [
    'ja' => 'halal-certification',
    'en' => 'halal-certification-en',
    'id' => 'sertifikasi-halal',
    'ar' => 'شهادة-حلال',
    'ms' => 'pensijilan-halal',
];
$lang     = function_exists( 'halal_lang' ) ? halal_lang() : 'ja';
$cert_url = home_url( '/' . ( $cert_slug[ $lang ] ?? 'halal-certification' ) . '/' );
if ( function_exists( 'pll_get_post' ) ) {
    // Polylang: get the real translated page URL
    $cert_page = get_page_by_path( 'halal-certification' );
    if ( $cert_page ) {
        $cert_translated = pll_get_post( $cert_page->ID );
        if ( $cert_translated ) {
            $cert_url = get_permalink( $cert_translated );
        }
    }
}

// "Next day" label differs per language
$next_day_labels = [
    'ja' => '翌日',
    'en' => 'Next Day',
    'id' => 'Besok',
    'ar' => 'اليوم التالي',
    'ms' => 'Hari Berikut',
];
$next_day = $next_day_labels[ $lang ] ?? '翌日';
?>
<section class="hero-section" aria-label="<?php esc_attr_e( 'Hero Banner', 'halal-shop-pro' ); ?>">
    <div class="hero-inner">
        <div class="hero-content">

            <div class="hero-badge">
                🕌 <?php esc_html_e( 'ハラール認証取得 | Halal Certified', 'halal-shop-pro' ); ?>
            </div>

            <h1 class="hero-title">
                <?php
                // halal_mod() reads from Polylang String Translations first,
                // then falls back to the Customizer value, then the default.
                echo nl2br( esc_html( halal_mod(
                    'hero_title',
                    "ハラールフードの\n安心・安全な\nオンラインショップ"
                ) ) );
                ?>
            </h1>

            <p class="hero-subtitle">
                <?php
                echo esc_html( halal_mod(
                    'hero_subtitle',
                    'ムスリムフレンドリーな食品を全国にお届け。厳選されたハラール認証食品を取り揃えています。'
                ) );
                ?>
            </p>

            <div class="hero-actions">
                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                <a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-secondary btn-lg">
                    🛒 <?php esc_html_e( '商品を見る / Shop Now', 'halal-shop-pro' ); ?>
                </a>
                <?php endif; ?>
                <a href="<?php echo esc_url( $cert_url ); ?>" class="btn btn-outline-white btn-lg">
                    <?php esc_html_e( 'Halal認証とは？', 'halal-shop-pro' ); ?>
                </a>
            </div>

            <div class="hero-s