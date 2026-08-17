<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>{{ $pembelian->no_nota }}</title>
    <style>
        /* dompdf hanya mendukung CSS terbatas: gunakan table-based layout, hindari flexbox/grid */
        @page {
            margin: 25px 35px;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        /* ===== Header ===== */
        .header-table td {
            vertical-align: top;
            padding: 0;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #c8992a;
            margin: 0 0 2px 0;
        }

        .company-address {
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }

        .logo-cell {
            width: 70px;
            vertical-align: top;
        }

        .logo-cell img {
            width: 60px;
        }

        .info-table {
            width: 100%;
            font-size: 10.5px;
        }

        .info-table td {
            padding: 1px 0;
        }

        .info-label {
            width: 90px;
            color: #333;
        }

        .info-colon {
            width: 10px;
        }

        .info-value {
            font-weight: bold;
        }

        .divider {
            border-top: 2px solid #333;
            margin: 8px 0 10px 0;
        }

        /* ===== Title bar ===== */
        .title-bar {
            background-color: #f0c14b;
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            padding: 5px 0;
            margin-bottom: 0;
        }

        /* ===== Items table ===== */
        .items-table {
            margin-top: 0;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #999;
            padding: 4px 6px;
        }

        .items-table thead th {
            background-color: #e6e6e6;
            font-weight: bold;
            text-align: center;
        }

        .items-table td.center {
            text-align: center;
        }

        .items-table td.right {
            text-align: right;
        }

        .items-table tfoot td {
            background-color: #e6e6e6;
            font-weight: bold;
        }

        /* ===== Summary section ===== */
        .summary-table {
            width: 100%;
            margin-top: 10px;
        }

        .summary-left {
            width: 60%;
            vertical-align: top;
            padding-right: 15px;
        }

        .summary-right {
            width: 40%;
            vertical-align: top;
        }

        .terbilang-label {
            background-color: #f0c14b;
            font-weight: bold;
            padding: 3px 6px;
            font-size: 10.5px;
        }

        .terbilang-box {
            border: 1px solid #999;
            padding: 8px 6px;
            font-style: italic;
            font-weight: bold;
            text-align: center;
            text-transform: capitalize;
        }

        .paybox-table td {
            padding: 3px 0;
            font-size: 10.5px;
        }

        .paybox-label {
            width: 90px;
        }

        .rincian-table {
            width: 100%;
        }

        .rincian-table td {
            padding: 3px 4px;
            font-size: 10.5px;
        }

        .rincian-label {
            width: 55%;
        }

        .rincian-colon {
            width: 10px;
        }

        .rincian-value {
            text-align: right;
        }

        .grand-total-row td {
            font-weight: bold;
            border-top: 1px solid #333;
            background-color: #f0c14b;
        }

        /* ===== Signature ===== */
        .signature-table {
            width: 100%;
            margin-top: 25px;
        }

        .signature-cell {
            width: 220px;
            text-align: center;
            float: right;
        }

        .signature-space {
            height: 55px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* ===== Footer ===== */
        .footer-bar {
            background-color: #f0c14b;
            font-size: 9.5px;
            padding: 3px 6px;
            margin-top: 40px;
        }
    </style>
</head>

<body>

    {{-- ===================== HEADER ===================== --}}
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <table>
                    <tr>
                        <td class="logo-cell">
                            {{-- Ganti path logo sesuai lokasi asset di project Anda --}}
                            <img src="{{ public_path('images/wellnobekgron.png') }}" alt="Logo">
                        </td>
                        <td>
                            <div class="company-name">PT WELL MAIRA FOOD</div>
                            <div class="company-address">
                                Kp. Pancurendang RT001/RW005 Cikadut, Cimenyan<br>
                                Bandung, Jawa Barat - 40194 - Indonesia<br>
                                No Telp : 08112286657
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%;">
                <table class="info-table">
                    <tr>
                        <td class="info-label">No Nota</td>
                        <td class="info-colon">:</td>
                        <td class="info-value">{{ $pembelian->no_nota }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Tanggal</td>
                        <td class="info-colon">:</td>
                        <td>{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->translatedFormat('d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">Supplier</td>
                        <td class="info-colon">:</td>
                        <td class="info-value">{{ $pembelian->supplier?->nama_supplier }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Telp</td>
                        <td class="info-colon">:</td>
                        <td>{{ $pembelian->supplier?->telepon_supplier ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Alamat</td>
                        <td class="info-colon">:</td>
                        <td>{{ $pembelian->supplier?->alamat_supplier ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">UP</td>
                        <td class="info-colon">:</td>
                        <td>{{ $pembelian->supplier?->sales ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="title-bar">Purchase Order</div>

    {{-- ===================== TABEL ITEM ===================== --}}
    @php
        // Sub Total = total keseluruhan (harga x kuantitas) + pajak per baris
        // Pajak    = total pajak seluruh item (sudah termasuk dalam Sub Total, ditampilkan sbg informasi)
        $totalDiskon = 0;
        $totalKuantitas = 0;
        $totalPajak = 0;
        $subTotal = 0;
    @endphp

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 20%;">Barang</th>
                <th style="width: 14%;">Deskripsi</th>
                <th style="width: 9%;">Kuantitas</th>
                <th style="width: 8%;">Satuan</th>
                <th style="width: 14%;">Harga</th>
                <th style="width: 14%;">Diskon</th>
                <th style="width: 14%;">Pajak</th>
                <th style="width: 17%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pembelian->detailPembelian as $item)
                @php
                    $subtotalKotor = $item->harga !== null ? $item->harga * $item->kuantitas : 0;
                    $diskonItem = $item->diskon ?? 0;
                    $pajakItem = $item->pajak ?? 0;
                    $subtotalBersih = $subtotalKotor - $diskonItem + $pajakItem;

                    $totalKuantitas += $item->kuantitas;
                    $totalDiskon += $diskonItem;
                    $totalPajak += $pajakItem;
                    $subTotal += $subtotalKotor; // Sub Total kotor sebelum diskon dan pajak
                @endphp
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $item->barang?->nama_barang }}</td>
                    <td class="center">{{ $item->deskripsi ?: '' }}</td>
                    <td class="center">{{ $item->kuantitas }}</td>
                    <td class="center">{{ $item->satuan?->kode_satuan }}</td>
                    <td class="right">
                        {{ $item->harga !== null ? number_format($item->harga, 0, ',', '.') : '-' }}</td>
                    <td class="right">
                        {{ $item->diskon !== null ? number_format($item->diskon, 0, ',', '.') : '-' }}</td>
                    <td class="right">
                        {{ $item->pajak !== null ? number_format($item->pajak, 0, ',', '.') : '-' }}</td>
                    <td class="right">{{ number_format($subtotalBersih, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="center">Tidak ada data barang pada pembelian ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="center">Total Jumlah</td>
                <td class="center">{{ $totalKuantitas }}</td>
                <td colspan="5"></td>
            </tr>
        </tfoot>
    </table>

    {{-- ===================== RINGKASAN & TERBILANG ===================== --}}
    @php
        // Grand Total = Sub Total - Diskon + Pajak (biaya lain diabaikan)
        $grandTotal = $subTotal - $totalDiskon + $totalPajak;
    @endphp

    <table class="summary-table">
        <tr>
            <td class="summary-left">
                <div class="terbilang-label">TERBILANG :</div>
                <div class="terbilang-box">
                    {{ terbilang_rupiah($grandTotal) }} Rupiah
                </div>

                <table class="paybox-table" style="margin-top: 12px;">
                    <tr>
                        <td class="paybox-label">Cara Bayar</td>
                        <td>: {{ $pembelian->cara_bayar }}</td>
                    </tr>
                </table>
            </td>
            <td class="summary-right">
                <table class="rincian-table">
                    <tr>
                        <td class="rincian-label">Sub Total</td>
                        <td class="rincian-colon">:</td>
                        <td class="rincian-value">{{ number_format($subTotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="rincian-label">Diskon</td>
                        <td class="rincian-colon">:</td>
                        <td class="rincian-value">{{ number_format($totalDiskon, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="rincian-label">Pajak (11%)</td>
                        <td class="rincian-colon">:</td>
                        <td class="rincian-value">{{ number_format($totalPajak, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="grand-total-row">
                        <td class="rincian-label">Grand Total</td>
                        <td class="rincian-colon">:</td>
                        <td class="rincian-value">{{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ===================== TANDA TANGAN ===================== --}}
    <table class="signature-table">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: center;">
                Dengan Hormat,
                <div class="signature-space"></div>
                {{-- <div class="signature-name">( {{ $pembelian->penanggung_jawab ?? '.....................' }} )</div> --}}
                <div>Bagian Keuangan</div>
            </td>
        </tr>
    </table>

    {{-- ===================== FOOTER ===================== --}}
    <table class="footer-bar">
        <tr>
            <td style="width: 50%;">Tanggal cetak : {{ now()->format('d/m/Y') }}</td>
            <td style="width: 50%; text-align: right;">Hal 1 dari 1</td>
        </tr>
    </table>

</body>

</html>
