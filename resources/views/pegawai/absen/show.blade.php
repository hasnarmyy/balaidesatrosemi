@extends('layouts.pegawai.main')

@section('content')
<div class="mt-4 mb-4 p-3 bg-white border shadow-sm lh-sm">
    <section class="content">
        <table class="table table-bordered table-hover table-striped table-sm mb-0">

            {{-- NAMA --}}
            <tr>
                <th width="200">Nama</th>
                <td>{{ $detail_absensi->nama_pegawai }}</td>
            </tr>

            {{-- JABATAN --}}
            <tr>
                <th>Jabatan</th>
                <td>{{ $detail_absensi->namjab }}</td>
            </tr>

            {{-- TANGGAL --}}
            <tr>
                <th>Tanggal</th>
                <td>{{ $detail_absensi->tanggal }}</td>
            </tr>

            {{-- JENIS ABSEN --}}
            <tr>
                <th>Jenis Absen</th>
                <td>
                    @switch($detail_absensi->keterangan)
                    @case(1) <span class="badge badge-success">Absen Masuk</span> @break
                    @case(2) <span class="badge badge-primary">Absen Pulang</span> @break
                    @case(3) <span class="badge badge-warning">Absen Lembur</span> @break
                    @case(4) <span class="badge badge-info">Izin Sakit</span> @break
                    @case(5) <span class="badge badge-secondary">Izin Tidak Masuk</span> @break
                    @default <span class="badge badge-danger">Alfa</span>
                    @endswitch
                </td>
            </tr>

            {{-- ===== ABSEN MASUK ===== --}}
            @if ($detail_absensi->keterangan == 1)
            <tr>
                <th>Jam Masuk</th>
                <td>
                    {{ $detail_absensi->jam_masuk }}
                    @if ($detail_absensi->keterangan_msk == 1)
                    <span class="badge badge-danger ml-2">Telat</span>
                    @else
                    <span class="badge badge-success ml-2">Tepat Waktu</span>
                    @endif
                </td>
            </tr>

            <tr>
                <th>Foto Absen Masuk</th>
                <td>
                    <a href="{{ asset('storage/'.$detail_absensi->foto_selfie_masuk) }}" target="_blank">
                        <img src="{{ asset('storage/'.$detail_absensi->foto_selfie_masuk) }}"
                            class="card-img" style="width:136px;height:140px;">
                    </a>
                </td>
            </tr>
            @endif

            {{-- ===== ABSEN PULANG ===== --}}
            @if ($detail_absensi->keterangan == 2)
            <tr>
                <th>Jam Pulang</th>
                <td>{{ $detail_absensi->jam_pulang }}</td>
            </tr>

            <tr>
                <th>Foto Absen Pulang</th>
                <td>
                    <a href="{{ asset('storage/'.$detail_absensi->foto_selfie_pulang) }}" target="_blank">
                        <img src="{{ asset('storage/'.$detail_absensi->foto_selfie_pulang) }}"
                            class="card-img" style="width:136px;height:140px;">
                    </a>
                </td>
            </tr>
            @endif

            {{-- ===== ABSEN LEMBUR ===== --}}
            @if ($detail_absensi->keterangan == 3)
            <tr>
                <th>Jam Lembur</th>
                <td>{{ $detail_absensi->jam_pulang }}</td>
            </tr>

            <tr>
                <th>Foto Absen Lembur</th>
                <td>
                    <a href="{{ asset('storage/'.$detail_absensi->foto_selfie_pulang) }}" target="_blank">
                        <img src="{{ asset('storage/'.$detail_absensi->foto_selfie_pulang) }}"
                            class="card-img" style="width:136px;height:140px;">
                    </a>
                </td>
            </tr>
            @endif

            {{-- ===== IZIN / SAKIT ===== --}}
            @if (in_array($detail_absensi->keterangan, [4,5]))
            <tr>
                <th>Keterangan</th>
                <td>{{ $detail_absensi->keterangan_izin }}</td>
            </tr>
            @endif

        </table>
    </section>
</div>
@endsection