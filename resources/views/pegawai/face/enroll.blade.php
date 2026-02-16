@extends('layouts.pegawai.main')

@section('content')
@php
$faceWeightsLocal = public_path('face-api-weights/ssd_mobilenetv1_model-weights_manifest.json');
$faceApiWeightsUrls = [];
if (file_exists($faceWeightsLocal)) {
    $faceApiWeightsUrls[] = asset('face-api-weights') . '/';
}
$faceApiWeightsUrls[] = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights/';
$faceApiWeightsUrls[] = 'https://cdn.jsdelivr.net/gh/cgarciagl/face-api.js/weights/';
@endphp

<div class="mt-4 mb-4 p-3 bg-white border shadow-sm lh-sm rounded">
    <h6 class="mb-4 bc-header"><i class="fa fa-user-circle mr-2"></i>Registrasi Wajah</h6>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fa fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    @endif

    @if(isset($samples) && count($samples) > 0)
    <div class="card mb-4">
        <div class="card-header bg-light">
            <strong>Daftar Sample Wajah ({{ count($samples) }})</strong>
            <span class="badge badge-info ml-2">Disarankan 2-3 sample untuk akurasi lebih baik</span>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Preview</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 1; @endphp
                    @foreach($samples as $s)
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>
                            @if($s->image_path)
                                <img src="{{ asset('storage/' . $s->image_path) }}" style="max-width:80px; max-height:80px; object-fit:cover; border:1px solid #ddd; border-radius:4px;" />
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $s->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <form method="POST" action="{{ route('pegawai.enrollFace.delete', $s->id_face_sample) }}" onsubmit="return confirm('Hapus sample ini?');" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i> Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header bg-light">
            <strong>Tambah Sample Wajah Baru</strong>
        </div>
        <div class="card-body">
            <form id="enrollForm" method="post" action="{{ route('pegawai.enrollFace.post') }}">
                @csrf
                <input type="hidden" name="foto_enroll" id="foto_enroll">
                <input type="hidden" name="embedding" id="embedding">
                <input type="hidden" name="detected_gender" id="detected_gender">

                <div class="alert alert-info py-2">
                    <small>
                        <strong>Cara pakai:</strong> Klik "Aktifkan Kamera" → pilih kamera → ambil foto. Atau unggah dari file. Ulangi 2-3 kali dengan sudut/cahaya berbeda.
                    </small>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label><strong>Ambil Foto Selfie</strong></label>
                        <div class="form-group mb-2">
                            <label class="small text-muted">Unggah file</label>
                            <input type="file" id="fileInput" accept="image/*" class="form-control form-control-sm">
                        </div>
                        <div class="text-center p-4" id="camera_placeholder" style="background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 8px;">
                            <i class="fa fa-camera fa-3x text-muted mb-3"></i><br>
                            <button type="button" class="btn btn-primary" id="btn_start_camera">
                                <i class="fa fa-video-camera"></i> Aktifkan Kamera
                            </button>
                        </div>
                        <div id="camera_area" style="display:none;">
                            <div class="form-group mb-2">
                                <label class="small">Pilih Kamera</label>
                                <select id="camera_select" class="form-control form-control-sm"></select>
                            </div>
                            <video id="video_enroll" width="100%" autoplay playsinline style="border: 2px solid #dee2e6; border-radius: 8px;" muted></video>
                            <button type="button" class="btn btn-primary btn-block mt-2" id="capture_enroll">
                                <i class="fa fa-camera"></i> Ambil Foto
                            </button>
                        </div>
                        <canvas id="canvas_enroll" style="display:none;"></canvas>
                        <img id="preview_enroll" style="display:none; width:100%; margin-top:10px; border: 2px solid #dee2e6; border-radius: 8px;">
                    </div>
                    <div class="col-md-6">
                        <label><strong>Status Verifikasi</strong></label>
                        <div id="face_status_enroll" style="padding: 24px; border: 1px solid #e9ecef; border-radius: 8px; min-height: 200px; display: flex; align-items: center; justify-content: center; text-align: center; background: #f8f9fa;">
                            <p class="text-muted mb-0">Status verifikasi akan muncul di sini</p>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4 mb-0">
                    <button type="submit" id="enrollBtn" class="btn btn-success" disabled>
                        <i class="fa fa-save"></i> Simpan Registrasi
                    </button>
                    <button type="button" id="resetBtn" class="btn btn-outline-secondary">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
const MODEL_URLS = @json($faceApiWeightsUrls);
let faceModelsLoaded = false;
let loadModelsPromise = null;
let currentStream = null;

function updateStatus(status, message) {
    const el = document.getElementById('face_status_enroll');
    el.innerHTML = '<p class="mb-0">' + message + '</p>';
    el.style.backgroundColor = status === 'success' ? '#d4edda' : (status === 'error' ? '#f8d7da' : '#e2e3e5');
    el.style.color = status === 'success' ? '#155724' : (status === 'error' ? '#721c24' : '#383d41');
    el.style.borderColor = status === 'success' ? '#c3e6cb' : (status === 'error' ? '#f5c6cb' : '#d6d8db');
}

