# Halal Shop Pro — Complete Setup Guide
**WordPress Halal E-Commerce Theme | v1.0.0**

---

## 1. HOSTING & SERVER REQUIREMENTS

### Recommended Hosting
| Provider | Plan | Notes |
|---|---|---|
| **Xserver** | Business | Best for Japan market, fast CDN, easy WP install |
| **ConoHa WING** | WING Basic | Affordable, good performance in Japan |
| **SiteGround** | GrowBig | Best for international traffic |
| **Cloudways** | DigitalOcean 2GB | Best performance, scalable |

### Minimum Requirements
- PHP 8.0 or higher
- MySQL 8.0 / MariaDB 10.6
- HTTPS/SSL certificate (free Let's Encrypt is fine)
- WordPress 6.0+
- Memory limit: 256MB minimum (512MB recommended)

---

## 2. DOMAIN PURCHASE

1. Register domain at **お名前.com**, **ムームードメイン**, or **Namecheap**
2. Recommended TLDs: `.jp` for Japan market, `.com` for international
3. Point nameservers to your hosting provider
4. Enable SSL (most hosts provide free Let's Encrypt via 1-click)

---

## 3. WORDPRESS INSTALLATION

### Via Hosting Control Panel (recommended)
Most Japanese hosts (Xserver, ConoHa) have a 1-click WordPress installer.
1. Log into hosting control panel
2. Find "WordPress簡単インストール" / WordPress auto-install
3. Set site URL, admin username, strong password
4. Click Install

### Manual Install
```bash
# Download WordPress
wget https://wordpress.org/latest.tar.gz
tar -xzf latest.tar.gz

# Create database (via phpMyAdmin or CLI)
CREATE DATABASE halalshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'halaluser'@'localhost' IDENTIFIED BY 'StrongPassword123!';
GRANT ALL PRIVILEGES ON halalshop.* TO 'halaluser'@'localhost';
```

### wp-config.php Key Settings
```php
define('WP_MEMORY_LIMIT', '512M');
define('WP_MAX_MEMORY_LIMIT', '512M');
define('WP_DEBUG', false);           // true during development only
define('WP_CACHE', true);
define('FORCE_SSL_ADMIN', true);

// Japanese timezone
date_default_timezone_set('Asia/Tokyo');
```

---

## 4. THEME INSTALLATION

1. Zip the `halal-shop-pro/` folder
2. WordPress Admin → **Appearance → Themes → Add New → Upload Theme**
3. Select `halal-shop-pro.zip` → Install → Activate
4. Go to **Appearance → Customize** to configure logo, colors, contact info

---

## 5. REQUIRED PLUGINS (Install in this order)

### A. WooCommerce (E-commerce)
```
Admin → Plugins → Add New → Search "WooCommerce" → Install → Activate
```
After activation, run the Setup Wizard:
- Store country: Japan
- Currency: Japanese Yen (¥)
- Selling: Physical products
- Enable taxes: Yes (10% consumption tax)

### B. WPML or Polylang (Multilingual)
**Option 1: WPML** (paid, most robust)
- Purchase at wpml.org (~$39/year)
- Install WPML Multilingual CMS + WooCommerce Multilingual
- Languages to add: Japanese (default), English, Indonesian, Arabic, Malay
- Enable String Translation and Media Translation

**Option 2: Polylang + WooCommerce** (free tier available)
```
Admin → Plugins → Add New → "Polylang for WooCommerce"
```
- Add languages: ja, en, id, ar, ms
- Translate pages, products, and menu items

### C. Rank Math SEO
```
Admin → Plugins → Add New → "Rank Math SEO" → Install
```
Setup Wizard settings:
- Business Type: Online Store
- Connect Google Search Console
- Enable Sitemap (submit to Google Search Console)
- Focus keywords: ハラール食品, halal food japan, makanan halal jepang

### D. Security — Wordfence
```
Admin → Plugins → Add New → "Wordfence Security"
```
- Enable firewall
- Enable malware scanner
- Enable brute force protection
- Set email alerts for: admin login, failed logins

### E. Performance — WP Rocket (paid) or W3 Total Cache (free)
**WP Rocket** ($59/year — highly recommended)
- Page caching: ON
- Browser caching: ON
- GZIP compression: ON
- Image lazy loading: ON
- Minify CSS/JS: ON
- CDN: Connect Cloudflare (free plan works)

**W3 Total Cache (free alternative)**
```
Admin → Plugins → Add New → "W3 Total Cache"
```

### F. Contact Form 7 (Contact page)
```
Admin → Plugins → Add New → "Contact Form 7"
```

### G. Yoast WooCommerce SEO (optional, adds product schema)

### H. WooCommerce Stripe Gateway
```
Admin → Plugins → Add New → "WooCommerce Stripe Payment Gateway"
```

---

## 6. WOOCOMMERCE CONFIGURATION

### Tax Settings (Japanese Consumption Tax 消費税)
```
WooCommerce → Settings → Tax
- Enable taxes: ✓
- Prices entered with tax: Yes (prices include tax)
- Display prices in shop: Including tax
- Display prices during cart: Including tax
- Tax based on: Customer billing address
```

Add tax rate:
```
WooCommerce → Settings → Tax → Standard Rates → Add Row
- Country: JP
- State: * (all)
- Rate %: 10
- Tax Name: 消費税
- Compound: ✓
- Shipping: ✓
```

### Shipping Zones
```
WooCommerce → Settings → Shipping → Add Shipping Zone
```

**Zone 1: Japan (国内)**
- Zone name: Japan 国内
- Zone regions: Japan
- Shipping methods:
  - Flat rate: ¥800 (under ¥5,000)
  - Free shipping: Minimum order ¥5,000
  - Local pickup (for Tokyo area)

**Zone 2: International**
- Zone name: International 海外
- Zone regions: All countries (except Japan)
- Flat rate: ¥2,500 (Southeast Asia), ¥3,500 (Middle East), ¥4,500 (Others)

### Payment Gateways

**Stripe (Credit Card)**
```
WooCommerce → Settings → Payments → Stripe → Enable
- Publishable key: pk_live_xxxxx
- Secret key: sk_live_xxxxx
- Enable Apple Pay / Google Pay: ✓
- Statement descriptor: HALALSHOP
```

**PayPay (Japan)**
Install plugin: "WooCommerce PayPay Payment Gateway" or use a compatible gateway.
Obtain credentials from PayPay for Business (developer.paypay.ne.jp)

**Bank Transfer (銀行振込)**
```
WooCommerce → Settings → Payments → Direct Bank Transfer
- Account name: ハラールショップ株式会社
- Account number: [your account]
- Bank name: [your bank]
- Instructions: ご注文確認後3営業日以内にお振込ください
```

---

## 7. PRODUCT SETUP

### Product Categories (create these first)
```
Products → Categories → Add New
```
Create these categories:
| Slug | Japanese Name | English |
|---|---|---|
| meat-poultry | 肉・肉加工品 | Meat & Poultry |
| seasonings | 調味料・ソース | Seasonings |
| frozen-foods | 冷凍食品 | Frozen Foods |
| snacks | お菓子・スナック | Snacks |
| beverages | 飲料 | Beverages |
| instant-foods | インスタント食品 | Instant Foods |
| halal-sweets | ハラールスイーツ | Halal Sweets |
| dairy | 乳製品 | Dairy |

### Adding a Product
```
Products → Add New
```
1. **Title**: Product name (Japanese primary)
2. **Description**: Detailed description
3. **Short Description**: 1-2 lines for product summary
4. **Product Data**:
   - Price: Regular price (税込 / incl. tax)
   - Sale price (optional)
   - Stock: Enable stock management
   - SKU: e.g., MEAT-001
5. **Product Category**: Select relevant category
6. **Featured Image**: Upload 800×800px image
7. **Gallery**: Additional product photos

### Adding Halal Certification (our custom field)
In the Product Data panel, click **"Halal Certification"** tab:
- ✓ Halal Certified
- Certification Body: JHFA / MUI / JAKIM / etc.
- Certificate Number: e.g., JHFA-2024-12345
- Expiry Date: YYYY-MM-DD
- Ingredients: Full ingredient list
- Country of Origin: Japan / Malaysia / etc.

### Product Tags for SEO
Add tags: `halal`, `ハラール`, `muslim-friendly`, `pork-free`, `alcohol-free`

---

## 8. PAGES TO CREATE

Create these pages under **Pages → Add New**:

| Page Title | Slug | Template |
|---|---|---|
| ホーム / Home | / | Front Page (set in Reading Settings) |
| ショップ / Shop | /shop | WooCommerce auto-creates |
| カート / Cart | /cart | WooCommerce auto-creates |
| レジ / Checkout | /checkout | WooCommerce auto-creates |
| マイアカウント / My Account | /my-account | WooCommerce auto-creates |
| 会社概要 / About | /about | Template: About Us |
| ハラール認証 / Halal Cert | /halal-certification | Template: Halal Certification |
| よくある質問 / FAQ | /faq | Template: FAQ |
| お問い合わせ / Contact | /contact | Template: Contact |
| プライバシーポリシー | /privacy-policy | Default |
| 利用規約 | /terms | Default |
| 特定商取引法 | /tokushoho | Default |
| 配送について | /shipping | Default |

### Reading Settings
```
Settings → Reading
- Your homepage displays: A static page
- Homepage: Home
- Posts page: Blog (create a blog page)
```

---

## 9. NAVIGATION MENUS

```
Appearance → Menus → Create New Menu
```

**Primary Menu** (assign to "Primary Navigation"):
- すべての商品 → Shop page
- 肉・肉加工品 → meat-poultry category
- 調味料 → seasonings category
- 冷凍食品 → frozen-foods category
- お菓子 → snacks category
- 飲料 → beverages category
- セール → Shop filtered by sale

**Footer Menus** (assign to Footer columns):
- Footer Products: product categories
- Footer Company: About, Halal Cert, FAQ
- Footer Support: Contact, Shipping, Returns

---

## 10. MULTILINGUAL SETUP (WPML)

### Language Configuration
```
WPML → Languages → Add Language
- Japanese (ja) — default/primary
- English (en)
- Indonesian (id)
- Arabic (ar) — RTL ✓ enabled automatically
- Malay (ms)
```

### Translation Workflow
1. **Automatic translation**: WPML + DeepL API or Google Translate API
   - WPML → Translation Management → Automatic Translation
   - Recommended: DeepL for JP↔EN, Google for ID/AR/MS
2. **Manual review**: Always review auto-translated product pages
3. **Arabic (RTL)**: The theme automatically loads `rtl.css` for Arabic

### URL Structure (SEO-friendly)
```
WPML → Languages → Language URL Format
- Recommended: /en/product/halal-chicken
                /id/product/halal-chicken
                /ar/product/halal-chicken (RTL)
```

### Currency per Language
```
WooCommerce Multilingual → Multi-currency
- Japanese (JA): JPY ¥
- English (EN): JPY ¥ or USD $
- Indonesian (ID): IDR Rp or JPY ¥
- Arabic (AR): JPY ¥ or SAR ر.س
- Malay (MS): JPY ¥ or MYR RM
```

---

## 11. SEO STRATEGY

### Rank Math Settings
```
Rank Math → General Settings → Breadcrumbs → Enable
Rank Math → Sitemap → Enable all post types
Rank Math → Schema → WooCommerce Product Schema: ✓
```

### Target Keywords
| Language | Primary Keywords |
|---|---|
| Japanese | ハラール食品, ハラールショップ, ムスリムフード, ハラール認証食品 |
| English | halal food japan, halal shop japan, muslim food delivery japan |
| Indonesian | belanja halal di jepang, makanan halal jepang, toko halal jepang |
| Arabic | طعام حلال في اليابان, متجر حلال اليابان |
| Malay | makanan halal di jepang, kedai halal jepang |

### Meta Title Templates (Rank Math)
- Shop: `ハラール食品オンラインショップ | %sitename%`
- Product: `%title% | ハラール認証 | %sitename%`
- Category: `%term% | ハラール食品 | %sitename%`

### Google Search Console
1. Verify site at search.google.com/search-console
2. Submit sitemaps:
   - `https://yourdomain.com/sitemap_index.xml`
   - Submit all language versions

---

## 12. PERFORMANCE OPTIMIZATION

### Cloudflare (Free CDN)
1. Sign up at cloudflare.com
2. Add your domain, update nameservers
3. Enable:
   - Auto-Minify: CSS, JS, HTML
   - Brotli compression
   - Browser Cache TTL: 1 month
   - Rocket Loader: OFF (can conflict with WooCommerce)
   - Image optimization (Polish): ON

### Image Optimization
- Install **ShortPixel** or **Imagify** plugin
- Convert all images to WebP format
- Lazy load all product images (already in theme)
- Recommended product image size: 800×800px, <150KB

### Core Web Vitals Checklist
- [ ] LCP < 2.5s (use CDN + image optimization)
- [ ] FID < 100ms (minimize JS)
- [ ] CLS < 0.1 (specify image dimensions)
- [ ] Mobile PageSpeed Score > 80

---

## 13. SECURITY HARDENING

### Wordfence Configuration
```
Wordfence → Firewall → Optimize Firewall (extended protection)
Wordfence → Login Security → Enable 2FA for admin accounts
Wordfence → Brute Force Protection:
  - Lock out after 5 failed attempts
  - Lock out for 60 minutes
```

### Additional Security
```php
// Add to wp-config.php
define('DISALLOW_FILE_EDIT', true);     // Disable theme/plugin editor
define('DISALLOW_FILE_MODS', false);    // Allow plugin updates
define('WP_AUTO_UPDATE_CORE', 'minor'); // Auto minor updates

// Limit login attempts plugin
// Hide WP version
// Change default admin username (never use 'admin')
```

### Recommended Security Plugins
- **Wordfence** (firewall + malware scan)
- **WPS Hide Login** (change /wp-admin URL)
- **Limit Login Attempts Reloaded** (brute force)

---

## 14. LAUNCH CHECKLIST

### Pre-launch
- [ ] SSL certificate active (HTTPS everywhere)
- [ ] All required pages created with correct templates
- [ ] Navigation menus assigned
- [ ] WooCommerce: tax, shipping, payments configured
- [ ] Test order placed (use Stripe test mode)
- [ ] All products have halal certification data filled
- [ ] Logo uploaded (SVG or PNG, transparent background)
- [ ] Favicon uploaded (512×512 PNG)
- [ ] Privacy Policy page complete (required for GDPR/Japan law)
- [ ] 特定商取引法 page complete (required for Japan e-commerce law)
- [ ] Contact form tested
- [ ] 404 page verified
- [ ] Mobile layout tested on real devices

### SEO Pre-launch
- [ ] Rank Math sitemap generated and submitted
- [ ] Google Search Console connected
- [ ] Google Analytics 4 connected (use GA4 plugin or Site Kit)
- [ ] All product pages have unique meta descriptions
- [ ] All product images have alt text

### Post-launch
- [ ] Monitor Core Web Vitals in Search Console
- [ ] Set up weekly Wordfence malware scan
- [ ] Enable automatic WordPress/plugin updates (minor only)
- [ ] Set up daily database backups (UpdraftPlus → Google Drive)

---

## 15. SALES-BOOSTING FEATURES

### Recommended Additional Plugins
| Plugin | Purpose | Cost |
|---|---|---|
| YITH WooCommerce Wishlist | Wishlist feature | Free/Paid |
| WooCommerce Product Bundles | Bundle deals | $49/year |
| AutomateWoo | Email marketing automation | $99/year |
| WooCommerce Points & Rewards | Loyalty program | $129/year |
| YITH WooCommerce Review Reminder | Auto review requests | Free |
| TrustPulse | Social proof notifications | $5/month |

### Trust-Building Features for Muslim Customers
1. **Halal Certificate Downloads** — Add PDF download link for each certificate
2. **Ingredient Checker Tool** — Search ingredients database
3. **Video Explanations** — YouTube embed on product pages showing halal process
4. **Live Chat** — Tidio or Crisp with multilingual support
5. **Community Reviews** — Filter reviews by country/language
6. **Sourcing Map** — Show where products come from

---

## 16. SUPPORT & MAINTENANCE

### Monthly Tasks
- Update WordPress core, themes, and plugins
- Review security scan reports
- Check Core Web Vitals in Search Console
- Review and respond to customer reviews
- Update halal certificate expiry dates
- Monitor stock levels

### Backup Strategy
```
UpdraftPlus → Backup Schedule:
- Files: Weekly
- Database: Daily
- Remote storage: Google Drive or Dropbox
- Keep: 4 weeks of backups
```

---

## THEME FILE STRUCTURE

```
halal-shop-pro/
├── style.css                    # Theme declaration
├── functions.php                # Main functions
├── index.php                    # Fallback template
├── front-page.php               # Homepage
├── page.php                     # Generic page
├── single.php                   # Blog post
├── archive.php                  # Archives
├── search.php                   # Search results
├── 404.php                      # Not found
├── header.php                   # Site header
├── footer.php                   # Site footer
├── inc/
│   ├── theme-setup.php          # Theme supports, menus, image sizes
│   ├── enqueue.php              # CSS/JS enqueue
│   ├── woocommerce.php          # WooCommerce hooks
│   ├── halal-meta.php           # Halal certification meta fields
│   ├── customizer.php           # Customizer settings
│   └── widgets.php              # Widget areas
├── template-parts/
│   ├── hero-banner.php          # Homepage hero
│   ├── trust-badges.php         # Trust features strip
│   ├── product-categories.php   # Categories grid
│   ├── featured-products.php    # Featured products
│   ├── halal-info.php           # Halal information section
│   └── testimonials.php         # Customer reviews
├── page-templates/
│   ├── template-about.php       # About Us page
│   ├── template-contact.php     # Contact page
│   ├── template-faq.php         # FAQ page
│   └── template-halal-cert.php  # Halal certification page
├── woocommerce/
│   ├── archive-product.php      # Shop/category pages
│   └── single-product.php       # Product detail page
├── assets/
│   ├── css/
│   │   ├── main.css             # All theme styles
│   │   └── rtl.css              # Arabic RTL overrides
│   └── js/
│       └── main.js              # All theme JavaScript
└── languages/                   # Translation files (.po/.mo)
```

---

*Built with ❤️ for the Muslim community in Japan and worldwide.*
