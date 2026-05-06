<?php
/**
 * Widget Areas
 */

function halal_shop_widgets_init() {
    $sidebars = [
        [
            'id'   => 'sidebar-shop',
            'name' => __( 'Shop Sidebar', 'halal-shop-pro' ),
            'desc' => __( 'Displayed on WooCommerce product archive pages.', 'halal-shop-pro' ),
        ],
        [
            'id'   => 'sidebar-main',
            'name' => __( 'Blog Sidebar', 'halal-shop-pro' ),
            'desc' => __( 'Displayed on blog/post pages.', 'halal-shop-pro' ),
        ],
        [
            'id'   => 'footer-1',
            'name' => __( 'Footer Column 1', 'halal-shop-pro' ),
            'desc' => __( 'First footer column widgets.', 'halal-shop-pro' ),
        ],
        [
            'id'   => 'footer-2',
            'name' => __( 'Footer Column 2', 'halal-shop-pro' ),
            'desc' => __( 'Second footer column widgets.', 'halal-shop-pro' ),
        ],
        [
            'id'   => 'footer-3',
            'name' => __( 'Footer Column 3', 'halal-shop-pro' ),
            'desc' => __( 'Third footer column widgets.', 'halal-shop-pro' ),
        ],
    ];

    foreach ( $sidebars as $sidebar ) {
        register_sidebar( [
            'id'            => $sidebar['id'],
            'name'          => $sidebar['name'],
            'description'   => $sidebar['desc'],
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ] );
    }
}
add_action( 'widgets_init', 'halal_shop_widgets_init' );
