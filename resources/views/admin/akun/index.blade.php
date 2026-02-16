@extends('layouts.admin.main')

@section('content')
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
        <div class="row border-bottom mb-4">
            <div class="col-sm-8 pt-2">
                <h6 class="mb-4 bc-header">{{ $title }}</h6>
            </div>
        </div>

        <div class="table-responsive">
            <table id="userTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>NO.</th>
                        <th>NAMA</th>
                        <th>EMAIL</th>
                        <th>STATUS</th>
                        <th>KETERANGAN</th>
                        <th>RESET PASSWORD</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($akun as $index => $b)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $b['name'] }}</td>
                            <td>{{ $b['email'] }}</td>
                            <td>
                                @if ($b['is_active'] == 1)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                @if ($b['role_id'] == 1)
                                    <span class="badge badge-primary">Super Admin</span>
                                @else
                                    <span class="badge badge-info">Pegawai</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <form action="{{ route('admin.reset.password', $b['id']) }}" method="POST"
                                    onsubmit="return confirm('Yakin Ingin Mereset Password?');" style="display:inline;">
                                    @csrf
                                    <button type="submit"
                                        style="display:inline-block; padding:6px 12px; background-color:#007bff; color:#fff;
                                        font-size:14px; border-radius:4px; text-decoration:none; border:none; cursor:pointer; 
                                        transition:background-color 0.2s;"
                                        onmouseover="this.style.backgroundColor='#0056b3';"
                                        onmouseout="this.style.backgroundColor='#007bff';">
                                        <i class="fa fa-repeat"></i> Reset Password
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            $('#userTable').DataTable({
                order:[],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                },
                "pageLength": 10,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "Semua"]
                ],
                "responsive": true,
                "order": [
                    [0, "asc"]
                ]
            });
        });
    </script>
@endsection
