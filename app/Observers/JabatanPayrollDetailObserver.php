<?php

namespace App\Observers;

use App\Models\Jabatan;
use App\Models\Payroll;
use App\Models\PayrollDetail;

class JabatanPayrollDetailObserver
{
    public function updated(Jabatan $jabatan)
    {
        // Ambil semua payroll terkait jabatan
        $payrolls = Payroll::where('id_jabatan', $jabatan->id_jabatan)->get();

        foreach ($payrolls as $payroll) {
            // Update id_jabatan di payroll (jika perlu)
            $payroll->id_jabatan = $jabatan->id_jabatan;
            $payroll->save();

            // Update semua payroll detail terkait
            $details = PayrollDetail::where('id_payroll', $payroll->id_payroll)->get();

            foreach ($details as $detail) {
                $detail->gaji_pokok  = $jabatan->salary;
                $detail->gaji_lembur = $jabatan->overtime; // nama kolom sesuai database
                $detail->save();
            }
        }
    }
}
