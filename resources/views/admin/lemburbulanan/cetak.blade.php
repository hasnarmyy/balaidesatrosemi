<!DOCTYPE html>
<html>

<head>
    <title>REKAP ABSENSI PEGAWAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body @if (request()->segment(2) === 'cetak-absen-lembur') onload="window.print()" @endif>
    <center>
        <br>
        <font size="4"><b>REKAP ABSEN PEGAWAI</b></font><br><br>
        <hr>

        <table id="example" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>NO.</th>
                    <th>NAMA</th>
                    <th>MASUK</th>
                    <th>LEMBUR</th>
                    <th>IZIN</th>
                    <th>SAKIT</th>
                    <th>TOTAL MASUK</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($absen as $b)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $b->pegawai->nama_pegawai }}</td>
                        <td>{{ $b->masuk }} hari</td>
                        <td>{{ $b->jumlem }} hari</td>
                        <td>{{ $b->izin }} hari</td>
                        <td>{{ $b->sakit }} hari</td>
                        <td>{{ $b->masuk + $b->jumlem }} hari</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </center>
</body>

</html>
