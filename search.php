<?php
/**
 * Search Results Template
 */
get_header();
?>
<div class="page-hero">
    <div class="container">
        <h1><?php printf( esc_html__( '"%s" の検索結果 / Search Results for "%s"', 'halal-shop-pro' ), get_search_query(), get_search_query() ); ?></h1>
        <p><?php printf( esc_html__( '%d results found', 'halal-shop-pro' ), (int) $wp_query->found_posts ); ?></p>
    </div>
</div>
<main class="section">
    <div class="container">
        <?php if ( have_posts() ) : ?>
        <div class="grid grid-4">
            <?php while ( have_posts() ) : the_post(); ?>
            <article <?php post_class( 'product-card' ); ?>>
                <?php if ( has_post_thumbnail() ) : ?>
                <div class="product-card__image">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'halal-product' ); ?></a>
                </div>
                <?php endif; ?>
                <div class="product-card__body">
                    <div class="product-card__category"><?php echo esc_html( get_post_type() ); ?></div>
                    <h2 class="product-card__name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p style="font-size:.875rem;color:var(--color-text-light)"><?php the_excerpt(); ?></p>
                    <a href="<?php the_permalink(); ?>" class="btn btn-outline btn-sm"><?php esc_html_e( '詳細を見る', 'halal-shop-pro' ); ?></a>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
        <div style="margin-top:2.5rem;text-align:center"><?php halal_shop_pagination(); ?></div>
        <?php else : ?>
        <div style="text-align:center;padding:3rem">
            <div style="font-size:4rem;margin-bottom:1rem">🔍</div>
            <h2><?php esc_html_e( '検索結果が見つかりませんでした', 'halal-shop-pro' ); ?></h2>
            <p style="color:var(--color-text-light);margin-bottom:2rem"><?php esc_html_e( 'Try different keywords or browse our categories below.', 'halal-shop-pro' ); ?></p>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary"><?php esc_html_e( 'ホームに戻る', 'halal-shop-pro' ); ?></a>
        </div>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
