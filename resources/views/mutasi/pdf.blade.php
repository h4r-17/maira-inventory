<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Mutasi {{ $barang->nama_barang }}</title>
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
    </style>
</head>

<body>
    <div class="header-title">
        <h2>KARTU STOK BAHAN BAKU</h2>
    </div>
    <table class="meta-table">
        <tr>
            <td style="width: 15%;"><strong>Kode Bahan Baku</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;">{{ $barang->kode_barang }}</td>

            <td style="width: 15%;"><strong>Periode</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;">{{ \Carbon\Carbon::parse($start_date)->translatedFormat('d F Y') }} s/d
                {{ \Carbon\Carbon::parse($end_date)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td style="width: 15%;"><strong>Nama Bahan Baku</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;">{{ $barang->nama_barang }}</td>

            <td style="width: 15%;"><strong>Satuan</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;">{{ $barang->satuan->nama_satuan ?? '-' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 9%;" class="text-center">Tanggal</th>
                <th style="width: 14%;" class="text-center">No. Dokumen</th>
                <th style="width: 13%;" class="text-center">Kode Batch</th>
                <th style="width: 10%;" class="text-center">Expired Date</th>
                <th style="width: 9%;" class="text-center">Tipe</th>
                <th style="width: 17%;" class="text-center">Keterangan</th>
                <th style="width: 8%;" class="text-center">Masuk</th>
                <th style="width: 8%;" class="text-center">Keluar</th>
                <th style="width: 8%;" class="text-center">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @if (count($mutasi) > 0)
                <tr>
                    <td colspan="9" class="text-right font-bold">Saldo Awal</td>
                    <td class="font-bold">{{ number_format($saldoAwal) }}</td>
                </tr>
            @endif
            @php $saldo = $saldoAwal; @endphp
            @forelse($mutasi as $index => $item)
                @php
                    if ($item->tipe != 'Retur') {
                        $saldo = (float) $saldo + (float) $item->masuk - (float) $item->keluar;
                    } else {
                        $saldo = (float) $saldo;
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                    <td class="text-center">{{ $item->no_dokumen }}</td>
                    <td class="text-center">{{ $item->kode_lot_supplier ?? ($item->kode_batch ?? '-') }}</td>
                    <td class="text-center">
                        {{ $item->expired_date ? \Carbon\Carbon::parse($item->expired_date)->format('d-m-Y') : '-' }}
                    </td>
                    <td class="text-center">{{ $item->tipe }}</td>
                    <td>{{ $item->keterangan ?? '' }}</td>
                    <td class="text-center">
                        {{ $item->masuk > 0 ? number_format($item->masuk) : '' }}</td>
                    <td class="text-center">
                        {{ $item->keluar > 0 ? number_format($item->keluar) : '' }}</td>
                    <td class="text-center">{{ number_format($saldo) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-4">Tidak ada transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if (count($mutasi) > 0)
            <tfoot>
                <tr>
                    <td colspan="9" class="text-right font-bold">Saldo Akhir</td>
                    <td class="font-bold text-center">{{ number_format($saldo) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
</body>

</html>
