<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Identitas aplikasi
    |--------------------------------------------------------------------------
    */
    'name' => env('APP_NAME', 'PEKPPP Pasaman'),

    'brand' => [
        'name' => 'PEKPPP Pasaman',
        'workspace' => 'Kabupaten Pasaman',
        'description' => 'Penilaian Kinerja Pelayanan Publik',
        'footer' => 'Bagian Organisasi Setda Kabupaten Pasaman',
        'logo' => 'logo-pasaman.png',
    ],

    /*
    |--------------------------------------------------------------------------
    | Kop dokumen cetak (Berita Acara & Rekapitulasi)
    |   Diambil dari Keputusan Bupati Pasaman tentang PEKPPP.
    |--------------------------------------------------------------------------
    */
    'document' => [
        'instansi' => 'Pemerintah Kabupaten Pasaman',
        'unit' => 'Bagian Organisasi',
        'sekretariat' => 'Sekretariat Daerah',
        'alamat' => 'Jl. Jenderal Sudirman, Lubuk Sikaping, Kabupaten Pasaman, Sumatera Barat',
        'logo' => 'logo-pasaman.png',
    ],

    /*
    |--------------------------------------------------------------------------
    | Penanda tangan dokumen cetak (default)
    |   Nilai default tempat; nama/jabatan/NIP diisi admin pada halaman
    |   Berita Acara dan disimpan per sesi evaluasi.
    |--------------------------------------------------------------------------
    */
    'ttd' => [
        'tempat' => 'Lubuk Sikaping',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pilihan warna aksen (customizer tampilan)
    |--------------------------------------------------------------------------
    */
    'accents' => [
        ['id' => 'indigo', 'name' => 'Indigo', 'hex' => 'hsl(243 75% 59%)'],
        ['id' => 'emerald', 'name' => 'Emerald', 'hex' => 'hsl(160 84% 39%)'],
        ['id' => 'rose', 'name' => 'Rose', 'hex' => 'hsl(347 77% 50%)'],
        ['id' => 'amber', 'name' => 'Amber', 'hex' => 'hsl(38 92% 50%)'],
        ['id' => 'sky', 'name' => 'Sky', 'hex' => 'hsl(199 89% 48%)'],
        ['id' => 'violet', 'name' => 'Violet', 'hex' => 'hsl(262 83% 58%)'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pilihan radius sudut (customizer tampilan)
    |--------------------------------------------------------------------------
    */
    'radii' => [
        ['name' => 'Tajam', 'value' => '0.25'],
        ['name' => 'Kecil', 'value' => '0.375'],
        ['name' => 'Sedang', 'value' => '0.5'],
        ['name' => 'Besar', 'value' => '0.75'],
        ['name' => 'Bulat', 'value' => '1'],
    ],

];
