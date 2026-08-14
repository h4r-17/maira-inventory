<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pengajuan {{ $pengajuan->no_pengajuan }}</title>
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
            margin-bottom: 20px;
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
            padding: 4px 0;
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
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-table td {
            text-align: center;
            vertical-align: top;
            width: 33.33%;
        }

        .signature-space {
            height: 60px;
        }
    </style>
</head>

<body>
    <div class="header-title">
        <h2>Laporan Pengajuan Bahan Baku</h2>
    </div>
    <table class="meta-table">
        <tr>
            <td style="width: 18%;"><strong>No Pengajuan</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 30%;">{{ $pengajuan->no_pengajuan }}</td>

            <td style="width: 18%;"><strong>Tanggal Pengajuan</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 30%;">{{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->translatedFormat('d F Y') }}
            </td>
        </tr>
        <tr>
            <td><strong>Total Bahan Baku</strong></td>
            <td>:</td>
            <td>{{ $pengajuan->detailPengajuan->count() }} Item</td>
        </tr>
    </table>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 20%">Nama Bahan Baku</th>
                <th style="width: 10%;" class="text-center">Kuantitas</th>
                <th style="width: 10%;" class="text-center">Satuan</th>
                <th style="width: 18%;" class="text-center">Harga</th>
                <th style="width: 20%;" class="text-center">Deskripsi</th>
                <th style="width: 20%;" class="text-center">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengajuan->detailPengajuan as $item)
                @php
                    $subtotal = $item->harga !== null ? $item->harga * $item->kuantitas : null;
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->barang?->nama_barang ?? '-' }}</td>
                    <td class="text-center">{{ $item->kuantitas }}</td>
                    <td class="text-center">{{ $item->satuan?->kode_satuan ?? '-' }}</td>
                    <td>
                        {{ $item->harga !== null ? 'Rp ' . number_format($item->harga, 0, ',', '.') : '' }}
                    </td>
                    <td>{{ $item->deskripsi ?: '' }}</td>
                    <td class="text-center">
                        {{ $subtotal !== null ? 'Rp ' . number_format($subtotal, 0, ',', '.') : '' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data bahan baku pada pengajuan ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-right">Jumlah (Dalam Rp)</th>
                <th class="text-right">Rp {{ number_format($total_harga, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <!-- SECTION TANDA TANGAN (Sesuai Referensi PDF) -->
    {{-- <table class="signature-table">
        <tr>
            <td>
                <div>DIBUAT OLEH,</div>
                <div class="signature-space"></div>
                <div class="font-bold">({{ $pengajuan->user?->name ?? '........................' }})</div>
                <div>Pemohon</div>
            </td>
            <td>
                <div>DIKETAHUI OLEH,</div>
                <div class="signature-space"></div>
                <div class="font-bold">(........................)</div>
                <div>Supervisor / Atasan</div>
            </td>
            <td>
                <div>DISETUJUI OLEH,</div>
                <div class="signature-space"></div>
                <div class="font-bold">(........................)</div>
                <div>Manajer / Manager</div>
            </td>
        </tr>
    </table> --}}
</body>

</html>
