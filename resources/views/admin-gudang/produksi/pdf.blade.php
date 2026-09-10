<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Produksi {{ $produksi->batch_produk }}</title>
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
        <h2>LAPORAN PRODUKSI</h2>
    </div>
    <table class="meta-table">
        <tr>
            <td style="width: 20%;"><strong>Nama Produk</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 38%;">{{ $produksi->barangJadi->nama_produk }}</td>

            <td style="width: 15%;"><strong>Tanggal Produksi</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 23%;">{{ \Carbon\Carbon::parse($produksi->tanggal_produksi)->translatedFormat('d F Y') }}
            </td>
        </tr>
        <tr>
            <td><strong>Batch Produk</strong></td>
            <td>:</td>
            <td>{{ $produksi->batch_produk }}</td>

            <td><strong>Produk Expired</strong></td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($produksi->produk_expired)->translatedFormat('d F Y ') }}</td>
        </tr>
        <tr>
            <td><strong>Hasil Produksi</strong></td>
            <td>:</td>
            <td>{{ $produksi->hasil_produksi }} PCS</td>

            <td><strong>Tujuan</strong></td>
            <td>:</td>
            <td>{{ $produksi->tujuan_produksi }}</td>
        </tr>
        <tr>
            <td><strong>Hasil Lolos QC</strong></td>
            <td>:</td>
            <td>{{ $produksi->hasil_qc }} PCS</td>
        </tr>
    </table>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 25%;">Nama Barang</th>
                <th style="width: 20%;" class="text-center">Kode Batch</th>
                <th style="width: 15%;" class="text-center">Jumlah Keluar</th>
                <th style="width: 10%;" class="text-center">Satuan</th>
                <th style="width: 25%;">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($detailProduksi as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->batch->barang->nama_barang }}</td>
                    <td class="text-center">{{ $item->batch->kode_lot_supplier ?? '-' }}</td>
                    <td class="text-center">{{ $item->jumlah_keluar }}</td>
                    <td class="text-center">{{ $item->batch->barang->satuan->kode_satuan }}</td>
                    <td>{{ $item->deskripsi ?: '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4">Tidak ada data bahan baku yang dicatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- <!-- SECTION TANDA TANGAN -->
    <table class="signature-table">
        <tr>
            <td>
                <div>Dibuat Oleh,</div>
                <div>(Staff Produksi)</div>
                <div class="signature-space"></div>
                <div class="font-bold">(.......................................)</div>
                <div>Nama & Tanda Tangan</div>
            </td>
            <td>
                <div>Diperiksa Oleh,</div>
                <div>(QC / Quality Control)</div>
                <div class="signature-space"></div>
                <div class="font-bold">(.......................................)</div>
                <div>Nama & Tanda Tangan</div>
            </td>
            <td>
                <div>Disetujui Oleh,</div>
                <div>(Kepala Produksi)</div>
                <div class="signature-space"></div>
                <div class="font-bold">(.......................................)</div>
                <div>Nama & Tanda Tangan</div>
            </td>
        </tr>
    </table> --}}
</body>

</html>
