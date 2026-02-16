@extends('layouts.admin.main')

@section('content')

@php
function nmbulan($angka) {
$bulan = [
1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
return $bulan[$angka] ?? '';
}
@endphp

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <p><strong><i class="fa fa-check"></i> {{ session('success') }}</strong></p>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <p><strong><i class="fa fa-exclamation"></i> {{ session('error') }}</strong></p>
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
                <h3 class="mb-0"><b>DATA LEMBUR PEGAWAI</b></h3>
                @if(!empty($thnselected) && !empty($blnselected))
                <div class="mt-0">
                    <h6 class="mb-0"><b>({{ nmbulan($blnselected) }}-{{ $thnselected }})</b></h6>
                </div>
                @endif
                <hr>
            </div>

            <form action="{{ route('admin.lembur-bulanan.filter') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-lg-3">
                        <select name="th" id="th" class="form-control">
                            <option value="">- PILIH TAHUN -</option>
                            @foreach($list_th as $t)
                            <option value="{{ $t }}" {{ isset($thn) && $thn == $t ? 'selected' : '' }}>
                                {{ $t }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <select name="bln" id="bln" class="form-control">
                            <option value="">- PILIH BULAN -</option>
                            @foreach($list_bln as $t)
                            <option value="{{ $t }}" {{ isset($blnnya) && $blnnya == $t ? 'selected' : '' }}>
                                {{ nmbulan($t) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <button type="submit" class="btn btn-primary mb-3" id="searchBtn">
                            <i class="fa fa-search"></i> <span id="btnText">Cari</span>
                        </button>

                        @if(!empty($thnselected) && !empty($blnselected))
                        <a target="_blank"
                            href="{{ route('admin.cetak.absen.lembur', [$thnselected, $blnselected]) }}"
                            class="btn btn-danger mb-3 ml-2">
                            <i class="fa fa-print"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table id="lemburTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>NO.</th>
                            <th>NAMA</th>
                            <th>JABATAN</th>
                            <th>MASUK</th>
                            <th>LEMBUR</th>
                            <th>IZIN</th>
                            <th>SAKIT</th>
                            <th>TOTAL MASUK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($absen) && count($absen) > 0)
                        @foreach($absen as $index => $b)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $b->pegawai->nama_pegawai }}</td>
                            <td>{{ $b->pegawai->relasiJabatan->jabatan }}</td>
                            <td>{{ $b->masuk }} hari</td>
                            <td>{{ $b->jumlem }} hari</td>
                            <td>{{ $b->izin }} hari</td>
                            <td>{{ $b->sakit }} hari</td>
                            <td>{{ $b->masuk + $b->jumlem }} hari</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#lemburTable').DataTable({
            order:[],
            "language": {
                "emptyTable": "Tidak ada data lembur bulanan",
                "lengthMenu": "Tampilkan _MENU_ data",
                "search": "Cari:",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "infoFiltered": "(difilter dari _MAX_ total data)",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            },
            "pageLength": 10,
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "Semua"]
            ],
            "responsive": true,
            "order": [
                [0, "asc"]
            ]
        });

        updateButtonText();

        $('#th, #bln').on('change', function() {
            updateButtonText();
        });
    });

    function updateButtonText() {
        var tahun = $('#th').val();
        var bulan = $('#bln').val();

        if (tahun && bulan) {
            $('#btnText').text('Refresh');
        } else {
            $('#btnText').text('Cari');
        }
    }
</script>
@endsection