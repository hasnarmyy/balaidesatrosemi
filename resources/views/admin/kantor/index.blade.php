@extends('layouts.admin.main')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
@endpush

@section('content')

@if (session('flash'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <p><strong><i class="fa fa-check"></i> {{ session('flash') }}</strong></p>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <p><strong><i class="fa fa-exclamation"></i> {{ session('error') }}</strong></p>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="mt-4 mb-4 p-3 bg-white border shadow-sm lh-sm">
    <div class="row border-bottom mb-4">
        <div class="col-sm-8 pt-2">
            <h6 class="mb-4 bc-header">{{ $title }}</h6>
        </div>
        <div class="col-sm-4 text-right pb-3">
            <button class="btn btn-round btn-theme" data-toggle="modal" data-target="#modalTambahKantor">
                <i class="fa fa-plus"></i> Tambah Kantor
            </button>
        </div>
    </div>

    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fa fa-info-circle"></i> <strong>Info:</strong> Kantor yang aktif akan digunakan untuk validasi jarak saat pegawai melakukan absen.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>NO.</th>
                    <th>NAMA KANTOR</th>
                    <th>ALAMAT</th>
                    <th>KOORDINAT</th>
                    <th>RADIUS (m)</th>
                    <th>STATUS</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($kantor as $k)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>
                        <strong>{{ $k->name }}</strong>
                        @if($k->active)
                        <span class="badge badge-success">Aktif</span>
                        @endif
                    </td>
                    <td>{{ $k->address ?? '-' }}</td>
                    <td>
                        <small>{{ $k->latitude }}, {{ $k->longitude }}</small>
                    </td>
                    <td>{{ $k->radius_meters }}</td>
                    <td>
                        @if($k->active)
                        <span class="badge badge-success"><i class="fa fa-check"></i> Aktif</span>
                        @else
                        <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#confirmSetActive{{ $k->id_office }}">
                            Set Aktif
                        </button>
                        @endif
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalEditKantor{{ $k->id_office }}">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        @if(!$k->active)
                        <form action="{{ route('admin.kantor.hapus', $k->id_office) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fa fa-trash"></i> Hapus
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>

                <!-- Modal Edit Kantor -->
                <div class="modal fade" id="modalEditKantor{{ $k->id_office }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header text-center">
                                <h5 class="modal-title"><strong>Edit Kantor</strong></h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <form action="{{ route('admin.kantor.edit', $k->id_office) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Nama Kantor</label>
                                        <input type="text" name="name" class="form-control" value="{{ $k->name }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Alamat</label>
                                        <textarea name="address" class="form-control kantor-address" rows="2">{{ $k->address }}</textarea>
                                        <small class="text-muted">Bisa diedit setelah dipilih dari peta.</small>
                                    </div>
                                    <div class="form-group">
                                        <label>Lokasi (dari peta)</label>
                                        <div class="input-group">
                                            <input type="number" name="latitude" class="form-control kantor-lat" step="0.00000001" value="{{ $k->latitude }}" required readonly>
                                            <input type="number" name="longitude" class="form-control kantor-lng" step="0.00000001" value="{{ $k->longitude }}" required readonly>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-primary btn-pilih-peta" data-target-modal="modalEditKantor{{ $k->id_office }}" data-lat="{{ $k->latitude }}" data-lng="{{ $k->longitude }}" title="Pilih dari peta">
                                                    <i class="fa fa-map-marker-alt"></i> Pilih dari Peta
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted">Koordinat diisi dari peta (tidak bisa diedit manual).</small>
                                    </div>
                                    <div class="form-group">
                                        <label>Radius Maksimal (meter)</label>
                                        <input type="number" name="radius_meters" class="form-control" min="1" value="{{ $k->radius_meters }}" required>
                                        <small class="text-muted">Jarak maksimal dari kantor untuk absen. Contoh: 100 (100 meter)</small>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="active" {{ $k->active ? 'checked' : '' }}>
                                            Set sebagai kantor aktif
                                        </label>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Confirm Set Active -->
                <div class="modal fade" id="confirmSetActive{{ $k->id_office }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Konfirmasi</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                Jadikan "<strong>{{ $k->name }}</strong>" sebagai kantor aktif?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                <form action="{{ route('admin.kantor.setActive', $k->id_office) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">Ya, Aktifkan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Kantor -->
<div class="modal fade" id="modalTambahKantor" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h5 class="modal-title"><strong>Tambah Kantor</strong></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('admin.kantor.tambah') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kantor</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="address" class="form-control kantor-address" rows="2"></textarea>
                        <small class="text-muted">Bisa diedit setelah dipilih dari peta.</small>
                    </div>
                    <div class="form-group">
                        <label>Lokasi (dari peta)</label>
                        <div class="input-group">
                            <input type="number" name="latitude" class="form-control kantor-lat" step="0.00000001" placeholder="Lat" required readonly>
                            <input type="number" name="longitude" class="form-control kantor-lng" step="0.00000001" placeholder="Lng" required readonly>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-primary btn-pilih-peta" data-target-modal="modalTambahKantor" data-lat="" data-lng="" title="Pilih dari peta">
                                    <i class="fa fa-map-marker-alt"></i> Pilih dari Peta
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Koordinat diisi dari peta (tidak bisa diedit manual).</small>
                    </div>
                    <div class="form-group">
                        <label>Radius Maksimal (meter)</label>
                        <input type="number" name="radius_meters" class="form-control" min="1" value="100" required>
                        <small class="text-muted">Jarak maksimal dari kantor untuk absen. Contoh: 100 (100 meter)</small>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="active">
                            Set sebagai kantor aktif
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Pilih Lokasi dari Peta -->
<div class="modal fade" id="modalMapPicker" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-map-marker-alt"></i> Pilih Lokasi dari Peta</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label class="small font-weight-bold">Cari lokasi (nama jalan, kota, alamat)</label>
                    <div class="input-group">
                        <input type="text" id="map-picker-search" class="form-control" placeholder="Contoh: Jl. Sudirman Jakarta, Monas, Bali...">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" id="map-picker-search-btn">
                                <i class="fa fa-search"></i> Cari
                            </button>
                        </div>
                    </div>
                    <div id="map-picker-results" class="list-group mt-2" style="max-height: 180px; overflow-y: auto; display: none;"></div>
                </div>
                <p class="text-muted small">Atau klik pada peta untuk memilih lokasi. Koordinat dan alamat akan terisi otomatis.</p>
                <div id="map-picker-container" style="height: 400px; border: 1px solid #ddd; border-radius: 4px;"></div>
                <div class="mt-2 small text-muted" id="map-picker-status">Klik peta atau cari lokasi di atas.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
(function() {
    var mapPickerTargetModal = null;
    var map = null;
    var marker = null;
    var defaultCenter = [-7.5887862, 110.7505422];
    var defaultZoom = 14;

    function initMapPicker(center, zoom) {
        if (map) {
            map.setView(center || defaultCenter, zoom || defaultZoom);
            if (marker) {
                marker.setLatLng(center || defaultCenter);
            } else {
                marker = L.marker(center || defaultCenter).addTo(map);
            }
            return;
        }
        var el = document.getElementById('map-picker-container');
        if (!el) return;
        map = L.map('map-picker-container').setView(center || defaultCenter, zoom || defaultZoom);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        marker = L.marker(center || defaultCenter).addTo(map);
        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;
            marker.setLatLng([lat, lng]);
            reverseGeocode(lat, lng);
        });
    }

    function reverseGeocode(lat, lng) {
        var status = document.getElementById('map-picker-status');
        if (status) status.textContent = 'Mengambil alamat...';
        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=18&addressdetails=1', {
            headers: { 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var addr = data.display_name || (lat + ', ' + lng);
            if (status) status.textContent = 'Lokasi dipilih. Alamat: ' + addr;
            fillTargetForm(lat, lng, addr);
        })
        .catch(function() {
            if (status) status.textContent = 'Lokasi: ' + lat + ', ' + lng + ' (alamat tidak tersedia)';
            fillTargetForm(lat, lng, lat + ', ' + lng);
        });
    }

    function searchLocation(query) {
        var q = (query || document.getElementById('map-picker-search').value || '').trim();
        if (!q) return;
        var resultsEl = document.getElementById('map-picker-results');
        var statusEl = document.getElementById('map-picker-status');
        resultsEl.style.display = 'block';
        resultsEl.innerHTML = '<div class="list-group-item text-muted">Mencari...</div>';
        fetch('https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(q) + '&format=json&limit=6&countrycodes=id', {
            headers: { 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data || data.length === 0) {
                resultsEl.innerHTML = '<div class="list-group-item text-muted">Tidak ada hasil. Coba kata kunci lain.</div>';
                return;
            }
            resultsEl.innerHTML = '';
            data.forEach(function(item) {
                var lat = parseFloat(item.lat);
                var lng = parseFloat(item.lon);
                var name = item.display_name || (lat + ', ' + lng);
                var a = document.createElement('a');
                a.href = '#';
                a.className = 'list-group-item list-group-item-action';
                a.textContent = name;
                a.addEventListener('click', function(ev) {
                    ev.preventDefault();
                    resultsEl.style.display = 'none';
                    resultsEl.innerHTML = '';
                    document.getElementById('map-picker-search').value = name;
                    if (map) {
                        map.setView([lat, lng], 17);
                        if (marker) marker.setLatLng([lat, lng]);
                    }
                    if (statusEl) statusEl.textContent = 'Lokasi dipilih: ' + name;
                    fillTargetForm(lat, lng, name);
                });
                resultsEl.appendChild(a);
            });
        })
        .catch(function() {
            resultsEl.innerHTML = '<div class="list-group-item text-danger">Gagal mencari. Coba lagi.</div>';
        });
    }

    document.getElementById('map-picker-search-btn').addEventListener('click', function() {
        searchLocation();
    });
    document.getElementById('map-picker-search').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchLocation();
        }
    });

    function fillTargetForm(lat, lng, address) {
        if (!mapPickerTargetModal) return;
        var modal = document.getElementById(mapPickerTargetModal);
        if (!modal) return;
        var latInput = modal.querySelector('.kantor-lat');
        var lngInput = modal.querySelector('.kantor-lng');
        var addrInput = modal.querySelector('.kantor-address');
        if (latInput) latInput.value = lat;
        if (lngInput) lngInput.value = lng;
        if (addrInput && address) addrInput.value = address;
    }

    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.btn-pilih-peta');
        if (!btn) return;
        mapPickerTargetModal = btn.getAttribute('data-target-modal');
        var lat = parseFloat(btn.getAttribute('data-lat')) || defaultCenter[0];
        var lng = parseFloat(btn.getAttribute('data-lng')) || defaultCenter[1];
        if (isNaN(lat) || isNaN(lng)) { lat = defaultCenter[0]; lng = defaultCenter[1]; }
        initMapPicker([lat, lng], 16);
        $('#modalMapPicker').modal('show');
        setTimeout(function() {
            if (map) map.invalidateSize();
        }, 300);
    });

    $('#modalMapPicker').on('hidden.bs.modal', function() {
        mapPickerTargetModal = null;
    });
})();
</script>
@endpush
