<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_presents', function (Blueprint $table) {
            $table->boolean('is_field')->default(false)->after('status');
            $table->text('field_reason')->nullable()->after('is_field');
            $table->string('field_approval_status')->nullable()->default('pending')->after('field_reason');
            $table->unsignedBigInteger('field_approved_by')->nullable()->after('field_approval_status');
            $table->timestamp('field_approved_at')->nullable()->after('field_approved_by');
            $table->index('is_field');
        });
    }

    public function down(): void
    {
        Schema::table('tb_presents', function (Blueprint $table) {
            $table->dropIndex(['is_field']);
            $table->dropColumn(['is_field', 'field_reason', 'field_approval_status', 'field_approved_by', 'field_approved_at']);
        });
    }
};
