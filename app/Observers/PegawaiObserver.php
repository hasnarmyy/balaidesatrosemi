<?php

namespace App\Observers;

use App\Models\Pegawai;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\Jabatan;

class PegawaiObserver
{
    public function updated(Pegawai $pegawai)
    {
        // Jika jabatan pegawai berubah
        if ($pegawai->wasChanged('id_jabatan')) {

            $jabatan = Jabatan::find($pegawai->id_jabatan);
            if (!$jabatan) return;

            // Ambil semua payroll pegawai
            $payrolls = Payroll::where('id_pegawai', $pegawai->id_pegawai)->get();

            foreach ($payrolls as $payroll) {

                // Update header payroll
                $payroll->update([
                    'id_jabatan' => $pegawai->id_jabatan,
                ]);

                // Update payroll detail
                $detail = PayrollDetail::where('id_payroll', $payroll->id_payroll)->first();

                if ($detail) {
                    $detail->update([
                        'gaji_pokok' => $jabatan->salary,
                        'gaji_lembur' => $jabatan->overtime,
                    ]);

                    // Hitung ulang gaji bersih
                    $payroll->update([
                        'gaji_bersih' =>
                            $detail->gaji_pokok +
                            $detail->gaji_lembur +
                            $detail->bonus -
                            $detail->potongan_absen
                    ]);
                }
            }
        }
    }
}
