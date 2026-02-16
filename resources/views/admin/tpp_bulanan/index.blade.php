@extends('layouts.admin.main')

@section('content')

@php
function rupiah($angka)
{
return 'Rp ' . number_format($angka ?? 0, 0, ',', '.');
}
@endphp

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <p><strong><i class="fa fa-check"></i> {{ session('success') }}</strong></p>
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
    <div class="row">
        <div class="col-md-12">
            <div class="row border-bottom mb-4">
                <div class="col-sm-8 pt-2">
                    <h6 class="mb-4 bc-header">{{ $title }}</h6>
                </div>
                <div class="col-sm-4 text-right pb-3">
                    <button class="btn btn-round btn-theme" data-toggle="modal" data-target="#myModal">
                        <i class="fa fa-plus"></i> Tambah
                    </button>
                </div>
            </div>

            <div class="text-center mb-3">
                <h3 class="mb-0"><b>DATA SLIP GAJI PEGAWAI</b></h3>
                <hr>
            </div>

            <form action="{{ route('admin.tpp-bulanan') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-lg-3">
                        <select name="th" id="th" class="form-control">
                            <option value="">- PILIH TAHUN -</option>
                            @if (isset($list_th))
                            @foreach ($list_th as $t)
                            <option value="{{ $t }}" {{ isset($thn) && $thn == $t ? 'selected' : '' }}>
                                {{ $t }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <select name="bln" id="bln" class="form-control">
                            <option value="">- PILIH BULAN -</option>
                            @if (isset($list_bln))
                            @foreach ($list_bln as $t)
                            <option value="{{ $t }}" {{ isset($blnnya) && $blnnya == $t ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($t)->locale('id')->monthName }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <button type="submit" class="btn btn-primary mb-3" id="searchBtn">
                            <i class="fa fa-search"></i> <span id="btnText">Cari</span>
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table id="payrollTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>NO.</th>
                            <th>NAMA PEGAWAI</th>
                            <th>JABATAN</th>
                            <th>GAJI POKOK</th>
                            <th>LEMBUR</th>
                            <th>BONUS</th>
                            <th>POTONGAN TERLAMBAT</th>
                            <th>KETERANGAN</th>
                            <th>GAJI BERSIH</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($gaji as $index => $b)
                        @if($b->details->isNotEmpty())
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $b->pegawai->nama_pegawai }}</td>
                            <td>{{ $b->pegawai->relasiJabatan->jabatan }}</td>
                            <td>{{ rupiah($b->details->first()->gaji_pokok ?? 0) }}</td>
                            <td>{{ rupiah($b->details->first()->gaji_lembur ?? 0) }}</td>
                            <td>{{ rupiah($b->details->first()->bonus ?? 0) }}</td>
                            <td>{{ rupiah($b->details->first()->potongan_absen ?? 0) }}</td>
                            <td>{{ $b->keterangan ?? '-' }}</td>
                            <td>{{ rupiah($b->gaji_bersih ?? 0) }}</td>
                            <td class="text-nowrap">

                                <!-- 🔄 REFRESH (Hitung ulang dari absensi) -->
                                <form action="{{ route('admin.gaji.refresh') }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Perbarui gaji dari absensi?');">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id_payroll" value="{{ $b->id_payroll }}">
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                </form>

                                <!-- ✏️ EDIT (Bonus & Keterangan saja) -->
                                <a class="btn btn-theme btn-sm ml-1"
                                    href="#"
                                    data-toggle="modal"
                                    data-target=".bd-example-modal{{ $b->id_payroll }}">
                                    <i class="fa fa-edit"></i>
                                </a>

                                <!-- 🗑️ HAPUS -->
                                <a href="javascript:void(0);"
                                    onclick="hapusPayroll('{{ $b->id_payroll }}')"
                                    class="btn btn-danger btn-sm ml-1">
                                    <i class="fa fa-trash"></i>
                                </a>

                            </td>
                        </tr>
                        @endif

                        @empty
                            <tr>
                                <td colspan="10" class="text-center">Tidak ada data gaji untuk periode yang dipilih</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Gaji -->
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
                        <input type="hidden" name="bulanpilih" value="{{ $blnselected ?? '' }}">
                        <input type="hidden" name="tahunpilih" value="{{ $thnselected ?? '' }}">

                        <div class="modal-body">
                            <div class="form-group col-sm-12">
                                <div class="flash"></div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-12">Nama Pegawai</label>
                                <div class="col-sm-12">
                                    <select class="form-control" id="id_pegawai_c" name="id_pegawai">
                                        <option value="">-pilih-</option>
                                        @if (isset($pegawai))
                                        @foreach ($pegawai as $j)
                                        <option value="{{ $j->id_pegawai }}">{{ $j->nama_pegawai }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <!-- Tahun & Bulan -->
                            <div class="row col-lg-12">
                                <div class="form-group col-lg-5">
                                    <label class="">Tahun</label>
                                    <div class="">
                                        <select name="th1" id="thn_c" class="form-control">
                                            <option value="">- PILIH TAHUN -</option>
                                            @if (isset($list_th))
                                            @foreach ($list_th as $t)
                                            <option value="{{ $t }}">{{ $t }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-lg-5">
                                    <label class="">Bulan</label>
                                    <div class="">
                                        <select name="bln1" id="bln_c" class="form-control">
                                            <option value="">- PILIH BULAN -</option>
                                            @if (isset($list_bln))
                                            @foreach ($list_bln as $t)
                                            <option value="{{ $t }}">
                                                {{ \Carbon\Carbon::create()->month($t)->locale('id')->monthName }}
                                            </option>
                                            @endforeach
                                            @endif
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

                            <!-- Gaji Pokok, Lembur, Potongan, Bonus, Keterangan -->
                            <div class="form-group col-lg-12" id="lain_nya" hidden>
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
                                            <input type="text" name="lembur1" id="lembur1" class="form-control" readonly required>
                                            <input type="hidden" name="lembur" id="lembur" class="form-control" readonly required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label class="">Potongan Terlambat</label>
                                    <div class="">
                                        <input type="text" name="potongan1" id="pot_ter1" class="form-control" readonly required>
                                        <input type="hidden" name="potongan" id="pot_ter" class="form-control" readonly required>
                                    </div>
                                </div>
                                <div class="row col-lg-12">
                                    <div class="form-group col-lg-6">
                                        <label class="">Bonus</label>
                                        <div class="">
                                            <input type="number" name="bonus" class="form-control"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label class="">Keterangan</label>
                                        <div class="">
                                            <input type="text" name="keterangan" class="form-control"
                                                oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')" required>
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

    <!-- Modal Edit Gaji -->
    @if (isset($gaji) && count($gaji) > 0)
    @foreach ($gaji as $j)
    <div class="modal fade bd-example-modal{{ $j->id_payroll }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title text-secondary"><strong>Edit Data Lembur Pegawai</strong></h5>
                    <button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-justify">
                    <form class="form-horizontal" action="{{ route('admin.gaji.update') }}" method="post">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="bulanpilih" value="{{ $blnselected ?? '' }}">
                        <input type="hidden" name="tahunpilih" value="{{ $thnselected ?? '' }}">
                        <input type="hidden" name="id_payroll" value="{{ $j->id_payroll }}" required>
                        <input type="hidden" name="gaber" value="{{ $j->gaji_bersih }}" required>

                        <div class="modal-body">
                            <div class="row col-lg-12">
                                <div class="row col-lg-12">
                                    <div class="form-group col-lg-6">
                                        <label class="">Bonus</label>
                                        <input type="number" name="bonus" class="form-control" value="{{ $j->bonus }}"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label class="">Keterangan</label>
                                        <input type="text" name="keterangan" class="form-control" value="{{ $j->keterangan }}"
                                            oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')" required>
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
    @endforeach
    @endif
</div>

<!-- JS: DataTables, Akumulasi, Update Button -->
<script>
    $(document).ready(function() {
        $('#payrollTable').DataTable({
            order:[],
            "language": {
                "emptyTable": "Tidak ada data gaji pegawai bulanan",
                "lengthMenu": "Show _MENU_ entries",
                "search": "Search:",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "Showing 0 to 0 of 0 entries",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            },
            "order": [
                [0, "asc"]
            ],
            "pageLength": 10,
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            "responsive": true,
            "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
            "initComplete": function() {
                $('.dataTables_empty').css('background-color', '#f8f9fa');
            }
        });

        updateButtonText();

        $('#th, #bln').on('change', function() {
            updateButtonText();
        });
    });

    function updateButtonText() {
        var tahun = $('#th').val();
        var bulan = $('#bln').val();
        $('#btnText').text(tahun && bulan ? 'Refresh' : 'Cari');
    }

    const rupiah = (number) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR"
        }).format(number);
    }

    function kosong() {
        $('#gapok').val('');
        $('#lembur').val('');
        $('#pot_ter').val('');
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
                '_token': '{{ csrf_token() }}',
                'id_pegawai': id_pegawai,
                'tahun_cari': thn,
                'bulan_cari': bln
            },
            success: function(data) {
                if (data.flash == "Data Ditemukan") {
                    $('.flash').html(`
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <p><strong>${data.flash} <i class="fa fa-info"></i></strong></p>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>`);
                    $('#lain_nya').prop('hidden', false);
                    $('#lembur').val(data.gaji_lembur);
                    $('#gapok').val(data.gaji_msk);
                    $('#pot_ter').val(data.potongan_terlambat);
                    $('#lembur1').val(rupiah(data.gaji_lembur));
                    $('#gapok1').val(rupiah(data.gaji_msk));
                    $('#pot_ter1').val(rupiah(data.potongan_terlambat));
                } else if (data.flash == "Gaji Bulan Ini Kosong") {
                    $('#lain_nya').prop('hidden', 'true');
                    kosong();
                    $('.flash').html(`
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <p><strong>${data.flash} <i class="fa fa-info"></i></strong></p>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>`);
                }
            }
        });
    }

    // Hapus payroll dengan DELETE
    function hapusPayroll(id) {
        if (confirm('Yakin ingin hapus gaji bulanan ini?')) {
            var form = document.createElement('form');
            form.action = `/admin/hapus-gaji/${id}`;
            form.method = 'POST';

            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            var methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endsection