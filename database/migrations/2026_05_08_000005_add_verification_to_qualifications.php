<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qualifications', function (Blueprint $table) {
            // Número de cédula (OM/OE/OMD) — em PT é o identificador público num registo profissional.
            $table->string('cedula_number', 64)->nullable()->after('description');
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending')->after('cedula_number');
            $table->timestamp('verified_at')->nullable()->after('verification_status');
            $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable()->after('verified_by');
        });
    }

    public function down(): void
    {
        Schema::table('qualifications', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['cedula_number', 'verification_status', 'verified_at', 'verified_by', 'rejection_reason']);
        });
    }
};
