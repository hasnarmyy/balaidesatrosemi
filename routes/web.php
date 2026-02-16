<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\FaceEnrollmentController;
use App\Http\Controllers\Api\FaceApiController;


Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
    Route::post('/pegawai/edit-profil/{id}', [PegawaiController::class, 'editProfil'])->name('pegawai.editProfil');
    Route::post('/pegawai/edit-password/{id}', [PegawaiController::class, 'editPassword'])->name('pegawai.editPassword');

    Route::get('/pegawai/absen', [PegawaiController::class, 'absenHarian'])->name('pegawai.absenHarian');
    Route::post('/pegawai/ambil-absen', [PegawaiController::class, 'ambilAbsen'])->name('pegawai.ambilAbsen');
    Route::post('/pegawai/ambil-absen-pulang', [PegawaiController::class, 'ambilAbsenPulang'])->name('pegawai.ambilAbsenPulang');
    Route::post('/pegawai/ambil-absen-lembur', [PegawaiController::class, 'lembur'])->name('pegawai.ambilAbsenLembur');

    Route::get('/pegawai/konfirmasi-absen', [PegawaiController::class, 'konfirmasiAbsen'])->name('pegawai.konfirmasiAbsen');

    // Face enrollment
    Route::get('/pegawai/enroll-face', [FaceEnrollmentController::class, 'showForm'])->name('pegawai.enrollFace');
    Route::post('/pegawai/enroll-face', [FaceEnrollmentController::class, 'enroll'])->name('pegawai.enrollFace.post');
    Route::post('/pegawai/enroll-face/delete/{id}', [FaceEnrollmentController::class, 'delete'])->name('pegawai.enrollFace.delete');

    // Face verification API
    Route::post('/api/face/verify', [FaceApiController::class, 'verify'])->name('api.face.verify');
    Route::get('/api/face/samples-count', [FaceApiController::class, 'getSamplesCount'])->name('api.face.samples-count');

    Route::post('/pegawai/lembur', [PegawaiController::class, 'lembur'])->name('pegawai.lembur');
    Route::post('/pegawai/cuti', [PegawaiController::class, 'cuti'])->name('pegawai.cuti');

    Route::match(['get', 'post'], '/absen-bulanan', [PegawaiController::class, 'absenBulanan'])->name('pegawai.absen-bulanan');
    Route::get('/detail-absen/{id}', [PegawaiController::class, 'detail'])->name('pegawai.detail-absen');

    Route::match(['get', 'post'], '/pegawai/laporan-tpp', [PegawaiController::class, 'laporanTppBulanan'])->name('pegawai.laporan-tpp-bulanan');
    Route::get('/pegawai/detail/{id_pegawai}/{bln}/{thn}', [PegawaiController::class, 'detailLaporanTppBulanan'])->name('pegawai.detail-laporan-tpp');
    Route::get('/pegawai/cetak/{id_pegawai}/{bln}/{thn}', [PegawaiController::class, 'cetakPayrolPegawai'])->name('pegawai.laporan-tpp.cetak');
});

