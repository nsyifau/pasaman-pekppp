@php
    $flashes = array_filter([
        'success' => session('success'),
        'error' => session('error'),
        'warning' => session('warning'),
        'status' => session('status'),
    ], fn ($value) => filled($value));

    // View error (mis. 404) dirender di luar middleware web sehingga $errors bisa tidak tersedia.
    $errorMessages = isset($errors) && $errors instanceof \Illuminate\Support\ViewErrorBag ? $errors->all() : [];
@endphp
@if (! empty($flashes) || ! empty($errorMessages))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var flashes = @json($flashes);
            var errors = @json($errorMessages);
            var titles = { success: 'Berhasil', error: 'Gagal', warning: 'Perhatian', status: 'Informasi' };
            function fire() {
                if (!window.App || !window.App.toast) { setTimeout(fire, 40); return; }
                Object.keys(flashes).forEach(function (key) {
                    App.toast({ title: titles[key] || 'Informasi', description: flashes[key], tone: key === 'status' ? 'info' : key });
                });
                if (errors.length) {
                    App.toast({ title: 'Periksa kembali isian Anda', description: errors.join(' '), tone: 'danger', duration: 8000 });
                }
            }
            fire();
        });
    </script>
@endif
