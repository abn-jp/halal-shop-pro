/**
 * Halal Shop Pro — Main JavaScript
 */
(function ($) {
  'use strict';

  const HS = window.halalShop || {};

  /* ── Sticky Header ─────────────────────────── */
  const header = document.getElementById('siteHeader');
  const backTop = document.getElementById('backToTop');

  window.addEventListener('scroll', () => {
    const y = window.scrollY;
    if (header) header.classList.toggle('scrolled', y > 80);
    if (backTop) backTop.classList.toggle('visible', y > 400);
  }, { passive: true });

  /* ── Back to Top ────────────────────────────── */
  if (backTop) {
    backTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  }

  /* ── Mobile Menu ────────────────────────────── */
  const menuToggle   = document.getElementById('menuToggle');
  const mobileMenu   = document.getElementById('mobileMenu');
  const mobileClose  = document.getElementById('mobileClose');
  const mobileOverlay = document.getElementById('mobileOverlay');

  function openMobileMenu() {
    mobileMenu && mobileMenu.classList.add('open');
    mobileOverlay && mobileOverlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeMobileMenu() {
    mobileMenu && mobileMenu.classList.remove('open');
    mobileOverlay && mobileOverlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  menuToggle && menuToggle.addEventListener('click', openMobileMenu);
  mobileClose && mobileClose.addEventListener('click', closeMobileMenu);
  mobileOverlay && mobileOverlay.addEventListener('click', closeMobileMenu);

  /* ── Cart Drawer ────────────────────────────── */
  const cartToggle  = document.getElementById('cartToggle');
  const cartDrawer  = document.getElementById('cartDrawer');
  const cartClose   = document.getElementById('cartClose');
  const cartOverlay = document.getElementById('cartOverlay');

  function openCart() {
    cartDrawer && cartDrawer.classList.add('open');
    cartOverlay && cartOverlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeCart() {
    cartDrawer && cartDrawer.classList.remove('open');
    cartOverlay && cartOverlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  cartToggle && cartToggle.addEventListener('click', openCart);
  cartClose && cartClose.addEventListener('click', closeCart);
  cartOverlay && cartOverlay.addEventListener('click', closeCart);

  /* ── Language Switcher ──────────────────────── */
  document.querySelectorAll('.language-switcher').forEach(switcher => {
    const btn      = switcher.querySelector('.language-switcher__btn');
    const dropdown = switcher.querySelector('.language-switcher__dropdown');

    btn && btn.addEventListener('click', e => {
      e.stopPropagation();
      dropdown.classList.toggle('open');
    });
  });

  document.addEventListener('click', () => {
    document.querySelectorAll('.language-switcher__dropdown.open').forEach(d => d.classList.remove('open'));
  });

  /* ── FAQ Accordion ──────────────────────────── */
  document.querySelectorAll('.faq-question').forEach(btn => {
    btn.addEventListener('click', () => {
      const item    = btn.closest('.faq-item');
      const answer  = btn.nextElementSibling;
      const isOpen  = btn.classList.contains('active');

      // Close all
      document.querySelectorAll('.faq-question.active').forEach(b => {
        b.classList.remove('active');
        b.setAttribute('aria-expanded', 'false');
        b.nextElementSibling && b.nextElementSibling.classList.remove('open');
      });

      // Toggle this
      if (!isOpen) {
        btn.classList.add('active');
        btn.setAttribute('aria-expanded', 'true');
        answer && answer.classList.add('open');
      }
    });
  });

  /* ── Add to Cart (AJAX) ─────────────────────── */
  document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const productId  = this.dataset.productId;
      const productUrl = this.dataset.productUrl;
      const originalHTML = this.innerHTML;

      if (!productId) { window.location = productUrl; return; }

      this.innerHTML = '<span class="loading-spinner"></span>';
      this.disabled  = true;

      $.ajax({
        url: HS.ajaxUrl || '/wp-admin/admin-ajax.php',
        type: 'POST',
        data: {
          action:     'woocommerce_ajax_add_to_cart',
          product_id: productId,
          quantity:   1,
          nonce:      HS.nonce,
        },
        success: (response) => {
          if (response.error) {
            window.location = productUrl;
          } else {
            // Update cart count
            const count = response.fragments && response.fragments['.cart-count'];
            if (count) {
              document.querySelectorAll('.cart-count').forEach(el => {
                const tmp = document.createElement('div');
                tmp.innerHTML = count;
                el.textContent = tmp.querySelector('.cart-count') ? tmp.querySelector('.cart-count').textContent : el.textContent;
              });
            }
            // Flash success
            this.innerHTML = '✓ ' + (HS.i18n && HS.i18n.addedToCart ? HS.i18n.addedToCart : 'Added!');
            this.style.background = '#28a745';
            setTimeout(() => {
              this.innerHTML = originalHTML;
              this.style.background = '';
              this.disabled = false;
              openCart();
            }, 1500);
          }
        },
        error: () => { window.location = productUrl; },
      });
    });
  });

  /* ── Newsletter Form ────────────────────────── */
  const newsForm = document.getElementById('newsletterForm');
  if (newsForm) {
    newsForm.addEventListener('submit', e => {
      e.preventDefault();
      const email   = newsForm.querySelector('input[type="email"]').value;
      const submitBtn = newsForm.querySelector('button[type="submit"]');
      const orig    = submitBtn.textContent;

      submitBtn.textContent = '...';
      submitBtn.disabled = true;

      // Integrate with your email service via AJAX or Mailchimp API
      setTimeout(() => {
        newsForm.innerHTML = '<p style="color:#fff;font-size:1.125rem;font-weight:600">✅ ' + (HS.i18n && HS.i18n.subscribe ? HS.i18n.subscribe : 'Thank you for subscribing!') + '</p>';
      }, 800);
    });
  }

  /* ── Smooth scroll to anchors ───────────────── */
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  /* ── Animate on scroll (simple IntersectionObserver) ── */
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate-in');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.product-card, .category-card, .testimonial-card, .trust-item').forEach(el => {
      observer.observe(el);
    });
  }

  /* ── Keyboard accessibility ─────────────────── */
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      closeMobileMenu();
      closeCart();
      document.querySelectorAll('.language-switcher__dropdown.open').forEach(d => d.classList.remove('open'));
    }
  });

  /* ── WooCommerce quantity input fix ─────────── */
  $(document).on('change', 'input.qty', function () {
    const val = parseInt(this.value, 10);
    const min = parseInt(this.getAttribute('min') || '1', 10);
    const max = parseInt(this.getAttribute('max') || '9999', 10);
    if (isNaN(val) || val < min) this.value = min;
    if (val > max) this.value = max;
  });

  /* ── Product image lazy load ────────────────── */
  if ('loading' in HTMLImageElement.prototype) {
    document.querySelectorAll('img[data-src]').forEach(img => {
      img.src = img.dataset.src;
    });
  }

})(jQuery);
