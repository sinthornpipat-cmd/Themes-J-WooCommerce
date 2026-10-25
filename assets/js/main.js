(function () {
  'use strict';
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

  // Mobile menu + search
  var mt = $('.menu-toggle'), nav = $('.main-nav');
  if (mt && nav) mt.addEventListener('click', function () {
    var o = nav.classList.toggle('open'); mt.setAttribute('aria-expanded', o);
  });
  var st = $('.search-toggle'), sp = $('.search-panel');
  if (st && sp) st.addEventListener('click', function () {
    var o = sp.classList.toggle('open'); st.setAttribute('aria-expanded', o);
    if (o) { var i = $('input', sp); i && i.focus(); }
  });

  // Hero slider
  var track = $('.hero-track');
  if (track) {
    var slides = $$('.hero-slide', track), dotsBox = $('.hero-dots'), idx = 0, timer;
    if (slides.length > 1 && dotsBox) {
      slides.forEach(function (_, i) {
        var b = document.createElement('button');
        b.setAttribute('aria-label', ((window.jshopData && jshopData.slide) || 'Slide %d').replace('%d', i + 1));
        b.addEventListener('click', function () { go(i); restart(); });
        dotsBox.appendChild(b);
      });
      var dots = $$('button', dotsBox);
      var go = function (i) {
        idx = (i + slides.length) % slides.length;
        track.scrollTo({ left: slides[idx].offsetLeft, behavior: 'smooth' });
        mark();
      };
      var mark = function () { dots.forEach(function (d, i) { d.classList.toggle('active', i === idx); }); };
      var restart = function () {
        clearInterval(timer);
        if (!matchMedia('(prefers-reduced-motion: reduce)').matches) timer = setInterval(function () { go(idx + 1); }, 5000);
      };
      track.addEventListener('scroll', function () {
        var n = Math.round(track.scrollLeft / track.clientWidth);
        if (n !== idx) { idx = n; mark(); }
      }, { passive: true });
      track.addEventListener('pointerdown', function () { clearInterval(timer); });
      mark(); restart();
    }
  }

  // Promo popup (shows once per session)
  var promo = $('.promo');
  if (promo && window.jshopData && jshopData.popupEnabled && !sessionStorage.getItem('jshop_promo')) {
    var left = Math.max(1, jshopData.popupMinutes) * 60, clock = $('.promo-time', promo);
    var fmt = function (s) { return ('0' + Math.floor(s / 60)).slice(-2) + ':' + ('0' + (s % 60)).slice(-2); };
    setTimeout(function () {
      promo.classList.add('show'); clock.textContent = fmt(left);
      var t = setInterval(function () {
        left--; clock.textContent = fmt(Math.max(left, 0));
        if (left <= 0) { clearInterval(t); promo.classList.remove('show'); }
      }, 1000);
    }, 4000);
    var close = function () { promo.classList.remove('show'); sessionStorage.setItem('jshop_promo', '1'); };
    $('.promo-close', promo).addEventListener('click', close);
    promo.addEventListener('click', function (e) { if (e.target === promo) close(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
    var cb = $('.promo-code button', promo);
    cb.addEventListener('click', function () {
      var code = $('.promo-code span', promo).textContent.trim();
      var done = function () { cb.textContent = jshopData.copied; setTimeout(function () { cb.textContent = jshopData.copy; }, 1800); };
      if (navigator.clipboard) navigator.clipboard.writeText(code).then(done);
      else { var r = document.createRange(); r.selectNode($('.promo-code span', promo)); getSelection().removeAllRanges(); getSelection().addRange(r); document.execCommand('copy'); done(); }
    });
  }
})();
