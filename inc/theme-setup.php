<?php
/**
 * Theme Setup
 */

function halal_shop_setup() {
    load_theme_textdomain( 'halal-shop-pro', HALAL_SHOP_DIR . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'custom-logo', [
        'height'      => 80,
        'width'       => 240,
        'flex-height' => true,
        'flex-width'  => true,
    ] );
    add_theme_support( 'custom-background', [
        'default-color' => 'ffffff',
    ] );

    // WooCommerce
    add_theme_support( 'woocommerce', [
        'thumbnail_image_width'         => 400,
        'gallery_thumbnail_image_width' => 100,
        'single_image_width'            => 600,
        'product_grid'                  => [
            'default_rows'    => 3,
            'min_rows'        => 1,
            'max_rows'        => 10,
            'default_columns' => 4,
            'min_columns'     => 1,
            'max_columns'     => 6,
        ],
    ] );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

    // Image sizes
    add_image_size( 'halal-product',   400, 400, true );
    add_image_size( 'halal-hero',     1920, 680, true );
    add_image_size( 'halal-category',  300, 300, true );
    add_image_size( 'halal-thumb',      80,  80, true );

    // Navigation menus
    register_nav_menus( [
        'primary'  => __( 'Primary Navigation', 'halal-shop-pro' ),
        'footer-1' => __( 'Footer – Products', 'halal-shop-pro' ),
        'footer-2' => __( 'Footer – Company', 'halal-shop-pro' ),
        'footer-3' => __( 'Footer – Support', 'halal-shop-pro' ),
        'mobile'   => __( 'Mobile Navigation', 'halal-shop-pro' ),
    ] );
}
add_action( 'after_setup_theme', 'halal_shop_setup' );

function halal_shop_excerpt_length() { return 20; }
add_filter( 'excerpt_length', 'halal_shop_excerpt_length' );

function halal_shop_excerpt_more() { return '&hellip;'; }
add_filter( 'excerpt_more', 'halal_shop_excerpt_more' );

function halal_shop_body_classes( $classes ) {
    if ( is_rtl() ) $classes[] = 'rtl-layout';
    if ( class_exists( 'WooCommerce' ) && is_woocommerce() ) $classes[] = 'is-woocommerce';
    return $classes;
}
add_filter( 'body_class', 'halal_shop_body_classes' );

// Allow SVG uploads for admin
function halal_shop_allow_svg( $mimes ) {
    $mimes['svg']  = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'halal_shop_allow_svg' );

// Pagination
function halal_shop_pagination() {
    global $wp_query;
    $big = 999999;
    echo paginate_links( [
        'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format'  => '?paged=%#%',
        'current' => max( 1, get_query_var( 'paged' ) ),
        'total'   => $wp_query->max_num_pages,
        'prev_text' => '&larr;',
        'next_text' => '&rarr;',
    ] );
}
