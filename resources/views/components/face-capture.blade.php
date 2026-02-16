@props([
    'modalId' => 'faceModal',
    'type' => 'masuk',
    'label' => 'Masuk',
])

<!-- Face Capture Modal Component -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $modalId }}Label">
                    Verifikasi Wajah - Absen {{ $label }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form{{ ucfirst($type) }}" method="POST" action="{{ route("pegawai.ambilAbsen$type") }}">
                    @csrf

                    <!-- Upload atau Capture -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Ambil Foto</div>
                                <div class="card-body">
                                    <video id="video_{{ $type }}" width="100%" style="border: 1px solid #ddd; margin-bottom: 10px;"></video>
                                    <button type="button" class="btn btn-primary btn-block" id="capture_{{ $type }}">
                                        <i class="fas fa-camera"></i> Ambil Foto
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Upload Foto</div>
                                <div class="card-body">
                                    <input type="file" id="fileInput_{{ $type }}" accept="image/*" class="form-control">
                                    <small class="text-muted">atau pilih dari galeri</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Preview & Face Detection Status -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <canvas id="canvas_{{ $type }}" style="border: 1px solid #ddd; width: 100%; margin-bottom: 10px; display:none;"></canvas>
                            <img id="preview_{{ $type }}" style="border: 1px solid #ddd; width: 100%; margin-bottom: 10px; display:none;">
                        </div>
                        <div class="col-md-6">
                            <div id="face_status_{{ $type }}" style="padding: 15px; border: 1px solid #e3e6f0; border-radius: 5px; min-height: 100px;">
                                <p style="color: #999; text-align: center;">Face status akan muncul di sini</p>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Fields for Backend -->
                    <input type="hidden" name="foto_selfie_{{ $type }}_data" id="foto_selfie_{{ $type }}_data">
                    <input type="hidden" name="embedding" id="embedding_{{ $type }}">
                    @if($type !== 'lembur')
                        <input type="hidden" name="latitude" id="latitude_{{ $type }}">
                        <input type="hidden" name="longitude" id="longitude_{{ $type }}">
                    @endif

                    <!-- Error Alert -->
                    <div id="error_{{ $type }}" class="alert alert-danger mt-3" style="display:none;"></div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="submit_{{ $type }}">
                    Kirim Absen
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    const type = '{{ $type }}';
    const modalId = '{{ $modalId }}';

    // Face-API Setup
    const MODEL_URL = 'https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/weights/';
    let faceModelsLoaded = false;

    // Load Face-API models once
    async function loadFaceModels() {
        if (faceModelsLoaded) return;
        try {
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
            ]);
            faceModelsLoaded = true;
        } catch (error) {
            console.error('Failed to load Face-API models:', error);
            updateFaceStatus('error', 'Gagal load model face-api.js');
        }
    }

    // Extract embedding from canvas
    async function extractEmbedding(canvas) {
        if (!faceModelsLoaded) {
            updateFaceStatus('detecting', 'Loading face detection models...');
            await loadFaceModels();
        }

        try {
            updateFaceStatus('detecting', 'Mendeteksi wajah...');

            const detections = await faceapi.detectAllFaces(canvas, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptors();

            if (detections.length === 0) {
                updateFaceStatus('error', '❌ Tidak ada wajah terdeteksi. Pastikan wajah terlihat jelas dan pencahayaan cukup.');
                return null;
            }

            if (detections.length > 1) {
                updateFaceStatus('error', '⚠️ Terdeteksi lebih dari 1 wajah. Pastikan hanya Anda yang di foto.');
                return null;
            }

            const descriptor = detections[0].descriptor;
            const embedding = Array.from(descriptor);
            document.getElementById('embedding_' + type).value = JSON.stringify(embedding);
            updateFaceStatus('success', '✅ Wajah terverifikasi! Siap dikirim.');
            return embedding;
        } catch (error) {
            updateFaceStatus('error', '❌ Error: ' + error.message);
            return null;
        }
    }

    // Update UI status
    function updateFaceStatus(status, message) {
        const elem = document.getElementById('face_status_' + type);
        elem.innerHTML = `<p>${message}</p>`;

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

    // Camera setup
    const video = document.getElementById('video_' + type);
    const canvas = document.getElementById('canvas_' + type);
    const captureBtn = document.getElementById('capture_' + type);

    captureBtn.onclick = async function() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user' }
            });
            video.srcObject = stream;
            video.onloadedmetadata = () => {
                video.play();
                setTimeout(() => {
                    const ctx = canvas.getContext('2d');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    ctx.drawImage(video, 0, 0);
                    canvas.style.display = 'block';

                    // Stop camera
                    stream.getTracks().forEach(track => track.stop());

                    // Save as data URL
                    const dataUrl = canvas.toDataURL('image/jpeg');
                    document.getElementById('foto_selfie_' + type + '_data').value = dataUrl;

                    // Extract embedding
                    extractEmbedding(canvas);
                }, 500);
            };
        } catch (error) {
            updateFaceStatus('error', 'Tidak bisa akses kamera: ' + error.message);
        }
    };

    // File upload handler
    const fileInput = document.getElementById('fileInput_' + type);
    fileInput.onchange = function(e) {
        const file = e.target.files[0];
        const reader = new FileReader();
        reader.onload = async function(evt) {
            const img = new Image();
            img.onload = async function() {
                canvas.width = img.width;
                canvas.height = img.height;
                canvas.getContext('2d').drawImage(img, 0, 0);
                canvas.style.display = 'block';

                document.getElementById('foto_selfie_' + type + '_data').value = evt.target.result;
                await extractEmbedding(canvas);
            };
            img.src = evt.target.result;
        };
        reader.readAsDataURL(file);
    };

    // Submit handler
    document.getElementById('submit_' + type).onclick = async function() {
        const embedding = document.getElementById('embedding_' + type).value;
        const foto = document.getElementById('foto_selfie_' + type + '_data').value;

        if (!foto) {
            showError('Silakan ambil atau upload foto terlebih dahulu!');
            return;
        }

        if (!embedding) {
            showError('Verifikasi wajah gagal atau belum dilakukan!');
            return;
        }

        // Get location for non-lembur
        @if($type !== 'lembur')
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                document.getElementById('latitude_' + type).value = pos.coords.latitude;
                document.getElementById('longitude_' + type).value = pos.coords.longitude;
                submitForm();
            }, function() {
                showError('Tidak bisa mengakses lokasi. Pastikan izin lokasi sudah diberikan!');
            });
        } else {
            showError('Geolocation tidak didukung browser Anda!');
        }
        @else
        submitForm();
        @endif
    };

    function submitForm() {
        const form = document.getElementById('form' + capitalizeFirstLetter(type));
        form.submit();
    }

    function showError(msg) {
        document.getElementById('error_' + type).innerHTML = msg;
        document.getElementById('error_' + type).style.display = 'block';
    }

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    // Load face models on modal open
    $('#' + modalId).on('show.bs.modal', function() {
        if (!faceModelsLoaded) {
            loadFaceModels();
        }
    });
});
</script>
