<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Direktur</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            padding: 0;
        }

        .header p {
            margin: 5px 0;
            color: #555;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 5px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Laporan Aktivitas Inventory</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} -
            {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
    </div>

    <!-- TABEL PENGAJUAN -->
    @if($jenis == 'pengajuan')
    <div class="section-title">Laporan Pengajuan Bahan Baku</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th>No Pengajuan</th>
                <th>Tanggal Pengajuan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengajuan as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->no_pengajuan }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y') }}</td>
                    <td>{{ $item->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data pengajuan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <!-- TABEL PEMBELIAN -->
    @if($jenis == 'pembelian')
    <div class="section-title">Laporan Pembelian Bahan Baku</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th>No Nota</th>
                <th>Tanggal Pembelian</th>
                <th>Supplier</th>
                <th>Cara Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pembelian as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->no_nota }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d-m-Y') }}</td>
                    <td>{{ $item->supplier->nama_supplier ?? '-' }}</td>
                    <td>{{ $item->cara_bayar }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data pembelian.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <!-- TABEL PENERIMAAN -->
    @if($jenis == 'penerimaan')
    <div class="section-title">Laporan Penerimaan Bahan Baku</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th>No Registrasi</th>
                <th>Tanggal Masuk</th>
                <th>Jenis Penerimaan</th>
                <th>No Nota / Pembelian</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($penerimaan as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->no_registrasi }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d-m-Y') }}</td>
                    <td>{{ $item->jenis_penerimaan }}</td>
                    <td>{{ $item->pembelian->no_nota ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data penerimaan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <!-- TABEL PRODUKSI -->
    @if($jenis == 'produksi')
    <div class="section-title">Laporan Produksi</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th>Batch Produk</th>
                <th>Tanggal Produksi</th>
                <th>Nama Produk</th>
                <th>Hasil Produksi</th>
                <th>Expired</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($produksi as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->batch_produk }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_produksi)->format('d-m-Y') }}</td>
                    <td>{{ $item->barangJadi->nama_produk ?? '-' }}</td>
                    <td>{{ $item->hasil_produksi }} PCS</td>
                    <td>{{ \Carbon\Carbon::parse($item->produk_expired)->format('d-m-Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data produksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <!-- TABEL PENOLAKAN -->
    @if($jenis == 'penolakan')
    <div class="section-title">Laporan Penolakan Produksi</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th>No Penolakan</th>
                <th>Tanggal Penolakan</th>
                <th>Batch Produk</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($penolakan as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->no_penolakan }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_penolakan)->format('d-m-Y') }}</td>
                    <td>{{ $item->produksi->batch_produk ?? '-' }}</td>
                    <td>{{ $item->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data penolakan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <!-- TABEL RETUR -->
    @if($jenis == 'retur')
    <div class="section-title">Laporan Retur Bahan Baku</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th>No Retur</th>
                <th>Tanggal Retur</th>
                <th>Supplier</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($retur as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->no_retur }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_retur)->format('d-m-Y') }}</td>
                    <td>{{ $item->pembelian->supplier->nama_supplier ?? '-' }}</td>
                    <td>{{ $item->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data retur.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @endif

</body>

</html>
