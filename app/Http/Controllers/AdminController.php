<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

use App\Models\Pegawai;
use App\Models\User;
use App\Models\Jabatan;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\Present;
use App\Models\Lembur;
use App\Models\Office;

class AdminController extends Controller
{

    public function index()
    {
        $totalPegawaiAktif = Pegawai::where('status_kepegawaian', 1)->count();

        $totalKehadiranHariIni = Present::whereDate('tanggal', Carbon::today())
            ->whereIn('keterangan', [1, 2, 3]) // Masuk, Pulang, Lembur yang sudah dikonfirmasi
            ->where('status', '>=', 1)
            ->count();

        $totalLemburBulanIni = Lembur::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->count();

        $totalPayrollBulanIni = Payroll::whereMonth('periode', Carbon::now()->month)
            ->whereYear('periode', Carbon::now()->year)
            ->sum('gaji_bersih');

        // ✅ HITUNG RECORD PENDING YANG BENAR
        $pelaporanPending = Present::whereDate('tanggal', Carbon::today())
            ->where('status', 0) // Status 0 = belum dikonfirmasi
            ->count();

        $totalUser = User::where('is_active', 1)->count();

        $payrollPerBulan = Payroll::selectRaw('MONTH(periode) as bulan, SUM(gaji_bersih) as total')
            ->whereYear('periode', Carbon::now()->year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $chartData = array_fill(1, 12, 0);
        foreach ($payrollPerBulan as $item) {
            $chartData[$item->bulan] = $item->total;
        }

        return view('admin.dashboard.index', [
            'title' => 'Dashboard',
            'user' => Auth::user(),
            'totalPegawaiAktif' => $totalPegawaiAktif,
            'totalKehadiranHariIni' => $totalKehadiranHariIni,
            'totalLemburBulanIni' => $totalLemburBulanIni,
            'totalPayrollBulanIni' => $totalPayrollBulanIni,
            'pelaporanPending' => $pelaporanPending,
            'totalUser' => $totalUser,
            'chartData' => array_values($chartData),
        ]);
    }

    public function jabatan()
    {
        $data['title'] = 'Data Jabatan';
        $data['user'] = Auth::user();
        $data['jabatan'] = DB::table('tb_jabatan')
            ->orderByDesc('id_jabatan')
            ->get();
        return view('admin.jabatan.index', $data);
    }

    public function tambahJabatan(Request $request)
    {
        DB::table('tb_jabatan')->insert([
            'jabatan' => $request->jabatan,
            'salary' => $request->salary,
            'overtime' => $request->overtime,
        ]);
        Session::flash('flash', 'Berhasil ditambah');
        return redirect()->route('admin.jabatan');
    }

    public function editJabatan(Request $request)
    {
        $jabatan = Jabatan::findOrFail($request->id_jabatan);
        $jabatan->update([
            'jabatan' => $request->jabatan,
            'salary' => $request->salary,
            'overtime' => $request->overtime,
        ]);
        Session::flash('flash', 'Berhasil Diperbarui');
        return redirect()->route('admin.jabatan');
    }

    public function hapusJabatan($id)
    {
        DB::table('tb_jabatan')->where('id_jabatan', $id)->delete();
        Session::flash('flash', 'Berhasil Dihapus');
        return redirect()->route('admin.jabatan');
    }

    // ========================
    // OFFICE/KANTOR MANAGEMENT
    // ========================

    public function kantor()
    {
        $data = [
            'title'  => 'Data Kantor',
            'user'   => Auth::user(),
            'kantor' => Office::orderByDesc('id_office')->get(),
        ];
        return view('admin.kantor.index', $data);
    }

    public function tambahKantor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meters' => 'required|integer|min:1',
        ]);

        // Jika ada office aktif sebelumnya, set ke inactive
        if ($request->has('active') && $request->active == 'on') {
            Office::where('active', true)->update(['active' => false]);
        }

        Office::create([
            'name' => $request->name,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius_meters' => $request->radius_meters,
            'active' => $request->has('active'),
        ]);

        Session::flash('flash', 'Kantor berhasil ditambahkan');
        return redirect()->route('admin.kantor');
    }

    public function editKantor(Request $request, $id)
    {
        $office = Office::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meters' => 'required|integer|min:1',
        ]);

        // Jika ada office aktif dan yang diupdate jadi aktif, set yang lain jadi inactive
        if ($request->has('active') && $request->active == 'on') {
            Office::where('id_office', '!=', $id)->update(['active' => false]);
        }

        $office->update([
            'name' => $request->name,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius_meters' => $request->radius_meters,
            'active' => $request->has('active'),
        ]);

        Session::flash('flash', 'Kantor berhasil diperbarui');
        return redirect()->route('admin.kantor');
    }

    public function hapusKantor($id)
    {
        $office = Office::findOrFail($id);

        // Jangan biarkan delete jika kantor aktif
        if ($office->active) {
            Session::flash('error', 'Tidak bisa menghapus kantor yang sedang aktif');
            return redirect()->route('admin.kantor');
        }

        $office->delete();
        Session::flash('flash', 'Kantor berhasil dihapus');
        return redirect()->route('admin.kantor');
    }

    public function setActiveKantor($id)
    {
        $office = Office::findOrFail($id);

        // Set semua kantor non-aktif
        Office::where('active', true)->update(['active' => false]);

        // Set kantor ini sebagai aktif
        $office->update(['active' => true]);

        Session::flash('flash', 'Kantor ' . $office->name . ' kini aktif');
        return redirect()->route('admin.kantor');
    }

    public function pegawai()
    {
        $lastIdUser = User::max('id') ?? 0;
        $nextIdUser = $lastIdUser + 1;

        $data = [
            'title'         => 'Data Pegawai',
            'user'          => Auth::user(),
            'pegawai'       => Pegawai::with('relasiJabatan')
                ->orderBy('status_kepegawaian', 'asc') // 0 (Tidak Aktif) di atas
                ->orderByDesc('id_pegawai')
                ->get(),
            'jabatan'       => Jabatan::all(),
            'jekel'         => ['L', 'P'],
            'stapeg'        => [1, 0],
            'agama'         => ['Islam', 'Protestan', 'Katolik', 'Hindu', 'Budha', 'Khonghucu'],

            'nextIdUser'    => $nextIdUser,
            'nextKodeUser'  => 'U-' . str_pad($nextIdUser, 3, '0', STR_PAD_LEFT),
            'nextIdPegawai' => 'P-' . str_pad($nextIdUser, 3, '0', STR_PAD_LEFT),
        ];

        return view('admin.pegawai.index', $data);
    }

    public function detailPegawai($id)
    {
        $pegawai = Pegawai::with('relasiJabatan')->findOrFail($id);

        return view('admin.pegawai.show', [
            'title' => 'Detail Pegawai',
            'user' => Auth::user(),
            'detail_pegawai' => $pegawai
        ]);
    }

    public function tambahPegawai(Request $request)
    {
        $request->merge([
            'email' => strtolower($request->email)
        ]);

        $validated = $request->validate([
            'nama_pegawai' => 'required|string|max:255',
            'jekel' => 'required|in:L,P',
            'email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/',
                'unique:user,email'
            ],
            'pendidikan' => 'required|string|max:255',
            'status_pegawai' => 'required|boolean',
            'agama' => 'required|string',
            'id_jabatan' => 'required|exists:tb_jabatan,id_jabatan',
            'nohp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'tgl_msk' => 'required|date',
            'userfilefoto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'userfilektp' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.regex' => 'Email harus menggunakan domain @gmail.com',
            'email.unique' => 'Email sudah terdaftar',
        ]);
        Log::info("ID Jabatan:", [$validated['id_jabatan']]);
        Log::info("Data Pegawai:", $validated);

        // ✅ AUTO LOWERCASE EMAIL
        $email = strtolower($validated['email']);
        DB::beginTransaction();

        try {
            // Upload file
            $fotoPath = $request->file('userfilefoto')->store('pegawai/foto', 'public');
            $ktpPath  = $request->file('userfilektp')->store('pegawai/ktp',  'public');

            // Ambil next ID user (auto increment)
            $lastId = User::max('id') ?? 0;
            $nextId = $lastId + 1;
            $kodeUser = 'U-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

            // Insert user
            $user = User::create([
                'kode'    => $kodeUser,
                'name'         => $validated['nama_pegawai'],
                'email'        => $validated['email'],
                'image'        => $fotoPath,
                'password'     => Hash::make('anggota'),
                'role_id'      => 2,
                'is_active'    => 1,
                'date_created' => time()
            ]);

            // Generate kode pegawai
            $kodePegawai = 'P-' . str_pad($user->id, 3, '0', STR_PAD_LEFT);

            Pegawai::create([
                'id_pegawai'        => $kodePegawai,
                'id_user'           => $user->id,
                'id_jabatan'        => $validated['id_jabatan'],
                'nama_pegawai'      => $validated['nama_pegawai'],
                'jekel'             => $validated['jekel'],
                'pendidikan'        => $validated['pendidikan'],
                'status_kepegawaian' => $validated['status_pegawai'],
                'agama'             => $validated['agama'],
                'no_hp'             => $validated['nohp'],
                'alamat'            => $validated['alamat'],
                'tanggal_masuk'     => $validated['tgl_msk'],
                'foto'              => $fotoPath,
                'ktp'               => $ktpPath,
            ]);

            DB::commit();

            return redirect()->route('admin.pegawai')
                ->with('success', 'Pegawai berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Tambah Pegawai Error: " . $e->getMessage());
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function editPegawai(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_pegawai'   => 'required|string|max:255',
            'jekel'          => 'required|in:L,P',
            'pendidikan'     => 'required|string|max:255',
            'status_pegawai' => 'required|boolean',
            'agama'          => 'required|string',
            'id_jabatan'      => 'required|exists:tb_jabatan,id_jabatan',
            'nohp'           => 'required|string|max:20',
            'alamat'         => 'required|string',
            'tgl_msk'        => 'required|date',
            'userfilefoto'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'userfilektp'    => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $pegawai = Pegawai::where('id_pegawai', $id)->firstOrFail();
        $user = User::findOrFail($pegawai->id_user);

        DB::beginTransaction();

        try {
            $updatePegawai = [
                'nama_pegawai'      => $validated['nama_pegawai'],
                'jekel'             => $validated['jekel'],
                'pendidikan'        => $validated['pendidikan'],
                'status_kepegawaian' => $validated['status_pegawai'],
                'agama'             => $validated['agama'],
                'id_jabatan'           => $validated['id_jabatan'],
                'no_hp'             => $validated['nohp'],
                'alamat'            => $validated['alamat'],
                'tanggal_masuk'     => $validated['tgl_msk'],
            ];

            $updateUser = [
                'name'      => $validated['nama_pegawai'],
                'is_active' => $validated['status_pegawai'],
            ];

            if ($request->hasFile('userfilefoto')) {
                Storage::disk('public')->delete($pegawai->foto);

                $pathFoto = $request->file('userfilefoto')->store('pegawai/foto', 'public');

                $updatePegawai['foto'] = $pathFoto;
                $updateUser['image'] = $pathFoto;
            }

            if ($request->hasFile('userfilektp')) {
                Storage::disk('public')->delete($pegawai->ktp);

                $updatePegawai['ktp'] = $request->file('userfilektp')->store('pegawai/ktp', 'public');
            }

            $pegawai->update($updatePegawai);
            $user->update($updateUser);

            DB::commit();

            return redirect()->route('admin.pegawai')
                ->with('success', 'Pegawai berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function hapusPegawai($id)
    {
        $pegawai = Pegawai::where('id_pegawai', $id)->firstOrFail();
        $user = User::find($pegawai->id_user);

        DB::beginTransaction();

        try {
            if ($pegawai->foto) Storage::disk('public')->delete($pegawai->foto);
            if ($pegawai->ktp)  Storage::disk('public')->delete($pegawai->ktp);

            $pegawai->delete();

            if ($user) {
                $user->delete();
            }

            DB::commit();

            return redirect()->route('admin.pegawai')
                ->with('success', 'Pegawai berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function akun_pegawai()
    {
        $akun = User::with('pegawai')
            ->where('role_id', 2)
            ->orderBy('is_active', 'asc')
            ->orderByDesc('id')
            ->get();

        return view('admin.akun.index', [
            'title' => 'Data Akun',
            'user' => Auth::user(),
            'akun' => $akun
        ]);
    }

    public function reset_password($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'password' => Hash::make('anggota')
        ]);

        return redirect()->route('admin.akun-pegawai')
            ->with('success', 'Password berhasil direset');
    }

    public function lembur_pegawai()
    {
        // Ambil semua lembur hari ini
        $lemburbydate = Lembur::with('pegawai')
            ->whereDate('date', Carbon::today())
            ->orderByDesc('id_lembur') // ⬅️ KUNCI: Urutkan berdasarkan ID lembur
            ->get();

        $pegawai = Pegawai::where('status_kepegawaian', 1)->get();

        return view('admin.lembur.index', [
            'title' => 'Lembur Hari Ini',
            'user' => Auth::user(),
            'pegawai' => $pegawai,
            'lemburbydate' => $lemburbydate
        ]);
    }

    public function simpan_lembur_pegawai(Request $request)
    {
        $validated = $request->validate([
            'id_pegawai' => 'required|exists:tb_pegawai,id_pegawai',
            'time' => 'required|date_format:H:i'
        ]);

        $pakaiBatasJamLembur = false; // 🔴 ubah ke true jika ingin aktifkan aturan jam 16.00

        if ($pakaiBatasJamLembur) {
            if (Carbon::createFromFormat('H:i', $validated['time'])->hour < 16) {
                return back()->with('error', 'Lembur hanya boleh dimulai setelah jam 16.00');
            }
        }

        Lembur::create([
            'id_pegawai' => $validated['id_pegawai'],
            'date' => Carbon::today(),
            'waktu_lembur' => Carbon::createFromFormat('H:i', $validated['time'])->format('H:i:s'),
            'status' => 1
        ]);

        return redirect()->route('admin.tambah-lembur')
            ->with('success', 'Data Lembur berhasil ditambah');
    }

    public function edit_lembur_pegawai(Request $request)
    {
        $validated = $request->validate([
            'id_lembur' => 'required|exists:tb_lembur,id_lembur',
            'id_pegawai' => 'required|exists:tb_pegawai,id_pegawai',
            'time' => 'required|date_format:H:i'
        ]);

        // OPSI ATURAN JAM LEMBUR
        $pakaiBatasJamLembur = false; // ubah true jika dibutuhkan

        if ($pakaiBatasJamLembur) {
            if (Carbon::createFromFormat('H:i', $validated['time'])->hour < 16) {
                return back()->with('error', 'Lembur hanya boleh dimulai setelah jam 16.00');
            }
        }

        $lembur = Lembur::findOrFail($validated['id_lembur']);
        $lembur->update([
            'id_pegawai' => $validated['id_pegawai'],
            'date' => Carbon::today(),
            'waktu_lembur' => Carbon::createFromFormat('H:i', $validated['time'])->format('H:i:s')
        ]);

        return redirect()->route('admin.tambah-lembur')
            ->with('success', 'Data Lembur berhasil diperbarui');
    }

    public function hapus_lembur_pegawai($id)
    {
        $lembur = Lembur::findOrFail($id);
        $lembur->delete();

        return redirect()->route('admin.tambah-lembur')
            ->with('success', 'Data berhasil dihapus');
    }

    public function tampil_konfirmasi()
    {
        // Ambil SEMUA data absen hari ini, urutkan berdasarkan waktu
        $konfirmasi = Present::with('pegawai')
            ->whereDate('tanggal', Carbon::today())
            ->orderByDesc('waktu')
            ->get();

        return view('admin.konfirmasi.index', [
            'title' => 'Konfirmasi Absen Hari Ini',
            'user' => Auth::user(),
            'konfirmasi' => $konfirmasi
        ]);
    }

    public function konfirmasi_absen($id)
    {
        $present = Present::findOrFail($id);

        if ($present->keterangan != 1) {
            return back()->with('error', 'Data ini bukan absen masuk!');
        }

        if ($present->status >= 1) {
            return back()->with('info', 'Absen masuk sudah terkonfirmasi sebelumnya');
        }

        // Hitung telat
        $jamMasuk = Carbon::parse($present->jam_masuk);
        $telat = $jamMasuk->gt(Carbon::createFromTimeString('08:00:00')) ? 1 : 0;

        $present->update([
            'status' => 1, // ✅ Konfirmasi masuk
            'keterangan_msk' => $telat
        ]);

        return redirect()->route('admin.tampil-konfirmasi')
            ->with('success', 'Absen Masuk berhasil dikonfirmasi');
    }

    public function konfirmasi_absen_pulang($id)
    {
        $present = Present::findOrFail($id);

        if ($present->keterangan != 2) {
            return back()->with('error', 'Data ini bukan absen pulang!');
        }

        if ($present->status >= 1) {
            return back()->with('info', 'Absen pulang sudah terkonfirmasi sebelumnya');
        }

        // ✅ KONFIRMASI PULANG
        $present->update(['status' => 2]);

        return redirect()->route('admin.tampil-konfirmasi')
            ->with('success', 'Absen Pulang berhasil dikonfirmasi');
    }

    public function konfirmasi_absen_lembur($id, $id_pegawai)
    {
        $present = Present::findOrFail($id);

        if ($present->keterangan != 3) {
            return back()->with('error', 'Data ini bukan absen lembur!');
        }

        if ($present->status >= 1) {
            return back()->with('info', 'Absen lembur sudah terkonfirmasi sebelumnya');
        }

        $lembur = Lembur::where('id_pegawai', $id_pegawai)
            ->whereDate('date', Carbon::parse($present->tanggal))
            ->first();

        if (!$lembur) {
            return redirect()->route('admin.tampil-konfirmasi')
                ->with('error', 'Data lembur tidak ditemukan. Harap tambahkan data lembur terlebih dahulu.');
        }

        // ✅ KONFIRMASI LEMBUR
        $present->update([
            'status' => 3,
            'id_lembur' => $lembur->id_lembur
        ]);

        return redirect()->route('admin.tampil-konfirmasi')
            ->with('success', 'Absen Lembur berhasil dikonfirmasi');
    }

    public function konfirmasi_absen_izin_sakit($id)
    {
        $present = Present::findOrFail($id);

        if ($present->keterangan != 4) {
            return back()->with('error', 'Data ini bukan izin sakit!');
        }

        // Cek apakah sudah terkonfirmasi
        if ($present->status == 4) {
            return back()->with('info', 'Izin sakit sudah terkonfirmasi sebelumnya');
        }

        $present->update(['status' => 4]);

        return redirect()->route('admin.tampil-konfirmasi')
            ->with('success', 'Izin sakit berhasil dikonfirmasi');
    }

    public function konfirmasi_absen_izin_tdkmsk($id)
    {
        $present = Present::findOrFail($id);

        if ($present->keterangan != 5) {
            return back()->with('error', 'Data ini bukan izin tidak masuk!');
        }

        // Cek apakah sudah terkonfirmasi
        if ($present->status == 5) {
            return back()->with('info', 'Izin tidak masuk sudah terkonfirmasi sebelumnya');
        }

        $present->update(['status' => 5]);

        return redirect()->route('admin.tampil-konfirmasi')
            ->with('success', 'Izin tidak masuk berhasil dikonfirmasi');
    }

    public function absen_bulanan(Request $request)
    {
        $tahun = $request->post('th');
        $bulan = $request->post('bln');
        $id_pegawai = $request->post('id_peg');

        $pegawai = Pegawai::all();
        $detailPegawai = null;
        $absen = collect();

        if ($id_pegawai && $tahun && $bulan) {
            $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
            $endDate = Carbon::create($tahun, $bulan, 1)->endOfMonth();

            $detailPegawai = Pegawai::with('relasiJabatan')->find($id_pegawai);

            $absen = Present::where('id_pegawai', $id_pegawai)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->get();
        }

        return view('admin.absenbulanan.index', [
            'title' => 'Absen Bulanan',
            'user' => Auth::user(),
            'list_th' => range(2025, 2030),
            'list_bln' => range(1, 12),
            'pegawai' => $pegawai,
            'detail_pegawai' => $detailPegawai ?? [],
            'absen' => $absen,
            'blnselected' => $bulan,
            'thnselected' => $tahun,
            'blnnya' => $bulan,
            'thn' => $tahun,
            'has_filter' => !empty($bulan) && !empty($tahun)
        ]);
    }

    public function cetak_absen_bulanan($tahun, $bulan, $idpeg)
    {
        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $detailPegawai = Pegawai::with('relasiJabatan')->findOrFail($idpeg);
        $absen = Present::where('id_pegawai', $idpeg)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        return view('admin.absenbulanan.cetak', [
            'detail_pegawai' => $detailPegawai,
            'absen' => $absen,
            'blnselected' => $bulan,
            'thnselected' => $tahun,
            'blnnya' => $bulan,
            'thn' => $tahun
        ]);
    }

    public function lembur_bulanan(Request $request)
    {
        $user = Auth::user();

        $tahun = $request->post('th');
        $bulan = $request->post('bln');

        $list_th  = $this->getTahunList();
        $list_bln = range(1, 12);

        $absen = [];
        $blnselected = null;
        $thnselected = null;

        if ($tahun && $bulan) {
            $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
            $endDate   = Carbon::create($tahun, $bulan, 1)->endOfMonth();

            $pegawaiList = Pegawai::with('relasiJabatan')
                ->orderByDesc('id_pegawai')
                ->get();

            $absen = $pegawaiList->map(function ($pegawai) use ($startDate, $endDate) {

                $data = Present::where('id_pegawai', $pegawai->id_pegawai)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->whereIn('keterangan', [1, 2, 3])
                    ->get()
                    ->groupBy(fn($i) => Carbon::parse($i->tanggal)->toDateString());

                $masuk  = 0;
                $lembur = 0;

                foreach ($data as $items) {

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

                $izin = Present::where('id_pegawai', $pegawai->id_pegawai)
                    ->where('keterangan', 5)
                    ->where('status', 5)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->count();

                $sakit = Present::where('id_pegawai', $pegawai->id_pegawai)
                    ->where('keterangan', 4)
                    ->where('status', 4)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->count();

                return (object)[
                    'pegawai'     => $pegawai,
                    'masuk'       => $masuk,
                    'jumlem'      => $lembur,
                    'izin'        => $izin,
                    'sakit'       => $sakit,
                    'total_masuk' => $masuk + $lembur,
                ];
            });

            $blnselected = $bulan;
            $thnselected = $tahun;
        }

        return view('admin.lemburbulanan.index', compact(
            'user',
            'list_th',
            'list_bln',
            'absen',
            'blnselected',
            'thnselected',
            'tahun',
            'bulan'
        ))->with('title', 'Lembur Bulanan');
    }

    public function cetak_absen_lembur($tahun, $bulan)
    {
        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate   = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $pegawaiList = Pegawai::with('relasiJabatan')->get();

        $absen = $pegawaiList->map(function ($pegawai) use ($startDate, $endDate) {

            $data = Present::where('id_pegawai', $pegawai->id_pegawai)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->whereIn('keterangan', [1, 2, 3])
                ->get()
                ->groupBy(fn($i) => Carbon::parse($i->tanggal)->toDateString());

            $masuk  = 0;
            $lembur = 0;

            foreach ($data as $items) {

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

            $izin = Present::where('id_pegawai', $pegawai->id_pegawai)
                ->where('keterangan', 5)
                ->where('status', 5)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->count();

            $sakit = Present::where('id_pegawai', $pegawai->id_pegawai)
                ->where('keterangan', 4)
                ->where('status', 4)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->count();

            return (object)[
                'pegawai'     => $pegawai,
                'masuk'       => $masuk,
                'jumlem'      => $lembur,
                'izin'        => $izin,
                'sakit'       => $sakit,
                'total_masuk' => $masuk + $lembur,
            ];
        });

        return view('admin.lemburbulanan.cetak', [
            'absen'       => $absen,
            'blnselected' => $bulan,
            'thnselected' => $tahun,
            'blnnya'      => $bulan,
            'thn'         => $tahun,
        ]);
    }

    public function detail_absen($id)
    {
        $detail_absensi = Present::with('pegawai.relasiJabatan')
            ->findOrFail($id);

        return view('admin.absenbulanan.detail', [
            'title' => 'Detail Absensi',
            'user' => Auth::user(),
            'detail_absensi' => $detail_absensi,
        ]);
    }

    public function tpp_bulanan(Request $request)
    {
        $tahun = $request->post('th');
        $bulan = $request->post('bln');

        $gaji = [];
        $blnselected = null;
        $thnselected = null;

        if ($tahun && $bulan) {
            $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
            $endDate = Carbon::create($tahun, $bulan, 1)->endOfMonth();

            $gaji = Payroll::with(['pegawai', 'jabatan', 'details'])
                ->whereBetween('periode', [$startDate, $endDate])
                ->whereHas('details') // Hanya ambil payroll yang punya detail
                ->orderByDesc('id_payroll')
                ->get();

            $blnselected = $bulan;
            $thnselected = $tahun;
        }

        $years = range(2025, 2030);
        $months = range(1, 12);

        return view('admin.tpp_bulanan.index', [
            'title' => 'Gaji Bulanan',
            'user' => Auth::user(),
            'pegawai' => Pegawai::all(),
            'list_th' => $years,
            'list_bln' => $months,
            'gaji' => $gaji,
            'blnselected' => $blnselected,
            'thnselected' => $thnselected,
            'blnnya' => $bulan,
            'thn' => $tahun
        ]);
    }

    public function akumulasi_gaji(Request $request)
    {
        $idPegawai = $request->id_pegawai;
        $tahun     = $request->tahun_cari;
        $bulan     = $request->bulan_cari;

        if (!$idPegawai || !$tahun || !$bulan) {
            return response()->json(['flash' => 'Data tidak lengkap']);
        }

        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate   = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $pegawai = Pegawai::with('relasiJabatan')->findOrFail($idPegawai);
        $jabatan = $pegawai->relasiJabatan;

        $gajiPokokPerHari = (float) ($jabatan->salary ?? 0);
        $upahLemburPerJam = (float) ($jabatan->overtime ?? 0);
        $jumlahHariKerja = 0;
        for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
            if ($d->isWeekday()) {
                $jumlahHariKerja++;
            }
        }

        $data = Present::with('lembur')
            ->where('id_pegawai', $idPegawai)
            ->whereIn('keterangan', [1, 2, 3]) // masuk, pulang, lembur
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->groupBy('tanggal');

        $totalMasuk = 0;

        foreach ($data as $items) {

            $masuk = $items->first(
                fn($i) =>
                $i->keterangan == 1 && $i->status == 1
            );

            $pulang = $items->first(
                fn($i) =>
                $i->keterangan == 2 && $i->status == 2
            );

            $lembur = $items->first(
                fn($i) =>
                $i->keterangan == 3 && $i->status == 3
            );

            if ($masuk && ($pulang || $lembur)) {
                $totalMasuk++;
            }
        }

        $totalGajiMasuk = $totalMasuk * $gajiPokokPerHari;
        $totalJamLembur = 0;

        foreach ($data as $items) {
            $masuk = $items->first(
                fn($i) =>
                $i->keterangan == 1 && $i->status == 1
            );

            $lembur = $items->first(
                fn($i) =>
                $i->keterangan == 3 && $i->status == 3
            );

            if ($masuk && $lembur && $lembur->lembur && $lembur->jam_pulang) {
                $mulai = Carbon::parse(
                    $lembur->lembur->date . ' ' . $lembur->lembur->waktu_lembur
                );

                $pulang = Carbon::parse(
                    $lembur->tanggal . ' ' . $lembur->jam_pulang
                );

                if ($pulang->lessThan($mulai)) {
                    $pulang->addDay();
                }

                $totalJamLembur += $mulai->diffInMinutes($pulang) / 60;
            }
        }

        $totalGajiLembur = round($totalJamLembur * $upahLemburPerJam);
        $totalTerlambat = Present::where('id_pegawai', $idPegawai)
            ->where('keterangan_msk', 1)
            ->where('status', 1)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->count();

        $potonganTerlambat = $totalTerlambat * 10000;

        return response()->json([
            'flash'              => 'Data Ditemukan',
            'total_hari_kerja'   => $jumlahHariKerja,
            'total_hadir'        => $totalMasuk,
            'gaji_msk'           => round($totalGajiMasuk),
            'gaji_lembur'        => $totalGajiLembur,
            'total_jam_lembur'   => round($totalJamLembur, 2),
            'potongan_terlambat' => $potonganTerlambat,
        ]);
    }

    public function simpan_gaji(Request $request)
    {
        $validated = $request->validate([
            'id_pegawai' => 'required|exists:tb_pegawai,id_pegawai',
            'th1'        => 'required|numeric',
            'bln1'       => 'required|numeric',
            'bonus'      => 'nullable|numeric',
            'keterangan' => 'nullable|string'
        ]);

        $startDate = Carbon::create($validated['th1'], $validated['bln1'], 1)->startOfMonth();
        $endDate   = Carbon::create($validated['th1'], $validated['bln1'], 1)->endOfMonth();

        // ❗ Cegah duplikat payroll
        $cek = Payroll::where('id_pegawai', $validated['id_pegawai'])
            ->whereBetween('periode', [$startDate, $endDate])
            ->first();

        if ($cek) {
            return redirect()->route('admin.tpp-bulanan')
                ->with('error', 'Payroll bulan ini sudah ada');
        }

        $reqAkumulasi = new Request([
            'id_pegawai' => $validated['id_pegawai'],
            'tahun_cari' => $validated['th1'],
            'bulan_cari' => $validated['bln1'],
        ]);

        $hasil = json_decode(
            $this->akumulasi_gaji($reqAkumulasi)->getContent(),
            true
        );

        $bonus   = $validated['bonus'] ?? 0;
        $gaber   = ($hasil['gaji_msk'] + $hasil['gaji_lembur'] + $bonus)
            - $hasil['potongan_terlambat'];

        $pegawai = Pegawai::findOrFail($validated['id_pegawai']);

        $payroll = Payroll::create([
            'id_pegawai'  => $pegawai->id_pegawai,
            'id_jabatan'  => $pegawai->id_jabatan,
            'periode'     => $endDate,
            'tanggal'     => now(),
            'keterangan'  => $validated['keterangan'],
            'gaji_bersih' => round($gaber),
        ]);

        PayrollDetail::create([
            'id_payroll'     => $payroll->id_payroll,
            'gaji_pokok'     => $hasil['gaji_msk'],
            'gaji_lembur'    => $hasil['gaji_lembur'],
            'bonus'          => $bonus,
            'potongan_absen' => $hasil['potongan_terlambat'],
        ]);

        return redirect()->route('admin.tpp-bulanan')
            ->with('success', 'Gaji berhasil disimpan');
    }

    public function edit_gaji(Request $request)
    {
        $validated = $request->validate([
            'id_payroll' => 'required|exists:tb_payroll,id_payroll',
            'bonus'      => 'nullable|numeric',
            'keterangan' => 'nullable|string',
        ]);

        $payroll = Payroll::with('details')->findOrFail($validated['id_payroll']);
        $detail  = $payroll->details->first();

        // 🔒 PERTAHANKAN DATA MANUAL LAMA
        $bonus = $request->filled('bonus')
            ? (float) $validated['bonus']
            : ($detail->bonus ?? 0);

        $keterangan = $request->filled('keterangan')
            ? $validated['keterangan']
            : $payroll->keterangan;

        $periode = Carbon::parse($payroll->periode);

        $reqAkumulasi = new Request([
            'id_pegawai' => $payroll->id_pegawai,
            'tahun_cari' => $periode->year,
            'bulan_cari' => $periode->month,
        ]);

        $hasil = json_decode(
            $this->akumulasi_gaji($reqAkumulasi)->getContent(),
            true
        );

        $gajiBersih = (
            $hasil['gaji_msk']
            + $hasil['gaji_lembur']
            + $bonus
        ) - $hasil['potongan_terlambat'];

        // ✅ UPDATE
        $payroll->update([
            'gaji_bersih' => round($gajiBersih),
            'keterangan'  => $keterangan,
        ]);

        $detail->update([
            'gaji_pokok'     => $hasil['gaji_msk'],
            'gaji_lembur'    => $hasil['gaji_lembur'],
            'bonus'          => $bonus,
            'potongan_absen' => $hasil['potongan_terlambat'],
        ]);

        return redirect()->route('admin.tpp-bulanan')
            ->with('success', 'Gaji berhasil diperbarui');
    }

    public function refresh_gaji(Request $request)
    {
        $payroll = Payroll::with('details')->findOrFail($request->id_payroll);
        $detail  = $payroll->details->first();

        // 🔒 kunci data manual
        $bonus      = $detail->bonus ?? 0;
        $keterangan = $payroll->keterangan;

        $periode = Carbon::parse($payroll->periode);

        $rekap = Present::where('id_pegawai', $payroll->id_pegawai)
            ->whereMonth('tanggal', $periode->month)
            ->whereYear('tanggal', $periode->year)
            ->get();

        $gaji_pokok = $rekap->sum('gaji_masuk');
        $lembur     = $rekap->sum('lembur');
        $potongan   = $rekap->sum('potongan_terlambat');

        // ✅ update hanya hasil absensi
        $detail->update([
            'gaji_pokok'     => $gaji_pokok,
            'gaji_lembur'    => $lembur,
            'potongan_absen' => $potongan,
        ]);

        // ✅ gaji bersih pakai data manual lama
        $payroll->update([
            'gaji_bersih' => $gaji_pokok + $lembur + $bonus - $potongan,
            'keterangan'  => $keterangan, // opsional, biar eksplisit
        ]);

        return back()->with('success', 'Payroll berhasil di-refresh');
    }

    // app/Http/Controllers/AdminController.php
    public function hapus_gaji($id)
    {
        // Cari payroll
        $payroll = Payroll::findOrFail($id);

        // Hapus payroll (detail otomatis ikut terhapus karena ON DELETE CASCADE)
        $payroll->delete();

        return redirect()->route('admin.tpp-bulanan')
            ->with('success', 'Gaji berhasil dihapus');
    }

    public function laporan_tpp_bulanan(Request $request)
    {
        $tahun = $request->post('th');
        $bulan = $request->post('bln');

        $gaji = collect();

        if ($tahun && $bulan) {
            $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
            $endDate = Carbon::create($tahun, $bulan, 1)->endOfMonth();

            $gaji = Payroll::with(['pegawai', 'jabatan', 'details'])
                ->whereBetween('periode', [$startDate, $endDate])
                ->orderBy('periode', 'desc') // terbaru di atas
                ->get();
        }

        return view('admin.laporan.laporan_tpp', [
            'title' => 'Cetak Gaji Bulanan',
            'user' => Auth::user(),
            'pegawai' => Pegawai::all(),
            'list_th' => range(2025, 2030),
            'list_bln' => range(1, 12),
            'gaji' => $gaji,
            'blnselected' => $bulan,
            'thnselected' => $tahun,
            'blnnya' => $bulan,
            'thn' => $tahun,
            'has_filter' => !empty($bulan) && !empty($tahun)
        ]);
    }

    public function detail_laporan_tpp_bulanan($id_pegawai, $bln, $thn)
    {
        $user = Auth::user();

        // Ambil data pegawai
        $pegawai = Pegawai::with('relasiJabatan')->findOrFail($id_pegawai);
        $jabatan = $pegawai->relasiJabatan;

        $tanggalAwal  = Carbon::create($thn, $bln, 1)->startOfMonth();
        $tanggalAkhir = Carbon::create($thn, $bln, 1)->endOfMonth();

        // Ambil payroll dan detail
        $payroll = Payroll::with('details')
            ->where('id_pegawai', $id_pegawai)
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

        // Kirim ke view
        return view('admin.laporan.detail', [
            'title' => 'Detail Laporan Gaji Bulanan',
            'user'  => $user,
            'pegawai' => $pegawai,           // Aman dipakai di view
            'id_pegawai' => $id_pegawai,     // Untuk route cetak
            'blnselected' => $bln,
            'thnselected' => $thn,
            'gaji' => (object)[
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

    public function cetak_payrol_pegawai($id_pegawai, $bulan, $tahun)
    {
        // Ambil payroll berdasarkan bulan & tahun
        $payroll = Payroll::with(['pegawai', 'jabatan', 'details'])
            ->where('id_pegawai', $id_pegawai)
            ->whereMonth('periode', $bulan)
            ->whereYear('periode', $tahun)
            ->firstOrFail();

        // 1 payroll = 1 detail
        $detail = $payroll->details->first();

        $gaji = [
            'id_pegawai'     => $payroll->pegawai->id_pegawai,
            'gaji_pokok'     => $detail->gaji_pokok ?? 0,
            'gaji_lembur'    => $detail->gaji_lembur ?? 0,
            'bonus'          => $detail->bonus ?? 0,
            'potongan_absen' => $detail->potongan_absen ?? 0,
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

        return view('admin.laporan.cetak', [
            'payroll'     => $payroll,
            'gaji'        => $gaji,
            'absen'       => $absen,
            'blnselected' => $bulan,
            'thnselected' => $tahun,
        ]);
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
        return range(2025, 2030);
    }
}