function compressImage(canvas, maxWidth, maxHeight, quality) {
    maxWidth = maxWidth || 640; maxHeight = maxHeight || 480; quality = quality || 0.75;
    let w = canvas.width, h = canvas.height;
    if (w <= maxWidth && h <= maxHeight) return canvas.toDataURL('image/jpeg', quality);
    const ratio = Math.min(maxWidth / w, maxHeight / h);
    w = Math.round(w * ratio); h = Math.round(h * ratio);
    const c = document.createElement('canvas');
    c.width = w; c.height = h;
    c.getContext('2d').drawImage(canvas, 0, 0, w, h);
    return c.toDataURL('image/jpeg', quality);
}

function createDetectCanvas(sourceCanvas, maxSize) {
    let w = sourceCanvas.width, h = sourceCanvas.height;
    if (w <= maxSize && h <= maxSize) return sourceCanvas;
    const ratio = Math.min(maxSize / w, maxSize / h);
    w = Math.round(w * ratio); h = Math.round(h * ratio);
    const small = document.createElement('canvas');
    small.width = w; small.height = h;
    small.getContext('2d').drawImage(sourceCanvas, 0, 0, w, h);
    return small;
}

function loadWithTimeout(promise, ms, label) {
    const timeout = new Promise((_, reject) =>
        setTimeout(() => reject(new Error('Timeout ' + label)), ms)
    );
    return Promise.race([promise, timeout]);
}

async function loadModels() {
    if (faceModelsLoaded) return;
    if (typeof faceapi === 'undefined') { updateStatus('error', 'Face-API belum siap.'); return; }
    if (loadModelsPromise) { await loadModelsPromise; return; }
    const MODEL_TIMEOUT = 30000;
    loadModelsPromise = (async () => {
        const baseUrl = MODEL_URLS[0] || 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights/';
        try {
            updateStatus('detecting', 'Memuat deteksi wajah (1/4)...');
            await loadWithTimeout(faceapi.nets.ssdMobilenetv1.loadFromUri(baseUrl), MODEL_TIMEOUT, 'SSD');
            updateStatus('detecting', 'Memuat landmark (2/4)...');
            await loadWithTimeout(faceapi.nets.faceLandmark68Net.loadFromUri(baseUrl), MODEL_TIMEOUT, 'Landmark');
            updateStatus('detecting', 'Memuat pengenalan wajah (3/4)...');
            await loadWithTimeout(faceapi.nets.faceRecognitionNet.loadFromUri(baseUrl), MODEL_TIMEOUT, 'Recognition');
            updateStatus('detecting', 'Memuat deteksi gender (4/4)...');
            try {
                await loadWithTimeout(faceapi.nets.ageGenderNet.loadFromUri(baseUrl), MODEL_TIMEOUT, 'Gender');
            } catch (ge) {
                console.warn('ageGenderNet optional, skip:', ge.message);
            }
            faceModelsLoaded = true;
            updateStatus('success', '✅ Model siap. Ambil foto atau unggah gambar.');
        } catch (e) {
            for (let i = 1; i < MODEL_URLS.length; i++) {
                try {
                    updateStatus('detecting', 'Coba URL alternatif (' + (i+1) + '/' + MODEL_URLS.length + ')...');
                    await Promise.all([
                        faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URLS[i]),
                        faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URLS[i]),
                        faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URLS[i]),
                    ]);
                    try { await faceapi.nets.ageGenderNet.loadFromUri(MODEL_URLS[i]); } catch (_) {}
                    faceModelsLoaded = true;
                    updateStatus('success', '✅ Model siap.');
                    return;
                } catch (err) { console.warn('URL gagal:', MODEL_URLS[i], err); }
            }
            updateStatus('error', 'Gagal memuat model. Cek internet atau pasang weights lokal (lihat FACE_WEIGHTS_SELF_HOST.md).');
        }
    })();
    await loadModelsPromise;
}

async function processImage(canvas) {
    if (!faceModelsLoaded) await loadModels();
    if (!faceModelsLoaded) return false;
    updateStatus('detecting', 'Mendeteksi wajah...');
    try {
        const detectCanvas = createDetectCanvas(canvas, 608);
        const options = new faceapi.SsdMobilenetv1Options({ minConfidence: 0.7 });
        const detections = await faceapi.detectAllFaces(detectCanvas, options)
            .withFaceLandmarks()
            .withFaceDescriptors()
            .withAgeAndGender();

        if (detections.length === 0) {
            updateStatus('error', '❌ Tidak ada wajah terdeteksi. Pastikan wajah terlihat jelas dan pencahayaan cukup.');
            document.getElementById('enrollBtn').disabled = true;
            return false;
        }
        if (detections.length > 1) {
            updateStatus('error', '⚠️ Terdeteksi lebih dari 1 wajah. Pastikan hanya Anda yang di foto.');
            document.getElementById('enrollBtn').disabled = true;
            return false;
        }
        const embedding = Array.from(detections[0].descriptor);
        const detected = detections[0];
        document.getElementById('embedding').value = JSON.stringify(embedding);
        document.getElementById('detected_gender').value = (detected.gender || '').toLowerCase();
        document.getElementById('foto_enroll').value = compressImage(canvas, 640, 480, 0.75);
        updateStatus('success', '✅ Wajah terverifikasi! Klik "Simpan Registrasi".');
        document.getElementById('enrollBtn').disabled = false;
        return true;
    } catch (e) {
        updateStatus('error', '❌ Gagal: ' + (e.message || 'Coba foto ulang'));
        document.getElementById('enrollBtn').disabled = true;
        return false;
    }
}

