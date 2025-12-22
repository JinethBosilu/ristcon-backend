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
        Schema::create('social_media_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained('conferences')->onDelete('cascade');
            $table->enum('platform', ['facebook', 'twitter', 'linkedin', 'instagram', 'youtube', 'email'])->comment('Social media platform');
            $table->string('url')->comment('URL to the social media page or mailto link');
            $table->string('label')->default('')->comment('Display label for accessibility');
            $table->integer('display_order')->default(0)->comment('Order of display');
            $table->boolean('is_active')->default(true)->comment('Whether link should be shown');
            $table->timestamps();

            $table->index(['conference_id', 'is_active', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_media_links');
    }
};
