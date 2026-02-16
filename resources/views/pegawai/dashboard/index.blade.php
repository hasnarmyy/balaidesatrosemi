@extends('layouts.pegawai.main')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="font-weight-bold text-dark mb-1">Dashboard</h3>
                    <p class="text-muted mb-0">Selamat datang kembali, {{ $user->name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target=".myModal">
                        <i class="fa fa-user-circle"></i> Profil
                    </button>
                    <button class="btn btn-outline-danger btn-sm" data-toggle="modal" data-target=".myModalpassword">
                        <i class="fa fa-lock"></i> Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if(session('flash'))
    <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-left: 4px solid #17a2b8 !important;">
        <div class="d-flex align-items-center">
            <i class="fa fa-info-circle fa-lg mr-3"></i>
            <div class="flex-grow-1">
                <strong>{{ session('flash') }}</strong>
            </div>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="row mb-4">

        {{-- PROFILE --}}
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <a href="{{ route('pegawai.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-body" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="text-white">
                                <p class="mb-1 text-white-50" style="font-size: 0.85rem;">Data Pegawai</p>
                                <h4 class="font-weight-bold mb-0">Profile</h4>
                            </div>
                            <div class="bg-white shadow-sm" style="width:55px;height:55px;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                <i class="fa fa-user text-primary" style="font-size:1.8rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <a href="{{ route('pegawai.absenHarian') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-body bg-success">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="text-white">
                                <p class="mb-1 text-white-50" style="font-size: 0.85rem;">Hari Ini</p>
                                <h4 class="font-weight-bold mb-0">Ambil Absen</h4>
                            </div>
                            <div class="bg-white shadow-sm"
                                style="width:55px;height:55px;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                <i class="fa fa-fingerprint text-success" style="font-size:1.8rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- REKAP ABSENSI --}}
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <a href="{{ route('pegawai.absen-bulanan') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-body" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="text-white">
                                <p class="mb-1 text-white-50" style="font-size: 0.85rem;">Rekap Bulanan</p>
                                <h4 class="font-weight-bold mb-0">Absensi</h4>
                            </div>
                            <div class="bg-white shadow-sm" style="width:55px;height:55px;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                <i class="fa fa-calendar-alt text-info" style="font-size:1.8rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- GAJI --}}
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <a href="{{ route('pegawai.laporan-tpp-bulanan') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-body bg-warning">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="text-white">
                                <p class="mb-1 text-white-50" style="font-size: 0.85rem;">Penghasilan</p>
                                <h4 class="font-weight-bold mb-0">Gaji & Bonus</h4>
                            </div>
                            <div class="bg-white shadow-sm" style="width:55px;height:55px;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                <i class="fa fa-wallet text-warning" style="font-size:1.8rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header border-0 bg-white py-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 font-weight-bold text-dark">
                        <i class="fa fa-calendar-check text-primary mr-2"></i>
                        Konfirmasi Absen Hari Ini
                    </h5>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Riwayat kehadiran Anda pada hari ini</p>
                </div>
                @php
                $grouped = $konfirmasi_absen->groupBy('tanggal');
                @endphp
                <div class="badge badge-primary badge-pill px-3 py-2" style="font-size: 0.85rem;">
                    {{ $grouped->count() }} Data
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="border-collapse: separate; border-spacing: 0;">
                    <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <tr>
                            <th class="text-white border-0 py-3" style="font-weight: 600; font-size: 0.85rem;">#</th>
                            <th class="text-white border-0 py-3" style="font-weight: 600; font-size: 0.85rem;">
                                <i class="fa fa-calendar mr-1"></i>Tanggal
                            </th>
                            <th class="text-white border-0 py-3" style="font-weight: 600; font-size: 0.85rem;">
                                <i class="fa fa-clock mr-1"></i>Waktu
                            </th>
                            <th class="text-white border-0 py-3" style="font-weight: 600; font-size: 0.85rem;">
                                <i class="fa fa-info-circle mr-1"></i>Keterangan
                            </th>
                            <th class="text-white border-0 py-3" style="font-weight: 600; font-size: 0.85rem;">
                                <i class="fa fa-check-circle mr-1"></i>Status
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $grouped = $konfirmasi_absen->groupBy('tanggal');
                        @endphp

                        @forelse($grouped as $tanggal => $items)
                        @php
                        $absenMasuk = $items->where('keterangan', 1)->first();
                        $absenPulang = $items->where('keterangan', 2)->first();
                        $absenLembur = $items->where('keterangan', 3)->first();
                        $izinSakit = $items->where('keterangan', 4)->first();
                        $izinTidakMasuk = $items->where('keterangan', 5)->first();

                        $absenKeluar = $absenLembur ?? $absenPulang;
                        @endphp

                        <tr style="transition: all 0.3s ease;">
                            <td class="align-middle py-3" style="font-weight: 500;">{{ $loop->iteration }}</td>
                            <td class="align-middle py-3">
                                <span class="text-dark font-weight-500">{{ $tanggal }}</span>
                            </td>

                            {{-- WAKTU --}}
                            <td class="align-middle py-3">
                                @if ($absenMasuk)
                                <span class="badge badge-light text-dark px-3 py-2 mb-1" style="font-size: 0.85rem; display: block; width: fit-content;">
                                    <i class="fa fa-clock mr-1"></i>Masuk: {{ $absenMasuk->waktu }}
                                </span>
                                @endif
                                @if ($absenKeluar)
                                <span class="badge badge-light text-dark px-3 py-2" style="font-size: 0.85rem; display: block; width: fit-content;">
                                    <i class="fa fa-clock mr-1"></i>{{ $absenLembur ? 'Lembur' : 'Pulang' }}: {{ $absenKeluar->waktu }}
                                </span>
                                @endif
                                @if ($izinSakit || $izinTidakMasuk)
                                <span class="badge badge-light text-dark px-3 py-2" style="font-size: 0.85rem;">
                                    <i class="fa fa-clock mr-1"></i>{{ ($izinSakit ?? $izinTidakMasuk)->waktu }}
                                </span>
                                @endif
                            </td>

                            {{-- KETERANGAN --}}
                            <td class="align-middle py-3">
                                @if ($absenMasuk)
                                <span class="badge px-3 py-2 mb-1" style="background: #28a745; color: white; border-radius: 8px; font-size: 0.85rem; display: block; width: fit-content;">
                                    <i class="fa fa-sign-in-alt mr-1"></i>Masuk
                                    @if ($absenMasuk->keterangan_msk == 1)
                                    <span class="badge badge-danger ml-1">TELAT</span>
                                    @endif
                                </span>
                                @endif

                                @if ($absenPulang)
                                <span class="badge px-3 py-2" style="background: #17a2b8; color: white; border-radius: 8px; font-size: 0.85rem; display: block; width: fit-content;">
                                    <i class="fa fa-sign-out-alt mr-1"></i>Pulang
                                </span>
                                @elseif ($absenLembur)
                                <span class="badge px-3 py-2" style="background: #6f42c1; color: white; border-radius: 8px; font-size: 0.85rem; display: block; width: fit-content;">
                                    <i class="fa fa-moon mr-1"></i>Lembur
                                </span>
                                @endif

                                @if ($izinSakit)
                                <span class="badge px-3 py-2" style="background: #fd7e14; color: white; border-radius: 8px; font-size: 0.85rem;">
                                    <i class="fa fa-medkit mr-1"></i>Izin Sakit
                                </span>
                                @elseif ($izinTidakMasuk)
                                <span class="badge px-3 py-2" style="background: #ffc107; color: white; border-radius: 8px; font-size: 0.85rem;">
                                    <i class="fa fa-hand-paper mr-1"></i>Izin Tidak Masuk
                                </span>
                                @endif
                            </td>

                            {{-- STATUS --}}
                            <td class="align-middle py-3">
                                @if ($absenMasuk)
                                <div class="mb-1">
                                    @if($absenMasuk->status >= 1)
                                    <span class="badge badge-success px-3 py-2" style="border-radius: 8px; font-size: 0.85rem;">
                                        <i class="fa fa-check-circle mr-1"></i>Masuk: Dikonfirmasi
                                    </span>
                                    @else
                                    <span class="badge badge-danger px-3 py-2" style="border-radius: 8px; font-size: 0.85rem;">
                                        <i class="fa fa-clock mr-1"></i>Masuk: Menunggu
                                    </span>
                                    @endif
                                </div>
                                @endif

                                @if ($absenPulang)
                                <div>
                                    @if($absenPulang->status >= 2)
                                    <span class="badge badge-success px-3 py-2" style="border-radius: 8px; font-size: 0.85rem;">
                                        <i class="fa fa-check-circle mr-1"></i>Pulang: Dikonfirmasi
                                    </span>
                                    @else
                                    <span class="badge badge-danger px-3 py-2" style="border-radius: 8px; font-size: 0.85rem;">
                                        <i class="fa fa-clock mr-1"></i>Pulang: Menunggu
                                    </span>
                                    @endif
                                </div>
                                @elseif ($absenLembur)
                                <div>
                                    @if($absenLembur->status >= 3)
                                    <span class="badge badge-success px-3 py-2" style="border-radius: 8px; font-size: 0.85rem;">
                                        <i class="fa fa-check-circle mr-1"></i>Lembur: Dikonfirmasi
                                    </span>
                                    @else
                                    <span class="badge badge-danger px-3 py-2" style="border-radius: 8px; font-size: 0.85rem;">
                                        <i class="fa fa-clock mr-1"></i>Lembur: Menunggu
                                    </span>
                                    @endif
                                </div>
                                @endif

                                @if ($izinSakit)
                                <div>
                                    @if($izinSakit->status >= 4)
                                    <span class="badge badge-success px-3 py-2" style="border-radius: 8px; font-size: 0.85rem;">
                                        <i class="fa fa-check-circle mr-1"></i>Sakit: Dikonfirmasi
                                    </span>
                                    @else
                                    <span class="badge badge-danger px-3 py-2" style="border-radius: 8px; font-size: 0.85rem;">
                                        <i class="fa fa-clock mr-1"></i>Sakit: Menunggu
                                    </span>
                                    @endif
                                </div>
                                @elseif ($izinTidakMasuk)
                                <div>
                                    @if($izinTidakMasuk->status >= 5)
                                    <span class="badge badge-success px-3 py-2" style="border-radius: 8px; font-size: 0.85rem;">
                                        <i class="fa fa-check-circle mr-1"></i>Izin: Dikonfirmasi
                                    </span>
                                    @else
                                    <span class="badge badge-danger px-3 py-2" style="border-radius: 8px; font-size: 0.85rem;">
                                        <i class="fa fa-clock mr-1"></i>Izin: Menunggu
                                    </span>
                                    @endif
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fa fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <h6 class="text-muted mb-1">Belum Ada Data</h6>
                                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Belum ada absensi yang tercatat hari ini</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Profil --}}
