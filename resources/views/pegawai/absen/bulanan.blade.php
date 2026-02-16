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
                <h3 class="mb-0"><b>DATA ABSENSI PEGAWAI</b></h3>
                @if ($detail_pegawai && $thn && $bln)
                <div class="mt-0">
                    <h5 class="mt-0"><b>{{ $detail_pegawai->nama_pegawai }} - {{ $detail_pegawai->namjab }}</b>
                    </h5>
                    <h6 class="mb-0">
                        <b>({{ ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$bln] }}
                            - {{ $thn }})</b>
                    </h6>
                </div>
                @endif
                <hr>
            </div>

            <form action="{{ route('pegawai.absen-bulanan') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-lg-3">
                        <select name="th" id="th" class="form-control">
                            <option value="">- PILIH TAHUN -</option>
                            @foreach ($listTahun as $tahun)
                            <option value="{{ $tahun }}" {{ $thn == $tahun ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <select name="bln" id="bln" class="form-control">
                            <option value="">- PILIH BULAN -</option>
                            @php
                            $bulan = [
                            'Januari',
                            'Februari',
                            'Maret',
                            'April',
                            'Mei',
                            'Juni',
                            'Juli',
                            'Agustus',
                            'September',
                            'Oktober',
                            'November',
                            'Desember',
                            ];
                            @endphp
                            @foreach ($bulan as $index => $namaBulan)
                            <option value="{{ $index + 1 }}" {{ $bln == $index + 1 ? 'selected' : '' }}>
                                {{ $namaBulan }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <button type="submit" class="btn btn-primary mb-3">
                            <i class="fa fa-search"></i>{{ $thn && $bln ? ' Refresh' : ' Cari' }}
                        </button>
                    </div>
                </div>
            </form>

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
                            <td>{{ $b->tanggal }}</td>
                            <td>{{ $b->waktu }}</td>
                            <td class="text-center">
                                @if ($b->keterangan == 0)
                                <span class="badge badge-success">Alfa</span>
                                @elseif($b->keterangan == 1)
                                <span class="badge badge-success">Masuk</span>
                                @elseif($b->keterangan == 2)
                                <span class="badge badge-success">Pulang</span>
                                @elseif($b->keterangan == 3)
                                <span class="badge badge-success">Lembur</span>
                                @elseif($b->keterangan == 4)
                                <span class="badge badge-success">Izin Sakit</span>
                                @elseif($b->keterangan == 5)
                                <span class="badge badge-success">Izin Tidak Masuk</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($b->id_lembur == null)
                                <span class="badge badge-danger">Tidak</span>
                                @else
                                <span class="badge badge-success">Iya</span>
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
                                <a href="{{ route('pegawai.detail-absen', $b->id_presents) }}" class="ml-2">
                                    <button type="button" class="btn btn-theme">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data absensi bulanan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection