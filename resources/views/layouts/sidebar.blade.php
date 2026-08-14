<ul class="navbar-nav bg-gradient-dark sidebar sidebar-dark accordion" id="accordionSidebar">
    @php
        $homeActive = request()->is('home');
        $mutasiActive = request()->is('mutasi*');
        $barangActive = request()->is(
            'satuan*',
            'barang',
            'barang/*',
            'kode-batch*',
            'resep-produksi*',
            'konversi-barang*',
        );
        $supplierActive = request()->is('supplier*');
        $pengajuanActive = request()->is('pengajuan*');
        $pembelianActive = request()->is('pembelian*');
        $penerimaanActive = request()->is('penerimaan*');
        $produksiActive = request()->is('produksi*');
        $penolakanActive = request()->is('penolakan*');
        $returActive = request()->is('retur*');
        $laporanActive = request()->is('laporan*');
        $produkActive = request()->is('barang-jadi*');
    @endphp

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('images/logowell.png') }}" alt="Logo" srcset="" width="40" height="40">
        </div>
        <div class="sidebar-brand-text mx-3">Inventory Maira</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ $homeActive ? 'active' : '' }}">
        <a class="nav-link {{ $homeActive ? 'active' : '' }}" href="{{ route('home') }}">
            <i class="fa-solid fa-gauge-high"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Nav Item - Mutasi -->
    <li class="nav-item {{ $mutasiActive ? 'active' : '' }}">
        <a class="nav-link {{ $mutasiActive ? 'active' : '' }}" href="{{ route('mutasi.index') }}">
            <i class="fa-solid fa-arrows-rotate"></i>
            <span>Mutasi</span></a>
    </li>

    <!-- Nav Item - Laporan -->
    @if (auth()->user()->hasRole(['Direktur']))
        <li class="nav-item {{ $laporanActive ? 'active' : '' }}">
            <a class="nav-link {{ $laporanActive ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                <i class="fa-solid fa-chart-line"></i>
                <span>Laporan</span></a>
        </li>
    @endif

    <!-- Divider -->
    @if (auth()->user()->hasRole(['Admin Gudang']))
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading text-gray-200 ">
            Master Data
        </div>
    @endif

    <!-- Nav Item - Barang -->
    @if (auth()->user()->hasRole(['Admin Gudang']))
        <li class="nav-item {{ $barangActive ? 'active' : '' }}">
            <a class="nav-link {{ $barangActive ? '' : 'collapsed active' }}" href="#" data-toggle="collapse"
                data-target="#collapseTwo" aria-expanded="{{ $barangActive ? 'true' : 'false' }}"
                aria-controls="collapseTwo">
                <i class="fa-solid fa-box-archive"></i>
                <span>Bahan Baku</span>
            </a>
            <div id="collapseTwo" class="collapse {{ $barangActive ? 'show' : '' }}" aria-labelledby="headingTwo"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header text-gray-600">Daftar Master</h6>
                    <a class="collapse-item {{ request()->is('barang') ? 'active' : '' }}"
                        href="{{ route('barang.index') }}">Bahan Baku</a>
                    <a class="collapse-item {{ request()->is('satuan*') ? 'active' : '' }}"
                        href="{{ route('satuan.index') }}">Satuan</a>
                    <a class="collapse-item {{ request()->is('kode-batch*') ? 'active' : '' }}"
                        href="{{ route('kode-batch.index') }}">Kode
                        Batch</a>
                    <a class="collapse-item {{ request()->is('resep-produksi*') ? 'active' : '' }}"
                        href="{{ route('resep-produksi.index') }}">Resep Produksi</a>
                    <a class="collapse-item {{ request()->is('konversi-barang*') ? 'active' : '' }}"
                        href="{{ route('konversi-barang.index') }}">Konversi Bahan Baku</a>
                </div>
            </div>
        </li>
    @endif

    <!-- Nav Item - Produk -->
    @if (auth()->user()->hasRole(['Admin Gudang']))
        <li class="nav-item {{ $produkActive ? 'active' : '' }}">
            <a class="nav-link {{ $produkActive ? 'active' : '' }}" href="{{ route('barang-jadi.index') }}">
                <i class="fa-solid fa-bread-slice"></i>
                <span>Produk</span></a>
        </li>
    @endif

    <!-- Nav Item - Utilities Collapse Menu -->
    {{-- <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
            aria-expanded="true" aria-controls="collapseUtilities">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Utilities</span>
        </a>
        <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Custom Utilities:</h6>
                <a class="collapse-item" href="utilities-color.html">Colors</a>
                <a class="collapse-item" href="utilities-border.html">Borders</a>
                <a class="collapse-item" href="utilities-animation.html">Animations</a>
                <a class="collapse-item" href="utilities-other.html">Other</a>
            </div>
        </div>
    </li> --}}

    <!-- Nav Item - Alasan Penolakan -->
    {{-- @if (auth()->user()->hasRole(['Admin Gudang']))
        <li class="nav-item {{ $alasanActive ? 'active' : '' }}">
            <a class="nav-link {{ $alasanActive ? 'active' : '' }}" href="{{ route('alasan-penolakan.index') }}">
                <i class="fa-regular fa-circle-xmark"></i>
                <span>Alasan Penolakan</span></a>
        </li>
    @endif --}}

    <!-- Nav Item - Supplier -->
    @if (auth()->user()->hasRole(['Bagian Keuangan']))
        <li class="nav-item {{ $supplierActive ? 'active' : '' }}">
            <a class="nav-link {{ $supplierActive ? 'active' : '' }}" href="{{ route('supplier.index') }}">
                <i class="fa-solid fa-truck-field-un"></i>
                <span>Supplier</span></a>
        </li>
    @endif

    <!-- Divider -->
    @if (auth()->user()->hasRole(['Admin Gudang', 'Bagian Keuangan']))
        <hr class="sidebar-divider">

        <!-- Heading Masuk -->
        <div class="sidebar-heading text-gray-200">
            Transaksi Masuk
        </div>
    @endif

    <!-- Nav Item - Pages Collapse Menu -->
    {{-- <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true"
            aria-controls="collapsePages">
            <i class="fas fa-fw fa-folder"></i>
            <span>Pages</span>
        </a>
        <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Login Screens:</h6>
                <a class="collapse-item" href="login.html">Login</a>
                <a class="collapse-item" href="register.html">Register</a>
                <a class="collapse-item" href="forgot-password.html">Forgot Password</a>
                <div class="collapse-divider"></div>
                <h6 class="collapse-header">Other Pages:</h6>
                <a class="collapse-item" href="404.html">404 Page</a>
                <a class="collapse-item" href="blank.html">Blank Page</a>
            </div>
        </div>
    </li> --}}

    <!-- Nav Item - Pengajuan -->
    @if (auth()->user()->hasRole(['Admin Gudang', 'Bagian Keuangan']))
        <li class="nav-item {{ $pengajuanActive ? 'active' : '' }}">
            <a class="nav-link {{ $pengajuanActive ? 'active' : '' }}" href="{{ route('pengajuan.index') }}">
                <i class="fa-solid fa-pen"></i>
                <span>Pengajuan</span></a>
        </li>
    @endif

    <!-- Nav Item - Pembelian -->
    @if (auth()->user()->hasRole(['Bagian Keuangan']))
        <li class="nav-item {{ $pembelianActive ? 'active' : '' }}">
            <a class="nav-link {{ $pembelianActive ? 'active' : '' }}" href="{{ route('pembelian.index') }}">
                <i class="fa-solid fa-cart-shopping"></i>
                <span>Pembelian</span></a>
        </li>
    @endif

    <!-- Nav Item - Penerimaan -->
    @if (auth()->user()->hasRole(['Admin Gudang']))
        <li class="nav-item {{ $penerimaanActive ? 'active' : '' }}">
            <a class="nav-link {{ $penerimaanActive ? 'active' : '' }}" href="{{ route('penerimaan.index') }}">
                <i class="fa-solid fa-hand-holding-hand"></i>
                <span>Penerimaan</span></a>
        </li>
    @endif

    <!-- Divider -->
    @if (auth()->user()->hasRole(['Admin Gudang', 'Bagian Keuangan']))
        <hr class="sidebar-divider">

        <!-- Heading Keluar -->
        <div class="sidebar-heading text-gray-200">
            Transaksi Keluar
        </div>
    @endif

    <!-- Nav Item - Produksi -->
    @if (auth()->user()->hasRole(['Admin Gudang']))
        <li class="nav-item {{ $produksiActive ? 'active' : '' }}">
            <a class="nav-link {{ $produksiActive ? 'active' : '' }}" href="{{ route('produksi.index') }}">
                <i class="fa-solid fa-industry"></i>
                <span>Produksi</span></a>
        </li>
    @endif

    @if (auth()->user()->hasRole(['Admin Gudang', 'Bagian Keuangan']))
        <!-- Nav Item - Penolakan -->
        <li class="nav-item {{ $penolakanActive ? 'active' : '' }}">
            <a class="nav-link {{ $penolakanActive ? 'active' : '' }}" href="{{ route('penolakan.index') }}">
                <i class="fa-solid fa-xmark"></i>
                <span>Penolakan</span></a>
        </li>
    @endif

    <!-- Nav Item - Retur -->
    @if (auth()->user()->hasRole(['Bagian Keuangan', 'Admin Gudang']))
        <li class="nav-item {{ $returActive ? 'active' : '' }}">
            <a class="nav-link {{ $returActive ? 'active' : '' }}" href="{{ route('retur.index') }}">
                <i class="fa-solid fa-truck-ramp-box"></i>
                <span>Retur</span></a>
        </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
