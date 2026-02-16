@extends('layouts.pegawai.main')

@section('content')

@php
function rupiah($angka)
{
    return 'Rp ' . number_format($angka ?? 0, 0, ',', '.');
}

function nmbulan($angka)
{
$bulan = [
1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
4 => 'April', 5 => 'Mei', 6 => 'Juni',
7 => 'Juli', 8 => 'Agustus', 9 => 'September',
10 => 'Oktober', 11 => 'November', 12 => 'Desember',
];
return $bulan[$angka] ?? '';
}
@endphp

<div class="mt-4 mb-4 p-3 bg-white border shadow-sm lh-sm">
    <div class="row border-bottom mb-4">
        <div class="col-sm-8 pt-2">
            <h6 class="mb-4 bc-header">{{ $title }}</h6>
        </div>
        <div class="col-sm-4 text-right pb-3">
            <a target="_blank"
                href="{{ route('pegawai.laporan-tpp.cetak', [$pegawai->id_pegawai, $blnselected, $thnselected]) }}">
                <button class="btn btn-danger">
                    <i class="fa fa-print"></i>
                </button>
            </a>
        </div>
    </div>

    <div class="text-center mb-3">
        <h3><b>LAPORAN GAJI PEGAWAI</b></h3>
        <h5><b>BALAI DESA TROSEMI</b></h5>
        <hr>
    </div>

    <table class="table table-bordered table-striped table-sm">

        {{-- DATA PEGAWAI --}}
        <tr>
            <td colspan="11"><b><u>DATA PEGAWAI</u></b></td>
        </tr>
        <tr>
            <td colspan="2"><b>NO PEGAWAI</b></td>
            <td colspan="4"><b>{{ $pegawai->id_pegawai }}</b></td>
        </tr>
        <tr>
            <td colspan="2"><b>NAMA PEGAWAI</b></td>
            <td colspan="4"><b>{{ strtoupper($pegawai->nama_pegawai) }}</b></td>
        </tr>
        <tr>
            <td colspan="2"><b>PERIODE</b></td>
            <td colspan="4"><b>{{ nmbulan($blnselected) }} {{ $thnselected }}</b></td>
        </tr>
        <tr>
            <td colspan="2"><b>JABATAN</b></td>
            <td colspan="4"><b>{{ strtoupper($pegawai->relasiJabatan->jabatan) }}</b></td>
        </tr>

        {{-- PENERIMAAN --}}
        <tr>
            <td colspan="11"><b><u>PENERIMAAN</u></b></td>
        </tr>
        <tr>
            <th class="text-center">1</th>
            <th>Gaji Pokok</th>
            <th colspan="5">{{ rupiah($gaji['gaji_pokok']) }}</th>
        </tr>
        <tr>
            <th class="text-center">2</th>
            <th>Upah Lembur</th>
            <th colspan="5">{{ rupiah($gaji['gaji_lembur']) }}</th>
        </tr>
        <tr>
            <th class="text-center">3</th>
            <th>Bonus</th>
            <th colspan="5">{{ rupiah($gaji['bonus']) }}</th>
        </tr>
        <tr>
            <th class="text-center">4</th>
            <th>Potongan</th>
            <th colspan="5">{{ rupiah($gaji['potongan_absen'] ?? 0) }}</th>
        </tr>

        <tr>
            <td colspan="10" class="text-center">
                <h5>
                    <b>Total Gaji Bersih :
                        {{ rupiah($gaji['gaji_bersih']) }}
                    </b>
                </h5>
            </td>
        </tr>

        {{-- ABSENSI --}}
        <tr>
            <td colspan="4" class="text-center"><b>KETERANGAN</b></td>
            <td colspan="3" class="text-center"><b>ABSENSI</b></td>
        </tr>
        <tr>
            <td colspan="4" rowspan="4" class="text-center align-middle">
                <b>{{ strtoupper($gaji['keterangan'] ?? '-') }}</b>
            </td>
            <td colspan="3" class="text-center"><b>Masuk : {{ $absen['masuk'] }}</b></td>
        </tr>
        <tr>
            <td colspan="3" class="text-center"><b>Lembur : {{ $absen['jumlem'] }}</b></td>
        </tr>
        <tr>
            <td colspan="3" class="text-center"><b>Izin Sakit : {{ $absen['sakit'] }}</b></td>
        </tr>
        <tr>
            <td colspan="3" class="text-center"><b>Izin Tidak Masuk : {{ $absen['izin'] }}</b></td>
        </tr>

    </table>
</div>
@endsection