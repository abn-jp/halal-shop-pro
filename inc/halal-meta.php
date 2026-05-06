<?php
/**
 * Halal Certification Custom Meta Fields for WooCommerce Products
 */

// Add Halal Certification tab to product data panel
function halal_shop_product_data_tabs( $tabs ) {
    $tabs['halal_certification'] = [
        'label'  => __( 'Halal Certification', 'halal-shop-pro' ),
        'target' => 'halal_certification_data',
        'class'  => [],
        'priority' => 60,
    ];
    return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'halal_shop_product_data_tabs' );

// Render the Halal tab fields
function halal_shop_product_data_fields() {
    global $post;
    echo '<div id="halal_certification_data" class="panel woocommerce_options_panel">';
    echo '<div class="options_group">';

    woocommerce_wp_checkbox( [
        'id'          => '_is_halal_certified',
        'label'       => __( 'Halal Certified', 'halal-shop-pro' ),
        'description' => __( 'Check if this product holds a valid Halal certificate.', 'halal-shop-pro' ),
    ] );

    woocommerce_wp_select( [
        'id'      => '_halal_certification_body',
        'label'   => __( 'Certification Body', 'halal-shop-pro' ),
        'options' => [
            ''       => __( '— Select —', 'halal-shop-pro' ),
            'JHFA'   => 'JHFA（ハラール・ジャパン協会）',
            'MHC'    => 'MHC（ムスリムフレンドリーレストランサービス）',
            'JAKIM'  => 'JAKIM (Malaysia)',
            'MUI'    => 'MUI (Indonesia)',
            'IFANCA' => 'IFANCA (USA)',
            'ESMA'   => 'ESMA (UAE)',
            'HFA'    => 'HFA (UK)',
            'OTHER'  => __( 'Other', 'halal-shop-pro' ),
        ],
    ] );

    woocommerce_wp_text_input( [
        'id'          => '_halal_cert_number',
        'label'       => __( 'Certificate Number', 'halal-shop-pro' ),
        'placeholder' => 'e.g. JHFA-2024-12345',
    ] );

    woocommerce_wp_text_input( [
        'id'          => '_halal_cert_expiry',
        'label'       => __( 'Certificate Expiry Date', 'halal-shop-pro' ),
        'placeholder' => 'YYYY-MM-DD',
        'type'        => 'date',
    ] );

    woocommerce_wp_textarea_input( [
        'id'          => '_halal_ingredients',
        'label'       => __( 'Ingredients (Halal Details)', 'halal-shop-pro' ),
        'placeholder' => __( 'List all ingredients and their halal status…', 'halal-shop-pro' ),
        'rows'        => 4,
    ] );

    woocommerce_wp_text_input( [
        'id'          => '_halal_origin_country',
        'label'       => __( 'Country of Origin', 'halal-shop-pro' ),
        'placeholder' => 'e.g. Japan, Malaysia, Indonesia',
    ] );

    woocommerce_wp_text_input( [
        'id'          => '_halal_manufacturer',
        'label'       => __( 'Manufacturer', 'halal-shop-pro' ),
    ] );

    woocommerce_wp_textarea_input( [
        'id'          => '_halal_cert_notes',
        'label'       => __( 'Certification Notes', 'halal-shop-pro' ),
        'placeholder' => __( 'Additional notes about halal status, allergens, etc.', 'halal-shop-pro' ),
        'rows'        => 3,
    ] );

    echo '</div></div>';
}
add_action( 'woocommerce_product_data_panels', 'halal_shop_product_data_fields' );

// Save meta fields
function halal_shop_save_product_meta( $post_id ) {
    $fields = [
        '_is_halal_certified',
        '_halal_certification_body',
        '_halal_cert_number',
        '_halal_cert_expiry',
        '_halal_ingredients',
        '_halal_origin_country',
        '_halal_manufacturer',
        '_halal_cert_notes',
    ];

    foreach ( $fields as $field ) {
        if ( $field === '_is_halal_certified' ) {
            $value = isset( $_POST[ $field ] ) ? 'yes' : 'no';
        } else {
            $value = isset( $_POST[ $field ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $field ] ) ) : '';
        }
        update_post_meta( $post_id, $field, $value );
    }
}
add_action( 'woocommerce_process_product_meta', 'halal_shop_save_product_meta' );

