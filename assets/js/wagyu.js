/* Halal Wagyu — Quick View, Card Animations, Interactions */
/* global halalShop, jQuery */
(function ($) {
    'use strict';

    var overlay   = document.getElementById('wagyuModalOverlay');
    var modalBody = document.getElementById('wagyuModalBody');
    var closeBtn  = document.getElementById('wagyuModalClose');
    var lastFocus = null;

    function openModal() {
        if (!overlay) return;
        lastFocus = document.activeElement;
        overlay.classList.add('open');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        if (closeBtn) closeBtn.focus();
    }

    function closeModal() {
        if (!overlay) return;
        overlay.classList.remove('open');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (modalBody) {
            modalBody.innerHTML = '<div class="wagyu-modal-loading"><div class="loading-spinner"></div></div>';
        }
        if (lastFocus) lastFocus.focus();
    }

    if (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeModal();
        });
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && overlay.classList.contains('open')) closeModal();
        });
        overlay.addEventListener('keydown', function (e) {
            if (e.key !== 'Tab') return;
            var focusable = overlay.querySelectorAll(
                'a[href], button:not([disabled]), input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            if (!focusable.length) return;
            var first = focusable[0];
            var last  = focusable[focusable.length - 1];
            if (e.shiftKey) {
                if (document.activeElement === first) { e.preventDefault(); last.focus(); }
            } else {
                if (document.activeElement === last)  { e.preventDefault(); first.focus(); }
            }
        });
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.wagyu-quick-view-btn');
        if (!btn) return;
        e.preventDefault();
        var productId = btn.getAttribute('data-product-id');
        if (!productId || !overlay) return;
        openModal();
        $.ajax({
            url:  halalShop.ajaxUrl,
            type: 'POST',
            data: { action: 'halal_wagyu_quick_view', nonce: halalShop.nonce, product_id: productId },
            success: function (res) {
                if (res.success && modalBody) {
                    modalBody.innerHTML = res.data.html;
                    $(document.body).trigger('wc_fragment_refresh');
                    var firstBtn = modalBody.querySelector('a, button, input');
                    if (firstBtn) firstBtn.focus();
                }
            },
            error: function () {
                if (modalBody) {
                    modalBody.innerHTML = '<p style="text-align:center;padding:2rem;color:#666;">読み込みに失敗しました。もう一度お試しください。<br>Failed to load. Please try again.</p>';
                }
            },
        });
    });

    var cards = document.querySelectorAll('.wagyu-product-card');
    if (cards.length && 'IntersectionObserver' in window) {
        cards.forEach(function (card, i) {
            card.style.opacity   = '0';
            card.style.transform = 'translateY(22px)';
            card.style.transition =
                'opacity .4s ease ' + (i % 4) * 0.07 + 's, ' +
                'transform .4s ease ' + (i % 4) * 0.07 + 's, ' +
                'box-shadow .25s ease, border-color .25s ease';
        });
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.style.opacity   = '1';
                    entry.target.style.transform = 'translateY(0)';
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });
        cards.forEach(function (card) { io.observe(card); });
    }

    function markActiveFromParams() {
        var params = new URLSearchParams(window.location.search);
        var grade  = params.get('wagyu_grade');
        var cut    = params.get('wagyu_cut');
        if (grade) {
            document.querySelectorAll('.wagyu-grade-filter-btn').forEach(function (btn) {
                if ((btn.getAttribute('href') || '').indexOf('wagyu_grade=' + grade) !== -1) btn.classList.add('active');
            });
        }
        if (cut) {
            document.querySelectorAll('.wagyu-cut-list li a').forEach(function (link) {
                if ((link.getAttribute('href') || '').indexOf('wagyu_cut=' + cut) !== -1) link.classList.add('active');
            });
        }
    }
    if (typeof URLSearchParams !== 'undefined') markActiveFromParams();

    var filterBoxTitles = document.querySelectorAll('.wagyu-filter-box__title');
    if (window.innerWidth <= 640) {
        filterBoxTitles.forEach(function (title) {
            var box  = title.parentElement;
            var body = box.querySelector('.wagyu-grade-filters, .wagyu-cut-list, .wagyu-cert-seal');
            if (!body) return;
            title.style.cursor = 'pointer';
            title.setAttribute('role', 'button');
            title.setAttribute('aria-expanded', 'false');
            body.style.display = 'none';
            title.addEventListener('click', function () {
                var expanded = title.getAttribute('aria-expanded') === 'true';
                title.setAttribute('aria-expanded', String(!expanded));
                body.style.display = expanded ? 'none' : '';
            });
        });
    }

})(jQuery);
