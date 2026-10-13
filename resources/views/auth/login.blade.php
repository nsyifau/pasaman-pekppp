<x-layouts.guest title="Masuk">
    <div class="flex min-h-screen items-center justify-center bg-background p-4">
        <div class="w-full max-w-md">
            <div class="card p-7 sm:p-8">
                <div class="mb-7 text-center">
                    <span class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary text-primary-foreground shadow-lg">
                        <x-ui.icon name="shield" class="h-8 w-8" />
                    </span>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-primary">Pemerintah Kabupaten Pasaman</p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight">SIMPUL PASAMAN</h1>
                    <p class="text-sm text-muted-foreground">Sistem Integrasi dan Penilaian Pelayanan Publik</p>
                    <p class="mt-1 text-xs text-muted-foreground">Bagian Organisasi Setda Kabupaten Pasaman</p>
                </div>

                @if ($errors->any() || session('error'))
                    <x-ui.alert variant="danger" class="mb-5">
                        @if (session('error'))
                            {{ session('error') }}
                        @elseif ($errors->first('username') || $errors->first('password'))
                            {{ $errors->first('username') ?: $errors->first('password') }}
                        @else
                            Username atau password salah.
                        @endif
                    </x-ui.alert>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5" autocomplete="off">
                    @csrf

                    <x-ui.field label="Username" name="username" required>
                        <x-ui.input name="username" icon="user" :value="old('username')" required autofocus
                            autocomplete="username" placeholder="Masukkan username" />
                    </x-ui.field>

                    <x-ui.field label="Password" name="password" required>
                        <div class="relative" data-password-toggle>
                            <x-ui.input name="password" type="password" icon="lock" required
                                autocomplete="current-password" placeholder="Masukkan password" class="pr-10" />
                            <button type="button" data-password-toggle-btn aria-label="Tampilkan password"
                                class="absolute right-1 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-md text-muted-foreground hover:text-foreground">
                                <x-ui.icon name="eye" class="h-4 w-4" />
                            </button>
                        </div>
                    </x-ui.field>

                    <label class="inline-flex cursor-pointer items-center gap-2 text-sm">
                        <input type="checkbox" name="remember" value="1" class="rounded border-border">
                        <span class="text-muted-foreground">Ingat saya</span>
                    </label>

                    <x-ui.button type="submit" class="w-full">Masuk</x-ui.button>
                </form>
            </div>
            <p class="mt-4 text-center text-xs text-muted-foreground">
                Bagian Organisasi Setda Kabupaten Pasaman &copy; {{ date('Y') }}
            </p>
        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('[data-password-toggle]').forEach(function (wrap) {
                var input = wrap.querySelector('input');
                var btn = wrap.querySelector('[data-password-toggle-btn]');
                if (!input || !btn) return;
                btn.addEventListener('click', function () {
                    input.type = input.type === 'password' ? 'text' : 'password';
                });
            });
        </script>
    @endpush
</x-layouts.guest>
