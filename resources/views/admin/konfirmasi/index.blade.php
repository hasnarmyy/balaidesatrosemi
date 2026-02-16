@extends('layouts.admin.main')

@section('content')
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <p><strong><i class="fa fa-check-circle"></i> {{ session('success') }}</strong></p>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <p><strong><i class="fa fa-times-circle"></i> {{ session('error') }}</strong></p>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session('info'))
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <p><strong><i class="fa fa-info-circle"></i> {{ session('info') }}</strong></p>
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

    <div class="table-responsive">
        <table id="konfirmasi" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>NAMA</th>
                    <th>TANGGAL</th>
                    <th>WAKTU</th>
                    <th>BUKTI MASUK/SURAT</th>
                    <th>BUKTI PULANG</th>
                    <th>JENIS ABSEN</th>
                    <th>STATUS</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @php
                // Grouping data berdasarkan pegawai + tanggal
                $grouped = $konfirmasi->groupBy(function($item) {
                return $item->id_pegawai . '_' . $item->tanggal;
                });
                @endphp

                @foreach ($grouped as $key => $items)
                @php
                $firstItem = $items->first();
                $absenMasuk = $items->where('keterangan', 1)->first();
                $absenPulang = $items->where('keterangan', 2)->first();
                $absenLembur = $items->where('keterangan', 3)->first();
                $izinSakit = $items->where('keterangan', 4)->first();
                $izinTidakMasuk = $items->where('keterangan', 5)->first();

                // Prioritas keluar: lembur > pulang
                $absenKeluar = $absenLembur ?? $absenPulang;

                // Jika ada izin, maka tidak ada masuk/pulang
                $adaIzin = $izinSakit || $izinTidakMasuk;
                @endphp

                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $firstItem->pegawai->nama_pegawai }}</td>
                    <td>{{ \Carbon\Carbon::parse($firstItem->tanggal)->format('d-m-Y') }}</td>

                    {{-- WAKTU --}}
                    <td>
                        @if ($absenMasuk)
                        <div><strong>Masuk:</strong> {{ $absenMasuk->waktu }}</div>
                        @endif
                        @if ($absenKeluar)
                        <div><strong>{{ $absenLembur ? 'Lembur' : 'Pulang' }}:</strong> {{ $absenKeluar->waktu }}</div>
                        @endif
                        @if ($adaIzin)
                        <div>{{ ($izinSakit ?? $izinTidakMasuk)->waktu }}</div>
                        @endif
                    </td>

                    {{-- BUKTI MASUK/SURAT --}}
                    <td class="text-center">
                        @if ($absenMasuk)
                        <a href="{{ asset('storage/' . $absenMasuk->foto_selfie_masuk) }}" target="_blank">
                            <img src="{{ asset('storage/' . $absenMasuk->foto_selfie_masuk) }}"
                                style="width:80px;height:80px; object-fit:cover;" class="rounded">
                        </a>
                        @elseif ($izinSakit && $izinSakit->foto_selfie_masuk)
                        <a href="{{ asset('storage/' . $izinSakit->foto_selfie_masuk) }}" target="_blank">
                            <img src="{{ asset('storage/' . $izinSakit->foto_selfie_masuk) }}"
                                style="width:80px;height:80px; object-fit:cover;" class="rounded">
                        </a>
                        @else
                        <span class="badge badge-secondary">-</span>
                        @endif
                    </td>

                    {{-- BUKTI PULANG --}}
                    <td class="text-center">
                        @if ($absenKeluar && $absenKeluar->foto_selfie_pulang)
                        <a href="{{ asset('storage/' . $absenKeluar->foto_selfie_pulang) }}" target="_blank">
                            <img src="{{ asset('storage/' . $absenKeluar->foto_selfie_pulang) }}"
                                style="width:80px;height:80px; object-fit:cover;" class="rounded">
                        </a>
                        @elseif ($absenKeluar)
                        <span class="badge badge-warning">Belum Upload</span>
                        @else
                        <span class="badge badge-secondary">-</span>
                        @endif
                    </td>

                    <td class="text-center">
                        @if ($absenMasuk)
                        <div>
                            <span>Masuk</span>
                            @if ($absenMasuk->keterangan_msk == 1)
                            <span> (Telat)</span>
                            @elseif ($absenMasuk->keterangan_msk == 0 && $absenMasuk->status >= 1)
                            <span> (Tepat Waktu)</span>
                            @endif
                        </div>
                        @endif

                        @if ($absenPulang)
                        <div><span>Pulang</span></div>
                        @elseif ($absenLembur)
                        <div><span>Lembur</span></div>
                        @endif

                        @if ($izinSakit)
                        <div><span>Izin Sakit</span></div>
                        @elseif ($izinTidakMasuk)
                        <div><span>Izin Tidak Masuk</span></div>
                        @endif
                    </td>

                    <td class="text-center">
                        @if ($absenMasuk)
                        <div>
                            <strong>Masuk:</strong>
                            <span>{{ $absenMasuk->status == 0 ? 'Menunggu' : 'Terkonfirmasi' }}</span>
                        </div>
                        @endif

                        @if ($absenKeluar)
                        <div>
                            <strong>{{ $absenLembur ? 'Lembur' : 'Pulang' }}:</strong>
                            <span>
                                {{
                    ($absenKeluar->status == 0 ||
                    ($absenLembur && $absenLembur->status < 3) ||
                    ($absenPulang && $absenPulang->status < 2))
                    ? 'Menunggu'
                    : 'Terkonfirmasi'
                }}
                            </span>
                        </div>
                        @endif

                        @if ($izinSakit)
                        <div>
                            <span>{{ $izinSakit->status == 0 ? 'Menunggu' : 'Terkonfirmasi' }}</span>
                        </div>
                        @elseif ($izinTidakMasuk)
                        <div>
                            <span>{{ $izinTidakMasuk->status == 0 ? 'Menunggu' : 'Terkonfirmasi' }}</span>
                        </div>
                        @endif
                    </td>

                    {{-- AKSI --}}
                    <td class="text-center">
                        {{-- KONFIRMASI MASUK --}}
                        @if ($absenMasuk && $absenMasuk->status == 0)
                        <form action="{{ route('admin.konfirmasi.absen', $absenMasuk->id_presents) }}" method="POST" class="mb-1">
                            @csrf
                            <button class="btn btn-sm btn-primary btn-block">
                                <i class="fa fa-check"></i> Konfirmasi Masuk
                            </button>
                        </form>
                        @endif

                        {{-- KONFIRMASI PULANG --}}
                        @if ($absenPulang && $absenPulang->status == 0)
                        <form action="{{ route('admin.konfirmasi.absen.pulang', $absenPulang->id_presents) }}" method="POST" class="mb-1">
                            @csrf
                            <button class="btn btn-sm btn-primary btn-block">
                                <i class="fa fa-check"></i> Konfirmasi Pulang
                            </button>
                        </form>
                        @endif

                        {{-- KONFIRMASI LEMBUR --}}
                        @if ($absenLembur && $absenLembur->status == 0)
                        <form action="{{ route('admin.konfirmasi.absen.lembur', ['id_presents' => $absenLembur->id_presents, 'id_pegawai' => $absenLembur->id_pegawai]) }}" method="POST" class="mb-1">
                            @csrf
                            <button class="btn btn-sm btn-primary btn-block">
                                <i class="fa fa-check"></i> Konfirmasi Lembur
                            </button>
                        </form>
                        @endif

                        {{-- KONFIRMASI SAKIT --}}
                        @if ($izinSakit && $izinSakit->status == 0)
                        <form action="{{ route('admin.konfirmasi.absen.sakit', $izinSakit->id_presents) }}" method="POST" class="mb-1">
                            @csrf
                            <button class="btn btn-sm btn-primary btn-block">
                                <i class="fa fa-check"></i> Konfirmasi Sakit
                            </button>
                        </form>
                        @endif

                        {{-- KONFIRMASI IZIN TIDAK MASUK --}}
                        @if ($izinTidakMasuk && $izinTidakMasuk->status == 0)
                        <form action="{{ route('admin.konfirmasi.absen.tidak_masuk', $izinTidakMasuk->id_presents) }}" method="POST" class="mb-1">
                            @csrf
                            <button class="btn btn-sm btn-primary btn-block">
                                <i class="fa fa-check"></i> Konfirmasi Izin
                            </button>
                        </form>
                        @endif

                        {{-- JIKA SEMUA SUDAH TERKONFIRMASI --}}
                        @php
                        $semuaTerkonfirmasi = true;
                        if ($absenMasuk && $absenMasuk->status == 0) $semuaTerkonfirmasi = false;
                        if ($absenKeluar && $absenKeluar->status == 0) $semuaTerkonfirmasi = false;
                        if ($izinSakit && $izinSakit->status == 0) $semuaTerkonfirmasi = false;
                        if ($izinTidakMasuk && $izinTidakMasuk->status == 0) $semuaTerkonfirmasi = false;
                        @endphp

                        @if ($semuaTerkonfirmasi)
                        <span class="badge badge-success">
                            <i class="fa fa-check-circle"></i> Semua Terkonfirmasi
                        </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#konfirmasi').DataTable({
            order:[],
            language: {
                emptyTable: "Tidak ada data konfirmasi absen hari ini",
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