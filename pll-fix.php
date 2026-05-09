<?php
/**
 * Polylang Language Fix — patches missing locale/flag_code in pll_languages_list
 * Access: https://halalshop.up.railway.app/wp-content/themes/halal-shop-pro/pll-fix.php
 * DELETE THIS FILE after running.
 */
if ( php_sapi_name() === 'cli' ) { chdir( '/var/www/html' ); }
header( 'Content-Type: text/plain; charset=utf-8' );

// Bootstrap WordPress
$roots = [ '/var/www/html', dirname(__DIR__,3), dirname(__DIR__,4) ];
$wp_root = null;
foreach ( $roots as $r ) {
    if ( file_exists( "$r/wp-load.php" ) ) { $wp_root = $r; break; }
}
if ( ! $wp_root ) { die( "ERROR: WordPress root not found\n" ); }
define( 'WP_USE_THEMES', false );
require $wp_root . '/wp-load.php';
echo "✓ WordPress loaded\n";

// Correct Polylang language definitions
$lang_defs = [
    'ja' => [ 'locale' => 'ja',    'flag_code' => 'ja', 'name' => 'Japanese',   'mo_id' => 0 ],
    'en' => [ 'locale' => 'en_US', 'flag_code' => 'us', 'name' => 'English',    'mo_id' => 0 ],
    'id' => [ 'locale' => 'id_ID', 'flag_code' => 'id', 'name' => 'Indonesian', 'mo_id' => 0 ],
    'ar' => [ 'locale' => 'ar',    'flag_code' => 'sa', 'name' => 'Arabic',     'mo_id' => 0, 'is_rtl' => 1 ],
    'ms' => [ 'locale' => 'ms_MY', 'flag_code' => 'my', 'name' => 'Malay',      'mo_id' => 0 ],
];

global $wpdb;
$raw = get_option( 'pll_languages_list', [] );
echo "Current pll_languages_list count: " . count($raw) . "\n";

$fixed = [];
foreach ( $raw as $lang_data ) {
    $slug = $lang_data['slug'] ?? null;
    if ( ! $slug && isset($lang_data['term_id']) ) {
        $term = get_term( $lang_data['term_id'], 'language' );
        $slug = $term ? $term->slug : null;
    }
    if ( $slug && isset( $lang_defs[ $slug ] ) ) {
        $def = $lang_defs[ $slug ];
        if ( empty( $lang_data['locale'] ) )    $lang_data['locale']    = $def['locale'];
        if ( empty( $lang_data['flag_code'] ) ) $lang_data['flag_code'] = $def['flag_code'];
        echo "  Patched: $slug → locale={$lang_data['locale']}, flag={$lang_data['flag_code']}\n";
    }
    $fixed[] = $lang_data;
}

if ( ! empty($fixed) ) {
    update_option( 'pll_languages_list', $fixed );
    echo "✓ pll_languages_list updated\n";
} else {
    echo "⚠ Empty list — rebuilding from scratch\n";
    $pll_list = [];
    $order = 0;
    foreach ( $lang_defs as $slug => $def ) {
        $term = get_term_by( 'slug', $slug, 'language' );
        if ( ! $term ) {
            $ins = wp_insert_term( $def['name'], 'language', [ 'slug' => $slug ] );
            $term_id = is_wp_error($ins) ? 0 : $ins['term_id'];
        } else { $term_id = $term->term_id; }
        $pll_list[] = [
            'term_id' => (int) $term_id, 'name' => $def['name'], 'slug' => $slug,
            'locale' => $def['locale'], 'flag_code' => $def['flag_code'],
            'is_rtl' => (int) ($def['is_rtl'] ?? 0), 'term_group' => $order * 10,
            'count' => 0, 'no_default_cat' => 0, 'mo_id' => 0, 'active' => true,
        ];
        echo "  ✓ Built: $slug (locale={$def['locale']})\n";
        $order++;
    }
    update_option( 'pll_languages_list', $pll_list );
    echo "✓ pll_languages_list written\n";
}

$pll_options = get_option( 'polylang', [] );
$pll_options['default_lang'] = 'ja';
$pll_options['rewrite'] = 1;
$pll_options['hide_default'] = 0;
update_option( 'polylang', $pll_options );
flush_rewrite_rules( true );
wp_cache_flush();
echo "✓ Done — delete this file, then test https://halalshop.up.railway.app/\n";
@unlink(__FILE__);
echo "✓ Self-deleted\n";
