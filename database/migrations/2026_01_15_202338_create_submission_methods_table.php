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
        Schema::create('submission_methods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('edition_id')->index('idx_edition_submission');
            $table->unsignedBigInteger('conference_id')->index('submission_methods_conference_id_foreign');
            $table->enum('document_type', ['author_info', 'abstract', 'extended_abstract', 'camera_ready', 'other']);
            $table->enum('submission_method', ['email', 'cmt_upload', 'online_form', 'postal']);
            $table->string('email_address')->nullable();
            $table->text('notes')->nullable();
            $table->integer('display_order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_methods');
    }
};
