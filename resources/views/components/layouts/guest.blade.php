@props(['title'])
<!DOCTYPE html>
<html lang="id">
<head>
    <x-layouts.head :title="$title" />
</head>
<body class="font-sans text-sm antialiased">
<main id="page-root">
    {{ $slot }}
</main>
<div id="toasts" class="pointer-events-none fixed bottom-4 right-4 z-[70] flex w-full max-w-sm flex-col gap-2" aria-live="polite" aria-atomic="true"></div>
@include('partials.flash')
@stack('scripts')
</body>
</html>

