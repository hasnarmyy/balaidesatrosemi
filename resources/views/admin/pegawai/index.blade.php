@extends('layouts.admin.main')
<style>
    .status-aktif,
    .status-nonaktif {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        padding: 2px 4px;
        border-radius: 4px;

        font-size: 12px !important;
        font-weight: 400;
        line-height: normal !important;

        color: #ffffff !important;
        white-space: nowrap;
    }

    .status-aktif {
        background-color: #28a745;
    }

    .status-nonaktif {
        background-color: #dc3545;
    }
</style>
@section('content')
@if (session('success'))
<div class="alert alert-primary alert-dismissible fade show" role="alert">
    <p><strong><i class="fa fa-check"></i> {{ session('success') }}</strong></p>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <p><strong><i class="fa fa-times"></i> {{ session('error') }}</strong></p>
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
        <table id="pegawai" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>NO.</th>
                    <th>NAMA PEGAWAI</th>
                    <th>STATUS KEPEGAWAIAN</th>
                    <th>TANGGAL MASUK</th>
                    <th>JABATAN</th>
                    <th>NO.HP</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pegawai as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $p->nama_pegawai }}</td>
                    <td>
                        <span class="{{ $p->status_kepegawaian == 1 ? 'status-aktif' : 'status-nonaktif' }}">
                            {{ $p->status_kepegawaian == 1 ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </td>
                    <td>{{ $p->tanggal_masuk }}</td>
                    <td>{{ $p->relasiJabatan->jabatan ?? '-' }}</td>
                    <td>{{ $p->no_hp }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center align-items-center gap-1">
                            <!-- Detail -->
                            <a href="{{ route('admin.pegawai.detail', $p->id_pegawai) }}"
                                class="btn btn-theme btn-sm" title="Detail">
                                <i class="fa fa-eye"></i>
                            </a>

                            <!-- Edit -->
                            <button type="button" class="btn btn-primary btn-sm"
                                data-toggle="modal" data-target="#editModal{{ $p->id_pegawai }}"
                                title="Edit">
                                <i class="fa fa-pencil-square-o"></i>
                            </button>

                            <!-- Hapus -->
                            <form action="{{ route('admin.pegawai.hapus', [$p->id_pegawai, $p->id_user]) }}"
                                method="POST" class="m-0 p-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                                    onclick="return confirm('Yakin Ingin Menghapus?')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title text-secondary"><strong>Tambah Pegawai</strong></h5>
                    <button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-justify">
                    <form action="{{ route('admin.pegawai.tambah') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="hidden" name="id_pegawai" class="form-control"
                                        value="{{ $nextIdPegawai }}">
                                    <input type="hidden" name="id_user" class="form-control"
                                        value="{{ $nextIdUser }}">

                                    <div class="form-group">
                                        <label>Nama</label>
                                        <input type="text" name="nama_pegawai" class="form-control" required
                                            oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                                    </div>

                                    <div class="form-group">
                                        <label>Jenis Kelamin</label>
                                        <select class="form-control" name="jekel" required>
                                            <option value="">-pilih-</option>
                                            <option value="L">Laki-Laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Email</label>
                                        <input
                                            type="email"
                                            name="email"
                                            class="form-control"
                                            placeholder="nama@gmail.com"
                                            required
                                            pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$"
                                            title="Email harus menggunakan domain @gmail.com"
                                            oninput="this.value = this.value.toLowerCase()">
                                    </div>
                                    @error('email')
                                    <small class="text-danger">
                                        {{ $message }}
                                    </small>
                                    @enderror

                                    <div class="form-group">
                                        <label>Pendidikan</label>
                                        <input type="text" name="pendidikan" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Status Kepegawaian</label>
                                        <select class="form-control" name="status_pegawai" required>
                                            <option value="">-pilih-</option>
                                            <option value="1">Aktif</option>
                                            <option value="0">Tidak Aktif</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>KTP</label>
                                        <input type="file" name="userfilektp" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Agama</label>
                                        <select class="form-control" name="agama" required>
                                            <option value="">-pilih-</option>
                                            <option value="Islam">Islam</option>
                                            <option value="Protestan">Protestan</option>
                                            <option value="Katolik">Katolik</option>
                                            <option value="Hindu">Hindu</option>
                                            <option value="Budha">Budha</option>
                                            <option value="Khonghucu">Khonghucu</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Jabatan</label>
                                        <select class="form-control" name="id_jabatan" required>
                                            <option value="">-pilih-</option>
                                            @foreach ($jabatan as $j)
                                            <option value="{{ $j->id_jabatan }}">{{ $j->jabatan }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>No.HP</label>
                                        <input type="number" name="nohp" class="form-control" required
                                            min="0" step="1"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            onkeydown="if(event.key === 'e' || event.key === '-' || event.key === '+') event.preventDefault()">
                                    </div>

                                    <div class="form-group">
                                        <label>Alamat</label>
                                        <input type="text" name="alamat" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Tanggal Masuk</label>
                                        <input type="date" name="tgl_msk" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Foto</label>
                                        <input type="file" name="userfilefoto" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-flat"
                                data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-flat">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @foreach ($pegawai as $p)
    <div class="modal fade" id="editModal{{ $p->id_pegawai }}" tabindex="-1" role="dialog"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title text-secondary"><strong>Edit Pegawai</strong></h5>
                    <button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-justify">
                    <form action="{{ route('admin.pegawai.edit', ['id' => $p->id_pegawai]) }}" method="POST"
                        enctype="multipart/form-data">

                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="hidden" name="id_pegawai" value="{{ $p->id_pegawai }}">
                                    <input type="hidden" name="id_user" value="{{ $p->id_user }}">

                                    <div class="form-group">
                                        <label>Nama</label>
                                        <div class="form-group">
                                            <input type="text" name="nama_pegawai" class="form-control"
                                                value="{{ $p->nama_pegawai }}" required
                                                oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Jenis Kelamin</label>
                                        <select class="form-control" name="jekel" required>
                                            <option value="">-pilih-</option>
                                            <option value="L" {{ $p->jekel == 'L' ? 'selected' : '' }}>
                                                Laki-Laki</option>
                                            <option value="P" {{ $p->jekel == 'P' ? 'selected' : '' }}>
                                                Perempuan</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Pendidikan</label>
                                        <input type="text" name="pendidikan" class="form-control"
                                            value="{{ $p->pendidikan }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Status Kepegawaian</label>
                                        <select class="form-control" name="status_pegawai" required>
                                            <option value="">-pilih-</option>
                                            <option value="1"
                                                {{ $p->status_kepegawaian == 1 ? 'selected' : '' }}>Aktif</option>
                                            <option value="0"
                                                {{ $p->status_kepegawaian == 0 ? 'selected' : '' }}>Tidak Aktif
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>KTP</label>
                                        <input type="file" name="userfilektp" class="form-control">

                                    </div>

                                    <div class="form-group">
                                        <label>Foto</label>
                                        <input type="file" name="userfilefoto" class="form-control">

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Agama</label>
                                        <select class="form-control" name="agama" required>
                                            <option value="">-pilih-</option>
                                            <option value="Islam" {{ $p->agama == 'Islam' ? 'selected' : '' }}>
                                                Islam</option>
                                            <option value="Protestan"
                                                {{ $p->agama == 'Protestan' ? 'selected' : '' }}>Protestan</option>
                                            <option value="Katolik"
                                                {{ $p->agama == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                            <option value="Hindu" {{ $p->agama == 'Hindu' ? 'selected' : '' }}>
                                                Hindu</option>
                                            <option value="Budha" {{ $p->agama == 'Budha' ? 'selected' : '' }}>
                                                Budha</option>
                                            <option value="Khonghucu"
                                                {{ $p->agama == 'Khonghucu' ? 'selected' : '' }}>Khonghucu</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Jabatan</label>
                                        <select class="form-control" name="id_jabatan" required>
                                            <option value="">-pilih-</option>
                                            @foreach ($jabatan as $j)
                                            <option value="{{ $j->id_jabatan }}"
                                                {{ old('id_jabatan', $p->id_jabatan) == $j->id_jabatan ? 'selected' : '' }}>
                                                {{ $j->jabatan }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>No.HP</label>
                                        <input type="text" name="nohp" class="form-control"
                                            value="{{ $p->no_hp }}" required
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>

                                    <div class="form-group">
                                        <label>Alamat</label>
                                        <input type="text" name="alamat" class="form-control"
                                            value="{{ $p->alamat }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Tanggal Masuk</label>
                                        <input type="date" name="tgl_msk" class="form-control"
                                            value="{{ $p->tanggal_masuk }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-flat"
                                data-dismiss="modal">Close</button>
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
        $('#pegawai').DataTable({
            order: [],
            language: {
                emptyTable: "Tidak ada data pegawai",
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
@if ($errors->any())
<script>
    $(document).ready(function() {
        $('#myModal').modal('show');
    });
</script>
@endif
@endsection