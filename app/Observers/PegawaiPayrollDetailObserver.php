<?php

namespace App\Observers;

use App\Models\Pegawai;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\Jabatan;

class PegawaiPayrollDetailObserver
{
    public function updated(Pegawai $pegawai)
    {
        $jabatan = Jabatan::find($pegawai->id_jabatan);
        if (!$jabatan) return;

        $payrolls = Payroll::where('id_pegawai', $pegawai->id_pegawai)->get();

        foreach ($payrolls as $payroll) {
            $payroll->id_jabatan = $pegawai->id_jabatan;
            $payroll->save();

            $details = PayrollDetail::where('id_payroll', $payroll->id_payroll)->get();
            foreach ($details as $detail) {
                $detail->gaji_pokok   = $jabatan->salary;
                $detail->gaji_lembur  = $jabatan->overtime;
                $detail->save();
            }
        }
    }
}
