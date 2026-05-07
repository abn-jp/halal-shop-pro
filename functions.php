<?php
/**
 * Halal Shop Pro - Main Functions File
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'HALAL_SHOP_VERSION', '1.0.0' );
define( 'HALAL_SHOP_DIR', get_template_directory() );
define( 'HALAL_SHOP_URI', get_template_directory_uri() );

require_once HALAL_SHOP_DIR . '/inc/multilingual.php'; // Must load first — URL/locale filters
require_once HALAL_SHOP_DIR . '/inc/theme-setup.php';
require_once HALAL_SHOP_DIR . '/inc/enqueue.php';
require_once HALAL_SHOP_DIR . '/inc/widgets.php';
require_once HALAL_SHOP_DIR . '/inc/customizer.php';
require_once HALAL_SHOP_DIR . '/inc/halal-meta.php';

if ( class_exists( 'WooCommerce' ) ) {
    require_once HALAL_SHOP_DIR . '/inc/woocommerce.php';
    require_once HALAL_SHOP_DIR . '/inc/halal-wagyu.php';
    require_once HALAL_SHOP_DIR . '/inc/wagyu-sample-data.php';
}

/**
 * Language switcher helper
 *
 * Priority order:
 *  1. WPML  (icl_get_languages)
 *  2. Polylang  (pll_the_languages / pll_current_language)
 *  3. Cookie-based fallback (switches WP locale & UI strings; requires .mo files)
 */
function halal_shop_language_switcher() {
    $languages = [
        'ja' => [ 'name' => '日本語',   'flag' => '🇯🇵', 'code' => 'ja' ],
        'en' => [ 'name' => 'English',  'flag' => '🇬🇧', 'code' => 'en' ],
        'id' => [ 'name' => 'Indonesia','flag' => '🇮🇩', 'code' => 'id' ],
        'ar' => [ 'name' => 'العربية',  'flag' => '🇸🇦', 'code' => 'ar', 'rtl' => true ],
        'ms' => [ 'name' => 'Melayu',   'flag' => '🇲🇾', 'code' => 'ms' ],
    ];

    // ── 1. WPML ──────────────────────────────────────────────────────────────
    if ( function_exists( 'icl_get_languages' ) ) {
        $wpml_languages = icl_get_languages( 'skip_missing=0' );
        if ( ! empty( $wpml_languages ) ) {
            $current_flag = $languages[ ICL_LANGUAGE_CODE ]['flag'] ?? '🌐';
            echo '<div class="language-switcher">';
            echo '<button class="language-switcher__btn" aria-label="' . esc_attr__( 'Select Language', 'halal-shop-pro' ) . '">';
            echo '<span>' . esc_html( $current_flag ) . '</span>';
            echo '<span>' . esc_html( strtoupper( ICL_LANGUAGE_CODE ) ) . '</span>';
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

    // ── 2. Polylang ──────────────────────────────────────────────────────────
    if ( function_exists( 'pll_current_language' ) ) {
        $pll_langs    = pll_the_languages( [ 'raw' => 1 ] );
        $current_code = pll_current_language( 'slug' );
        if ( ! empty( $pll_langs ) ) {
            $current_flag = $languages[ $current_code ]['flag'] ?? '🌐';
            echo '<div class="language-switcher">';
            echo '<button class="language-switcher__btn" aria-label="' . esc_attr__( 'Select Language', 'halal-shop-pro' ) . '">';
            echo '<span>' . esc_html( $current_flag ) . '</span>';
            echo '<span>' . esc_html( strtoupper( $current_code ) ) . '</span>';
            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"