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
        Schema::create('tb_pegawai', function (Blueprint $table) {

            // Primary Key
            $table->string('id_pegawai', 255);
            $table->primary('id_pegawai');

            // Foreign Keys (INT biasa, TANPA unsigned)
            $table->integer('id_user');
            $table->integer('id_jabatan');

            // Data Pegawai
            $table->string('nama_pegawai', 255);
            $table->string('jekel', 10);
            $table->string('pendidikan', 100);
            $table->integer('status_kepegawaian');
            $table->string('agama', 100);
            $table->string('no_hp', 255);
            $table->text('alamat');
            $table->string('foto', 255);
            $table->string('ktp', 255);
            $table->date('tanggal_masuk');

            // Foreign Key Constraint
            $table->foreign('id_user')
                ->references('id')
                ->on('user')
                ->onDelete('cascade');

            $table->foreign('id_jabatan')
                ->references('id_jabatan')
                ->on('tb_jabatan')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pegawai');
    }
};
