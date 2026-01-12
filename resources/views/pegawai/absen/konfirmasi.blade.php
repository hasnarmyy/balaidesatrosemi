<!-- views/pegawai/absen/konfirmasi.blade.php -->

@extends('layouts.pegawai.main')

@section('content')
<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="{{ asset('jamServer.js') }}"></script>

<div class="mt-4 mb-4 p-3 bg-white border shadow-sm lh-sm">
    <div class="row border-bottom mb-4">
        <div class="col-sm-8 pt-2">
            <h6 class="mb-4 bc-header">{{ $title }}</h6>
        </div>
    </div>

    <div class="mt-1 mb-5 button-container">
        <div class="card shadow-sm">
            <div class="card-header bg-primary">
                <h6 class="text-white">Konfirmasi Absen Hari Ini</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>NO.</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            // Grouping berdasarkan tanggal (1 pegawai per hari)
                            $grouped = $konfirmasi_absen->groupBy('tanggal');
                            @endphp

                            @foreach ($grouped as $tanggal => $items)
                            @php
                            $absenMasuk = $items->where('keterangan', 1)->first();
                            $absenPulang = $items->where('keterangan', 2)->first();
                            $absenLembur = $items->where('keterangan', 3)->first();
                            $izinSakit = $items->where('keterangan', 4)->first();
                            $izinTidakMasuk = $items->where('keterangan', 5)->first();

                            // Prioritas keluar: lembur > pulang
                            $absenKeluar = $absenLembur ?? $absenPulang;
                            @endphp

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $tanggal }}</td>

                                {{-- WAKTU --}}
                                <td>
                                    @if ($absenMasuk)
                                    <div><strong>Masuk:</strong> {{ $absenMasuk->waktu }}</div>
                                    @endif
                                    @if ($absenKeluar)
                                    <div><strong>{{ $absenLembur ? 'Lembur' : 'Pulang' }}:</strong> {{ $absenKeluar->waktu }}</div>
                                    @endif
                                    @if ($izinSakit || $izinTidakMasuk)
                                    <div>{{ ($izinSakit ?? $izinTidakMasuk)->waktu }}</div>
                                    @endif
                                </td>

                                {{-- KETERANGAN --}}
                                <td>
                                    @if ($absenMasuk)
                                    <span class="badge badge-success">Masuk</span>
                                    @if ($absenMasuk->keterangan_msk == 0 && $absenMasuk->status >= 1)
                                    <span class="badge badge-success">(TIDAK TELAT)</span>
                                    @elseif ($absenMasuk->keterangan_msk == 1)
                                    <span class="badge badge-danger">(TELAT)</span>
                                    @endif
                                    <br>
                                    @endif

                                    @if ($absenPulang)
                                    <span">Pulang</span>
                                        @elseif ($absenLembur)
                                        <span">Lembur</span>
                                            @endif

                                            @if ($izinSakit)
                                            <span">Izin Sakit</span>
                                                @elseif ($izinTidakMasuk)
                                                <span">Izin Tidak Masuk</span>
                                                    @endif
                                </td>

                                {{-- STATUS --}}
                                <td>
                                    @if ($absenMasuk)
                                    <div>
                                        <span>Masuk:</span>
                                        @if ($absenMasuk->status >= 1)
                                        <span class="badge badge-success">Terkonfirmasi</span>
                                        @else
                                        <span class="badge badge-danger">Belum dikonfirmasi</span>
                                        @endif
                                    </div>
                                    @endif

                                    @if ($absenPulang)
                                    <div>
                                        <span>Pulang:</span>
                                        @if ($absenPulang->status >= 2)
                                        <span class="badge badge-success">Terkonfirmasi</span>
                                        @else
                                        <span class="badge badge-danger">Belum dikonfirmasi</span>
                                        @endif
                                    </div>
                                    @elseif ($absenLembur)
                                    <div>
                                        <span>Lembur:</span>
                                        @if ($absenLembur->status >= 3)
                                        <span class="badge badge-success">Terkonfirmasi</span>
                                        @else
                                        <span class="badge badge-danger">Belum dikonfirmasi</span>
                                        @endif
                                    </div>
                                    @endif

                                    @if ($izinSakit)
                                    <div>
                                        <span>Sakit:</span>
                                        @if ($izinSakit->status >= 4)
                                        <span class="badge badge-success">Terkonfirmasi</span>
                                        @else
                                        <span class="badge badge-danger">Belum dikonfirmasi</span>
                                        @endif
                                    </div>
                                    @elseif ($izinTidakMasuk)
                                    <div>
                                        <span>Izin:</span>
                                        @if ($izinTidakMasuk->status >= 5)
                                        <span class="badge badge-success">Terkonfirmasi</span>
                                        @else
                                        <span class="badge badge-danger">Belum dikonfirmasi</span>
                                        @endif
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection