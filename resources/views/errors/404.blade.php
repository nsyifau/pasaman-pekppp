<x-layouts.guest title="Kesalahan 404">
<div class="relative flex min-h-screen flex-col items-center justify-center px-6 text-center">
  <button type="button" data-theme-toggle aria-label="Ganti tema" class="btn btn-outline btn-icon absolute right-4 top-4"><svg class="h-4 w-4" aria-hidden="true"><use class="js-theme-icon" href="{{ asset('icons.svg') }}#i-moon"/></svg></button>
  <p class="text-7xl font-semibold tracking-tight text-primary">404</p>
  <h1 class="mt-4 text-2xl font-semibold tracking-tight">Halaman tidak ditemukan</h1>
  <p class="mt-2 max-w-md text-muted-foreground">Alamat yang Anda tuju mungkin salah ketik, sudah dipindahkan, atau tidak lagi tersedia.</p>
  <div class="mt-6 flex gap-3">
    <a href="{{ route('home') }}" class="btn btn-primary"><x-ui.icon name="home" />Ke beranda</a>
    <a href="{{ route('login') }}" class="btn btn-outline"><x-ui.icon name="login" />Masuk</a>
  </div>
</div>
</x-layouts.guest>
