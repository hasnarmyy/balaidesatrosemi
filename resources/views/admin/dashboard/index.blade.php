@extends('layouts.admin.main')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="font-weight-bold text-dark mb-1">Dashboard Admin</h3>
                    <p class="text-muted mb-0">Selamat datang kembali, {{ $user->name }}</p>
                </div>
                <div class="badge badge-primary badge-pill px-3 py-2" style="font-size: 0.9rem;">
                    <i
                        class="fa fa-calendar-alt mr-1"></i>{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-3">
            <a href="{{ route('admin.pegawai') }}" style="text-decoration: none;">
                <div class="card border-0 shadow-sm h-100"
                    style="border-radius: 15px; overflow: hidden; transition: transform 0.2s;">
                    <div class="card-body bg-success">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="text-white">
                                <p class="mb-2 font-weight-600" style="font-size: 0.95rem; opacity: 0.9;">Data Pegawai
                                </p>
                                <h2 class="font-weight-bold mb-1">{{ $totalPegawaiAktif }}</h2>
                                <p class="mb-0 font-weight-500" style="font-size: 0.9rem;">Pegawai Aktif</p>
                            </div>
                            <div class="bg-white shadow-sm"
                                style="width: 55px; height: 55px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-users text-success" style="font-size: 1.8rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-3">
            <a href="{{ route('admin.tampil-konfirmasi') }}" style="text-decoration: none;">
                <div class="card border-0 shadow-sm h-100"
                    style="border-radius: 15px; overflow: hidden; transition: transform 0.2s;">
                    <div class="card-body bg-danger">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="text-white">
                                <p class="mb-2 font-weight-600" style="font-size: 0.95rem; opacity: 0.9;">Absensi Hari
                                    Ini</p>
                                <h2 class="font-weight-bold mb-1">{{ $totalKehadiranHariIni }}</h2>
                                <p class="mb-0 font-weight-500" style="font-size: 0.9rem;">Hadir</p>
                            </div>
                            <div class="bg-white shadow-sm"
                                style="width: 55px; height: 55px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-bookmark text-danger" style="font-size: 1.8rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-3">
            <a href="{{ route('admin.tambah-lembur') }}" style="text-decoration: none;">
                <div class="card border-0 shadow-sm h-100"
                    style="border-radius: 15px; overflow: hidden; transition: transform 0.2s;">
                    <div class="card-body" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="text-white">
                                <p class="mb-2 font-weight-600" style="font-size: 0.95rem; opacity: 0.9;">Lembur Bulan
                                    Ini</p>
                                <h2 class="font-weight-bold mb-1">{{ $totalLemburBulanIni }}</h2>
                                <p class="mb-0 font-weight-500" style="font-size: 0.9rem;">Total Lembur</p>
                            </div>
                            <div class="bg-white shadow-sm"
                                style="width: 55px; height: 55px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-clock" style="font-size: 1.8rem; color: #4facfe;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <a href="{{ route('admin.tampil-konfirmasi') }}" style="text-decoration: none;">
                <div class="card border-0 shadow-sm h-100"
                    style="border-radius: 15px; overflow: hidden; transition: transform 0.2s;">
                    <div class="card-body bg-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-white">
                                <p class="mb-2 font-weight-600" style="font-size: 1rem; opacity: 0.9;">Absen Pending
                                    Hari Ini</p>
                                <h1 class="font-weight-bold mb-1 display-4">{{ $pelaporanPending }}</h1>
                                <p class="mb-0 font-weight-500" style="font-size: 0.95rem;">Menunggu Konfirmasi</p>
                            </div>
                            <div class="bg-white shadow-sm"
                                style="width: 70px; height: 70px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-hourglass-half text-info" style="font-size: 2.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body bg-warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-white">
                            <p class="mb-2 font-weight-600" style="font-size: 1rem; opacity: 0.9;">User Aktif</p>
                            <h1 class="font-weight-bold mb-1 display-4">{{ $totalUser }}</h1>
                            <p class="mb-0 font-weight-500" style="font-size: 0.95rem;">Total User Terdaftar</p>
                        </div>
                        <div class="bg-white shadow-sm"
                            style="width: 70px; height: 70px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa fa-users-cog text-warning" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header border-0 bg-white py-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 font-weight-bold text-dark">
                        <i class="fa fa-chart-line text-primary mr-2"></i>
                        Grafik Payroll Tahun {{ \Carbon\Carbon::now()->year }}
                    </h5>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Total gaji yang dibayarkan per bulan</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <canvas id="payrollChart" data-chart='@json($chartData)' style="height: 300px;"></canvas>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header bg-success py-3">
            <h6 class="mb-0 text-white font-weight-600">
                <i class="fa fa-building mr-2"></i>Balai Desa Trosemi
            </h6>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2 text-center mb-3 mb-md-0">
                    <i class="fa fa-map-marker-alt fa-4x text-muted"></i>
                </div>
                <div class="col-md-10">
                    <h5 class="font-weight-bold text-dark mb-2">Balai Desa Trosemi</h5>
                    <p class="mb-1 text-muted">
                        <i class="fa fa-map-pin mr-2 text-success"></i>Balai Desa Trosemi, Trosemi, Gatak, Kulon, Kabupaten Sukoharjo, 57557
                    </p>
                    <p class="mb-1 text-muted">
                        <i class="fa fa-envelope mr-2 text-success"></i>balaidesatrosemi@gmail.com
                    </p>
                    <p class="mb-0 text-muted">
                        <i class="fa fa-phone mr-2 text-success"></i>(0271) 654-3210
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card:hover {
        transform: translateY(-5px);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('payrollChart');
        const ctx = canvas.getContext('2d');
        const chartData = JSON.parse(canvas.dataset.chart);

        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(52, 152, 219, 0.3)');
        gradient.addColorStop(1, 'rgba(52, 152, 219, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Total Payroll',
                    data: chartData,
                    borderColor: '#3498db',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3498db',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.parsed.y;
                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + ' juta';
                                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + ' ribu';
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            // tetap gunakan data asli untuk scale
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + ' jt';
                                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + ' rb';
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endpush