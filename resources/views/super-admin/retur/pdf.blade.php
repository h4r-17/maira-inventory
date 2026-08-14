<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Jalan Retur {{ $retur->no_retur }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #000;
            /* Warna font hitam solid seperti surat jalan cetak */
            margin: 0;
            padding: 20px;
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

        /* HEADER LAYOUT */
        .header-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .header-table td {
            vertical-align: top;
        }

        .surat-jalan-title {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            margin-top: 30px;
            text-transform: uppercase;
        }

        .tujuan-table {
            width: 100%;
            border-collapse: collapse;
        }

        .tujuan-table td {
            padding: 4px 0;
            font-size: 13px;
        }

        .tujuan-line {
            border-bottom: 1px solid #000;
            padding-left: 5px;
        }

        /* TEKS PENGANTAR */
        .pengantar-teks {
            font-size: 12px;
            margin-bottom: 10px;
        }

        /* TABEL DATA */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 8px;
        }

        .data-table th {
            font-size: 11px;
            background-color: #f9f9f9;
        }

        /* SIGNATURE LAYOUT */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
            margin-top: 30px;
        }

        .signature-table td {
            text-align: center;
            vertical-align: top;
            width: 50%;
        }

        .signature-space {
            height: 80px;
        }

        .signature-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            width: 200px;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <!-- Kiri-->
            <td style="width: 50%;">
                <h2 class="surat-jalan-title">SURAT JALAN No. <br><u>{{ $retur->no_retur }}</u></h2>
                <div style="margin-top: 5px; font-size: 11px;">
                    Nota: {{ $retur->pembelian->no_nota ?? '-' }}
                </div>
            </td>

            <!-- Kanan -->
            <td style="width: 50%; padding-left: 20px;">
                <div style="border-bottom: 1px solid #000; margin-bottom: 5px; padding-bottom: 2px;">
                    {{ \Carbon\Carbon::parse($retur->tanggal_retur)->locale('id')->translatedFormat('d F Y') }}
                </div>
                <table class="tujuan-table">
                    <tr>
                        <td style="width: 15%;">Tuan</td>
                        <td class="tujuan-line">
                            {{ $retur->pembelian->supplier->sales ?? '........................................' }}</td>
                    </tr>
                    <tr>
                        <td>Toko</td>
                        <td class="tujuan-line">
                            {{ $retur->pembelian->supplier->nama_supplier ?? '........................................' }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Pengantar -->
    <div class="pengantar-teks">
        Kami kirimkan bahan baku tersebut dibawah ini dengan kendaraan
        ........................................... No. ...................................
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 20%;">Nama Bahan Baku</th>
                <th style="width: 15%;" class="text-center">Kode Batch</th>
                <th style="width: 10%;" class="text-center">Jumlah Retur</th>
                <th style="width: 10%;" class="text-center">Satuan</th>
                <th style="width: 15%;" class="text-center">Tanggal Kadaluarsa</th>
                <th style="width: 20%;" class="text-center">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($retur->detailRetur as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $item->batch->barang->nama_barang ?? '-' }}</td>
                    <td class="text-center">{{ $item->batch->kode_lot_supplier ?? '-' }}</td>
                    <td class="text-center">{{ $item->jumlah_retur ?? '-' }}</td>
                    <td class="text-center">{{ $item->satuan?->kode_satuan ?? '-' }}</td>
                    <td class="text-center">
                        {{ $item->batch->expired_date ? \Carbon\Carbon::parse($item->batch->expired_date)->format('d-m-Y') : '-' }}
                    </td>
                    <td class="text-center">{{ $item->deskripsi ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4">Tidak ada data bahan baku pada retur ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                <div>Tanda Terima</div>
                <div class="signature-space"></div>
                <div>{{ $retur->pembelian->supplier->sales ?? '........................................' }}</div>
            </td>
            <td>
                <div>Hormat Kami,</div>
                <div class="signature-space"></div>
                <div>PT. Well Maira Food</div>
            </td>
        </tr>
    </table>

</body>

</html>
