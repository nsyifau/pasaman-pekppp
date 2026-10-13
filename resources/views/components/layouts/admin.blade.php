@props(['title', 'group' => 'Platform'])
<!DOCTYPE html>
<html lang="id">
<head>
    <x-layouts.head :title="$title" />
</head>
<body data-sidebar="closed" class="font-sans text-sm antialiased">
<script>document.body.dataset.sidebar = window.innerWidth >= 1024 ? 'open' : 'closed';</script>
<div id="overlay" class="fixed inset-0 z-40 bg-black/50" aria-hidden="true"></div>
<x-admin.sidebar />

<div id="main" class="min-h-screen">
    <x-admin.header :title="$title" :group="$group" />
    <main id="page-root" class="mx-auto max-w-7xl space-y-6 p-4 sm:p-6">
        {{ $slot }}
    </main>
    <x-admin.footer />
</div>

<x-admin.customizer />
<div id="toasts" class="pointer-events-none fixed bottom-4 right-4 z-[70] flex w-[calc(100%-2rem)] max-w-sm flex-col gap-2" aria-live="polite" aria-atomic="true"></div>
<div id="app-loading" hidden role="alertdialog" aria-live="assertive" aria-busy="true" aria-labelledby="app-loading-title">
    <div class="flex w-full max-w-sm flex-col items-center gap-3 rounded-xl border bg-card px-8 py-7 text-center shadow-xl">
        <span class="spinner h-8 w-8 text-primary" aria-hidden="true"></span>
        <p id="app-loading-title" class="font-semibold" data-loading-text>Memproses...</p>
        <p class="text-sm text-muted-foreground">Mohon tunggu, jangan menutup halaman ini.</p>
    </div>
</div>
@include('partials.flash')
@stack('scripts')
</body>
</html>