document.getElementById('fileInput').addEventListener('change', async function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = async function(ev) {
        const img = new Image();
        img.onload = async function() {
            const canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;
            canvas.getContext('2d').drawImage(img, 0, 0);
            document.getElementById('foto_enroll').value = compressImage(canvas, 640, 480, 0.75);
            document.getElementById('preview_enroll').src = document.getElementById('foto_enroll').value;
            document.getElementById('preview_enroll').style.display = 'block';
            await processImage(canvas);
        };
        img.src = ev.target.result;
    };
    reader.readAsDataURL(file);
});

const placeholder = document.getElementById('camera_placeholder');
const cameraArea = document.getElementById('camera_area');
const video = document.getElementById('video_enroll');
const cameraSelect = document.getElementById('camera_select');
const captureBtn = document.getElementById('capture_enroll');
const startBtn = document.getElementById('btn_start_camera');

startBtn.addEventListener('click', async function() {
    try {
        updateStatus('detecting', 'Memuat daftar kamera...');
        const devices = await navigator.mediaDevices.enumerateDevices();
        const videoDevices = devices.filter(d => d.kind === 'videoinput');
        if (videoDevices.length === 0) { updateStatus('error', 'Tidak ada kamera ditemukan.'); return; }
        cameraSelect.innerHTML = '';
        videoDevices.forEach((d, i) => {
            const opt = document.createElement('option');
            opt.value = d.deviceId;
            opt.textContent = d.label || 'Kamera ' + (i + 1);
            cameraSelect.appendChild(opt);
        });
        placeholder.style.display = 'none';
        cameraArea.style.display = 'block';
        startBtn.disabled = true;
        const startCam = async (deviceId) => {
            if (currentStream) currentStream.getTracks().forEach(t => t.stop());
            currentStream = await navigator.mediaDevices.getUserMedia({
                video: deviceId ? { deviceId: { exact: deviceId }, width: { ideal: 640 }, height: { ideal: 480 } } : { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
                audio: false
            });
            video.srcObject = currentStream;
            await video.play();
        };
        await startCam(videoDevices[0].deviceId);
        updateStatus('success', '✅ Kamera aktif. Posisikan wajah di tengah lalu klik "Ambil Foto".');
        cameraSelect.onchange = async () => {
            updateStatus('detecting', 'Mengganti kamera...');
            await startCam(cameraSelect.value);
            updateStatus('success', '✅ Klik "Ambil Foto".');
        };
    } catch (e) {
        updateStatus('error', '❌ ' + e.message);
        placeholder.style.display = 'block';
        cameraArea.style.display = 'none';
        startBtn.disabled = false;
    }
});

captureBtn.addEventListener('click', async function() {
    try {
        updateStatus('detecting', 'Menangkap foto...');
        const canvas = document.getElementById('canvas_enroll');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
        document.getElementById('foto_enroll').value = compressImage(canvas, 640, 480, 0.75);
        if (currentStream) { currentStream.getTracks().forEach(t => t.stop()); currentStream = null; }
        video.srcObject = null;
        cameraArea.style.display = 'none';
        placeholder.style.display = 'block';
        startBtn.disabled = false;
        document.getElementById('preview_enroll').src = dataUrl;
        document.getElementById('preview_enroll').style.display = 'block';
        await processImage(canvas);
    } catch (e) {
        updateStatus('error', '❌ ' + e.message);
    }
});

document.getElementById('resetBtn').addEventListener('click', function() {
    document.getElementById('foto_enroll').value = '';
    document.getElementById('embedding').value = '';
    document.getElementById('fileInput').value = '';
    document.getElementById('preview_enroll').style.display = 'none';
    updateStatus('detecting', 'Status verifikasi akan muncul di sini.');
    document.getElementById('enrollBtn').disabled = true;
    if (currentStream) { currentStream.getTracks().forEach(t => t.stop()); currentStream = null; }
    cameraArea.style.display = 'none';
    placeholder.style.display = 'block';
    startBtn.disabled = false;
});

document.addEventListener('DOMContentLoaded', function() {
    updateStatus('detecting', 'Memuat model di background...');
    loadModels();
});
</script>
@endsection
