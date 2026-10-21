/* ===== سلة ووكومرس — Shroo =====
   بيخلي أزرار "أضف للسلة" والدروار يشتغلوا على سلة ووكومرس الحقيقية بدل سلة المتصفح.
   بيتحمّل بعد main.js، وبيلقط الضغطة قبله (capture) فـ main.js مش محتاج يتعدل.
   البيانات جاية من PHP في window.shrooCart (inc/wc-cart-bridge.php).
*/
(() => {
  'use strict';
  const cfg = window.shrooCart;
  if (!cfg || !cfg.endpoint) return;

  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

  // سلة المتصفح القديمة مبقتش مستخدمة
  try { localStorage.removeItem('shroo_cart'); } catch (e) { /* ignore */ }

  const openCart = () => document.body.classList.add('drawer-open');
  let toastTimer;
  const toast = html => {
    const t = $('.toast'); if (!t) return;
    t.innerHTML = '<svg class="ic ic-check" aria-hidden="true"><use href="#i-check"></use></svg><span>' + html + '</span>';
    t.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), 2400);
  };

  /* ---------- رسم الدروار من بيانات ووكومرس ---------- */
  function render(c) {
    if (!c) return;
    $$('.cart-count').forEach(e => { e.textContent = c.count; });

    const lines = $('.drawer-lines'), tpl = document.getElementById('tpl-drawer-line');
    if (lines && tpl) {
      lines.replaceChildren(...c.items.map(it => {
        const n = tpl.content.firstElementChild.cloneNode(true);
        const img = $('img', n); if (img) { img.src = it.img; img.alt = ''; }
        const a = $('a', n); if (a) { a.href = it.url; a.textContent = it.name; }
        const pr = $('.dl-price', n); if (pr) pr.textContent = it.price;
        $$('[data-d]', n).forEach(b => { b.dataset.wkey = it.key; });
        const q = $('.qty input', n); if (q) q.value = it.qty;
        const rm = $('.dl-rm', n); if (rm) rm.dataset.wrm = it.key;
        return n;
      }));
    }

    const empty = !c.items.length;
    const de = $('.drawer-empty'), df = $('.drawer-foot');
    if (de) de.hidden = !empty;
    if (df) df.hidden = empty;

    const more = $('.ship-more'), free = $('.ship-free'), leftEl = $('.ship-left'), bar = $('.ship-bar span'), sub = $('.drawer-sub');
    const min = +cfg.freeShippingMin || 0;
    if (more) more.hidden = c.left <= 0;
    if (free) free.hidden = c.left > 0;
    if (leftEl) leftEl.textContent = c.leftText;
    if (bar) bar.style.width = (min ? Math.min(100, c.subtotal / min * 100) : 100) + '%';
    if (sub) sub.textContent = c.subtotalText;
    $$('.drawer-foot a.btn').forEach(a => { a.href = cfg.checkout; });
  }

  /* ---------- طلب للسيرفر ---------- */
  async function call(op, data = {}) {
    const body = new FormData();
    body.append('op', op);
    Object.entries(data).forEach(([k, v]) => {
      if (Array.isArray(v)) v.forEach(x => body.append(k + '[]', x));
      else if (v !== undefined && v !== null) body.append(k, v);
    });
    const res = await fetch(cfg.endpoint, { method: 'POST', body, credentials: 'same-origin' });
    const json = await res.json();
    if (!json.success) throw (json.data || {});
    render(json.data);
    return json.data;
  }

  /* ---------- الضغطات (قبل main.js) ---------- */
  let busy = false;
  document.addEventListener('click', async e => {
    const t = e.target;
    const mainBtn = t.closest('#add-main, #buy-now');
    const card = !mainBtn && t.closest('[data-add]');
    const fbt = t.closest('#fbt-add');
    const step = t.closest('[data-wkey]');
    const rm = t.closest('[data-wrm]');
    if (!mainBtn && !card && !fbt && !step && !rm) return;

    e.preventDefault();
    e.stopPropagation(); // main.js مايضيفش في سلة المتصفح
    if (busy) return;
    busy = true;

    try {
      if (step) {
        await call('step', { key: step.dataset.wkey, d: step.dataset.d });
      } else if (rm) {
        await call('remove', { key: rm.dataset.wrm });
      } else if (fbt) {
        const picked = $$('#fbt-items input:checked');
        for (const i of picked) await call('add', { product_id: i.dataset.id || i.value, qty: 1 });
        if (picked.length) { toast('تمت إضافة الباقة إلى السلة'); openCart(); }
      } else {
        const btn = mainBtn ? $('#add-main') : card;
        if (!btn) return;
        const data = { product_id: btn.dataset.id || btn.dataset.add, qty: 1 };

        if (mainBtn) {
          const q = $('#qty');
          data.qty = Math.max(1, parseInt(q && q.value, 10) || 1);
          const r = $('#offers input[name="offer"]:checked');
          if (r) {
            data.offer = r.value;
            data.picks = $$('.opt-line select', r.closest('.offer')).map(s => s.value);
          }
        }

        await call('add', data);

        if (t.closest('#buy-now')) { location.href = cfg.checkout; return; }
        toast(`تمت إضافة <b>${esc(btn.dataset.name || 'المنتج')}</b> إلى السلة`);
        openCart();
      }
    } catch (err) {
      if (err && err.redirect) { location.href = err.redirect; return; }
      toast(esc((err && err.message) || 'حصل خطأ، حاولي تاني'));
    } finally {
      busy = false;
    }
  }, true);

  /* ---------- أول تحميل: اعرض سلة ووكومرس (بعد ما main.js يخلص) ---------- */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => render(cfg.cart));
  } else {
    setTimeout(() => render(cfg.cart), 0);
  }
})();