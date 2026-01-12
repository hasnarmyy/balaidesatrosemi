@extends('layouts.admin.main')

@section('title', $title)

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
                <h3 class="mb-0"><b>DATA SLIP GAJI PEGAWAI</b></h3>
                <hr>
            </div>

            <form action="{{ route('admin.laporan-tpp-bulanan') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-lg-3">
                        <select name="th" id="th" class="form-control">
                            <option value="">- PILIH TAHUN -</option>
                            @foreach ($list_th as $year)
                            <option value="{{ $year }}" {{ $thn == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <select name="bln" id="bln" class="form-control">
                            <option value="">- PILIH BULAN -</option>
                            @foreach ($list_bln as $month)
                            <option value="{{ $month }}" {{ $blnnya == $month ? 'selected' : '' }}>
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
                    </div>
                </div>
            </form>

            @if ($has_filter)
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>NO.</th>
                            <th>NAMA PEGAWAI</th>
                            <th>JABATAN</th>
                            <th>GAJI POKOK</th>
                            <th>LEMBUR</th>
                            <th>BONUS</th>
                            <th>KETERANGAN</th>
                            <th>POTONGAN TERLAMBAT</th>
                            <th>GAJI BERSIH</th>
                            <th width=60>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($gaji as $index => $b)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $b->pegawai->nama_pegawai }}</td>
                            <td>{{ $b->pegawai->relasiJabatan->jabatan }}</td>
                            <td>Rp {{ number_format($b->details->sum('gaji_pokok'), 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($b->details->sum('gaji_lembur'), 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($b->details->sum('bonus'), 0, ',', '.') }}</td>
                            <td>{{ $b->keterangan }}</td>
                            <td>Rp {{ number_format($b->details->sum('potongan_absen'), 0, ',', '.') }}</td>
                            <td>Rp {{ number_format(
                                $b->details->sum('gaji_pokok')
                                + $b->details->sum('gaji_lembur')
                                + $b->details->sum('bonus')
                                - $b->details->sum('potongan_absen'),
                                0, ',', '.'
                            ) }}</td>
                            <td width="20px">
                                <div class="row">
                                    <a href="{{ route('admin.detail.laporan.tpp', [$b['id_pegawai'], $blnselected, $thnselected]) }}"
                                        class="ml-3 mb-0">
                                        <button type="button" class="btn btn-theme">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </a>
                                    <hr>
                                    <a target="_blank"
                                        href="{{ route('admin.cetak.payrol.pegawai', [$b['id_pegawai'], $blnnya, $thn]) }}"
                                        class="ml-0">
                                        <button type="button" class="btn btn-danger">
                                            <i class="fa fa-print"></i>
                                        </button>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">Tidak ada data gaji untuk periode yang dipilih</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-info mt-3">
                <i class="fa fa-info-circle"></i> Silakan pilih tahun dan bulan untuk melihat data slip gaji.
            </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title text-secondary"><strong>GAJI PEGAWAI</strong></h5>
                    <button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-justify">
                    <form class="form-horizontal" action="{{ route('admin.gaji.store') }}" method="post">
                        @csrf
                        <input type="hidden" name="bulanpilih" value="{{ $blnselected }}">
                        <input type="hidden" name="tahunpilih" value="{{ $thnselected }}">
                        <div class="modal-body">
                            <div class="form-group col-sm-12">
                                <div class="flash"></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-12">Nama Pegawai</label>
                                <div class="col-sm-12">
                                    <select class="form-control" id="id_pegawai_c" name="id_pegawai">
                                        <option value="">-pilih-</option>
                                        @foreach ($pegawai as $j)
                                        <option value="{{ $j['id_pegawai'] }}"> {{ $j['nama_pegawai'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row col-lg-12">
                                <div class="form-group col-lg-5">
                                    <label class="">Tahun</label>
                                    <div class="">
                                        <select name="th1" id="thn_c" class="form-control">
                                            <option value="">- PILIH TAHUN -</option>
                                            @foreach ($list_th as $year)
                                            <option value="{{ $year }}" {{ $thn == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-lg-5">
                                    <label class="">Bulan</label>
                                    <div class="">
                                        <select name="bln1" id="bln_c" class="form-control">
                                            <option value="">- PILIH BULAN -</option>
                                            @foreach ($list_bln as $month)
                                            <option value="{{ $month }}" {{ $blnnya == $month ? 'selected' : '' }}>
                                                {{ nmbulan($month) }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-lg-2">
                                    <label class="">_</label>
                                    <div class="">
                                        <button type="button" onclick="cari()" class="btn btn-success">Akumulasi</button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-lg-12" name="lain_nya" id="lain_nya" hidden>
                                <div class="row col-lg-12">
                                    <div class="form-group col-lg-6">
                                        <label class="">Gaji Pokok</label>
                                        <div class="">
                                            <input type="text" name="gapok1" id="gapok1" class="form-control" readonly required>
                                            <input type="hidden" name="gapok" id="gapok" class="form-control" readonly required>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label class="">Lembur</label>
                                        <div class="">
                                            <input type="text" name="lembur1" id="lembur1" class="form-control" value="" readonly required>
                                            <input type="hidden" name="lembur" id="lembur" class="form-control" value="" readonly required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row col-lg-12">
                                    <div class="form-group col-lg-6">
                                        <label class="">Bonus</label>
                                        <div class="">
                                            <input type="text" name="bonus" class="form-control" value="">
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label class="">Keterangan</label>
                                        <div class="">
                                            <input type="text" name="keterangan" class="form-control" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-flat" id="simpan">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    const rupiah = (number) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR"
        }).format(number);
    }

    function kosong() {
        $('#gapok').val('')
        $('#lembur').val('')
    }

    function cari() {
        var id_pegawai = $('#id_pegawai_c').val();
        var thn = $('#thn_c').val();
        var bln = $('#bln_c').val();

        $.ajax({
            url: '{{ route("admin.akumulasi.gaji") }}',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'id_pegawai': id_pegawai,
                'tahun_cari': thn,
                'bulan_cari': bln,
                '_token': '{{ csrf_token() }}'
            },
            success: function(data) {
                if (data.flash == "Data Ditemukan") {
                    var flash = `
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <p><strong>${data.flash} <i class="fa fa-info"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </strong></p>
                    </div>`;
                    $('.flash').html(flash);
                    $('#lain_nya').prop('hidden', false);
                    $('#lembur').val(data.gaji_lembur);
                    $('#gapok').val(data.gaji_msk);
                    $('#lembur1').val(rupiah(data.gaji_lembur));
                    $('#gapok1').val(rupiah(data.gaji_msk));
                } else {
                    $('#lain_nya').prop('hidden', 'true');
                    kosong();
                    var flash = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <p><strong>${data.flash} <i class="fa fa-info"></i>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </strong></p>
                    </div>`;
                    $('.flash').html(flash);
                }
            }
        });
    }
</script>
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