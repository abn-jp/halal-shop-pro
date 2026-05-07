<?php
/**
 * Enqueue Scripts & Styles
 */

function halal_shop_enqueue_scripts() {
    // Google Fonts (Noto Sans JP + Arabic + Inter)
    wp_enqueue_style(
        'halal-shop-fonts',
        'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Noto+Sans+Arabic:wght@300;400;500;700&family=Inter:wght@300;400;500;600;700&display=swap',
        [],
        null
    );

    // Main stylesheet
    wp_enqueue_style(
        'halal-shop-main',
        HALAL_SHOP_URI . '/assets/css/main.css',
        [ 'halal-shop-fonts' ],
        HALAL_SHOP_VERSION
    );

    // RTL stylesheet
    if ( halal_shop_is_rtl() ) {
        wp_enqueue_style(
            'halal-shop-rtl',
            HALAL_SHOP_URI . '/assets/css/rtl.css',
            [ 'halal-shop-main' ],
            HALAL_SHOP_VERSION
        );
    }

    // WooCommerce compatibility
    if ( class_exists( 'WooCommerce' ) ) {
        wp_enqueue_style(
            'halal-shop-wc',
            HALAL_SHOP_URI . '/assets/css/woocommerce.css',
            [ 'halal-shop-main' ],
            HALAL_SHOP_VERSION
        );
    }

    // Main JS (deferred)
    wp_enqueue_script(
        'halal-shop-main',
        HALAL_SHOP_URI . '/assets/js/main.js',
        [ 'jquery' ],
        HALAL_SHOP_VERSION,
        true
    );

    // Localize script
    wp_localize_script( 'halal-shop-main', 'halalShop', [
        'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
        'nonce'      => wp_create_nonce( 'halal-shop-nonce' ),
        'cartUrl'    => class_exists( 'WooCommerce' ) ? wc_get_cart_url() : home_url( '/cart' ),
        'isRtl'      => halal_shop_is_rtl() ? 'true' : 'false',
        'currency'   => class_exists( 'WooCommerce' ) ? get_woocommerce_currency_symbol() : '¥',
        'i18n'       => [
            'addedToCart'   => __( 'Added to cart!', 'halal-shop-pro' ),
            'viewCart'      => __( 'View Cart', 'halal-shop-pro' ),
            'outOfStock'    => __( 'Out of Stock', 'halal-shop-pro' ),
            'subscribe'     => __( 'Thank you for subscribing!', 'halal-shop-pro' ),
        ],
    ] );

    // Comment reply
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }

    // Wagyu category assets — loaded only on halal-wagyu/* pages
    if ( is_tax( 'product_cat' ) ) {
        $term = get_queried_object();
        if ( $term && strpos( $term->slug, 'halal-wagyu' ) !== false ) {
            wp_enqueue_style(
                'halal-shop-wagyu',
                HALAL_SHOP_URI . '/assets/css/wagyu.css',
                [ 'halal-shop-main' ],
                HALAL_SHOP_VERSION
            );
            wp_enqueue_script(
                'halal-shop-wagyu',
                HALAL_SHOP_URI . '/assets/js/wagyu.js',
                [ 'jquery', 'halal-shop-main' ],
                HALAL_SHOP_VERSION,
                true
            );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'halal_shop_enqueue_scripts' );

// Admin styles
function halal_shop_admin_styles() {
    wp_enqueue_style(
        'halal-shop-admin',
        HALAL_SHOP_URI . '/assets/css/admin.css',
        [],
        HALAL_SHOP_VERSION
    );
}
add_action( 'admin_enqueue_scripts', 'halal_shop_admin_styles' );
