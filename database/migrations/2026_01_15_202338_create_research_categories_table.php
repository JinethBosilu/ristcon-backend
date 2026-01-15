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
        Schema::create('research_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('edition_id')->index('idx_edition_researchca');
            $table->unsignedBigInteger('conference_id')->index('research_categories_conference_id_foreign');
            $table->string('category_code', 10);
            $table->string('category_name');
            $table->text('description')->nullable();
            $table->integer('display_order');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['edition_id', 'category_code'], 'idx_edition_category_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_categories');
    }
};
