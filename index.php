<?php
/**
 * Main template fallback
 */
get_header();
?>
<main class="section">
    <div class="container">
        <?php if ( have_posts() ) : ?>
        <h1 class="section-title"><?php
            if ( is_home() ) esc_html_e( 'Latest News', 'halal-shop-pro' );
            elseif ( is_search() ) printf( esc_html__( 'Search Results for: %s', 'halal-shop-pro' ), get_search_query() );
            elseif ( is_archive() ) the_archive_title();
            else esc_html_e( 'Posts', 'halal-shop-pro' );
        ?></h1>

        <div class="grid grid-3" style="margin-top:2rem">
            <?php while ( have_posts() ) : the_post(); ?>
            <article <?php post_class( 'product-card' ); ?>>
                <?php if ( has_post_thumbnail() ) : ?>
                <div class="product-card__image">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'halal-product' ); ?></a>
                </div>
                <?php endif; ?>
                <div class="product-card__body">
                    <div class="product-card__category"><?php echo esc_html( get_the_date() ); ?></div>
                    <h2 class="product-card__name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p style="font-size:.875rem;color:var(--color-text-light)"><?php the_excerpt(); ?></p>
                    <a href="<?php the_permalink(); ?>" class="btn btn-outline btn-sm"><?php esc_html_e( 'Read More', 'halal-shop-pro' ); ?></a>
                </div>
            </article>
            <?php endwhile; ?>
        </div>

        <div style="margin-top:2.5rem;text-align:center"><?php halal_shop_pagination(); ?></div>

        <?php else : ?>
        <p style="text-align:center;color:var(--color-text-light)"><?php esc_html_e( 'No content found.', 'halal-shop-pro' ); ?></p>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
