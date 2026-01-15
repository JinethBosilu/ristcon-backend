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
        Schema::create('conference_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conference_id');
            $table->enum('asset_type', ['logo', 'poster', 'banner', 'brochure', 'image', 'other']);
            $table->string('file_name');
            $table->string('file_path');
            $table->string('alt_text')->nullable();
            $table->string('usage_context')->nullable();
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
        Schema::dropIfExists('conference_assets');
    }
};
