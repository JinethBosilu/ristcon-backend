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
        Schema::table('conference_editions', function (Blueprint $table) {
            $table->boolean('is_legacy_site')->default(false)->after('site_version');
            $table->text('legacy_website_url')->nullable()->after('is_legacy_site');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_editions', function (Blueprint $table) {
            $table->dropColumn(['is_legacy_site', 'legacy_website_url']);
        });
    }
};
