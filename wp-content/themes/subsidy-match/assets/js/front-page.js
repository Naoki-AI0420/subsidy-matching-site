/**
 * フロントページ — カウントアップアニメーション + FAQ アコーディオン
 */
(function () {
  'use strict';

  /* =====================
     カウントアップアニメーション
     ===================== */
  function animateCounters() {
    var counters = document.querySelectorAll('.stat-number[data-target]');
    if (!counters.length) return;

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var el = entry.target;
        if (el.dataset.animated) return;
        el.dataset.animated = '1';

        var target = parseInt(el.dataset.target, 10);
        var duration = 1600; // ms
        var start = 0;
        var startTime = null;

        function step(timestamp) {
          if (!startTime) startTime = timestamp;
          var progress = Math.min((timestamp - startTime) / duration, 1);
          // ease-out quad
          var eased = 1 - (1 - progress) * (1 - progress);
          var current = Math.floor(eased * target);
          el.textContent = current.toLocaleString();
          if (progress < 1) {
            requestAnimationFrame(step);
          } else {
            el.textContent = target.toLocaleString();
          }
        }

        requestAnimationFrame(step);
        observer.unobserve(el);
      });
    }, { threshold: 0.3 });

    counters.forEach(function (el) {
      observer.observe(el);
    });
  }

  /* =====================
     FAQ アコーディオン
     ===================== */
  function initFAQ() {
    var items = document.querySelectorAll('.faq-item');
    items.forEach(function (item) {
      var btn = item.querySelector('.faq-question');
      if (!btn) return;
      btn.addEventListener('click', function () {
        var isOpen = item.classList.contains('is-open');
        // 他を閉じる
        items.forEach(function (other) {
          other.classList.remove('is-open');
          var otherBtn = other.querySelector('.faq-question');
          if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
        });
        if (!isOpen) {
          item.classList.add('is-open');
          btn.setAttribute('aria-expanded', 'true');
        }
      });
    });
  }

  /* =====================
     Init
     ===================== */
  document.addEventListener('DOMContentLoaded', function () {
    animateCounters();
    initFAQ();
  });
})();
