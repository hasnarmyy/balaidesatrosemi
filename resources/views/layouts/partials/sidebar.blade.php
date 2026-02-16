<div class="col-sm-3 col-xs-6 sidebar pl-0">
    <div class="inner-sidebar mr-3">

        <div class="avatar text-center">
            <img src="{{ asset('storage/' . $user->image) }}" alt="" class="rounded-circle" />
            <p><strong>{{ $user->name }}</strong></p>
            <span class="text-primary small"><strong>Selamat Datang</strong></span>
        </div>

        <div class="sidebar-menu-container">
            <ul class="sidebar-menu mt-4 mb-4">

                @if ($user->role_id == 1)
                <li class="parent">
                    <a href="{{ url('admin') }}">
                        <i class="fa fa-dashboard mr-3"></i>
                        <span class="none">Dashboard</span>
                    </a>
                </li>

                <li class="parent">
                    <a href="#" onclick="toggle_menu('datamaster'); return false">
                        <i class="fa fa-book mr-3"></i>
                        <span class="none">Data Master <i
                                class="fa fa-angle-down pull-right align-bottom"></i></span>
                    </a>
                    <ul class="children" id="datamaster">
                        <li class="child">
                            <a href="{{ url('admin/jabatan') }}" class="ml-4">
                                <i class="fa fa-angle-right mr-2"></i>Data Jabatan
                            </a>
                        </li>
                        <li class="child">
                            <a href="{{ url('admin/pegawai') }}" class="ml-4">
                                <i class="fa fa-angle-right mr-2"></i>Data Pegawai
                            </a>
                        </li>
                        <li class="child">
                            <a href="{{ url('admin/kantor') }}" class="ml-4">
                                <i class="fa fa-angle-right mr-2"></i>Data Lokasi Kantor
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="parent">
                    <a href="#" onclick="toggle_menu('akun'); return false">
                        <i class="fas fa-user-circle mr-3"></i>
                        <span class="none">Data Akun <i
                                class="fa fa-angle-down pull-right align-bottom"></i></span>
                    </a>
                    <ul class="children" id="akun">
                        <li class="child">
                            <a href="{{ url('admin/akun-pegawai') }}" class="ml-4">
                                <i class="fa fa-angle-right mr-2"></i>Akun Pegawai
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="parent">
                    <a href="#" onclick="toggle_menu('datalembur'); return false">
                        <i class="fa fa-calendar mr-3"></i>
                        <span class="none">Data Lembur <i
                                class="fa fa-angle-down pull-right align-bottom"></i></span>
                    </a>
                    <ul class="children" id="datalembur">
                        <li class="child">
                            <a href="{{ url('admin/tambah-lembur') }}" class="ml-4">
                                <i class="fa fa-angle-right mr-2"></i>Lembur Hari Ini
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="parent">
                    <a href="#" onclick="toggle_menu('absensi'); return false">
                        <i class="fa fa-bookmark mr-3"></i>
                        <span class="none">Konfirmasi Absensi <i
                                class="fa fa-angle-down pull-right align-bottom"></i></span>
                    </a>
                    <ul class="children" id="absensi">
                        <li class="child">
                            <a href="{{ url('admin/tampil-konfirmasi') }}" class="ml-4">
                                <i class="fa fa-angle-right mr-2"></i>Konfirmasi Absen
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="parent">
                    <a href="#" onclick="toggle_menu('tunjangan'); return false">
                        <i class="fa fa-credit-card mr-3"></i>
                        <span class="none">Data Gaji Pegawai <i
                                class="fa fa-angle-down pull-right align-bottom"></i></span>
                    </a>
                    <ul class="children" id="tunjangan">
                        <li class="child">
                            <a href="{{ url('admin/tpp-bulanan') }}" class="ml-4">
                                <i class="fa fa-angle-right mr-2"></i>Gaji Pegawai Bulanan
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="parent">
                    <a href="#" onclick="toggle_menu('laporan'); return false">
                        <i class="fa fa-file-powerpoint mr-3"></i>
                        <span class="none">Laporan <i class="fa fa-angle-down pull-right align-bottom"></i></span>
                    </a>
                    <ul class="children" id="laporan">
                        <li class="child">
                            <a href="{{ url('admin/lembur-bulanan') }}" class="ml-4">
                                <i class="fa fa-angle-right mr-2"></i>Data Lembur Bulanan
                            </a>
                        </li>
                        <li class="child">
                            <a href="{{ url('admin/absen-bulanan') }}" class="ml-4">
                                <i class="fa fa-angle-right mr-2"></i>Detail Absen Bulanan
                            </a>
                        </li>
                        <li class="child">
                            <a href="{{ url('admin/laporan-tpp-bulanan') }}" class="ml-4">
                                <i class="fa fa-angle-right mr-2"></i>Data Gaji Pegawai Bulanan
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="parent">
                    <a href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out-alt mr-3"></i>
                        <span class="none">Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('auth.logout') }}" method="POST"
                        style="display: none;">
                        @csrf
                    </form>
                </li>
                @endif

                @if ($user->role_id == 2)
                <li class="parent">
                    <a href="{{ url('pegawai') }}">
                        <i class="fa fa-dashboard mr-3"></i>
                        <span class="none">Dashboard</span>
                    </a>
                </li>

                <li class="parent">
                    <a href="#" onclick="toggle_menu('wajah'); return false">
                        <i class="fa fa-face-smile mr-3"></i>
                        <span class="none">Face Recognition <i
                                class="fa fa-angle-down pull-right align-bottom"></i></span>
                    </a>
                    <ul class="children" id="wajah">
                        <li class="child">
                            <a href="{{ route('pegawai.enrollFace') }}" class="ml-4">
                                <i class="fa fa-camera mr-2"></i>Registrasi Wajah
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="parent">
                    <a href="#" onclick="toggle_menu('absensi'); return false">
                        <i class="fa fa-bookmark mr-3"></i>
                        <span class="none">Data Absensi <i
                                class="fa fa-angle-down pull-right align-bottom"></i></span>
                    </a>
                    <ul class="children" id="absensi">
                        <li class="child">
                            <a href="{{ route('pegawai.absenHarian') }}" class="ml-4">
                                <i class="fa fa-angle-right mr-2"></i>Ambil Absen
                            </a>
                        </li>
                        <li class="child">
                            <a href="{{ url('pegawai/konfirmasi-absen') }}" class="ml-4">
                                <i class="fa fa-angle-right mr-2"></i>Konfirmasi Absen
                            </a>
                        </li>
                        <li class="child">
                            <a href="{{ route('pegawai.absen-bulanan') }}" class="ml-4">
                                <i class="fa fa-angle-right mr-2"></i>Data Bulanan
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="parent">
                    <a href="#" onclick="toggle_menu('tunjangan'); return false">
                        <i class="fa fa-credit-card mr-3"></i>
                        <span class="none">Gaji & Bonus <i
                                class="fa fa-angle-down pull-right align-bottom"></i></span>
                    </a>
                    <ul class="children" id="tunjangan">
                        <li class="child">
                            <a href="{{ route('pegawai.laporan-tpp-bulanan') }}" class="ml-4">
                                <i class="fa fa-angle-right mr-2"></i>Gaji Bulanan
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="parent">
                    <a href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out-alt mr-3"></i>
                        <span class="none">Logout</span>
                    </a>

                    <form id="logout-form" action="{{ route('auth.logout') }}" method="POST"
                        style="display: none;">
                        @csrf
                    </form>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>
