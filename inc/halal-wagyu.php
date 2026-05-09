<?php
/**
 * Halal Wagyu — Category, SEO, Meta Fields, Badges, Quick View
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ─── 1. CATEGORY REGISTRATION ────────────────────────────────────────────────

function halal_wagyu_register_categories() {
    $parent = get_term_by( 'slug', 'halal-wagyu', 'product_cat' );
    if ( ! $parent ) {
        $result = wp_insert_term(
            'Halal Wagyu | ハラール和牛',
            'product_cat',
            [
                'slug'        => 'halal-wagyu',
                'description' => 'Premium Japanese Wagyu Beef — 100% Halal Certified.',
            ]
        );
        $parent_id = is_wp_error( $result ) ? 0 : $result['term_id'];
    } else {
        $parent_id = $parent->term_id;
    }

    if ( ! $parent_id ) return;

    $subcats = [
        [ 'name' => 'A5 Wagyu',                  'slug' => 'halal-wagyu-a5',     'desc' => 'Top-grade A5 Wagyu — the pinnacle of Japanese beef quality.' ],
        [ 'name' => 'A4 Wagyu',                  'slug' => 'halal-wagyu-a4',     'desc' => 'Premium A4 Wagyu — exceptional quality with rich marbling.' ],
        [ 'name' => 'Sliced Wagyu (しゃぶしゃぶ)', 'slug' => 'halal-wagyu-sliced', 'desc' => 'Thinly sliced Wagyu perfect for shabu-shabu and sukiyaki.' ],
        [ 'name' => 'Steak Cuts',                'slug' => 'halal-wagyu-steak',  'desc' => 'Thick-cut Wagyu steaks — ribeye, sirloin, and tenderloin.' ],
        [ 'name' => 'Gift Sets ギフトセット',      'slug' => 'halal-wagyu-gift',   'desc' => 'Luxury Halal Wagyu gift sets.' ],
    ];

    foreach ( $subcats as $sub ) {
        if ( ! term_exists( $sub['slug'], 'product_cat' ) ) {
            wp_insert_term( $sub['name'], 'product_cat', [
                'slug'        => $sub['slug'],
                'description' => $sub['desc'],
                'parent'      => $parent_id,
            ] );
        }
    }
}
add_action( 'init', 'halal_wagyu_register_categories' );

// ─── 2. TEMPLATE OVERRIDE ────────────────────────────────────────────────────

add_filter( 'template_include', function( $template ) {
    if ( is_tax( 'product_cat' ) ) {
        $term = get_queried_object();
        if ( $term && strpos( $term->slug, 'halal-wagyu' ) !== false ) {
            $custom = locate_template( 'woocommerce/taxonomy-product_cat-halal-wagyu.php' );
            if ( $custom ) return $custom;
        }
    }
    return $template;
}, 99 );

// ─── 3. SEO META ─────────────────────────────────────────────────────────────

function halal_wagyu_seo_meta() {
    if ( ! is_tax( 'product_cat' ) ) return;
    $term = get_queried_object();
    if ( ! $term || strpos( $term->slug, 'halal-wagyu' ) === false ) return;
    $desc = 'Buy 100% Halal Certified Japanese Wagyu beef online. A5 & A4 grade available.';
    $kw   = 'halal wagyu, wagyu halal japan, a5 halal wagyu, a4 wagyu halal, ハラール和牛';
    echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
    echo '<meta name="keywords"    content="' . esc_attr( $kw )   . '">' . "\n";
    echo '<meta property="og:title"       content="Halal Wagyu Beef – Premium Japanese Wagyu | ' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
    echo '<meta property="og:type"        content="website">' . "\n";
}
add_action( 'wp_head', 'halal_wagyu_seo_meta' );

add_filter( 'pre_get_document_title', function( $title ) {
    if ( is_tax( 'product_cat' ) ) {
        $term = get_queried_object();
        if ( $term && strpos( $term->slug, 'halal-wagyu' ) !== false ) {
            return 'Halal Wagyu Beef – Premium Japanese Wagyu | ' . get_bloginfo( 'name' );
        }
    }
    return $title;
} );

// ─── 4. PRODUCT LOOP BADGES ──────────────────────────────────────────────────

function halal_wagyu_bestseller_badge() {
    global $product;
    if ( ! $product ) return;
    if ( get_post_meta( $product->get_id(), '_is_bestseller', true ) === 'yes' ) {
        echo '<span class="badge badge-bestseller">&#9733; Best Seller</span>';
    }
}
add_action( 'woocommerce_before_shop_loop_item_title', 'halal_wagyu_bestseller_badge', 4 );

function halal_wagyu_grade_badge() {
    global $product;
    if ( ! $product ) return;
    $terms = get_the_terms( $product->get_id(), 'product_cat' );
    if ( ! $terms ) return;
    $is_wagyu = false;
    foreach ( $terms as $t ) {
        if ( strpos( $t->slug, 'halal-wagyu' ) !== false ) { $is_wagyu = true; break; }
    }
    if ( ! $is_wagyu ) return;
    $grade = get_post_meta( $product->get_id(), '_wagyu_grade', true );
    if ( $grade ) {
        echo '<span class="badge wagyu-grade-badge wagyu-grade-' . esc_attr( strtolower( $grade ) ) . '">' . esc_html( $grade ) . '</span>';
    }
}
add_action( 'woocommerce_before_shop_loop_item_title', 'halal_wagyu_grade_badge', 6 );

// ─── 5. WAGYU ADMIN META FIELDS ──────────────────────────────────────────────

function halal_wagyu_product_data_tab( $tabs ) {
    $tabs['wagyu_details'] = [ 'label' => __( '🥩 Wagyu Details', 'halal-shop-pro' ), 'target' => 'wagyu_details_data', 'class' => [], 'priority' => 65 ];
    return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'halal_wagyu_product_data_tab' );

function halal_wagyu_product_data_fields() {
    echo '<div id="wagyu_details_data" class="panel woocommerce_options_panel"><div class="options_group">';
    woocommerce_wp_checkbox( [ 'id' => '_is_bestseller', 'label' => __( 'Best Seller', 'halal-shop-pro' ), 'description' => __( 'Show "Best Seller" badge.', 'halal-shop-pro' ) ] );
    woocommerce_wp_select( [ 'id' => '_wagyu_grade', 'label' => __( 'Wagyu Grade', 'halal-shop-pro' ), 'options' => [ '' => __( '— Select —', 'halal-shop-pro' ), 'A5' => 'A5 (最高級 / Supreme)', 'A4' => 'A4 (特選 / Premium)', 'A3' => 'A3 (上選 / Select)' ] ] );
    woocommerce_wp_text_input( [ 'id' => '_wagyu_prefecture', 'label' => __( 'Prefecture / Region', 'halal-shop-pro' ), 'placeholder' => 'e.g. Miyazaki, Kagoshima, Kobe' ] );
    woocommerce_wp_text_input( [ 'id' => '_wagyu_bms', 'label' => __( 'BMS (Beef Marbling Score)', 'halal-shop-pro' ), 'placeholder' => 'e.g. BMS 10–12' ] );
    woocommerce_wp_select( [ 'id' => '_wagyu_cut', 'label' => __( 'Cut Type', 'halal-shop-pro' ), 'options' => [ '' => '— Select —', 'ribeye' => 'Ribeye / リブアイ', 'sirloin' => 'Sirloin / サーロイン', 'tenderloin' => 'Tenderloin / ヒレ', 'sliced' => 'Sliced / スライス', 'mixed' => 'Gift Set / ギフトセット' ] ] );
    woocommerce_wp_text_input( [ 'id' => '_wagyu_weight_g', 'label' => __( 'Weight (g)', 'halal-shop-pro' ), 'type' => 'number', 'placeholder' => '200' ] );
    echo '</div></div>';
}
add_action( 'woocommerce_product_data_panels', 'halal_wagyu_product_data_fields' );

function halal_wagyu_save_product_meta( $post_id ) {
    $checkboxes  = [ '_is_bestseller' ];
    $text_fields = [ '_wagyu_grade', '_wagyu_prefecture', '_wagyu_bms', '_wagyu_cut', '_wagyu_weight_g' ];
    foreach ( $checkboxes  as $f ) { update_post_meta( $post_id, $f, isset( $_POST[$f] ) ? 'yes' : 'no' ); }
    foreach ( $text_fields as $f ) { if ( isset( $_POST[$f] ) ) update_post_meta( $post_id, $f, sanitize_text_field( wp_unslash( $_POST[$f] ) ) ); }
}
add_action( 'woocommerce_process_product_meta', 'halal_wagyu_save_product_meta' );

// ─── 6. QUICK VIEW AJAX ──────────────────────────────────────────────────────

function halal_wagyu_quick_view() {
    check_ajax_referer( 'halal-shop-nonce', 'nonce' );
    $product_id = absint( $_POST['product_id'] ?? 0 );
    $product    = $product_id ? wc_get_product( $product_id ) : null;
    if ( ! $product ) wp_send_json_error( 'Invalid product', 400 );

    $grade      = get_post_meta( $product_id, '_wagyu_grade', true );
    $prefecture = get_post_meta( $product_id, '_wagyu_prefecture', true );
    $is_cert    = get_post_meta( $product_id, '_is_halal_certified', true );
    $bms        = get_post_meta( $product_id, '_wagyu_bms', true );
    $weight     = get_post_meta( $product_id, '_wagyu_weight_g', true );
    $cut        = get_post_meta( $product_id, '_wagyu_cut', true );
    $cut_labels = [ 'ribeye' => 'Ribeye', 'sirloin' => 'Sirloin', 'tenderloin' => 'Tenderloin', 'sliced' => 'Sliced', 'mixed' => 'Gift Set' ];

    ob_start(); ?>
    <div class="wagyu-quick-view">
        <div class="wagyu-qv-image">
            <?php echo $product->get_image( 'halal-product' ); ?>
            <?php if ( $grade ) : ?><span class="wagyu-qv-grade wagyu-grade-badge wagyu-grade-<?php echo esc_attr( strtolower( $grade ) ); ?>"><?php echo esc_html( $grade ); ?></span><?php endif; ?>
        </div>
        <div class="wagyu-qv-info">
            <h2 class="wagyu-qv-title"><?php echo esc_html( $product->get_name() ); ?></h2>
            <?php if ( $is_cert === 'yes' ) : ?><div class="wagyu-qv-halal-badge">✓ <?php esc_html_e( 'Halal Certified', 'halal-shop-pro' ); ?></div><?php endif; ?>
            <div class="wagyu-qv-price"><?php echo $product->get_price_html(); ?></div>
            <div class="wagyu-qv-short-desc"><?php echo wp_kses_post( $product->get_short_description() ); ?></div>
            <dl class="wagyu-qv-specs">
                <?php if ( $grade )      : ?><div><dt><?php esc_html_e( 'Grade', 'halal-shop-pro' ); ?></dt><dd><?php echo esc_html( $grade ); ?></dd></div><?php endif; ?>
                <?php if ( $prefecture ) : ?><div><dt><?php esc_html_e( 'Origin', 'halal-shop-pro' ); ?></dt><dd>&#127471;&#127477; <?php echo esc_html( $prefecture ); ?></dd></div><?php endif; ?>
                <?php if ( $bms )        : ?><div><dt>BMS</dt><dd><?php echo esc_html( $bms ); ?></dd></div><?php endif; ?>
                <?php if ( $weight )     : ?><div><dt><?php esc_html_e( 'Weight', 'halal-shop-pro' ); ?></dt><dd><?php echo esc_html( number_format( (int) $weight ) ); ?>g</dd></div><?php endif; ?>
            </dl>
            <div class="wagyu-qv-actions">
                <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="button"><?php esc_html_e( 'View Details', 'halal-shop-pro' ); ?></a>
            </div>
        </div>
    </div>
    <?php wp_send_json_success( [ 'html' => ob_get_clean() ] );
}
add_action( 'wp_ajax_halal_wagyu_quick_view',        'halal_wagyu_quick_view' );
add_action( 'wp_ajax_nopriv_halal_wagyu_quick_view', 'halal_wagyu_quick_view' );
