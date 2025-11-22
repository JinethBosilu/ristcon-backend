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
        Schema::create('payment_policies', function (Blueprint $table) {
            $table->id('policy_id');
            $table->unsignedBigInteger('conference_id');
            $table->text('policy_text')->comment('Policy text/description');
            $table->enum('policy_type', ['requirement', 'restriction', 'note'])->default('note');
            $table->integer('display_order')->default(0);
            $table->boolean('is_highlighted')->default(false)->comment('Highlight important policies');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
            $table->index(['conference_id', 'policy_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_policies');
    }
};
