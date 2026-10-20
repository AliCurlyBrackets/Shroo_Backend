/* ===== Shroo RadiantCharm — سلوك المتجر =====
   كل محتوى الصفحات مكتوب في HTML مباشرة.
   الملف ده مسؤول عن التفاعل بس: السلة، المفضلة، السلايدر، الفلاتر، المعرض، والدفع.
   أي عنصر بيتكرر (سطر في السلة مثلاً) شكله موجود في HTML جوه <template>.
*/
(() => {
  'use strict';

  /* ---------- Helpers ---------- */
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const page = document.body.dataset.page;
  const LOC = 'ar-u-nu-latn-ca-gregory';
  const FREE_SHIPPING_MIN = (window.SHR && window.SHR.freeShippingThreshold !== undefined && window.SHR.freeShippingThreshold !== false) ? window.SHR.freeShippingThreshold : 150;
  const SHIPPING_FEE = (window.SHR && window.SHR.shippingFee) || 25;
  const COUPONS = { SHROO10: 0.10, BEAUTY15: 0.15 };
  const CURRENCY = (window.SHR && window.SHR.currency) || { symbol: 'ر.س', position: 'right' };

  const money = n => {
    const formatted = (Math.round(n * 100) / 100).toLocaleString('en-US', { minimumFractionDigits: n % 1 ? 2 : 0, maximumFractionDigits: 2 });
    return CURRENCY.position === 'left' ? CURRENCY.symbol + formatted : formatted + ' ' + CURRENCY.symbol;
  };
  const plural = (n, one, two, many) => n === 1 ? one : n === 2 ? two : n >= 3 && n <= 10 ? `${n} ${many}` : `${n} ${one}`;
  const items = n => plural(n, 'منتج واحد', 'منتجان', 'منتجات');
  const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const tpl = id => document.getElementById(id).content.firstElementChild.cloneNode(true);

  const store = {
    get(k, d) { try { const v = localStorage.getItem(k); return v ? JSON.parse(v) : d; } catch (e) { return d; } },
    set(k, v) { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) { /* storage unavailable */ } }
  };

  /* reads the product data written on the element as data-* attributes */
  const productFrom = el => ({
    id: el.dataset.add || el.dataset.id || el.value,
    name: el.dataset.name,
    sub: el.dataset.sub || '',
    size: el.dataset.size || '',
    img: el.dataset.img,
    price: +el.dataset.price
  });

  /* ---------- Toast ---------- */
  let toastT;
  function toast(html) {
    const t = $('.toast'); if (!t) return;
    t.innerHTML = '<svg class="ic ic-check" aria-hidden="true"><use href="#i-check"></use></svg><span>' + html + '</span>';
    t.classList.add('show');
    clearTimeout(toastT);
    toastT = setTimeout(() => t.classList.remove('show'), 2400);
  }

  /* ---------- Cart ---------- */
  const Cart = {
    items: store.get('shroo_cart', []).filter(i => i && i.name && i.img),
    save() { store.set('shroo_cart', this.items); renderCart(); document.dispatchEvent(new Event('cart:change')); },
    add(p, qty = 1, silent = false) {
      const line = this.items.find(i => i.id === p.id);
      if (line) { line.qty += qty; line.price = p.price; }
      else this.items.push({ ...p, qty });
      this.save();
      if (!silent) toast(`تمت إضافة <b>${esc(p.name)}</b> إلى السلة`);
    },
    setQty(id, qty) { const l = this.items.find(i => i.id === id); if (!l) return; l.qty = Math.max(1, qty); this.save(); },
    step(id, d) { const l = this.items.find(i => i.id === id); if (l) this.setQty(id, l.qty + d); },
    remove(id) { this.items = this.items.filter(i => i.id !== id); this.save(); },
    clear() { this.items = []; this.save(); },
    count() { return this.items.reduce((s, i) => s + i.qty, 0); },
    subtotal() { return this.items.reduce((s, i) => s + i.qty * i.price, 0); }
  };

  const openCart = () => document.body.classList.add('drawer-open');
  const closeCart = () => document.body.classList.remove('drawer-open');

  /* wires the +/− and remove buttons of a cloned cart line */
  function bindLine(node, l) {
    $$('[data-d]', node).forEach(b => { b.dataset.dq = l.id; });
    const rm = $('.dl-rm', node); if (rm) rm.dataset.rm = l.id;
    const q = $('.qty input', node); if (q) q.value = l.qty;
  }

  function renderCart() {
    $$('.cart-count').forEach(e => { e.textContent = Cart.count(); });
    const lines = $('.drawer-lines'); if (!lines) return;
    const items = Cart.items;
    if (items.length) {
      lines.replaceChildren(...items.map(l => {
        const n = tpl('tpl-drawer-line');
        if (n) {
          $('img', n).src = l.img || '';
          const a = $('a', n); a.href = '#'; a.textContent = l.name || '';
          const p = $('.dl-price', n); if (p) p.textContent = money(l.price || 0);
          bindLine(n, l);
          return n;
        }
      }));
    } else {
      lines.replaceChildren();
    }
    const empty = !Cart.items.length;
    const de = $('.drawer-empty'); if (de) de.hidden = !empty;
    const df = $('.drawer-foot'); if (df) df.hidden = empty;
    const sub = Cart.subtotal(), left = FREE_SHIPPING_MIN - sub;
    const shipMore = $('.ship-more'); if (shipMore) shipMore.hidden = left <= 0;
    const shipFree = $('.ship-free'); if (shipFree) shipFree.hidden = left > 0;
    const shipLeft = $('.ship-left'); if (shipLeft) shipLeft.textContent = money(Math.max(0, left));
    const shipBar = $('.ship-bar span'); if (shipBar && FREE_SHIPPING_MIN > 0) shipBar.style.width = Math.min(100, sub / FREE_SHIPPING_MIN * 100) + '%';
    const drawerSub = $('.drawer-sub'); if (drawerSub) drawerSub.textContent = money(sub);
  }

  /* ---------- Wishlist ---------- */
  const Wish = {
    ids: store.get('shroo_wish', []),
    toggle(id) { this.ids = this.ids.includes(id) ? this.ids.filter(x => x !== id) : [...this.ids, id]; store.set('shroo_wish', this.ids); return this.ids.includes(id); },
    has(id) { return this.ids.includes(id); }
  };
  $$('[data-wish]').forEach(b => b.classList.toggle('on', Wish.has(b.dataset.wish)));

  /* ---------- Global events ---------- */
  document.addEventListener('click', e => {
    const t = e.target;
    const add = t.closest('[data-add]');
    if (add) { 
      e.preventDefault(); 
      try {
        Cart.add(productFrom(add)); 
        if (page !== 'checkout') openCart(); 
      } catch (err) {
        console.error('Add to cart error:', err);
        openCart();
      }
      return; 
    }
    const w = t.closest('[data-wish]');
    if (w) {
      e.preventDefault();
      const on = Wish.toggle(w.dataset.wish);
      $$(`[data-wish="${w.dataset.wish}"]`).forEach(x => x.classList.toggle('on', on));
      toast(on ? 'تمت الإضافة للمفضلة' : 'تمت الإزالة من المفضلة');
      return;
    }
    if (t.closest('.cart-btn, .js-open-cart')) { e.preventDefault(); openCart(); return; }
    if (t.closest('.drawer-close, .drawer-bg')) { closeCart(); return; }
    const dq = t.closest('[data-dq]');
    if (dq) { Cart.step(dq.dataset.dq, +dq.dataset.d); return; }
    const rm = t.closest('[data-rm]');
    if (rm) { Cart.remove(rm.dataset.rm); return; }
    if (t.closest('.burger')) { document.body.classList.toggle('nav-open'); return; }
    if (document.body.classList.contains('nav-open') && !t.closest('.nav')) document.body.classList.remove('nav-open');
  });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeCart(); document.body.classList.remove('nav-open'); } });
  $$('.nl-form').forEach(f => f.addEventListener('submit', e => { e.preventDefault(); f.reset(); toast('شكراً لاشتراكك! 💖'); }));

  /* ---------- Home ---------- */
  function initHome() {
    const hero = $('#hero');
    if (hero) {
      const slides = $$('.slide', hero), dots = $$('.dots button', hero);
      let cur = 0, timer;
      const go = i => {
        cur = (i + slides.length) % slides.length;
        slides.forEach((s, k) => { s.classList.toggle('active', k === cur); s.setAttribute('aria-hidden', k !== cur); });
        dots.forEach((d, k) => d.classList.toggle('on', k === cur));
      };
      const play = () => { clearInterval(timer); timer = setInterval(() => go(cur + 1), 6000); };
      $$('.hero-arrow', hero).forEach(b => b.addEventListener('click', () => { go(cur + +b.dataset.dir); play(); }));
      dots.forEach((d, k) => d.addEventListener('click', () => { go(k); play(); }));
      hero.addEventListener('mouseenter', () => clearInterval(timer));
      hero.addEventListener('mouseleave', play);
      let x0 = null;
      hero.addEventListener('touchstart', e => { x0 = e.touches[0].clientX; }, { passive: true });
      hero.addEventListener('touchend', e => {
        if (x0 === null) return;
        const dx = e.changedTouches[0].clientX - x0; x0 = null;
        if (Math.abs(dx) > 40) { go(cur + (dx > 0 ? 1 : -1)); play(); }
      });
      play();
    }
    $$('#bs-tabs button').forEach(b => b.addEventListener('click', () => {
      $$('#bs-tabs button').forEach(x => x.classList.toggle('active', x === b));
      $$('.bs-grid').forEach(g => { g.hidden = g.dataset.cat !== b.dataset.cat; });
    }));
  }

  /* ---------- Shop: the filters show/hide and reorder the cards already in the HTML ---------- */
  function initShop() {
    const grid = $('#shop-grid'); if (!grid) return;
    const cards = $$('.pcard', grid), emptyMsg = $('.shop-empty', grid);
    cards.forEach((c, i) => { c.dataset.order = i; c.dataset.text = c.textContent.replace(/\s+/g, ' ').toLowerCase(); });

    const params = new URLSearchParams(location.search);
    const range = $('#price-range'), rv = $('#price-val'), sortSel = $('#sort');
    const maxPrice = range ? +range.max : Infinity;
    const state = { cat: params.get('cat') || 'all', q: (params.get('q') || '').trim().toLowerCase(), max: maxPrice, benefits: [], skin: [], sort: 'featured', wish: params.get('wish') === '1' };
    const clientSort = sortSel && !sortSel.form; // لو جوه form يبقى السيرفر هو اللي بيرتب
    const words = s => (s || '').split(' ').filter(Boolean);
    const has = (attr, list) => !list.length || list.some(v => words(attr).includes(v));
    const sorters = {
      featured: (a, b) => a.dataset.order - b.dataset.order,
      low: (a, b) => a.dataset.price - b.dataset.price,
      high: (a, b) => b.dataset.price - a.dataset.price,
      rating: (a, b) => b.dataset.rating - a.dataset.rating,
      popular: (a, b) => b.dataset.reviews - a.dataset.reviews
    };

    const draw = () => {
      $$('#cat-list button').forEach(b => b.classList.toggle('active', b.dataset.cat === state.cat));
      if (range) { range.style.setProperty('--v', (state.max / maxPrice * 100) + '%'); if (rv) rv.textContent = money(state.max); }
      const visible = cards.filter(c => {
        const ok = (state.cat === 'all' || words(c.dataset.cat).includes(state.cat)) &&
          +c.dataset.price <= state.max &&
          (!state.q || c.dataset.text.includes(state.q)) &&
          has(c.dataset.benefits, state.benefits) && has(c.dataset.skin, state.skin) &&
          (!state.wish || Wish.has(c.dataset.id));
        c.hidden = !ok;
        return ok;
      });
      if (clientSort) [...cards].sort(sorters[state.sort] || sorters.featured).forEach(c => grid.insertBefore(c, emptyMsg));
      const count = $('#count'); if (count) count.textContent = items(visible.length);
      if (emptyMsg) emptyMsg.hidden = visible.length > 0;
    };

    const catList = $('#cat-list');
    if (catList) catList.addEventListener('click', e => { const b = e.target.closest('button'); if (b) { state.cat = b.dataset.cat; draw(); } });
    $$('[data-f]').forEach(i => i.addEventListener('change', () => { const k = i.dataset.f; state[k] = $$(`[data-f="${k}"]:checked`).map(x => x.value); draw(); }));
    if (range) range.addEventListener('input', () => { state.max = +range.value; draw(); });
    if (clientSort) sortSel.addEventListener('change', e => { state.sort = e.target.value; draw(); });
    const clear = $('#clear');
    if (clear) clear.addEventListener('click', () => {
      $$('[data-f]').forEach(i => { i.checked = false; });
      Object.assign(state, { cat: 'all', q: '', max: maxPrice, benefits: [], skin: [], wish: false });
      if (range) range.value = range.max;
      draw();
    });
    $$('.view-toggle button').forEach(b => b.addEventListener('click', () => {
      $$('.view-toggle button').forEach(x => x.classList.toggle('active', x === b));
      grid.classList.toggle('list-view', b.dataset.view === 'list');
    }));
    draw();
  }

  /* ---------- Product ---------- */
  function initProduct() {
    /* gallery */
    const main = $('#g-main img'), thumbs = $$('#g-thumbs button');
    let gi = 0;
    const setG = i => {
      if (!main || thumbs.length < 2) return;
      gi = (i + thumbs.length) % thumbs.length;
      main.src = thumbs[gi].dataset.full;
      thumbs.forEach((b, k) => b.classList.toggle('active', k === gi));
    };
    if (thumbs.length) thumbs.forEach((b, i) => b.addEventListener('click', () => setG(i)));
    if ($('#g-prev')) $('#g-prev').addEventListener('click', () => setG(gi - 1));
    if ($('#g-next')) $('#g-next').addEventListener('click', () => setG(gi + 1));

    /* quantity + add / buy */
    const qi = $('#qty'), addBtn = $('#add-main');
    if (qi && addBtn) {
      const qty = () => Math.max(1, parseInt(qi.value, 10) || 1);
      $$('[data-q]').forEach(b => b.addEventListener('click', () => { qi.value = Math.max(1, qty() + +b.dataset.q); }));
      addBtn.addEventListener('click', () => { Cart.add(productFrom(addBtn), qty()); openCart(); });
      if ($('#buy-now')) {
        $('#buy-now').addEventListener('click', () => { 
          Cart.add(productFrom(addBtn), qty(), true); 
          const url = (window.SHR && window.SHR.checkoutUrl) || '/';
          location.href = url; 
        });
      }
    }

    /* tabs + faq */
    const showTab = id => {
      $$('.tabs button').forEach(x => x.classList.toggle('active', x.dataset.tab === id));
      $$('.tab-panel').forEach(x => { x.hidden = x.id !== id; });
    };
    $$('.tabs button').forEach(b => b.addEventListener('click', () => showTab(b.dataset.tab)));
    $$('.js-open-reviews').forEach(a => a.addEventListener('click', () => showTab('t-rev')));
    $$('.faq-item button').forEach(b => b.addEventListener('click', () => {
      const parent = b.parentElement;
      const isOpen = parent.classList.contains('open');
      $$('.faq-item').forEach(item => item.classList.remove('open'));
      if (!isOpen) parent.classList.add('open');
    }));

    /* frequently bought together */
    const BUNDLE_PRICE = 179;
    const fbtInputs = $$('#fbt-items input');
    const fullPrice = fbtInputs.reduce((s, i) => s + (+i.dataset.price || 0), 0);
    const fbtCalc = () => {
      const sel = fbtInputs.filter(i => i.checked);
      const sum = sel.reduce((s, i) => s + (+i.dataset.price || 0), 0);
      const all = sel.length === fbtInputs.length;
      const fbtOld = $('#fbt-old'); if (fbtOld) { fbtOld.textContent = money(sum); fbtOld.hidden = !all && sum > 0; }
      const fbtSave = $('#fbt-save'); if (fbtSave) fbtSave.hidden = !all;
      if (fbtSave && all && fullPrice) fbtSave.textContent = `وفّر ${Math.round((1 - BUNDLE_PRICE / fullPrice) * 100)}%`;
      const fbtTotal = $('#fbt-total'); if (fbtTotal) fbtTotal.textContent = money(all && fullPrice ? BUNDLE_PRICE : sum);
      const fbtAdd = $('#fbt-add'); if (fbtAdd) fbtAdd.disabled = !sel.length;
    };
    if (fbtInputs.length) fbtInputs.forEach(i => i.addEventListener('change', fbtCalc));
    if (fbtInputs.length) fbtCalc();
    const fbtAddBtn = $('#fbt-add');
    if (fbtAddBtn) {
      fbtAddBtn.addEventListener('click', () => {
        const sel = fbtInputs.filter(i => i.checked), all = sel.length === fbtInputs.length;
        sel.forEach(i => {
          const p = productFrom(i);
          if (all && fullPrice) p.price = Math.round(p.price * BUNDLE_PRICE / fullPrice * 100) / 100;
          Cart.add(p, 1, true);
        });
        toast('تمت إضافة الباقة إلى السلة');
        openCart();
      });
    }

    /* routine carousel (RTL: "next" scrolls toward the left) */
    const tr = $('#routine-track');
    if (tr) {
      const prevBtn = $('#r-prev'), nextBtn = $('#r-next');
      if (prevBtn) prevBtn.addEventListener('click', () => tr.scrollBy({ left: tr.clientWidth * .8, behavior: 'smooth' }));
      if (nextBtn) nextBtn.addEventListener('click', () => tr.scrollBy({ left: -tr.clientWidth * .8, behavior: 'smooth' }));
    }
  }

  /* ---------- Checkout ---------- */
  function initCheckout() {
    let coupon = store.get('shroo_coupon', null);
    const upsell = $('#upsell-btn');

    const totals = () => {
      const sub = Cart.subtotal(), disc = coupon ? sub * COUPONS[coupon] : 0;
      const ship = !sub || sub - disc >= FREE_SHIPPING_MIN ? 0 : SHIPPING_FEE;
      return { sub, disc, ship, total: sub - disc + ship };
    };

    const draw = () => {
      const n = Cart.count();
      $('.sum-lines').replaceChildren(...Cart.items.map(l => {
        const node = tpl('tpl-sum-line');
        $('img', node).src = l.img;
        $('.sl-qty', node).textContent = l.qty;
        $('b', node).textContent = l.name;
        $('small', node).textContent = l.size || l.sub;
        $('.sl-total', node).textContent = money(l.price * l.qty);
        bindLine(node, l);
        return node;
      }));
      $('.sum-empty').hidden = n > 0;
      const t = totals();
      $('#sum-count').textContent = items(n);
      $('#sum-sub-label').textContent = `المجموع الفرعي (${items(n)})`;
      $('#sum-sub').textContent = money(t.sub);
      $('#sum-disc-row').hidden = !t.disc;
      $('#sum-disc').textContent = '- ' + money(t.disc);
      $('#sum-code').textContent = coupon || '';
      $('#sum-ship').textContent = t.ship ? money(t.ship) : 'مجاني';
      $('#sum-ship').classList.toggle('free', !t.ship);
      $('#sum-total').textContent = money(t.total);
      $('#btn-total').textContent = money(t.total);
      const inCart = Cart.items.some(i => i.id === upsell.dataset.id);
      upsell.disabled = inCart;
      $('.ub-add', upsell).hidden = inCart;
      $('.ub-done', upsell).hidden = !inCart;
    };
    document.addEventListener('cart:change', draw);
    draw();

    upsell.addEventListener('click', () => Cart.add(productFrom(upsell)));

    $('#coupon-form').addEventListener('submit', e => {
      e.preventDefault();
      const c = $('#coupon').value.trim().toUpperCase(), msg = $('#coupon-msg');
      if (COUPONS[c]) {
        coupon = c; store.set('shroo_coupon', c);
        msg.textContent = `تم تطبيق الكوبون ${c} — خصم ${COUPONS[c] * 100}%`; msg.className = 'c-msg ok';
      } else {
        msg.textContent = 'كود الخصم غير صحيح. جرّبي SHROO10'; msg.className = 'c-msg err';
      }
      draw();
    });

    $$('.pay-opt input').forEach(r => r.addEventListener('change', () => $$('.pay-opt').forEach(o => o.classList.toggle('active', $('input', o).checked))));

    const billSame = $('#bill-same'), billBox = $('#bill-box'), billToggle = $('#bill-toggle');
    const syncBill = () => { billBox.hidden = billSame.checked; billToggle.classList.toggle('open', !billSame.checked); };
    billSame.addEventListener('change', syncBill);
    billToggle.addEventListener('click', () => { billSame.checked = !billSame.checked; syncBill(); });
    $('#use-billing').addEventListener('change', e => { billSame.checked = e.target.checked; syncBill(); });

    const form = $('#checkout-form');
    $$('.field input, .field select', form).forEach(i => i.addEventListener('input', () => i.closest('.field').classList.remove('invalid')));
    form.addEventListener('submit', e => {
      e.preventDefault();
      let ok = true;
      $$('[required]', form).forEach(i => {
        const v = i.value.trim();
        const bad = !v || (i.type === 'email' && !/^\S+@\S+\.\S+$/.test(v)) || (i.name === 'phone' && v.replace(/\D/g, '').length < 9);
        i.closest('.field').classList.toggle('invalid', bad);
        if (bad) ok = false;
      });
      if (!Cart.count()) { toast('سلتك فارغة'); return; }
      if (!ok) {
        const f = $('.field.invalid input, .field.invalid select'); if (f) f.focus();
        toast('من فضلك أكملي الحقول المطلوبة'); return;
      }
      const d = Object.fromEntries(new FormData(form));
      const payNames = { card: 'بطاقة ائتمان / مدى', apple: 'Apple Pay', cod: 'الدفع عند الاستلام' };
      store.set('shroo_last_order', {
        no: 'SHR' + Math.floor(100000 + Math.random() * 900000),
        date: new Date().toISOString(),
        payment: payNames[d.payment],
        total: totals().total,
        lines: Cart.items.map(({ id, name, sub, img, qty, price }) => ({ id, name, sub, img, qty, price })),
        ship: { name: `${d.first} ${d.last}`, address: d.addr1 + (d.addr2 ? '، ' + d.addr2 : ''), city: d.city, region: d.region, zip: d.zip }
      });
      store.set('shroo_coupon', null);
      Cart.clear();
      location.href = window.SHR.checkoutUrl || 'checkout.html';
    });
  }

  /* ---------- Thank you: the HTML shows a sample order; this swaps in the real one ---------- */
  function initThanks() {
    const o = store.get('shroo_last_order', null);
    if (!o) return;
    const dt = new Date(o.date);
    const fmt = (d, y) => d.toLocaleDateString(LOC, { month: 'long', day: 'numeric', ...(y ? { year: 'numeric' } : {}) });
    const plus = n => { const d = new Date(dt); d.setDate(d.getDate() + n); return d; };
    $('#o-no .ltr').textContent = '#' + o.no;
    $('#o-date').textContent = `${fmt(dt, 1)} – ${dt.toLocaleTimeString(LOC, { hour: '2-digit', minute: '2-digit' })}`;
    $('#o-pay').textContent = o.payment;
    $('#o-total').textContent = money(o.total);
    $('#o-count').textContent = items(o.lines.reduce((s, l) => s + l.qty, 0));
    $('#o-items').replaceChildren(...o.lines.map(l => {
      const n = tpl('tpl-order-item');
      $('img', n).src = l.img;
      $('b', n).textContent = l.name;
      $('small', n).textContent = l.sub;
      $('strong', n).textContent = money(l.price);
      $('.oi-qty', n).textContent = `الكمية: ${l.qty}`;
      return n;
    }));
    const s = o.ship, sp = $$('#o-ship > *');
    sp[0].textContent = s.name;
    sp[1].textContent = s.address;
    sp[2].textContent = `${s.city}${s.region && s.region !== s.city ? '، ' + s.region : ''}، المملكة العربية السعودية`;
    sp[3].textContent = s.zip;
    $('#o-eta').textContent = `${fmt(plus(3))} – ${fmt(plus(6), 1)}`;
  }

  /* ---------- Boot ---------- */
  renderCart();
  try {
    ({ home: initHome, shop: initShop, product: initProduct, checkout: initCheckout, thanks: initThanks })[page]?.();
  } catch (err) {
    console.error('Init error:', err);
  }
})();
