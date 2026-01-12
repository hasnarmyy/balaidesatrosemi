<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_payroll_detail', function (Blueprint $table) {

            // Primary Key (INT biasa)
            $table->integer('id_payroll_detail')->autoIncrement();

            // Foreign Key (INT biasa, TANPA unsigned)
            $table->integer('id_payroll');

            // Detail gaji
            $table->double('potongan_absen')->default(0);
            $table->double('gaji_pokok')->default(0);
            $table->double('gaji_lembur')->default(0);
            $table->double('bonus')->default(0);

            // Foreign Key Constraint
            $table->foreign('id_payroll')
                  ->references('id_payroll')
                  ->on('tb_payroll')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_payroll_detail');
    }
};
