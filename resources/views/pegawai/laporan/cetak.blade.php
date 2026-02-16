<!DOCTYPE html>
<html>

<head>
    <title>SLIP GAJI PEGAWAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body onload="window.print()">
    <center>
        <table class="table table-bordered">
            <h4 class="text-center fw-bold">BALAI DESA TROSEMI</h4>
            <center>
                <address class="text-center">
                    <small>
                        Jl. Raya Trosemi No. 45, Desa Trosemi, Kec. Gatak, Kab. Sukoharjo, Jawa Tengah<br>
                        Email: balaidesatrosemi@gmail.com<br>
                        Indonesia
                    </small>
                </address>
            </center>
            <hr>
            <tr height="40px">
                <td colspan="11" class="align-middle">
                    <h6><b><u>DATA PEGAWAI :</u></b></h6>
                </td>
            </tr>
            <tr>
                <td colspan="2"><b>&nbsp;&nbsp;&nbsp;&nbsp;NO PEGAWAI</b></td>
                <td colspan="4"><b>{{ strtoupper($payroll->pegawai->id_pegawai) }}</b></td>
            </tr>
            <tr>
                <td colspan="2"><b>&nbsp;&nbsp;&nbsp;&nbsp;NAMA PEGAWAI</b></td>
                <td colspan="4"><b>{{ strtoupper($payroll->pegawai->nama_pegawai) }}</b></td>
            </tr>
            <tr>
                <td colspan="2"><b>&nbsp;&nbsp;&nbsp;&nbsp;PERIODE</b></td>
                <td colspan="4">
                    <b>{{ \Carbon\Carbon::parse($payroll->periode)->locale('id')->translatedFormat('F Y') }}</b>
                </td>
            </tr>
            <tr>
                <td colspan="2"><b>&nbsp;&nbsp;&nbsp;&nbsp;JABATAN</b></td>
                <td colspan="4"><b>{{ strtoupper($payroll->jabatan->jabatan) }}</b></td>
            </tr>
            <tr height="40px">
                <td colspan="11" class="align-middle">
                    <h6><b><u>PENERIMAAN :</u></b></h6>
                </td>
            </tr>
            <tr>
                <th width="44" class="text-center">1</th>
                <th width="300">Gaji Pokok</th>
                <th width="508" colspan="5">{{ rupiah($gaji['gaji_pokok']) }}</th>
            </tr>
            <tr>
                <th width="44" class="text-center">2</th>
                <th width="300">Upah Lembur</th>
                <th width="508" colspan="5">{{ rupiah($gaji['gaji_lembur']) }}</th>
            </tr>
            <tr>
                <th width="44" class="text-center">3</th>
                <th width="300">Bonus</th>
                <th width="508" colspan="5">{{ rupiah($gaji['bonus']) }}</th>
            </tr>
            <tr>
                <th width="44" class="text-center">4</th>
                <th width="300">Potongan Terlambat</th>
                <th width="508" colspan="5">{{ rupiah($gaji['potongan_absen']) }}</th>
            </tr>
            <tr height="50px">
                <td colspan="10" class="align-middle">
                    <center>
                        <h5><b>Total Gaji Bersih Anda : Rp. {{ rupiah($gaji['gaji_bersih']) }}</b></h5>
                    </center>
                </td>
            </tr>
            <tr height="40px">
                <td colspan="11"></td>
            </tr>
            <tr>
                <td colspan="4" align="center"><b>KETERANGAN</b></td>
                <td colspan="3" align="center"><b>ABSENSI/KEHADIRAN</b></td>
            </tr>
            <tr>
                <td colspan="4" rowspan="5" align="center" class="align-middle">
                    <b>{{ strtoupper($gaji['keterangan'] ?? '-') }}</b>
                </td>
                <td align="center" colspan="3"><b>Masuk : {{ $absen['masuk'] }}</b></td>
            </tr>
            <tr>
                <td align="center" colspan="3"><b>Lembur : {{ number_format($absen['jumlem'], 0) }}</b></td>
            </tr>
            <tr>
                <td align="center" colspan="3"><b>Izin Sakit : {{ $absen['sakit'] }}</b></td>
            </tr>
            <tr>
                <td align="center" colspan="3"><b>Izin Tidak Masuk : {{ $absen['izin'] }}</b></td>
            </tr>
        </table>
    </center>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>
@php
function rupiah($angka)
{
    return 'Rp ' . number_format($angka ?? 0, 0, ',', '.');
}
@endphp