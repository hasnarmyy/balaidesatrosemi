<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_face_samples', function (Blueprint $table) {
            $table->id('id_face_sample');
            $table->string('id_pegawai');
            $table->string('image_path');
            $table->longText('embedding')->nullable();
            $table->string('model_version')->nullable();
            $table->timestamps();

            $table->foreign('id_pegawai')->references('id_pegawai')->on('tb_pegawai')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_face_samples');
    }
};
