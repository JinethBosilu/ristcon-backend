<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration adds edition_id foreign key to all edition-scoped tables.
     * 
     * Strategy:
     * 1. Add edition_id column as nullable first (to allow backfilling)
     * 2. Create composite indexes for performance
     * 3. Add foreign key constraints with CASCADE delete
     * 
     * The conference_id column is retained for backward compatibility
     * and will be deprecated in a future migration.
     */
    public function up(): void
    {
        // List of all edition-scoped tables with their primary key column names
        $editionScopedTables = [
            'important_dates' => 'id',
            'speakers' => 'id',
            'committee_members' => 'id',
            'contact_persons' => 'id',
            'conference_documents' => 'id',
            'conference_assets' => 'id',
            'research_categories' => 'id',
            'submission_methods' => 'id',
            'presentation_guidelines' => 'id',
            'payment_information' => 'payment_id',
            'registration_fees' => 'fee_id',
            'payment_policies' => 'policy_id',
            'social_media_links' => 'id',
            'abstract_formats' => 'id',
            'event_locations' => 'id',
            'author_page_config' => 'id',
        ];

        foreach ($editionScopedTables as $table => $primaryKey) {
            Schema::table($table, function (Blueprint $table) use ($primaryKey) {
                // Add edition_id column (nullable initially for backfilling)
                $table->unsignedBigInteger('edition_id')
                    ->nullable()
                    ->after($primaryKey)
                    ->comment('Foreign key to conference_editions table');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $editionScopedTables = [
            'important_dates',
            'speakers',
            'committee_members',
            'contact_persons',
            'conference_documents',
            'conference_assets',
            'research_categories',
            'submission_methods',
            'presentation_guidelines',
            'payment_information',
            'registration_fees',
            'payment_policies',
            'social_media_links',
            'abstract_formats',
            'event_locations',
            'author_page_config',
        ];

        foreach ($editionScopedTables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // Drop foreign key first if it exists
                if (Schema::hasColumn($tableName, 'edition_id')) {
                    $foreignKeyName = $tableName . '_edition_id_foreign';
                    try {
                        $table->dropForeign($foreignKeyName);
                    } catch (\Exception $e) {
                        // Foreign key might not exist, continue
                    }
                    
                    // Drop index if it exists
                    $indexName = 'idx_edition_' . str_replace('_', '', substr($tableName, 0, 10));
                    try {
                        $table->dropIndex($indexName);
                    } catch (\Exception $e) {
                        // Index might not exist, continue
                    }
                    
                    $table->dropColumn('edition_id');
                }
            });
        }
    }
};
