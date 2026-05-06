<?php
/**
 * Theme Customizer Settings
 */

function halal_shop_customizer( $wp_customize ) {

    /* ── Announcement Bar ── */
    $wp_customize->add_section( 'halal_announcement', [
        'title'    => __( 'Announcement Bar', 'halal-shop-pro' ),
        'priority' => 30,
    ] );
    $wp_customize->add_setting( 'announcement_text', [ 'default' => '🎉 全国送料無料 ¥5,000以上 | Free Shipping on orders over ¥5,000', 'sanitize_callback' => 'sanitize_text_field' ] );
    $wp_customize->add_control( 'announcement_text', [ 'label' => __( 'Announcement Text', 'halal-shop-pro' ), 'section' => 'halal_announcement', 'type' => 'text' ] );
    $wp_customize->add_setting( 'announcement_enabled', [ 'default' => true, 'sanitize_callback' => 'wp_validate_boolean' ] );
    $wp_customize->add_control( 'announcement_enabled', [ 'label' => __( 'Enable Announcement Bar', 'halal-shop-pro' ), 'section' => 'halal_announcement', 'type' => 'checkbox' ] );

    /* ── Hero Section ── */
    $wp_customize->add_section( 'halal_hero', [
        'title'    => __( 'Homepage Hero', 'halal-shop-pro' ),
        'priority' => 35,
    ] );
    $hero_settings = [
        'hero_title'    => [ __( 'Hero Title (JP)', 'halal-shop-pro' ), 'ハラールフードの安心・安全な\nオンラインショップ' ],
        'hero_title_en' => [ __( 'Hero Title (EN)', 'halal-shop-pro' ), 'Your Trusted Halal Food Online Store in Japan' ],
        'hero_subtitle' => [ __( 'Hero Subtitle', 'halal-shop-pro' ), 'ムスリムフレンドリーな食品を全国にお届け。厳選されたハラール認証食品を取り揃えています。' ],
        'hero_btn_text' => [ __( 'Button Text', 'halal-shop-pro' ), '商品を見る / Shop Now' ],
    ];
    foreach ( $hero_settings as $key => $data ) {
        $wp_customize->add_setting( $key, [ 'default' => $data[1], 'sanitize_callback' => 'sanitize_textarea_field' ] );
        $wp_customize->add_control( $key, [ 'label' => $data[0], 'section' => 'halal_hero', 'type' => ( $key === 'hero_btn_text' ? 'text' : 'textarea' ) ] );
    }
    $wp_customize->add_setting( 'hero_image', [ 'sanitize_callback' => 'esc_url_raw' ] );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hero_image', [
        'label'   => __( 'Hero Background Image', 'halal-shop-pro' ),
        'section' => 'halal_hero',
    ] ) );

    /* ── Colors ── */
    $wp_customize->add_section( 'halal_colors', [
        'title'    => __( 'Brand Colors', 'halal-shop-pro' ),
        'priority' => 40,
    ] );
    $color_settings = [
        'color_primary'   => [ __( 'Primary Green', 'halal-shop-pro' ), '#1B8A3B' ],
        'color_secondary' => [ __( 'Gold Accent', 'halal-shop-pro' ),   '#C9A84C' ],
        'color_dark'      => [ __( 'Dark Green', 'halal-shop-pro' ),    '#0D5C26' ],
    ];
    foreach ( $color_settings as $key => $data ) {
        $wp_customize->add_setting( $key, [ 'default' => $data[1], 'sanitize_callback' => 'sanitize_hex_color', 'transport' => 'postMessage' ] );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $key, [
            'label'   => $data[0],
            'section' => 'halal_colors',
        ] ) );
    }

    /* ── Contact Info ── */
    $wp_customize->add_section( 'halal_contact', [
        'title'    => __( 'Company Contact Info', 'halal-shop-pro' ),
        'priority' => 50,
    ] );
    $contact_settings = [
        'company_email'   => [ __( 'Email', 'halal-shop-pro' ),     'info@halalshop.example.com' ],
        'company_phone'   => [ __( 'Phone', 'halal-shop-pro' ),     '03-XXXX-XXXX' ],
        'company_address' => [ __( 'Address', 'halal-shop-pro' ),   '東京都〇〇区〇〇 X-X-X' ],
        'company_hours'   => [ __( 'Business Hours', 'halal-shop-pro' ), '月–金 9:00–18:00' ],
    ];
    foreach ( $contact_settings as $key => $data ) {
        $wp_customize->add_setting( $key, [ 'default' => $data[1], 'sanitize_callback' => 'sanitize_text_field' ] );
        $wp_customize->add_control( $key, [ 'label' => $data[0], 'section' => 'halal_contact', 'type' => 'text' ] );
    }

    /* ── Social Links ── */
    $wp_customize->add_section( 'halal_social', [
        'title'    => __( 'Social Media Links', 'halal-shop-pro' ),
        'priority' => 55,
    ] );
    $socials = [ 'instagram', 'facebook', 'twitter', 'youtube', 'line' ];
    foreach ( $socials as $s ) {
        $wp_customize->add_setting( 'social_' . $s, [ 'default' => '', 'sanitize_callback' => 'esc_url_raw' ] );
        $wp_customize->add_control( 'social_' . $s, [ 'label' => ucfirst( $s ) . ' URL', 'section' => 'halal_social', 'type' => 'url' ] );
    }

    /* ── Footer ── */
    $wp_customize->add_section( 'halal_footer', [
        'title'    => __( 'Footer', 'halal-shop-pro' ),
        'priority' => 60,
    ] );
    $wp_customize->add_setting( 'footer_about', [
        'default'           => 'ハラールショップは、日本に住むムスリムの方々や訪日旅行者の皆様に、安心・安全なハラール食品をお届けするオンラインショップです。',
        'sanitize_callback' => 'sanitize_textarea_field',
    ] );
    $wp_customize->add_control( 'footer_about', [ 'label' => __( 'Footer About Text', 'halal-shop-pro' ), 'section' => 'halal_footer', 'type' => 'textarea' ] );
    $wp_customize->add_setting( 'footer_copyright', [ 'default' => '© ' . date('Y') . ' Halal Shop Pro. All rights reserved.', 'sanitize_callback' => 'sanitize_text_field' ] );
    $wp_customize->add_control( 'footer_copyright', [ 'label' => __( 'Copyright Text', 'halal-shop-pro' ), 'section' => 'halal_footer', 'type' => 'text' ] );
}
add_action( 'customize_register', 'halal_shop_customizer' );

// Output customizer CSS variables
function halal_shop_customizer_css() {
    $primary   = get_theme_mod( 'color_primary', '#1B8A3B' );
    $secondary = get_theme_mod( 'color_secondary', '#C9A84C' );
    $dark      = get_theme_mod( 'color_dark', '#0D5C26' );
    echo '<style id="halal-shop-customizer-css">:root{'
        . '--color-primary:' . sanitize_hex_color( $primary ) . ';'
        . '--color-secondary:' . sanitize_hex_color( $secondary ) . ';'
        . '--color-primary-dark:' . sanitize_hex_color( $dark ) . ';'
        . '}</style>';
}
add_action( 'wp_head', 'halal_shop_customizer_css' );
