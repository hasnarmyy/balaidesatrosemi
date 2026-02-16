@extends('layouts.pegawai.main')

@section('content')
@php
use Carbon\Carbon;

// Set locale ke Indonesia
Carbon::setLocale('id');

// Ambil tanggal sekarang
$now = Carbon::now();

// Hari dalam bahasa Indonesia
$hari = $now->translatedFormat('l'); // contoh: Selasa

// Tanggal lengkap
$tgl_skrng = $now->translatedFormat('d F Y'); // contoh: 06 Januari 2026

// ✅ QUERY UNTUK CEK STATUS ABSEN HARI INI
$absen_masuk = DB::table('tb_presents')
->where('id_pegawai', $pegawai->id_pegawai)
->whereDate('tanggal', now()->toDateString())
->where('keterangan', 1)
->first();

$absen_pulang = DB::table('tb_presents')
->where('id_pegawai', $pegawai->id_pegawai)
->whereDate('tanggal', now()->toDateString())
->where('keterangan', 2)
->first();

$absen_lembur = DB::table('tb_presents')
->where('id_pegawai', $pegawai->id_pegawai)
->whereDate('tanggal', now()->toDateString())
->where('keterangan', 3)
->first();

$absen_izin = DB::table('tb_presents')
->where('id_pegawai', $pegawai->id_pegawai)
->whereDate('tanggal', now()->toDateString())
->whereIn('keterangan', [4, 5])
->first();

// ✅ CEK APAKAH LEMBUR TERDAFTAR HARI INI
$terdaftar_lembur = DB::table('tb_lembur')
->where('id_pegawai', $pegawai->id_pegawai)
->whereDate('date', now()->toDateString())
->first();

// URL weights Face-API: prioritas lokal (self-host), lalu CDN
$faceWeightsLocal = public_path('face-api-weights/ssd_mobilenetv1_model-weights_manifest.json');
$faceApiWeightsUrls = [];
if (file_exists($faceWeightsLocal)) {
    $faceApiWeightsUrls[] = asset('face-api-weights') . '/';
}
$faceApiWeightsUrls[] = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights/';
$faceApiWeightsUrls[] = 'https://cdn.jsdelivr.net/gh/cgarciagl/face-api.js/weights/';
@endphp

