/*
 * charts.js: grafik SVG ringan tanpa dependensi.
 * Warna mengikuti CSS variable (--chart-1..5, --success, dst) sehingga otomatis
 * ikut berubah saat tema atau aksen diganti.
 *
 * Pemakaian:  <div data-chart="area" data-labels='["Sen","Sel"]' data-series='[{"name":"A","data":[1,2]}]'></div>
 * Tipe: area | line | bar | stacked | donut | radial | spark | hbar
 *
 * Di React: ganti dengan Recharts / Chart.js / visx, data-nya sama persis.
 */
(function (global) {
  'use strict';

  var NS = 'http://www.w3.org/2000/svg';

  function svg(name, attrs, parent) {
    var e = document.createElementNS(NS, name);
    for (var k in attrs) e.setAttribute(k, attrs[k]);
    if (parent) parent.appendChild(e);
    return e;
  }
  function html(tag, cls, parent, text) {
    var e = document.createElement(tag);
    if (cls) e.className = cls;
    if (text != null) e.textContent = text;
    if (parent) parent.appendChild(e);
    return e;
  }
  function colorOf(c) {
    c = c || 'chart-1';
    if (/^\d$/.test(String(c))) c = 'chart-' + c;
    return 'hsl(var(--' + c + '))';
  }
  function fmt(v, c) {
    return (c.prefix || '') + Number(v).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + (c.suffix || '');
  }
  function ticks(max, n) {
    var raw = max / n, mag = Math.pow(10, Math.floor(Math.log10(raw))), norm = raw / mag;
    var step = (norm <= 1 ? 1 : norm <= 2 ? 2 : norm <= 2.5 ? 2.5 : norm <= 5 ? 5 : 10) * mag;
    var top = Math.ceil(max / step) * step, out = [];
    for (var v = 0; v <= top + step / 1000; v += step) out.push(+v.toFixed(6));
    return out;
  }
  function curve(pts, base) {
    if (pts.length < 3) return 'M' + pts.map(function (p) { return p.join(','); }).join('L');
    var d = 'M' + pts[0][0] + ',' + pts[0][1];
    for (var i = 0; i < pts.length - 1; i++) {
      var p0 = pts[i - 1] || pts[i], p1 = pts[i], p2 = pts[i + 1], p3 = pts[i + 2] || p2;
      var c1y = Math.min(p1[1] + (p2[1] - p0[1]) / 6, base), c2y = Math.min(p2[1] - (p3[1] - p1[1]) / 6, base);
      d += 'C' + (p1[0] + (p2[0] - p0[0]) / 6) + ',' + c1y + ' ' + (p2[0] - (p3[0] - p1[0]) / 6) + ',' + c2y + ' ' + p2[0] + ',' + p2[1];
    }
    return d;
  }
  function topRound(x, y, w, h, r) {
    r = Math.max(0, Math.min(r, w / 2, h));
    return 'M' + x + ',' + (y + h) + 'V' + (y + r) + 'Q' + x + ',' + y + ' ' + (x + r) + ',' + y + 'H' + (x + w - r) + 'Q' + (x + w) + ',' + y + ' ' + (x + w) + ',' + (y + r) + 'V' + (y + h) + 'Z';
  }
  function legend(box, items) {
    var l = html('div', 'mt-4 flex flex-wrap gap-x-4 gap-y-1 text-xs text-muted-foreground', box);
    items.forEach(function (it) {
      var i = html('span', 'inline-flex items-center gap-1.5', l);
      var d = html('span', 'h-2 w-2 rounded-full', i); d.style.background = it[1];
      html('span', '', i, it[0]);
    });
  }
  function cfgOf(box) {
    var d = box.dataset;
    function j(k, def) { try { return d[k] ? JSON.parse(d[k]) : def; } catch (e) { return def; } }
    return {
      type: d.chart, labels: j('labels', []), series: j('series', []), items: j('items', []), values: j('values', []),
      height: d.height, prefix: d.prefix || '', suffix: d.suffix || '', smooth: d.smooth !== undefined,
      legend: d.legend !== undefined, value: d.value, label: d.label, color: d.color, size: d.size, kind: d.kind,
      total: d.totalLabel, aria: d.aria
    };
  }

  /* ---------- area | line | bar | stacked ---------- */
  function cartesian(box, c) {
    var type = c.type, S = c.series, L = c.labels, n = L.length, stacked = type === 'stacked';
    var W = Math.max(box.clientWidth, 260), H = +c.height || 260;
    var m = { l: 42, r: 8, t: 10, b: 26 }, iw = W - m.l - m.r, ih = H - m.t - m.b, base = m.t + ih;
    var max = 0, i;
    for (i = 0; i < n; i++) {
      var sum = 0;
      S.forEach(function (s) { var v = +s.data[i] || 0; sum = stacked ? sum + v : Math.max(sum, v); });
      max = Math.max(max, sum);
    }
    var tk = ticks(max || 1, 4), top = tk[tk.length - 1], bw = iw / n;
    function X(k) { return m.l + bw * (k + 0.5); }
    function Y(v) { return base - (v / top) * ih; }

    box.innerHTML = '';
    box.style.position = 'relative';
    var sv = svg('svg', { viewBox: '0 0 ' + W + ' ' + H, role: 'img', 'aria-label': c.aria || 'Grafik' }, box);
    sv.style.cssText = 'display:block;width:100%;height:' + H + 'px';

    tk.forEach(function (t) {
      var y = Y(t);
      svg('line', { x1: m.l, x2: W - m.r, y1: y, y2: y, style: 'stroke:hsl(var(--border));' + (t ? 'stroke-dasharray:3 4' : '') }, sv);
      var tx = svg('text', { x: m.l - 8, y: y + 4, 'text-anchor': 'end', style: 'fill:hsl(var(--muted-foreground));font-size:11px' }, sv);
      tx.textContent = t.toLocaleString('id-ID');
    });
    var every = Math.ceil(n / Math.max(1, Math.floor(iw / 46)));
    L.forEach(function (l, k) {
      if (k % every) return;
      var tx = svg('text', { x: X(k), y: H - 7, 'text-anchor': 'middle', style: 'fill:hsl(var(--muted-foreground));font-size:11px' }, sv);
      tx.textContent = l;
    });

    var isBar = type === 'bar' || stacked;
    var hl = svg('rect', { y: m.t, height: ih, width: bw, rx: 4, style: 'fill:hsl(var(--muted));opacity:0' }, sv);
    var guide = svg('line', { y1: m.t, y2: base, style: 'stroke:hsl(var(--muted-foreground));stroke-width:1;stroke-dasharray:3 3;opacity:0' }, sv);
    var dots = [];

    S.forEach(function (s, si) {
      var col = colorOf(s.color || si + 1);
      if (type === 'bar') {
        var gw = bw * 0.62, bwid = (gw - (S.length - 1) * 3) / S.length;
        s.data.forEach(function (v, k) {
          var h = base - Y(v); if (h <= 0) return;
          svg('path', { d: topRound(X(k) - gw / 2 + si * (bwid + 3), Y(v), bwid, h, 4), style: 'fill:' + col }, sv);
        });
      } else if (stacked) {
        var cw = bw * 0.56;
        for (var k = 0; k < n; k++) {
          var below = 0;
          for (var q = 0; q < si; q++) below += +S[q].data[k] || 0;
          var v2 = +s.data[k] || 0, y0 = Y(below), y1 = Y(below + v2);
          if (v2 <= 0) continue;
          if (si === S.length - 1) svg('path', { d: topRound(X(k) - cw / 2, y1, cw, y0 - y1, 4), style: 'fill:' + col }, sv);
          else svg('rect', { x: X(k) - cw / 2, y: y1, width: cw, height: y0 - y1, style: 'fill:' + col + ';stroke:hsl(var(--card));stroke-width:1' }, sv);
        }
      } else {
        var pts = s.data.map(function (v, k) { return [X(k), Y(+v || 0)]; });
        var d = c.smooth ? curve(pts, base) : 'M' + pts.map(function (p) { return p.join(','); }).join('L');
        if (type === 'area') svg('path', { d: d + 'L' + X(n - 1) + ',' + base + 'L' + X(0) + ',' + base + 'Z', style: 'fill:' + col + ';opacity:.13' }, sv);
        svg('path', { d: d, fill: 'none', 'stroke-linejoin': 'round', 'stroke-linecap': 'round', style: 'stroke:' + col + ';stroke-width:2' }, sv);
      }
    });
    if (!isBar) {
      S.forEach(function (s, si) {
        dots.push(svg('circle', { r: 4, style: 'fill:hsl(var(--card));stroke:' + colorOf(s.color || si + 1) + ';stroke-width:2;opacity:0' }, sv));
      });
    }

    var tip = html('div', 'pointer-events-none absolute z-10 min-w-[9rem] rounded-lg border bg-card px-3 py-2 text-xs shadow-md', box);
    tip.style.display = 'none';
    var over = svg('rect', { x: m.l, y: m.t, width: iw, height: ih, fill: 'transparent' }, sv);

    function idxAt(ev) {
      var r = sv.getBoundingClientRect();
      var x = (ev.clientX - r.left) * (W / r.width) - m.l;
      return Math.max(0, Math.min(n - 1, Math.floor(x / bw)));
    }
    function show(k) {
      if (isBar) { hl.setAttribute('x', m.l + bw * k); hl.style.opacity = 1; }
      else {
        guide.setAttribute('x1', X(k)); guide.setAttribute('x2', X(k)); guide.style.opacity = 1;
        dots.forEach(function (dt, si) { dt.setAttribute('cx', X(k)); dt.setAttribute('cy', Y(+S[si].data[k] || 0)); dt.style.opacity = 1; });
      }
      var rows = '<p class="mb-1.5 font-medium">' + L[k] + '</p>';
      S.forEach(function (s, si) {
        rows += '<div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full" style="background:' + colorOf(s.color || si + 1) + '"></span>' +
          '<span class="text-muted-foreground">' + s.name + '</span><span class="ml-auto pl-4 font-medium tabular-nums">' + fmt(s.data[k], c) + '</span></div>';
      });
      tip.innerHTML = rows;
      tip.style.display = 'block';
      var tw = tip.offsetWidth, px = X(k), left = px + 14;
      if (left + tw > W - 4) left = px - tw - 14;
      tip.style.left = Math.max(4, left) + 'px';
      tip.style.top = (m.t + 6) + 'px';
    }
    function hide() {
      hl.style.opacity = 0; guide.style.opacity = 0; tip.style.display = 'none';
      dots.forEach(function (dt) { dt.style.opacity = 0; });
    }
    over.addEventListener('pointermove', function (ev) { show(idxAt(ev)); });
    over.addEventListener('pointerleave', hide);

    if (S.length > 1 || c.legend) legend(box, S.map(function (s, si) { return [s.name, colorOf(s.color || si + 1)]; }));
  }

  /* ---------- donut ---------- */
  function donut(box, c) {
    var I = c.items, total = I.reduce(function (a, b) { return a + (+b.value); }, 0);
    var size = +c.size || 176, sw = 20, R = (size - sw) / 2, C = 2 * Math.PI * R, gap = I.length > 1 ? 3 : 0, mid = size / 2;
    box.innerHTML = '';
    box.classList.add('flex', 'flex-wrap', 'items-center', 'justify-center', 'gap-4');
    var wrap = html('div', 'relative shrink-0', box);
    wrap.style.cssText = 'width:' + size + 'px;height:' + size + 'px';
    var sv = svg('svg', { viewBox: '0 0 ' + size + ' ' + size, width: size, height: size, role: 'img', 'aria-label': c.aria || 'Grafik donat' }, wrap);
    svg('circle', { cx: mid, cy: mid, r: R, fill: 'none', 'stroke-width': sw, style: 'stroke:hsl(var(--muted))' }, sv);
    var off = 0, segs = [];
    I.forEach(function (it, i) {
      var len = it.value / total * C;
      segs.push(svg('circle', {
        cx: mid, cy: mid, r: R, fill: 'none', 'stroke-width': sw,
        'stroke-dasharray': Math.max(len - gap, 0.1) + ' ' + (C - len + gap), 'stroke-dashoffset': -off,
        transform: 'rotate(-90 ' + mid + ' ' + mid + ')', style: 'stroke:' + colorOf(it.color || i + 1) + ';transition:opacity .15s'
      }, sv));
      off += len;
    });
    var ctr = html('div', 'pointer-events-none absolute inset-0 flex flex-col items-center justify-center text-center', wrap);
    var big = html('p', 'text-2xl font-semibold tabular-nums', ctr);
    var small = html('p', 'text-xs text-muted-foreground', ctr);
    function reset() { big.textContent = total.toLocaleString('id-ID'); small.textContent = c.total || 'Total'; segs.forEach(function (s) { s.style.opacity = 1; }); }
    function focus(i) { big.textContent = Number(I[i].value).toLocaleString('id-ID'); small.textContent = I[i].label; segs.forEach(function (s, j) { s.style.opacity = j === i ? 1 : 0.3; }); }
    var ul = html('ul', 'min-w-[14rem] max-w-full flex-1 space-y-2.5 text-sm', box);
    I.forEach(function (it, i) {
      var li = html('li', 'flex items-center gap-2', ul);
      var d = html('span', 'h-2.5 w-2.5 shrink-0 rounded-full', li); d.style.background = colorOf(it.color || i + 1);
      html('span', 'min-w-0 flex-1 truncate', li, it.label);
      html('span', 'font-medium tabular-nums', li, Number(it.value).toLocaleString('id-ID'));
      html('span', 'w-10 text-right text-xs tabular-nums text-muted-foreground', li, Math.round(it.value / total * 100) + '%');
      li.addEventListener('mouseenter', function () { focus(i); });
      li.addEventListener('mouseleave', reset);
      segs[i].addEventListener('mouseenter', function () { focus(i); });
      segs[i].addEventListener('mouseleave', reset);
    });
    reset();
  }

  /* ---------- radial ---------- */
  function radial(box, c) {
    var size = +c.size || 120, sw = 10, R = (size - sw) / 2, C = 2 * Math.PI * R, mid = size / 2;
    var v = Math.max(0, Math.min(100, +c.value || 0));
    box.innerHTML = '';
    box.style.cssText = 'position:relative;width:' + size + 'px;height:' + size + 'px';
    var sv = svg('svg', { viewBox: '0 0 ' + size + ' ' + size, width: size, height: size, role: 'img', 'aria-label': (c.label || 'Progres') + ' ' + v + '%' }, box);
    svg('circle', { cx: mid, cy: mid, r: R, fill: 'none', 'stroke-width': sw, style: 'stroke:hsl(var(--muted))' }, sv);
    svg('circle', { cx: mid, cy: mid, r: R, fill: 'none', 'stroke-width': sw, 'stroke-linecap': 'round', 'stroke-dasharray': (v / 100 * C) + ' ' + C, transform: 'rotate(-90 ' + mid + ' ' + mid + ')', style: 'stroke:' + colorOf(c.color || 'chart-1') }, sv);
    var ctr = html('div', 'absolute inset-0 flex flex-col items-center justify-center', box);
    html('p', 'text-xl font-semibold tabular-nums', ctr, v + '%');
    if (c.label) html('p', 'text-[11px] text-muted-foreground', ctr, c.label);
  }

  /* ---------- spark ---------- */
  function spark(box, c) {
    var V = c.values, n = V.length, W = Math.max(box.clientWidth, 80), H = +c.height || 36, col = colorOf(c.color || 'chart-1');
    var max = Math.max.apply(null, V), min = Math.min.apply(null, V), rng = max - min || 1;
    box.innerHTML = '';
    var sv = svg('svg', { viewBox: '0 0 ' + W + ' ' + H, role: 'img', 'aria-label': c.aria || 'Tren' }, box);
    sv.style.cssText = 'display:block;width:100%;height:' + H + 'px';
    if (c.kind === 'bar') {
      var bw = W / n;
      V.forEach(function (v, i) {
        var h = 4 + (v - min) / rng * (H - 6);
        svg('rect', { x: i * bw + bw * 0.2, y: H - h, width: bw * 0.6, height: h, rx: 2, style: 'fill:' + col + ';opacity:' + (i === n - 1 ? 1 : 0.35) }, sv);
      });
      return;
    }
    var pts = V.map(function (v, i) { return [(i / (n - 1)) * (W - 4) + 2, H - 3 - (v - min) / rng * (H - 8)]; });
    var d = curve(pts, H);
    if (c.kind === 'area') svg('path', { d: d + 'L' + (W - 2) + ',' + H + 'L2,' + H + 'Z', style: 'fill:' + col + ';opacity:.12' }, sv);
    svg('path', { d: d, fill: 'none', 'stroke-linecap': 'round', style: 'stroke:' + col + ';stroke-width:1.75' }, sv);
  }

  /* ---------- hbar ---------- */
  function hbar(box, c) {
    var I = c.items, max = Math.max.apply(null, I.map(function (i) { return +i.value; }));
    box.innerHTML = '';
    box.className += ' space-y-4';
    I.forEach(function (it) {
      var row = html('div', '', box);
      row.innerHTML = '<div class="mb-1.5 flex items-center justify-between text-sm"><span>' + it.label + '</span><span class="font-medium tabular-nums">' + fmt(it.value, c) + '</span></div>' +
        '<div class="progress"><div class="progress-bar" style="width:' + (it.value / max * 100) + '%;background:' + colorOf(it.color || c.color || 1) + '"></div></div>';
    });
  }

  var TYPES = { area: cartesian, line: cartesian, bar: cartesian, stacked: cartesian, donut: donut, radial: radial, spark: spark, hbar: hbar };
  function draw(box) { var c = cfgOf(box), f = TYPES[c.type]; if (f) f(box, c); }

  function init(scope) {
    (scope || document).querySelectorAll('[data-chart]').forEach(function (box) {
      if (box.__chart) return;
      box.__chart = true;
      draw(box);
      if (global.ResizeObserver && /^(area|line|bar|stacked|spark)$/.test(box.dataset.chart)) {
        var w = box.clientWidth;
        new ResizeObserver(function () {
          if (Math.abs(box.clientWidth - w) > 1) { w = box.clientWidth; draw(box); }
        }).observe(box);
      }
    });
  }

  global.Charts = { init: init, draw: draw };
})(window);

