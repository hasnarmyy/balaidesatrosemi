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
        Schema::create('user', function (Blueprint $table) {

            // Primary Key
            $table->integer('id')->autoIncrement();

            // Data User
            $table->string('kode', 100)->unique();
            $table->string('name', 150);
            $table->string('email', 150);
            $table->string('image', 150);
            $table->string('password', 260);

            // Role & status
            $table->integer('role_id');
            $table->integer('is_active');

            // Timestamp custom (stored as integer to match legacy dump)
            $table->bigInteger('date_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
