@props(['title' => 'Dashboard'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-layouts.head :title="$title" />
</head>
<body data-layout="blank" class="font-sans text-sm antialiased">
    <x-icon.sprite />

    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </div>

    <div id="toasts" class="pointer-events-none fixed bottom-4 right-4 z-[70] flex w-full max-w-sm flex-col gap-2"
        aria-live="polite"></div>

    @stack('scripts')
</body>
</html>
