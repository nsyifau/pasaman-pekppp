@props(['title' => 'Dokumen'])

@php
    $docCss = <<<'CSS'
    body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; line-height: 1.45; color: #333; margin: 0; padding: 0; background: #f1f3f5; }
    .doc-toolbar { position: sticky; top: 0; z-index: 10; display: flex; flex-wrap: wrap; align-items: center; gap: 8px; padding: 10px 16px; background: #fff; border-bottom: 1px solid #dee2e6; }
    .doc-toolbar .spacer { flex: 1; }
    .doc-btn { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 6px; border: 1px solid #ced4da; background: #fff; color: #333; font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; }
    .doc-btn-primary { background: #0e5c3b; border-color: #0e5c3b; color: #fff; }
    .doc-toolbar-title { font-size: 12px; font-weight: 600; color: #495057; }
    .doc-sheet { max-width: 210mm; margin: 16px auto; background: #fff; padding: 16mm 14mm; box-shadow: 0 1px 6px rgba(0,0,0,.12); }

    .header-table { width: 100%; border-collapse: collapse; border-bottom: 3px double #000; margin-bottom: 14px; }
    .header-logo { width: 80px; text-align: center; vertical-align: middle; }
    .header-logo img { max-width: 70px; }
    .header-text { text-align: center; vertical-align: middle; }
    .header-text h3 { margin: 0; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; }
    .header-text h4 { margin: 2px 0 0 0; font-size: 15px; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; }
    .header-text p { margin: 4px 0 0 0; font-size: 9px; color: #555; font-style: italic; }

    .title-section { text-align: center; margin-bottom: 14px; }
    .title-section h2 { margin: 0; font-size: 13px; text-transform: uppercase; font-weight: bold; text-decoration: underline; }
    .title-section p { margin: 3px 0 0 0; font-size: 10px; font-weight: bold; }

    .intro-text { text-align: justify; margin: 0 0 12px 0; }
    .intro-text + .intro-text { text-indent: 30px; }

    .info-table, .data-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    .info-table th, .info-table td, .data-table th, .data-table td { border: 1px solid #cfd4da; padding: 5px 8px; text-align: left; vertical-align: top; }
    .info-table th { background-color: #f5f6f7; font-weight: bold; width: 24%; }
    .data-table th { background-color: #f5f6f7; font-weight: bold; text-align: center; }
    .data-table td.num { text-align: center; white-space: nowrap; }
    .data-table td.right { text-align: right; white-space: nowrap; }
    .data-table tbody tr.subtotal td { background-color: #fafbfc; font-weight: bold; }
    .data-table .aspek-row td { background-color: #eef2f0; font-weight: bold; }
    .muted { color: #6c757d; }
    .text-center { text-align: center; }

    .section-title { font-size: 11px; font-weight: bold; text-transform: uppercase; margin: 16px 0 6px 0; }
    .sign-block { width: 100%; border-collapse: collapse; margin-top: 26px; page-break-inside: avoid; }
    .sign-block td { width: 50%; text-align: center; vertical-align: top; padding-top: 6px; }
    .ttd-space { height: 62px; }
    .ttd-name { margin: 0; font-weight: bold; text-decoration: underline; }
    .ttd-nip { margin: 1px 0 0 0; }

    @media print {
        body { background: #fff; }
        .doc-toolbar, .no-print { display: none !important; }
        .doc-sheet { margin: 0; padding: 0; box-shadow: none; max-width: none; }
        table { page-break-inside: auto; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        thead { display: table-header-group; }
        .sign-block, .info-table { page-break-inside: avoid; }
    }
    @page { size: A4; margin: 14mm; }
    CSS;
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <style>{!! $docCss !!}</style>
</head>
<body>
    <div class="doc-toolbar no-print">
        <span class="doc-toolbar-title">{{ $title }}</span>
        <span class="spacer"></span>
        {{ $toolbar ?? '' }}
        <button type="button" class="doc-btn doc-btn-primary" onclick="window.print()">Cetak / Simpan PDF</button>
        <a href="{{ route('laporan.index') }}" class="doc-btn">Kembali</a>
    </div>

    <div class="doc-sheet">
        @include('laporan._kop')
        {{ $slot }}
    </div>
</body>
</html>
