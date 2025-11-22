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
            $table->id();
            $table->unsignedBigInteger('conference_id');
            $table->enum('presentation_type', ['oral', 'poster', 'workshop', 'panel']);
            $table->integer('duration_minutes')->nullable();
            $table->integer('presentation_minutes')->nullable();
            $table->integer('qa_minutes')->nullable();
            $table->decimal('poster_width', 8, 2)->nullable();
            $table->decimal('poster_height', 8, 2)->nullable();
            $table->enum('poster_unit', ['inches', 'cm', 'mm'])->nullable();
            $table->enum('poster_orientation', ['portrait', 'landscape'])->nullable();
            $table->boolean('physical_presence_required')->default(true);
            $table->text('detailed_requirements')->nullable();
            $table->timestamps();
            
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
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
