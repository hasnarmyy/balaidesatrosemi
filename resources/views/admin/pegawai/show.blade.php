@extends('layouts.admin.main')
@section('content')
<style>
.status-aktif {
    color: #28a745 !important;
    font-weight: 700;
}
.status-nonaktif {
    color: #dc3545 !important;
    font-weight: 700;
}
</style>

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
    </div>

    <div class="content-wrapper">
        <section class="content">
            <table class="table table-bordered table-hover table-striped table-sm mb-0">
                <tbody>
                    <tr>
                        <th>Id Pegawai</th>
                        <td>{{ $detail_pegawai['id_pegawai'] }}</td>
                        <td rowspan="5" width="350px">
                            <img src="{{ asset('/storage/' . $detail_pegawai->foto) }}" class="card-img mx-auto d-block" style="width: 120px; height: 140px;">
                            <a href="{{ asset('/storage/' . $detail_pegawai->foto) }}" target="_blank">
                                <center>Foto</center>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Nama Lengkap</th>
                        <td>{{ $detail_pegawai['nama_pegawai'] }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Kelamin</th>
                        <td>
                            @if ($detail_pegawai['jekel'] == 'P')
                            Perempuan
                            @else
                            Laki-Laki
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Pendidikan</th>
                        <td>{{ $detail_pegawai['pendidikan'] }}</td>
                    </tr>
                    <tr>
                        <th>Status Kepegawaian</th>
                        <td>
                            @if ($detail_pegawai['status_kepegawaian'] == 1)
                            <span class="status-aktif">Aktif</span>
                            @else
                            <span class="status-nonaktif">Tidak Aktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Agama</th>
                        <td>{{ $detail_pegawai['agama'] }}</td>
                        <td rowspan="5">
                            <img src="{{ asset('/storage/' . $detail_pegawai->ktp) }}" class="card-img mx-auto d-block" style="width: 290px; height: 140px;">
                            <a href="{{ asset('/storage/' . $detail_pegawai->ktp) }}" target="_blank">
                                <center>Kartu Tanda Penduduk</center>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Jabatan</th>
                        <td>{{ $detail_pegawai->relasiJabatan->jabatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>No_hp</th>
                        <td>{{ $detail_pegawai->no_hp }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $detail_pegawai->alamat}}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Masuk</th>
                        <td>{{ $detail_pegawai->tanggal_masuk}}</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
</div>
@endsection