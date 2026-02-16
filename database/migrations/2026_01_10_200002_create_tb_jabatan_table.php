<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbJabatanTable extends Migration
{
    public function up()
    {
        Schema::create('tb_jabatan', function (Blueprint $table) {
            $table->integer('id_jabatan')->autoIncrement();
            $table->string('jabatan', 255);
            $table->double('salary');
            $table->double('overtime');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tb_jabatan');
    }
}
