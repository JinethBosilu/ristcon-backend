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
        Schema::create('important_dates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('edition_id')->index('idx_edition_importantd');
            $table->unsignedBigInteger('conference_id')->index('important_dates_conference_id_foreign');
            $table->enum('date_type', ['submission_deadline', 'notification', 'camera_ready', 'conference_date', 'registration_deadline', 'other']);
            $table->date('date_value');
            $table->boolean('is_extended')->default(false);
            $table->integer('display_order');
            $table->string('display_label');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['edition_id', 'date_type'], 'idx_edition_date_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('important_dates');
    }
};
