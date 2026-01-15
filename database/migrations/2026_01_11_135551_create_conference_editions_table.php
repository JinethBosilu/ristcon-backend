<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration creates the conference_editions table which serves as the
     * central table for managing multiple conference years/editions.
     * 
     * Key Design Decisions:
     * - year is UNIQUE to ensure one edition per year
     * - is_active_edition flag determines the default edition for API queries
     * - status enum controls visibility and lifecycle management
     * - slug provides URL-friendly identifier
     * - soft deletes enabled for data preservation
     */
    public function up(): void
    {
        Schema::create('conference_editions', function (Blueprint $table) {
            $table->id();
            
            // Core identifiers
            $table->year('year')->unique()->comment('Conference year (e.g., 2026, 2027)');
            $table->integer('edition_number')->comment('Sequential edition count (e.g., 13th, 14th)');
            $table->string('name', 255)->comment('Display name (e.g., "RISTCON 2027")');
            $table->string('slug', 100)->unique()->comment('URL-friendly identifier (e.g., "2027", "ristcon-2027")');
            
            // Status management
            $table->enum('status', [
                'draft',        // Being prepared, not visible to public
                'published',    // Live and visible to public
                'archived',     // Past conference, read-only
                'cancelled'     // Conference was cancelled
            ])->default('draft')->index()->comment('Current lifecycle status');
            
            $table->boolean('is_active_edition')
                ->default(false)
                ->index()
                ->comment('Marks the default edition when no year is specified in API');
            
            // Conference details
            $table->date('conference_date')->comment('Main conference date');
            
            $table->enum('venue_type', ['physical', 'virtual', 'hybrid'])
                ->default('physical')
                ->comment('Conference format');
            
            $table->string('venue_location', 255)
                ->nullable()
                ->comment('Physical location if applicable');
            
            $table->text('theme')->comment('Conference theme/focus');
            $table->text('description')->nullable()->comment('Detailed conference description');
            
            // Contact information
            $table->string('general_email', 255)->comment('General inquiry email');
            $table->string('availability_hours', 255)
                ->nullable()
                ->comment('Support/contact availability hours');
            
            // Metadata
            $table->year('copyright_year')->comment('Copyright year for footer');
            $table->string('site_version', 20)
                ->default('1.0')
                ->comment('Website version identifier');
            
            $table->datetime('last_updated')
                ->nullable()
                ->comment('Custom last update timestamp for frontend display');
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Composite indexes for common queries
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
