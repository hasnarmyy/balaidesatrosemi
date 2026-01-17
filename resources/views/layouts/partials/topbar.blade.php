<div class="loader-wrapper">
    <div class="loader-circle">
        <div class="loader-wave"></div>
    </div>
</div>

<div class="container-fluid">
    <div class="row header shadow-sm">
        <div class="col-sm-3 pl-0 text-center header-logo">
            <div class="bg-theme mr-3 pt-3 pb-3 mb-0">
                <h6 style="white-space: nowrap; font-size: 0.9rem; font-weight: 600;">
                    <strong>Aplikasi Absensi dan Penggajian</strong>
                </h6>

            </div>
        </div>

        <div class="col-sm-9 header-menu pt-2 pb-0">
            <div class="row">
                <div class="col-sm-4 col-8 pl-0">
                    <span class="menu-icon" onclick="toggle_sidebar()">
                        <span id="sidebar-toggle-btn"></span>
                    </span>
                </div>

                <div class="col-sm-8 col-4 text-right flex-header-menu justify-content-end">
                    <div class="mr-4">
                        <a href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ $user->name }}</span>
                            <img src="{{ asset('storage/' . $user->image) }}" class="rounded-circle"
                                width="40px" height="40px">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right mt-13" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa fa-power-off pr-2"></i> Logout
                            </a>

                            <form id="logout-form" action="{{ route('auth.logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
