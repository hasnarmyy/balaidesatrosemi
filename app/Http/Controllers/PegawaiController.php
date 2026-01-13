<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\User;
use App\Models\Jabatan;
use App\Models\PayrollDetail;
use App\Models\Pegawai;
use App\Models\Present;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\Lembur;
use Illuminate\Support\Facades\Storage;

class PegawaiController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $konfirmasi_absen = DB::table('tb_presents')
            ->join('tb_pegawai', 'tb_pegawai.id_pegawai', '=', 'tb_presents.id_pegawai')
            ->where('tb_pegawai.id_user', $user->id)
            ->whereDate('tb_presents.tanggal', now()->toDateString())
            ->select('tb_presents.*', 'tb_pegawai.nama_pegawai as nampeg')
            ->get();

        return view('pegawai.dashboard.index', [
            'title' => 'Dashboard',
            'user' => $user,
            'konfirmasi_absen' => $konfirmasi_absen
        ]);
    }
    public function editProfil(Request $request, $id)
    {
        $nama = $request->input('nama');

        if ($request->hasFile('userfilefoto')) {
            $file = $request->file('userfilefoto');
            $path = $file->store('pegawai', 'public');

            DB::table('tb_pegawai')->where('id_user', $id)->update([
                'nama_pegawai' => $nama,
                'foto' => $path
            ]);

            DB::table('user')->where('id', $id)->update([
                'name' => $nama,
                'image' => $path
            ]);
        } else {
            DB::table('tb_pegawai')->where('id_user', $id)->update([
                'nama_pegawai' => $nama,
            ]);

            DB::table('user')->where('id', $id)->update([
                'name' => $nama,
            ]);
        }

        return redirect()->route('pegawai.index')->with('flash', 'Berhasil diperbarui');
    }

    public function editPassword(Request $request, $id)
    {
        $user = Auth::user();
        $passwordLama = $request->input('password_lama');
        $passwordBaru = $request->input('password_baru');
        $passwordBaru1 = $request->input('password_baru1');

        if (Hash::check($passwordLama, $user->password)) {
            if ($passwordBaru === $passwordBaru1) {
                DB::table('user')->where('id', $id)->update([
                    'password' => Hash::make($passwordBaru),
                ]);
                return redirect()->route('pegawai.index')->with('flash', 'Password Berhasil Diubah!');
            } else {
                return redirect()->route('pegawai.index')->with('flash', 'Konfirmasi Password Berbeda!');
            }
        } else {
            return redirect()->route('pegawai.index')->with('flash', 'Password Lama Salah!');
        }
    }

    public function absenHarian()
    {
        $user = Auth::user();

        $pegawai = DB::table('tb_pegawai')
            ->join('tb_jabatan', 'tb_jabatan.id_jabatan', '=', 'tb_pegawai.id_jabatan')
            ->where('tb_pegawai.id_user', $user->id)
            ->select('tb_pegawai.*', 'tb_jabatan.jabatan as namjab')
            ->first();

        // Cek apakah pegawai terdaftar lembur hari ini
        $cekLembur = DB::table('tb_lembur')
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->whereDate('date', now()->toDateString())
            ->first();

        // ✅ PERBAIKAN: Cek absen MASUK (keterangan = 1)
        $absenMasuk = DB::table('tb_presents')
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->whereDate('tanggal', now()->toDateString())
            ->where('keterangan', 1)
            ->first();

        // ✅ PERBAIKAN: Cek absen PULANG (keterangan = 2)
        $absenPulang = DB::table('tb_presents')
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->whereDate('tanggal', now()->toDateString())
            ->where('keterangan', 2)
            ->first();

        // ✅ PERBAIKAN: Cek absen LEMBUR (keterangan = 3)
        $absenLembur = DB::table('tb_presents')
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->whereDate('tanggal', now()->toDateString())
            ->where('keterangan', 3)
            ->first();

        // ✅ PERBAIKAN: Cek IZIN (keterangan = 4 atau 5)
        $absenIzin = DB::table('tb_presents')
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->whereDate('tanggal', now()->toDateString())
            ->whereIn('keterangan', [4, 5])
            ->first();

        // Tentukan status absen berdasarkan prioritas
        $absen = $absenIzin ?? $absenLembur ?? $absenPulang ?? $absenMasuk ?? (object)[
            'keterangan' => null,
            'id_pegawai' => 'peg',
            'status' => null,
            'id_lembur' => null,
            'id_presents' => null,
            'jam_masuk' => null,
            'jam_pulang' => null,
        ];

        return view('pegawai.absen.index', [
            'title' => 'Absen Harian',
            'user' => $user,
            'pegawai' => $pegawai,
            'cek_lembur' => $cekLembur ?? (object)['id_pegawai' => ''],
            'absen' => $absen,
            'absen_masuk' => $absenMasuk,    // ✅ tambahan
            'absen_pulang' => $absenPulang,  // ✅ tambahan
            'absen_lembur' => $absenLembur,  // ✅ tambahan
        ]);
    }

    public function ambilAbsen(Request $request)
    {
        try {
            $user = Auth::user();
            $pegawai = DB::table('tb_pegawai')->where('id_user', $user->id)->first();
            // ✅ Cek status aktif pegawai
            if (!$pegawai || $pegawai->status_kepegawaian != 1) {
                return redirect()
                    ->route('pegawai.absenHarian')
                    ->with('error', 'Anda tidak diizinkan mengambil absen karena status tidak aktif.');
            }
            
            // Cek apakah sudah absen masuk hari ini
            $cek_masuk = DB::table('tb_presents')
                ->where('id_pegawai', $pegawai->id_pegawai)
                ->whereDate('tanggal', now()->toDateString())
                ->where('keterangan', 1) // 1 = masuk
                ->first();

            if ($cek_masuk) {
                return redirect()
                    ->route('pegawai.absenHarian')
                    ->with('error', 'Anda sudah absen masuk hari ini!');
            }

            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'foto_selfie_masuk' => 'required|string',
            ]);

            $latKantor = -7.5887862;
            $longKantor = 110.7505422;
            $radiusMaks = 100; // dalam meter

            $latUser = (float) $request->input('latitude');
            $longUser = (float) $request->input('longitude');
            $jarak = $this->hitungJarak($latUser, $longUser, $latKantor, $longKantor);

            if ($jarak > $radiusMaks) {
                return redirect()
                    ->route('pegawai.absenHarian')
                    ->with('error', 'Absen gagal: Anda terlalu jauh dari lokasi kantor (' . round($jarak, 2) . ' m)');
            }

            date_default_timezone_set('Asia/Jakarta');
            $tanggalSekarang = now()->toDateString();
            $waktuSekarang = $request->input('waktu') ?? now()->toTimeString();

            $batasMasuk = strtotime('08:00:00');
            $jamSekarang = strtotime($waktuSekarang);
            $keteranganMsk = $jamSekarang <= $batasMasuk ? 0 : 1;

            $image = str_replace(['data:image/jpeg;base64,', ' '], ['', '+'], $request->input('foto_selfie_masuk'));
            $imageName = 'absen_masuk_' . $pegawai->id_pegawai . '_' . time() . '.jpg';
            $imageData = base64_decode($image);
            $path = 'gambar/absensi/' . $imageName;
            Storage::disk('public')->put($path, $imageData);

            // ✅ INSERT ABSEN MASUK SAJA (keterangan = 1)
            DB::table('tb_presents')->insert([
                'id_pegawai'        => $pegawai->id_pegawai,
                'tanggal'           => $tanggalSekarang,
                'jam_masuk'         => $waktuSekarang,
                'waktu'             => $waktuSekarang,
                'keterangan'        => 1, // ✅ MASUK
                'keterangan_msk'    => $keteranganMsk,
                'status'            => 0, // Menunggu konfirmasi
                'latitude'          => $latUser,
                'longitude'         => $longUser,
                'foto_selfie_masuk' => $path,
            ]);

            $msg = $keteranganMsk == 0 ? 'Absen Masuk Berhasil (TIDAK TELAT)' : 'Absen Masuk Berhasil (TELAT)';
            return redirect()
                ->route('pegawai.absenHarian')
                ->with('success', $msg . ' | Jarak: ' . round($jarak, 2) . ' m');
        } catch (\Exception $e) {
            return redirect()
                ->route('pegawai.absenHarian')
                ->with('error', 'Gagal menyimpan absen: ' . $e->getMessage());
        }
    }

    public function ambilAbsenPulang(Request $request)
    {
        try {
            $user = Auth::user();
            $pegawai = Pegawai::where('id_user', $user->id)->firstOrFail();
            // ✅ Cek status aktif pegawai
            if (!$pegawai || $pegawai->status_kepegawaian != 1) {
                return redirect()
                    ->route('pegawai.absenHarian')
                    ->with('error', 'Anda tidak diizinkan mengambil absen karena status tidak aktif.');
            }

            // 🔒 BATAS JAM ABSEN PULANG
            $now = Carbon::now();
            $batasJam = Carbon::createFromTime(16, 0, 0);

            if ($now->lt($batasJam)) {
                return redirect()
                    ->route('pegawai.absenHarian')
                    ->with('error', 'Absen pulang hanya dapat dilakukan setelah memenuhi jam kerja.');
            }

            // Cek lembur
            $cek_lembur_hari_ini = Lembur::where('id_pegawai', $pegawai->id_pegawai)
                ->whereDate('date', now()->toDateString())
                ->first();

            if ($cek_lembur_hari_ini) {
                return redirect()
                    ->route('pegawai.absenHarian')
                    ->with('error', 'Anda memiliki jadwal lembur hari ini. Gunakan fitur absen lembur, bukan absen pulang.');
            }

            // Cek apakah pegawai sudah absen masuk
            $cek_masuk = Present::where('id_pegawai', $pegawai->id_pegawai)
                ->whereDate('tanggal', now()->toDateString())
                ->where('keterangan', 1)
                ->first();

            if (!$cek_masuk) {
                return redirect()
                    ->route('pegawai.absenHarian')
                    ->with('error', 'Anda belum absen masuk hari ini!');
            }

            // Cek apakah sudah absen pulang
            $cek_pulang = Present::where('id_pegawai', $pegawai->id_pegawai)
                ->whereDate('tanggal', now()->toDateString())
                ->where('keterangan', 2)
                ->first();

            if ($cek_pulang) {
                return redirect()
                    ->route('pegawai.absenHarian')
                    ->with('error', 'Anda sudah absen pulang hari ini!');
            }

            // Validasi foto
            $request->validate([
                'foto_selfie_pulang' => 'required|string',
            ]);

            // Simpan foto selfie
            $image = str_replace(['data:image/jpeg;base64,', ' '], ['', '+'], $request->input('foto_selfie_pulang'));
            $imageName = 'absen_pulang_' . $pegawai->id_pegawai . '_' . time() . '.jpg';
            $imageData = base64_decode($image);
            $path = 'gambar/absensi/' . $imageName;
            Storage::disk('public')->put($path, $imageData);

            // ✅ INSERT RECORD BARU UNTUK ABSEN PULANG
            Present::create([
                'id_pegawai'         => $pegawai->id_pegawai,
                'tanggal'            => now()->toDateString(),
                'waktu'              => now()->toTimeString(),
                'jam_pulang'         => now()->toTimeString(),
                'foto_selfie_pulang' => $path,
                'keterangan'         => 2, // ✅ PULANG
                'status'             => 0, // Menunggu konfirmasi
                'latitude'           => $cek_masuk->latitude,   // 🔥
                'longitude'          => $cek_masuk->longitude,  // 🔥
            ]);

            return redirect()
                ->route('pegawai.absenHarian')
                ->with('success', 'Absen Pulang Berhasil! Menunggu konfirmasi admin.');
        } catch (\Exception $e) {
            return redirect()
                ->route('pegawai.absenHarian')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function lembur(Request $request)
    {
        try {
            $user = Auth::user();
            $pegawai = Pegawai::where('id_user', $user->id)->firstOrFail();
            // ✅ Cek status aktif pegawai
            if (!$pegawai || $pegawai->status_kepegawaian != 1) {
                return redirect()
                    ->route('pegawai.absenHarian')
                    ->with('error', 'Anda tidak diizinkan mengambil absen karena status tidak aktif.');
            }

            // 🔒 BATAS JAM ABSEN PULANG
            $now = Carbon::now();
            $batasJam = Carbon::createFromTime(16, 0, 0);

            if ($now->lt($batasJam)) {
                return redirect()
                    ->route('pegawai.absenHarian')
                    ->with('error', 'Absen lembur hanya dapat dilakukan setelah memenuhi jam kerja.');
            }

            // Cek apakah pegawai sudah absen masuk
            $cek_masuk = Present::where('id_pegawai', $pegawai->id_pegawai)
                ->whereDate('tanggal', now()->toDateString())
                ->where('keterangan', 1)
                ->first();

            if (!$cek_masuk) {
                return redirect()
                    ->route('pegawai.absenHarian')
                    ->with('error', 'Anda belum absen masuk hari ini!');
            }

            // Cek apakah pegawai terdaftar lembur hari ini
            $lembur_terdaftar = Lembur::where('id_pegawai', $pegawai->id_pegawai)
                ->whereDate('date', now()->toDateString())
                ->first();

            if (!$lembur_terdaftar) {
                return redirect()
                    ->route('pegawai.absenHarian')
                    ->with('error', 'Tidak ada data lembur hari ini. Anda tidak terdaftar lembur.');
            }

            // Validasi foto
            $request->validate([
                'foto_selfie_pulang' => 'required|string',
            ]);

            // Cek apakah sudah lembur
            $cek_lembur = Present::where('id_pegawai', $pegawai->id_pegawai)
                ->whereDate('tanggal', now()->toDateString())
                ->where('keterangan', 3)
                ->first();

            if ($cek_lembur) {
                return redirect()
                    ->route('pegawai.absenHarian')
                    ->with('error', 'Anda sudah absen lembur hari ini!');
            }

            // Simpan foto selfie lembur
            $image = str_replace(['data:image/jpeg;base64,', ' '], ['', '+'], $request->input('foto_selfie_pulang'));
            $imageName = 'absen_lembur_' . $pegawai->id_pegawai . '_' . time() . '.jpg';
            $imageData = base64_decode($image);
            $path = 'gambar/absensi/' . $imageName;
            Storage::disk('public')->put($path, $imageData);

            // ✅ INSERT RECORD BARU UNTUK ABSEN LEMBUR
            Present::create([
                'id_pegawai'         => $pegawai->id_pegawai,
                'tanggal'            => now()->toDateString(),
                'waktu'              => now()->toTimeString(),
                'jam_pulang'         => now()->toTimeString(),
                'foto_selfie_pulang' => $path,
                'keterangan'         => 3, // ✅ LEMBUR
                'id_lembur'          => $lembur_terdaftar->id_lembur,
                'status'             => 0, // Menunggu konfirmasi
                'latitude'           => $cek_masuk->latitude,   // 🔥
                'longitude'          => $cek_masuk->longitude,  // 🔥
            ]);

            return redirect()
                ->route('pegawai.absenHarian')
                ->with('success', 'Absen Lembur Berhasil! Menunggu konfirmasi admin.');
        } catch (\Exception $e) {
            return redirect()
                ->route('pegawai.absenHarian')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function konfirmasiAbsen()
    {
        $user = Auth::user();

        $konfirmasi_absen = DB::table('tb_presents')
            ->join('tb_pegawai', 'tb_pegawai.id_pegawai', '=', 'tb_presents.id_pegawai')
            ->where('tb_pegawai.id_user', $user->id)
            ->whereDate('tb_presents.tanggal', now()->toDateString())
            ->select('tb_presents.*', 'tb_pegawai.nama_pegawai as nampeg')
            ->get();

        return view('pegawai.absen.konfirmasi', [
            'title' => 'Konfirmasi Absen',
            'user' => $user,
            'konfirmasi_absen' => $konfirmasi_absen,
        ]);
    }

    public function cuti(Request $request)
    {
        $user = Auth::user();
        $pegawai = DB::table('tb_pegawai')->where('id_user', $user->id)->first();

        // ✅ Cek status aktif pegawai
        if (!$pegawai || $pegawai->status_kepegawaian != 1) {
            return redirect()
                ->route('pegawai.absenHarian')
                ->with('error', 'Anda tidak diizinkan mengambil cuti karena status tidak aktif.');
        }

        $request->validate([
            'keterangan' => 'required|in:4,5',
            'keterangan_izin' => 'required',
            'foto_selfie_masuk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $keterangan = $request->input('keterangan');
        $keterangan_izin = $request->input('keterangan_izin');
        $pathSurat = null;

        if ($keterangan == 4 && $request->hasFile('foto_selfie_masuk')) {
            $file = $request->file('foto_selfie_masuk');
            $filename = 'foto_selfie_masuk_' . $pegawai->id_pegawai . '_' . time() . '.' . $file->getClientOriginalExtension();
            $pathSurat = $file->storeAs('gambar/pegawai/suratdokter', $filename, 'public');
        }

        $waktuSekarang = now()->toTimeString();
        DB::table('tb_presents')->insert([
            'id_pegawai' => $pegawai->id_pegawai,
            'tanggal' => now()->toDateString(),
            'keterangan' => $keterangan,
            'keterangan_izin' => $keterangan_izin,
            'foto_selfie_masuk' => $pathSurat,
            'status' => 0,
            'waktu' => $waktuSekarang,
        ]);

        return redirect()
            ->route('pegawai.absenHarian')
            ->with('success', 'Izin berhasil dicatat!');
    }

    public function absenBulanan(Request $request)
    {
        $user = Auth::user();

        $pegawai = DB::table('tb_pegawai')
            ->where('id_user', $user->id)
            ->first();

        if (!$pegawai) {
            return redirect()->back()->with('error', 'Data pegawai tidak ditemukan');
        }

        // Ambil input
        $thn = $request->input('th');
        $bln = $request->input('bln');

        // VALIDASI TAHUN & BULAN
        if ($thn && !in_array($thn, $this->getTahunList())) {
            return back()->with('error', 'Tahun tidak valid');
        }

        if ($bln && ($bln < 1 || $bln > 12)) {
            return back()->with('error', 'Bulan tidak valid');
        }

        $detail_pegawai = DB::table('tb_pegawai')
            ->join('tb_jabatan', 'tb_jabatan.id_jabatan', '=', 'tb_pegawai.id_jabatan')
            ->where('tb_pegawai.id_pegawai', $pegawai->id_pegawai)
            ->select('tb_pegawai.*', 'tb_jabatan.jabatan as namjab')
            ->first();

        $absen = collect();

        if ($thn && $bln) {
            $absen = DB::table('tb_presents')
                ->where('id_pegawai', $pegawai->id_pegawai)
                ->whereYear('tanggal', $thn)
                ->whereMonth('tanggal', $bln)
                ->orderBy('tanggal', 'asc')
                ->get();
        }

        return view('pegawai.absen.bulanan', [
            'title' => 'Absen Bulanan',
            'user' => $user,
            'detail_pegawai' => $detail_pegawai,
            'absen' => $absen,
            'thn' => $thn,
            'bln' => $bln,
            'listTahun' => $this->getTahunList() // ⬅️ PENTING
        ]);
    }

    public function detail($id)
    {
        $user = Auth::user();

        $detail_absensi = DB::table('tb_presents')
            ->join('tb_pegawai', 'tb_pegawai.id_pegawai', '=', 'tb_presents.id_pegawai')
            ->join('tb_jabatan', 'tb_jabatan.id_jabatan', '=', 'tb_pegawai.id_jabatan')
            ->leftJoin('tb_lembur', 'tb_lembur.id_lembur', '=', 'tb_presents.id_lembur')
            ->where('tb_presents.id_presents', $id)
            ->where('tb_pegawai.id_user', $user->id)
            ->select(
                'tb_presents.*',
                'tb_pegawai.nama_pegawai',
                'tb_jabatan.jabatan as namjab', // <-- diperbaiki
                'tb_lembur.waktu_lembur'
            )
            ->first();

        if (!$detail_absensi) {
            return redirect()->route('pegawai.absen-bulanan')->with('error', 'Data absensi tidak ditemukan');
        }

        return view('pegawai.absen.show', [
            'title' => 'Detail Absensi',
            'user' => $user,
            'detail_absensi' => $detail_absensi,
        ]);
    }

    public function laporanTppBulanan(Request $request)
    {
        $user = Auth::user();
        $pegawai = Pegawai::where('id_user', $user->id)->first();

        $bulan = $request->input('bln');
        $tahun = $request->input('th');

        $data = [
            'title' => 'Cetak Gaji Bulanan',
            'user' => $user,
            'pegawai' => Pegawai::all(),
            'bulanTersedia' => $this->getBulanList(),
            'tahunTersedia' => $this->getTahunList(),
            'blnselected' => $bulan,
            'thnselected' => $tahun,
            'gaji' => null,
        ];

        if ($bulan && $tahun && $pegawai) {

            $payroll = Payroll::with('details')
                ->where('id_pegawai', $pegawai->id_pegawai)
                ->whereMonth('periode', $bulan)
                ->whereYear('periode', $tahun)
                ->first();

            if ($payroll && $payroll->details->count() > 0) {

                $detail = $payroll->details->first();

                $data['gaji'] = [
                    'id_pegawai'     => $pegawai->id_pegawai,
                    'nama_pegawai'   => $pegawai->nama_pegawai,
                    'jabatan'        => $pegawai->relasiJabatan->jabatan ?? '-',
                    'gaji_pokok'     => $detail->gaji_pokok,
                    'gaji_lembur'    => $detail->gaji_lembur,
                    'bonus'          => $detail->bonus,
                    'keterangan'     => $payroll->keterangan ?? '-',
                    'potongan_absen' => $detail->potongan_absen,
                    'gaji_bersih'    => $payroll->gaji_bersih,
                ];
            }
        }

        return view('pegawai.laporan.index', $data);
    }

    public function detailLaporanTppBulanan($id_pegawai, $bln, $thn)
    {
        $user = Auth::user();

        // 🔒 pastikan hanya data pegawai login
        $pegawai = Pegawai::with('relasiJabatan')
            ->where('id_user', $user->id)
            ->where('id_pegawai', $id_pegawai)
            ->firstOrFail();

        $jabatan = $pegawai->relasiJabatan;

        $tanggalAwal  = Carbon::create($thn, $bln, 1)->startOfMonth();
        $tanggalAkhir = Carbon::create($thn, $bln, 1)->endOfMonth();

        $payroll = Payroll::with('details')
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->whereMonth('periode', $bln)
            ->whereYear('periode', $thn)
            ->first();

        $gaji_pokok  = 0;
        $gaji_lembur = 0;
        $bonus       = 0;
        $potongan_absen = 0;
        $keterangan = '-';

        if ($payroll && $payroll->details->count()) {
            $gaji_pokok     = $payroll->details->sum('gaji_pokok');
            $gaji_lembur    = $payroll->details->sum('gaji_lembur');
            $bonus          = $payroll->details->sum('bonus');
            $potongan_absen = $payroll->details->sum('potongan_absen');
        }

        if ($payroll) {
            $keterangan = $payroll->keterangan ?? '-';
        }

        // ================= ABSENSI (SUDAH DIKONFIRMASI) =================
        $dataAbsen = Present::where('id_pegawai', $id_pegawai)
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
            ->whereIn('keterangan', [1, 2, 3])
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->tanggal)->toDateString();
            });

        $masuk  = 0;
        $lembur = 0;

        foreach ($dataAbsen as $items) {

            $masukValid  = $items->where('keterangan', 1)->where('status', 1)->count() > 0;
            $pulangValid = $items->where('keterangan', 2)->where('status', 2)->count() > 0;
            $lemburValid = $items->where('keterangan', 3)->where('status', 3)->count() > 0;

            if ($masukValid && $pulangValid) {
                $masuk++;
            }

            if ($masukValid && $lemburValid) {
                $lembur++;
            }
        }

        $sakit = Present::where('id_pegawai', $id_pegawai)
            ->where('keterangan', 4)
            ->where('status', 4)
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
            ->count();

        $izin = Present::where('id_pegawai', $id_pegawai)
            ->where('keterangan', 5)
            ->where('status', 5)
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
            ->count();

        return view('pegawai.laporan.show', [
            'title' => 'Detail Laporan Gaji Bulanan',
            'user'  => $user,
            'pegawai' => $pegawai,
            'blnselected' => $bln,
            'thnselected' => $thn,
            'gaji' => [
                'gaji_pokok'     => $gaji_pokok,
                'gaji_lembur'    => $gaji_lembur,
                'bonus'          => $bonus,
                'potongan_absen' => $potongan_absen,
                'keterangan'     => $keterangan,
                'gaji_bersih'    => ($gaji_pokok + $gaji_lembur + $bonus) - $potongan_absen,
            ],
            'absen' => [
                'masuk'  => $masuk,
                'jumlem' => $lembur,
                'sakit'  => $sakit,
                'izin'   => $izin,
            ],
        ]);
    }

    public function cetakPayrolPegawai($id_pegawai, $bulan, $tahun)
    {
        // Ambil payroll berdasarkan BULAN & TAHUN
        $payroll = Payroll::with(['pegawai', 'jabatan', 'details'])
            ->where('id_pegawai', $id_pegawai)
            ->whereMonth('periode', $bulan)
            ->whereYear('periode', $tahun)
            ->firstOrFail();

        $detail = $payroll->details->first(); // 1 payroll = 1 detail

        // Data gaji FINAL (bukan hitung ulang)
        $gaji = [
            'id_pegawai'     => $payroll->pegawai->id_pegawai,
            'gaji_pokok'     => $detail->gaji_pokok,
            'gaji_lembur'    => $detail->gaji_lembur,
            'bonus'          => $detail->bonus,
            'potongan_absen' => $detail->potongan_absen,
            'gaji_bersih'    => $payroll->gaji_bersih,
            'keterangan'     => $payroll->keterangan,
        ];

        // Hitung data absen
        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate   = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $dataAbsen = Present::where('id_pegawai', $id_pegawai)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->whereIn('keterangan', [1, 2, 3])
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->tanggal)->toDateString();
            });

        $masuk  = 0;
        $lembur = 0;

        foreach ($dataAbsen as $items) {

            $masukValid  = $items->where('keterangan', 1)->where('status', 1)->count() > 0;
            $pulangValid = $items->where('keterangan', 2)->where('status', 2)->count() > 0;
            $lemburValid = $items->where('keterangan', 3)->where('status', 3)->count() > 0;

            if ($masukValid && $pulangValid) {
                $masuk++;
            }

            if ($masukValid && $lemburValid) {
                $lembur++;
            }
        }

        $absen = [
            'masuk'  => $masuk,
            'jumlem' => $lembur,
            'sakit'  => Present::where('id_pegawai', $id_pegawai)
                ->where('keterangan', 4)
                ->where('status', 4)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->count(),
            'izin'   => Present::where('id_pegawai', $id_pegawai)
                ->where('keterangan', 5)
                ->where('status', 5)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->count(),
        ];

        return view('pegawai.laporan.cetak', [
            'payroll' => $payroll,
            'gaji' => $gaji,
            'absen' => $absen,
            'blnselected' => $bulan,
            'thnselected' => $tahun,
        ]);
    }

    private function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    // Helper untuk list bulan
    private function getBulanList()
    {
        return [
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
    }

    // Helper untuk list tahun
    private function getTahunList()
    {
        return range(2024, 2028);
    }
}
