<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        @if (auth()->check() &&
                auth()->user()->hasRole(['Admin Gudang', 'Super Admin']))
            <!-- Nav Item - Alerts/Notifikasi -->
            <li class="nav-item dropdown no-arrow mx-1">
                <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell fa-fw" style="color: #000;"></i>
                    <!-- Counter - Alerts -->
                    @if (isset($lowStockItems) && $lowStockItems->count() > 0)
                        <span class="badge badge-danger badge-counter">{{ $lowStockItems->count() }}</span>
                    @endif
                </a>
                <!-- Dropdown - Alerts -->
                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                    aria-labelledby="alertsDropdown">
                    <h6 class="dropdown-header">
                        Notifikasi
                    </h6>
                    @if (isset($lowStockItems) && $lowStockItems->count() > 0)
                        @foreach ($lowStockItems as $item)
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div>
                                    <div class="icon-circle bg-danger">
                                        <i class="fas fa-exclamation-triangle text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <span class="font-weight-bold">
                                        Stok {{ $item->nama_barang }} menipis/Habis! (Sisa:
                                        {{ $item->batchBarang->sum('sisa_persediaan') }}
                                        {{ $item->satuan->nama_satuan ?? '' }})
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <a class="dropdown-item text-center small text-gray-500" href="#">Tidak ada notifikasi</a>
                    @endif
                </div>
            </li>
        @endif

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow d-flex align-items-center">
            <a class="nav-link">
                <span class="mr-2 d-none d-lg-inline text-gray-900">{{ Auth()->user()->name }}</span>
                <i class="fas fa-user" style="color: #000;"></i>
            </a>
            <div class="btn btn-sm btn-danger">
                <a class="text-light" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-light"></i>
                    {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
    </ul>

</nav>
