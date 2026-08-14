@extends('layouts.master')

@section('title', '- Dashboard')
@section('judul', 'Selamat Datang, ' . Auth::user()->name . '!')
@section('content')
    <!-- Metric Cards Row -->
    <div class="row">
        <!-- Total Bahan Baku Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Bahan Baku</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalBarang) }} Bahan Baku
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Barang Jadi Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Produk</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalBarangJadi) }} Produk
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Supplier Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Supplier</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalSupplier) }} Supplier
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck-loading fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Grafik Transaksi -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Grafik Aktivitas Transaksi (6 Bulan)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="transaksiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Produksi -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Jumlah Produksi (6 Bulan)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="produksiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row 1 -->
    <div class="row">
        <!-- Tabel Aktivitas Terbaru -->
        <div class="col-lg-12 mb-2">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($aktivitasTerbaru as $aktivitas)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($aktivitas['tanggal'])->format('d M Y') }}</td>
                                        <td>
                                            @if ($aktivitas['jenis'] == 'Penerimaan')
                                                <span class="badge badge-success">{{ $aktivitas['jenis'] }}</span>
                                            @elseif($aktivitas['jenis'] == 'Produksi')
                                                <span class="badge badge-primary">{{ $aktivitas['jenis'] }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ $aktivitas['jenis'] }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $aktivitas['keterangan'] }}</td>
                                        <td>
                                            @if ($aktivitas['status'] == 'Pending' || $aktivitas['status'] == 'Diproses')
                                                <span class="badge badge-warning">{{ $aktivitas['status'] }}</span>
                                            @elseif($aktivitas['status'] == 'Selesai' || $aktivitas['status'] == 'Disetujui' || $aktivitas['status'] == 'Diterima')
                                                <span class="badge badge-success">{{ $aktivitas['status'] }}</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $aktivitas['status'] }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada aktivitas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row 2 -->
    <div class="row">
        <!-- Tabel Pembelian Terbaru -->
        <div class="col-lg-6 mb-2">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pembelian Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>No Nota</th>
                                    <th>Tanggal</th>
                                    <th>Supplier</th>
                                    <th>Cara Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pembelianTerbaru as $pembelian)
                                    <tr>
                                        <td>{{ $pembelian->no_nota }}</td>
                                        <td>{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d M Y') }}
                                        </td>
                                        <td>{{ $pembelian->supplier->nama_supplier ?? '-' }}</td>
                                        <td>{{ $pembelian->cara_bayar }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data pembelian.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <!-- Tabel Produksi Terbaru -->
        <div class="col-lg-6 mb-2">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Produksi Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Batch</th>
                                    <th>Tanggal</th>
                                    <th>Produk</th>
                                    <th>Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($produksiTerbaru as $produksi)
                                    <tr>
                                        <td>{{ $produksi->batch_produk }}</td>
                                        <td>{{ \Carbon\Carbon::parse($produksi->tanggal_produksi)->format('d M Y') }}</td>
                                        <td>{{ $produksi->barangJadi->nama_produk ?? '-' }}</td>
                                        <td>{{ number_format($produksi->hasil_produksi) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data produksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tabel Penerimaan -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Penerimaan Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>No Registrasi</th>
                                    <th>Tanggal</th>
                                    <th>Supplier</th>
                                    <th>No Faktur</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($penerimaanTerbaru as $penerimaan)
                                    <tr>
                                        <td>{{ $penerimaan->no_registrasi }}</td>
                                        <td>{{ \Carbon\Carbon::parse($penerimaan->tanggal_masuk)->format('d M Y') }}</td>
                                        <td>{{ $penerimaan->pembelian->supplier->nama_supplier ?? '-' }}</td>
                                        <td>{{ $penerimaan->no_faktur ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data penerimaan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <!-- Tabel Retur Terbaru -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Retur Pending & Diretur</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>No Retur</th>
                                    <th>Tanggal</th>
                                    <th>Supplier</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($returTerbaru as $retur)
                                    <tr>
                                        <td>{{ $retur->no_retur }}</td>
                                        <td>{{ \Carbon\Carbon::parse($retur->tanggal_retur)->format('d M Y') }}</td>
                                        <td>{{ $retur->pembelian->supplier->nama_supplier ?? '-' }}</td>
                                        <td>
                                            @if ($retur->status == 'Pending' || $retur->status == 'Diproses')
                                                <span class="badge badge-warning">{{ $retur->status }}</span>
                                            @else
                                                <span class="badge badge-success">{{ $retur->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data retur.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('template/vendor/chart.js/Chart.min.js') }}"></script>
    <script>
        // Set new default font family and font color to mimic Bootstrap's default styling
        Chart.defaults.global.defaultFontFamily = 'Nunito',
            '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#858796';

        function number_format(number, decimals, dec_point, thousands_sep) {
            number = (number + '').replace(',', '').replace(' ', '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function(n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }

        // Data dari Controller
        var labelsBulan = {!! json_encode($labelsBulan) !!};
        var dataProduksi = {!! json_encode($dataProduksi) !!};
        var dataPembelian = {!! json_encode($dataPembelian) !!};
        var dataPenerimaan = {!! json_encode($dataPenerimaan) !!};
        var dataPengajuan = {!! json_encode($dataPengajuan) !!};
        var dataProduksiCount = {!! json_encode($dataProduksiCount) !!};
        var dataPenolakan = {!! json_encode($dataPenolakan) !!};
        var dataRetur = {!! json_encode($dataRetur) !!};

        // Area Chart - Transaksi
        var ctxArea = document.getElementById("transaksiChart");
        if (ctxArea) {
            var myLineChart = new Chart(ctxArea, {
                type: 'line',
                data: {
                    labels: labelsBulan,
                    datasets: [{
                        label: "Pembelian",
                        lineTension: 0.3,
                        backgroundColor: "transparent",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: dataPembelian,
                    }, {
                        label: "Penerimaan",
                        lineTension: 0.3,
                        backgroundColor: "transparent",
                        borderColor: "rgba(28, 200, 138, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(28, 200, 138, 1)",
                        pointBorderColor: "rgba(28, 200, 138, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(28, 200, 138, 1)",
                        pointHoverBorderColor: "rgba(28, 200, 138, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: dataPenerimaan,
                    }, {
                        label: "Pengajuan",
                        lineTension: 0.3,
                        backgroundColor: "transparent",
                        borderColor: "rgba(54, 185, 204, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(54, 185, 204, 1)",
                        pointBorderColor: "rgba(54, 185, 204, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(54, 185, 204, 1)",
                        pointHoverBorderColor: "rgba(54, 185, 204, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: dataPengajuan,
                    }, {
                        label: "Produksi",
                        lineTension: 0.3,
                        backgroundColor: "transparent",
                        borderColor: "rgba(246, 194, 62, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(246, 194, 62, 1)",
                        pointBorderColor: "rgba(246, 194, 62, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(246, 194, 62, 1)",
                        pointHoverBorderColor: "rgba(246, 194, 62, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: dataProduksiCount,
                    }, {
                        label: "Penolakan",
                        lineTension: 0.3,
                        backgroundColor: "transparent",
                        borderColor: "rgba(231, 74, 59, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(231, 74, 59, 1)",
                        pointBorderColor: "rgba(231, 74, 59, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(231, 74, 59, 1)",
                        pointHoverBorderColor: "rgba(231, 74, 59, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: dataPenolakan,
                    }, {
                        label: "Retur",
                        lineTension: 0.3,
                        backgroundColor: "transparent",
                        borderColor: "rgba(90, 92, 105, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(90, 92, 105, 1)",
                        pointBorderColor: "rgba(90, 92, 105, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(90, 92, 105, 1)",
                        pointHoverBorderColor: "rgba(90, 92, 105, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: dataRetur,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 25,
                            bottom: 0
                        }
                    },
                    scales: {
                        xAxes: [{
                            time: {
                                unit: 'date'
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 7
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                maxTicksLimit: 5,
                                padding: 10,
                                callback: function(value, index, values) {
                                    return number_format(value);
                                }
                            },
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }],
                    },
                    legend: {
                        display: true
                    },
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        titleMarginBottom: 10,
                        titleFontColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem, chart) {
                                var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                                return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
                            }
                        }
                    }
                }
            });
        }

        // Bar Chart - Produksi
        var ctxBar = document.getElementById("produksiChart");
        if (ctxBar) {
            var myBarChart = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: labelsBulan,
                    datasets: [{
                        label: "Hasil Produksi",
                        backgroundColor: "#4e73df",
                        hoverBackgroundColor: "#2e59d9",
                        borderColor: "#4e73df",
                        data: dataProduksi,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 25,
                            bottom: 0
                        }
                    },
                    scales: {
                        xAxes: [{
                            time: {
                                unit: 'month'
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 6
                            },
                            maxBarThickness: 25,
                        }],
                        yAxes: [{
                            ticks: {
                                maxTicksLimit: 5,
                                padding: 10,
                                callback: function(value, index, values) {
                                    return number_format(value);
                                }
                            },
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }],
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        titleMarginBottom: 10,
                        titleFontColor: '#6e707e',
                        titleFontSize: 14,
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem, chart) {
                                var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                                return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
                            }
                        }
                    },
                }
            });
        }
    </script>
@endpush