<!-- Face-API.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show flex items-center gap-2" role="alert">
    <i class="fa fa-check-circle"></i>
    <strong>{{ session('success') }}</strong>
    <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show flex items-center gap-2" role="alert">
    <i class="fa fa-times-circle"></i>
    <strong>{{ session('error') }}</strong>
    <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="mt-4 mb-4 p-3 bg-white border shadow-sm lh-sm">
    <div class="row border-bottom mb-4">
        <div class="col-sm-8 pt-2">
            <h6 class="mb-4 bc-header">{{ $title }}</h6>
        </div>
        @if (!$absen_izin)
        <div class="col-sm-4 text-right pb-3">
            <button class="btn btn-round btn-theme" data-toggle="modal" data-target="#myModal">
                <i class="fa fa-plus"></i> Ajukan Cuti
            </button>
        </div>
        @endif
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>TANGGAL</th>
                    <th>ABSEN MASUK</th>
                    @if ($absen_masuk && !$terdaftar_lembur && !$absen_izin)
                    <th>ABSEN PULANG</th>
                    @endif
                    @if ($absen_masuk && $terdaftar_lembur && !$absen_izin)
                    <th>ABSEN LEMBUR</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#</td>
                    <td>{{ $hari }}, {{ $tgl_skrng }}</td>
                    <td>
                        @if ($absen_masuk)
                            @if ($absen_masuk->status >= 1)
                            <span class="badge badge-success">Absen Masuk Terkonfirmasi</span>
                            @else
                            <span class="badge badge-warning">Menunggu Konfirmasi</span>
                            @endif
                        @else
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModalMasuk">
                            <i class="fa fa-camera"></i> Absen Masuk
                        </button>
                        @endif
                    </td>

                    @if ($absen_masuk && !$terdaftar_lembur && !$absen_izin)
                    <td>
                        @if ($absen_pulang)
                            @if ($absen_pulang->status >= 2)
                            <span class="badge badge-success">Absen Pulang Terkonfirmasi</span>
                            @else
                            <span class="badge badge-warning">Menunggu Konfirmasi</span>
                            @endif
                        @else
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModalPulang">
                            <i class="fa fa-camera"></i> Absen Pulang
                        </button>
                        @endif
                    </td>
                    @endif

                    @if ($absen_masuk && $terdaftar_lembur && !$absen_izin)
                    <td>
                        @if ($absen_lembur)
                            @if ($absen_lembur->status >= 3)
                            <span class="badge badge-success">Absen Lembur Terkonfirmasi</span>
                            @else
                            <span class="badge badge-warning">Menunggu Konfirmasi</span>
                            @endif
                        @else
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModalLembur">
                            <i class="fa fa-camera"></i> Absen Lembur
                        </button>
                        @endif
                    </td>
                    @endif
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL ABSEN MASUK --}}
    <div class="modal fade" id="myModalMasuk" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title text-secondary"><strong>Absen Masuk - Verifikasi Wajah</strong></h5>
                    <button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-justify">
                    <form action="{{ route('pegawai.ambilAbsen') }}" method="post" id="formMasuk">
                        @csrf
                        <div class="card-body">
                            <table style="width: 100%;">
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <h2>{{ strtoupper($hari) }}, {{ strtoupper($tgl_skrng) }}</h2>
                                        <h4><span class="jamServer">{{ date('H:i:s') }}</span></h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="20%">Nama</td>
                                    <td>:</td>
                                    <td><b>{{ $pegawai->nama_pegawai }}</b></td>
                                </tr>
                                <tr>
                                    <td>Jabatan</td>
                                    <td>:</td>
                                    <td><b>{{ $pegawai->namjab }}</b></td>
                                </tr>
                            </table><br>

                            <div class="form-group">
                                <label>Latitude
                                    <button type="button" id="refresh_location_masuk" class="btn btn-sm btn-outline-secondary" style="margin-left:8px;">Refresh Lokasi</button>
                                </label>
                                <input type="text" name="latitude" id="latitude" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Longitude</label>
                                <input type="text" name="longitude" id="longitude" class="form-control" readonly>
                            </div>

                            <!-- Face Capture Section -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <label><strong>Ambil Foto Selfie</strong></label>
                                    <div class="text-center p-4" id="camera_placeholder_masuk" style="background: #f8f9fa; border: 2px dashed #ddd; border-radius: 5px;">
                                        <i class="fa fa-camera fa-3x text-muted mb-3"></i><br>
                                        <button type="button" class="btn btn-primary mt-2" id="btn_start_camera_masuk">
                                            <i class="fa fa-video-camera"></i> Aktifkan Kamera
                                        </button>
                                    </div>
                                    <div id="camera_area_masuk" style="display:none;">
                                        <div class="form-group mb-2">
                                            <label class="small">Pilih Kamera</label>
                                            <select id="camera_select_masuk" class="form-control form-control-sm"></select>
                                        </div>
                                        <video id="video_masuk" width="100%" autoplay playsinline style="border: 2px solid #ddd; border-radius: 5px;" muted></video>
                                        <button type="button" class="btn btn-primary btn-block mt-2" id="capture_masuk">
                                            <i class="fa fa-camera"></i> Ambil Foto
                                        </button>
                                    </div>
                                    <canvas id="canvas_masuk" style="display:none;"></canvas>
                                    <img id="preview_masuk" style="display:none; width:100%; margin-top:10px; border: 2px solid #ddd; border-radius: 5px;">
                                </div>
                                <div class="col-md-6">
                                    <label><strong>Status Verifikasi</strong></label>
                                    <div id="face_status_masuk" style="padding: 20px; border: 1px solid #e3e6f0; border-radius: 5px; min-height: 150px; display: flex; align-items: center; justify-content: center; text-align: center;">
                                        <p style="color: #999; margin: 0;">Status verifikasi akan muncul di sini</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden Fields -->
                            <input type="hidden" name="foto_selfie_masuk" id="foto_selfie_masuk_data">
                            <input type="hidden" name="embedding" id="embedding_masuk">
                            <input type="hidden" name="detected_gender" id="detected_gender_masuk">
                        </div>

                        <!-- Error Alert -->
                        <div id="error_masuk" class="alert alert-danger mt-3" style="display:none;"></div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="submit_masuk">
                                <i class="fa fa-paper-plane"></i> Kirim Absen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL ABSEN PULANG --}}
    <div class="modal fade" id="myModalPulang" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title text-secondary"><strong>Absen Pulang - Verifikasi Wajah</strong></h5>
                    <button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-justify">
                    <form action="{{ route('pegawai.ambilAbsenPulang') }}" method="post" id="formPulang">
                        @csrf
                        <div class="card-body">
                            <table style="width: 100%;">
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <h2>{{ strtoupper($hari) }}, {{ strtoupper($tgl_skrng) }}</h2>
                                        <h4><span class="jamServer">{{ date('H:i:s') }}</span></h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="20%">Nama</td>
                                    <td>:</td>
                                    <td><b>{{ $pegawai->nama_pegawai }}</b></td>
                                </tr>
                                <tr>
                                    <td>Jabatan</td>
                                    <td>:</td>
                                    <td><b>{{ $pegawai->namjab }}</b></td>
                                </tr>
                            </table><br>

                            <div class="form-group">
                                <label>Latitude
                                    <button type="button" id="refresh_location_pulang" class="btn btn-sm btn-outline-secondary" style="margin-left:8px;">Refresh Lokasi</button>
                                </label>
                                <input type="text" name="latitude" id="latitude_pulang" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Longitude</label>
                                <input type="text" name="longitude" id="longitude_pulang" class="form-control" readonly>
                            </div>

                            <!-- Face Capture Section -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <label><strong>Ambil Foto Selfie</strong></label>
                                    <div class="text-center p-4" id="camera_placeholder_pulang" style="background: #f8f9fa; border: 2px dashed #ddd; border-radius: 5px;">
                                        <i class="fa fa-camera fa-3x text-muted mb-3"></i><br>
                                        <button type="button" class="btn btn-primary mt-2" id="btn_start_camera_pulang">
                                            <i class="fa fa-video-camera"></i> Aktifkan Kamera
                                        </button>
                                    </div>
                                    <div id="camera_area_pulang" style="display:none;">
                                        <div class="form-group mb-2">
                                            <label class="small">Pilih Kamera</label>
                                            <select id="camera_select_pulang" class="form-control form-control-sm"></select>
                                        </div>
                                        <video id="video_pulang" width="100%" autoplay playsinline style="border: 2px solid #ddd; border-radius: 5px;" muted></video>
                                        <button type="button" class="btn btn-primary btn-block mt-2" id="capture_pulang">
                                            <i class="fa fa-camera"></i> Ambil Foto
                                        </button>
                                    </div>
                                    <canvas id="canvas_pulang" style="display:none;"></canvas>
                                    <img id="preview_pulang" style="display:none; width:100%; margin-top:10px; border: 2px solid #ddd; border-radius: 5px;">
                                </div>
                                <div class="col-md-6">
                                    <label><strong>Status Verifikasi</strong></label>
                                    <div id="face_status_pulang" style="padding: 20px; border: 1px solid #e3e6f0; border-radius: 5px; min-height: 150px; display: flex; align-items: center; justify-content: center; text-align: center;">
                                        <p style="color: #999; margin: 0;">Status verifikasi akan muncul di sini</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden Fields -->
                            <input type="hidden" name="foto_selfie_pulang" id="foto_selfie_pulang_data">
                            <input type="hidden" name="embedding" id="embedding_pulang">
                            <input type="hidden" name="detected_gender" id="detected_gender_pulang">
                        </div>

                        <!-- Error Alert -->
                        <div id="error_pulang" class="alert alert-danger mt-3" style="display:none;"></div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="submit_pulang">
                                <i class="fa fa-paper-plane"></i> Kirim Absen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL ABSEN LEMBUR --}}
    <div class="modal fade" id="myModalLembur" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title text-secondary"><strong>Absen Lembur - Verifikasi Wajah</strong></h5>
                    <button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-justify">
                    <form action="{{ route('pegawai.ambilAbsenLembur') }}" method="post" id="formLembur">
                        @csrf
                        <div class="card-body">
                            <table style="width: 100%;">
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <h2>{{ strtoupper($hari) }}, {{ strtoupper($tgl_skrng) }}</h2>
                                        <h4><span class="jamServer">{{ date('H:i:s') }}</span></h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="20%">Nama</td>
                                    <td>:</td>
                                    <td><b>{{ $pegawai->nama_pegawai }}</b></td>
                                </tr>
                                <tr>
                                    <td>Jabatan</td>
                                    <td>:</td>
                                    <td><b>{{ $pegawai->namjab }}</b></td>
                                </tr>
                            </table><br>

                            <!-- Face Capture Section -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <label><strong>Ambil Foto Selfie</strong></label>
                                    <div class="text-center p-4" id="camera_placeholder_lembur" style="background: #f8f9fa; border: 2px dashed #ddd; border-radius: 5px;">
                                        <i class="fa fa-camera fa-3x text-muted mb-3"></i><br>
                                        <button type="button" class="btn btn-primary mt-2" id="btn_start_camera_lembur">
                                            <i class="fa fa-video-camera"></i> Aktifkan Kamera
                                        </button>
                                    </div>
                                    <div id="camera_area_lembur" style="display:none;">
                                        <div class="form-group mb-2">
                                            <label class="small">Pilih Kamera</label>
                                            <select id="camera_select_lembur" class="form-control form-control-sm"></select>
                                        </div>
                                        <video id="video_lembur" width="100%" autoplay playsinline style="border: 2px solid #ddd; border-radius: 5px;" muted></video>
                                        <button type="button" class="btn btn-primary btn-block mt-2" id="capture_lembur">
                                            <i class="fa fa-camera"></i> Ambil Foto
                                        </button>
                                    </div>
                                    <canvas id="canvas_lembur" style="display:none;"></canvas>
                                    <img id="preview_lembur" style="display:none; width:100%; margin-top:10px; border: 2px solid #ddd; border-radius: 5px;">
                                </div>
                                <div class="col-md-6">
                                    <label><strong>Status Verifikasi</strong></label>
                                    <div id="face_status_lembur" style="padding: 20px; border: 1px solid #e3e6f0; border-radius: 5px; min-height: 150px; display: flex; align-items: center; justify-content: center; text-align: center;">
                                        <p style="color: #999; margin: 0;">Status verifikasi akan muncul di sini</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden Fields -->
                            <input type="hidden" name="foto_selfie_pulang" id="foto_selfie_lembur_data">
                            <input type="hidden" name="embedding" id="embedding_lembur">
                            <input type="hidden" name="detected_gender" id="detected_gender_lembur">
                        </div>

                        <!-- Error Alert -->
                        <div id="error_lembur" class="alert alert-danger mt-3" style="display:none;"></div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="submit_lembur">
                                <i class="fa fa-paper-plane"></i> Kirim Absen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL AJUKAN CUTI --}}
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title text-secondary"><strong>Ajukan Cuti</strong></h5>
                    <button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-justify">
                    <form action="{{ route('pegawai.cuti') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="hidden" name="id_peg" value="{{ $pegawai->id_pegawai }}">
                                    <div class="form-group">
                                        <label>Jenis Izin</label>
                                        <select class="form-control" id="jenisizin" name="keterangan">
                                            <option value="">-pilih-</option>
                                            <option value="4">Izin Sakit</option>
                                            <option value="5">Izin Tidak Masuk</option>
                                        </select>
                                    </div>
                                    <div class="form-group" id="suratsakit" hidden>
                                        <label>Upload Surat Keterangan Sakit</label>
                                        <input type="file" name="foto_selfie_masuk" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Keterangan</label>
                                        <input type="text" name="keterangan_izin" class="form-control"
                                            oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <p>Ket.<br>
                                        -Silahkan pilih jenis izin anda<br>
                                        -Upload bukti keterangan dokter untuk "Izin Sakit"<br>
                                        -Silahkan isi keterangan alasan</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// ======================================
