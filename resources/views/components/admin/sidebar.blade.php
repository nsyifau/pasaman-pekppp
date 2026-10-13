@php
    $user = auth()->user();
    $navGroups = [];

    $navGroups[] = [
        'label' => 'Navigasi Utama',
        'items' => [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'dashboard'],
        ],
    ];

    $instrumen = [];
    if ($user->isOpd()) {
        $instrumen[] = ['label' => 'F-01 Penilaian Mandiri', 'route' => 'f01.index', 'icon' => 'edit'];
    }
    if ($user->isAdmin()) {
        $instrumen[] = ['label' => 'F-02 Penilaian', 'route' => 'f02.index', 'icon' => 'check-circle'];
        $instrumen[] = ['label' => 'F-03 Penilaian Persepsi', 'route' => 'f03.index', 'icon' => 'message'];
    }
    if ($instrumen) {
        $navGroups[] = ['label' => 'Instrumen PEKPPP', 'items' => $instrumen];
    }

    $navGroups[] = [
        'label' => 'Laporan & Hasil',
        'items' => array_values(array_filter([
            ['label' => 'Peringkat IPP', 'route' => 'peringkat.index', 'icon' => 'star'],
            $user->isAdmin() ? ['label' => 'Laporan & Cetak', 'route' => 'laporan.index', 'icon' => 'file-text'] : null,
        ])),
    ];

    $master = [];
    if ($user->isAdmin()) {
        $master[] = ['label' => 'Evaluasi Unit', 'route' => 'evaluations.index', 'icon' => 'calendar'];
        $master[] = ['label' => 'Unit Layanan', 'route' => 'opds.index', 'icon' => 'layers'];
        $master[] = ['label' => 'Pengguna', 'route' => 'users.index', 'icon' => 'users'];
    }
    if ($master) {
        $navGroups[] = ['label' => 'Master Data', 'items' => $master];
    }

    $initials = collect(preg_split('/\s+/', trim($user->name ?? '')))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->implode('');
@endphp

<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col overflow-hidden border-r bg-sidebar" aria-label="Navigasi utama">
    <div class="p-3">
        <a href="{{ route('dashboard') }}" class="flex w-full items-center gap-2.5 rounded-md p-2 text-left hover:bg-accent focus-ring">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-primary text-primary-foreground"><x-ui.icon name="shield" /></span>
            <span class="sb-label min-w-0 flex-1 leading-tight">
                <span class="block truncate text-sm font-medium">{{ config('pekppp.brand.name') }}</span>
                <span class="block truncate text-xs text-muted-foreground">{{ config('pekppp.brand.description') }}</span>
            </span>
        </a>
    </div>

    <nav class="flex-1 space-y-5 overflow-y-auto overflow-x-hidden px-3 pb-3 pt-1">
        @foreach ($navGroups as $group)
            <div>
                <p class="sb-label mb-1 px-2 text-xs font-medium text-muted-foreground">{{ $group['label'] }}</p>
                <ul class="space-y-0.5">
                    @foreach ($group['items'] as $item)
                        <li><x-admin.nav-link :item="$item" /></li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    <div class="border-t p-3">
        <a href="{{ route('reset-password.form') }}" class="nav-link mb-1">
            <x-ui.icon name="key" class="h-4 w-4 shrink-0" /><span class="sb-label">Ganti Password</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link w-full">
                <x-ui.icon name="logout" class="h-4 w-4 shrink-0" /><span class="sb-label">Keluar</span>
            </button>
        </form>
        <div class="mt-2 flex items-center gap-2.5 rounded-md p-2">
            <span class="avatar avatar-sm">{{ $initials }}</span>
            <span class="sb-label min-w-0 flex-1 leading-tight">
                <span class="block truncate text-sm font-medium">{{ $user->name }}</span>
                <span class="block truncate text-xs text-muted-foreground">{{ \App\Models\User::roles()[$user->role] ?? $user->role }}</span>
            </span>
        </div>
    </div>
</aside>
