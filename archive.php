<?php
/**
 * Archive Template
 */
get_header();
?>
<div class="page-hero">
    <div class="container">
        <h1><?php the_archive_title(); ?></h1>
        <?php the_archive_description( '<p>', '</p>' ); ?>
    </div>
</div>
<main class="section">
    <div class="container">
        <?php if ( have_posts() ) : ?>
        <div class="grid grid-3">
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
        <p style="text-align:center;color:var(--color-text-light)"><?php esc_html_e( 'Nothing found.', 'halal-shop-pro' ); ?></p>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