// FACE-API.JS INITIALIZATION & SETUP
// ======================================
const MODEL_URLS = @json($faceApiWeightsUrls);
let faceModelsLoaded = false;
let loadFaceModelsPromise = null;  // Satu promise saja - semua pemanggil tunggu yang sama

function loadWithTimeout(promise, ms, label) {
    const timeout = new Promise((_, reject) =>
        setTimeout(() => reject(new Error('Timeout ' + label)), ms)
    );
    return Promise.race([promise, timeout]);
}

async function loadFaceModels() {
    if (faceModelsLoaded) return;
    if (typeof faceapi === 'undefined') {
        console.error('❌ face-api.js belum dimuat.');
        alert('Face-API.js belum siap. Refresh halaman dan coba lagi.');
        return;
    }
    if (loadFaceModelsPromise) {
        await loadFaceModelsPromise;
        return;
    }
    const MODEL_TIMEOUT = 30000;
    loadFaceModelsPromise = (async () => {
        const baseUrl = MODEL_URLS[0] || 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights/';
        try {
            console.log('Loading SSD...');
            await loadWithTimeout(faceapi.nets.ssdMobilenetv1.loadFromUri(baseUrl), MODEL_TIMEOUT, 'SSD');
            console.log('Loading Landmark...');
            await loadWithTimeout(faceapi.nets.faceLandmark68Net.loadFromUri(baseUrl), MODEL_TIMEOUT, 'Landmark');
            console.log('Loading Recognition...');
            await loadWithTimeout(faceapi.nets.faceRecognitionNet.loadFromUri(baseUrl), MODEL_TIMEOUT, 'Recognition');
            console.log('Loading Gender...');
            try {
                await loadWithTimeout(faceapi.nets.ageGenderNet.loadFromUri(baseUrl), MODEL_TIMEOUT, 'Gender');
            } catch (ge) {
                console.warn('ageGenderNet optional:', ge.message);
            }
            faceModelsLoaded = true;
            console.log('✅ Face-API models loaded');
        } catch (err) {
            console.warn('Primary URL gagal, coba alternatif:', err.message);
            for (let i = 1; i < MODEL_URLS.length; i++) {
                try {
                    await Promise.all([
                        faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URLS[i]),
                        faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URLS[i]),
                        faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URLS[i]),
                    ]);
                    try { await faceapi.nets.ageGenderNet.loadFromUri(MODEL_URLS[i]); } catch (_) {}
                    faceModelsLoaded = true;
                    console.log('✅ Models loaded (fallback)');
                    return;
                } catch (e) {
                    console.warn('URL gagal:', MODEL_URLS[i], e.message);
                }
            }
            alert('Gagal memuat model.\n\nCoba: 1) Cek internet 2) Izin VPN/firewall 3) Pasang weights lokal (FACE_WEIGHTS_SELF_HOST.md)');
        }
    })();
    await loadFaceModelsPromise;
}

