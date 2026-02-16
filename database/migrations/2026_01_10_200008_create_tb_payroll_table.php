<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tb_payroll', function (Blueprint $table) {
            $table->integer('id_payroll')->autoIncrement();
            $table->string('id_pegawai', 255);
            $table->integer('id_jabatan');
            $table->text('periode');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->double('gaji_bersih');

            // ================= FOREIGN KEY CONSTRAINT =================
            $table->foreign('id_pegawai')
                  ->references('id_pegawai')
                  ->on('tb_pegawai')
                  ->onDelete('cascade');

            $table->foreign('id_jabatan')
                  ->references('id_jabatan')
                  ->on('tb_jabatan')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_payroll');
    }
};
