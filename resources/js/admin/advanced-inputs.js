/* Input lanjutan untuk halaman showcase. Semua state hanya hidup di browser. */
(function () {
  'use strict';

  document.querySelectorAll('[data-combobox]').forEach(function (root) {
    var input = root.querySelector('[data-combobox-input]');
    var value = root.querySelector('[data-combobox-value]');
    var list = root.querySelector('[data-combobox-list]');
    var empty = root.querySelector('[data-combobox-empty]');
    var options = Array.from(root.querySelectorAll('[data-combobox-option]'));
    var visible = options.slice();
    var active = -1;

    function setActive(index) {
      active = index;
      options.forEach(function (option) { option.setAttribute('aria-selected', String(visible[index] === option)); });
      if (index < 0) { input.removeAttribute('aria-activedescendant'); return; }
      input.setAttribute('aria-activedescendant', visible[index].id);
      visible[index].scrollIntoView({ block: 'nearest' });
    }
    function filter() {
      var query = input.value.trim().toLocaleLowerCase('id-ID');
      visible = options.filter(function (option) {
        var match = option.textContent.toLocaleLowerCase('id-ID').includes(query);
        option.hidden = !match;
        return match;
      });
      empty.hidden = visible.length > 0;
      setActive(-1);
    }
    function open() { filter(); list.hidden = false; input.setAttribute('aria-expanded', 'true'); }
    function close() { list.hidden = true; input.setAttribute('aria-expanded', 'false'); setActive(-1); }
    function choose(option) {
      input.value = option.textContent.trim();
      value.value = option.dataset.value;
      close();
      input.focus();
      value.dispatchEvent(new Event('change', { bubbles: true }));
    }

    input.addEventListener('focus', open);
    input.addEventListener('input', function () { value.value = ''; open(); });
    input.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') { if (!list.hidden) { event.preventDefault(); close(); } return; }
      if (event.key === 'Tab') { close(); return; }
      if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
        event.preventDefault();
        if (list.hidden) open();
        if (visible.length) setActive((active + (event.key === 'ArrowDown' ? 1 : visible.length - 1)) % visible.length);
      }
      if (event.key === 'Enter' && !list.hidden && active >= 0) {
        event.preventDefault(); choose(visible[active]);
      }
    });
    options.forEach(function (option) {
      option.addEventListener('mousedown', function (event) { event.preventDefault(); });
      option.addEventListener('click', function () { choose(option); });
    });
    document.addEventListener('pointerdown', function (event) { if (!root.contains(event.target)) close(); });
    input.addEventListener('blur', function () { queueMicrotask(function () { if (!root.contains(document.activeElement)) close(); }); });
  });

  document.querySelectorAll('[data-date-range]').forEach(function (root) {
    var from = root.querySelector('[data-range-from]');
    var to = root.querySelector('[data-range-to]');
    var status = root.querySelector('[data-range-status]');
    var presets = Array.from(root.querySelectorAll('[data-range-days]'));
    function dateValue(date) {
      return [date.getFullYear(), String(date.getMonth() + 1).padStart(2, '0'), String(date.getDate()).padStart(2, '0')].join('-');
    }
    function update() {
      var invalid = Boolean(from.value && to.value && from.value > to.value);
      [from, to].forEach(function (input) {
        input.setAttribute('aria-invalid', String(invalid));
        if (input._flatpickr && input._flatpickr.altInput) input._flatpickr.altInput.setAttribute('aria-invalid', String(invalid));
      });
      status.className = invalid ? 'field-error' : 'field-help';
      status.textContent = invalid ? 'Tanggal awal tidak boleh setelah tanggal akhir.' :
        from.value && to.value ? 'Periode: ' + from.value + ' hingga ' + to.value + '.' :
        'Pilih tanggal atau gunakan preset.';
    }
    presets.forEach(function (button) {
      button.addEventListener('click', function () {
        var last = new Date(), first = new Date(last);
        first.setDate(last.getDate() - Number(button.dataset.rangeDays) + 1);
        if (from._flatpickr) from._flatpickr.setDate(dateValue(first), false);
        else from.value = dateValue(first);
        if (to._flatpickr) to._flatpickr.setDate(dateValue(last), false);
        else to.value = dateValue(last);
        presets.forEach(function (item) { item.setAttribute('aria-pressed', String(item === button)); });
        update();
      });
    });
    root.querySelector('[data-range-clear]').addEventListener('click', function () {
      if (from._flatpickr) from._flatpickr.clear(false); else from.value = '';
      if (to._flatpickr) to._flatpickr.clear(false); else to.value = '';
      presets.forEach(function (button) { button.setAttribute('aria-pressed', 'false'); });
      update(); (from._flatpickr?.altInput || from).focus();
    });
    [from, to].forEach(function (input) { input.addEventListener('change', function () {
      presets.forEach(function (button) { button.setAttribute('aria-pressed', 'false'); }); update();
    }); });
  });

  document.querySelectorAll('[data-filter-chips]').forEach(function (root) {
    var chips = Array.from(root.querySelectorAll('[data-filter-chip]'));
    var summary = root.querySelector('[data-filter-summary]');
    function update() {
      var chosen = chips.slice(1).filter(function (chip) { return chip.getAttribute('aria-pressed') === 'true'; });
      chips[0].setAttribute('aria-pressed', String(chosen.length === 0));
      summary.textContent = chosen.length ? 'Status aktif: ' + chosen.map(function (chip) { return chip.textContent.trim(); }).join(', ') + '.' : 'Menampilkan semua status.';
    }
    chips.forEach(function (chip, index) { chip.addEventListener('click', function () {
      if (index === 0) chips.slice(1).forEach(function (item) { item.setAttribute('aria-pressed', 'false'); });
      else chip.setAttribute('aria-pressed', String(chip.getAttribute('aria-pressed') !== 'true'));
      update();
    }); });
  });

  document.querySelectorAll('[data-file-preview]').forEach(function (root) {
    var input = root.querySelector('[data-file-input]');
    var drop = root.querySelector('[data-file-drop]');
    var result = root.querySelector('[data-file-result]');
    var image = root.querySelector('[data-file-image]');
    var icon = root.querySelector('[data-file-icon]');
    var error = root.querySelector('[data-file-error]');
    var objectUrl = null;
    function clear(resetInput) {
      if (objectUrl) URL.revokeObjectURL(objectUrl);
      objectUrl = null;
      if (resetInput !== false) input.value = '';
      image.removeAttribute('src');
      result.hidden = true; error.hidden = true;
    }
    function show(file) {
      clear(false);
      if (!file) return;
      var validType = ['image/png', 'image/jpeg', 'application/pdf'].includes(file.type);
      var maxBytes = Number(root.dataset.maxMb || 5) * 1024 * 1024;
      if (!validType || file.size > maxBytes) {
        error.textContent = !validType ? 'Gunakan berkas PNG, JPG, atau PDF.' : 'Ukuran berkas melebihi batas ' + root.dataset.maxMb + ' MB.';
        error.hidden = false; input.value = ''; return;
      }
      root.querySelector('[data-file-name]').textContent = file.name;
      root.querySelector('[data-file-size]').textContent = (file.size / 1024 / 1024).toLocaleString('id-ID', { maximumFractionDigits: 2 }) + ' MB · pratinjau lokal';
      image.hidden = !file.type.startsWith('image/');
      icon.hidden = !image.hidden;
      if (!image.hidden) { objectUrl = URL.createObjectURL(file); image.src = objectUrl; }
      result.hidden = false;
    }
    input.addEventListener('change', function () { show(input.files[0]); });
    root.querySelector('[data-file-remove]').addEventListener('click', function () { clear(); });
    drop.addEventListener('dragover', function (event) { event.preventDefault(); drop.classList.add('is-dragging'); });
    drop.addEventListener('dragleave', function () { drop.classList.remove('is-dragging'); });
    drop.addEventListener('drop', function (event) {
      event.preventDefault(); drop.classList.remove('is-dragging');
      if (!event.dataTransfer.files.length) return;
      input.files = event.dataTransfer.files;
      show(input.files[0]);
    });
  });

  document.querySelectorAll('[data-table-state-demo]').forEach(function (root) {
    var buttons = Array.from(root.querySelectorAll('[data-table-state-set]'));
    var views = Array.from(root.querySelectorAll('[data-table-state-view]'));
    function show(state) {
      buttons.forEach(function (button) { button.setAttribute('aria-pressed', String(button.dataset.tableStateSet === state)); });
      views.forEach(function (view) { view.hidden = view.dataset.tableStateView !== state; });
    }
    buttons.forEach(function (button) { button.addEventListener('click', function () { show(button.dataset.tableStateSet); }); });
    root.querySelectorAll('[data-table-state-action]').forEach(function (button) {
      button.addEventListener('click', function () { show(button.dataset.tableStateAction); });
    });
  });

  document.querySelectorAll('[data-demo-form]').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      event.preventDefault();
      if (!form.reportValidity()) return;
      window.App.toast({ title: form.dataset.demoMessage, tone: 'info' });
    });
  });
})();
