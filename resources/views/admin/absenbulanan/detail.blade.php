@extends('layouts.admin.main')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

@section('title', $title)
@section('content')

<style>
    .map-box {
        height: 300px;
        width: 100%;
        margin-top: 10px;
        border-radius: 5px;
        border: 1px solid #ddd;
    }

    .table-detail th {
        width: 220px;
        background: #f8f9fa;
    }

    .card-img {
        width: 136px;
        height: 140px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
</style>

@php
$label = [
1 => 'Masuk',
2 => 'Pulang',
3 => 'Lembur',
4 => 'Sakit',
5 => 'Izin',
];
@endphp

<div class="container mt-4 mb-4 p-3 bg-white border shadow-sm">

    {{-- DATA --}}
    <table class="table table-bordered table-detail">
        <tr>
            <th>Nama</th>
            <td>{{ $detail_absensi->pegawai->nama_pegawai }}</td>
        </tr>
        <tr>
            <th>Jabatan</th>
            <td>{{ $detail_absensi->pegawai->relasiJabatan->jabatan }}</td>
        </tr>
        <tr>
            <th>Tanggal</th>
            <td>{{ $detail_absensi->tanggal }}</td>
        </tr>
        <tr>
            <th>Jenis Absen</th>
            <td>
                <span class="badge badge-success">
                    {{ $label[$detail_absensi->keterangan] }}
                </span>
            </td>
        </tr>

        {{-- JAM --}}
        @if($detail_absensi->keterangan == 1)
        <tr>
            <th>Jam Masuk</th>
            <td>{{ $detail_absensi->jam_masuk }}</td>
        </tr>
        @elseif($detail_absensi->keterangan == 2)
        <tr>
            <th>Jam Pulang</th>
            <td>{{ $detail_absensi->jam_pulang }}</td>
        </tr>
        @elseif($detail_absensi->keterangan == 3)
        <tr>
            <th>Jam Lembur</th>
            <td>{{ $detail_absensi->waktu }}</td>
        </tr>
        @endif

        {{-- FOTO --}}
        @if($detail_absensi->keterangan == 1 && $detail_absensi->foto_selfie_masuk)
        <tr>
            <th>Foto Masuk</th>
            <td>
                <img src="{{ asset('storage/'.$detail_absensi->foto_selfie_masuk) }}" class="card-img">
            </td>
        </tr>

        @elseif(in_array($detail_absensi->keterangan, [2,3]) && $detail_absensi->foto_selfie_pulang)
        <tr>
            <th>Foto {{ $label[$detail_absensi->keterangan] }}</th>
            <td>
                <img src="{{ asset('storage/'.$detail_absensi->foto_selfie_pulang) }}" class="card-img">
            </td>
        </tr>

        @elseif($detail_absensi->keterangan == 4 && $detail_absensi->foto_selfie_masuk)
        <tr>
            <th>Surat Keterangan Dokter</th>
            <td>
                <img src="{{ asset('storage/'.$detail_absensi->foto_selfie_masuk) }}" class="card-img">
            </td>
        </tr>
        @endif

        @if (in_array($detail_absensi['keterangan'], [4, 5]))
        <tr>
            <th>Keterangan</th>
            <td>{{ $detail_absensi['keterangan_izin'] }}</td>
        </tr>
        @endif
    </table>

    @if(in_array($detail_absensi->keterangan, [1,2,3]))
    <div class="mt-4 p-3 bg-white border shadow-sm lh-sm">

        <!-- Koordinat -->
        <div id="coords" style="margin-bottom:10px; font-weight:600;">
            Latitude: {{ $detail_absensi->latitude ?? '-' }},
            Longitude: {{ $detail_absensi->longitude ?? '-' }}
        </div>

        <!-- Map -->
        <div id="map" class="map-box"
            data-lat="{{ $detail_absensi->latitude }}"
            data-lng="{{ $detail_absensi->longitude }}"
            data-label="{{ $label[$detail_absensi->keterangan] }}">
        </div>
    </div>
    @endif
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mapEl = document.getElementById('map');
        if (!mapEl) return;

        const lat = parseFloat(mapEl.dataset.lat);
        const lng = parseFloat(mapEl.dataset.lng);
        const label = mapEl.dataset.label;

        const map = L.map('map').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        L.marker([lat, lng]).addTo(map).bindPopup(label).openPopup();
    });
</script>

@endsection