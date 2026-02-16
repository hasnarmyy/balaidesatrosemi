<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pegawai;
use App\Models\FaceSample;
use App\Helpers\FaceHelper;

class FaceApiController extends Controller
{
    /**
     * Verify incoming embedding against enrollment samples for current user
     */
    public function verify(Request $request)
    {
        $request->validate([
            'embedding' => 'required|array|min:1',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $pegawai = Pegawai::where('id_user', $user->id)->first();
        if (!$pegawai) {
            return response()->json(['error' => 'Pegawai tidak ditemukan'], 404);
        }

        // Get all enrollment samples for this pegawai
        $samples = FaceSample::where('id_pegawai', $pegawai->id_pegawai)
            ->whereNotNull('embedding')
            ->pluck('embedding')
            ->toArray();

        if (empty($samples)) {
            return response()->json([
                'error' => 'Belum ada sample wajah (enroll dulu)',
                'verified' => false
            ], 400);
        }

        $liveEmbedding = $request->input('embedding');

        // Verify
        $result = FaceHelper::verifyEmbedding($liveEmbedding, $samples);

        return response()->json($result, 200);
    }

    /**
     * Get enrollment samples count for current user
     */
    public function getSamplesCount()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $pegawai = Pegawai::where('id_user', $user->id)->first();
        if (!$pegawai) {
            return response()->json(['count' => 0], 200);
        }

        $count = FaceSample::where('id_pegawai', $pegawai->id_pegawai)
            ->whereNotNull('embedding')
            ->count();

        return response()->json(['count' => $count], 200);
    }
}
