@extends('layouts.admin.main')

@section('title', $title)

@php
function rupiah($angka)
{
    return 'Rp ' . number_format($angka ?? 0, 0, ',', '.');
}

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
return $bulan[(int) $angka] ?? '';
}
@endphp

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

    <div class="row border-bottom mb-4">
        <div class="col-sm-8 pt-2">
            <h6 class="mb-4 bc-header">{{ $title }}</h6>
        </div>
        <div class="col-sm-4 text-right pb-3">
            <a target="_blank"
                href="{{ route('admin.cetak.payrol.pegawai', [$id_pegawai, $blnselected, $thnselected]) }}"
                class="ml-0">
                <button type="button" class="btn btn-danger">
                    <i class="fa fa-print"></i>
                </button>
            </a>
        </div>
    </div>

    <div class="text-center mb-3">
        <h3 class="mb-0"><b>LAPORAN GAJI PEGAWAI</b></h3>
        <h5 class="mb-0"><b>BALAI DESA TROSEMI</b></h5>
        <hr>
    </div>

    <table class="table table-bordered table-striped table-sm mb-0">

        {{-- DATA PEGAWAI --}}
        <tr height="40px">
            <td colspan="11">
                <h6><b><u>DATA PEGAWAI :</u></b></h6>
            </td>
        </tr>

        <tr>
            <td colspan="2"><b>NO PEGAWAI</b></td>
            <td colspan="4"><b>{{ strtoupper($pegawai->id_pegawai) }}</b></td>
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
        <tr height="40px">
            <td colspan="11">
                <h6><b><u>PENERIMAAN :</u></b></h6>
            </td>
        </tr>

        <tr>
            <th class="text-center" width="44">1</th>
            <th width="300">Gaji Pokok</th>
            <th colspan="5">{{ rupiah($gaji->gaji_pokok) }}</th>
        </tr>

        <tr>
            <th class="text-center">2</th>
            <th>Upah Lembur</th>
            <th colspan="5">{{ rupiah($gaji->gaji_lembur) }}</th>
        </tr>

        <tr>
            <th class="text-center">3</th>
            <th>Bonus</th>
            <th colspan="5">{{ rupiah($gaji->bonus) }}</th>
        </tr>

        <tr>
            <th class="text-center">4</th>
            <th>Potongan Terlambat</th>
            <th colspan="5">{{ rupiah($gaji->potongan_absen) }}</th>
        </tr>

        {{-- TOTAL --}}
        <tr height="50px">
            <td colspan="10" class="text-center">
                <h5>
                    <b>Total Gaji Bersih Anda:
                        {{ rupiah($gaji->gaji_bersih) }}
                    </b>
                </h5>
            </td>
        </tr>

        {{-- ABSENSI --}}
        <tr>
            <td colspan="11"></td>
        </tr>

        <tr>
            <td colspan="4" class="text-center"><b>KETERANGAN</b></td>
            <td colspan="3" class="text-center"><b>ABSENSI / KEHADIRAN</b></td>
        </tr>

        <tr>
            <td colspan="4" rowspan="4" class="text-center align-middle">
                <b>{{ strtoupper($gaji->keterangan) }}</b>
            </td>

            <td colspan="3" class="text-center"><b>Masuk: {{ $absen['masuk'] }}</b></td>
        </tr>

        <tr>
            <td colspan="3" class="text-center"><b>Lembur: {{ $absen['jumlem'] }}</b></td>
        </tr>

        <tr>
            <td colspan="3" class="text-center"><b>Izin Sakit: {{ $absen['sakit'] }}</b></td>
        </tr>

        <tr>
            <td colspan="3" class="text-center"><b>Izin Tidak Masuk: {{ $absen['izin'] }}</b></td>
        </tr>

    </table>

</div>
@endsection