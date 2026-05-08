<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Encrypted casts produzem blobs base64 (~200-300 chars para inputs curtos);
        // alargamos para TEXT para evitar truncagem em MySQL/Postgres.
        Schema::table('medical_infos', function (Blueprint $table) {
            $table->text('blood_type')->nullable()->change();
            $table->text('emergency_contact')->nullable()->change();
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->text('tax_id')->nullable()->change();
            $table->text('social_security_number')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('medical_infos', function (Blueprint $table) {
            $table->string('blood_type')->nullable()->change();
            $table->string('emergency_contact')->nullable()->change();
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->string('tax_id')->nullable()->change();
            $table->string('social_security_number')->nullable()->change();
        });
    }
};
