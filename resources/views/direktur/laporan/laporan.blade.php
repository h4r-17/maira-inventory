@extends('layouts.master')

@section('title', '| Laporan')
@section('judul', 'Laporan')
@section('konten')

    {{-- Filter Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.index') }}" method="GET" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_date" class="text-gray-900">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="{{ $startDate ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="end_date" class="text-gray-900">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="{{ $endDate ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Tampilkan
                            </button>
                            @php
                                $isReady = isset($startDate) && isset($endDate) && $pengajuan->count() > 0;
                                $pdfUrl = $isReady
                                    ? route('laporan.pdf', [
                                        'start_date' => $startDate,
                                        'end_date' => $endDate,
                                        'jenis' => 'pengajuan',
                                    ])
                                    : '#';
                            @endphp
                            <a href="{{ $pdfUrl }}" target="_blank" id="btnCetakPdf"
                                class="btn btn-info ml-2 {{ !$isReady ? 'disabled opacity-50' : '' }}"
                                {!! !$isReady ? 'style="pointer-events: none; cursor: not-allowed;" aria-disabled="true"' : '' !!}>
                                <i class="fa-solid fa-print"></i> Cetak PDF
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            @if ($startDate && $endDate)
                <div class="alert alert-info alert-sm py-2 mb-0 mt-2">
                    Menampilkan data dari <strong>{{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }}</strong>
                    sampai <strong>{{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}</strong>
                </div>
            @endif
        </div>
    </div>

    @if ($startDate && $endDate)
        {{-- Tab Navigation --}}
        <style>
            #laporanTabs .nav-link {
                border: 1px solid #e3e6f0;
                border-bottom: none;
                margin-right: 4px;
                color: #858796;
                background-color: #f8f9fc;
                border-radius: 0.35rem 0.35rem 0 0;
                transition: all 0.2s ease-in-out;
            }

            #laporanTabs .nav-link:hover {
                background-color: #eaecf4;
                color: #4e73df;
            }

            #laporanTabs .nav-link.active {
                background-color: #fff;
                border-color: #e3e6f0;
                border-bottom-color: #fff;
                color: #4e73df;
                font-weight: bold;
            }
        </style>
        <ul class="nav nav-tabs mb-0" id="laporanTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tab-pengajuan" data-toggle="tab" href="#pengajuan" role="tab"
                    data-count="{{ $pengajuan->count() }}">
                    <i class="fa-solid fa-pen mr-1"></i> Pengajuan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-pembelian" data-toggle="tab" href="#pembelian" role="tab"
                    data-count="{{ $pembelian->count() }}">
                    <i class="fa-solid fa-cart-shopping mr-1"></i> Pembelian
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-penerimaan" data-toggle="tab" href="#penerimaan" role="tab"
                    data-count="{{ $penerimaan->count() }}">
                    <i class="fa-solid fa-hand-holding-hand mr-1"></i> Penerimaan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-produksi" data-toggle="tab" href="#produksi" role="tab"
                    data-count="{{ $produksi->count() }}">
                    <i class="fa-solid fa-industry mr-1"></i> Produksi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-penolakan" data-toggle="tab" href="#penolakan" role="tab"
                    data-count="{{ $penolakan->count() }}">
                    <i class="fa-solid fa-xmark mr-1"></i> Penolakan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-retur" data-toggle="tab" href="#retur" role="tab"
                    data-count="{{ $retur->count() }}">
                    <i class="fa-solid fa-truck-ramp-box mr-1"></i> Retur
                </a>
            </li>
        </ul>

        <div class="tab-content" id="laporanTabsContent">

            {{-- ==================== TAB PENGAJUAN ==================== --}}
            <div class="tab-pane fade show active" id="pengajuan" role="tabpanel">
                <div class="card shadow mb-4" style="border-top-left-radius: 0; border-top-right-radius: 0;">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Laporan Pengajuan Bahan Baku</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-gray-900" id="tabelPengajuan" width="100%"
                                cellspacing="0">
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
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->no_pengajuan }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y') }}</td>
                                            <td>
                                                <span
                                                    class="badge 
                                                @if ($item->status == 'Disetujui') badge-success
                                                @elseif($item->status == 'Ditolak') badge-danger
                                                @elseif($item->status == 'Pending') badge-warning
                                                @else badge-secondary @endif">
                                                    {{ $item->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada data pengajuan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== TAB PEMBELIAN ==================== --}}
            <div class="tab-pane fade" id="pembelian" role="tabpanel">
                <div class="card shadow mb-4" style="border-top-left-radius: 0; border-top-right-radius: 0;">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Laporan Pembelian Bahan Baku</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-gray-900" id="tabelPembelian" width="100%"
                                cellspacing="0">
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
                                            <td>{{ $loop->iteration }}</td>
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
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== TAB PENERIMAAN ==================== --}}
            <div class="tab-pane fade" id="penerimaan" role="tabpanel">
                <div class="card shadow mb-4" style="border-top-left-radius: 0; border-top-right-radius: 0;">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Laporan Penerimaan Bahan Baku</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-gray-900" id="tabelPenerimaan" width="100%"
                                cellspacing="0">
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
                                            <td>{{ $loop->iteration }}</td>
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
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== TAB PRODUKSI ==================== --}}
            <div class="tab-pane fade" id="produksi" role="tabpanel">
                <div class="card shadow mb-4" style="border-top-left-radius: 0; border-top-right-radius: 0;">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Laporan Produksi</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-gray-900" id="tabelProduksi" width="100%"
                                cellspacing="0">
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
                                            <td>{{ $loop->iteration }}</td>
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
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== TAB PENOLAKAN ==================== --}}
            <div class="tab-pane fade" id="penolakan" role="tabpanel">
                <div class="card shadow mb-4" style="border-top-left-radius: 0; border-top-right-radius: 0;">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Laporan Penolakan Produksi</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-gray-900" id="tabelPenolakan" width="100%"
                                cellspacing="0">
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
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->no_penolakan }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_penolakan)->format('d-m-Y') }}</td>
                                            <td>{{ $item->produksi->batch_produk ?? '-' }}</td>
                                            <td>
                                                <span
                                                    class="badge 
                                                @if ($item->status == 'Diretur') badge-success
                                                @elseif($item->status == 'Ditolak') badge-danger
                                                @elseif($item->status == 'Pending') badge-warning
                                                @else badge-secondary @endif">
                                                    {{ $item->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data penolakan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== TAB RETUR ==================== --}}
            <div class="tab-pane fade" id="retur" role="tabpanel">
                <div class="card shadow mb-4" style="border-top-left-radius: 0; border-top-right-radius: 0;">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Laporan Retur Bahan Baku</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-gray-900" id="tabelRetur" width="100%"
                                cellspacing="0">
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
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->no_retur }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_retur)->format('d-m-Y') }}</td>
                                            <td>{{ $item->pembelian->supplier->nama_supplier ?? '-' }}</td>
                                            <td>
                                                <span
                                                    class="badge 
                                                @if ($item->status == 'Diretur') badge-success
                                                @elseif($item->status == 'Ditolak') badge-danger
                                                @elseif($item->status == 'Pending') badge-warning
                                                @else badge-secondary @endif">
                                                    {{ $item->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data retur.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- end tab-content --}}
    @else
        <div class="alert alert-warning text-center mt-4">
            Silakan pilih periode terlebih dahulu untuk menampilkan laporan.
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        $(function() {
            // Inisialisasi DataTable untuk setiap tab
            var tableConfig = {
                "pageLength": 10,
                "lengthMenu": [
                    [5, 10, 25, 50],
                    [5, 10, 25, 50]
                ],
                "responsive": true,
                "autoWidth": false,
                "columnDefs": [{
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                }],
                language: {
                    emptyTable: "Data belum tersedia.",
                    zeroRecords: "Data yang dicari tidak ditemukan.",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(Disaring dari _MAX_ total data)",
                    paginate: {
                        previous: "<",
                        next: ">"
                    }
                }
            };

            $('#tabelPengajuan').DataTable(tableConfig);
            $('#tabelPembelian').DataTable(tableConfig);
            $('#tabelPenerimaan').DataTable(tableConfig);
            $('#tabelProduksi').DataTable(tableConfig);
            $('#tabelPenolakan').DataTable(tableConfig);
            $('#tabelRetur').DataTable(tableConfig);

            var hasDate = {{ isset($startDate) && isset($endDate) ? 'true' : 'false' }};
            var baseUrl = "{{ route('laporan.pdf') }}";
            var startDate = "{{ $startDate ?? '' }}";
            var endDate = "{{ $endDate ?? '' }}";

            // Re-draw DataTable saat tab aktif supaya kolom tidak kacak
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                var tabId = $(e.target).attr('href').substring(1);
                var dataCount = parseInt($(e.target).attr('data-count')) || 0;

                $('#' + tabId).find('table').DataTable().columns.adjust().draw();

                // Update URL Cetak PDF sesuai tab yang aktif dan disable jika data kosong
                var btnPdf = $('#btnCetakPdf');
                if (hasDate && dataCount > 0) {
                    btnPdf.removeClass('disabled opacity-50').removeAttr('style aria-disabled');
                    var url = new URL(baseUrl);
                    url.searchParams.set('start_date', startDate);
                    url.searchParams.set('end_date', endDate);
                    url.searchParams.set('jenis', tabId);
                    btnPdf.attr('href', url.toString());
                } else {
                    btnPdf.addClass('disabled opacity-50').attr({
                        'style': 'pointer-events: none; cursor: not-allowed;',
                        'aria-disabled': 'true'
                    }).attr('href', '#');
                }
            });
        });
    </script>
@endpush
