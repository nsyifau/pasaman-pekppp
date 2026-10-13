<dialog id="customizer" class="drawer" aria-labelledby="customizer-title">
    <div class="flex h-full flex-col">
        <div class="flex items-start justify-between gap-4 border-b p-5">
            <div><h2 id="customizer-title" class="font-semibold">Kustomisasi tampilan</h2><p class="text-sm text-muted-foreground">Pengaturan tersimpan di peramban.</p></div>
            <button type="button" class="btn btn-ghost btn-icon btn-sm" data-modal-close aria-label="Tutup"><x-ui.icon name="x" /></button>
        </div>
        <div class="flex-1 space-y-6 overflow-y-auto p-5">
            <section>
                <h3 class="label mb-3">Tema</h3>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" class="pref-btn" data-pref="theme" data-value="light" aria-pressed="false"><x-ui.icon name="sun" />Terang</button>
                    <button type="button" class="pref-btn" data-pref="theme" data-value="dark" aria-pressed="false"><x-ui.icon name="moon" />Gelap</button>
                    <button type="button" class="pref-btn" data-pref="theme" data-value="system" aria-pressed="false"><x-ui.icon name="monitor" />Sistem</button>
                </div>
            </section>
            <section>
                <h3 class="label mb-3">Warna aksen</h3>
                <div class="grid grid-cols-6 gap-3">
                    @foreach (config('pekppp.accents') as $accent)
                        <button type="button" class="swatch" style="background: {{ $accent['hex'] }}" data-pref="accent" data-value="{{ $accent['id'] }}" aria-label="{{ $accent['name'] }}" title="{{ $accent['name'] }}" aria-pressed="false"></button>
                    @endforeach
                </div>
            </section>
            <section>
                <h3 class="label mb-3">Radius sudut</h3>
                <div class="grid grid-cols-5 gap-2">
                    @foreach (config('pekppp.radii') as $radius)
                        <button type="button" class="pref-btn px-1 text-xs" data-pref="radius" data-value="{{ $radius['value'] }}" aria-pressed="false">{{ $radius['name'] }}</button>
                    @endforeach
                </div>
            </section>
            <section class="hidden lg:block">
                <h3 class="label mb-1">Sidebar</h3><p class="field-help mb-3">Mode ikon melebar saat kursor diarahkan ke sidebar.</p>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" class="pref-btn" data-pref="sbmode" data-value="full" aria-pressed="false">Penuh</button>
                    <button type="button" class="pref-btn" data-pref="sbmode" data-value="mini" aria-pressed="false">Ikon</button>
                </div>
            </section>
        </div>
        <div class="border-t p-5"><button type="button" class="btn btn-outline w-full" data-pref-reset><x-ui.icon name="refresh" />Atur ulang</button></div>
    </div>
</dialog>