// ======================================
// GENERIC FACE CAPTURE SETUP FUNCTION
// ======================================
function setupFaceCapture(type) {
    console.log('setupFaceCapture() init for', type);
    const startCameraBtn = document.getElementById('btn_start_camera_' + type);
    const video = document.getElementById('video_' + type);
    const captureBtn = document.getElementById('capture_' + type);
    const canvas = document.getElementById('canvas_' + type);
    const preview = document.getElementById('preview_' + type);
    const statusDiv = document.getElementById('face_status_' + type);
    const errorDiv = document.getElementById('error_' + type);
    const submitBtn = document.getElementById('submit_' + type);
    const form = document.getElementById('form' + type.charAt(0).toUpperCase() + type.slice(1));

    console.log('setupFaceCapture elements:', {
        startCameraBtnExists: !!startCameraBtn,
        videoExists: !!video,
        captureBtnExists: !!captureBtn,
        canvasExists: !!canvas,
        previewExists: !!preview,
        statusDivExists: !!statusDiv,
        errorDivExists: !!errorDiv,
        submitBtnExists: !!submitBtn,
        formExists: !!form
    });

    // Basic element guards and debug logs
    if (!startCameraBtn) {
        console.error('❌ setupFaceCapture: startCameraBtn not found for', type);
        return;
    }
    if (!video) {
        console.error('❌ setupFaceCapture: video element not found for', type);
        return;
    }
    if (!captureBtn) {
        console.error('❌ setupFaceCapture: captureBtn not found for', type);
        return;
    }

    // ========== CAMERA SETUP (dengan pilihan kamera) ==========
    const placeholder = document.getElementById('camera_placeholder_' + type);
    const cameraArea = document.getElementById('camera_area_' + type);
    const cameraSelect = document.getElementById('camera_select_' + type);
    let currentStream = null;

    function stopCameraStream() {
        if (currentStream) {
            currentStream.getTracks().forEach(t => t.stop());
            currentStream = null;
        }
    }

    async function startCameraWithDevice(deviceId) {
        stopCameraStream();
        const constraints = {
            video: deviceId ? { deviceId: { exact: deviceId }, width: { ideal: 640 }, height: { ideal: 480 } } : { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
            audio: false
        };
        currentStream = await navigator.mediaDevices.getUserMedia(constraints);
        video.srcObject = currentStream;
        await video.play().catch(err => console.error('Play error:', err));
    }

    startCameraBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        e.stopPropagation();

        try {
            updateStatus(statusDiv, 'detecting', '⏳ Memuat daftar kamera...');

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('getUserMedia tidak didukung. Pastikan menggunakan HTTPS atau localhost.');
            }

            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoDevices = devices.filter(d => d.kind === 'videoinput');

            if (videoDevices.length === 0) {
                throw new Error('Tidak ada kamera ditemukan!');
            }

            cameraSelect.innerHTML = '';
            videoDevices.forEach((dev, i) => {
                const opt = document.createElement('option');
                opt.value = dev.deviceId;
                opt.textContent = dev.label || 'Kamera ' + (i + 1);
                cameraSelect.appendChild(opt);
            });

            placeholder.style.display = 'none';
            cameraArea.style.display = 'block';
            startCameraBtn.disabled = true;

            await startCameraWithDevice(videoDevices[0].deviceId);
            updateStatus(statusDiv, 'success', '✅ Kamera aktif. Posisikan wajah di tengah, lalu klik "Ambil Foto"');

            cameraSelect.onchange = async function() {
                updateStatus(statusDiv, 'detecting', '⏳ Mengganti kamera...');
                await startCameraWithDevice(this.value);
                updateStatus(statusDiv, 'success', '✅ Kamera diganti. Klik "Ambil Foto"');
            };

            const modalId = type === 'masuk' ? 'myModalMasuk' : (type === 'pulang' ? 'myModalPulang' : 'myModalLembur');
            $('#' + modalId).off('hidden.bs.modal.camera' + type).on('hidden.bs.modal.camera' + type, function() {
                stopCameraStream();
                placeholder.style.display = 'block';
                cameraArea.style.display = 'none';
                startCameraBtn.disabled = false;
            });
        } catch (error) {
            console.error('❌ Camera error:', error);
            const msg = '❌ ' + error.message + '\n\nPastikan:\n1. Izin kamera diberikan ke browser\n2. Tidak ada app lain menggunakan kamera';
            updateStatus(statusDiv, 'error', msg);
            showError(errorDiv, msg);
            startCameraBtn.disabled = false;
            placeholder.style.display = 'block';
            cameraArea.style.display = 'none';
        }
    });

    // ========== CAPTURE & EXTRACT EMBEDDING ==========
    captureBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('📸 Capture button clicked for', type);

        try {
            updateStatus(statusDiv, 'detecting', '⏳ Menangkap foto & mendeteksi wajah...');

            const ctx = canvas.getContext('2d');
            if (!ctx) throw new Error('Canvas context not available');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            if (canvas.width === 0 || canvas.height === 0) {
                throw new Error('Video belum fully loaded. Coba lagi.');
            }

            ctx.drawImage(video, 0, 0);

            // Save as data URL
            const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
            const fotoField = document.getElementById('foto_selfie_' + type + '_data');
            if (fotoField) {
                fotoField.value = dataUrl;
                console.log('✅ Foto captured and saved');
            }

            // Extract embedding
            console.log('🧠 Extracting embedding...');
            await extractEmbedding(canvas, type, statusDiv);

            // Show preview
            if (preview) {
                preview.src = dataUrl;
                preview.style.display = 'block';
            }

            // Stop camera
            stopCameraStream();
            video.srcObject = null;
            cameraArea.style.display = 'none';
            placeholder.style.display = 'block';
            startCameraBtn.disabled = false;
            console.log('✅ Capture process complete for', type);

        } catch (error) {
            console.error('❌ Capture error:', error);
            updateStatus(statusDiv, 'error', '❌ Error: ' + error.message);
            showError(errorDiv, '❌ Gagal menangkap foto: ' + error.message);
        }
    });

    // ========== SUBMIT HANDLER ==========
    submitBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('📤 Submit button clicked for', type);

        errorDiv.style.display = 'none';

        const fotoField = document.getElementById('foto_selfie_' + type + '_data');
        const embeddingField = document.getElementById('embedding_' + type);
        const foto = fotoField ? fotoField.value : '';
        const embedding = embeddingField ? embeddingField.value : '';

        console.log('Form validation:', {
            fotoExists: !!foto,
            embeddingExists: !!embedding,
            type: type
        });

        if (!foto) {
            showError(errorDiv, '❌ Silakan ambil foto selfie terlebih dahulu!');
            return;
        }

        if (!embedding) {
            showError(errorDiv, '❌ Verifikasi wajah gagal atau belum dilakukan! Ambil foto lagi.');
            return;
        }

        // Get location for masuk & pulang
        if (type === 'masuk' || type === 'pulang') {
            const latField = type === 'masuk' ? 'latitude' : 'latitude_pulang';
            const longField = type === 'masuk' ? 'longitude' : 'longitude_pulang';
            const latEl = document.getElementById(latField);
            const longEl = document.getElementById(longField);

            // Check if location already captured
            if (latEl && longEl && latEl.value && longEl.value) {
                console.log('✅ Location already captured:', latEl.value, longEl.value);
                updateStatus(statusDiv, 'success', '✅ Siap mengirim! Lokasi sudah tersimpan. Mengirim...');
                setTimeout(() => form.submit(), 500);
                return;
            }

            // Try to get fresh location
            if (navigator.geolocation) {
                console.log('📍 Requesting fresh geolocation before submit...');
                updateStatus(statusDiv, 'detecting', '⏳ Mendapatkan lokasi terbaru...');
                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        console.log('✅ Fresh geolocation captured');
                        if (latEl) latEl.value = pos.coords.latitude;
                        if (longEl) longEl.value = pos.coords.longitude;
                        updateStatus(statusDiv, 'success', '✅ Lokasi terbaru didapat! Mengirim...');
                        setTimeout(() => form.submit(), 500);
                    },
                    function(error) {
                        console.error('❌ Fresh geolocation failed:', error);
                        if (latEl && longEl && (latEl.value || longEl.value)) {
                            console.log('Using cached location');
                            updateStatus(statusDiv, 'success', '✅ Menggunakan lokasi sebelumnya. Mengirim...');
                            setTimeout(() => form.submit(), 500);
                        } else {
                            const msg = '❌ Tidak bisa mendapatkan lokasi!\n\nError: ' + error.message + '\n\nPastikan Anda memberikan izin lokasi ke browser.';
                            showError(errorDiv, msg);
                        }
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 5000 }
                );
            } else {
                const msg = '❌ Geolocation tidak didukung browser Anda!';
                showError(errorDiv, msg);
            }
        } else {
            // Lembur doesn't need location
            console.log('✅ Lembur type - no location needed. Submitting...');
            updateStatus(statusDiv, 'success', '✅ Mengirim...');
            setTimeout(() => form.submit(), 500);
        }
    });
}

