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
        Schema::create('conference_editions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->year('year')->unique()->comment('Conference year (e.g., 2026, 2027)');
            $table->integer('edition_number')->comment('Sequential edition count (e.g., 13th, 14th)');
            $table->string('name')->comment('Display name (e.g., "RISTCON 2027")');
            $table->string('slug', 100)->unique()->comment('URL-friendly identifier (e.g., "2027", "ristcon-2027")');
            $table->enum('status', ['draft', 'published', 'archived', 'cancelled'])->default('draft')->index()->comment('Current lifecycle status');
            $table->boolean('is_active_edition')->default(false)->index()->comment('Marks the default edition when no year is specified in API');
            $table->date('conference_date')->comment('Main conference date');
            $table->enum('venue_type', ['physical', 'virtual', 'hybrid'])->default('physical')->comment('Conference format');
            $table->string('venue_location')->nullable()->comment('Physical location if applicable');
            $table->text('theme')->comment('Conference theme/focus');
            $table->text('description')->nullable()->comment('Detailed conference description');
            $table->string('general_email')->comment('General inquiry email');
            $table->string('availability_hours')->nullable()->comment('Support/contact availability hours');
            $table->year('copyright_year')->comment('Copyright year for footer');
            $table->string('site_version', 20)->default('1.0')->comment('Website version identifier');
            $table->boolean('is_legacy_site')->default(false);
            $table->text('legacy_website_url')->nullable();
            $table->dateTime('last_updated')->nullable()->comment('Custom last update timestamp for frontend display');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_active_edition'], 'idx_status_active');
            $table->index(['year', 'status'], 'idx_year_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_editions');
    }
};
