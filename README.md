# Kenanga Admin — Laravel Blade Starter Kit

Kenanga Admin adalah starter kit dashboard Laravel 12 yang dislicing dari template HTML `kenanga-admin-template`. Seluruh halaman utama sudah memakai Blade layout, Blade components, Vite, Tailwind CSS 4, JavaScript modular, tema terang/gelap, pilihan aksen, dan layout responsif.

Panduan lengkap tersedia di situs VitePress dalam `docs/`: jalankan `npm run docs:dev` setelah `npm install`. Build dokumentasi dengan `npm run docs:build`. Dokumentasi mencakup referensi komponen `<x-ui.*>` dan cara menghubungkan halaman demo ke backend Laravel.

## Menjalankan proyek

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm run dev
php artisan serve
```

Untuk build produksi:

```bash
npm run build
php artisan optimize
```

## Struktur utama

- `resources/views/components/layouts` — layout admin, guest, dan blank.
- `resources/views/components/admin` — sidebar, header, footer, navigasi, dan customizer.
- `resources/views/components/ui` — komponen UI reusable seperti card, button, field, badge, modal, drawer, combobox, date range, file preview, table state, dan chart.
- `resources/views/admin` — dashboard, analitik, dan pengaturan akun.
- `resources/views/showcase` — katalog komponen untuk referensi saat membangun proyek baru.
- `resources/views/examples/records` — contoh alur daftar, detail, tambah, dan edit yang sepenuhnya frontend.
- `resources/js/admin` — grafik SVG dan interaksi dashboard.
- `resources/css/app.css` — design tokens, tema, dan component styles.
- `config/kenanga.php` — branding, navigasi, pilihan tema, serta data demo.

## Halaman bawaan

| URL | Route name | Keterangan |
| --- | --- | --- |
| `/dashboard` | `admin.dashboard` | Dashboard utama |
| `/analytics` | `admin.analytics` | Analitik |
| `/settings` | `admin.settings` | Pengaturan akun |
| `/components/*` | `showcase.*` | Katalog komponen |
| `/components/filters` | `showcase.filters` | Combobox, rentang tanggal, filter chip, dan unggah berkas |
| `/components/table-states` | `showcase.table-states` | State tabel siap, memuat, kosong, tanpa hasil, dan error |
| `/components/charts` | `showcase.charts` | Variasi grafik SVG dan state kosong |
| `/examples/records` | `examples.records.index` | Contoh daftar dengan pencarian, filter, dan paginasi |
| `/examples/records/new` | `examples.records.create` | Contoh form tambah |
| `/examples/records/detail` | `examples.records.show` | Contoh halaman detail |
| `/examples/records/edit` | `examples.records.edit` | Contoh form edit |
| `/login` | `login` | Tampilan masuk |
| `/demo/404` | `demo.404` | Demo halaman 404 |

Halaman login dan semua contoh data masih berupa presentational UI. Form contoh hanya menampilkan toast; tidak menyimpan data. Hubungkan autentikasi, validasi server, dan persistensi sesuai kebutuhan proyek nyata.

## Kustomisasi starter kit

Identitas dasar dapat diubah melalui `.env`:

```dotenv
ADMIN_NAME="Kenanga Admin"
ADMIN_BRAND_NAME="Kenanga Admin"
ADMIN_WORKSPACE="Kopi Kenanga"
ADMIN_WORKSPACE_DESCRIPTION="Toko online"
ADMIN_SHOWCASE=true
```

Menu sidebar didefinisikan di `config/kenanga.php`. Tambahkan route, view, lalu masukkan item baru ke array `navigation`. Untuk menyembunyikan katalog komponen dan halaman contoh pada aplikasi produksi, gunakan `ADMIN_SHOWCASE=false`.

Contoh halaman baru:

```blade
<x-layouts.admin title="Produk" group="Katalog">
    <x-ui.page-header
        title="Produk"
        description="Kelola produk toko Anda."
    />

    <x-ui.card>
        Konten halaman
    </x-ui.card>
</x-layouts.admin>
```

## Tema dan preferensi

Customizer di kanan header mendukung mode sistem/terang/gelap, 12 warna aksen, ukuran radius, serta mode sidebar penuh/mini. Preferensi disimpan di `localStorage`, sehingga tidak memerlukan backend.

## Validasi

```bash
php artisan test
php artisan view:cache
npm run build
```

Sprite ikon lokal berada di `public/icons.svg`; starter kit tidak bergantung pada CDN untuk ikon maupun font Inter.
