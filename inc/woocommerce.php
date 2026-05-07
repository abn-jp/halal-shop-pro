<?php
/**
 * WooCommerce Customizations
 */

// Remove default WooCommerce styles (we use our own)
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

// Remove sidebar from shop
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

// Change number of products per row
add_filter( 'loop_shop_columns', function() { return 4; } );

// Products per page
add_filter( 'loop_shop_per_page', function() { return 12; } );

// Show sale badge with percentage
add_filter( 'woocommerce_sale_flash', function( $html, $post, $product ) {
    if ( $product->is_type( 'variable' ) ) return $html;
    $regular = (float) $product->get_regular_price();
    $sale    = (float) $product->get_sale_price();
    if ( $regular > 0 && $sale > 0 ) {
        $pct = round( ( ( $regular - $sale ) / $regular ) * 100 );
        return '<span class="badge badge-sale">-' . $pct . '%</span>';
    }
    return $html;
}, 10, 3 );

// Breadcrumb defaults
add_filter( 'woocommerce_breadcrumb_defaults', function( $defaults ) {
    $defaults['delimiter'] = ' <span class="breadcrumb-sep">›</span> ';
    $defaults['home']      = __( 'Home', 'halal-shop-pro' );
    return $defaults;
} );

// Checkout fields customization
add_filter( 'woocommerce_checkout_fields', function( $fields ) {
    // Add company name prominence
    $fields['billing']['billing_company']['priority'] = 25;

    // Remove unneeded fields for Japanese market
    // (keep all for international compatibility)

    return $fields;
} );

// Add Japanese consumption tax (10%) note
add_action( 'woocommerce_cart_totals_after_order_total', function() {
    echo '<tr class="tax-note"><td colspan="2"><small>' . esc_html__( '※ 消費税10%を含みます / Includes 10% Japanese Consumption Tax', 'halal-shop-pro' ) . '</small></td></tr>';
} );

// Custom shipping zones notice
function halal_shop_shipping_notice() {
    if ( ! is_cart() && ! is_checkout() ) return;
    echo '<div class="halal-shipping-notice">';
    echo '<p>🚚 ' . esc_html__( '全国配送対応（ヤマト運輸・佐川急便） | Nationwide delivery via Yamato & Sagawa', 'halal-shop-pro' ) . '</p>';
    echo '</div>';
}
add_action( 'woocommerce_before_cart', 'halal_shop_shipping_notice' );
add_action( 'woocommerce_before_checkout_form', 'halal_shop_shipping_notice' );

// Product category icons mapping
function halal_shop_category_icon( $cat_slug ) {
    $icons = [
        'wagyu'       => '🥩',
        'meat'        => '🥩',
        'poultry'     => '🍗',
        'seafood'     => '🐟',
        'seasonings'  => '🧂',
        'frozen'      => '❄️',
        'snacks'      => '🍘',
        'beverages'   => '🧃',
        'sweets'      => '🍬',
        'dairy'       => '🥛',
        'bread'       => '🍞',
        'instant'     => '🍜',
        'sauce'       => '🫙',
    ];
    foreach ( $icons as $key => $icon ) {
        if ( strpos( $cat_slug, $key ) !== false ) return $icon;
    }
    return '🛒';
}

// Register halal product categories
function halal_shop_register_categories() {
    $categories = [
        [ 'name' => '肉・肉加工品 (Meat)', 'slug' => 'meat-poultry', 'icon' => '🥩' ],
        [ 'name' => '調味料・ソース (Seasonings)', 'slug' => 'seasonings', 'icon' => '🧂' ],
        [ 'name' => '冷凍食品 (Frozen Foods)', 'slug' => 'frozen-foods', 'icon' => '❄️' ],
        [ 'name' => 'お菓子 (Snacks)', 'slug' => 'snacks', 'icon' => '🍘' ],
        [ 'name' => '飲料 (Beverages)', 'slug' => 'beverages', 'icon' => '🧃' ],
        [ 'name' => 'インスタント食品 (Instant Foods)', 'slug' => 'instant-foods', 'icon' => '🍜' ],
    ];
    foreach ( $categories as $cat ) {
        if ( ! term_exists( $cat['slug'], 'product_cat' ) ) {
            wp_insert_term( $cat['name'], 'product_cat', [ 'slug' => $cat['slug'] ] );
        }
    }
}
add_action( 'init', 'halal_shop_register_categories' );

// Mini cart fragment
add_filter( 'woocommerce_add_to_cart_fragments', function( $fragments ) {
    ob_start();
    ?>
    <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    <?php
    $fragments['.cart-count'] = ob_get_clean();
    return $fragments;
} );

// Custom "Added to cart" message
add_filter( 'wc_add_to_cart_message_html', function( $message, $products ) {
    return '<div class="halal-cart-notice">' . $message . '</div>';
}, 10, 2 );

// Japanese tax settings helper (run once via setup)
function halal_shop_configure_tax_settings() {
    // Only run if explicitly triggered
    update_option( 'woocommerce_calc_taxes', 'yes' );
    update_option( 'woocommerce_tax_display_shop', 'incl' );   // Show prices incl. tax in shop
    update_option( 'woocommerce_tax_display_cart', 'incl' );
    update_option( 'woocommerce_tax_total_display', 'single' );
}
// Uncomment to auto-configure on theme activation:
// add_action( 'after_switch_theme', 'halal_shop_configure_tax_settings' );

// Display related products limit
add_filter( 'woocommerce_output_related_products_args', function( $args ) {
    $args['posts_per_page'] = 4;
    $args['columns']        = 4;
    return $args;
} );
