<?php
/**
 * Halal Shop Pro - Main Functions File
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'HALAL_SHOP_VERSION', '1.1.1' );
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
    if ( file_exists( HALAL_SHOP_DIR . '/inc/halal-wagyu.php' ) ) {
        require_once HALAL_SHOP_DIR . '/inc/halal-wagyu.php';
    }
    if ( file_exists( HALAL_SHOP_DIR . '/inc/wagyu-sample-data.php' ) ) {
        require_once HALAL_SHOP_DIR . '/inc/wagyu-sample-data.php';
    }
}

/**
 * Language switcher helper
 *
 * Priority order:
 *  1. WPML  (icl_get_languages)
 *  2. Polyland  (pll_the_languages / pll_current_language)
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
            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>';
            echo '</button>';
            echo '<div class="language-switcher__dropdown">';
            foreach ( $pll_langs as $lang ) {
                $active = $lang['current_lang'] ? ' active' : '';
                // no_translation=1 means this page has no translation; link to home instead
                $url = ( isset( $lang['no_translation'] ) && $lang['no_translation'] )
                    ? pll_home_url( $lang['slug'] )
                    : $lang['url'];
                echo '<a href="' . esc_url( $url ) . '" class="language-switcher__item' . $active . '">';
                echo '<span>' . esc_html( $languages[ $lang['slug'] ]['flag'] ?? '🌐' ) . '</span>';
                echo '<span>' . esc_html( $lang['name'] ) . '</span>';
                echo '</a>';
            }
            echo '</div></div>';
            return;
        }
    }

    // ── 3. Cookie-based fallback ──────────────────────────────────────────────
    // Switches WP locale / UI strings via a cookie.
    // NOTE: This does NOT translate post content — install Polylang or WPML for that.
    $current_code = halal_shop_get_fallback_lang();
    $current_lang = $languages[ $current_code ] ?? $languages['en'];
    $switch_base  = add_query_arg( [] ); // current URL, clean

    echo '<div class="language-switcher">';
    echo '<button class="language-switcher__btn" aria-label="' . esc_attr__( 'Select Language', 'halal-shop-pro' ) . '">';
    echo '<span>' . esc_html( $current_lang['flag'] ) . '</span>';
    echo '<span>' . esc_html( strtoupper( $current_code ) ) . '</span>';
    echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>';
    echo '</button>';
    echo '<div class="language-switcher__dropdown">';
    foreach ( $languages as $code => $lang ) {
        $active = ( $code === $current_code ) ? ' active' : '';
        $url    = add_query_arg( 'lang', $code, $switch_base );
        echo '<a href="' . esc_url( $url ) . '" class="language-switcher__item' . $active . '">';
        echo '<span>' . esc_html( $lang['flag'] ) . '</span>';
        echo '<span>' . esc_html( $lang['name'] ) . '</span>';
        echo '</a>';
    }
    echo '</div></div>';
}

if ( ! function_exists( 'halal_shop_get_fallback_lang' ) ) {
/**
 * Get the active language code for the cookie-based fallback.
 * Reads ?lang=XX from the URL, persists it in a cookie, and falls back
 * to the browser's Accept-Language header, then the site default.
 */
function halal_shop_get_fallback_lang() {
    $allowed = [ 'ja', 'en', 'id', 'ar', 'ms' ];

    // 1. Query-string takes highest priority (link click)
    if ( isset( $_GET['lang'] ) && in_array( $_GET['lang'], $allowed, true ) ) {
        $lang = sanitize_key( $_GET['lang'] );
        if ( ! headers_sent() ) {
            setcookie( 'halal_lang', $lang, time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
        }
        return $lang;
    }

    // 2. Cookie (set on a previous visit)
    if ( isset( $_COOKIE['halal_lang'] ) && in_array( $_COOKIE['halal_lang'], $allowed, true ) ) {
        return sanitize_key( $_COOKIE['halal_lang'] );
    }

    // 3. Browser Accept-Language header (first two chars)
    if ( ! empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
        $browser_lang = strtolower( substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 ) );
        if ( in_array( $browser_lang, $allowed, true ) ) {
            return $browser_lang;
        }
    }

    // 4. Site default locale
    $locale = get_option( 'WPLANG', 'ja' );
    $code   = strtolower( substr( $locale, 0, 2 ) );
    return in_array( $code, $allowed, true ) ? $code : 'ja';
}
}

/**
 * Apply the cookie-based fallback locale to WordPress.
 * Runs early so load_textdomain picks up the right language files.
 * Has no effect when WPML or Polylang is active (they manage locale themselves).
 */
function halal_shop_apply_fallback_locale( $locale ) {
    // Don't interfere with proper multilingual plugins
    if ( function_exists( 'icl_get_languages' ) || function_exists( 'pll_current_language' ) ) {
        return $locale;
    }

    $locale_map = [
        'ja' => 'ja',
        'en' => 'en_US',
        'id' => 'id_ID',
        'ar' => 'ar',
        'ms' => 'ms_MY',
    ];

    $lang = halal_shop_get_fallback_lang();
    return $locale_map[ $lang ] ?? $locale;
}
add_filter( 'locale', 'halal_shop_apply_fallback_locale' );

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
