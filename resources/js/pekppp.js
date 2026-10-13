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
            dialog.addEventListener('click', function (event) {
                if (event.target.closest('[data-confirm-cancel]') || event.target === dialog) { finish(false); return; }
                if (event.target.closest('[data-confirm-ok]')) { finish(true); }
            });
            dialog.addEventListener('cancel', function (event) { event.preventDefault(); finish(false); });
            dialog.showModal();
        });
    }

    window.App = window.App || {};
    window.App.confirm = confirmDialog;
})();
