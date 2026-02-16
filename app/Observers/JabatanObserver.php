<?php

namespace App\Observers;

use App\Models\Jabatan;
use App\Models\Payroll;
use App\Models\PayrollDetail;

class JabatanObserver
{
    public function updated(Jabatan $jabatan)
    {
        if ($jabatan->wasChanged(['salary', 'overtime'])) {

            $payrolls = Payroll::where('id_jabatan', $jabatan->id_jabatan)->get();

            foreach ($payrolls as $payroll) {

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
