<?php
/**
 * Fix WordPress siteurl/home to use the public Railway domain.
 * Run: https://halalshop.up.railway.app/wp-content/themes/halal-shop-pro/fix-url.php
 */
header('Content-Type: text/plain; charset=utf-8');
$roots = ['/var/www/html', dirname(__DIR__,3), dirname(__DIR__,4)];
$wp_root = null;
foreach ($roots as $r) { if (file_exists("$r/wp-load.php")) { $wp_root = $r; break; } }
if (!$wp_root) die("ERROR: WP not found\n");
define('WP_USE_THEMES', false);
require $wp_root . '/wp-load.php';

$correct_url = 'https://halalshop.up.railway.app';

$old_siteurl = get_option('siteurl');
$old_home    = get_option('home');
echo "Old siteurl: $old_siteurl\n";
echo "Old home:    $old_home\n";

update_option('siteurl', $correct_url);
update_option('home',    $correct_url);

echo "New siteurl: " . get_option('siteurl') . "\n";
echo "New home:    " . get_option('home') . "\n";

// Also fix any posts/metas with old internal URL
global $wpdb;
$old_base = rtrim($old_siteurl, '/');
if ($old_base !== $correct_url && strpos($old_base, 'railway.app') !== false) {
  $count = $wpdb->query($wpdb->prepare(
    "UPDATE {$wpdb->options} SET option_value = REPLACE(option_value, %s, %s) WHERE option_value LIKE %s",
    $old_base, $correct_url, '%' . $wpdb->esc_like($old_base) . '%'
  ));
  echo "Fixed $count option rows with old URL\n";
}

wp_cache_flush();
flush_rewrite_rules(true);
echo "✓ Done — CSS and assets should now load from $correct_url\n";
@unlink(__FILE__);
echo "✓ Self-deleted\n";
