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
        Schema::create('conferences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->year('year');
            $table->integer('edition_number');
            $table->date('conference_date');
            $table->enum('venue_type', ['physical', 'virtual', 'hybrid'])->default('physical');
            $table->string('venue_location')->nullable();
            $table->string('theme');
            $table->text('description')->nullable();
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])->default('upcoming');
            $table->string('general_email');
            $table->string('availability_hours')->nullable()->comment('Contact availability hours (e.g., "Available Mon-Fri, 9AM-5PM")');
            $table->dateTime('last_updated')->nullable();
            $table->year('copyright_year');
            $table->string('site_version')->default('1.0');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conferences');
    }
};