// ========== EXTRACT EMBEDDING FROM CANVAS (optimized) ==========
function createDetectCanvas(sourceCanvas, maxSize) {
    let w = sourceCanvas.width, h = sourceCanvas.height;
    if (w <= maxSize && h <= maxSize) return sourceCanvas;
    const ratio = Math.min(maxSize / w, maxSize / h);
    w = Math.round(w * ratio);
    h = Math.round(h * ratio);
    const small = document.createElement('canvas');
    small.width = w;
    small.height = h;
    small.getContext('2d').drawImage(sourceCanvas, 0, 0, w, h);
    return small;
}

async function extractEmbedding(canvas, type, statusDiv) {
    if (!faceModelsLoaded) {
        updateStatus(statusDiv, 'detecting', '⏳ Memuat model...');
        await loadFaceModels();
        if (!faceModelsLoaded) {
            updateStatus(statusDiv, 'error', '❌ Model gagal dimuat. Refresh halaman.');
            return null;
        }
    }

    updateStatus(statusDiv, 'detecting', '⏳ Mendeteksi wajah...');

    try {
        const detectCanvas = createDetectCanvas(canvas, 608);
        const options = new faceapi.SsdMobilenetv1Options({ minConfidence: 0.7 });

        const detections = await faceapi.detectAllFaces(detectCanvas, options)
            .withFaceLandmarks()
            .withFaceDescriptors()
            .withAgeAndGender();

        if (detections.length === 0) {
            updateStatus(statusDiv, 'error', '❌ Tidak ada wajah terdeteksi. Pastikan wajah terlihat jelas dan pencahayaan cukup.');
            return null;
        }

        if (detections.length > 1) {
            updateStatus(statusDiv, 'error', '⚠️ Terdeteksi lebih dari 1 wajah. Pastikan hanya Anda yang di foto.');
            return null;
        }

        const det = detections[0];
        const embedding = Array.from(det.descriptor);
        document.getElementById('embedding_' + type).value = JSON.stringify(embedding);
        const genderEl = document.getElementById('detected_gender_' + type);
        if (genderEl) genderEl.value = (det.gender || '').toLowerCase();
        updateStatus(statusDiv, 'success', '✅ Wajah terverifikasi! Siap untuk dikirim.');
        return embedding;

    } catch (error) {
        console.error('extractEmbedding error:', error);
        updateStatus(statusDiv, 'error', '❌ Gagal deteksi: ' + (error.message || 'Unknown error') + '. Coba foto ulang.');
        return null;
    }
}

