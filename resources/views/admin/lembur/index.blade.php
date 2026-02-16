@extends('layouts.admin.main')

@section('content')

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

    <div class="table-responsive">
        <table id="lemburini" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>NO.</th>
                    <th>NAMA</th>
                    <th>WAKTU MULAI LEMBUR</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lemburbydate as $index => $lembur)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $lembur->pegawai->nama_pegawai ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($lembur->waktu_lembur)->format('H:i') }}</td>
                    <td>
                        <button class="btn btn-theme btn-sm ml-1" data-toggle="modal" data-target="#editModal{{ $lembur->id_lembur }}">
                            Edit
                        </button>
                        <form action="{{ url('admin/hapus-lembur/' . $lembur->id_lembur) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm ml-1" onclick="return confirm('Yakin Ingin Menghapus?')">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title text-secondary"><strong>Tambah Lembur</strong></h5>
                    <button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-justify">
                    <form action="{{ url('admin/simpan-lembur') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="col-sm-12">Nama Pegawai</label>
                                <div class="col-sm-12">
                                    <select class="form-control" name="id_pegawai" required>
                                        <option value="">-pilih-</option>
                                        @foreach($pegawai as $p)
                                        <option value="{{ $p->id_pegawai }}">{{ $p->nama_pegawai }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-12">Waktu Mulai Lembur</label>
                                <div class="col-sm-12">
                                    <input type="time" name="time" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-flat">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modals -->
    @foreach($lemburbydate as $lembur)
    <div class="modal fade" id="editModal{{ $lembur->id_lembur }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title text-secondary"><strong>Edit Data Lembur Pegawai</strong></h5>
                    <button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-justify">
                    <form action="{{ url('admin/edit-lembur') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id_lembur" value="{{ $lembur->id_lembur }}">

                        <div class="modal-body">
                            <div class="form-group">
                                <label class="col-sm-12">Nama Pegawai</label>
                                <div class="col-sm-12">
                                    <select class="form-control" name="id_pegawai" required>
                                        <option value="">-pilih-</option>
                                        @foreach($pegawai as $p)
                                        <option value="{{ $p->id_pegawai }}" {{ $p->id_pegawai == $lembur->id_pegawai ? 'selected' : '' }}>
                                            {{ $p->nama_pegawai }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-12">Waktu Mulai Lembur</label>
                                <div class="col-sm-12">
                                    <input type="time" name="time" class="form-control" value="{{ $lembur->waktu_lembur }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-flat">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
<script>
    $(document).ready(function() {
        $('#lemburini').DataTable({
            order:[],
            language: {
                emptyTable: "Tidak ada data lembur hari ini",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                }
            }
        });
    });
</script>
@endsection