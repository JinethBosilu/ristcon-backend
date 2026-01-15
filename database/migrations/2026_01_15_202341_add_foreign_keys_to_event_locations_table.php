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
        Schema::table('event_locations', function (Blueprint $table) {
            $table->foreign(['conference_id'])->references(['id'])->on('conferences')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['edition_id'])->references(['id'])->on('conference_editions')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_locations', function (Blueprint $table) {
            $table->dropForeign('event_locations_conference_id_foreign');
            $table->dropForeign('event_locations_edition_id_foreign');
        });
    }
};