// ========== UI HELPERS ==========
function updateStatus(elem, status, message) {
    elem.innerHTML = `<p style="margin: 0;">${message}</p>`;

    if (status === 'success') {
        elem.style.backgroundColor = '#d4edda';
        elem.style.color = '#155724';
        elem.style.borderColor = '#c3e6cb';
    } else if (status === 'error') {
        elem.style.backgroundColor = '#f8d7da';
        elem.style.color = '#721c24';
        elem.style.borderColor = '#f5c6cb';
    } else {
        elem.style.backgroundColor = '#e2e3e5';
        elem.style.color = '#383d41';
        elem.style.borderColor = '#d6d8db';
    }
}

function showError(elem, msg) {
    elem.innerHTML = msg;
    elem.style.display = 'block';
    elem.scrollIntoView({ behavior: 'smooth' });
}

// ======================================
// INITIALIZE ON PAGE LOAD
// ======================================
document.addEventListener('DOMContentLoaded', async function() {
    console.log('🚀 DOMContentLoaded - Initializing attendance system');
    loadFaceModels();

    // Setup face capture for all three types (guarded)
    try {
        setupFaceCapture('masuk');
        console.log('✅ setupFaceCapture(masuk) initialized');
    } catch (e) { console.error('❌ setupFaceCapture(masuk) failed', e); }

    try {
        setupFaceCapture('pulang');
        console.log('✅ setupFaceCapture(pulang) initialized');
    } catch (e) { console.error('❌ setupFaceCapture(pulang) failed', e); }

    try {
        setupFaceCapture('lembur');
        console.log('✅ setupFaceCapture(lembur) initialized');
    } catch (e) { console.error('❌ setupFaceCapture(lembur) failed', e); }

    // Load models when modals open and request location for masuk/pulang
    function requestLocation(type, statusDiv, errorDiv) {
        if (!navigator.geolocation) {
            console.error('❌ Geolocation not supported');
            showError(errorDiv, '❌ Geolocation tidak didukung browser Anda!');
            return;
        }

        console.log('📍 Requesting geolocation for:', type);
        try {
            updateStatus(statusDiv, 'detecting', '⏳ Mendapatkan lokasi...');

            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    console.log('✅ Geolocation SUCCESS for', type, '- Lat:', pos.coords.latitude, 'Long:', pos.coords.longitude);
                    const latField = type === 'masuk' ? 'latitude' : 'latitude_pulang';
                    const longField = type === 'masuk' ? 'longitude' : 'longitude_pulang';
                    const latEl = document.getElementById(latField);
                    const longEl = document.getElementById(longField);

                    if (latEl && longEl) {
                        latEl.value = pos.coords.latitude;
                        longEl.value = pos.coords.longitude;
                        updateStatus(statusDiv, 'success', '✅ Lokasi berhasil didapatkan!\nLat: ' + pos.coords.latitude.toFixed(6) + '\nLong: ' + pos.coords.longitude.toFixed(6));
                    } else {
                        console.error('❌ Location input elements not found');
                    }
                },
                function(err) {
                    console.error('❌ Geolocation ERROR:', err.code, err.message);
                    let errorMsg = '❌ Gagal mengakses lokasi: ' + err.message;
                    if (err.code === 1) errorMsg += '\n\nBrowser izin akses lokasi ditolak. Buka izin di settings browser.';
                    else if (err.code === 2) errorMsg += '\n\nPositi tidak tersedia. Cek koneksi GPS/internet.';
                    else if (err.code === 3) errorMsg += '\n\nTimeout. Coba lagi.';

                    showError(errorDiv, errorMsg);
                    updateStatus(statusDiv, 'error', '❌ Gagal mendapatkan lokasi. Klik Refresh Lokasi.');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 20000,
                    maximumAge: 60000
                }
            );
        } catch (e) {
            console.error('❌ requestLocation exception:', e);
            showError(errorDiv, '❌ Error saat meminta lokasi: ' + e.message);
        }
    }

    // Modal open event - load models sekali (jika belum), request location
    $('#myModalMasuk, #myModalPulang, #myModalLembur').on('shown.bs.modal', function(e) {
        const modalEl = this;
        console.log('📱 Modal shown:', modalEl.id);

        if (!faceModelsLoaded && !loadFaceModelsPromise) {
            const modalId = (modalEl.id || '').toLowerCase();
            const type = modalId.includes('pulang') ? 'pulang' : (modalId.includes('lembur') ? 'lembur' : 'masuk');
            const statusDiv = document.getElementById('face_status_' + type);
            if (statusDiv) updateStatus(statusDiv, 'detecting', '⏳ Memuat model (sekali saja)...');
            loadFaceModels();
        }

        const modalId = (modalEl.id || '').toLowerCase();
        let type = 'masuk';
        if (modalId.includes('pulang')) type = 'pulang';
        if (modalId.includes('lembur')) type = 'lembur';

        // Auto-request location for masuk & pulang (segera, tanpa setTimeout agar user gesture masih valid)
        if (type === 'masuk' || type === 'pulang') {
            const statusDiv = document.getElementById('face_status_' + type);
            const errorDiv = document.getElementById('error_' + type);
            if (statusDiv && errorDiv) {
                requestLocation(type, statusDiv, errorDiv);
            }
        }
    });

    // Refresh location buttons
    const refreshMasukBtn = document.getElementById('refresh_location_masuk');
    const refreshPulangBtn = document.getElementById('refresh_location_pulang');

    if (refreshMasukBtn) {
        refreshMasukBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('🔄 Manual refresh location - MASUK');
            const statusDiv = document.getElementById('face_status_masuk');
            const errorDiv = document.getElementById('error_masuk');
            requestLocation('masuk', statusDiv, errorDiv);
        });
    }

    if (refreshPulangBtn) {
        refreshPulangBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('🔄 Manual refresh location - PULANG');
            const statusDiv = document.getElementById('face_status_pulang');
            const errorDiv = document.getElementById('error_pulang');
            requestLocation('pulang', statusDiv, errorDiv);
        });
    }

    console.log('✅ DOMContentLoaded initialization complete');
});

// ======================================
// JAM SERVER (EXISTING CODE)
// ======================================
const jamServer = new Date();
setInterval(() => {
    const jam = new Date();
    let jam_sekarang = jam.getHours() + ':' + (jam.getMinutes() < 10 ? '0' : '') + jam.getMinutes() + ':' + (jam.getSeconds() < 10 ? '0' : '') + jam.getSeconds();
    document.querySelectorAll('.jamServer').forEach(el => {
        el.textContent = jam_sekarang;
    });
}, 1000);

</script>
@endsection
