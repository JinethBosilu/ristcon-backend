<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration adds foreign key constraints and indexes to edition_id columns.
     * 
     * This is separated from the column addition migration to allow for data backfilling
     * between migrations. The sequence should be:
     * 1. Create conference_editions table
     * 2. Add edition_id columns (nullable)
     * 3. Run seeder to backfill data
     * 4. Run this migration to add constraints
     * 
     * IMPORTANT: Run the EditionDataMigrationSeeder BEFORE running this migration!
     */
    public function up(): void
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
                // Make edition_id NOT NULL after backfilling
                $table->unsignedBigInteger('edition_id')
                    ->nullable(false)
                    ->change();
                
                // Add foreign key constraint
                $table->foreign('edition_id', $tableName . '_edition_id_foreign')
                    ->references('id')
                    ->on('conference_editions')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
                
                // Add index for performance
                // Index name shortened to avoid length limits
                $shortTableName = substr(str_replace('_', '', $tableName), 0, 10);
                $table->index('edition_id', 'idx_edition_' . $shortTableName);
            });
        }

        // Add composite indexes for frequently queried combinations
        Schema::table('important_dates', function (Blueprint $table) {
            $table->index(['edition_id', 'date_type'], 'idx_edition_date_type');
        });

        Schema::table('speakers', function (Blueprint $table) {
            $table->index(['edition_id', 'speaker_type'], 'idx_edition_speaker_type');
        });

        Schema::table('committee_members', function (Blueprint $table) {
            $table->index(['edition_id', 'committee_type_id'], 'idx_edition_committee_type');
        });

        Schema::table('conference_documents', function (Blueprint $table) {
            $table->index(['edition_id', 'document_category', 'is_active'], 'idx_edition_doc_category');
        });

        Schema::table('conference_assets', function (Blueprint $table) {
            $table->index(['edition_id', 'asset_type'], 'idx_edition_asset_type');
        });

        Schema::table('research_categories', function (Blueprint $table) {
            $table->index(['edition_id', 'category_code'], 'idx_edition_category_code');
        });

        Schema::table('registration_fees', function (Blueprint $table) {
            $table->index(['edition_id', 'attendee_type'], 'idx_edition_attendee_type');
        });

        Schema::table('payment_information', function (Blueprint $table) {
            $table->index(['edition_id', 'payment_type'], 'idx_edition_payment_type');
        });

        Schema::table('payment_policies', function (Blueprint $table) {
            $table->index(['edition_id', 'policy_type'], 'idx_edition_policy_type');
        });

        Schema::table('social_media_links', function (Blueprint $table) {
            $table->index(['edition_id', 'is_active', 'display_order'], 'idx_edition_social_active');
        });
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

        // Drop composite indexes first
        $compositeIndexes = [
            'important_dates' => ['idx_edition_date_type'],
            'speakers' => ['idx_edition_speaker_type'],
            'committee_members' => ['idx_edition_committee_type'],
            'conference_documents' => ['idx_edition_doc_category'],
            'conference_assets' => ['idx_edition_asset_type'],
            'research_categories' => ['idx_edition_category_code'],
            'registration_fees' => ['idx_edition_attendee_type'],
            'payment_information' => ['idx_edition_payment_type'],
            'payment_policies' => ['idx_edition_policy_type'],
            'social_media_links' => ['idx_edition_social_active'],
        ];

        foreach ($compositeIndexes as $tableName => $indexes) {
            Schema::table($tableName, function (Blueprint $table) use ($indexes) {
                foreach ($indexes as $indexName) {
                    try {
                        $table->dropIndex($indexName);
                    } catch (\Exception $e) {
                        // Index might not exist
                    }
                }
            });
        }

        // Drop foreign keys and basic indexes
        foreach ($editionScopedTables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // Drop foreign key
                try {
                    $table->dropForeign($tableName . '_edition_id_foreign');
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
                
                // Drop index
                $shortTableName = substr(str_replace('_', '', $tableName), 0, 10);
                try {
                    $table->dropIndex('idx_edition_' . $shortTableName);
                } catch (\Exception $e) {
                    // Index might not exist
                }
                
                // Make column nullable again
                $table->unsignedBigInteger('edition_id')->nullable()->change();
            });
        }
    }
};
