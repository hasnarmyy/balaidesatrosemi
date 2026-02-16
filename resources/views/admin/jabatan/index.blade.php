@extends('layouts.admin.main')

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
            <button class="btn btn-round btn-theme" data-toggle="modal" data-target="#myModal">
                <i class="fa fa-plus"></i> Tambah
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table id="jabatan" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>NO.</th>
                    <th>JABATAN</th>
                    <th>GAJI/HARI</th>
                    <th>LEMBUR/JAM</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($jabatan as $b)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $b->jabatan }}</td>
                    <td>Rp {{ number_format($b->salary, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($b->overtime, 0, ',', '.') }}</td>
                    <td>
                        <a class="btn btn-theme ml-1" data-toggle="modal" data-target=".bd-example-modal{{ $b->id_jabatan }}">Edit</a>
                        <a class="btn btn-danger ml-1" href="{{ route('admin.jabatan.hapus', $b->id_jabatan) }}" onclick="return confirm('Yakin Ingin Menghapus?');">Hapus</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title text-secondary"><strong> Tambah Jabatan</strong></h5>
                    <button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-justify">
                    <form class="form-horizontal" action="{{ route('admin.jabatan.tambah') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="col-sm-12">Jabatan</label>
                                <div class="col-sm-12">
                                    <input type="text" name="jabatan" class="form-control"
                                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-12">Gaji/hari</label>
                                <div class="col-sm-12">
                                    <input type="number" name="salary" class="form-control" required
                                        min="0" step="1"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        onkeydown="if(event.key === 'e' || event.key === '-' || event.key === '+') event.preventDefault()">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-12">Lembur/jam</label>
                                <div class="col-sm-12">
                                    <input type="number" name="overtime" class="form-control" required
                                        min="0" step="1"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        onkeydown="if(event.key === 'e' || event.key === '-' || event.key === '+') event.preventDefault()">
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

    @foreach ($jabatan as $j)
    <div class="modal fade bd-example-modal{{ $j->id_jabatan }}" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title text-secondary"><strong>Edit Jabatan</strong></h5>
                    <button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-justify">
                    <form class="form-horizontal" action="{{ route('admin.jabatan.edit') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id_jabatan" value="{{ $j->id_jabatan }}">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="col-sm-12">Jabatan</label>
                                <div class="col-sm-12">
                                    <input type="text" name="jabatan" value="{{ $j->jabatan }}" class="form-control"
                                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-12">Gaji/hari</label>
                                <div class="col-sm-12">
                                    <input type="number" name="salary" value="{{ $j->salary }}" class="form-control" required
                                        min="0" step="1"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        onkeydown="if(event.key === 'e' || event.key === '-' || event.key === '+') event.preventDefault()">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-12">Lembur/Jam</label>
                                <div class="col-sm-12">
                                    <input type="number" name="overtime" value="{{ $j->overtime }}" class="form-control" required
                                        min="0" step="1"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        onkeydown="if(event.key === 'e' || event.key === '-' || event.key === '+') event.preventDefault()">
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
</div>
<script>
    $(document).ready(function() {
        $('#jabatan').DataTable({
            order: [],
            language: {
                emptyTable: "Tidak ada data jabatan",
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