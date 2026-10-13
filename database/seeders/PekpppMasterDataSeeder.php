<?php

namespace Database\Seeders;

use App\Models\Aspect;
use App\Models\Indicator;
use App\Models\F01Question;
use App\Models\Opd;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PekpppMasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Superadmin / Penilai
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator PEKPPP',
                'email' => 'admin@pasamankab.go.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'nip' => '198501012010011001',
                'phone' => '081234567890',
            ]
        );

        // 2. Unit Layanan / OPD beserta akun operatornya.
        // Urutan mengikuti daftar resmi; nomor urut dipakai untuk penomoran password
        // (pekppp-26Pas1 .. pekppp-26Pas55). Lihat KREDENSIAL-AKUN-UNIT-LAYANAN.txt.
        $units = [
            // Organisasi Perangkat Daerah (OPD)
            ['Dinas Komunikasi dan Informatika', 'opd', 'diskominfo'],
            ['Dinas Kependudukan dan Pencatatan Sipil', 'opd', 'disdukcapil'],
            ['Sekretariat Daerah', 'opd', 'setda'],
            ['Dinas Sosial', 'opd', 'dinsos'],
            ['Badan Perencanaan dan Pembangunan Daerah', 'opd', 'bappeda'],
            ['Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu', 'opd', 'dpmptsp'],
            ['Dinas Pendidikan', 'opd', 'disdik'],
            ['Badan Kepegawaian dan Pengembangan Sumber Daya Manusia', 'opd', 'bkpsdm'],
            ['Dinas Pekerjaan Umum dan Penataan Ruang', 'opd', 'dpupr'],
            ['Dinas Kesehatan', 'opd', 'dinkes'],
            ['Inspektorat', 'opd', 'inspektorat'],
            ['Dinas Perpustakaan dan Kearsipan', 'opd', 'disperpusip'],
            ['Satuan Polisi Pamong Praja dan Pemadam Kebakaran', 'opd', 'satpolpp'],
            ['Dinas Perikanan', 'opd', 'diskan'],
            ['Badan Keuangan Daerah', 'opd', 'bakuda'],
            ['Dinas Pemberdayaan Perempuan, Perlindungan Anak, Pengendalian Penduduk dan Keluarga Berencana', 'opd', 'dp3akb'],
            ['Dinas Koperasi, Usaha Kecil Menengah, Perdagangan dan Tenaga Kerja', 'opd', 'diskopdag'],
            ['Dinas Pemberdayaan Masyarakat', 'opd', 'dpm'],
            ['Badan Kesatuan Bangsa dan Politik', 'opd', 'kesbangpol'],
            ['Badan Penanggulangan Bencana Daerah', 'opd', 'bpbd'],
            ['Dinas Pertanian', 'opd', 'distan'],
            ['Sekretariat Dewan Perwakilan Rakyat Daerah', 'opd', 'setdprd'],
            ['Dinas Perumahan Rakyat dan Kawasan Pemukiman, Perhubungan dan Lingkungan Hidup', 'opd', 'disperkim'],
            ['Dinas Pemuda Olahraga dan Kebudayaan', 'opd', 'dispora'],
            // Puskesmas
            ['Puskesmas Kumpulan', 'puskesmas', 'pkm_kumpulan'],
            ['Puskesmas Lubuk Sikaping', 'puskesmas', 'pkm_lubuksikaping'],
            // Rumah Sakit
            ['Rumah Sakit Umum Daerah Tuanku Imam Bonjol', 'opd', 'rsud_imambonjol'],
            // Puskesmas
            ['Puskesmas Bonjol', 'puskesmas', 'pkm_bonjol'],
            ['Puskesmas Ladang Panjang', 'puskesmas', 'pkm_ladangpanjang'],
            ['Puskesmas Sundatar', 'puskesmas', 'pkm_sundatar'],
            ['Puskesmas Simpang Tonang', 'puskesmas', 'pkm_simpangtonang'],
            ['Puskesmas Koto Rajo', 'puskesmas', 'pkm_kotorajo'],
            ['Puskesmas Lansap Kadap', 'puskesmas', 'pkm_lansapkadap'],
            ['Puskesmas Cubadak', 'puskesmas', 'pkm_cubadak'],
            ['Puskesmas Pintu Padang', 'puskesmas', 'pkm_pintupadang'],
            ['Puskesmas Pegang Baru', 'puskesmas', 'pkm_pegangbaru'],
            ['Puskesmas Kuamang', 'puskesmas', 'pkm_kuamang'],
            ['Puskesmas Tapus', 'puskesmas', 'pkm_tapus'],
            ['Puskesmas Silayang', 'puskesmas', 'pkm_silayang'],
            ['Puskesmas Simpang Alahan Mati', 'puskesmas', 'pkm_simpangalahanmati'],
            ['Puskesmas Rao', 'puskesmas', 'pkm_rao'],
            // Perusahaan Daerah & Rumah Sakit
            ['Perusahaan Daerah Air Minum', 'opd', 'pdam'],
            ['Rumah Sakit Umum Daerah Tuanku Rao', 'opd', 'rsud_tuankurao'],
            // Kecamatan
            ['Kecamatan Padang Gelugur', 'kecamatan', 'kec_padangelugur'],
            ['Kecamatan Panti', 'kecamatan', 'kec_panti'],
            ['Kecamatan Lubuk Sikaping', 'kecamatan', 'kec_lubuksikaping'],
            ['Kecamatan Simpang Alahan Mati', 'kecamatan', 'kec_simpangalahanmati'],
            ['Kecamatan Rao Selatan', 'kecamatan', 'kec_raoselatan'],
            ['Kecamatan Rao', 'kecamatan', 'kec_rao'],
            ['Kecamatan Duo Koto', 'kecamatan', 'kec_duokoto'],
            ['Kecamatan Bonjol', 'kecamatan', 'kec_bonjol'],
            ['Kecamatan Mapat Tunggul Selatan', 'kecamatan', 'kec_mapatunggulselatan'],
            ['Kecamatan Mapat Tunggul', 'kecamatan', 'kec_mapatunggul'],
            ['Kecamatan Tigo Nagari', 'kecamatan', 'kec_tigonagari'],
            ['Kecamatan Rao Utara', 'kecamatan', 'kec_raoutara'],
        ];

        foreach ($units as $index => [$name, $category, $username]) {
            $opd = Opd::updateOrCreate(
                ['name' => $name],
                ['category' => $category, 'is_active' => true],
            );

            User::updateOrCreate(
                ['username' => $username],
                [
                    'name' => 'Operator ' . $name,
                    'email' => $username . '@pasamankab.go.id',
                    'password' => Hash::make('pekppp-26Pas' . ($index + 1)),
                    'role' => 'opd',
                    'opd_id' => $opd->id,
                ],
            );
        }

        // 3. SEED ASPEK & INDIKATOR F-02 SESUAI DOKUMEN SK PEKPPP PASAMAN
        $aspectDefinitions = [
            [
                'name' => 'Kebijakan Pelayanan',
                'code' => 'kebijakan_pelayanan',
                'weight' => 24.00,
                'order' => 1,
                'indicators' => [
                    [
                        'number' => 1,
                        'code' => '1.a.Ak',
                        'name' => 'Tersedia Standar Pelayanan (SP) sesuai dengan ketentuan peraturan perundang-undangan yang berlaku',
                        'description' => 'Evaluasi ketersediaan, kelengkapan 14 komponen SP, pelibatan masyarakat, penetapan, dan monev SP.',
                        'rubrics' => [
                            0 => 'Tidak tersedia Standar Pelayanan.',
                            1 => 'Tersedia SP namun tidak memenuhi 14 komponen.',
                            2 => 'Tersedia SP yang memenuhi 14 komponen.',
                            3 => 'Tersedia SP yang memenuhi 14 komponen dan dilakukan penetapan.',
                            4 => 'Tersedia SP yang memenuhi 14 komponen, melibatkan masyarakat dalam penyusunan SP, dan dilakukan penetapan.',
                            5 => 'Tersedia SP yang memenuhi 14 komponen, melibatkan masyarakat dalam penyusunan SP, dilakukan penetapan, dan dilakukan monev.',
                        ],
                    ],
                    [
                        'number' => 2,
                        'code' => '2.a.P',
                        'name' => 'Proses penyusunan dan perubahan SP telah melibatkan unsur masyarakat',
                        'description' => 'Evaluasi pelibatan unsur masyarakat (pengguna layanan, akademisi/ahli, instansi terkait, OMS, media massa).',
                        'rubrics' => [
                            0 => 'Penyusunan SP tanpa melibatkan unsur masyarakat dan pihak terkait (stakeholder).',
                            1 => 'Penyusunan SP telah melibatkan minimal 1 unsur masyarakat.',
                            2 => 'Penyusunan SP telah melibatkan minimal 2 unsur masyarakat.',
                            3 => 'Penyusunan SP telah melibatkan minimal 3 unsur masyarakat.',
                            4 => 'Penyusunan SP telah melibatkan minimal 4 unsur masyarakat.',
                            5 => 'Penyusunan SP telah melibatkan lebih dari 4 unsur masyarakat.',
                        ],
                    ],
                    [
                        'number' => 3,
                        'code' => '3.a.T',
                        'name' => 'Jumlah media publikasi untuk komponen service delivery',
                        'description' => 'Publikasi seluruh komponen service delivery di berbagai media serta terhubung dengan SIPPN.',
                        'rubrics' => [
                            0 => 'Tidak ada publikasi SP.',
                            1 => 'Tersedia publikasi SP hanya sebagian dari komponen service delivery.',
                            2 => 'Tersedia publikasi SP seluruh komponen service delivery pada 2 atau lebih media namun belum di SIPPN.',
                            3 => 'Tersedia publikasi SP seluruh komponen service delivery pada 2 media publikasi dan pada SIPP Nasional.',
                            4 => 'Tersedia publikasi SP seluruh komponen service delivery pada 3 media publikasi dan pada SIPP Nasional.',
                            5 => 'Tersedia publikasi SP seluruh komponen service delivery pada 4 atau lebih media publikasi dan pada SIPP Nasional.',
                        ],
                    ],
                    [
                        'number' => 4,
                        'code' => '4.a.K',
                        'name' => 'Telah dilakukan peninjauan ulang secara berkala terhadap Standar Pelayanan',
                        'description' => 'Evaluasi periode peninjauan ulang Standar Pelayanan (1-3 tahun sekali).',
                        'rubrics' => [
                            0 => 'Tidak dilaksanakan peninjauan ulang secara berkala terhadap Standar Pelayanan.',
                            1 => 'Dilakukan peninjauan ulang 3 tahun atau lebih terhadap seluruh jenis layanan.',
                            2 => 'Dilakukan peninjauan ulang 2 tahun sekali terhadap sebagian jenis layanan.',
                            3 => 'Dilakukan peninjauan ulang 2 tahun sekali terhadap seluruh jenis layanan.',
                            4 => 'Dilakukan peninjauan ulang 1 tahun sekali atau lebih cepat terhadap sebagian jenis layanan.',
                            5 => 'Dilakukan peninjauan ulang 1 tahun sekali atau lebih cepat terhadap seluruh jenis layanan.',
                        ],
                    ],
                    [
                        'number' => 5,
                        'code' => '5.a.K',
                        'name' => 'Pemenuhan siklus Maklumat Pelayanan (ketersediaan, penetapan, dan publikasi)',
                        'description' => 'Ketersediaan, legalitas isi, dan publikasi Maklumat Pelayanan di media cetak/elektronik.',
                        'rubrics' => [
                            0 => 'Tidak tersedia Maklumat Pelayanan.',
                            1 => 'Tersedia Maklumat Pelayanan namun belum ditetapkan.',
                            2 => 'Tersedia Maklumat Pelayanan yang sudah ditetapkan namun isinya belum sesuai.',
                            3 => 'Tersedia Maklumat Pelayanan yang sudah ditetapkan dan isinya telah sesuai ketentuan.',
                            4 => 'Tersedia Maklumat Pelayanan yang sudah ditetapkan, sesuai, dan dipublikasikan pada media non-elektronik.',
                            5 => 'Tersedia Maklumat Pelayanan yang sudah ditetapkan, sesuai, dan dipublikasikan pada media non-elektronik dan elektronik.',
                        ],
                    ],
                    [
                        'number' => 6,
                        'code' => '6.a.B',
                        'name' => 'SKM yang dilaksanakan sesuai dengan Peraturan Menteri PANRB Nomor 14 Tahun 2017',
                        'description' => 'Pelaksanaan Survei Kepuasan Masyarakat, publikasi, dan tindak lanjut rekomendasi SKM.',
                        'rubrics' => [
                            0 => 'Belum melaksanakan SKM.',
                            1 => 'Sudah melaksanakan SKM namun tidak sesuai dengan Peraturan Menteri PANRB.',
                            2 => 'Sudah melaksanakan SKM sesuai dengan Peraturan Menteri PANRB.',
                            3 => 'Sudah melaksanakan SKM sesuai PermenPANRB dan dipublikasikan pada media non-elektronik.',
                            4 => 'Sudah melaksanakan SKM sesuai PermenPANRB dan dipublikasikan pada media non-elektronik dan elektronik.',
                            5 => 'Sudah melaksanakan SKM sesuai PermenPANRB, dipublikasikan di media non-elektronik & elektronik, serta ditindaklanjuti.',
                        ],
                    ],
                    [
                        'number' => 7,
                        'code' => '7.a.T',
                        'name' => 'Jumlah media publikasi hasil SKM',
                        'description' => 'Banyaknya kanal/media publikasi yang memuat hasil SKM kepada masyarakat luas.',
                        'rubrics' => [
                            0 => 'Tidak dipublikasikan.',
                            1 => 'SKM dipublikasikan pada 1 (satu) media publikasi.',
                            2 => 'SKM dipublikasikan pada 2 (dua) media publikasi.',
                            3 => 'SKM dipublikasikan pada 3 (tiga) media publikasi.',
                            4 => 'SKM dipublikasikan pada 4 (empat) media publikasi.',
                            5 => 'SKM dipublikasikan pada lebih dari 4 (empat) media publikasi.',
                        ],
                    ],
                    [
                        'number' => 8,
                        'code' => '8.a.Ak',
                        'name' => 'Persentase rencana tindak lanjut hasil SKM yang telah selesai ditindaklanjuti',
                        'description' => 'Realisasi pelaksanaan rencana tindak lanjut rekomendasi hasil SKM.',
                        'rubrics' => [
                            0 => 'Tidak ada rencana tindak lanjut SKM.',
                            1 => 'Ada rencana tindak lanjut tapi belum dilaksanakan.',
                            2 => 'Tindak lanjut hasil SKM dilaksanakan kurang dari 30%.',
                            3 => 'Tindak lanjut hasil SKM dilaksanakan 30% - 80%.',
                            4 => 'Tindak lanjut hasil SKM dilaksanakan lebih dari 80%.',
                            5 => 'Tindak lanjut hasil SKM dilaksanakan 100%.',
                        ],
                    ],
                    [
                        'number' => 9,
                        'code' => '9.a.Ak',
                        'name' => 'Kecepatan tindak lanjut hasil SKM seluruh jenis pelayanan',
                        'description' => 'Kecepatan penyelesaian tindak lanjut sejak laporan SKM diterbitkan.',
                        'rubrics' => [
                            0 => 'Rekomendasi hasil SKM tidak ditindaklanjuti.',
                            1 => 'Rekomendasi hasil SKM ditindaklanjuti seluruhnya 1 tahun setelah laporan SKM terbit.',
                            2 => 'Rekomendasi hasil SKM ditindaklanjuti seluruhnya 9 bulan setelah laporan SKM terbit.',
                            3 => 'Rekomendasi hasil SKM ditindaklanjuti seluruhnya 6 bulan setelah laporan SKM terbit.',
                            4 => 'Rekomendasi hasil SKM ditindaklanjuti seluruhnya 3 bulan setelah laporan SKM terbit.',
                            5 => 'Rekomendasi hasil SKM ditindaklanjuti seluruhnya 1 bulan setelah laporan SKM terbit.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Profesionalisme SDM',
                'code' => 'profesionalisme_sdm',
                'weight' => 25.00,
                'order' => 2,
                'indicators' => [
                    [
                        'number' => 10,
                        'code' => '10.b.As',
                        'name' => 'Tersedia waktu pelayanan yang memudahkan pengguna layanan',
                        'description' => 'Kebijakan jam pelayanan tanpa jeda, shift istirahat, penambahan waktu layanan, atau layanan 24 jam.',
                        'rubrics' => [
                            0 => 'Tidak memiliki kebijakan jam pelayanan/kerja.',
                            1 => 'Memiliki kebijakan jam pelayanan/kerja.',
                            2 => 'Memiliki kebijakan jam pelayanan/kerja dan 1 unsur kemudahan lainnya.',
                            3 => 'Memiliki kebijakan jam pelayanan/kerja dan 2 unsur kemudahan lainnya.',
                            4 => 'Memiliki kebijakan jam pelayanan/kerja dan 3 unsur kemudahan lainnya.',
                            5 => 'Memiliki kebijakan jam pelayanan/kerja dan 4 atau lebih unsur kemudahan lainnya.',
                        ],
                    ],
                    [
                        'number' => 11,
                        'code' => '11.b.K',
                        'name' => 'Tersedia Kode Etik dan Kode Perilaku Pelaksana dan/atau Budaya Pelayanan di lingkungan instansi',
                        'description' => 'Aturan kode etik meliputi hak/kewajiban, larangan KKN, larangan diskriminasi, sanksi, dan penghargaan.',
                        'rubrics' => [
                            0 => 'Tidak tersedia aturan kode etik dan kode perilaku.',
                            1 => 'Aturan kode etik hanya meliputi nilai dasar hak dan kewajiban.',
                            2 => 'Aturan kode etik meliputi hak dan kewajiban serta 1 unsur lainnya.',
                            3 => 'Aturan kode etik meliputi hak dan kewajiban serta 2 unsur lainnya.',
                            4 => 'Aturan kode etik meliputi hak dan kewajiban serta 3 unsur lainnya.',
                            5 => 'Aturan kode etik meliputi hak dan kewajiban serta 4 unsur lainnya secara lengkap.',
                        ],
                    ],
                    [
                        'number' => 12,
                        'code' => '12.b.Ak',
                        'name' => 'Tersedia mekanisme unit kerja dibangun untuk menjaga dan meningkatkan motivasi kerja Pelaksana pelayanan',
                        'description' => 'Mekanisme penghargaan, pelatihan/diklat, beasiswa, konseling, capacity building.',
                        'rubrics' => [
                            0 => 'Tidak tersedia mekanisme peningkatan motivasi kerja.',
                            1 => 'Tersedia 1 jenis mekanisme peningkatan motivasi kerja.',
                            2 => 'Tersedia 2 jenis mekanisme peningkatan motivasi kerja.',
                            3 => 'Tersedia 3 jenis mekanisme peningkatan motivasi kerja.',
                            4 => 'Tersedia 4 jenis mekanisme peningkatan motivasi kerja.',
                            5 => 'Tersedia lebih dari 4 jenis mekanisme peningkatan motivasi kerja.',
                        ],
                    ],
                    [
                        'number' => 13,
                        'code' => '13.b.K',
                        'name' => 'Tersedia kriteria pemberian penghargaan bagi pegawai pelaksana layanan yang berprestasi',
                        'description' => 'Kriteria pemberian penghargaan (kehadiran, kinerja, kerja sama, inovasi, penampilan, bebas komplain).',
                        'rubrics' => [
                            0 => 'Tidak ada pemberian penghargaan.',
                            1 => 'Ada penghargaan hanya berdasarkan 1-2 unsur kecuali kinerja.',
                            2 => 'Ada penghargaan berdasarkan 3-5 unsur kecuali kinerja.',
                            3 => 'Ada penghargaan berdasarkan 1-2 unsur termasuk kinerja.',
                            4 => 'Ada penghargaan berdasarkan 3-4 unsur termasuk kinerja.',
                            5 => 'Ada penghargaan berdasarkan 5-6 unsur termasuk kinerja.',
                        ],
                    ],
                    [
                        'number' => 14,
                        'code' => '14.b.K',
                        'name' => 'Tersedia pelaksana yang menerapkan budaya pelayanan',
                        'description' => 'Penerapan seragam, name tag, logo/pin, 5S (senyum, salam, sapa, sopan, santun), dan nilai budaya/motto.',
                        'rubrics' => [
                            0 => 'Tidak menerapkan budaya layanan.',
                            1 => 'Pelaksana menerapkan 1 unsur budaya pelayanan.',
                            2 => 'Pelaksana menerapkan 2 unsur budaya pelayanan.',
                            3 => 'Pelaksana menerapkan 3 unsur budaya pelayanan.',
                            4 => 'Pelaksana menerapkan 4 unsur budaya pelayanan.',
                            5 => 'Pelaksana menerapkan 5 unsur budaya pelayanan secara utuh.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Sarana dan Prasarana',
                'code' => 'sarana_prasarana',
                'weight' => 18.00,
                'order' => 3,
                'indicators' => [
                    [
                        'number' => 15,
                        'code' => '15.c.K',
                        'name' => 'Tersedia tempat parkir dengan fasilitas pendukung yang memadai',
                        'description' => 'Marka Roda 2/4 terpisah, petugas parkir, karcis parkir, CCTV, penitipan helm, kanopi/atap pelindung.',
                        'rubrics' => [
                            0 => 'Tidak tersedia tempat parkir.',
                            1 => 'Tersedia tempat parkir dan memiliki 1 fasilitas parkir.',
                            2 => 'Tersedia tempat parkir dan memiliki 2 fasilitas parkir.',
                            3 => 'Tersedia tempat parkir dan memiliki 3 fasilitas parkir.',
                            4 => 'Tersedia tempat parkir dan memiliki 4 fasilitas parkir.',
                            5 => 'Tersedia tempat parkir dan memiliki 5 atau lebih fasilitas parkir.',
                        ],
                    ],
                    [
                        'number' => 16,
                        'code' => '16.c.As',
                        'name' => 'Tersedia ruang tunggu dengan fasilitas wajib dan pelengkap',
                        'description' => 'Fasilitas wajib (kursi + pendingin/sirkulasi) + pelengkap (mesin antrian monitor, TV, bacaan, charger booth, wifi, air minum).',
                        'rubrics' => [
                            0 => 'Tidak tersedia fasilitas apapun.',
                            1 => 'Tersedia fasilitas wajib.',
                            2 => 'Tersedia fasilitas wajib dan 1 fasilitas pelengkap.',
                            3 => 'Tersedia fasilitas wajib dan 2 fasilitas pelengkap.',
                            4 => 'Tersedia fasilitas wajib dan 3 fasilitas pelengkap.',
                            5 => 'Tersedia fasilitas wajib dan 4 atau lebih fasilitas pelengkap.',
                        ],
                    ],
                    [
                        'number' => 17,
                        'code' => '17.c.As',
                        'name' => 'Tersedia sarana toilet pengguna layanan yang layak pakai',
                        'description' => 'Pemisahan pria/wanita, wastafel berfungsi, toiletries, air bersih, monev kebersihan toilet.',
                        'rubrics' => [
                            0 => 'Tidak tersedia toilet pengguna layanan.',
                            1 => 'Toilet pengguna layanan dengan 1 kondisi.',
                            2 => 'Toilet pengguna layanan dengan 2 kondisi.',
                            3 => 'Toilet pengguna layanan dengan 3 kondisi.',
                            4 => 'Toilet pengguna layanan dengan 4 kondisi.',
                            5 => 'Toilet pengguna layanan dengan lebih dari 4 kondisi.',
                        ],
                    ],
                    [
                        'number' => 18,
                        'code' => '18.c.K',
                        'name' => 'Tersedia sarana prasarana bagi pengguna layanan kelompok rentan',
                        'description' => 'Kursi roda/kruk, ramp jalan landai, toilet khusus disabilitas, loket khusus, ruang tunggu prioritas, guiding block, parkir disabilitas, braille, bermain anak, laktasi, dll.',
                        'rubrics' => [
                            0 => 'Tidak tersedia sarana prasarana kelompok rentan.',
                            1 => 'Tersedia 1-3 sarana prasarana bagi kelompok rentan.',
                            2 => 'Tersedia 4-6 sarana prasarana bagi kelompok rentan.',
                            3 => 'Tersedia 7-9 sarana prasarana bagi kelompok rentan.',
                            4 => 'Tersedia 10-12 sarana prasarana bagi kelompok rentan.',
                            5 => 'Tersedia 13 atau lebih sarana prasarana bagi kelompok rentan.',
                        ],
                    ],
                    [
                        'number' => 19,
                        'code' => '19.c.As',
                        'name' => 'Tersedia sarana prasarana penunjang',
                        'description' => 'Fotocopy/ATK, P3K, APAR, kantin, tempat ibadah, area merokok, jalur evakuasi/titik kumpul, tempat sampah terpilah, CCTV.',
                        'rubrics' => [
                            0 => 'Tidak tersedia fasilitas penunjang.',
                            1 => 'Tersedia 1 fasilitas penunjang.',
                            2 => 'Tersedia 2 fasilitas penunjang.',
                            3 => 'Tersedia 3 fasilitas penunjang.',
                            4 => 'Tersedia 4 fasilitas penunjang.',
                            5 => 'Tersedia 5 atau lebih fasilitas penunjang.',
                        ],
                    ],
                    [
                        'number' => 20,
                        'code' => '20.c.B',
                        'name' => 'Sarana Front Office (FO) bagian Informasi di unit layanan',
                        'description' => 'Petugas khusus, meja/kursi informasi, display layar info, bahan cetak brosur, buku register tamu.',
                        'rubrics' => [
                            0 => 'Tidak tersedia sarana FO informasi.',
                            1 => 'Tersedia sarana FO dengan 1 fasilitas.',
                            2 => 'Tersedia sarana FO dengan 2 fasilitas.',
                            3 => 'Tersedia sarana FO dengan 3 fasilitas.',
                            4 => 'Tersedia sarana FO dengan 4 fasilitas.',
                            5 => 'Tersedia sarana FO dengan 5 atau lebih fasilitas.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Sistem Informasi Pelayanan Publik',
                'code' => 'sistem_informasi',
                'weight' => 11.00,
                'order' => 4,
                'indicators' => [
                    [
                        'number' => 21,
                        'code' => '21.d.T',
                        'name' => 'Tersedia sistem informasi pelayanan publik untuk informasi publik',
                        'description' => 'Ketersediaan informasi non elektronik (lisan, cetak), elektronik (display, website), hingga terhubung SIPPN Nasional.',
                        'rubrics' => [
                            0 => 'Tidak tersedia sistem informasi pelayanan publik.',
                            1 => 'Tersedia sistem informasi non-elektronik media lisan (pusat informasi).',
                            2 => 'Tersedia SIPP non-elektronik (lisan, papan pengumuman, dan cetak).',
                            3 => 'Tersedia SIPP elektronik belum online (e-kiosk, TV display).',
                            4 => 'Tersedia SIPP berbasis online/website.',
                            5 => 'SIPP telah online/website dan terhubung serta menginput layanan ke SIPP Nasional.',
                        ],
                    ],
                    [
                        'number' => 22,
                        'code' => '22.d.B',
                        'name' => 'Tersedia sistem informasi pelayanan publik pendukung operasional pelayanan',
                        'description' => 'SIPP didukung antrian daring, SP4N LAPOR, SKM, penilaian kinerja, FAQ/keuangan layanan.',
                        'rubrics' => [
                            0 => 'Tidak tersedia sistem pendukung operasional pelayanan.',
                            1 => 'SIPP didukung unsur data pendukung (standar pelayanan).',
                            2 => 'SIPP didukung data pelayanan dan pengelolaan pengaduan SP4N-LAPOR.',
                            3 => 'SIPP didukung data pelayanan, SP4N-LAPOR, dan SKM.',
                            4 => 'SIPP didukung data pelayanan, SP4N-LAPOR, SKM, dan penilaian kinerja petugas.',
                            5 => 'SIPP didukung data pelayanan, SP4N-LAPOR, SKM, penilaian kinerja, FAQ/keuangan.',
                        ],
                    ],
                    [
                        'number' => 23,
                        'code' => '23.d.As',
                        'name' => 'Kualitas penggunaan SIPP Elektronik (Website/Aplikasi)',
                        'description' => 'Kemudahan operasional, kemudahan akses, kompatibilitas perangkat, domain resmi pemerintah .go.id, kemudahan navigasi.',
                        'rubrics' => [
                            0 => 'SIPP Elektronik tidak terhubung secara daring.',
                            1 => 'SIPP berbasis website/aplikasi mudah dioperasikan.',
                            2 => 'SIPP mudah dioperasikan dan mudah diakses.',
                            3 => 'SIPP mudah dioperasikan, diakses, dan kompatibel multiplatform.',
                            4 => 'SIPP mudah dioperasikan, diakses, kompatibel, serta domain resmi .go.id.',
                            5 => 'SIPP mudah dioperasikan, diakses, kompatibel, domain .go.id, dan navigasi jelas.',
                        ],
                    ],
                    [
                        'number' => 24,
                        'code' => '24.d.T',
                        'name' => 'Pemutakhiran data dan informasi kanal digital',
                        'description' => 'Frekuensi pemutakhiran konten informasi kanal digital (tahunan, semesteran, bulanan, mingguan, harian).',
                        'rubrics' => [
                            0 => 'Data dan informasi pelayanan publik tidak pernah dimutakhirkan.',
                            1 => 'Pemutakhiran dilakukan tahunan.',
                            2 => 'Pemutakhiran dilakukan semesteran (per 6 bulan).',
                            3 => 'Pemutakhiran dilakukan bulanan.',
                            4 => 'Pemutakhiran dilakukan mingguan.',
                            5 => 'Pemutakhiran dilakukan secara harian dan detail/lengkap.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Konsultasi dan Pengaduan',
                'code' => 'konsultasi_pengaduan',
                'weight' => 10.00,
                'order' => 5,
                'indicators' => [
                    [
                        'number' => 25,
                        'code' => '25.e.P',
                        'name' => 'Tersedia sarana konsultasi dan pengaduan secara tatap muka yang berkualitas',
                        'description' => 'Kotak saran, ruang konsultasi khusus, petugas khusus, air minum/snack, buku register, publikasi alur pengaduan.',
                        'rubrics' => [
                            0 => 'Tidak tersedia sarana konsultasi dan pengaduan tatap muka.',
                            1 => 'Sarana konsultasi dan pengaduan dengan 1 fasilitas.',
                            2 => 'Sarana konsultasi dan pengaduan dengan 2 fasilitas.',
                            3 => 'Sarana konsultasi dan pengaduan dengan 3 fasilitas.',
                            4 => 'Sarana konsultasi dan pengaduan dengan 4 fasilitas.',
                            5 => 'Sarana konsultasi dan pengaduan dengan 5 atau lebih fasilitas.',
                        ],
                    ],
                    [
                        'number' => 26,
                        'code' => '26.e.P',
                        'name' => 'Tersedia sarana dan media konsultasi serta pengaduan yang bisa dimanfaatkan semua lapisan masyarakat',
                        'description' => 'Saluran offline terpisah dari FO, serta saluran daring multiplatform dan terintegrasi SP4N-LAPOR!.',
                        'rubrics' => [
                            0 => 'Tidak ada sarana dan petugas konsultasi/pengaduan.',
                            1 => 'Hanya media offline menyatu dengan front office.',
                            2 => 'Hanya media offline terpisah dari front office.',
                            3 => 'Media offline menyatu front office dan tersedia secara online.',
                            4 => 'Media offline terpisah front office dan tersedia secara online.',
                            5 => 'Media offline terpisah front office dan terhubung langsung dengan SP4N-LAPOR!.',
                        ],
                    ],
                    [
                        'number' => 27,
                        'code' => '27.e.Ak',
                        'name' => 'Tersedia akuntabilitas hasil konsultasi dan/atau pengaduan',
                        'description' => 'Arsip dokumentasi, laporan pengaduan, pelaksanaan monev berkala, bukti tindak lanjut, dan publikasi rekapitulasi ke publik.',
                        'rubrics' => [
                            0 => 'Tidak ada dokumentasi konsultasi/pengaduan.',
                            1 => 'Terdapat dokumentasi yang diarsipkan.',
                            2 => 'Dokumentasi diarsipkan dan dituangkan dalam laporan.',
                            3 => 'Dokumentasi diarsipkan, dilaporkan, dan dilakukan monev.',
                            4 => 'Dokumentasi dilaporkan, dimonev, dan ditindaklanjuti.',
                            5 => 'Dokumentasi dilaporkan, dimonev, ditindaklanjuti, dan dipublikasikan luas.',
                        ],
                    ],
                    [
                        'number' => 28,
                        'code' => '28.e.Ak',
                        'name' => 'Tersedia tindak lanjut atas konsultasi dan pengaduan dari semua lapisan masyarakat',
                        'description' => 'Persentase tindak lanjut konsultasi/pengaduan yang tuntas melalui SP4N-LAPOR! maupun kanal internal.',
                        'rubrics' => [
                            0 => 'Tidak ada pengaduan yang ditindaklanjuti.',
                            1 => '< 50% pengaduan ditindaklanjuti non-SP4N LAPOR.',
                            2 => '≥ 50% pengaduan ditindaklanjuti non-SP4N LAPOR.',
                            3 => '< 50% pengaduan SP4N-LAPOR ditindaklanjuti hingga tuntas.',
                            4 => '50% - 90% pengaduan SP4N-LAPOR ditindaklanjuti hingga tuntas.',
                            5 => '≥ 90% pengaduan SP4N-LAPOR ditindaklanjuti hingga selesai.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Inovasi Pelayanan Publik',
                'code' => 'inovasi_pelayanan',
                'weight' => 12.00,
                'order' => 6,
                'indicators' => [
                    [
                        'number' => 29,
                        'code' => '29.f.B',
                        'name' => 'Penciptaan Inovasi Pelayanan Publik',
                        'description' => 'Tahapan inovasi pelayanan publik dari gagasan, uji coba, masukan masyarakat, hingga implementasi berlandaskan dokumen penetapan.',
                        'rubrics' => [
                            0 => 'Tidak tersedia inovasi pelayanan publik.',
                            1 => 'Inovasi masih dalam bentuk ide/gagasan.',
                            2 => 'Inovasi sudah dilakukan uji coba.',
                            3 => 'Inovasi sudah diuji coba dan mendapatkan masukan masyarakat.',
                            4 => 'Inovasi sudah diimplementasikan namun belum ada dokumen penetapan.',
                            5 => 'Inovasi sudah diimplementasikan dan didukung dengan dokumen penetapan resmi.',
                        ],
                    ],
                    [
                        'number' => 30,
                        'code' => '30.f.B',
                        'name' => 'Sumber daya yang mendukung keberlanjutan Inovasi Pelayanan Publik',
                        'description' => 'Dukungan regulasi penetapan, anggaran inovasi, sarana prasarana, SDM pelaksana, dan kolaborasi lintas stakeholder.',
                        'rubrics' => [
                            0 => 'Tidak memenuhi kondisi sumber daya apapun.',
                            1 => 'Terpenuhi 1 kondisi sumber daya pendukung inovasi.',
                            2 => 'Terpenuhi 2 kondisi sumber daya pendukung inovasi.',
                            3 => 'Terpenuhi 3 kondisi sumber daya pendukung inovasi.',
                            4 => 'Terpenuhi 4 kondisi sumber daya pendukung inovasi.',
                            5 => 'Terpenuhi 5 kondisi sumber daya pendukung inovasi secara menyeluruh.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Pertanyaan Tambahan',
                'code' => 'pertanyaan_tambahan',
                'weight' => 0.00,
                'order' => 7,
                'indicators' => [
                    [
                        'number' => 31,
                        'code' => 'tambahan.antrian',
                        'name' => 'Tersedia sistem antrian untuk menunjang pelayanan',
                        'description' => 'Ketersediaan fasilitas antrian: pengeras suara, pemandu, kelompok antrian, pendaftaran online, nomor antrian, monitor antrian.',
                        'rubrics' => [
                            0 => 'Tidak tersedia sistem antrian.',
                            1 => 'Tersedia sistem antrian dengan 1 fasilitas.',
                            2 => 'Tersedia sistem antrian dengan 2 fasilitas.',
                            3 => 'Tersedia sistem antrian dengan 3 fasilitas.',
                            4 => 'Tersedia sistem antrian dengan 4 fasilitas.',
                            5 => 'Tersedia sistem antrian dengan lebih dari 4 fasilitas.',
                        ],
                    ],
                ],
            ],
        ];

        $indicatorMap = [];

        foreach ($aspectDefinitions as $aData) {
            $aspect = Aspect::updateOrCreate(
                ['code' => $aData['code']],
                [
                    'name' => $aData['name'],
                    'weight' => $aData['weight'],
                    'order' => $aData['order'],
                ]
            );

            foreach ($aData['indicators'] as $iData) {
                $indicator = Indicator::updateOrCreate(
                    [
                        'aspect_id' => $aspect->id,
                        'number' => $iData['number'],
                    ],
                    [
                        'code' => $iData['code'],
                        'name' => $iData['name'],
                        'description' => $iData['description'],
                        'rubric_scales' => $iData['rubrics'],
                    ]
                );
                $indicatorMap[$iData['number']] = $indicator->id;
            }
        }

        // 4. SEED 90 BUTIR PERTANYAAN/JAWABAN F-01 BESERTA LINK GOOGLE DRIVE
        $this->seedF01Questions($indicatorMap);
    }

    private function seedF01Questions(array $map): void
    {
        // Rincian 90 P/J riil sesuai instrumen F-01 dalam PDF SK Pekppp Pasaman:
        // Kebijakan Pelayanan (31 P/J)
        // SDM (9 P/J)
        // Sarpras (9 P/J)
        // SIPP (17 P/J)
        // Pengaduan (14 P/J)
        // Inovasi (8 P/J)
        // Tambahan (2 P/J)
        $questions = [
            // === ASPEK 1: KEBIJAKAN PELAYANAN (31 P/J) ===
            // Indikator 1
            [
                'indicator_number' => 1,
                'question_code' => '1.a',
                'question_text' => 'Apakah sudah dibuat Standar Pelayanan, baik sebagian ataupun seluruh jenis pelayanan?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Daftar jenis pelayanan yang diselenggarakan & SK Dokumen Standar Pelayanan (Upload ke Google Drive lalu masukkan tautannya)',
            ],
            [
                'indicator_number' => 1,
                'question_code' => '1.a.1',
                'question_text' => 'Berapa Jumlah Jenis Pelayanan yang dimiliki oleh Unit Saudara?',
                'question_type' => 'number',
                'evidence_hint' => 'Daftar rincian jenis pelayanan',
            ],
            [
                'indicator_number' => 1,
                'question_code' => '1.a.2',
                'question_text' => 'Berapa Jumlah Jenis Standar Pelayanan yang telah dibuat?',
                'question_type' => 'number',
                'evidence_hint' => 'Daftar SK Standar Pelayanan',
            ],
            [
                'indicator_number' => 1,
                'question_code' => '1.b',
                'question_text' => 'Apakah Standar Pelayanan yang disusun telah memenuhi 14 komponen?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Dokumen Standar Pelayanan 14 Komponen',
            ],
            [
                'indicator_number' => 1,
                'question_code' => '1.c',
                'question_text' => 'Apakah dalam proses penyusunan Standar Pelayanan telah melibatkan unsur masyarakat?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Berita Acara, Foto kegiatan FKP, Notulen rapat',
            ],
            [
                'indicator_number' => 1,
                'question_code' => '1.d',
                'question_text' => 'Apakah Standar Pelayanan telah ditetapkan oleh pimpinan unit/instansi?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Surat Keputusan (SK) Penetapan Standar Pelayanan',
            ],
            [
                'indicator_number' => 1,
                'question_code' => '1.e',
                'question_text' => 'Apakah Standar Pelayanan yang dimiliki dilakukan monev secara berkala?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Laporan Pelaksanaan Monev / Forum Konsultasi Publik',
            ],

            // Indikator 2 (6 P/J)
            [
                'indicator_number' => 2,
                'question_code' => '2.a',
                'question_text' => 'Apakah dalam penyusunan SP telah melibatkan unsur masyarakat?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Berita acara, daftar hadir, notulen dan foto FKP',
            ],
            [
                'indicator_number' => 2,
                'question_code' => '2.b.1',
                'question_text' => 'Pelibatan Unsur: Masyarakat Pengguna Layanan',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Daftar hadir perwakilan pengguna layanan',
            ],
            [
                'indicator_number' => 2,
                'question_code' => '2.b.2',
                'question_text' => 'Pelibatan Unsur: Ahli / Praktisi / Akademisi',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Daftar hadir ahli/akademisi',
            ],
            [
                'indicator_number' => 2,
                'question_code' => '2.b.3',
                'question_text' => 'Pelibatan Unsur: Instansi Terkait',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Daftar hadir perwakilan instansi terkait',
            ],
            [
                'indicator_number' => 2,
                'question_code' => '2.b.4',
                'question_text' => 'Pelibatan Unsur: Organisasi Masyarakat Sipil (OMS)',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Daftar hadir perwakilan OMS/LSM/Ormas',
            ],
            [
                'indicator_number' => 2,
                'question_code' => '2.b.5',
                'question_text' => 'Pelibatan Unsur: Media Massa',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Daftar hadir perwakilan wartawan/media',
            ],

            // Indikator 3 (8 P/J)
            [
                'indicator_number' => 3,
                'question_code' => '3.a',
                'question_text' => 'Apakah Standar Pelayanan dipublikasikan kepada masyarakat?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Foto atau screenshot publikasi',
            ],
            [
                'indicator_number' => 3,
                'question_code' => '3.b',
                'question_text' => 'Apakah SP yang dipublikasikan mencakup seluruh 6 komponen service delivery?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak, hanya sebagian'],
                'evidence_hint' => 'Tautan foto display komponen persyaratan, biaya, waktu, alur, dll',
            ],
            [
                'indicator_number' => 3,
                'question_code' => '3.c.1',
                'question_text' => 'Publikasi Media: Media Cetak / Non Elektronik (brosur, leaflet, papan pengumuman)',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Foto brosur/banner/papan pengumuman',
            ],
            [
                'indicator_number' => 3,
                'question_code' => '3.c.2',
                'question_text' => 'Publikasi Media: Media Elektronik (layar TV monitor / E-Kios)',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Foto display TV antrian/informasi',
            ],
            [
                'indicator_number' => 3,
                'question_code' => '3.c.3',
                'question_text' => 'Publikasi Media: Media Sosial (Instagram, Facebook, WA, dll)',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Screenshot postingan media sosial resmi',
            ],
            [
                'indicator_number' => 3,
                'question_code' => '3.c.4',
                'question_text' => 'Publikasi Media: Website / Subsite Resmi Unit',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Link webpage & screenshot menu standar pelayanan',
            ],
            [
                'indicator_number' => 3,
                'question_code' => '3.c.5',
                'question_text' => 'Publikasi Media: Aplikasi Khusus yang Bisa Diunduh',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Tautan aplikasi Playstore/Appstore/APK',
            ],
            [
                'indicator_number' => 3,
                'question_code' => '3.c.6',
                'question_text' => 'Publikasi Media: SIPP Nasional (SIPPN)',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Screenshot akun unit pada sippn.menpan.go.id',
            ],

            // Indikator 4 (1 P/J)
            [
                'indicator_number' => 4,
                'question_code' => '4.a',
                'question_text' => 'Bagaimana pelaksanaan peninjauan ulang secara berkala terhadap Standar Pelayanan?',
                'question_type' => 'radio',
                'options' => [
                    'Tidak dilaksanakan peninjauan ulang secara berkala',
                    'Dilakukan peninjauan ulang 3 tahun atau lebih terhadap seluruh jenis layanan',
                    'Dilakukan peninjauan ulang 2 tahun atau lebih terhadap sebagian jenis layanan',
                    'Dilakukan peninjauan ulang 2 tahun atau lebih cepat terhadap seluruh jenis layanan',
                    'Dilakukan peninjauan ulang 1 tahun atau lebih cepat terhadap sebagian jenis layanan',
                    'Dilakukan peninjauan ulang 1 tahun atau lebih cepat terhadap seluruh jenis layanan',
                ],
                'evidence_hint' => 'BA FKP peninjauan SP, laporan rencana aksi & foto kegiatan',
            ],

            // Indikator 5 (4 P/J)
            [
                'indicator_number' => 5,
                'question_code' => '5.a',
                'question_text' => 'Apakah tersedia Maklumat Pelayanan yang ditetapkan?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Dokumen Pengesahan Maklumat Pelayanan',
            ],
            [
                'indicator_number' => 5,
                'question_code' => '5.b',
                'question_text' => 'Apakah isi maklumat pelayanan sesuai dengan peraturan perundangan yang berlaku?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Foto redaksi isi maklumat (janji pelayanan & kompensasi/sanksi)',
            ],
            [
                'indicator_number' => 5,
                'question_code' => '5.c.1',
                'question_text' => 'Apakah Maklumat Pelayanan dipublikasikan pada media Non Elektronik?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Foto banner/papan maklumat di ruang pelayanan',
            ],
            [
                'indicator_number' => 5,
                'question_code' => '5.c.2',
                'question_text' => 'Apakah Maklumat Pelayanan dipublikasikan pada media Elektronik/Website?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Screenshot maklumat di website/media sosial',
            ],

            // Indikator 6 (6 P/J)
            [
                'indicator_number' => 6,
                'question_code' => '6.a',
                'question_text' => 'Apakah Unit Lokus telah melaksanakan Survei Kepuasan Masyarakat (SKM)?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Laporan hasil SKM & instrumen SKM',
            ],
            [
                'indicator_number' => 6,
                'question_code' => '6.b',
                'question_text' => 'Berapa kali dalam satu tahun dilaksanakan SKM?',
                'question_type' => 'number',
                'evidence_hint' => 'Bukti periode pelaksanaan SKM (triwulanan/semesteran/tahunan)',
            ],
            [
                'indicator_number' => 6,
                'question_code' => '6.c',
                'question_text' => 'Berapa nilai rata-rata SKM periode terakhir (Nilai Konversi 100)?',
                'question_type' => 'text',
                'evidence_hint' => 'Sertifikat/Piagam/Laporan nilai IKM konversi',
            ],
            [
                'indicator_number' => 6,
                'question_code' => '6.d',
                'question_text' => 'Apakah pelaksanaan SKM sesuai dengan PermenPANRB No 14 Tahun 2017?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Metodologi dan formulir kuesioner 9 unsur SKM',
            ],
            [
                'indicator_number' => 6,
                'question_code' => '6.e',
                'question_text' => 'Apakah hasil SKM dipublikasikan secara terbuka kepada masyarakat?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Foto display hasil SKM di dinding/medsos',
            ],
            [
                'indicator_number' => 6,
                'question_code' => '6.f',
                'question_text' => 'Apakah hasil rekomendasi SKM dilakukan tindak lanjut perbaikan?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Laporan tindak lanjut rekomendasi SKM',
            ],

            // Indikator 7 (1 P/J)
            [
                'indicator_number' => 7,
                'question_code' => '7.a',
                'question_text' => 'Sebutkan media yang digunakan untuk mempublikasikan hasil SKM (centang semua yang ada):',
                'question_type' => 'checkbox',
                'options' => ['Media Cetak / Papan Pengumuman', 'Media Elektronik (TV Display)', 'Media Sosial Resmi', 'Website / Subsite', 'Aplikasi Mobile'],
                'evidence_hint' => 'Foto dokumentasi seluruh media publikasi hasil SKM',
            ],

            // Indikator 8 (3 P/J)
            [
                'indicator_number' => 8,
                'question_code' => '8.a',
                'question_text' => 'Apakah sudah dibuat rencana tindak lanjut dari SKM yang telah dilaksanakan?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Dokumen rencana tindak lanjut SKM',
            ],
            [
                'indicator_number' => 8,
                'question_code' => '8.b',
                'question_text' => 'Berapa persen (%) rencana tindak lanjut yang terealisasi dalam 1 tahun terakhir?',
                'question_type' => 'number',
                'evidence_hint' => 'Laporan realisasi tindak lanjut SKM',
            ],
            [
                'indicator_number' => 8,
                'question_code' => '8.c',
                'question_text' => 'Apakah dibuat dokumen laporan formal pelaksanaan tindak lanjut SKM?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Buku laporan tindak lanjut perbaikan layanan',
            ],

            // Indikator 9 (2 P/J)
            [
                'indicator_number' => 9,
                'question_code' => '9.a',
                'question_text' => 'Apakah rekomendasi hasil SKM ditindaklanjuti secara terjadwal?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Matriks jadwal rencana aksi SKM',
            ],
            [
                'indicator_number' => 9,
                'question_code' => '9.b',
                'question_text' => 'Berapa lama kecepatan penyelesaian tindak lanjut sejak laporan SKM terbit?',
                'question_type' => 'radio',
                'options' => [
                    '1 bulan setelah laporan SKM terbit',
                    '3 bulan setelah laporan SKM terbit',
                    '6 bulan setelah laporan SKM terbit',
                    '9 bulan setelah laporan SKM terbit',
                    '1 tahun setelah laporan SKM terbit',
                    'Tidak ditindaklanjuti',
                ],
                'evidence_hint' => 'Tanggal laporan SKM vs tanggal berita acara implementasi saran perbaikan',
            ],

            // === ASPEK 2: PROFESIONALISME SDM (9 P/J) ===
            // Indikator 10 (2 P/J)
            [
                'indicator_number' => 10,
                'question_code' => '10.a',
                'question_text' => 'Apakah tersedia waktu pelayanan yang memudahkan pengguna layanan?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'SK Jam Pelayanan / Foto maklumat jam buka',
            ],
            [
                'indicator_number' => 10,
                'question_code' => '10.b',
                'question_text' => 'Bagaimana bentuk kemudahan waktu pelayanan yang diterapkan? (Pilih semua yang berlaku)',
                'question_type' => 'checkbox',
                'options' => [
                    'Memiliki kebijakan jam pelayanan/kerja resmi',
                    'Tidak ada jeda istirahat (pelayanan bergilir/shift)',
                    'Penambahan waktu layanan di luar jam pelayanan (hari kerja)',
                    'Penambahan waktu layanan di luar hari kerja pada kondisi tertentu',
                    'Penambahan waktu layanan di luar hari kerja secara rutin',
                    'Layanan 24 Jam (inti pendaftaran/layanan)',
                ],
                'evidence_hint' => 'SK Shift / Jadwal piket istirahat / Surat Perintah Lembur',
            ],

            // Indikator 11 (2 P/J)
            [
                'indicator_number' => 11,
                'question_code' => '11.a',
                'question_text' => 'Apakah sudah tersedia Aturan Perilaku dan Kode Etik Pelaksana Pelayanan?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Dokumen regulasi/SK Kode Etik dan Kode Perilaku',
            ],
            [
                'indicator_number' => 11,
                'question_code' => '11.b',
                'question_text' => 'Ruang lingkup Aturan Perilaku dan Kode Etik meliputi unsur apa saja:',
                'question_type' => 'checkbox',
                'options' => ['Nilai dasar hak dan kewajiban', 'Larangan KKN', 'Larangan diskriminasi', 'Sanksi pelanggaran', 'Penghargaan bagi yang patuh'],
                'evidence_hint' => 'Salinan pasal-pasal kode etik pada regulasi',
            ],

            // Indikator 12 (2 P/J)
            [
                'indicator_number' => 12,
                'question_code' => '12.a',
                'question_text' => 'Apakah tersedia mekanisme yang dibangun Unit Kerja untuk menjaga dan meningkatkan motivasi kerja?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Dokumentasi kegiatan motivasi kerja',
            ],
            [
                'indicator_number' => 12,
                'question_code' => '12.b',
                'question_text' => 'Jenis mekanisme motivasi kerja yang disediakan unit:',
                'question_type' => 'checkbox',
                'options' => [
                    'Pemberian penghargaan (reward)',
                    'Pemberian kesempatan mengikuti diklat/pelatihan',
                    'Pengembangan kapasitas (rekomendasi beasiswa)',
                    'Program konseling pegawai',
                    'Program team building / capacity building / gathering',
                    'Mekanisme insentif/penunjang lainnya',
                ],
                'evidence_hint' => 'Sertifikat diklat, foto capacity building, SK beasiswa/konseling',
            ],

            // Indikator 13 (2 P/J)
            [
                'indicator_number' => 13,
                'question_code' => '13.a',
                'question_text' => 'Apakah terdapat kriteria pemberian penghargaan bagi petugas pelaksana layanan yang berprestasi?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'SK Penetapan kriteria Employee of the Month / Reward',
            ],
            [
                'indicator_number' => 13,
                'question_code' => '13.b',
                'question_text' => 'Unsur kriteria penilaian penghargaan yang digunakan:',
                'question_type' => 'checkbox',
                'options' => ['Kehadiran/Disiplin', 'Kinerja (capaian tugas)', 'Kerja sama tim', 'Inovatif / Kreatif', 'Penampilan / Atribut rapi', 'Tidak pernah menerima komplain personal'],
                'evidence_hint' => 'Formulir penilaian prestasi kerja pegawai layanan',
            ],

            // Indikator 14 (1 P/J)
            [
                'indicator_number' => 14,
                'question_code' => '14.a',
                'question_text' => 'Unsur budaya pelayanan yang diterapkan oleh pelaksana layanan di unit Saudara:',
                'question_type' => 'checkbox',
                'options' => [
                    'Mengenakan pakaian seragam dinas sesuai aturan',
                    'Mengenakan identitas nama (name tag / ID card)',
                    'Mengenakan PIN / atribut / logo unit pelayanan',
                    'Menerapkan aturan 5S (Senyum, Salam, Sapa, Sopan, Santun)',
                    'Memiliki slogan / motto / maskot budaya layanan',
                ],
                'evidence_hint' => 'Foto petugas berseragam, name tag, PIN, dan spanduk 5S',
            ],

            // === ASPEK 3: SARANA DAN PRASARANA (9 P/J) ===
            // Indikator 15 (2 P/J)
            [
                'indicator_number' => 15,
                'question_code' => '15.a',
                'question_text' => 'Apakah tersedia tempat parkir kendaraan bermotor bagi pengguna layanan?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Foto area parkir',
            ],
            [
                'indicator_number' => 15,
                'question_code' => '15.b',
                'question_text' => 'Fasilitas pendukung tempat parkir yang tersedia:',
                'question_type' => 'checkbox',
                'options' => [
                    'Ketersediaan parkir roda dua dan roda empat dengan marka jelas',
                    'Petugas parkir yang berjaga',
                    'Pemeriksaan karcis / kartu parkir',
                    'CCTV aktif di area parkir',
                    'Tempat penitipan jaket / helm',
                    'Pelindung (kanopi / atap parkir)',
                ],
                'evidence_hint' => 'Foto seluruh fasilitas parkir (marka, pos jaga, karcis, cctv, kanopi)',
            ],

            // Indikator 16 (2 P/J)
            [
                'indicator_number' => 16,
                'question_code' => '16.a',
                'question_text' => 'Ketersediaan fasilitas WAJIB di ruang tunggu (harus terpenuhi kursi dan pendingin/sirkulasi):',
                'question_type' => 'checkbox',
                'options' => ['Kursi tunggu yang memadai', 'Pendingin ruangan (AC) / Kipas angin / sirkulasi udara baik'],
                'evidence_hint' => 'Foto ruang tunggu dengan kursi dan AC/kipas',
            ],
            [
                'indicator_number' => 16,
                'question_code' => '16.b',
                'question_text' => 'Fasilitas PELENGKAP ruang tunggu yang tersedia:',
                'question_type' => 'checkbox',
                'options' => [
                    'Mesin antrian dilengkapi monitor display',
                    'Televisi menyala untuk pengunjung',
                    'Bahan bacaan (koran, majalah, buku)',
                    'Pengisi daya baterai / charger booth',
                    'Hotspot / WiFi gratis untuk pengunjung',
                    'Air minum (dispenser / kemasan gratis)',
                ],
                'evidence_hint' => 'Foto fasilitas pelengkap ruang tunggu',
            ],

            // Indikator 17 (2 P/J)
            [
                'indicator_number' => 17,
                'question_code' => '17.a',
                'question_text' => 'Apakah tersedia sarana toilet khusus bagi pengguna layanan?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Foto toilet layanan',
            ],
            [
                'indicator_number' => 17,
                'question_code' => '17.b',
                'question_text' => 'Kondisi kelayakan toilet pengguna layanan yang terpenuhi:',
                'question_type' => 'checkbox',
                'options' => [
                    'Pemisahan toilet pria dan wanita',
                    'Wastafel dengan kran berfungsi baik',
                    'Toiletries (sabun, tissue, tempat sampah)',
                    'Ketersediaan air bersih melimpah',
                    'Checklist / kartu kontrol monev kebersihan toilet',
                ],
                'evidence_hint' => 'Foto wastafel, sabun, kran air, dan kartu kontrol kebersihan harian',
            ],

            // Indikator 18 (1 P/J)
            [
                'indicator_number' => 18,
                'question_code' => '18.a',
                'question_text' => 'Sebutkan sarana dan prasarana kelompok rentan/disabilitas yang tersedia di unit Saudara:',
                'question_type' => 'checkbox',
                'options' => [
                    'Kursi roda / tongkat / kruk',
                    'Pintu masuk yang mudah diakses tanpa undakan terjal',
                    'Ramp / jalan landai dengan pegangan rambat',
                    'Lift khusus dengan tombol huruf braille (atau layanan di lantai 1)',
                    'Selasar datar yang menghubungkan seluruh ruangan',
                    'Toilet khusus difabel (ada pegangan khusus)',
                    'Loket prioritas kelompok rentan',
                    'Ruang tunggu / kursi prioritas difabel, lansia, ibu hamil',
                    'Guiding block (jalur pemandu tuna netra)',
                    'Parkir khusus disabilitas yang mudah diakses',
                    'Alat bantu tuna netra/tuna rungu / buku panduan braille',
                    'Arena bermain anak ramah anak',
                    'Ruang laktasi / menyusui tertutup dan bersih',
                    'Petugas pemandu khusus / menguasai bahasa isyarat',
                ],
                'evidence_hint' => 'Foto masing-masing sarana kelompok rentan yang ada',
            ],

            // Indikator 19 (1 P/J)
            [
                'indicator_number' => 19,
                'question_code' => '19.a',
                'question_text' => 'Sarana prasarana penunjang yang tersedia di unit Saudara:',
                'question_type' => 'checkbox',
                'options' => [
                    'Fotocopy / layanan ATK',
                    'Kotak P3K lengkap obat standar',
                    'APAR (Alat Pemadam Api Ringan) aktif & terkalibrasi',
                    'Kantin / penyedia makanan minuman',
                    'Ruang ibadah (musholla) yang bersih dan layak',
                    'Area khusus merokok di luar gedung',
                    'Jalur evakuasi dan titik kumpul darurat bertanda jelas',
                    'Tempat sampah terpilah (organik, anorganik, B3)',
                    'CCTV pengawas keamanan aktif',
                ],
                'evidence_hint' => 'Foto sarana penunjang: APAR, P3K, musholla, jalur evakuasi, cctv',
            ],

            // Indikator 20 (1 P/J)
            [
                'indicator_number' => 20,
                'question_code' => '20.a',
                'question_text' => 'Fasilitas Front Office (FO) bagian Meja Layanan Informasi yang tersedia:',
                'question_type' => 'checkbox',
                'options' => [
                    'Petugas khusus informasi',
                    'Meja dan kursi informasi yang nyaman',
                    'Layar / monitor display informasi',
                    'Bahan cetak informasi (brosur / leaflet ringkas)',
                    'Buku register tamu / buku tamu digital',
                ],
                'evidence_hint' => 'Foto meja Front Office beserta petugas, register, brosur',
            ],

            // === ASPEK 4: SISTEM INFORMASI PELAYANAN PUBLIK (17 P/J) ===
            // Indikator 21 (6 P/J)
            [
                'indicator_number' => 21,
                'question_code' => '21.a',
                'question_text' => 'Apakah tersedia Sistem Informasi Pelayanan Publik (SIPP)?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Tautan bukti implementasi SIPP',
            ],
            [
                'indicator_number' => 21,
                'question_code' => '21.b',
                'question_text' => 'Format SIPP yang tersedia di unit Saudara:',
                'question_type' => 'checkbox',
                'options' => ['SIPP secara Elektronik', 'SIPP secara Non-Elektronik'],
                'evidence_hint' => 'Foto/tangkapan layar sistem informasi',
            ],
            [
                'indicator_number' => 21,
                'question_code' => '21.c',
                'question_text' => 'SIPP Non-Elektronik tersedia dalam bentuk:',
                'question_type' => 'checkbox',
                'options' => ['Pusat informasi tatap muka (lisan)', 'Papan pengumuman/bagan', 'Media cetak/katalog'],
                'evidence_hint' => 'Foto papan info dan media cetak',
            ],
            [
                'indicator_number' => 21,
                'question_code' => '21.d',
                'question_text' => 'SIPP Elektronik tersedia dalam bentuk:',
                'question_type' => 'checkbox',
                'options' => ['Intranet / offline lokal', 'Website publik daring (online)'],
                'evidence_hint' => 'Screenshot url website / e-kiosk',
            ],
            [
                'indicator_number' => 21,
                'question_code' => '21.e',
                'question_text' => 'Apakah unit telah memiliki akun dan menginput data di SIPPN Nasional (sippn.menpan.go.id)?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Screenshot profil instansi di SIPPN',
            ],
            [
                'indicator_number' => 21,
                'question_code' => '21.f',
                'question_text' => 'Berapa jumlah layanan yang sudah dipublikasikan di SIPPN Nasional?',
                'question_type' => 'number',
                'evidence_hint' => 'Screenshot halaman daftar layanan di SIPPN',
            ],

            // Indikator 22 (5 P/J)
            [
                'indicator_number' => 22,
                'question_code' => '22.a',
                'question_text' => 'Unsur pendukung SIPP: Ketersediaan data standar pelayanan digital',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Screenshot database SP digital',
            ],
            [
                'indicator_number' => 22,
                'question_code' => '22.b',
                'question_text' => 'Unsur pendukung SIPP: Integrasi tautan/kanal SP4N-LAPOR!',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Screenshot widget LAPOR di web',
            ],
            [
                'indicator_number' => 22,
                'question_code' => '22.c',
                'question_text' => 'Unsur pendukung SIPP: Modul e-SKM (Survei Kepuasan Online)',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Screenshot halaman e-SKM',
            ],
            [
                'indicator_number' => 22,
                'question_code' => '22.d',
                'question_text' => 'Unsur pendukung SIPP: Penilaian kinerja petugas layanan daring',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Screenshot rating kepuasan petugas',
            ],
            [
                'indicator_number' => 22,
                'question_code' => '22.e',
                'question_text' => 'Unsur pendukung SIPP: Pengelolaan keuangan / FAQ / antrian terintegrasi',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Screenshot dashboard antrian/pembayaran',
            ],

            // Indikator 23 (4 P/J)
            [
                'indicator_number' => 23,
                'question_code' => '23.a',
                'question_text' => 'Apakah website/aplikasi SIPP mudah dioperasikan dan responsif diakses perangkat mobile?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Screenshot tampilan mobile',
            ],
            [
                'indicator_number' => 23,
                'question_code' => '23.b',
                'question_text' => 'Apakah SIPP Elektronik menggunakan domain resmi pemerintah (*.pasamankab.go.id / *.go.id)?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Tautan URL domain resmi pemerintah',
            ],
            [
                'indicator_number' => 23,
                'question_code' => '23.c',
                'question_text' => 'Apakah susunan menu dan navigasi informasi mudah dipahami oleh masyarakat umum?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Screenshot tata letak menu website',
            ],
            [
                'indicator_number' => 23,
                'question_code' => '23.d',
                'question_text' => 'Apakah backend SIPP dikelola secara mandiri oleh tim administrator unit lokus?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Screenshot dasbor admin web pengelola',
            ],

            // Indikator 24 (2 P/J)
            [
                'indicator_number' => 24,
                'question_code' => '24.a',
                'question_text' => 'Apakah terdapat agenda pemutakhiran berkala atas data dan informasi pelayanan di kanal digital?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Log riwayat update postingan',
            ],
            [
                'indicator_number' => 24,
                'question_code' => '24.b',
                'question_text' => 'Berapa frekuensi jangka waktu pemutakhiran informasi pelayanan publik dilakukan?',
                'question_type' => 'radio',
                'options' => ['Setiap Hari (Harian)', 'Mingguan', 'Bulanan', 'Per Enam Bulan (Semesteran)', 'Tahunan'],
                'evidence_hint' => 'Tangkapan layar 5 update artikel/informasi berurutan dengan tanggal jelas',
            ],

            // === ASPEK 5: KONSULTASI DAN PENGADUAN (14 P/J) ===
            // Indikator 25 (2 P/J)
            [
                'indicator_number' => 25,
                'question_code' => '25.a',
                'question_text' => 'Apakah tersedia sarana konsultasi dan pengaduan langsung tatap muka?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Foto loket/ruang konsultasi pengaduan',
            ],
            [
                'indicator_number' => 25,
                'question_code' => '25.b',
                'question_text' => 'Fasilitas sarana konsultasi dan pengaduan yang dimiliki unit:',
                'question_type' => 'checkbox',
                'options' => [
                    'Kotak saran / pengaduan fisik',
                    'Ruang khusus konsultasi pengaduan (privasi)',
                    'Petugas khusus yang ditunjuk SK',
                    'Air minum / makanan ringan bagi pelapor',
                    'Buku register konsultasi dan pengaduan',
                    'Publikasi alur mekanisme pengaduan (spanduk/poster/leaflet)',
                ],
                'evidence_hint' => 'SK Petugas khusus pengaduan dan foto sarana ruang pengaduan',
            ],

            // Indikator 26 (3 P/J)
            [
                'indicator_number' => 26,
                'question_code' => '26.a',
                'question_text' => 'Karakteristik media tatap muka pengaduan:',
                'question_type' => 'radio',
                'options' => ['Terpisah khusus dari Front Office', 'Menyatu dengan Front Office', 'Tidak ada ruangan tatap muka'],
                'evidence_hint' => 'Foto tata letak meja pengaduan vs front office',
            ],
            [
                'indicator_number' => 26,
                'question_code' => '26.b',
                'question_text' => 'Media daring (online) pengaduan yang aktif disediakan unit:',
                'question_type' => 'checkbox',
                'options' => ['Email khusus aduan', 'SMS / Telepon Hotline / WhatsApp Aduan', 'Media Sosial (IG, FB, Twitter)', 'Website Formulir Pengaduan Unit'],
                'evidence_hint' => 'Screenshot akun WA / Sosmed / Form web pengaduan',
            ],
            [
                'indicator_number' => 26,
                'question_code' => '26.c',
                'question_text' => 'Apakah unit terhubung sebagai pejabat penghubung di aplikasi SP4N-LAPOR!?',
                'question_type' => 'radio',
                'options' => ['Ya, aktif terhubung', 'Tidak'],
                'evidence_hint' => 'SK Pejabat Penghubung SP4N LAPOR & screenshot dashboard admin lapor.go.id',
            ],

            // Indikator 27 (5 P/J)
            [
                'indicator_number' => 27,
                'question_code' => '27.a',
                'question_text' => 'Apakah seluruh pengaduan dan konsultasi yang masuk diarsipkan secara tertib?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Buku register / arsip pengaduan',
            ],
            [
                'indicator_number' => 27,
                'question_code' => '27.b',
                'question_text' => 'Apakah hasil pengaduan dituangkan dalam laporan rekapitulasi periodik?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Dokumen laporan berkala pengaduan',
            ],
            [
                'indicator_number' => 27,
                'question_code' => '27.c',
                'question_text' => 'Apakah dilakukan rapat monev atas laporan konsultasi dan pengaduan?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Notulen rapat monev pengaduan',
            ],
            [
                'indicator_number' => 27,
                'question_code' => '27.d',
                'question_text' => 'Apakah rekomendasi pengaduan ditindaklanjuti secara nyata di lapangan?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Foto bukti kondisi sebelum vs sesudah tindak lanjut',
            ],
            [
                'indicator_number' => 27,
                'question_code' => '27.e',
                'question_text' => 'Apakah laporan tindak lanjut pengaduan dipublikasikan kepada publik (infografis/pie chart)?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Tangkapan layar postingan infografis tindak lanjut aduan',
            ],

            // Indikator 28 (4 P/J)
            [
                'indicator_number' => 28,
                'question_code' => '28.a',
                'question_text' => 'Berapa total jumlah pengaduan/konsultasi yang masuk dalam 1 tahun terakhir?',
                'question_type' => 'number',
                'evidence_hint' => 'Rekapitulasi total aduan',
            ],
            [
                'indicator_number' => 28,
                'question_code' => '28.b',
                'question_text' => 'Berapa jumlah pengaduan yang berhasil diselesaikan/tuntas?',
                'question_type' => 'number',
                'evidence_hint' => 'Daftar aduan selesai',
            ],
            [
                'indicator_number' => 28,
                'question_code' => '28.c',
                'question_text' => 'Berapa jumlah aduan yang masuk melalui aplikasi SP4N-LAPOR!?',
                'question_type' => 'number',
                'evidence_hint' => 'Screenshot statistik SP4N-LAPOR',
            ],
            [
                'indicator_number' => 28,
                'question_code' => '28.d',
                'question_text' => 'Berapa jumlah aduan di SP4N-LAPOR! yang telah ditindaklanjuti hingga tuntas?',
                'question_type' => 'number',
                'evidence_hint' => 'Screenshot laporan tuntas di SP4N-LAPOR',
            ],

            // === ASPEK 6: INOVASI PELAYANAN PUBLIK (8 P/J) ===
            // Indikator 29 (4 P/J)
            [
                'indicator_number' => 29,
                'question_code' => '29.a',
                'question_text' => 'Apakah terdapat inovasi pelayanan publik yang dikembangkan oleh unit?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Proposal inovasi / Blueprint rancang bangun',
            ],
            [
                'indicator_number' => 29,
                'question_code' => '29.b',
                'question_text' => 'Tuliskan nama dan deskripsi ringkas inovasi pelayanan publik unit Anda:',
                'question_type' => 'text',
                'evidence_hint' => 'Ringkasan narasi inovasi',
            ],
            [
                'indicator_number' => 29,
                'question_code' => '29.c',
                'question_text' => 'Tahapan kematangan inovasi saat ini:',
                'question_type' => 'radio',
                'options' => [
                    'Masih dalam bentuk ide / gagasan',
                    'Sudah dilakukan uji coba lapangan',
                    'Sudah diuji coba dan mendapatkan masukan dari masyarakat',
                    'Sudah diimplementasikan penuh',
                ],
                'evidence_hint' => 'Laporan uji coba atau dokumentasi operasional',
            ],
            [
                'indicator_number' => 29,
                'question_code' => '29.d',
                'question_text' => 'Apakah inovasi telah memiliki dokumen penetapan resmi (SK Pimpinan/Keputusan Bupati)?',
                'question_type' => 'radio',
                'options' => ['Sudah ada dokumen penetapan resmi', 'Belum ada dokumen penetapan'],
                'evidence_hint' => 'SK Penetapan Inovasi Pelayanan Publik',
            ],

            // Indikator 30 (4 P/J)
            [
                'indicator_number' => 30,
                'question_code' => '30.a',
                'question_text' => 'Apakah tersedia sumber daya yang mendukung keberlanjutan inovasi pelayanan publik?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Tautan dokumen pendukung keberlanjutan inovasi',
            ],
            [
                'indicator_number' => 30,
                'question_code' => '30.b.1',
                'question_text' => 'Dukungan Regulasi: Terdapat regulasi penetapan dan petunjuk teknis pelaksanaan inovasi',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Salinan Peraturan/SOP/Petunjuk Teknis Inovasi',
            ],
            [
                'indicator_number' => 30,
                'question_code' => '30.b.2',
                'question_text' => 'Dukungan Anggaran: Terdapat alokasi anggaran operasional/pemeliharaan pelaksanaan inovasi',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Salinan DPA / RKA pendukung inovasi',
            ],
            [
                'indicator_number' => 30,
                'question_code' => '30.b.3',
                'question_text' => 'Dukungan SDM & Kemitraan: Terdapat tim pengelola khusus dan kerja sama lintas stakeholder',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'SK Tim Pelaksana Inovasi dan MoU kerja sama',
            ],

            // === PERTANYAAN TAMBAHAN (2 P/J) ===
            [
                'indicator_number' => 31,
                'question_code' => 'tambahan.1',
                'question_text' => 'Apakah terdapat sistem antrian pelayanan di unit lokus?',
                'question_type' => 'radio',
                'options' => ['Ya', 'Tidak'],
                'evidence_hint' => 'Foto loket dan mesin/mesin antrian',
            ],
            [
                'indicator_number' => 31,
                'question_code' => 'tambahan.2',
                'question_text' => 'Fasilitas pada sistem antrian yang tersedia (centang semua yang ada):',
                'question_type' => 'checkbox',
                'options' => [
                    'Pengeras suara / sound system pemanggil antrian',
                    'Petugas khusus yang memandu alur antrian',
                    'Pengelompokan jenis antrian berdasarkan kebutuhan layanan',
                    'Sistem pendaftaran antrian daring/online (Website/WhatsApp/Aplikasi)',
                    'Sistem cetak nomor antrian (kertas/tiket)',
                    'Monitor layar display antrian di ruang tunggu',
                ],
                'evidence_hint' => 'Foto sound antrian, tiket antrian, display TV, dan screenshot pendaftaran online',
            ],
        ];

        foreach ($questions as $idx => $q) {
            $indicatorId = $map[$q['indicator_number']] ?? null;
            if (!$indicatorId) continue;

            F01Question::updateOrCreate(
                [
                    'indicator_id' => $indicatorId,
                    'question_code' => $q['question_code'],
                ],
                [
                    'question_text' => $q['question_text'],
                    'question_type' => $q['question_type'],
                    'options' => $q['options'] ?? null,
                    'help_text' => null,
                    'evidence_hint' => $q['evidence_hint'] ?? 'Tautan Link Google Drive Dokumen Bukti Dukung',
                    'requires_drive_link' => true,
                    'order' => $idx + 1,
                ]
            );
        }
    }
}
