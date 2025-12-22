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
        Schema::create('abstract_formats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conference_id')->constrained()->onDelete('cascade');
            $table->enum('format_type', ['abstract', 'extended_abstract']);
            
            // Title specifications
            $table->integer('max_title_characters')->nullable();
            $table->string('title_font_name', 100)->nullable();
            $table->integer('title_font_size')->nullable();
            $table->string('title_style', 100)->nullable(); // e.g., 'bold', 'italic'
            
            // Body specifications
            $table->integer('max_body_words')->nullable();
            $table->string('body_font_name', 100)->nullable();
            $table->integer('body_font_size')->nullable();
            $table->decimal('body_line_spacing', 3, 1)->nullable();
            
            // Keywords specifications
            $table->integer('max_keywords')->nullable();
            $table->string('keywords_font_name', 100)->nullable();
            $table->integer('keywords_font_size')->nullable();
            $table->string('keywords_style', 100)->nullable();
            
            // Extended abstract specific
            $table->integer('max_references')->nullable();
            $table->json('sections')->nullable(); // For extended abstract structure
            
            // Additional info
            $table->text('additional_notes')->nullable();
            $table->integer('display_order')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['conference_id', 'format_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abstract_formats');
    }
};
