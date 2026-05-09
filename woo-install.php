<?php
/**
 * WooCommerce Installer & Category Setup
 * Access: https://halalshop.up.railway.app/wp-content/themes/halal-shop-pro/woo-install.php
 * Self-deletes after running. Run ONCE.
 */
header('Content-Type: text/plain; charset=utf-8');
set_time_limit(300);

define('WP_USE_THEMES', false);
require '/var/www/html/wp-load.php';
echo "OK WordPress loaded\n";

$slug     = 'woocommerce';
$plug_rel = 'woocommerce/woocommerce.php';
$plug_abs = WP_PLUGIN_DIR . '/woocommerce/woocommerce.php';

if ( file_exists( $plug_abs ) ) {
    echo "OK WooCommerce files present\n";
} else {
    echo "Downloading WooCommerce...\n";
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    WP_Filesystem();
    $tmp = download_url( 'https://downloads.wordpress.org/plugin/woocommerce.latest-stable.zip' );
    if ( is_wp_error($tmp) ) die( "FAIL download: " . $tmp->get_error_message() . "\n" );
    echo "OK downloaded\n";
    $r = unzip_file( $tmp, WP_PLUGIN_DIR );
    @unlink($tmp);
    if ( is_wp_error($r) ) die( "FAIL unzip: " . $r->get_error_message() . "\n" );
    echo "OK unzipped\n";
}

require_once ABSPATH . 'wp-admin/includes/plugin.php';
$active = (array) get_option('active_plugins', []);
if ( in_array($plug_rel, $active, true) ) {
    echo "OK WooCommerce already active\n";
} else {
    $r = activate_plugin($plug_rel, '', false, true);
    if ( is_wp_error($r) ) die("FAIL activate: " . $r->get_error_message() . "\n");
    echo "OK WooCommerce activated\n";
}

$shop = get_page_by_path('shop');
if ( !$shop ) {
    $id = wp_insert_post(['post_title'=>'Shop','post_name'=>'shop','post_status'=>'publish','post_type'=>'page']);
    update_option('woocommerce_shop_page_id', $id);
    echo "OK shop page created ID=$id\n";
} else {
    update_option('woocommerce_shop_page_id', $shop->ID);
    echo "OK shop page exists ID={$shop->ID}\n";
}

$cats = [
    'meat-poultry'  => '\u8089\u30fb\u8089\u52a0\u5de5\u54c1 Meat & Poultry',
    'seasonings'    => '\u8abf\u5473\u6599\u30fb\u30bd\u30fc\u30b9 Seasonings',
    'frozen-foods'  => '\u51b7\u51cd\u98df\u54c1 Frozen Foods',
    'snacks'        => '\u304a\u83d3\u5b50 Snacks',
    'beverages'     => '\u98f2\u6599 Beverages',
    'instant-foods' => '\u30a4\u30f3\u30b9\u30bf\u30f3\u30c8 Instant Foods',
    'halal-wagyu'   => '\u30cf\u30e9\u30fcWAGYU Halal Wagyu',
];
foreach ($cats as $sl => $nm) {
    $ex = get_term_by('slug', $sl, 'product_cat');
    if ($ex) { echo "OK cat exists: $sl\n"; }
    else {
        $r = wp_insert_term($nm, 'product_cat', ['slug'=>$sl]);
        echo (is_wp_error($r) ? "FAIL cat $sl: ".$r->get_error_message() : "OK cat created: $sl (ID={$r['term_id']})") . "\n";
    }
}

update_option('woocommerce_currency', 'JPY');
update_option('woocommerce_price_num_decimals', 0);
update_option('woocommerce_default_country', 'JP');
echo "OK currency JPY\n";

flush_rewrite_rules(true);
wp_cache_flush();
echo "OK permalinks flushed\n";

@unlink(__FILE__);
echo "DONE\n";
