(function ($) {
  'use strict';
  var drawer = document.getElementById('mini-cart');
  var overlay = document.querySelector('.mc-overlay');
  var lastFocus = null;

  function open() {
    if (!drawer) return;
    lastFocus = document.activeElement;
    drawer.classList.add('open'); drawer.setAttribute('aria-hidden', 'false');
    if (overlay) { overlay.hidden = false; overlay.classList.add('show'); }
    document.documentElement.classList.add('mc-lock');
    var c = drawer.querySelector('.mc-close'); c && c.focus();
  }
  function close() {
    if (!drawer) return;
    drawer.classList.remove('open'); drawer.setAttribute('aria-hidden', 'true');
    if (overlay) { overlay.classList.remove('show'); setTimeout(function () { overlay.hidden = true; }, 250); }
    document.documentElement.classList.remove('mc-lock');
    if (lastFocus) lastFocus.focus();
  }

  // Header cart icon opens drawer (except on cart / checkout)
  $(document).on('click', '.cart-toggle', function (e) {
    if (window.DPSW && DPSW.isCart) return;
    if (!drawer) return;
    e.preventDefault(); open();
  });
  $(document).on('click', '.mc-close, .mc-overlay', close);
  $(document).on('keydown', function (e) { if (e.key === 'Escape') close(); });

  // Open after ajax add-to-cart
  $(document.body).on('added_to_cart', function () {
    if (window.DPSW && DPSW.openOnAdd) open();
  });

  // Quantity +/- buttons
  $(document).on('click', '.qty-btn', function () {
    var $q = $(this).closest('.quantity').find('input.qty');
    if (!$q.length) return;
    var step = parseFloat($q.attr('step')) || 1, min = parseFloat($q.attr('min')),
        max = parseFloat($q.attr('max')), v = parseFloat($q.val()) || 0;
    v += step * parseInt($(this).data('d'), 10);
    if (!isNaN(min) && v < min) v = min;
    if (!isNaN(max) && max > 0 && v > max) v = max;
    $q.val(v).trigger('change');
  });

  /* ===== Mini-cart: quantity, remove, coupon, live totals ===== */
  var timers = {};

  function money(n) {
    var c = (window.DPSW && DPSW.cur) || { sym: '฿', dec: 2, dsep: '.', tsep: ',', fmt: '%1$s%2$s' };
    var neg = n < 0; n = Math.abs(n);
    var parts = n.toFixed(c.dec).split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, c.tsep);
    var num = parts.join(c.dsep);
    var out = c.fmt.replace('%1$s', c.sym).replace('%2$s', num);
    return '<span class="woocommerce-Price-amount amount"><bdi>' + (neg ? '−' : '') + out + '</bdi></span>';
  }

  function toast(msg) {
    if (!drawer || !msg) return;
    var t = document.createElement('div');
    t.className = 'mc-toast'; t.setAttribute('role', 'status'); t.textContent = msg;
    drawer.appendChild(t);
    setTimeout(function () { t.classList.add('out'); }, 2600);
    setTimeout(function () { t.remove(); }, 3000);
  }

  // Prefer the light ?wc-ajax= endpoint; fall back to admin-ajax.php.
  function post(action, data) {
    var url = DPSW.wcBase ? DPSW.wcBase.replace('%%endpoint%%', action) : DPSW.ajax;
    return $.post(url, $.extend({ action: action, nonce: DPSW.nonce }, data));
  }

  function applyFragments(res) {
    if (!res || !res.success) return false;
    $.each(res.data.fragments || {}, function (sel, html) { $(sel).replaceWith(html); });
    $(document.body).trigger('wc_fragments_refreshed');
    return true;
  }

  function sendQty($item, qty) {
    var key = $item.data('key');
    $item.addClass('is-loading'); $(drawer).addClass('mc-busy');
    post('dps_cart_qty', { key: key, qty: qty }).done(function (res) {
      if (applyFragments(res)) {
        if (res.data && res.data.capped) toast(DPSW.i18n.max);
      } else { toast((res && res.data && res.data.message) || DPSW.i18n.err); $item.removeClass('is-loading'); }
    }).fail(function () { toast(DPSW.i18n.err); $item.removeClass('is-loading'); })
      .always(function () { $(drawer).removeClass('mc-busy'); });
  }

  // Optimistic update so numbers move instantly; server response then replaces everything
  function optimistic($item, qty) {
    var unit = parseFloat($item.data('unit')) || 0, $foot = $('.mc-foot');
    var $line = $item.find('.mc-line'), oldLine = parseFloat($line.data('raw'));
    if (isNaN(oldLine)) oldLine = unit * (parseInt($item.data('qty'), 10) || 1);
    var newLine = unit * qty, diff = newLine - oldLine;
    $line.html(money(newLine)).data('raw', newLine);
    $item.data('qty', qty);
    var sub = parseFloat($foot.attr('data-sub')) + diff, est = parseFloat($foot.attr('data-est')) + diff;
    $foot.attr('data-sub', sub).attr('data-est', est);
    $foot.find('.mc-sub-val').html(money(sub)); $foot.find('.mc-est-val').html(money(est));
  }

  function setQty($item, qty) {
    var $in = $item.find('.mc-qty-input'), max = parseInt($in.attr('max'), 10);
    qty = parseInt(qty, 10); if (isNaN(qty) || qty < 1) qty = 1;
    if (!isNaN(max) && max > 0 && qty > max) { qty = max; toast(DPSW.i18n.max); }
    $in.val(qty);
    var cur = parseInt($item.data('qty'), 10);
    if (isNaN(cur)) cur = qty;
    optimistic($item, qty);
    clearTimeout(timers[$item.data('key')]);
    timers[$item.data('key')] = setTimeout(function () { sendQty($item, qty); }, 350);
  }

  $(document).on('click', '.mc-qty-btn', function () {
    var $item = $(this).closest('.mc-item'), $in = $item.find('.mc-qty-input');
    if ($item.data('qty') === undefined) $item.data('qty', parseInt($in.val(), 10) || 1);
    setQty($item, (parseInt($in.val(), 10) || 1) + parseInt($(this).data('d'), 10));
  });
  $(document).on('focus', '.mc-qty-input', function () {
    var $item = $(this).closest('.mc-item');
    if ($item.data('qty') === undefined) $item.data('qty', parseInt(this.value, 10) || 1);
  });
  $(document).on('change', '.mc-qty-input', function () { setQty($(this).closest('.mc-item'), this.value); });
  $(document).on('keydown', '.mc-qty-input', function (e) { if (e.key === 'Enter') { e.preventDefault(); $(this).trigger('change'); } });

  $(document).on('click', '.mc-remove', function (e) {
    if (!DPSW.ajax) return;
    e.preventDefault();
    sendQty($(this).closest('.mc-item'), 0);
  });

  // Coupon
  $(document).on('click', '.mc-coupon-toggle', function () {
    var $f = $('.mc-coupon-form'), open = $f.prop('hidden');
    $f.prop('hidden', !open); $(this).attr('aria-expanded', open);
    if (open) $f.find('input').trigger('focus');
  });
  $(document).on('submit', '.mc-coupon-form', function (e) {
    e.preventDefault();
    var $f = $(this), code = $.trim($f.find('input').val());
    if (!code) return;
    $f.addClass('is-loading');
    post('dps_cart_coupon', { code: code, op: 'apply' }).done(function (res) {
      if (applyFragments(res)) toast(DPSW.i18n.coupon);
      else { toast((res && res.data && res.data.message) || DPSW.i18n.err); $f.removeClass('is-loading'); }
    }).fail(function () { toast(DPSW.i18n.err); $f.removeClass('is-loading'); });
  });
  $(document).on('click', '.mc-chip-x', function () {
    var $c = $(this).closest('.mc-chip').addClass('is-loading');
    post('dps_cart_coupon', { code: $(this).data('coupon'), op: 'remove' }).done(applyFragments)
      .fail(function () { toast(DPSW.i18n.err); $c.removeClass('is-loading'); });
  });

  /* ===== Cart page: auto-update on quantity change ===== */
  var cartTimer;
  $(document).on('change', '.woocommerce-cart-form input.qty', function () {
    clearTimeout(cartTimer);
    cartTimer = setTimeout(function () {
      var $b = $('.woocommerce-cart-form [name="update_cart"]');
      $b.prop('disabled', false).attr('aria-disabled', 'false').trigger('click');
    }, 600);
  });

  /* ===== Add to cart (shop loops + single product): open the drawer instantly, one request ===== */
  function setLoading(on) {
    if (drawer) drawer.classList.toggle('mc-loading', on);
  }

  // Send the add-to-cart request; the drawer is already open and shows a loading state.
  function addToCart(data, fallbackUrl, done) {
    open();
    setLoading(true);
    fetch(DPSW.wcAjax, { method: 'POST', body: data, credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res && res.success) {
          applyFragments(res);
          setLoading(false);
        } else {
          setLoading(false);
          var d = (res && res.data) || {};
          toast(d.message || DPSW.i18n.err);
          if (d.product_url) { setTimeout(function () { window.location.href = d.product_url; }, 1200); }
        }
      })
      .catch(function () { setLoading(false); if (fallbackUrl) { window.location.href = fallbackUrl; } })
      .then(function () { if (done) { done(); } });
  }

  // Shop / home loops
  document.addEventListener('click', function (e) {
    var a = e.target.closest ? e.target.closest('a.add_to_cart_button') : null;
    if (!a || !drawer || !window.fetch || !window.DPSW || !DPSW.wcAjax) return;
    if (!/product_type_simple/.test(a.className) || a.classList.contains('disabled')) return; // variable/external → normal link
    var pid = a.getAttribute('data-product_id');
    if (!pid) return;
    e.preventDefault();
    e.stopImmediatePropagation();
    if (a.classList.contains('loading')) return;
    a.classList.add('loading');
    var body = new URLSearchParams();
    body.set('product_id', pid);
    body.set('quantity', a.getAttribute('data-quantity') || '1');
    addToCart(body, a.getAttribute('href'), function () { a.classList.remove('loading'); });
  }, true);

  // Single product page (simple + variable)
  $(document).on('submit', 'form.cart', function (e) {
    var form = this, $form = $(form);
    if ($form.hasClass('grouped_form') || !window.fetch || !drawer || !window.DPSW || !DPSW.wcAjax) return;
    var $btn = $form.find('.single_add_to_cart_button');
    if ($btn.is('.disabled, :disabled') || $btn.hasClass('wc-variation-selection-needed')) return;
    e.preventDefault();
    var data = new FormData(form);
    if ($btn.attr('name') && !data.has($btn.attr('name'))) data.append($btn.attr('name'), $btn.val() || '');
    $btn.addClass('loading');
    addToCart(data, window.location.href, function () { $btn.removeClass('loading'); });
  });

  // Sticky add-to-cart bar
  var bar = document.querySelector('.sticky-atc');
  var form = document.querySelector('form.cart');
  if (bar && form && 'IntersectionObserver' in window) {
    new IntersectionObserver(function (en) {
      var out = !en[0].isIntersecting && en[0].boundingClientRect.top < 0;
      bar.classList.toggle('show', out);
    }).observe(form);
    var btn = bar.querySelector('.sa-btn');
    btn.addEventListener('click', function () {
      var single = form.querySelector('button.single_add_to_cart_button');
      var variable = form.classList.contains('variations_form');
      if (!variable && single) single.click();
      else form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
  }
  /* ===== Checkout: inline coupon in the order summary ===== */
  /**
   * Apply a coupon from the checkout summary, then refresh the order review.
   */
  function applyCheckoutCoupon() {
    var $in = $('#dps-co-coupon-code'), code = $.trim($in.val()), $b = $('.dps-co-coupon-btn');
    if (!code || $b.prop('disabled')) return;
    $b.prop('disabled', true);
    post('dps_cart_coupon', { code: code, op: 'apply' }).done(function (res) {
      if (res && res.success) { $in.val(''); toast(DPSW.i18n.coupon); $(document.body).trigger('update_checkout'); }
      else { toast((res && res.data && res.data.message) || DPSW.i18n.err); }
    }).fail(function () { toast(DPSW.i18n.err); }).always(function () { $b.prop('disabled', false); });
  }
  $(document).on('click', '.dps-co-coupon-btn', applyCheckoutCoupon);
  $(document).on('keydown', '#dps-co-coupon-code', function (e) {
    if (e.key === 'Enter') { e.preventDefault(); applyCheckoutCoupon(); }
  });
})(jQuery);
