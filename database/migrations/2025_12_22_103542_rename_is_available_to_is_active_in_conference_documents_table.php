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
            $table->renameColumn('is_available', 'is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_documents', function (Blueprint $table) {
            $table->renameColumn('is_active', 'is_available');
        });
    }
};
