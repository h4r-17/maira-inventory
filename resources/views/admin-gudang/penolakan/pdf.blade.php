<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Penolakan {{ $penolakan->no_penolakan }}</title>
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
        <h2>LAPORAN PENOLAKAN BAHAN BAKU (REJECT)</h2>
    </div>
    <table class="meta-table">
        <tr>
            <td style="width: 25%;"><strong>No Penolakan</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;">{{ $penolakan->no_penolakan }}</td>

            <td style="width: 20%;"><strong>Tanggal Ditolak</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;">{{ \Carbon\Carbon::parse($penolakan->tanggal_penolakan)->translatedFormat('d F Y') }}
            </td>

        </tr>
        <tr>
            <td style="width: 25%;"><strong>Ditemukan Pada Produksi</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;">{{ $penolakan->produksi->batch_produk }}</td>

            <td style="width: 20%;"><strong>Tanggal Produksi</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;">
                {{ \Carbon\Carbon::parse($penolakan->produksi->tanggal_produksi)->translatedFormat('d F Y') }}
            </td>
        </tr>
    </table>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 20%;" class="text-center">Nama Bahan Baku</th>
                <th style="width: 20%;" class="text-center">Kode Batch</th>
                <th style="width: 10%;" class="text-center">Jumlah Ditolak</th>
                <th style="width: 10%;" class="text-center">Satuan</th>
                <th style="width: 15%;" class="text-center">Alasan Penolakan</th>
                <th style="width: 21%;" class="text-center">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penolakan->detailPenolakan as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $item->batch->barang->nama_barang ?? '' }}</td>
                    <td class="text-center">
                        {{ !blank($item->batch->kode_lot_supplier) ? $item->batch->kode_lot_supplier : (!blank($item->batch->kode_batch) ? $item->batch->kode_batch : '') }}
                    </td>
                    <td class="text-center">{{ $item->jumlah_ditolak }}</td>
                    <td class="text-center">{{ $item->batch->barang->satuan->kode_satuan ?? '' }}</td>
                    <td class="text-center">{{ $item->alasan_penolakan ?? '' }}</td>
                    <td class="text-center">{{ $item->deskripsi ?: '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4">Tidak ada data bahan baku pada penolakan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- <!-- SECTION TANDA TANGAN -->
    <table class="signature-table">
        <tr>
            <td>
                <div>Dibuat Oleh,</div>
                <div>(Staff Gudang / QC)</div>
                <div class="signature-space"></div>
                <div class="font-bold">(.......................................)</div>
                <div>Nama & Tanda Tangan</div>
            </td>
            <td>
                <div>Diketahui Oleh,</div>
                <div>(Kepala Gudang)</div>
                <div class="signature-space"></div>
                <div class="font-bold">(.......................................)</div>
                <div>Nama & Tanda Tangan</div>
            </td>
            <td>
                <div>Telah Diterima & Disetujui,</div>
                <div>(Supplier / Ekspedisi)</div>
                <div class="signature-space"></div>
                <div class="font-bold">(.......................................)</div>
                <div>Nama & Tanda Tangan</div>
            </td>
        </tr>
    </table> --}}

</body>

</html>
