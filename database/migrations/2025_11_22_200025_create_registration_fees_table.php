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
        Schema::create('registration_fees', function (Blueprint $table) {
            $table->id('fee_id');
            $table->unsignedBigInteger('conference_id');
            $table->string('attendee_type', 100)->comment('Type of attendee: Foreign, Local, Student, etc.');
            $table->string('currency', 10)->comment('Currency code: USD, LKR, etc.');
            $table->decimal('amount', 10, 2)->comment('Registration fee amount');
            $table->decimal('early_bird_amount', 10, 2)->nullable()->comment('Early bird discount amount');
            $table->date('early_bird_deadline')->nullable()->comment('Early bird registration deadline');
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
            $table->index(['conference_id', 'attendee_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_fees');
    }
};
