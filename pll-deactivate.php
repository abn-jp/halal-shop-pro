<?php
/**
 * Polylang Deactivator — removes polylang from active_plugins so the
 * theme's own multilingual engine takes over (cookie-based fallback).
 * Run: https://halalshop.up.railway.app/wp-content/themes/halal-shop-pro/pll-deactivate.php
 */
header('Content-Type: text/plain; charset=utf-8');
$roots = ['/var/www/html', dirname(__DIR__,3), dirname(__DIR__,4)];
$wp_root = null;
foreach ($roots as $r) { if (file_exists("$r/wp-load.php")) { $wp_root = $r; break; } }
if (!$wp_root) die("ERROR: WP not found\n");
define('WP_USE_THEMES', false);
require $wp_root . '/wp-load.php';
echo "✓ WordPress loaded\n";

// Remove Polylang from active plugins
$active = (array) get_option('active_plugins', []);
echo "Active plugins before: " . count($active) . "\n";
$active = array_filter($active, function($p) { return strpos($p, 'polylang') === false; });
$active = array_values($active);
update_option('active_plugins', $active);
echo "Active plugins after: " . count($active) . "\n";

// Also disable the mu-plugin section 9 auto-reinstall by writing a flag
update_option('halal_polylang_skip', 1);
echo "✓ Polylang deactivated\n";

// Remove the broken language terms from DB (they cause the null-locale crash)
global $wpdb;
$lang_terms = $wpdb->get_col("SELECT term_id FROM {$wpdb->term_taxonomy} WHERE taxonomy IN ('language','term_language')");
foreach ($lang_terms as $tid) {
  $wpdb->delete($wpdb->termmeta, ['term_id' => $tid]);
  $wpdb->delete($wpdb->term_taxonomy, ['term_id' => $tid]);
  $wpdb->delete($wpdb->terms, ['term_id' => $tid]);
}
delete_option('pll_languages_list');
delete_option('polylang');
echo "✓ Polylang DB data cleared\n";

wp_cache_flush();
flush_rewrite_rules(true);
echo "✓ Cache flushed\n";
echo "\nSite will now use cookie-based language switching (5 languages: ja/en/id/ar/ms)\n";
echo "Test: https://halalshop.up.railway.app/\n";
echo "Test: https://halalshop.up.railway.app/?lang=en\n";
@unlink(__FILE__);
echo "✓ Self-deleted\n";