<div class="modal fade myModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fa fa-user-edit text-primary mr-2"></i>Edit Profil
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ url('pegawai/edit-profil/' . $user->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4">
                    <div class="form-group">
                        <label class="font-weight-600 text-dark" style="font-size: 0.9rem;">Email</label>
                        <input type="text" name="email" class="form-control" value="{{ $user->email }}" readonly style="border-radius: 8px; background-color: #f8f9fa;">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-600 text-dark" style="font-size: 0.9rem;">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="{{ $user->name }}" style="border-radius: 8px;">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-600 text-dark mb-3" style="font-size: 0.9rem;">Foto Profil</label>
                        <div class="row align-items-center">
                            <div class="col-md-4 text-center mb-3 mb-md-0">
                                <img src="{{ url('storage/' . $user->image) }}" class="rounded shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                            <div class="col-md-8">
                                <div class="custom-file">
                                    <input type="file" name="userfilefoto" class="custom-file-input" id="userfilefoto">
                                    <label class="custom-file-label" for="userfilefoto" style="border-radius: 8px;">Pilih file</label>
                                </div>
                                <small class="form-text text-muted mt-2">JPG, PNG maksimal 2MB</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light px-4" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">
                        <i class="fa fa-save mr-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Password --}}
<div class="modal fade myModalpassword" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fa fa-key text-danger mr-2"></i>Ubah Password
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ url('pegawai/edit-password/' . $user->id) }}" method="post">
                @csrf
                <div class="modal-body px-4">

                    {{-- Password Lama --}}
                    <div class="form-group">
                        <label class="font-weight-600 text-dark" style="font-size: 0.9rem;">
                            Password Lama
                        </label>
                        <div class="input-group">
                            <input type="password" name="password_lama" id="password_lama"
                                class="form-control" required
                                style="border-radius: 8px 0 0 8px;">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePassword('password_lama', this)"
                                    style="border-radius: 0 8px 8px 0;">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Password Baru --}}
                    <div class="form-group">
                        <label class="font-weight-600 text-dark" style="font-size: 0.9rem;">
                            Password Baru
                        </label>
                        <div class="input-group">
                            <input type="password" name="password_baru" id="password_baru"
                                class="form-control" required
                                style="border-radius: 8px 0 0 8px;">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePassword('password_baru', this)"
                                    style="border-radius: 0 8px 8px 0;">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Minimal 8 karakter</small>
                    </div>

                    {{-- Konfirmasi Password Baru --}}
                    <div class="form-group">
                        <label class="font-weight-600 text-dark" style="font-size: 0.9rem;">
                            Konfirmasi Password Baru
                        </label>
                        <div class="input-group">
                            <input type="password" name="password_baru1" id="password_baru1"
                                class="form-control" required
                                style="border-radius: 8px 0 0 8px;">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePassword('password_baru1', this)"
                                    style="border-radius: 0 8px 8px 0;">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light px-4"
                        data-dismiss="modal" style="border-radius: 8px;">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-danger px-4"
                        style="border-radius: 8px;">
                        <i class="fa fa-save mr-1"></i>Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>

<style>
    .table-hover tbody tr:hover {
        background-color: #f8f9ff !important;
        transform: scale(1.01);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }

    .custom-file-input:lang(en)~.custom-file-label::after {
        content: "Browse";
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
</style>
@endsection