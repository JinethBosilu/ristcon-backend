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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('edition_id')->index('idx_edition_conference');
            $table->unsignedBigInteger('conference_id')->index('conference_documents_conference_id_foreign');
            $table->enum('document_category', ['abstract_template', 'author_form', 'registration_form', 'presentation_template', 'camera_ready_template', 'flyer', 'other'])->nullable();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('display_name');
            $table->boolean('is_active')->default(true);
            $table->integer('button_width_percent')->nullable();
            $table->integer('display_order');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->timestamps();

            $table->index(['edition_id', 'document_category', 'is_active'], 'idx_edition_doc_category');
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
