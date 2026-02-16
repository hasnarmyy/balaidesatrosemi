<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbLemburTable extends Migration
{
    public function up()
    {
        Schema::create('tb_lembur', function (Blueprint $table) {
            $table->integer('id_lembur')->autoIncrement();
            $table->string('id_pegawai', 255);
            $table->index('id_pegawai');
            $table->date('date');
            $table->time('waktu_lembur');
            $table->integer('status');

            // Foreign Key Constraint
            $table->foreign('id_pegawai')
                  ->references('id_pegawai')
                  ->on('tb_pegawai')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tb_lembur');
    }
}
