<?php
/**
 * Polylang Deep Fix — sets term_meta locale/flag + rebuilds pll_languages_list
 * Run: https://halalshop.up.railway.app/wp-content/themes/halal-shop-pro/pll-fix2.php
 */
header('Content-Type: text/plain; charset=utf-8');
$roots = ['/var/www/html', dirname(__DIR__,3), dirname(__DIR__,4)];
$wp_root = null;
foreach ($roots as $r) { if (file_exists("$r/wp-load.php")) { $wp_root = $r; break; } }
if (!$wp_root) die("ERROR: WP not found\n");
define('WP_USE_THEMES', false);
require $wp_root . '/wp-load.php';
echo "✓ WordPress loaded\n";

global $wpdb;

// Language definitions
$defs = [
  'ja' => ['name'=>'Japanese',   'locale'=>'ja',    'flag_code'=>'ja', 'rtl'=>0, 'order'=>0],
  'en' => ['name'=>'English',    'locale'=>'en_US', 'flag_code'=>'us', 'rtl'=>0, 'order'=>1],
  'id' => ['name'=>'Indonesian', 'locale'=>'id_ID', 'flag_code'=>'id', 'rtl'=>0, 'order'=>2],
  'ar' => ['name'=>'Arabic',     'locale'=>'ar',    'flag_code'=>'sa', 'rtl'=>1, 'order'=>3],
  'ms' => ['name'=>'Malay',      'locale'=>'ms_MY', 'flag_code'=>'my', 'rtl'=>0, 'order'=>4],
];

// Step 1: Delete ALL existing language terms and start fresh
$old_terms = $wpdb->get_col("SELECT term_id FROM {$wpdb->term_taxonomy} WHERE taxonomy IN ('language','term_language')");
foreach ($old_terms as $tid) {
  $wpdb->delete($wpdb->termmeta, ['term_id' => $tid]);
  $wpdb->delete($wpdb->term_taxonomy, ['term_id' => $tid]);
  $wpdb->delete($wpdb->terms, ['term_id' => $tid]);
  echo "  Deleted term: $tid\n";
}
echo "✓ Old language terms cleared\n";

// Step 2: Create fresh language terms with term_meta
$pll_list = [];
foreach ($defs as $slug => $def) {
  // Insert term
  $wpdb->insert($wpdb->terms, ['name' => $def['name'], 'slug' => $slug, 'term_group' => $def['order'] * 10]);
  $term_id = $wpdb->insert_id;
  
  // Insert term_taxonomy for 'language'
  $wpdb->insert($wpdb->term_taxonomy, [
    'term_id'  => $term_id, 'taxonomy' => 'language',
    'description' => $def['locale'], 'parent' => 0, 'count' => 0
  ]);
  $tt_id = $wpdb->insert_id;
  
  // Insert term_taxonomy for 'term_language' (Polylang's secondary taxonomy)
  $wpdb->insert($wpdb->term_taxonomy, [
    'term_id'  => $term_id, 'taxonomy' => 'term_language',
    'description' => $def['locale'], 'parent' => 0, 'count' => 0
  ]);
  
  // Set all required term_meta
  $metas = [
    'locale'     => $def['locale'],
    'flag_code'  => $def['flag_code'],
    'is_rtl'     => $def['rtl'],
    'active'     => '1',
    'term_group' => (string)($def['order'] * 10),
    'mo_id'      => '0',
  ];
  foreach ($metas as $key => $val) {
    $wpdb->insert($wpdb->termmeta, ['term_id' => $term_id, 'meta_key' => $key, 'meta_value' => $val]);
  }
  
  // Build list entry
  $pll_list[] = [
    'term_id'    => (int)$term_id,
    'term_tt_id' => (int)$tt_id,
    'name'       => $def['name'],
    'slug'       => $slug,
    'locale'     => $def['locale'],
    'flag_code'  => $def['flag_code'],
    'is_rtl'     => (int)$def['rtl'],
    'term_group' => $def['order'] * 10,
    'count'      => 0,
    'no_default_cat' => 0,
    'mo_id'      => 0,
    'active'     => true,
  ];
  echo "  ✓ Created: $slug (term_id=$term_id, locale={$def['locale']})\n";
}

// Step 3: Save pll_languages_list
update_option('pll_languages_list', $pll_list);
echo "✓ pll_languages_list saved\n";

// Step 4: Polylang options
$opts = get_option('polylang', []);
$opts['default_lang'] = 'ja';
$opts['rewrite']      = 1;
$opts['hide_default'] = 0;
$opts['force_lang']   = 1;
update_option('polylang', $opts);
echo "✓ Polylang options saved (default=ja)\n";

// Step 5: Flush
delete_option('rewrite_rules');
flush_rewrite_rules(true);
wp_cache_flush();
echo "✓ Rewrite flushed\n";

echo "\n=== COMPLETE ===\n";
echo "Test: https://halalshop.up.railway.app/\n";
echo "Test: https://halalshop.up.railway.app/en/\n";

@unlink(__FILE__);
echo "✓ Self-deleted\n";
