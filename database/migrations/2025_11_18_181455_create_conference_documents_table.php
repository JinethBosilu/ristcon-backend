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
        Schema::create('conference_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conference_id');
            $table->enum('document_category', ['abstract_template', 'author_form', 'registration_form', 'presentation_template', 'camera_ready_template', 'other']);
            $table->string('file_name');
            $table->string('file_path');
            $table->string('display_name');
            $table->boolean('is_available')->default(true);
            $table->integer('button_width_percent')->nullable();
            $table->integer('display_order');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->timestamps();
            
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_documents');
    }
};
