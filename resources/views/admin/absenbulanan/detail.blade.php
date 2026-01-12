@extends('layouts.admin.main')

@section('title', $title)

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
    #map {
        height: 600px;
        width: 100%;
    }
</style>
@endsection

@section('content')
@if (session('flash'))
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <p><strong><i class="fa fa-info"></i> {{ session('flash') }}</strong></p>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="mt-4 mb-4 p-3 bg-white border shadow-sm lh-sm">
    <div class="row">
        <div class="col-md-12">
            <div class="row border-bottom mb-4">
                <div class="col-sm-8 pt-2">
                    <h6 class="mb-4 bc-header">{{ $title }}</h6>
                </div>
            </div>

            <div class="text-center mb-3">
                <h3 class="mb-0"><b>DATA ABSENSI PEGAWAI</b></h3>
                <h6 class="mt-0">
                    @if (!empty($detail_pegawai['nama_pegawai']))
                    <div class="mt-0">
                        <h5 class="mt-0"><b>{{ $detail_pegawai['nama_pegawai'] }} -
                                {{ $detail_pegawai->relasiJabatan->jabatan }}</b></h5>
                        <h6 class="mb-0"><b>({{ nmbulan($blnselected) }} - {{ $thnselected }})</b></h6>
                    </div>
                    @endif
                </h6>
                <hr>
            </div>

            <form action="{{ route('admin.absen-bulanan') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-lg-3">
                        <select name="id_peg" id="id_peg" class="form-control">
                            <option value="">- PILIH PEGAWAI -</option>
                            @foreach ($pegawai as $t)
                            <option value="{{ $t['id_pegawai'] }}"
                                {{ old('id_peg', request('id_peg')) == $t['id_pegawai'] || (!empty($detail_pegawai['id_pegawai']) && $detail_pegawai['id_pegawai'] == $t['id_pegawai']) ? 'selected' : '' }}>
                                {{ $t['nama_pegawai'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <select name="th" id="th" class="form-control">
                            <option value="">- PILIH TAHUN -</option>
                            @foreach ($list_th as $year)
                            <option value="{{ $year }}"
                                {{ $thn == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <select name="bln" id="bln" class="form-control">
                            <option value="">- PILIH BULAN -</option>
                            @foreach ($list_bln as $month)
                            <option value="{{ $month }}"
                                {{ $blnnya == $month ? 'selected' : '' }}>
                                {{ nmbulan($month) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3">
                        @if ($has_filter)
                        <button type="submit" class="btn btn-primary mb-3">
                            <i class="fa fa-search"></i> Refresh
                        </button>
                        @else
                        <button type="submit" class="btn btn-primary mb-3">
                            <i class="fa fa-search"></i> Cari
                        </button>
                        @endif

                        @if ($has_filter && !empty($detail_pegawai['id_pegawai']))
                        <a target="_blank"
                            href="{{ route('admin.cetak.absen.bulanan', [
                                        'thn' => $thn,
                                        'bln' => $blnnya,
                                        'idpeg' => $detail_pegawai['id_pegawai'],
                                    ]) }}"
                            class="btn btn-danger mb-3">
                            <i class="fa fa-print"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>

            @if ($has_filter)
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>NO.</th>
                            <th>TANGGAL</th>
                            <th>WAKTU</th>
                            <th>JENIS ABSEN</th>
                            <th>LEMBUR</th>
                            <th>KETERANGAN</th>
                            <th>DETAIL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absen as $index => $b)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $b['tanggal'] ?? '' }}</td>
                            <td>{{ $b['waktu'] ?? '' }}</td>
                            <td class="text-center">
                                @switch($b['keterangan'] ?? null)
                                @case(0)
                                <span class="badge badge-success">Alfa</span>
                                @break

                                @case(1)
                                <span>Masuk</span>
                                @break

                                @case(2)
                                <span>Pulang</span>
                                @break

                                @case(3)
                                <span>Lembur</span>
                                @break

                                @case(4)
                                <span>Izin Sakit</span>
                                @break

                                @case(5)
                                <span>Izin Tidak Masuk</span>
                                @break

                                @default
                                <span>Tidak Diketahui</span>
                                @endswitch
                            </td>
                            <td class="text-center">
                                @if (empty($b['id_lembur']))
                                <span>Tidak</span>
                                @else
                                <span>Iya</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @switch($b->keterangan)
                                @case(1)
                                <span>Masuk</span>
                                @break

                                @case(2)
                                <span>Pulang</span>
                                @break

                                @case(3)
                                <span>Lembur</span>
                                @break

                                @case(4)
                                @case(5)
                                <span>{{ $b->keterangan_izin }}</span>
                                @break

                                @default
                                <span>-</span>
                                @endswitch
                            </td>
                            <td>
                                @if (!empty($b['id_presents']))
                                <a href="{{ route('admin.detail.absen', $b['id_presents']) }}"
                                    class="ml-2">
                                    <button type="button" class="btn btn-theme">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data absen untuk periode yang dipilih</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-info mt-3">
                <i class="fa fa-info-circle"></i> Silakan pilih pegawai, tahun, dan bulan untuk melihat data
                absensi.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@php
function nmbulan($angka)
{
$bulan = [
1 => 'Januari',
2 => 'Februari',
3 => 'Maret',
4 => 'April',
5 => 'Mei',
6 => 'Juni',
7 => 'Juli',
8 => 'Agustus',
9 => 'September',
10 => 'Oktober',
11 => 'November',
12 => 'Desember',
];

return $bulan[$angka] ?? 'Bulan tidak valid';
}
@endphp