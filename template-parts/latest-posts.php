<?php
/**
 * Template Part: Latest Blog Posts (front page section)
 * Supports all 5 languages via halal_mod() inline table.
 */

$posts_query = new WP_Query( [
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 3,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'no_found_rows'  => true,
] );

if ( ! $posts_query->have_posts() ) return;

$lang       = function_exists( 'halal_lang' ) ? halal_lang() : 'ja';
$blog_url   = get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/blog/' );

$section_title = [
    'ja' => 'ブログ・最新情報',
    'en' => 'Latest from Our Blog',
    'id' => 'Artikel Terbaru',
    'ar' => 'أحدث المقالات',
    'ms' => 'Artikel Terkini',
][ $lang ] ?? 'ブログ・最新情報';

$section_sub = [
    'ja' => 'ハラールフードや日本のムスリムライフに関する最新情報をお届けします',
    'en' => 'Stay updated with the latest news on halal food and Muslim life in Japan',
    'id' => 'Dapatkan informasi terbaru tentang makanan halal dan kehidupan Muslim di Jepang',
    'ar' => 'ابقَ على اطلاع بآخر الأخبار حول الطعام الحلال والحياة الإسلامية في اليابان',
    'ms' => 'Dapatkan berita terkini tentang makanan halal dan kehidupan Muslim di Jepun',
][ $lang ] ?? 'ハラールフードや日本のムスリムライフに関する最新情報をお届けします';

$read_more = [
    'ja' => '続きを読む →',
    'en' => 'Read More →',
    'id' => 'Baca Selengkapnya →',
    'ar' => 'اقرأ المزيد →',
    'ms' => 'Baca Lagi →',
][ $lang ] ?? '続きを読む →';

$view_all = [
    'ja' => 'すべての記事を見る',
    'en' => 'View All Posts',
    'id' => 'Lihat Semua Artikel',
    'ar' => 'عرض جميع المقالات',
    'ms' => 'Lihat Semua Artikel',
][ $lang ] ?? 'すべての記事を見る';

$is_rtl = ( $lang === 'ar' );
?>

<section class="latest-posts-section" <?php if ( $is_rtl ) echo 'dir="rtl"'; ?>>
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?php echo esc_html( $section_title ); ?></h2>
            <p class="section-subtitle"><?php echo esc_html( $section_sub ); ?></p>
        </div>

        <div class="latest-posts-grid">
            <?php while ( $posts_query->have_posts() ) : $posts_query->the_post(); ?>
            <article class="post-card">
                <?php if ( has_post_thumbnail() ) : ?>
                <a href="<?php the_permalink(); ?>" class="post-card__image-wrap" tabindex="-1" aria-hidden="true">
                    <?php the_post_thumbnail( 'halal-category', [ 'class' => 'post-card__image', 'loading' => 'lazy' ] ); ?>
                </a>
                <?php else : ?>
                <a href="<?php the_permalink(); ?>" class="post-card__image-wrap post-card__image-placeholder" tabindex="-1" aria-hidden="true">
                    <div class="post-card__no-image">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                </a>
                <?php endif; ?>

                <div class="post-card__body">
                    <div class="post-card__meta">
                        <time class="post-card__date" datetime="<?php echo get_the_date( 'c' ); ?>">
                            <?php echo get_the_date(); ?>
                        </time>
                    </div>
                    <h3 class="post-card__title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <p class="post-card__excerpt"><?php echo wp_trim_words( get_the_excerpt(), 18, '…' ); ?></p>
                    <a href="<?php the_permalink(); ?>" class="post-card__readmore"><?php echo esc_html( $read_more ); ?></a>
                </div>
            </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <div class="latest-posts-footer">
            <a href="<?php echo esc_url( $blog_url ); ?>" class="btn btn-outline">
                <?php echo esc_html( $view_all ); ?>
            </a>
        </div>
    </div>
</section>
