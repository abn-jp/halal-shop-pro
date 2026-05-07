<?php
/**
 * Halal Shop Pro — Page Translation Creator
 * Run via: wp eval-file setup-pages.php --allow-root
 *
 * Creates translated skeleton pages for all 5 languages and links
 * them together in Polylang so the language switcher shows correct URLs.
 */

if ( ! function_exists( 'PLL' ) ) {
    echo "ERROR: Polylang is not loaded. Activate it first.\n";
    exit( 1 );
}

$langs = pll_languages_list( [ 'fields' => 'slug' ] );
if ( empty( $langs ) ) {
    echo "ERROR: No languages configured in Polylang.\n";
    exit( 1 );
}
echo "Languages: " . implode( ', ', $langs ) . "\n\n";

// ─── Page definitions ─────────────────────────────────────────────────────────
$pages = [
    [
        'slugs'    => [ 'ja'=>'home',              'en'=>'home-en',          'id'=>'home-id',         'ar'=>'home-ar',       'ms'=>'home-ms'          ],
        'titles'   => [ 'ja'=>'ホーム',            'en'=>'Home',             'id'=>'Beranda',         'ar'=>'الرئيسية',      'ms'=>'Laman Utama'       ],
        'contents' => [ 'ja'=>'',                  'en'=>'',                 'id'=>'',                'ar'=>'',              'ms'=>''                  ],
        'template' => '',
        'is_front' => true,
    ],
    [
        'slugs'    => [ 'ja'=>'about',             'en'=>'about-en',         'id'=>'tentang-kami',    'ar'=>'من-نحن',        'ms'=>'tentang-kami-ms'   ],
        'titles'   => [ 'ja'=>'会社概要',          'en'=>'About Us',         'id'=>'Tentang Kami',    'ar'=>'من نحن',        'ms'=>'Tentang Kami'       ],
        'contents' => [
            'ja' => '<p>私たちについてのページです。こちらに日本語のコンテンツを追加してください。</p>',
            'en' => '<p>Add your About Us content here in English.</p>',
            'id' => '<p>Tambahkan konten Tentang Kami Anda di sini dalam Bahasa Indonesia.</p>',
            'ar' => '<p>أضف محتوى صفحة "من نحن" هنا باللغة العربية.</p>',
            'ms' => '<p>Tambah kandungan Tentang Kami anda di sini dalam Bahasa Melayu.</p>',
        ],
        'template' => 'page-templates/template-about.php',
    ],
    [
        'slugs'    => [ 'ja'=>'faq',               'en'=>'faq-en',           'id'=>'faq-id',          'ar'=>'الاسئلة-الشائعة','ms'=>'faq-ms'           ],
        'titles'   => [ 'ja'=>'よくある質問',      'en'=>'FAQ',              'id'=>'Pertanyaan Umum', 'ar'=>'الأسئلة الشائعة','ms'=>'Soalan Lazim'     ],
        'contents' => [
            'ja' => '<p>よくある質問のページです。</p>',
            'en' => '<p>Frequently Asked Questions.</p>',
            'id' => '<p>Pertanyaan yang Sering Diajukan.</p>',
            'ar' => '<p>الأسئلة المتكررة.</p>',
            'ms' => '<p>Soalan yang Kerap Ditanya.</p>',
        ],
        'template' => 'page-templates/template-faq.php',
    ],
    [
        'slugs'    => [ 'ja'=>'contact',           'en'=>'contact-en',       'id'=>'kontak',          'ar'=>'اتصل-بنا',      'ms'=>'hubungi-kami'      ],
        'titles'   => [ 'ja'=>'お問い合わせ',      'en'=>'Contact Us',       'id'=>'Kontak',          'ar'=>'اتصل بنا',      'ms'=>'Hubungi Kami'       ],
        'contents' => [
            'ja' => '<p>お問い合わせはこちらからどうぞ。</p>',
            'en' => '<p>Get in touch with us.</p>',
            'id' => '<p>Hubungi kami di sini.</p>',
            'ar' => '<p>تواصل معنا هنا.</p>',
            'ms' => '<p>Hubungi kami di sini.</p>',
        ],
        'template' => 'page-templates/template-contact.php',
    ],
    [
        'slugs'    => [ 'ja'=>'halal-certification','en'=>'halal-cert-en',   'id'=>'sertifikasi-halal','ar'=>'شهادة-حلال',   'ms'=>'pensijilan-halal'  ],
        'titles'   => [ 'ja'=>'ハラール認証について','en'=>'Halal Certification','id'=>'Sertifikasi Halal','ar'=>'شهادة الحلال','ms'=>'Pensijilan Halal'  ],
        'contents' => [
            'ja' => '<p>ハラール認証についての説明ページです。</p>',
            'en' => '<p>Information about Halal certification.</p>',
            'id' => '<p>Informasi tentang sertifikasi Halal.</p>',
            'ar' => '<p>معلومات حول شهادة الحلال.</p>',
            'ms' => '<p>Maklumat tentang pensijilan Halal.</p>',
        ],
        'template' => 'page-templates/template-halal-cert.php',
    ],
];

