<?php
/**
 * URL Diagnostic — shows where the bad URL is coming from
 * Access: https://halalshop.up.railway.app/wp-content/themes/halal-shop-pro/url-diag.php
 * DELETE AFTER USE.
 */
header('Content-Type: text/plain; charset=utf-8');

echo "=== ENV VARS ===\n";
foreach (['RAILWAY_PUBLIC_DOMAIN','RAILWAY_STATIC_URL','RAILWAY_SERVICE_NAME','WP_HOME','WP_SITEURL'] as $k) {
    echo "$k = " . (getenv($k) ?: '(not set)') . "\n";
}
echo "HTTP_HOST = " . ($_SERVER['HTTP_HOST'] ?? 'n/a') . "\n";

define('WP_USE_THEMES', false);
require '/var/www/html/wp-load.php';

echo "\n=== AFTER WP LOAD ===\n";
echo "get_option(siteurl)          = " . get_option('siteurl') . "\n";
echo "get_option(home)             = " . get_option('home') . "\n";
echo "site_url()                   = " . site_url() . "\n";
echo "home_url()                   = " . home_url() . "\n";
echo "content_url()                = " . content_url() . "\n";
echo "get_template_directory_uri() = " . get_template_directory_uri() . "\n";

echo "\n=== CONSTANTS ===\n";
echo "WP_SITEURL: " . (defined('WP_SITEURL') ? WP_SITEURL : 'NOT DEFINED') . "\n";
echo "WP_HOME:    " . (defined('WP_HOME') ? WP_HOME : 'NOT DEFINED') . "\n";
echo "WP_CONTENT_URL: " . (defined('WP_CONTENT_URL') ? WP_CONTENT_URL : 'NOT DEFINED') . "\n";

echo "\n=== wp-config.php URL lines ===\n";
$cfg = @file_get_contents('/var/www/html/wp-config.php');
if ($cfg) {
    foreach (explode("\n", $cfg) as $line) {
        if (preg_match('/siteurl|WP_HOME|WP_CONTENT|railway|https?:\/\//i', $line)) {
            echo trim($line) . "\n";
        }
    }
}

@unlink(__FILE__);
echo "\n=== DONE ===\n";
