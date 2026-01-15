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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('edition_id')->index('idx_edition_committeem');
            $table->unsignedBigInteger('conference_id')->index('committee_members_conference_id_foreign');
            $table->unsignedBigInteger('committee_type_id')->index('committee_members_committee_type_id_foreign');
            $table->string('full_name');
            $table->string('designation');
            $table->string('department')->nullable();
            $table->string('affiliation');
            $table->string('role');
            $table->string('role_category')->nullable();
            $table->string('country')->nullable();
            $table->boolean('is_international')->default(false);
            $table->string('photo_path')->nullable();
            $table->integer('display_order');
            $table->timestamps();

            $table->index(['edition_id', 'committee_type_id'], 'idx_edition_committee_type');
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
