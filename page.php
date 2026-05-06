<?php
/**
 * Generic Page Template
 */
get_header();
?>
<main class="section">
    <div class="container" style="max-width:900px">
        <?php while ( have_posts() ) : the_post(); ?>
        <article <?php post_class(); ?>>
            <h1 style="color:var(--color-primary);margin-bottom:1.5rem"><?php the_title(); ?></h1>
            <div class="entry-content" style="line-height:1.8"><?php the_content(); ?></div>
        </article>
        <?php endwhile; ?>
    </div>
</main>
<?php get_footer(); ?>
