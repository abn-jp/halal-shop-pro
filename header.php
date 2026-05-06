<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Cart Drawer Overlay -->
<div class="cart-drawer-overlay" id="cartOverlay"></div>

<!-- Cart Drawer -->
<div class="cart-drawer" id="cartDrawer" role="dialog" aria-label="<?php esc_attr_e( 'Shopping Cart', 'halal-shop-pro' ); ?>">
    <div class="cart-drawer__header">
        <h2 class="cart-drawer__title">🛒 <?php esc_html_e( 'Your Cart', 'halal-shop-pro' ); ?></h2>
        <button class="cart-drawer__close" id="cartClose" aria-label="<?php esc_attr_e( 'Close cart', 'halal-shop-pro' ); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="cart-drawer__items">
        <?php if ( class_exists( 'WooCommerce' ) && ! WC()->cart->is_empty() ) : ?>
            <?php foreach ( WC()->cart->get_cart() as $key => $item ) :
                $product = $item['data']; ?>
            <div class="cart-item">
                <div class="cart-item__image"><?php echo $product->get_image( 'halal-thumb' ); ?></div>
                <div class="cart-item__info">
                    <div class="cart-item__name"><?php echo esc_html( $product->get_name() ); ?></div>
                    <div class="cart-item__qty"><?php echo esc_html__( 'Qty:', 'halal-shop-pro' ) . ' ' . $item['quantity']; ?></div>
                    <div class="cart-item__price"><?php echo WC()->cart->get_product_subtotal( $product, $item['quantity'] ); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p class="cart-empty"><?php esc_html_e( 'Your cart is empty.', 'halal-shop-pro' ); ?></p>
        <?php endif; ?>
    </div>
    <?php if ( class_exists( 'WooCommerce' ) ) : ?>
    <div class="cart-drawer__footer">
        <div class="cart-total">
            <span><?php esc_html_e( 'Total', 'halal-shop-pro' ); ?></span>
            <span><?php echo WC()->cart->get_cart_total(); ?></span>
        </div>
        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="btn btn-outline w-full" style="margin-bottom:.75rem"><?php esc_html_e( 'View Cart', 'halal-shop-pro' ); ?></a>
        <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn-primary w-full"><?php esc_html_e( 'Checkout', 'halal-shop-pro' ); ?></a>
    </div>
    <?php endif; ?>
</div>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" id="mobileOverlay"></div>

<!-- Mobile Menu -->
<nav class="mobile-menu" id="mobileMenu" role="navigation" aria-label="<?php esc_attr_e( 'Mobile Navigation', 'halal-shop-pro' ); ?>">
    <div class="mobile-menu-header">
        <span><?php esc_html_e( 'Menu', 'halal-shop-pro' ); ?></span>
        <button class="mobile-menu-close" id="mobileClose" aria-label="<?php esc_attr_e( 'Close menu', 'halal-shop-pro' ); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="mobile-nav">
        <?php wp_nav_menu( [
            'theme_location' => 'mobile',
            'container'      => false,
            'fallback_cb'    => false,
            'depth'          => 2,
        ] ); ?>
        <?php if ( class_exists( 'WooCommerce' ) ) : ?>
        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>">🛒 <?php esc_html_e( 'Cart', 'halal-shop-pro' ); ?> (<?php echo WC()->cart->get_cart_contents_count(); ?>)</a>
        <a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>">👤 <?php esc_html_e( 'My Account', 'halal-shop-pro' ); ?></a>
        <?php endif; ?>
    </div>
</nav>

<div id="page" class="site">

    <!-- Announcement Bar -->
    <?php if ( get_theme_mod( 'announcement_enabled', true ) ) : ?>
    <div class="announcement-bar" role="banner">
        <?php echo esc_html( get_theme_mod( 'announcement_text', '🎉 全国送料無料 ¥5,000以上 | Free Shipping on orders over ¥5,000' ) ); ?>
    </div>
    <?php endif; ?>

    <header class="site-header" id="siteHeader" role="banner">
        <div class="header-inner">

            <!-- Logo -->
            <div class="site-logo-wrap">
                <?php if ( has_custom_logo() ) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo">
                        <span class="site-logo__text">Halal<span>Shop</span></span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Search -->
            <div class="header-search">
                <?php get_search_form(); ?>
            </div>

            <!-- Actions -->
            <div class="header-actions">
                <!-- Language Switcher -->
                <?php halal_shop_language_switcher(); ?>

                <!-- Wishlist (requires plugin) -->
                <a href="#" class="header-action-btn" aria-label="<?php esc_attr_e( 'Wishlist', 'halal-shop-pro' ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                    <span><?php esc_html_e( 'Wishlist', 'halal-shop-pro' ); ?></span>
                </a>

                <!-- Account -->
                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                <a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>" class="header-action-btn" aria-label="<?php esc_attr_e( 'My Account', 'halal-shop-pro' ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span><?php is_user_logged_in() ? esc_html_e( 'Account', 'halal-shop-pro' ) : esc_html_e( 'Login', 'halal-shop-pro' ); ?></span>
                </a>

                <!-- Cart -->
                <button class="header-action-btn" id="cartToggle" aria-label="<?php esc_attr_e( 'Open cart', 'halal-shop-pro' ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                    <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                    <span><?php esc_html_e( 'Cart', 'halal-shop-pro' ); ?></span>
                </button>
                <?php endif; ?>

                <!-- Mobile Toggle -->
                <button class="menu-toggle" id="menuToggle" aria-label="<?php esc_attr_e( 'Open menu', 'halal-shop-pro' ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
            </div>

        </div><!-- .header-inner -->
    </header><!-- .site-header -->

    <!-- Main Navigation -->
    <nav class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'halal-shop-pro' ); ?>">
        <div class="container">
            <?php wp_nav_menu( [
                'theme_location' => 'primary',
                'menu_class'     => 'main-nav',
                'container'      => false,
                'fallback_cb'    => 'halal_shop_default_nav',
                'depth'          => 3,
            ] ); ?>
        </div>
    </nav>

    <div id="content" class="site-content">
