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
        Schema::create('tb_presents', function (Blueprint $table) {

            // Primary Key
            $table->integer('id_presents')->autoIncrement();

            // Foreign Key ke tb_pegawai (varchar)
            $table->string('id_pegawai', 255);

            // Waktu & tanggal
            $table->date('tanggal');
            $table->time('waktu');

            // Keterangan & status
            $table->integer('keterangan');
            $table->integer('status')->nullable()->default(0);

            // Foto & izin
            $table->string('foto_selfie_masuk', 255)->nullable();
            $table->string('foto_selfie_pulang', 255)->nullable();
            $table->text('keterangan_izin')->nullable();

            // Lembur & jam
            $table->integer('id_lembur')->nullable();
            $table->time('jam_masuk')->nullable();
            $table->integer('keterangan_msk')->nullable();
            $table->time('jam_pulang')->nullable();

            // Geolokasi
            $table->string('latitude', 255)->nullable();
            $table->string('longitude', 255)->nullable();

            // Foreign Key Constraint
            $table->foreign('id_pegawai')
                ->references('id_pegawai')
                ->on('tb_pegawai')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_presents');
    }
};
