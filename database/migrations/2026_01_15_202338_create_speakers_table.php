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
        Schema::create('speakers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('edition_id')->index('idx_edition_speakers');
            $table->unsignedBigInteger('conference_id')->index('speakers_conference_id_foreign');
            $table->enum('speaker_type', ['keynote', 'plenary', 'invited']);
            $table->integer('display_order');
            $table->string('full_name');
            $table->string('title')->nullable();
            $table->string('affiliation');
            $table->string('additional_affiliation')->nullable();
            $table->text('bio')->nullable();
            $table->string('photo_filename')->nullable();
            $table->string('website_url')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();

            $table->index(['edition_id', 'speaker_type'], 'idx_edition_speaker_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('speakers');
    }
};
