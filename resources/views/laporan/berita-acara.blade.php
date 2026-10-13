@php
    $statuses = \App\Models\Evaluation::statuses();
    $categories = \App\Models\Opd::categories();
    $weight = fn ($w) => rtrim(rtrim(number_format((float) $w, 2), '0'), ',');
    $angka = fn ($v) => number_format((float) $v, 3, ',', '.');

    $unit = $evaluation->opd?->name ?? '—';
    $kategori = $categories[$evaluation->opd?->category] ?? $evaluation->opd?->category ?? '—';
    $totalButir = array_sum(array_column($recap, 'questions'));
    $totalDinilai = array_sum(array_column($recap, 'scored'));
    $predikat = $evaluation->category_ipp
        ? ($evaluation->category_ipp.' — '.($evaluation->category_ipp_label ?? ''))
        : 'Belum ditetapkan';
@endphp

<x-layouts.document title="Berita Acara - {{ $unit }}">
    <x-slot:toolbar>
        <span class="doc-toolbar-title">{{ $evaluation->opd?->name }} · {{ $evaluation->year }}</span>
    </x-slot:toolbar>

    {{-- Panel pengaturan penanda tangan (tidak ikut tercetak) --}}
    <div class="no-print" style="border:1px solid #cfd4da; background:#f8f9fa; border-radius:8px; padding:12px 14px; margin-bottom:18px;">
        <p style="margin:0 0 10px 0; font-weight:bold; font-size:11px;">Identitas &amp; Penanda Tangan Berita Acara</p>
        <form method="POST" action="{{ route('laporan.berita_acara.simpan', $evaluation) }}">
            @csrf
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="padding:4px 6px 4px 0; width:16%;"><label style="font-size:11px;">Nomor BA</label></td>
                    <td style="padding:4px 6px; width:34%;"><input type="text" name="ba_nomor" value="{{ old('ba_nomor', $baNomor) }}" class="input input-sm" style="width:100%;"></td>
                    <td style="padding:4px 6px 4px 12px; width:16%;"><label style="font-size:11px;">Tempat</label></td>
                    <td style="padding:4px 6px;"><input type="text" name="ba_tempat" value="{{ old('ba_tempat', $baTempat) }}" class="input input-sm" style="width:100%;"></td>
                </tr>
                <tr>
                    <td style="padding:4px 6px 4px 0;"><label style="font-size:11px;">Tanggal BA</label></td>
                    <td style="padding:4px 6px;"><input type="date" name="ba_tanggal" value="{{ old('ba_tanggal', optional($baTanggal)->format('Y-m-d')) }}" class="input input-sm" style="width:100%;"></td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td style="padding:10px 6px 4px 0;" colspan="4"><span style="font-weight:bold; font-size:11px;">Penanda Tangan — Pejabat Berwenang</span></td>
                </tr>
                <tr>
                    <td style="padding:4px 6px 4px 0;"><label style="font-size:11px;">Nama</label></td>
                    <td style="padding:4px 6px;"><input type="text" name="pejabat_nama" value="{{ old('pejabat_nama', $evaluation->pejabat_nama) }}" class="input input-sm" style="width:100%;"></td>
                    <td style="padding:4px 6px 4px 12px;"><label style="font-size:11px;">Jabatan</label></td>
                    <td style="padding:4px 6px;"><input type="text" name="pejabat_jabatan" value="{{ old('pejabat_jabatan', $evaluation->pejabat_jabatan) }}" class="input input-sm" style="width:100%;"></td>
                </tr>
                <tr>
                    <td style="padding:4px 6px 4px 0;"><label style="font-size:11px;">NIP</label></td>
                    <td style="padding:4px 6px;"><input type="text" name="pejabat_nip" value="{{ old('pejabat_nip', $evaluation->pejabat_nip) }}" class="input input-sm" style="width:100%;"></td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td style="padding:10px 6px 4px 0;" colspan="4"><span style="font-weight:bold; font-size:11px;">Penanda Tangan — Penilai / Tim Evaluator</span></td>
                </tr>
                <tr>
                    <td style="padding:4px 6px 4px 0;"><label style="font-size:11px;">Nama</label></td>
                    <td style="padding:4px 6px;"><input type="text" name="penilai_nama" value="{{ old('penilai_nama', $evaluation->penilai_nama) }}" class="input input-sm" style="width:100%;"></td>
                    <td style="padding:4px 6px 4px 12px;"><label style="font-size:11px;">Jabatan</label></td>
                    <td style="padding:4px 6px;"><input type="text" name="penilai_jabatan" value="{{ old('penilai_jabatan', $evaluation->penilai_jabatan) }}" class="input input-sm" style="width:100%;"></td>
                </tr>
                <tr>
                    <td style="padding:4px 6px 4px 0;"><label style="font-size:11px;">NIP</label></td>
                    <td style="padding:4px 6px;"><input type="text" name="penilai_nip" value="{{ old('penilai_nip', $evaluation->penilai_nip) }}" class="input input-sm" style="width:100%;"></td>
                    <td colspan="2"></td>
                </tr>
            </table>
            <div style="margin-top:10px; text-align:right;">
                <button type="submit" class="doc-btn doc-btn-primary">Simpan Penanda Tangan</button>
            </div>
        </form>
    </div>

    {{-- ================= DOKUMEN CETAK ================= --}}
    <div class="title-section">
        <h2>Berita Acara Hasil Penilaian Kinerja Pelayanan Publik</h2>
        <p>Nomor: {{ $baNomor }}</p>
    </div>

    <p class="intro-text">
        Pada hari ini, {{ $baTanggal->translatedFormat('l') }} tanggal {{ $baTanggal->translatedFormat('d') }}
        bulan {{ $baTanggal->translatedFormat('F') }} tahun {{ $baTanggal->translatedFormat('Y') }},
       bertempat di {{ $baTempat }}, telah dilaksanakan penilaian kinerja pelayanan publik unit layanan
        <strong>{{ $unit }}</strong> berdasarkan hasil Penilaian Mandiri (F-01), Penilaian Per Butir Pertanyaan
        (F-02), dan Penilaian Persepsi Masyarakat (F-03) sesuai Keputusan Bupati Pasaman tentang Pedoman
        Penilaian Kinerja Pelayanan Publik.
    </p>

    <table class="info-table">
        <tr>
            <th>Unit Layanan / Lokus</th>
            <td>{{ $unit }}</td>
            <th>Kategori Unit</th>
            <td>{{ $kategori }}</td>
        </tr>
        <tr>
            <th>Tahun Penilaian</th>
            <td>{{ $evaluation->year }}</td>
            <th>Status Penilaian</th>
            <td>{{ $statuses[$evaluation->status] ?? $evaluation->status }}</td>
        </tr>
        <tr>
            <th>Tanggal Isian F-01 Diterima</th>
            <td>{{ $evaluation->submitted_at?->translatedFormat('d F Y') ?? '—' }}</td>
            <th>Tanggal Penilaian</th>
            <td>{{ $evaluation->evaluated_at?->translatedFormat('d F Y') ?? '—' }}</td>
        </tr>
        <tr>
            <th>Jumlah Butir Dinilai</th>
            <td>{{ $totalDinilai }} dari {{ $totalButir }} butir</td>
            <th>Predikat Indeks</th>
            <td>{{ $predikat }}</td>
        </tr>
    </table>

    <p class="section-title">A. Rekapitulasi Skor F-02 Per Aspek</p>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:34px;">No</th>
                <th>Aspek Penilaian</th>
                <th style="width:70px;">Bobot</th>
                <th style="width:110px;">Butir Dinilai</th>
                <th style="width:110px;">Rata-Rata Skor</th>
                <th style="width:110px;">Nilai Terbobot</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($recap as $i => $row)
                <tr>
                    <td class="num">{{ $i + 1 }}</td>
                    <td>{{ $row['aspect']->name }}</td>
                    <td class="num">{{ $weight($row['aspect']->weight) }}%</td>
                    <td class="num">{{ $row['scored'] }}/{{ $row['questions'] }}</td>
                    <td class="num">{{ $angka($row['average']) }}</td>
                    <td class="num">{{ $angka($row['weighted']) }}</td>
                </tr>
            @endforeach
            <tr class="subtotal">
                <td colspan="4" class="right">Total Skor F-02 Terbobot</td>
                <td class="num">—</td>
                <td class="num">{{ $angka($evaluation->score_f02) }}</td>
            </tr>
        </tbody>
    </table>

    <p class="section-title">B. Hasil Akhir Penilaian</p>
    <table class="data-table">
        <thead>
            <tr>
                <th>Komponen Penilaian</th>
                <th style="width:110px;">Bobot</th>
                <th style="width:130px;">Nilai (skala 0-5)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Skor F-02 (Penilaian per butir pertanyaan)</td>
                <td class="num">75%</td>
                <td class="num">{{ $angka($evaluation->score_f02) }}</td>
            </tr>
            <tr>
                <td>Skor F-03 (Persepsi pengguna layanan)</td>
                <td class="num">25%</td>
                <td class="num">{{ $angka($evaluation->score_f03) }}</td>
            </tr>
            <tr class="subtotal">
                <td colspan="2" class="right">Indeks Pelayanan Publik (IPP)</td>
                <td class="num">{{ $angka($evaluation->final_score_ipp) }}</td>
            </tr>
            <tr>
                <td colspan="2" class="right">Kategori / Predikat</td>
                <td class="num">{{ $predikat }}</td>
            </tr>
        </tbody>
    </table>

    @if (! empty($evaluation->evaluator_notes))
        <p class="section-title">C. Catatan Penilai</p>
        <p style="text-align:justify; border:1px solid #cfd4da; padding:8px 10px;">{{ $evaluation->evaluator_notes }}</p>
    @endif

    <p class="intro-text" style="margin-top:14px;">
        Demikian Berita Acara ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.
    </p>

    <table class="sign-block">
        <tr>
            <td></td>
            <td>
                {{ $baTempat }}, {{ $baTanggal->translatedFormat('d F Y') }}<br>
                Pejabat Berwenang
            </td>
        </tr>
        <tr>
            <td>
                Penilai / Tim Evaluator
            </td>
            <td></td>
        </tr>
        <tr>
            <td><div class="ttd-space"></div><p class="ttd-name">{{ $evaluation->penilai_nama ?: '..............................' }}</p><p class="ttd-nip">NIP. {{ $evaluation->penilai_nip ?: '..............................' }}</p></td>
            <td><div class="ttd-space"></div><p class="ttd-name">{{ $evaluation->pejabat_nama ?: '..............................' }}</p><p class="ttd-nip">NIP. {{ $evaluation->pejabat_nip ?: '..............................' }}</p><p class="muted" style="margin:0;">{{ $evaluation->pejabat_jabatan ?: '' }}</p></td>
        </tr>
    </table>
</x-layouts.document>
