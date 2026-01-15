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
        Schema::create('payment_information', function (Blueprint $table) {
            $table->bigIncrements('payment_id');
            $table->unsignedBigInteger('edition_id')->index('idx_edition_paymentinf');
            $table->unsignedBigInteger('conference_id');
            $table->enum('payment_type', ['local', 'foreign'])->comment('Type of payment: local or foreign');
            $table->string('beneficiary_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number', 100)->nullable();
            $table->string('swift_code', 20)->nullable();
            $table->string('branch_code', 50)->nullable();
            $table->string('branch_name')->nullable();
            $table->text('bank_address')->nullable();
            $table->string('currency', 10)->nullable();
            $table->text('additional_info')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['edition_id', 'payment_type'], 'idx_edition_payment_type');
            $table->index(['conference_id', 'payment_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_information');
    }
};
