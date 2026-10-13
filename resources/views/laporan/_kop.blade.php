@php
    $doc = config('pekppp.document', []);
    $logo = $doc['logo'] ?? 'logo-pasaman.png';
    $logoPath = public_path($logo);
    $logoSrc = file_exists($logoPath)
        ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
        : asset($logo);
@endphp

{{-- Kop instansi (gaya mengikuti pdf.blade.php) --}}
<table class="header-table">
    <tr>
        <td class="header-logo">
            <img src="{{ $logoSrc }}" alt="Logo Kabupaten Pasaman">
        </td>
        <td class="header-text">
            <h3>{{ $doc['instansi'] ?? 'Pemerintah Kabupaten Pasaman' }}</h3>
            <h4>{{ $doc['sekretariat'] ?? 'Sekretariat Daerah' }}</h4>
            <h4>{{ $doc['unit'] ?? 'Bagian Organisasi' }}</h4>
            <p>{{ $doc['alamat'] ?? '' }}</p>
        </td>
    </tr>
</table>
