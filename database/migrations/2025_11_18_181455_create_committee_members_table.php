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
        Schema::create('committee_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conference_id');
            $table->unsignedBigInteger('committee_type_id');
            $table->string('full_name');
            $table->string('designation');
            $table->string('department')->nullable();
            $table->string('affiliation');
            $table->string('role');
            $table->string('role_category')->nullable();
            $table->string('country')->nullable();
            $table->boolean('is_international')->default(false);
            $table->integer('display_order');
            $table->timestamps();
            
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
            $table->foreign('committee_type_id')->references('id')->on('committee_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committee_members');
    }
};
