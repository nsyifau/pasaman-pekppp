/*
 * app.js: perilaku interaktif template. Memakai event delegation dan atribut data-*,
 * jadi satu file ini menangani semua halaman.
 *
 * Peta ke React:
 *   data-dropdown / data-tabs / data-collapse  -> state useState di komponen masing-masing
 *   dialog + data-modal-open                   -> <Dialog> (shadcn/ui, Radix)
 *   data-table                                 -> TanStack Table
 *   App.toast()                                -> sonner / react-hot-toast
 *   preferensi (tema, aksen, radius, sidebar)  -> Context + localStorage
 */
(function () {
  'use strict';

  var root = document.documentElement;
  var body = document.body;
  function $(s, r) { return (r || document).querySelector(s); }
  function $$(s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); }
  function iconHref(name) {
    var use = document.querySelector('use[href*="icons.svg"]');
    var base = use ? use.getAttribute('href').split('#')[0] : '/icons.svg';
    return base + '#i-' + name;
  }
  var store = {
    get: function (k) { try { return localStorage.getItem(k); } catch (e) { return null; } },
    set: function (k, v) { try { localStorage.setItem(k, v); } catch (e) {} },
    del: function (k) { try { localStorage.removeItem(k); } catch (e) {} }
  };

  /* =====================================================
     PREFERENSI: tema, aksen, radius, mode sidebar
     ===================================================== */
  var DEFAULTS = { theme: 'system', accent: 'indigo', radius: '0.5', sbmode: 'full' };
  function pref(k) { return store.get(k) || DEFAULTS[k]; }
  function effTheme() {
    return root.dataset.theme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  }
  function apply(k, v) {
    if (k === 'theme') { if (v === 'system') delete root.dataset.theme; else root.dataset.theme = v; }
    if (k === 'accent') { if (v === 'indigo') delete root.dataset.accent; else root.dataset.accent = v; }
    if (k === 'radius') root.style.setProperty('--radius', v + 'rem');
    if (k === 'sbmode') { if (v === 'mini') root.dataset.sbmode = 'mini'; else delete root.dataset.sbmode; }
  }
  function syncPrefs() {
    $$('[data-pref]').forEach(function (b) {
      b.setAttribute('aria-pressed', String(pref(b.dataset.pref) === b.dataset.value));
    });
    var icon = iconHref(effTheme() === 'dark' ? 'sun' : 'moon');
    $$('.js-theme-icon').forEach(function (u) { u.setAttribute('href', icon); });
  }
  function setPref(k, v) { store.set(k, v); apply(k, v); syncPrefs(); }
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', syncPrefs);

  /* =====================================================
     SIDEBAR
     ===================================================== */
  function setSidebar(open) { body.dataset.sidebar = open ? 'open' : 'closed'; }

  function currentKey() {
    if (window.__SPA__) {
      var h = location.hash.match(/^#\/([\w-]+)/);
      return h ? h[1] : 'index';
    }
    return location.pathname.split('/').pop().replace(/\.html$/, '') || 'index';
  }
  function markActive() {
    var k = currentKey();
    $$('#sidebar a.nav-link').forEach(function (a) {
      var href = a.getAttribute('href') || '';
      var key = href.replace(/^.*\//, '').replace(/\.html.*$/, '');
      var on = key === k;
      if (on) a.setAttribute('aria-current', 'page'); else a.removeAttribute('aria-current');
      if (on) {
        var sub = a.closest('.nav-sub');
        if (sub) {
          sub.hidden = false;
          var t = $('[data-collapse="#' + sub.id + '"]');
          if (t) t.setAttribute('aria-expanded', 'true');
        }
      }
    });
  }

  /* =====================================================
     TOAST
     ===================================================== */
  var TONES = {
    success: ['check-circle', 'text-success'], danger: ['alert-circle', 'text-danger'],
    warning: ['alert-triangle', 'text-warning'], info: ['info', 'text-info'], neutral: ['bell', 'text-muted-foreground']
  };
  function motionTime(el) {
    var style = window.getComputedStyle(el);
    function ms(value) { return value.endsWith('ms') ? parseFloat(value) : parseFloat(value) * 1000; }
    var durations = style.animationDuration.split(',').map(function (v) { return ms(v.trim()) || 0; });
    var delays = style.animationDelay.split(',').map(function (v) { return ms(v.trim()) || 0; });
    return Math.max.apply(null, durations.map(function (duration, i) { return duration + (delays[i] || 0); }));
  }
  function afterMotion(el, callback) {
    var duration = motionTime(el);
    if (duration <= 0) { callback(); return function () {}; }
    var done = false, timer;
    function cleanup() { clearTimeout(timer); el.removeEventListener('animationend', onEnd); }
    function finish() { if (done) return; done = true; cleanup(); callback(); }
    function onEnd(event) { if (event.target === el && !event.pseudoElement) finish(); }
    el.addEventListener('animationend', onEnd);
    timer = setTimeout(finish, duration + 60);
    return function () { if (done) return; done = true; cleanup(); };
  }
  function toast(o) {
    var c = $('#toasts'); if (!c) return;
    var t = TONES[o.tone] || TONES.neutral;
    var el = document.createElement('div');
    el.className = 'toast-notice pointer-events-auto flex items-start gap-3 rounded-lg border bg-card p-4 text-sm shadow-lg';
    el.innerHTML =
      '<svg class="mt-0.5 h-4 w-4 shrink-0 ' + t[1] + '" aria-hidden="true"><use href="' + iconHref(t[0]) + '"/></svg>' +
      '<div class="min-w-0 flex-1"><p class="font-medium">' + (o.title || '') + '</p>' +
      (o.description ? '<p class="mt-0.5 text-muted-foreground">' + o.description + '</p>' : '') + '</div>' +
      '<button type="button" class="-m-1 rounded p-1 text-muted-foreground hover:text-foreground" aria-label="Tutup"><svg class="h-4 w-4"><use href="' + iconHref('x') + '"/></svg></button>';
    var timer;
    var close = function () {
      if (el.dataset.closing) return;
      clearTimeout(timer);
      el.dataset.closing = 'true';
      afterMotion(el, function () { el.remove(); });
    };
    el.querySelector('button').addEventListener('click', close);
    c.appendChild(el);
    timer = setTimeout(close, o.duration || 4500);
  }

  /* =====================================================
     DROPDOWN (menu diposisikan fixed agar tidak terpotong tabel/overflow)
     ===================================================== */
  function closeMenus(except) {
    $$('[data-dropdown] .menu').forEach(function (m) {
      if (m === except || m.hidden || m.classList.contains('hidden') || m.dataset.closing) return;
      m.dataset.closing = 'true';
      m.__cancelClose = afterMotion(m, function () {
        m.hidden = true;
        delete m.dataset.closing;
        m.__cancelClose = null;
      });
    });
    $$('[data-dropdown-toggle]').forEach(function (b) { b.setAttribute('aria-expanded', 'false'); });
  }
  function openMenu(tg) {
    var w = tg.closest('[data-dropdown]'), m = $('.menu', w);
    closeMenus(m);
    if (m.__cancelClose) m.__cancelClose();
    m.__cancelClose = null;
    delete m.dataset.closing;
    m.classList.remove('hidden');
    m.hidden = false;
    var r = tg.getBoundingClientRect(), mw = m.offsetWidth, mh = m.offsetHeight;
    var left = m.classList.contains('menu-right') ? r.right - mw : r.left;
    left = Math.max(8, Math.min(left, window.innerWidth - mw - 8));
    var topPos = r.bottom + 4;
    if (topPos + mh > window.innerHeight - 8 && r.top - 4 - mh > 8) topPos = r.top - 4 - mh;
    m.style.left = left + 'px'; m.style.top = topPos + 'px';
    tg.setAttribute('aria-expanded', 'true');
  }

  function openDialog(dialog) {
    if (dialog.__cancelClose) dialog.__cancelClose();
    dialog.__cancelClose = null;
    delete dialog.dataset.closing;
    if (!dialog.open) dialog.showModal();
  }
  function closeDialog(dialog, onClosed) {
    if (!dialog || !dialog.open) { if (onClosed) onClosed(); return; }
    if (dialog.dataset.closing) return;
    dialog.dataset.closing = 'true';
    dialog.__cancelClose = afterMotion(dialog, function () {
      dialog.close();
      delete dialog.dataset.closing;
      dialog.__cancelClose = null;
      if (onClosed) onClosed();
    });
  }
  document.addEventListener('cancel', function (event) {
    if (!event.target.matches('dialog.modal, dialog.drawer')) return;
    event.preventDefault();
    closeDialog(event.target);
  }, true);

  /* =====================================================
     EVENT DELEGATION
     ===================================================== */
  document.addEventListener('click', function (e) {
    var t = e.target;
    var el;

    if ((el = t.closest('#sidebarToggle'))) { setSidebar(body.dataset.sidebar !== 'open'); return; }
    if (t.id === 'overlay') { setSidebar(false); return; }
    if (t.closest('#sidebar a.nav-link[href]') && window.innerWidth < 1024) setSidebar(false);

    if ((el = t.closest('[data-theme-toggle]'))) { setPref('theme', effTheme() === 'dark' ? 'light' : 'dark'); return; }
    if ((el = t.closest('[data-pref]'))) { setPref(el.dataset.pref, el.dataset.value); return; }
    if (t.closest('[data-pref-reset]')) {
      Object.keys(DEFAULTS).forEach(function (k) { store.del(k); apply(k, DEFAULTS[k]); });
      syncPrefs(); return;
    }

    if ((el = t.closest('[data-collapse]'))) {
      var target = $(el.dataset.collapse);
      if (target) { target.hidden = !target.hidden; el.setAttribute('aria-expanded', String(!target.hidden)); }
      return;
    }

    if ((el = t.closest('[data-dropdown-toggle]'))) {
      var menu = $('.menu', el.closest('[data-dropdown]'));
      if (menu.hidden || menu.classList.contains('hidden') || menu.dataset.closing) openMenu(el); else closeMenus();
      return;
    }
    if (t.closest('.menu-item') && !t.closest('[data-keep-open]')) closeMenus();
    else if (!t.closest('.menu')) closeMenus();

    if ((el = t.closest('[role="tab"]'))) { selectTab(el); return; }

    if ((el = t.closest('[data-modal-open]'))) {
      var d = $(el.dataset.modalOpen);
      if (d && d.showModal) openDialog(d);
      return;
    }
    if ((el = t.closest('[data-modal-close]'))) {
      var dd = el.closest('dialog');
      closeDialog(dd, el.hasAttribute('data-toast') ? function () {
        toast({ title: el.dataset.toastTitle, description: el.dataset.toastDesc, tone: el.dataset.toast });
      } : null);
      return;
    }
    if (t.tagName === 'DIALOG') { closeDialog(t); return; }

    if ((el = t.closest('[data-toast]'))) {
      toast({ title: el.dataset.toastTitle, description: el.dataset.toastDesc, tone: el.dataset.toast });
      return;
    }
    if ((el = t.closest('[data-dismiss]'))) {
      var box = el.closest('[data-dismissible]');
      if (box && !box.dataset.closing) {
        box.dataset.closing = 'true';
        afterMotion(box, function () { box.remove(); });
      }
      return;
    }
    if ((el = t.closest('[data-copy]'))) {
      try { navigator.clipboard.writeText(el.dataset.copy); } catch (er) {}
      toast({ title: 'Disalin ke papan klip', tone: 'success' });
      return;
    }
    if ((el = t.closest('[data-load]'))) {
      var label = el.innerHTML;
      el.disabled = true;
      el.innerHTML = '<span class="spinner"></span>Memproses';
      setTimeout(function () { el.disabled = false; el.innerHTML = label; }, 1600);
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { closeMenus(); if (window.innerWidth < 1024 && !$('dialog[open]')) setSidebar(false); }
    var tab = e.target.closest && e.target.closest('[role="tab"]');
    if (tab && (e.key === 'ArrowRight' || e.key === 'ArrowLeft')) {
      var tabs = $$('[role="tab"]', tab.closest('[role="tablist"]'));
      var next = tabs[(tabs.indexOf(tab) + (e.key === 'ArrowRight' ? 1 : tabs.length - 1)) % tabs.length];
      next.focus(); selectTab(next);
    }
  });
  window.addEventListener('scroll', closeMenus, true);
  window.addEventListener('resize', closeMenus);

  function selectTab(tab) {
    var list = tab.closest('[role="tablist"]');
    $$('[role="tab"]', list).forEach(function (x) {
      var on = x === tab;
      x.setAttribute('aria-selected', String(on));
      x.tabIndex = on ? 0 : -1;
      var p = x.getAttribute('aria-controls') && document.getElementById(x.getAttribute('aria-controls'));
      if (p) p.hidden = !on;
    });
    if (window.Charts) Charts.init(document);
  }

  /* Pilih semua / baris pada tabel */
  document.addEventListener('change', function (e) {
    var t = e.target, tbl = t.closest && t.closest('[data-table]');
    if (!tbl) return;
    if (t.matches('[data-check-all]')) {
      $$('tbody tr', tbl).forEach(function (r) {
        var c = $('[data-check-row]', r);
        if (c && !r.hidden) c.checked = t.checked;
      });
    }
    updateBulk(tbl);
  });
  function updateBulk(tbl) {
    var n = $$('[data-check-row]:checked', tbl).length;
    var bar = $('[data-bulk]', tbl), cnt = $('[data-bulk-count]', tbl);
    if (bar) bar.hidden = n === 0;
    if (cnt) cnt.textContent = n;
  }

  /* =====================================================
     DATA TABLE: cari, filter, urut, paginasi (client-side)
     ===================================================== */
  function initTable(t) {
    if (t.__init) return; t.__init = true;
    var tbody = $('tbody', t), rows = $$('tr', tbody);
    var st = { q: '', f: '', col: -1, dir: 1, page: 1, size: +t.dataset.pageSize || 8 };
    var qi = $('[data-table-search]', t), fi = $('[data-table-filter]', t);
    var pager = $('[data-table-pager]', t), info = $('[data-table-info]', t), empty = $('[data-table-empty]', t);
    var ths = $$('th[data-sort]', t);
    rows.forEach(function (r) {
      r.__txt = $$('td', r).slice(0, -1).map(function (c) { return c.textContent.trim().toLowerCase(); }).join(' ');
    });
    function cell(r, i) { var c = r.cells[i]; return c ? (c.dataset.value != null ? c.dataset.value : c.textContent.trim()) : ''; }

    function drawPager(pages) {
      pager.innerHTML = '';
      function btn(label, p, o) {
        var b = document.createElement('button');
        b.type = 'button'; b.className = 'page-btn'; b.innerHTML = label;
        if (o && o.aria) b.setAttribute('aria-label', o.aria);
        if (o && o.disabled) b.disabled = true;
        if (o && o.current) b.setAttribute('aria-current', 'page');
        b.addEventListener('click', function () { st.page = p; draw(); });
        pager.appendChild(b);
      }
      btn('‹', st.page - 1, { disabled: st.page <= 1, aria: 'Sebelumnya' });
      var list = [];
      for (var p = 1; p <= pages; p++) {
        if (p === 1 || p === pages || Math.abs(p - st.page) <= 1) list.push(p);
        else if (list[list.length - 1] !== '…') list.push('…');
      }
      list.forEach(function (p) {
        if (p === '…') { var s = document.createElement('span'); s.className = 'px-1 text-muted-foreground'; s.textContent = '…'; pager.appendChild(s); }
        else btn(String(p), p, { current: p === st.page });
      });
      btn('›', st.page + 1, { disabled: st.page >= pages, aria: 'Berikutnya' });
    }

    function draw() {
      var list = rows.filter(function (r) {
        if (st.f && r.dataset.status !== st.f) return false;
        return !st.q || r.__txt.indexOf(st.q) > -1;
      });
      if (st.col > -1) {
        var th = ths.filter(function (h) { return h.cellIndex === st.col; })[0];
        var num = th && th.dataset.sort === 'num';
        list.sort(function (a, b) {
          var x = cell(a, st.col), y = cell(b, st.col);
          return (num ? parseFloat(x) - parseFloat(y) : x.localeCompare(y, 'id')) * st.dir;
        });
      }
      var pages = Math.max(1, Math.ceil(list.length / st.size));
      st.page = Math.min(st.page, pages);
      var s = (st.page - 1) * st.size, e = s + st.size;
      rows.forEach(function (r) { r.hidden = true; });
      list.forEach(function (r, i) { r.hidden = !(i >= s && i < e); tbody.appendChild(r); });
      if (empty) empty.hidden = list.length > 0;
      if (info) info.textContent = list.length ? 'Menampilkan ' + (s + 1) + '–' + Math.min(e, list.length) + ' dari ' + list.length + ' data' : 'Tidak ada data';
      if (pager) drawPager(pages);
      ths.forEach(function (h) { h.setAttribute('aria-sort', h.cellIndex === st.col ? (st.dir > 0 ? 'ascending' : 'descending') : 'none'); });
      var ca = $('[data-check-all]', t); if (ca) ca.checked = false;
      $$('[data-check-row]', t).forEach(function (c) { c.checked = false; });
      updateBulk(t);
    }

    if (qi) qi.addEventListener('input', function () { st.q = qi.value.trim().toLowerCase(); st.page = 1; draw(); });
    if (fi) fi.addEventListener('change', function () { st.f = fi.value; st.page = 1; draw(); });
    var reset = $('[data-demo-reset-table]', t);
    if (reset) reset.addEventListener('click', function () {
      if (qi) qi.value = '';
      if (fi) fi.value = '';
      st.q = ''; st.f = ''; st.page = 1; draw();
      if (qi) qi.focus();
    });
    ths.forEach(function (h) {
      h.tabIndex = 0;
      var go = function () {
        if (st.col === h.cellIndex) st.dir *= -1; else { st.col = h.cellIndex; st.dir = 1; }
        st.page = 1; draw();
      };
      h.addEventListener('click', go);
      h.addEventListener('keydown', function (e) { if (e.key === 'Enter') go(); });
    });
    draw();
  }

  /* =====================================================
     INIT HALAMAN (dipanggil ulang setiap konten halaman berganti)
     ===================================================== */
  function initPage(scope) {
    scope = scope || document;
    if (window.EnhancedControls) EnhancedControls.init(scope);
    $$('[data-table]', scope).forEach(initTable);
    if (window.Charts) Charts.init(scope);
    markActive();
    syncPrefs();
  }

  window.App = { toast: toast, setPref: setPref, initPage: initPage };

  /* =====================================================
     ROUTER PRATINJAU (hanya aktif pada pratinjau satu-file: window.__SPA__)
     Pada build multi-halaman, tiap halaman dimuat normal oleh browser.
     ===================================================== */
  function startSpa() {
    var pageRoot = $('#page-root');
    function go() {
      var key = currentKey();
      var tpl = $('template[data-page="' + key + '"]') || $('template[data-page="index"]');
      pageRoot.replaceChildren(tpl.content.cloneNode(true));
      body.dataset.layout = tpl.dataset.layout === 'blank' ? 'blank' : 'app';
      document.title = tpl.dataset.title + ' · Kenanga Admin';
      var g = $('#crumb-group'), tt = $('#crumb-title');
      if (g) g.textContent = tpl.dataset.group;
      if (tt) tt.textContent = tpl.dataset.title;
      window.scrollTo(0, 0);
      initPage(pageRoot);
    }
    window.addEventListener('hashchange', go);
    document.addEventListener('click', function (e) {
      var a = e.target.closest('a[href]'); if (!a) return;
      var m = (a.getAttribute('href') || '').match(/^([\w-]+)\.html/);
      if (m && $('template[data-page="' + m[1] + '"]')) { e.preventDefault(); location.hash = '#/' + m[1]; }
    });
    go();
  }

  /* Mulai */
  syncPrefs();
  if (window.__SPA__) startSpa(); else initPage(document);
})();