// Display Halal Certification tab on single product page
function halal_shop_product_tabs( $tabs ) {
    global $post;
    $is_certified = get_post_meta( $post->ID, '_is_halal_certified', true );

    if ( $is_certified === 'yes' ) {
        $tabs['halal_info'] = [
            'title'    => __( '🕌 Halal Certification', 'halal-shop-pro' ),
            'priority' => 25,
            'callback' => 'halal_shop_certification_tab_content',
        ];
    }

    $ingredients = get_post_meta( $post->ID, '_halal_ingredients', true );
    if ( $ingredients ) {
        $tabs['halal_ingredients'] = [
            'title'    => __( '📋 Ingredients', 'halal-shop-pro' ),
            'priority' => 30,
            'callback' => 'halal_shop_ingredients_tab_content',
        ];
    }

    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'halal_shop_product_tabs' );

function halal_shop_certification_tab_content() {
    global $post;
    $cert_body   = get_post_meta( $post->ID, '_halal_certification_body', true );
    $cert_number = get_post_meta( $post->ID, '_halal_cert_number', true );
    $cert_expiry = get_post_meta( $post->ID, '_halal_cert_expiry', true );
    $origin      = get_post_meta( $post->ID, '_halal_origin_country', true );
    $manufacturer = get_post_meta( $post->ID, '_halal_manufacturer', true );
    $notes       = get_post_meta( $post->ID, '_halal_cert_notes', true );
    ?>
    <div class="halal-certification-tab">
        <div class="halal-cert-header">
            <div class="halal-cert-icon">🕌</div>
            <div>
                <h3><?php esc_html_e( 'Halal Certified Product', 'halal-shop-pro' ); ?></h3>
                <p><?php esc_html_e( 'This product has been certified Halal by an accredited certification body.', 'halal-shop-pro' ); ?></p>
            </div>
        </div>
        <table class="halal-cert-table">
            <?php if ( $cert_body ) : ?>
            <tr>
                <th><?php esc_html_e( 'Certification Body', 'halal-shop-pro' ); ?></th>
                <td><?php echo esc_html( $cert_body ); ?></td>
            </tr>
            <?php endif; ?>
            <?php if ( $cert_number ) : ?>
            <tr>
                <th><?php esc_html_e( 'Certificate Number', 'halal-shop-pro' ); ?></th>
                <td><?php echo esc_html( $cert_number ); ?></td>
            </tr>
            <?php endif; ?>
            <?php if ( $cert_expiry ) : ?>
            <tr>
                <th><?php esc_html_e( 'Valid Until', 'halal-shop-pro' ); ?></th>
                <td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $cert_expiry ) ) ); ?></td>
            </tr>
            <?php endif; ?>
            <?php if ( $origin ) : ?>
            <tr>
                <th><?php esc_html_e( 'Country of Origin', 'halal-shop-pro' ); ?></th>
                <td><?php echo esc_html( $origin ); ?></td>
            </tr>
            <?php endif; ?>
            <?php if ( $manufacturer ) : ?>
            <tr>
                <th><?php esc_html_e( 'Manufacturer', 'halal-shop-pro' ); ?></th>
                <td><?php echo esc_html( $manufacturer ); ?></td>
            </tr>
            <?php endif; ?>
        </table>
        <?php if ( $notes ) : ?>
        <div class="halal-cert-notes">
            <strong><?php esc_html_e( 'Notes:', 'halal-shop-pro' ); ?></strong>
            <p><?php echo wp_kses_post( $notes ); ?></p>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

function halal_shop_ingredients_tab_content() {
    global $post;
    $ingredients = get_post_meta( $post->ID, '_halal_ingredients', true );
    echo '<div class="halal-ingredients-tab">';
    echo '<h3>' . esc_html__( 'Ingredients', 'halal-shop-pro' ) . '</h3>';
    echo '<div class="ingredients-content">' . wp_kses_post( nl2br( $ingredients ) ) . '</div>';
    echo '</div>';
}

// Show halal badge on product loop
function halal_shop_product_loop_badge() {
    global $product;
    if ( ! $product ) return;
    $is_certified = get_post_meta( $product->get_id(), '_is_halal_certified', true );
    if ( $is_certified === 'yes' ) {
        echo '<span class="badge badge-halal">✓ Halal</span>';
    }
}
add_action( 'woocommerce_before_shop_loop_item_title', 'halal_shop_product_loop_badge', 5 );

// Show halal badge on single product
function halal_shop_single_product_halal_badge() {
    global $product;
    if ( ! $product ) return;
    $is_certified = get_post_meta( $product->get_id(), '_is_halal_certified', true );
    if ( $is_certified === 'yes' ) {
        $cert_body = get_post_meta( $product->get_id(), '_halal_certification_body', true );
        echo '<div class="halal-certification-badge">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>';
        echo '<span>' . esc_html__( 'Halal Certified', 'halal-shop-pro' );
        if ( $cert_body ) echo ' — ' . esc_html( $cert_body );
        echo '</span></div>';
    }
}
add_action( 'woocommerce_single_product_summary', 'halal_shop_single_product_halal_badge', 6 );
