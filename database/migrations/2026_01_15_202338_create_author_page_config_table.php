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
        Schema::create('author_page_config', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('edition_id')->index('idx_edition_authorpage');
            $table->unsignedBigInteger('conference_id')->index('author_page_config_conference_id_foreign');
            $table->enum('conference_format', ['in_person', 'virtual', 'hybrid']);
            $table->string('cmt_url')->nullable();
            $table->string('submission_email')->nullable();
            $table->boolean('blind_review_enabled')->default(false);
            $table->boolean('camera_ready_required')->default(true);
            $table->text('special_instructions')->nullable();
            $table->text('acknowledgment_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('author_page_config');
    }
};
