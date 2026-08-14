<?php
use Carbon\Carbon;
$totalMasukKonversi = $penerimaan->detailPenerimaan->sum(function ($item) {
    $nilaiKonversi = $item->batch?->barang?->konversiBarang?->nilai_konversi ?? 1;
    return $item->jumlah_masuk * $nilaiKonversi;
});
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Penerimaan {{ $penerimaan->no_registrasi }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .font-bold {
            font-weight: bold;
        }

        .header-title {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header-title h2 {
            margin: 0;
            padding: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 5px 0;
            vertical-align: top;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #333;
            padding: 6px 8px;
        }

        .data-table th {
            background-color: #f2f2f2;
            font-size: 11px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-table td {
            text-align: center;
            vertical-align: top;
            width: 33.33%;
        }

        .signature-space {
            height: 70px;
        }
    </style>
</head>

<body>
    <div class="header-title">
        <h2>Laporan Penerimaan Bahan Baku</h2>
    </div>
    <table class="meta-table">
        <tr>
            <td style="width: 15%;"><strong>No Registrasi</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;">{{ $penerimaan->no_registrasi }}</td>

            <td style="width: 15%;"><strong>Supplier</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;">{{ $penerimaan->pembelian?->supplier?->nama_supplier ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>No Nota</strong></td>
            <td>:</td>
            <td>{{ $penerimaan->pembelian?->no_nota ?? '-' }}</td>

            <td><strong>No Faktur</strong></td>
            <td>:</td>
            <td>{{ $penerimaan->no_faktur ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Masuk</strong></td>
            <td>:</td>
            <td>{{ Carbon::parse($penerimaan->tanggal_masuk)->translatedFormat('d F Y') }}</td>

            <td><strong>No Surat Jalan</strong></td>
            <td>:</td>
            <td>{{ $penerimaan->surat_jalan ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Status</strong></td>
            <td>:</td>
            <td>{{ $penerimaan->jenis_penerimaan }}</td>
        </tr>
    </table>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 20%;">Nama Bahan Baku</th>
                <th style="width: 12%;" class="text-center">Kode Batch</th>
                <th style="width: 12%;" class="text-center">Jumlah Masuk</th>
                <th style="width: 12%;" class="text-center">Jumlah (Gram)</th>
                <th style="width: 15%;" class="text-center">Tanggal Kadaluarsa</th>
                <th style="width: 10%;" class="text-center">Jumlah Ditolak</th>
                <th style="width: 15%;" class="text-center">Alasan Penolakan</th>
                <th style="width: 20%;">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penerimaan->detailPenerimaan as $item)
                @php
                    // Ambil data konversi dari relasi barang
                    $konversi = $item->batch?->barang?->konversiBarang;

                    // Ambil kolom 'nilai_konversi' dari gambar. Jika kosong, default 1
                    $nilaiKonversiItem = $konversi?->nilai_konversi ?? 1;

                    // Hitung hasil perkalian (Contoh: 2 x 15000 = 30000)
                    $jumlahHasilKonversi = $item->jumlah_masuk * $nilaiKonversiItem;
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->batch?->barang?->nama_barang ?? '-' }}</td>
                    <td class="text-center">{{ $item->batch?->kode_lot_supplier ?? '' }}</td>
                    <td class="text-center">{{ number_format($item->jumlah_masuk, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($jumlahHasilKonversi, 0, ',', '.') }}</td>
                    <td class="text-center">
                        {{ $item->batch?->expired_date ? Carbon::parse($item->batch->expired_date)->format('d-m-Y') : '-' }}
                    </td>
                    <td class="text-center">{{ $item->jumlah_ditolak ?? '' }}</td>
                    <td class="text-center">{{ $item->alasan_penolakan ?? '' }}</td>
                    <td>{{ $item->deskripsi ?: '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-4">Tidak ada data bahan baku yang diterima.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- <!-- SECTION TANDA TANGAN -->
    <table class="signature-table">
        <tr>
            <td>
                <div>Diserahkan Oleh,</div>
                <div>(Supplier / Ekspedisi)</div>
                <div class="signature-space"></div>
                <div class="font-bold">(.......................................)</div>
                <div>Nama & Tanda Tangan</div>
            </td>
            <td>
                <div>Mengetahui,</div>
                <div>(Kepala Gudang)</div>
                <div class="signature-space"></div>
                <div class="font-bold">(.......................................)</div>
                <div>Nama & Tanda Tangan</div>
            </td>
            <td>
                <div>Diterima Oleh,</div>
                <div>(Staff Gudang)</div>
                <div class="signature-space"></div>
                <div class="font-bold">({{ $penerimaan->user?->name ?? '.......................................' }})</div>
                <div>Nama & Tanda Tangan</div>
            </td>
        </tr>
    </table> --}}

</body>

</html>
