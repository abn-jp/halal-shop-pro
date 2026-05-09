<?php
/**
 * WooCommerce Activation & Category Setup
 * Run ONCE after Dockerfile deploys WooCommerce into the image.
 * Access: https://halalshop.up.railway.app/wp-content/themes/halal-shop-pro/woo-setup.php
 * Self-deletes after running.
 */
header('Content-Type: text/plain; charset=utf-8');
set_time_limit(120);
define('WP_USE_THEMES', false);
require '/var/www/html/wp-load.php';
echo "OK WordPress loaded\n";

require_once ABSPATH . 'wp-admin/includes/plugin.php';
$plug_rel = 'woocommerce/woocommerce.php';
$plug_abs = WP_PLUGIN_DIR . '/woocommerce/woocommerce.php';
echo "WC file: " . (file_exists($plug_abs) ? 'YES' : 'NO') . "\n";
echo "WC active: " . (is_plugin_active($plug_rel) ? 'YES' : 'NO') . "\n";

if (!file_exists($plug_abs)) { die("FAIL: WooCommerce missing from Docker image\n"); }

if (!is_plugin_active($plug_rel)) {
    $r = activate_plugin($plug_rel, '', false, true);
    echo is_wp_error($r) ? "FAIL: ".$r->get_error_message()."\n" : "OK activated\n";
} else { echo "OK already active\n"; }

// Force taxonomy registration
do_action('init');
if (!taxonomy_exists('product_cat') && class_exists('WC_Post_Types')) {
    WC_Post_Types::register_taxonomies();
}
echo "product_cat: " . (taxonomy_exists('product_cat') ? 'registered' : 'MISSING') . "\n";

// WooCommerce pages
$pages = [
    'shop'       => ['Shop',       'woocommerce_shop_page_id',      ''],
    'cart'       => ['Cart',       'woocommerce_cart_page_id',      '[woocommerce_cart]'],
    'checkout'   => ['Checkout',   'woocommerce_checkout_page_id',  '[woocommerce_checkout]'],
    'my-account' => ['My Account', 'woocommerce_myaccount_page_id', '[woocommerce_my_account]'],
];
echo "\n--- Pages ---\n";
foreach ($pages as $slug => [$title, $opt, $content]) {
    $p = get_page_by_path($slug);
    if (!$p) {
        $id = wp_insert_post(['post_title'=>$title,'post_name'=>$slug,'post_status'=>'publish','post_type'=>'page','post_content'=>$content]);
        update_option($opt, $id);
        echo "  created: $slug ID=$id\n";
    } else { update_option($opt, $p->ID); echo "  exists: $slug ID={$p->ID}\n"; }
}

// Categories
echo "\n--- Categories ---\n";
if (taxonomy_exists('product_cat')) {
    $cats = [
        'meat-poultry'  => '\u8089\u30fb\u8089\u52a0\u5de5\u54c1 Meat & Poultry',
        'seasonings'    => '\u8abf\u5473\u6599\u30fb\u30bd\u30fc\u30b9 Seasonings',
        'frozen-foods'  => '\u51b7\u51cd\u98df\u54c1 Frozen Foods',
        'snacks'        => '\u304a\u83d3\u5b50 Snacks',
        'beverages'     => '\u98f2\u6599 Beverages',
        'instant-foods' => '\u30a4\u30f3\u30b9\u30bf\u30f3\u30c8 Instant Foods',
        'halal-wagyu'   => 'Halal Wagyu',
    ];
    foreach ($cats as $slug => $name) {
        $ex = get_term_by('slug', $slug, 'product_cat');
        if ($ex) { echo "  exists: $slug\n"; }
        else {
            $r = wp_insert_term($name, 'product_cat', ['slug'=>$slug]);
            echo is_wp_error($r) ? "  FAIL $slug: ".$r->get_error_message()."\n" : "  created: $slug ID={$r['term_id']}\n";
        }
    }
} else { echo "  SKIP — product_cat not registered\n"; }

update_option('woocommerce_currency','JPY');
update_option('woocommerce_price_num_decimals',0);
update_option('woocommerce_default_country','JP');
echo "\nOK settings\n";

flush_rewrite_rules(true);
wp_cache_flush();
echo "OK permalinks flushed\n";
@unlink(__FILE__);
echo "DONE\nTest: https://halalshop.up.railway.app/product-category/meat-poultry\n";
