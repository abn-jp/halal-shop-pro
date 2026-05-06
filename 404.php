<?php
/**
 * 404 Page
 */
get_header();
?>
<main class="section" style="min-height:60vh;display:flex;align-items:center">
    <div class="container" style="text-align:center">
        <div style="font-size:6rem;margin-bottom:1rem">🕌</div>
        <h1 style="font-size:6rem;font-weight:700;color:var(--color-primary);margin-bottom:0;line-height:1">404</h1>
        <h2 style="margin-bottom:1rem;color:var(--color-text)"><?php esc_html_e( 'ページが見つかりません', 'halal-shop-pro' ); ?></h2>
        <p style="color:var(--color-text-light);font-size:1.125rem;margin-bottom:2rem">
            <?php esc_html_e( 'The page you are looking for does not exist or has been moved.', 'halal-shop-pro' ); ?>
        </p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary btn-lg">
                <?php esc_html_e( 'ホームに戻る / Go Home', 'halal-shop-pro' ); ?>
            </a>
            <?php if ( class_exists( 'WooCommerce' ) ) : ?>
            <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="btn btn-outline btn-lg">
                🛒 <?php esc_html_e( 'Shop Halal Products', 'halal-shop-pro' ); ?>
            </a>
            <?php endif; ?>
        </div>
        <div style="margin-top:3rem;max-width:400px;margin-left:auto;margin-right:auto">
            <?php get_search_form(); ?>
        </div>
    </div>
</main>
<?php get_footer(); ?>
