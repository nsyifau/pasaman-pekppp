<x-layouts.admin title="Ganti Password" group="Akun">
    @php
        $user = auth()->user();
        $initials = collect(preg_split('/\s+/', trim($user->name ?? '')))
            ->filter()->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('');
    @endphp

    <x-ui.page-header title="Ganti Password"
        description="Perbarui password akun Anda secara berkala demi keamanan data penilaian." />

    <div class="mx-auto w-full max-w-2xl space-y-4">
        <div class="card flex items-center gap-4 p-6">
            <span class="avatar avatar-xl">{{ $initials }}</span>
            <div class="min-w-0">
                <h2 class="text-lg font-semibold">{{ $user->name }}</h2>
                <p class="text-sm text-muted-foreground">
                    {{ $user->username }} &middot; {{ \App\Models\User::roles()[$user->role] ?? $user->role }}
                </p>
                @if ($user->opd?->name)
                    <p class="text-sm text-muted-foreground">{{ $user->opd->name }}</p>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('reset-password.update') }}" autocomplete="off" class="card">
            @csrf
            @method('PUT')

            <div class="card-header gov-header-card rounded-t-xl">
                <h2 class="card-title text-white"><x-ui.icon name="lock" class="inline h-4 w-4" /> Ubah Password Akun</h2>
            </div>

            <div class="card-content space-y-4 pt-6">
                <x-ui.field label="Password Lama" name="current_password" required>
                    <x-ui.input name="current_password" type="password" required autocomplete="current-password" />
                </x-ui.field>

                <x-ui.field label="Password Baru" name="password" required
                    help="Minimal 8 karakter dan harus berbeda dari password lama.">
                    <x-ui.input name="password" type="password" required minlength="8" autocomplete="new-password" />
                </x-ui.field>

                <x-ui.field label="Konfirmasi Password Baru" name="password_confirmation" required>
                    <x-ui.input name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" />
                </x-ui.field>
            </div>

            <div class="card-footer justify-between">
                <p class="text-xs text-muted-foreground">Setelah password diganti, gunakan password baru pada login berikutnya.</p>
                <div class="flex gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline">Batal</a>
                    <x-ui.button type="submit" variant="success">Simpan Password Baru</x-ui.button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>
