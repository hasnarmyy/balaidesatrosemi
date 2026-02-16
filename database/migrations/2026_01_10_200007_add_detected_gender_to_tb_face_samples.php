<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_face_samples', function (Blueprint $table) {
            $table->string('detected_gender', 10)->nullable()->after('model_version');
        });
    }

    public function down(): void
    {
        Schema::table('tb_face_samples', function (Blueprint $table) {
            $table->dropColumn('detected_gender');
        });
    }
};