// ─── Create / connect each page ───────────────────────────────────────────────
foreach ( $pages as $def ) {
    $translations = [];
    $is_front     = $def['is_front'] ?? false;

    foreach ( $langs as $lang ) {
        if ( ! isset( $def['titles'][ $lang ] ) ) continue;

        $slug    = $def['slugs'][ $lang ] ?? ( reset( $def['slugs'] ) . '-' . $lang );
        $title   = $def['titles'][ $lang ];
        $content = $def['contents'][ $lang ] ?? '';
        $tmpl    = $def['template'] ?? '';

        // Look for existing page by slug or title in this language
        $existing = get_page_by_path( $slug, OBJECT, 'page' );
        if ( ! $existing ) {
            $found = get_posts( [ 'post_type'=>'page', 'post_status'=>'publish', 'title'=>$title, 'numberposts'=>1 ] );
            if ( $found && pll_get_post_language( $found[0]->ID ) === $lang ) {
                $existing = $found[0];
            }
        }

        if ( $existing ) {
            $page_id = $existing->ID;
            pll_set_post_language( $page_id, $lang );
            if ( $tmpl ) update_post_meta( $page_id, '_wp_page_template', $tmpl );
            echo "  – Reused existing: [{$lang}] {$title} (ID: {$page_id})\n";
        } else {
            $page_id = wp_insert_post( [
                'post_title'   => $title,
                'post_name'    => $slug,
                'post_content' => $content,
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ] );
            if ( is_wp_error( $page_id ) ) {
                echo "  ✗ [{$lang}] {$title}: " . $page_id->get_error_message() . "\n";
                continue;
            }
            if ( $tmpl ) update_post_meta( $page_id, '_wp_page_template', $tmpl );
            pll_set_post_language( $page_id, $lang );
            echo "  ✓ Created: [{$lang}] {$title} (ID: {$page_id})\n";
        }

        $translations[ $lang ] = $page_id;
    }

    // Link all versions together
    if ( count( $translations ) > 1 ) {
        pll_save_post_translations( $translations );
        echo "  ✓ Linked: " . implode( ' ↔ ', array_map( fn($l,$id) => "{$l}(#{$id})", array_keys($translations), $translations ) ) . "\n";
    }

    // Set as front page (Japanese version)
    if ( $is_front && isset( $translations['ja'] ) ) {
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $translations['ja'] );
        echo "  ✓ Set homepage: ja #{$translations['ja']}\n";
    }
    echo "\n";
}

// ─── WooCommerce system pages ─────────────────────────────────────────────────
if ( class_exists( 'WooCommerce' ) ) {
    echo "── WooCommerce pages ──\n";
    $wc_map = [
        'shop'      => [ 'ja'=>'ショップ',        'en'=>'Shop',        'id'=>'Toko',   'ar'=>'المتجر',    'ms'=>'Kedai'         ],
        'cart'      => [ 'ja'=>'カート',          'en'=>'Cart',        'id'=>'Troli',  'ar'=>'السلة',     'ms'=>'Troli'         ],
        'checkout'  => [ 'ja'=>'チェックアウト',  'en'=>'Checkout',    'id'=>'Bayar',  'ar'=>'الدفع',     'ms'=>'Daftar Keluar' ],
        'myaccount' => [ 'ja'=>'マイアカウント',  'en'=>'My Account',  'id'=>'Akun',   'ar'=>'حسابي',     'ms'=>'Akaun Saya'    ],
    ];

    foreach ( $wc_map as $wc_key => $titles ) {
        $source_id = (int) get_option( "woocommerce_{$wc_key}_page_id" );
        if ( ! $source_id ) { echo "  ⚠ WC page not found: {$wc_key}\n"; continue; }

        if ( ! pll_get_post_language( $source_id ) ) {
            pll_set_post_language( $source_id, 'ja' );
        }

        $tr = pll_get_post_translations( $source_id );
        $src_post = get_post( $source_id );

        foreach ( $langs as $lang ) {
            if ( $lang === 'ja' ) { $tr['ja'] = $source_id; continue; }
            if ( isset( $tr[ $lang ] ) && get_post( $tr[ $lang ] ) ) {
                echo "  – WC {$wc_key}/{$lang}: already exists\n";
                continue;
            }
            $new_id = wp_insert_post( [
                'post_title'   => $titles[ $lang ] ?? $src_post->post_title,
                'post_name'    => $src_post->post_name . '-' . $lang,
                'post_content' => $src_post->post_content,
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ] );
            if ( is_wp_error( $new_id ) ) { echo "  ✗ WC {$wc_key}/{$lang}: " . $new_id->get_error_message() . "\n"; continue; }
            pll_set_post_language( $new_id, $lang );
            $tr[ $lang ] = $new_id;
            echo "  ✓ WC {$wc_key}/{$lang}: created #{$new_id}\n";
        }
        pll_save_post_translations( $tr );
        echo "  ✓ WC {$wc_key}: all languages linked\n";
    }
}

echo "\nDone. All pages created and linked in Polylang.\n";
echo "Visit WP Admin → Pages to review and add real content to each language version.\n";
