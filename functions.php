<?php
/**
 * Halal Shop Pro - Main Functions File
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'HALAL_SHOP_VERSION', '1.0.0' );
define( 'HALAL_SHOP_DIR', get_template_directory() );
define( 'HALAL_SHOP_URI', get_template_directory_uri() );

require_once HALAL_SHOP_DIR . '/inc/theme-setup.php';
require_once HALAL_SHOP_DIR . '/inc/enqueue.php';
require_once HALAL_SHOP_DIR . '/inc/widgets.php';
require_once HALAL_SHOP_DIR . '/inc/customizer.php';
require_once HALAL_SHOP_DIR . '/inc/halal-meta.php';

if ( class_exists( 'WooCommerce' ) ) {
    require_once HALAL_SHOP_DIR . '/inc/woocommerce.php';
}

/**
 * Language switcher helper
 */
function halal_shop_language_switcher() {
    $languages = [
        'ja' => [ 'name' => '日本語', 'flag' => '🇯🇵', 'code' => 'ja' ],
        'en' => [ 'name' => 'English', 'flag' => '🇬🇧', 'code' => 'en' ],
        'id' => [ 'name' => 'Indonesia', 'flag' => '🇮🇩', 'code' => 'id' ],
        'ar' => [ 'name' => 'العربية', 'flag' => '🇸🇦', 'code' => 'ar', 'rtl' => true ],
        'ms' => [ 'name' => 'Melayu', 'flag' => '🇲🇾', 'code' => 'ms' ],
    ];

    // If WPML is active, use its language list
    if ( function_exists( 'icl_get_languages' ) ) {
        $wpml_languages = icl_get_languages( 'skip_missing=0' );
        if ( ! empty( $wpml_languages ) ) {
            echo '<div class="language-switcher">';
            echo '<button class="language-switcher__btn" aria-label="' . esc_attr__( 'Select Language', 'halal-shop-pro' ) . '">';
            echo '<span>' . esc_html( ICL_LANGUAGE_CODE ) . '</span>';
            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>';
            echo '</button>';
            echo '<div class="language-switcher__dropdown">';
            foreach ( $wpml_languages as $lang ) {
                $active = $lang['active'] ? ' active' : '';
                echo '<a href="' . esc_url( $lang['url'] ) . '" class="language-switcher__item' . $active . '">';
                echo '<span>' . esc_html( $languages[ $lang['language_code'] ]['flag'] ?? '🌐' ) . '</span>';
                echo '<span>' . esc_html( $lang['native_name'] ) . '</span>';
                echo '</a>';
            }
            echo '</div></div>';
            return;
        }
    }

    // Fallback static switcher
    $current_lang = get_locale();
    echo '<div class="language-switcher">';
    echo '<button class="language-switcher__btn">';
    echo '<span>🌐</span><span>' . esc_html( strtoupper( substr( $current_lang, 0, 2 ) ) ) . '</span>';
    echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>';
    echo '</button>';
    echo '<div class="language-switcher__dropdown">';
    foreach ( $languages as $code => $lang ) {
        echo '<a href="#" class="language-switcher__item" data-lang="' . esc_attr( $code ) . '">';
        echo '<span>' . esc_html( $lang['flag'] ) . '</span>';
        echo '<span>' . esc_html( $lang['name'] ) . '</span>';
        echo '</a>';
    }
    echo '</div></div>';
}

/**
 * Get halal certification label for a product
 */
function halal_shop_get_cert_label( $product_id ) {
    $cert = get_post_meta( $product_id, '_halal_certification_body', true );
    if ( ! $cert ) return false;
    return [
        'body'   => $cert,
        'number' => get_post_meta( $product_id, '_halal_cert_number', true ),
        'expiry' => get_post_meta( $product_id, '_halal_cert_expiry', true ),
    ];
}

/**
 * Check if current page is RTL
 */
function halal_shop_is_rtl() {
    if ( defined( 'ICL_LANGUAGE_CODE' ) && ICL_LANGUAGE_CODE === 'ar' ) return true;
    if ( function_exists( 'pll_current_language' ) && pll_current_language() === 'ar' ) return true;
    return is_rtl();
}
