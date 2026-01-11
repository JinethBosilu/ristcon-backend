<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\Conference;
use App\Models\ConferenceEdition;

class EditionDataMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder migrates existing conference data to the new multi-edition structure.
     * 
     * CRITICAL EXECUTION ORDER:
     * 1. Run migration: create_conference_editions_table
     * 2. Run migration: add_edition_id_to_scoped_tables
     * 3. Run this seeder
     * 4. Run migration: add_edition_foreign_keys_and_indexes
     * 
     * What this seeder does:
     * 1. Copies data from conferences table to conference_editions
     * 2. Backfills edition_id in all related tables based on conference_id
     * 3. Marks the 2026 edition as active
     * 4. Validates data integrity
     */
    public function run(): void
    {
        $this->command->info('Starting edition data migration...');

        DB::beginTransaction();

        try {
            // Step 1: Migrate conferences to conference_editions
            $this->migrateConferences();

            // Step 2: Backfill edition_id in all related tables
            $this->backfillEditionIds();

            // Step 3: Validate data integrity
            $this->validateMigration();

            DB::commit();

            $this->command->info('✓ Edition data migration completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('✗ Migration failed: ' . $e->getMessage());
            Log::error('Edition migration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Migrate data from conferences table to conference_editions table.
     */
    private function migrateConferences(): void
    {
        $this->command->info('Migrating conferences to conference_editions...');

        $conferences = DB::table('conferences')->get();

        if ($conferences->isEmpty()) {
            $this->command->warn('No conferences found to migrate.');
            return;
        }

        foreach ($conferences as $conference) {
            // Check if already migrated
            $exists = DB::table('conference_editions')
                ->where('year', $conference->year)
                ->exists();

            if ($exists) {
                $this->command->warn("Edition {$conference->year} already exists, skipping...");
                continue;
            }

            // Determine status based on conference_date
            $status = $this->determineStatus($conference);

            // Create slug from year
            $slug = (string) $conference->year;

            // Insert into conference_editions
            $editionId = DB::table('conference_editions')->insertGetId([
                'year' => $conference->year,
                'edition_number' => $conference->edition_number,
                'name' => "RISTCON {$conference->year}",
                'slug' => $slug,
                'status' => $status,
                'is_active_edition' => $conference->year == 2026, // Make 2026 the active edition
                'conference_date' => $conference->conference_date,
                'venue_type' => $conference->venue_type,
                'venue_location' => $conference->venue_location,
                'theme' => $conference->theme,
                'description' => $conference->description,
                'general_email' => $conference->general_email,
                'availability_hours' => $conference->availability_hours,
                'copyright_year' => $conference->copyright_year,
                'site_version' => $conference->site_version ?? '1.0',
                'last_updated' => $conference->last_updated,
                'created_at' => $conference->created_at ?? now(),
                'updated_at' => $conference->updated_at ?? now(),
                'deleted_at' => $conference->deleted_at,
            ]);

            $this->command->info("✓ Migrated {$conference->year} (ID: {$editionId}) - Status: {$status}");
        }
    }

    /**
     * Determine the status of a conference based on its date.
     */
    private function determineStatus($conference): string
    {
        if ($conference->status === 'cancelled') {
            return 'cancelled';
        }

        $conferenceDate = \Carbon\Carbon::parse($conference->conference_date);
        $now = now();

        if ($conferenceDate->isFuture()) {
            return 'published'; // Upcoming conferences are published
        } elseif ($conferenceDate->isPast()) {
            return 'archived'; // Past conferences are archived
        } else {
            return 'published'; // Today's conference is published
        }
    }

    /**
     * Backfill edition_id in all related tables.
     */
    private function backfillEditionIds(): void
    {
        $this->command->info('Backfilling edition_id in related tables...');

        // Get mapping of conference_id to edition_id
        $conferenceToEditionMap = DB::table('conferences')
            ->join('conference_editions', 'conferences.year', '=', 'conference_editions.year')
            ->select('conferences.id as conference_id', 'conference_editions.id as edition_id')
            ->get()
            ->pluck('edition_id', 'conference_id')
            ->toArray();

        if (empty($conferenceToEditionMap)) {
            $this->command->warn('No conference-edition mapping found.');
            return;
        }

        // Tables to update
        $tables = [
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

        foreach ($tables as $table) {
            $this->backfillTable($table, $conferenceToEditionMap);
        }
    }

    /**
     * Backfill edition_id for a specific table.
     */
    private function backfillTable(string $table, array $conferenceToEditionMap): void
    {
        // Check if table exists and has conference_id column
        if (!Schema::hasTable($table)) {
            $this->command->warn("Table {$table} does not exist, skipping...");
            return;
        }

        if (!Schema::hasColumn($table, 'conference_id')) {
            $this->command->warn("Table {$table} does not have conference_id column, skipping...");
            return;
        }

        if (!Schema::hasColumn($table, 'edition_id')) {
            $this->command->warn("Table {$table} does not have edition_id column, skipping...");
            return;
        }

        // Build UPDATE query for each conference
        foreach ($conferenceToEditionMap as $conferenceId => $editionId) {
            $updated = DB::table($table)
                ->where('conference_id', $conferenceId)
                ->whereNull('edition_id') // Only update if not already set
                ->update(['edition_id' => $editionId]);

            if ($updated > 0) {
                $this->command->info("  ✓ {$table}: Updated {$updated} rows for edition {$editionId}");
            }
        }
    }

    /**
     * Validate the migration by checking data integrity.
     */
    private function validateMigration(): void
    {
        $this->command->info('Validating migration...');

        $errors = [];

        // Check 1: All conferences have corresponding editions
        $conferenceCount = DB::table('conferences')->count();
        $editionCount = DB::table('conference_editions')->count();

        if ($conferenceCount !== $editionCount) {
            $errors[] = "Conference count ({$conferenceCount}) doesn't match edition count ({$editionCount})";
        }

        // Check 2: All edition-scoped tables have edition_id populated
        $tables = [
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

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            if (!Schema::hasColumn($table, 'edition_id')) {
                continue;
            }

            $nullCount = DB::table($table)->whereNull('edition_id')->count();

            if ($nullCount > 0) {
                $errors[] = "Table {$table} has {$nullCount} rows with NULL edition_id";
            }
        }

        // Check 3: Only one active edition exists
        $activeCount = DB::table('conference_editions')
            ->where('is_active_edition', true)
            ->count();

        if ($activeCount !== 1) {
            $errors[] = "Expected 1 active edition, found {$activeCount}";
        }

        // Check 4: Verify 2026 is the active edition
        $active2026 = DB::table('conference_editions')
            ->where('year', 2026)
            ->where('is_active_edition', true)
            ->exists();

        if (!$active2026) {
            $errors[] = "Edition 2026 is not marked as active";
        }

        // Report results
        if (!empty($errors)) {
            $this->command->error('Validation failed with the following errors:');
            foreach ($errors as $error) {
                $this->command->error("  ✗ {$error}");
            }
            throw new \Exception('Migration validation failed');
        }

        $this->command->info('✓ All validation checks passed!');
    }
}
