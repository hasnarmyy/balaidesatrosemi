<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\FaceSample;
use App\Models\Pegawai;

class FaceEnrollmentController extends Controller
{
    public function showForm()
    {
        $user = Auth::user();
        $pegawai = Pegawai::where('id_user', $user->id)->first();
        $samples = [];
        if ($pegawai) {
            $samples = FaceSample::where('id_pegawai', $pegawai->id_pegawai)->orderByDesc('id_face_sample')->get();
        }

        return view('pegawai.face.enroll', [
            'title' => 'Enroll Wajah',
            'user' => $user,
            'pegawai' => $pegawai,
            'samples' => $samples,
        ]);
    }

    public function enroll(Request $request)
    {
        $request->validate([
            'foto_enroll' => 'required|string',
            'embedding' => 'required',
            'detected_gender' => 'nullable|string|in:male,female',
        ]);

        $user = Auth::user();
        $pegawai = Pegawai::where('id_user', $user->id)->first();
        if (!$pegawai) {
            return redirect()->back()->with('error', 'Data pegawai tidak ditemukan');
        }

        try {
            $imageBase64 = $request->input('foto_enroll');
            $image = str_replace(['data:image/jpeg;base64,', 'data:image/png;base64,', ' '], ['', '', '+'], $imageBase64);
            $imageName = 'face_' . $pegawai->id_pegawai . '_' . time() . '.jpg';
            $path = 'gambar/faces/' . $imageName;
            $imageData = base64_decode($image, true);

            if (!$imageData) {
                return redirect()->back()->with('error', 'Gagal decode image data');
            }

            // Check image size (max 5MB for safety)
            if (strlen($imageData) > 5242880) {
                return redirect()->back()->with('error', 'Ukuran gambar terlalu besar (max 5MB)');
            }

            Storage::disk('public')->put($path, $imageData);

            // Get embedding from client (face-api.js)
            $embeddingInput = $request->input('embedding');
            $embedding = null;

            if (is_array($embeddingInput)) {
                $embedding = $embeddingInput;
            } else {
                // Expect JSON string from hidden input
                $embedding = json_decode($embeddingInput, true);
            }

            if (!is_array($embedding) || count($embedding) < 1) {
                return redirect()->back()->with('error', 'Embedding tidak valid atau kosong');
            }

            // Save record with embedding and detected gender from client
            $detectedGender = $request->input('detected_gender');
            $sample = FaceSample::create([
                'id_pegawai' => $pegawai->id_pegawai,
                'image_path' => $path,
                'embedding' => json_encode($embedding),
                'model_version' => 'face-api.js',
                'detected_gender' => in_array($detectedGender, ['male', 'female']) ? $detectedGender : null,
            ]);

            if (!$sample) {
                return redirect()->back()->with('error', 'Gagal menyimpan sample wajah');
            }

            return redirect()->route('pegawai.enrollFace')->with('success', 'Registrasi wajah berhasil! Embedding disimpan via face-api.js');
        } catch (\Exception $e) {
            \Log::error('Face enrollment error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function delete(Request $request, $id)
    {
        $user = Auth::user();
        $pegawai = Pegawai::where('id_user', $user->id)->first();
        if (!$pegawai) {
            return redirect()->back()->with('error', 'Data pegawai tidak ditemukan');
        }

        $sample = FaceSample::where('id_face_sample', $id)->where('id_pegawai', $pegawai->id_pegawai)->first();
        if (!$sample) {
            return redirect()->back()->with('error', 'Sample tidak ditemukan atau bukan milik Anda');
        }

        // Hapus file jika ada
        try {
            if ($sample->image_path && Storage::disk('public')->exists($sample->image_path)) {
                Storage::disk('public')->delete($sample->image_path);
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to delete face sample file: ' . $e->getMessage());
        }

        $sample->delete();

        return redirect()->route('pegawai.enrollFace')->with('success', 'Sample wajah berhasil dihapus');
    }
}
