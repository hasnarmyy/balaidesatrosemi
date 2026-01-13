@extends('layouts.pegawai.main')

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
                <h3 class="mb-0"><b>LAPORAN GAJI BULANAN</b></h3>
                @if ($gaji && is_array($gaji) && $thnselected && $blnselected)
                <div class="mt-0">
                    <h5 class="mt-0">
                        <b>{{ $gaji['nama_pegawai'] }} - {{ $gaji['jabatan'] }}</b>
                    </h5>
                    <h6 class="mb-0">
                        <b>({{ $bulanTersedia[$blnselected] ?? '' }} - {{ $thnselected }})</b>
                    </h6>
                </div>
                @endif
                <hr>
            </div>

            <form action="{{ route('pegawai.laporan-tpp-bulanan') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-lg-3">
                        <select name="th" id="th" class="form-control">
                            <option value="">- PILIH TAHUN -</option>
                            @foreach ($tahunTersedia as $tahun)
                            <option value="{{ $tahun }}" {{ $thnselected == $tahun ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <select name="bln" id="bln" class="form-control">
                            <option value="">- PILIH BULAN -</option>
                            @foreach ($bulanTersedia as $key => $bulan)
                            <option value="{{ $key }}" {{ $blnselected == $key ? 'selected' : '' }}>
                                {{ $bulan }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <button type="submit" class="btn btn-primary mb-3">
                            <i class="fa fa-search"></i>{{ $thnselected && $blnselected ? ' Refresh' : ' Cari' }}
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>NO.</th>
                            <th>NAMA PEGAWAI</th>
                            <th>JABATAN</th>
                            <th>GAJI POKOK</th>
                            <th>GAJI LEMBUR</th>
                            <th>BONUS</th>
                            <th>GAJI BERSIH</th>
                            <th>KETERANGAN</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($gaji && is_array($gaji))
                        <tr>
                            <td>1</td>
                            <td>{{ $gaji['nama_pegawai'] }}</td>
                            <td>{{ $gaji['jabatan'] }}</td>
                            <td>Rp {{ number_format($gaji['gaji_pokok'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($gaji['gaji_lembur'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($gaji['bonus'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($gaji['gaji_bersih'], 0, ',', '.') }}</td>
                            <td>{{ $gaji['keterangan'] }}</td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('pegawai.detail-laporan-tpp', [$gaji['id_pegawai'], $blnselected, $thnselected]) }}">
                                        <button type="button" class="btn btn-theme mr-2">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </a>
                                    <a target="_blank"
                                        href="{{ route('pegawai.laporan-tpp.cetak', [$gaji['id_pegawai'], $blnselected, $thnselected]) }}">
                                        <button type="button" class="btn btn-danger">
                                            <i class="fa fa-print"></i>
                                        </button>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @else
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data gaji bulanan</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection