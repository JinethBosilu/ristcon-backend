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
        Schema::table('conference_documents', function (Blueprint $table) {
            $table->foreign(['conference_id'])->references(['id'])->on('conferences')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['edition_id'])->references(['id'])->on('conference_editions')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_documents', function (Blueprint $table) {
            $table->dropForeign('conference_documents_conference_id_foreign');
            $table->dropForeign('conference_documents_edition_id_foreign');
        });
    }
};
