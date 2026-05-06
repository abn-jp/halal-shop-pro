<?php
/**
 * Single Post Template
 */
get_header();
?>
<main class="section">
    <div class="container" style="max-width:800px">
        <?php while ( have_posts() ) : the_post(); ?>
        <article <?php post_class(); ?>>

            <?php if ( has_post_thumbnail() ) : ?>
            <div style="margin-bottom:2rem;border-radius:16px;overflow:hidden">
                <?php the_post_thumbnail( 'halal-hero' ); ?>
            </div>
            <?php endif; ?>

            <div style="margin-bottom:1.5rem">
                <div style="font-size:.875rem;color:var(--color-text-light);margin-bottom:.5rem">
                    <?php echo esc_html( get_the_date() ); ?> &nbsp;·&nbsp;
                    <?php the_category( ', ' ); ?>
                </div>
                <h1 style="color:var(--color-primary)"><?php the_title(); ?></h1>
            </div>

            <div class="entry-content" style="line-height:1.85"><?php the_content(); ?></div>

            <div style="margin-top:2rem;padding-top:2rem;border-top:1px solid var(--color-border)">
                <?php the_tags( '<div style="display:flex;gap:.5rem;flex-wrap:wrap"><span style="font-weight:600">Tags:</span>', ', ', '</div>' ); ?>
            </div>

            <?php if ( comments_open() || get_comments_number() ) : ?>
            <div style="margin-top:3rem"><?php comments_template(); ?></div>
            <?php endif; ?>

        </article>
        <?php endwhile; ?>
    </div>
</main>
<?php get_footer(); ?>
