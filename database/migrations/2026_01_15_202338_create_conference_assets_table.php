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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('edition_id')->index('idx_edition_conference');
            $table->unsignedBigInteger('conference_id')->index('conference_assets_conference_id_foreign');
            $table->enum('asset_type', ['logo', 'poster', 'banner', 'brochure', 'image', 'other']);
            $table->string('file_name');
            $table->string('file_path');
            $table->string('alt_text')->nullable();
            $table->string('usage_context')->nullable();
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->timestamps();

            $table->index(['edition_id', 'asset_type'], 'idx_edition_asset_type');
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
