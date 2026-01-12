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

$terdaftar_lembur = ($cek_lembur->id_pegawai == $pegawai->id_pegawai);
@endphp

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

                    {{-- ========== JIKA IZIN/SAKIT ========== --}}
                    @if ($absen_izin)
                    <td colspan="2" class="text-center">
                        <span class="badge badge-primary">ANDA HARI INI IZIN</span>
                    </td>
                    @else
                    {{-- ========== KOLOM ABSEN MASUK ========== --}}
                    <td>
                        @if (!$absen_masuk)
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModalMasuk">
                            <i class="fa fa-check"></i> Absen Masuk
                        </button>
                        @else
                        @if ($absen_masuk->status == 0)
                        <span class="badge badge-warning">Menunggu Konfirmasi</span>
                        @else
                        <span class="badge badge-success">Absen Masuk Terkonfirmasi</span>
                        @endif
                        @endif
                    </td>

                    {{-- ========== KOLOM ABSEN PULANG (hanya jika TIDAK terdaftar lembur) ========== --}}
                    @if ($absen_masuk && !$terdaftar_lembur)
                    <td>
                        @if (!$absen_pulang)
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModalPulang">
                            <i class="fa fa-check"></i> Absen Pulang
                        </button>
                        @else
                        @if ($absen_pulang->status == 0)
                        <span class="badge badge-warning">Menunggu Konfirmasi</span>
                        @else
                        <span class="badge badge-success">Absen Pulang Terkonfirmasi</span>
                        @endif
                        @endif
                    </td>
                    @endif

                    {{-- ========== KOLOM ABSEN LEMBUR (hanya jika terdaftar lembur) ========== --}}
                    @if ($absen_masuk && $terdaftar_lembur)
                    <td>
                        @if (!$absen_lembur)
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModalLembur">
                            <i class="fa fa-check"></i> Absen Lembur
                        </button>
                        @else
                        @if ($absen_lembur->status == 0)
                        <span class="badge badge-warning">Menunggu Konfirmasi</span>
                        @else
                        <span class="badge badge-success">Absen Lembur Terkonfirmasi</span>
                        @endif
                        @endif
                    </td>
                    @endif
                    @endif
                </tr>
            </tbody>
        </table>
    </div>

    {{-- MODAL ABSEN MASUK --}}
    <div class="modal fade" id="myModalMasuk" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title text-secondary"><strong>Absen Masuk</strong></h5>
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
                                <label>Latitude</label>
                                <input type="text" name="latitude" id="latitude" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Longitude</label>
                                <input type="text" name="longitude" id="longitude" class="form-control" readonly>
                            </div>

                            <div id="camera_section_masuk" class="form-group">
                                <div id="start_camera_masuk" class="text-center p-4" style="background: #f8f9fa; border: 2px dashed #ddd; border-radius: 5px;">
                                    <i class="fa fa-camera fa-3x text-muted mb-3"></i><br>
                                    <button type="button" class="btn btn-primary mt-2" id="btn_start_camera_masuk">
                                        <i class="fa fa-video-camera"></i> Aktifkan Kamera
                                    </button>
                                </div>
                                <video id="video_masuk" width="100%" autoplay style="display:none; border: 2px solid #ddd; border-radius: 5px;"></video>
                                <button type="button" class="btn btn-primary btn-block mt-2" id="capture_masuk" style="display:none;">
                                    <i class="fa fa-camera"></i> Ambil Foto
                                </button>
                                <canvas id="canvas_masuk" style="display:none;"></canvas>
                                <img id="preview_masuk" style="display:none; width:100%; margin-top:10px; border: 2px solid #ddd; border-radius: 5px;">
                            </div>

                            <input type="hidden" name="foto_selfie_masuk" id="foto_selfie_masuk_data">
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

    {{-- MODAL ABSEN PULANG --}}
    <div class="modal fade" id="myModalPulang" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title text-secondary"><strong>Absen Pulang</strong></h5>
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
                                <label>Latitude</label>
                                <input type="text" name="latitude_pulang" id="latitude_pulang" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Longitude</label>
                                <input type="text" name="longitude_pulang" id="longitude_pulang" class="form-control" readonly>
                            </div>

                            <div id="camera_section_pulang" class="form-group">
                                <div id="start_camera_pulang" class="text-center p-4" style="background: #f8f9fa; border: 2px dashed #ddd; border-radius: 5px;">
                                    <i class="fa fa-camera fa-3x text-muted mb-3"></i><br>
                                    <button type="button" class="btn btn-primary mt-2" id="btn_start_camera_pulang">
                                        <i class="fa fa-video-camera"></i> Aktifkan Kamera
                                    </button>
                                </div>
                                <video id="video_pulang" width="100%" autoplay style="display:none; border: 2px solid #ddd; border-radius: 5px;"></video>
                                <button type="button" class="btn btn-primary btn-block mt-2" id="capture_pulang" style="display:none;">
                                    <i class="fa fa-camera"></i> Ambil Foto
                                </button>
                                <canvas id="canvas_pulang" style="display:none;"></canvas>
                                <img id="preview_pulang" style="display:none; width:100%; margin-top:10px; border: 2px solid #ddd; border-radius: 5px;">
                            </div>

                            <input type="hidden" name="foto_selfie_pulang" id="foto_selfie_pulang_data">
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

    {{-- MODAL ABSEN LEMBUR --}}
    <div class="modal fade" id="myModalLembur" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title text-secondary"><strong>Absen Lembur</strong></h5>
                    <button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-justify">
                    <form action="{{ route('pegawai.lembur') }}" method="post" id="formLembur">
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
                                <label>Latitude</label>
                                <input type="text" name="latitude" id="latitude_lembur" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Longitude</label>
                                <input type="text" name="longitude" id="longitude_lembur" class="form-control" readonly>
                            </div>

                            <div id="camera_section_lembur" class="form-group">
                                <div id="start_camera_lembur" class="text-center p-4" style="background: #f8f9fa; border: 2px dashed #ddd; border-radius: 5px;">
                                    <i class="fa fa-camera fa-3x text-muted mb-3"></i><br>
                                    <button type="button" class="btn btn-primary mt-2" id="btn_start_camera_lembur">
                                        <i class="fa fa-video-camera"></i> Aktifkan Kamera
                                    </button>
                                </div>
                                <video id="video_lembur" width="100%" autoplay style="display:none; border: 2px solid #ddd; border-radius: 5px;"></video>
                                <button type="button" class="btn btn-primary btn-block mt-2" id="capture_lembur" style="display:none;">
                                    <i class="fa fa-camera"></i> Ambil Foto
                                </button>
                                <canvas id="canvas_lembur" style="display:none;"></canvas>
                                <img id="preview_lembur" style="display:none; width:100%; margin-top:10px; border: 2px solid #ddd; border-radius: 5px;">
                            </div>

                            <input type="hidden" name="foto_selfie_pulang" id="foto_selfie_lembur_data">
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    getLocation();

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
            showPosition,
            function(error) {
                alert("Gagal ambil lokasi: " + error.message);
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        }
    }

    function showPosition(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        // ABSEN MASUK
        $('#latitude').val(lat);
        $('#longitude').val(lng);

        // ABSEN PULANG
        $('#latitude_pulang').val(lat);
        $('#longitude_pulang').val(lng);

        // ABSEN LEMBUR
        $('#latitude_lembur').val(lat);
        $('#longitude_lembur').val(lng);
    }

    let streamMasuk, streamPulang, streamLembur;

    function startCamera(type) {
        const video = document.getElementById('video_' + type);
        const startBtn = document.getElementById('start_camera_' + type);
        const captureBtn = document.getElementById('capture_' + type);

        const constraints = {
            video: {
                facingMode: 'user',
                width: {
                    ideal: 640
                },
                height: {
                    ideal: 480
                }
            }
        };

        navigator.mediaDevices.getUserMedia(constraints)
            .then(function(stream) {
                if (type === 'masuk') streamMasuk = stream;
                if (type === 'pulang') streamPulang = stream;
                if (type === 'lembur') streamLembur = stream;

                video.srcObject = stream;
                startBtn.style.display = 'none';
                video.style.display = 'block';
                captureBtn.style.display = 'block';
            })
            .catch(function(err) {
                console.error("Error accessing camera: ", err);
                alert("Tidak dapat mengakses kamera: " + err.message + "\n\nSilakan gunakan opsi upload foto atau pastikan izin kamera telah diberikan.");
            });
    }

    function stopCamera(stream) {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    }

    function handleMethodChange(type) {
        const cameraSection = document.getElementById('camera_section_' + type);
        const uploadSection = document.getElementById('upload_section_' + type);
        const cameraRadio = document.getElementById('camera_' + type);

        if (cameraRadio.checked) {
            cameraSection.style.display = 'block';
            uploadSection.style.display = 'none';
        } else {
            cameraSection.style.display = 'none';
            uploadSection.style.display = 'block';
            if (type === 'masuk') stopCamera(streamMasuk);
            if (type === 'pulang') stopCamera(streamPulang);
            if (type === 'lembur') stopCamera(streamLembur);
        }
    }

    function setupCapture(type) {
        const video = document.getElementById('video_' + type);
        const canvas = document.getElementById('canvas_' + type);
        const preview = document.getElementById('preview_' + type);
        const captureBtn = document.getElementById('capture_' + type);
        const photoData = document.getElementById('foto_selfie_' + type + '_data');

        captureBtn.addEventListener('click', function() {
            if (captureBtn.textContent.includes('Ambil Ulang')) {
                video.style.display = 'block';
                preview.style.display = 'none';
                captureBtn.innerHTML = '<i class="fa fa-camera"></i> Ambil Foto';
                captureBtn.classList.remove('btn-warning');
                captureBtn.classList.add('btn-primary');
                photoData.value = '';
                return;
            }

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = canvas.toDataURL('image/jpeg', 0.8);
            photoData.value = imageData;
            preview.src = imageData;
            preview.style.display = 'block';
            video.style.display = 'none';
            captureBtn.innerHTML = '<i class="fa fa-refresh"></i> Ambil Ulang Foto';
            captureBtn.classList.remove('btn-primary');
            captureBtn.classList.add('btn-warning');
        });
    }

    function resetModal(type) {
        if (type === 'masuk') stopCamera(streamMasuk);
        if (type === 'pulang') stopCamera(streamPulang);
        if (type === 'lembur') stopCamera(streamLembur);

        document.getElementById('video_' + type).style.display = 'none';
        document.getElementById('start_camera_' + type).style.display = 'block';
        document.getElementById('capture_' + type).style.display = 'none';
        document.getElementById('preview_' + type).style.display = 'none';
        document.getElementById('file_preview_' + type).style.display = 'none';
        document.getElementById('foto_selfie_' + type + '_data').value = '';

        const fileInput = document.getElementById('file_' + type);
        if (fileInput) fileInput.value = '';

        const captureBtn = document.getElementById('capture_' + type);
        captureBtn.innerHTML = '<i class="fa fa-camera"></i> Ambil Foto';
        captureBtn.classList.remove('btn-warning');
        captureBtn.classList.add('btn-primary');

        $('input[name="photo_method_' + type + '"][value="camera"]').prop('checked', true).parent().addClass('active');
        $('input[name="photo_method_' + type + '"][value="upload"]').parent().removeClass('active');
        document.getElementById('camera_section_' + type).style.display = 'block';
        document.getElementById('upload_section_' + type).style.display = 'none';
    }

    $(document).ready(function() {
        $('#btn_start_camera_masuk').on('click', function() {
            startCamera('masuk');
        });

        $('#btn_start_camera_pulang').on('click', function() {
            startCamera('pulang');
        });

        $('#btn_start_camera_lembur').on('click', function() {
            startCamera('lembur');
        });

        setupCapture('masuk');
        setupCapture('pulang');
        setupCapture('lembur');

        $('input[name="photo_method_masuk"]').change(function() {
            handleMethodChange('masuk');
        });

        $('input[name="photo_method_pulang"]').change(function() {
            handleMethodChange('pulang');
        });

        $('input[name="photo_method_lembur"]').change(function() {
            handleMethodChange('lembur');
        });

        $('#myModalMasuk').on('hidden.bs.modal', function() {
            resetModal('masuk');
        });

        $('#myModalPulang').on('hidden.bs.modal', function() {
            resetModal('pulang');
        });

        $('#myModalLembur').on('hidden.bs.modal', function() {
            resetModal('lembur');
        });

        $('#formMasuk').on('submit', function(e) {
            const photoMethod = $('input[name="photo_method_masuk"]:checked').val();
            const photoData = $('#foto_selfie_masuk_data').val();

            if (photoMethod === 'camera' && !photoData) {
                e.preventDefault();
                alert('Silakan ambil foto selfie terlebih dahulu!');
                return false;
            }

            if (photoMethod === 'upload' && !photoData) {
                e.preventDefault();
                alert('Silakan upload foto selfie terlebih dahulu!');
                return false;
            }
        });

        $('#formPulang').on('submit', function(e) {
            const photoMethod = $('input[name="photo_method_pulang"]:checked').val();
            const photoData = $('#foto_selfie_pulang_data').val();

            if (photoMethod === 'camera' && !photoData) {
                e.preventDefault();
                alert('Silakan ambil foto selfie terlebih dahulu!');
                return false;
            }

            if (photoMethod === 'upload' && !photoData) {
                e.preventDefault();
                alert('Silakan upload foto selfie terlebih dahulu!');
                return false;
            }
        });

        $('#formLembur').on('submit', function(e) {
            const photoMethod = $('input[name="photo_method_lembur"]:checked').val();
            const photoData = $('#foto_selfie_lembur_data').val();

            if (photoMethod === 'camera' && !photoData) {
                e.preventDefault();
                alert('Silakan ambil foto selfie terlebih dahulu!');
                return false;
            }

            if (photoMethod === 'upload' && !photoData) {
                e.preventDefault();
                alert('Silakan upload foto selfie terlebih dahulu!');
                return false;
            }
        });

        // Jam Server
        var serverClocks = $(".jamServer");
        serverClocks.each(function() {
            showServerTime($(this), $(this).text());
        });

        function showServerTime(obj, time) {
            var parts = time.split(":");
            var newTime = new Date();
            newTime.setHours(parseInt(parts[0], 10));
            newTime.setMinutes(parseInt(parts[1], 10));
            newTime.setSeconds(parseInt(parts[2], 10));
            var timeDifference = new Date().getTime() - newTime.getTime();

            var methods = {
                displayTime: function() {
                    var now = new Date(new Date().getTime() - timeDifference);
                    obj.text([
                        methods.leadZeros(now.getHours(), 2),
                        methods.leadZeros(now.getMinutes(), 2),
                        methods.leadZeros(now.getSeconds(), 2)
                    ].join(":"));
                    setTimeout(methods.displayTime, 500);
                },
                leadZeros: function(time, width) {
                    while (String(time).length < width) time = "0" + time;
                    return time;
                }
            };
            methods.displayTime();
        }

        // Jenis Izin Toggle
        $("#jenisizin").change(function() {
            if ($(this).val() == '4') {
                $('#suratsakit').prop('hidden', false);
            } else {
                $('#suratsakit').prop('hidden', true);
            }
        });
    });
</script>
@endsection