Route::middleware('auth', 'admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');

    Route::get('/jabatan', [AdminController::class, 'jabatan'])->name('jabatan');
    Route::post('/jabatan/tambah', [AdminController::class, 'tambahJabatan'])->name('jabatan.tambah');
    Route::post('/jabatan/edit', [AdminController::class, 'editJabatan'])->name('jabatan.edit');
    Route::get('/jabatan/hapus/{id}', [AdminController::class, 'hapusJabatan'])->name('jabatan.hapus');

    Route::get('/pegawai', [AdminController::class, 'pegawai'])->name('pegawai');
    Route::get('/pegawai/detail/{id}', [AdminController::class, 'detailPegawai'])->name('pegawai.detail');
    Route::post('/pegawai/tambah', [AdminController::class, 'tambahPegawai'])->name('pegawai.tambah');
    Route::put('/pegawai/{id}/edit', [AdminController::class, 'editPegawai'])->name('pegawai.edit');
    Route::delete('/pegawai/hapus/{id}/{id_user}', [AdminController::class, 'hapusPegawai'])->name('pegawai.hapus');

    // Office/Kantor Management
    Route::get('/kantor', [AdminController::class, 'kantor'])->name('kantor');
    Route::post('/kantor/tambah', [AdminController::class, 'tambahKantor'])->name('kantor.tambah');
    Route::put('/kantor/{id}/edit', [AdminController::class, 'editKantor'])->name('kantor.edit');
    Route::delete('/kantor/{id}', [AdminController::class, 'hapusKantor'])->name('kantor.hapus');
    Route::post('/kantor/{id}/setActive', [AdminController::class, 'setActiveKantor'])->name('kantor.setActive');

    Route::get('/tambah-lembur', [AdminController::class, 'lembur_pegawai'])->name('tambah-lembur');
    Route::post('/simpan-lembur', [AdminController::class, 'simpan_lembur_pegawai'])->name('lembur.store');
    Route::put('/edit-lembur', [AdminController::class, 'edit_lembur_pegawai'])->name('lembur.update');
    Route::delete('/hapus-lembur/{id}', [AdminController::class, 'hapus_lembur_pegawai'])->name('lembur.destroy');

    Route::get('/tampil-konfirmasi', [AdminController::class, 'tampil_konfirmasi'])->name('tampil-konfirmasi');
    Route::post('/konfirmasi-absen/{id}', [AdminController::class, 'konfirmasi_absen'])->name('konfirmasi.absen');
    Route::post('/konfirmasi-absen-pulang/{id}', [AdminController::class, 'konfirmasi_absen_pulang'])->name('konfirmasi.absen.pulang');
    Route::post('/konfirmasi-absen-lembur/{id_presents}/{id_pegawai}', [AdminController::class, 'konfirmasi_absen_lembur'])->name('konfirmasi.absen.lembur');
    Route::post('/konfirmasi-absen-izin-sakit/{id}', [AdminController::class, 'konfirmasi_absen_izin_sakit'])->name('konfirmasi.absen.sakit');
    Route::post('/konfirmasi-absen-izin-tdkmsk/{id}', [AdminController::class, 'konfirmasi_absen_izin_tdkmsk'])->name('konfirmasi.absen.tidak_masuk');


    Route::get('/akun-pegawai', [AdminController::class, 'akun_pegawai'])->name('akun-pegawai');
    Route::post('/reset-password/{id}', [AdminController::class, 'reset_password'])->name('reset.password');
    Route::match(['get', 'post'], '/absen-bulanan', [AdminController::class, 'absen_bulanan'])->name('absen-bulanan');
    Route::get('/cetak-absen-bulanan/{thn}/{bln}/{idpeg}', [AdminController::class, 'cetak_absen_bulanan'])->name('cetak.absen.bulanan');
    Route::get('/detail-absen/{id}', [AdminController::class, 'detail_absen'])->name('detail.absen');

    Route::get('/lembur-bulanan', [AdminController::class, 'lembur_bulanan'])->name('lembur-bulanan');
    Route::post('/lembur-bulanan', [AdminController::class, 'lembur_bulanan'])->name('lembur-bulanan.filter');
    Route::get('/cetak-absen-lembur/{thn}/{bln}', [AdminController::class, 'cetak_absen_lembur'])->name('cetak.absen.lembur');

    Route::match(['get', 'post'], '/tpp-bulanan', [AdminController::class, 'tpp_bulanan'])->name('tpp-bulanan');
    Route::post('/akumulasi-gaji', [AdminController::class, 'akumulasi_gaji'])->name('akumulasi.gaji');
    Route::post('/simpan-gaji', [AdminController::class, 'simpan_gaji'])->name('gaji.store');
    Route::put('/edit-gaji', [AdminController::class, 'edit_gaji'])->name('gaji.update');
    Route::put('/refresh-gaji', [AdminController::class, 'edit_gaji'])->name('gaji.refresh');
    Route::delete('/hapus-gaji/{id}', [AdminController::class, 'hapus_gaji'])->name('gaji.destroy');

    Route::match(['get', 'post'], '/laporan-tpp-bulanan', [AdminController::class, 'laporan_tpp_bulanan'])->name('laporan-tpp-bulanan');
    Route::get('/detail-laporan-tpp-bulanan/{id_pegawai}/{bln}/{thn}', [AdminController::class, 'detail_laporan_tpp_bulanan'])->name('detail.laporan.tpp');
    Route::get('/cetak-payrol-pegawai/{id_pegawai}/{bln}/{thn}', [AdminController::class, 'cetak_payrol_pegawai'])->name('cetak.payrol.pegawai');
});
