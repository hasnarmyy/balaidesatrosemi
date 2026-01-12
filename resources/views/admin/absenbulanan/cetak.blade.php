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
return $bulan[$angka] ?? '';
}
@endphp

<!DOCTYPE html>
<html>

<head>
    <title>REKAP ABSENSI PEGAWAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body @if (request()->segment(2) === 'cetak-absen-bulanan') onload="window.print()" @endif>
    <center>
        <br>
        <h4><b>REKAP ABSEN PEGAWAI</b></h4>
        <hr>

        <table width="700">
            <tr>
                <td colspan="2"><b>NAMA PEGAWAI</b></td>
                <td colspan="9"><b>: {{ strtoupper($detail_pegawai['nama_pegawai']) }}</b></td>
            </tr>
            <tr>
                <td colspan="2"><b>JABATAN</b></td>
                <td colspan="9"><b>: {{ strtoupper($detail_pegawai->relasiJabatan->jabatan) }}</b></td>
            </tr>
            <tr>
                <td colspan="2"><b>PERIODE ABSEN</b></td>
                <td colspan="9"><b>{{ strtoupper(nmbulan($blnselected)) }}-{{ strtoupper($thnselected) }}</b></td>
            </tr>
        </table>

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>NO.</th>
                    <th>TANGGAL</th>
                    <th>WAKTU</th>
                    <th>JENIS ABSEN</th>
                    <th>LEMBUR</th>
                    <th>KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($absen as $b)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $b['tanggal'] }}</td>
                    <td>{{ $b['waktu'] }}</td>
                    <td class="text-center">
                        @if ($b['keterangan'] == 0)
                        Alfa
                        @elseif($b['keterangan'] == 1)
                        Masuk
                        @elseif($b['keterangan'] == 2)
                        Pulang
                        @elseif($b['keterangan'] == 3)
                        Lembur
                        @elseif($b['keterangan'] == 4)
                        Izin Sakit
                        @elseif($b['keterangan'] == 5)
                        Izin Tidak Masuk
                        @endif
                    </td>
                    <td>
                        @if (is_null($b['id_lembur']))
                        Tidak
                        @else
                        Iya
                        @endif
                    </td>
                    <td>
                        @switch($b['keterangan'])
                        @case(1)
                        Masuk
                        @break

                        @case(2)
                        Pulang
                        @break

                        @case(3)
                        Lembur
                        @break

                        @case(4)
                        @case(5)
                        {{ $b['keterangan_izin'] }}
                        @break

                        @default
                        -
                        @endswitch
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </center>
</body>

</html>