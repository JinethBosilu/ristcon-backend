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
        Schema::create('presentation_guidelines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('edition_id')->index('idx_edition_presentati');
            $table->unsignedBigInteger('conference_id')->index('presentation_guidelines_conference_id_foreign');
            $table->enum('presentation_type', ['oral', 'poster', 'workshop', 'panel']);
            $table->integer('duration_minutes')->nullable();
            $table->integer('presentation_minutes')->nullable();
            $table->integer('qa_minutes')->nullable();
            $table->decimal('poster_width')->nullable();
            $table->decimal('poster_height')->nullable();
            $table->enum('poster_unit', ['inches', 'cm', 'mm'])->nullable();
            $table->enum('poster_orientation', ['portrait', 'landscape'])->nullable();
            $table->boolean('physical_presence_required')->default(true);
            $table->text('detailed_requirements')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presentation_guidelines');
    }
};
