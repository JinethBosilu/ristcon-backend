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
        Schema::table('registration_fees', function (Blueprint $table) {
            $table->foreign(['conference_id'])->references(['id'])->on('conferences')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['edition_id'])->references(['id'])->on('conference_editions')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_fees', function (Blueprint $table) {
            $table->dropForeign('registration_fees_conference_id_foreign');
            $table->dropForeign('registration_fees_edition_id_foreign');
        });
    }
};
