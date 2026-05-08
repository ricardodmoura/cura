<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            // JSON map: { service_updates: bool, review_received: bool, marketing: bool, ... }
            $table->json('notification_preferences')->nullable()->after('social_security_number');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('notification_preferences');
        });
    }
};
