/*
 * Perilaku khusus aplikasi PEKPPP.
 * - [data-auto-submit] : kirim form induk saat select/date berubah
 *   (tetap bekerja dengan select ber-search Choices.js karena Choices
 *    memancarkan event 'change' yang bubble pada elemen select aslinya).
 */
(function () {
    'use strict';

    function bindAutoSubmit() {
        document.querySelectorAll('[data-auto-submit]').forEach(function (wrapper) {
            if (wrapper.__autoSubmit) return;
            wrapper.__autoSubmit = true;
            wrapper.querySelectorAll('select, input[type="date"]').forEach(function (el) {
                el.addEventListener('change', function () {
                    var form = el.closest('form');
                    if (form) form.requestSubmit ? form.requestSubmit() : form.submit();
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', bindAutoSubmit);

    /* Dialog konfirmasi berbasis Promise: App.confirm({...}).then(ok => ...) */
    function confirmDialog(options) {
        options = options || {};
        return new Promise(function (resolve) {
            var dialog = document.createElement('dialog');
            dialog.className = 'modal max-w-md';
            dialog.setAttribute('data-app-confirm', '');
            dialog.innerHTML =
                '<div class="p-6">' +
                '<h2 class="text-lg font-semibold">' + (options.title || 'Lanjutkan?') + '</h2>' +
                (options.description ? '<p class="mt-1 text-sm text-muted-foreground">' + options.description + '</p>' : '') +
                '<div class="mt-6 flex justify-end gap-2">' +
                '<button type="button" class="btn btn-outline" data-confirm-cancel>Batal</button>' +
                '<button type="button" class="btn btn-success" data-confirm-ok>' + (options.confirm || 'Ya, lanjutkan') + '</button>' +
                '</div></div>';
            document.body.appendChild(dialog);
            var settled = false;
            var finish = function (value) {
                if (settled) return;
                settled = true;
                resolve(value);
                if (dialog.open) dialog.close();
                dialog.remove();
            };
            dialog.querySelector('[data-confirm-ok]').addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                finish(true);
            });
            dialog.querySelector('[data-confirm-cancel]').addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                finish(false);
            });
            dialog.addEventListener('click', function (event) {
                if (event.target !== dialog) return;
                var box = dialog.getBoundingClientRect();
                var outside = event.clientX < box.left || event.clientX > box.right || event.clientY < box.top || event.clientY > box.bottom;
                if (outside) finish(false);
            });
            dialog.addEventListener('cancel', function (event) { event.preventDefault(); finish(false); });
            dialog.showModal();
        });
    }

    function setLoading(visible, message) {
        var el = document.getElementById('app-loading');
        if (!el) return;
        var text = el.querySelector('[data-loading-text]');
        if (text && message) text.textContent = message;
        if (visible) {
            el.hidden = false;
            el.setAttribute('data-open', '');
            document.body.style.overflow = 'hidden';
        } else {
            el.hidden = true;
            el.removeAttribute('data-open');
            document.body.style.overflow = '';
        }
    }

    window.App = window.App || {};
    window.App.confirm = confirmDialog;
    window.App.setLoading = setLoading;
})();
