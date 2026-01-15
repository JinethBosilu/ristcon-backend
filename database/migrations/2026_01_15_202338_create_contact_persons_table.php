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
        Schema::create('contact_persons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('edition_id')->index('idx_edition_contactper');
            $table->unsignedBigInteger('conference_id')->index('contact_persons_conference_id_foreign');
            $table->string('full_name');
            $table->string('role');
            $table->string('department')->nullable();
            $table->string('mobile');
            $table->string('phone')->nullable();
            $table->string('email');
            $table->text('address')->nullable();
            $table->integer('display_order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_persons');
    }
};
