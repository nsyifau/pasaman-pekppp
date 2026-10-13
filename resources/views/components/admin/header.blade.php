@props(['title', 'group' => 'Platform'])
@php
    $user = auth()->user();
    $initials = collect(preg_split('/\s+/', trim($user->name ?? '')))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->implode('');
@endphp
<header class="app-header sticky z-30 flex h-14 items-center gap-2 border-b bg-card px-4">
    <img src="{{ asset(config('pekppp.brand.logo')) }}" alt="Logo Kabupaten Pasaman" class="hidden h-8 w-auto shrink-0 object-contain sm:block">
    <button id="sidebarToggle" type="button" aria-label="Buka atau tutup sidebar" class="btn btn-ghost btn-icon btn-sm text-muted-foreground"><x-ui.icon name="panel" /></button>
    <div class="mx-1 hidden h-4 w-px bg-border sm:block"></div>
    <nav aria-label="Breadcrumb" class="hidden min-w-0 items-center gap-1.5 text-sm sm:flex">
        <span class="text-muted-foreground">{{ $group }}</span>
        <x-ui.icon name="chevron-right" class="h-3.5 w-3.5 text-muted-foreground" />
        <span class="truncate font-medium">{{ $title }}</span>
    </nav>
    <div class="ml-auto flex items-center gap-2">
        @if ($user?->isOpd() && $user->opd)
            <span class="badge badge-soft hidden md:inline-flex">{{ $user->opd->name }}</span>
        @endif
        <button type="button" data-modal-open="#customizer" aria-label="Kustomisasi tampilan" data-tip="Kustomisasi" data-tip-pos="bottom left" class="btn btn-outline btn-icon"><x-ui.icon name="palette" /></button>
        <button type="button" data-theme-toggle aria-label="Ganti tema" class="btn btn-outline btn-icon"><svg class="h-4 w-4" aria-hidden="true"><use class="js-theme-icon" href="{{ asset('icons.svg') }}#i-moon"/></svg></button>
        <div class="relative" data-dropdown>
            <button type="button" data-dropdown-toggle aria-expanded="false" aria-label="Menu pengguna" class="btn btn-outline gap-2 pl-1.5 pr-2.5">
                <span class="avatar avatar-sm">{{ $initials }}</span>
                <span class="hidden max-w-32 truncate text-sm font-medium lg:block">{{ $user->name }}</span>
                <x-ui.icon name="chevron-down" class="h-4 w-4 text-muted-foreground" />
            </button>
            <div class="menu hidden w-64" role="menu">
                <div class="menu-label truncate">{{ $user->name }}</div>
                <div class="px-3 pb-2 text-xs text-muted-foreground">
                    {{ \App\Models\User::roles()[$user->role] ?? $user->role }}
                    @if ($user->opd) · {{ $user->opd->name }} @endif
                </div>
                <div class="menu-sep"></div>
                <a href="{{ route('reset-password.form') }}" role="menuitem" class="menu-item"><x-ui.icon name="key" class="h-4 w-4" />Ganti Password</a>
                <div class="menu-sep"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" role="menuitem" class="menu-item w-full text-danger"><x-ui.icon name="logout" class="h-4 w-4" />Keluar</button>
                </form>
            </div>
        </div>
    </div>
</header>
