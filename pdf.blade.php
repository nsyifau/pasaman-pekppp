<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Tuntas - {{ $nomorBat }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 5px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #000;
            margin-bottom: 15px;
            padding-bottom: 5px;
        }
        .header-logo {
            width: 80px;
            text-align: center;
            vertical-align: middle;
        }
        .header-logo img {
            max-width: 70px;
        }
        .header-text {
            text-align: center;
            vertical-align: middle;
        }
        .header-text h3 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
        }
        .header-text h4 {
            margin: 2px 0 0 0;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
        }
        .header-text p {
            margin: 5px 0 0 0;
            font-size: 9px;
            color: #555;
            font-style: italic;
        }
        .title-section {
            text-align: center;
            margin-bottom: 15px;
        }
        .title-section h2 {
            margin: 0;
            font-size: 13px;
            text-transform: uppercase;
            font-weight: bold;
            text-decoration: underline;
        }
        .title-section p {
            margin: 3px 0 0 0;
            font-size: 10px;
            font-weight: bold;
        }
        .intro-text {
            text-align: justify;
            margin-bottom: 12px;
            text-indent: 30px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table th, .info-table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        .info-table th {
            background-color: #f9f9f9;
            font-weight: bold;
            width: 25%;
        }
        .recommendations-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .recommendations-table th, .recommendations-table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }
        .recommendations-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .recommendations-table tr {
            page-break-inside: avoid;
        }
        .closing-text {
            text-align: justify;
            margin-bottom: 20px;
            text-indent: 30px;
        }
        .footer-section {
            width: 100%;
            margin-top: 15px;
        }
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
        }
        .ttd-left {
            width: 50%;
        }
        .ttd-right {
            width: 50%;
            text-align: center;
        }
        .ttd-title {
            margin-bottom: 5px;
            font-size: 11px;
        }
        .ttd-space {
            height: 60px;
            margin: 5px 0;
            vertical-align: middle;
            text-align: center;
        }
        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin: 0;
            font-size: 11px;
        }
        .ttd-nip {
            margin: 2px 0 0 0;
            font-size: 11px;
        }
        .digital-stamp {
            display: inline-block;
            border: 2px solid #198754;
            color: #198754;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 8px;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
            background-color: #f4faf6;
            margin: 2px 0;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT (Pasaman style) -->
    @php
        $inspektur = \App\Models\User::whereHas('role', function($q) {
            $q->where('level', 4);
        })->first();
    @endphp
    <div class="rangkasurat" style="margin-bottom: 15px;">
        <table width="100%" style="position: relative; border: none; border-collapse: collapse;">
            <tr style="border: none;">
                <td width="40" style="border: none; padding: 0;">
                    <img src="{{ public_path('assets/dist/assets/img/logo.png') }}" style="position: absolute; top: 0.1cm; width: 50px;">
                </td>

                <td style="text-align: center; border: none; padding: 0;">
                    <h4 style="margin:0; font-size: 14px;"><strong>PEMERINTAH KABUPATEN PASAMAN</strong></h4>
                    <h2 style="font-weight: 700; padding-left:20px; padding-right:20px; text-transform: uppercase; margin:2px 0; font-size: 16px;">
                        INSPEKTORAT
                    </h2>
                    <p style="margin:0; font-size:10px;">
                        Jln. Jendral Sudirman No.40 Lubuk Sikaping Pos 26311 Telp - (0753) 20202 - 20281
                    </p>
                </td>
                <td width="40" style="border: none; padding: 0;"></td>
            </tr>
            <tr style="border: none;">
                <td colspan="3" style="border: none; padding: 5px 0 0 0;">
                    <hr style="border-top: 4px double #000; margin: 0;">
                </td>
            </tr>
        </table>
    </div>

    <!-- JUDUL -->
    <div class="title-section">
        <h2>Berita Acara Penyelesaian Tuntas</h2>
        <p>Nomor: {{ $nomorBat }}</p>
    </div>

    <!-- PENGANTAR -->
    <div class="intro-text">
        Berdasarkan hasil pemantauan dan evaluasi yang dilakukan oleh Tim Tindak Lanjut Hasil Pemeriksaan (TLHP) Inspektorat Daerah Kabupaten Pasaman, dengan ini diterangkan bahwa objek pemeriksaan telah menyelesaikan tindak lanjut atas temuan dan rekomendasi laporan hasil pemeriksaan berikut:
    </div>

    <!-- DETAIL INFORMASI LHP -->
    <table class="info-table">
        <tr>
            <th>Nomor LHP</th>
            <td><strong>{{ $lhp->nomor_lhp ?? '-' }}</strong></td>
        </tr>
        <tr>
            <th>Tanggal Terbit LHP</th>
            <td>{{ $lhp->tanggal_terbit ? \Carbon\Carbon::parse($lhp->tanggal_terbit)->translatedFormat('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <th>Jenis Pemeriksaan</th>
            <td>{{ $lhp->dataHasilPemeriksaan->jenis ?? '-' }}</td>
        </tr>
        <tr>
            <th>Tahun Pemeriksaan</th>
            <td>{{ $lhp->dataHasilPemeriksaan->tahun ?? '-' }}</td>
        </tr>
        <tr>
            <th>OPD / OBRIK</th>
            <td>{{ $lhp->dataHasilPemeriksaan->opd->nama ?? '-' }}</td>
        </tr>
        <tr>
            <th>Tanggal Penyampaian</th>
            <td>{{ $lhp->tanggal_penyampaian ? \Carbon\Carbon::parse($lhp->tanggal_penyampaian)->translatedFormat('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <th>Status Penyelesaian</th>
            <td><strong>TUNTAS (100%)</strong></td>
        </tr>
    </table>

    <h3 style="margin-top: 15px; margin-bottom: 8px; font-size: 11px; text-transform: uppercase;">Daftar Temuan dan Tindak Lanjut Rekomendasi</h3>
    <table class="recommendations-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Temuan Audit</th>
                <th style="width: 35%;">Rekomendasi (Kode & Uraian)</th>
                <th style="width: 25%;">Tindak Lanjut OPD</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @if($lhp->dataHasilPemeriksaan && $lhp->dataHasilPemeriksaan->temuanAudits)
                @foreach($lhp->dataHasilPemeriksaan->temuanAudits as $temuan)
                    @foreach($temuan->rekomendasis as $rekomendasi)
                        <tr>
                            <td style="text-align: center;">{{ $no++ }}</td>
                            <td>{{ $temuan->uraian_temuan }}</td>
                            <td>
                                <strong>Kode: {{ $rekomendasi->kodeRekomendasi->kode ?? '-' }}</strong><br>
                                {{ $rekomendasi->uraian }}
                            </td>
                            <td>
                                {{ $rekomendasi->tindakLanjut?->verifikasi_status === 'verified' ? 'Terverifikasi' : '-' }}
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            @else
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada data temuan atau rekomendasi.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- PENUTUP -->
    <div class="closing-text">
        Demikian Berita Acara Tuntas ini dibuat secara otomatis oleh sistem sebagai dokumen resmi bukti penyelesaian tindak lanjut rekomendasi hasil pemeriksaan.
    </div>

    <!-- PENANDA TANGAN -->
    <div style="margin-top: 40px; width: 100%; page-break-inside: avoid;">
        <div style="text-align: center; width: 260px; float: right; margin-right: 10px;">
            <p style="margin: 0;">Lubuk Sikaping, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p style="font-weight: bold; margin: 4px 0;">INSPEKTUR</p>
            @if(isset($isSigned) && $isSigned)
                {{-- ponytail: table layout untuk centering logo di tengah QR — position:absolute tidak reliable di DomPDF --}}
                <div style="display:inline-block; position:relative; width:80px; height:80px; margin:0 auto;">
                    <img src="{{ $qrBase64 }}" width="80" height="80" style="display:block; position:absolute; top:0; left:0;">
                    <img src="{{ $logoBase64 ?? '' }}" width="22" height="22"
                         style="position:absolute; top:29px; left:29px; background:#fff; padding:1px; border-radius:2px;">
                </div>
            @else
                <div style="height: 60px;"></div>
            @endif
            @php
                $namaInspektur = $namaInspektur ?? ($bat->nama_penandatangan ?? ($inspektur?->name ?? '-'));
                $nipInspektur  = $nipInspektur  ?? ($bat->nip_penandatangan  ?? ($inspektur?->nip  ?? '-'));
            @endphp
            <p class="ttd-name">{{ $namaInspektur }}</p>
            <p class="ttd-nip">NIP. {{ $nipInspektur }}</p>
        </div>
    </div>

    <!-- Footnote -->
    <div style="position: fixed; bottom: -20px; left: -20px; right: -20px; font-size: 10px; font-style: italic; color: #555; font-family: Arial, sans-serif; z-index: 100; text-align: center;">
        Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan oleh Balai Besar Sertifikasi Elektronik (BSrE), Badan Siber dan Sandi Negara (BSSN).
    </div>

</body>
</html>
