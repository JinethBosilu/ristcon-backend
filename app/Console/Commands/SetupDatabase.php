<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupDatabase extends Command
{
    protected $signature = 'ristcon:setup';
    protected $description = 'Setup RISTCON database with complete schema';

    public function handle()
    {
        $this->info('Setting up RISTCON database...');

        try {
            // Add columns to conferences table
            DB::statement('ALTER TABLE conferences 
                ADD COLUMN year INT NOT NULL UNIQUE COMMENT "Conference year",
                ADD COLUMN edition_number INT NOT NULL,
                ADD COLUMN conference_date DATE NOT NULL,
                ADD COLUMN venue_type ENUM("virtual", "physical", "hybrid") NOT NULL,
                ADD COLUMN venue_location VARCHAR(255),
                ADD COLUMN theme TEXT NOT NULL,
                ADD COLUMN description TEXT,
                ADD COLUMN status ENUM("upcoming", "ongoing", "completed") DEFAULT "upcoming",
                ADD COLUMN general_email VARCHAR(255),
                ADD COLUMN last_updated TIMESTAMP,
                ADD COLUMN copyright_year INT,
                ADD COLUMN site_version VARCHAR(20),
                ADD COLUMN deleted_at TIMESTAMP NULL');

            $this->info('✓ Conferences table configured');

            // Configure important_dates
            DB::statement('ALTER TABLE important_dates
                ADD COLUMN conference_id BIGINT UNSIGNED NOT NULL,
                ADD COLUMN date_type ENUM("submission_deadline", "notification", "camera_ready", "conference_date", "registration_deadline") NOT NULL,
                ADD COLUMN date_value DATE NOT NULL,
                ADD COLUMN is_extended BOOLEAN DEFAULT FALSE,
                ADD COLUMN original_date DATE,
                ADD COLUMN display_order INT,
                ADD COLUMN display_label VARCHAR(255),
                ADD COLUMN notes TEXT,
                ADD COLUMN deleted_at TIMESTAMP NULL,
                ADD FOREIGN KEY (conference_id) REFERENCES conferences(id) ON DELETE CASCADE');

            $this->info('✓ Important dates table configured');

            // Configure speakers
            DB::statement('ALTER TABLE speakers
                ADD COLUMN conference_id BIGINT UNSIGNED NOT NULL,
                ADD COLUMN speaker_type ENUM("keynote", "plenary", "invited") NOT NULL,
                ADD COLUMN display_order INT DEFAULT 1,
                ADD COLUMN full_name VARCHAR(255) NOT NULL,
                ADD COLUMN title VARCHAR(255),
                ADD COLUMN affiliation TEXT NOT NULL,
                ADD COLUMN additional_affiliation TEXT,
                ADD COLUMN bio TEXT,
                ADD COLUMN photo_filename VARCHAR(255),
                ADD COLUMN website_url VARCHAR(500),
                ADD COLUMN email VARCHAR(255),
                ADD COLUMN deleted_at TIMESTAMP NULL,
                ADD FOREIGN KEY (conference_id) REFERENCES conferences(id) ON DELETE CASCADE');

            $this->info('✓ Speakers table configured');

            //  Configure committee_types
            DB::statement('ALTER TABLE committee_types
                ADD COLUMN committee_name VARCHAR(100) NOT NULL UNIQUE,
                ADD COLUMN display_order INT');

            $this->info('✓ Committee types table configured');

            // Configure committee_members
            DB::statement('ALTER TABLE committee_members
                ADD COLUMN conference_id BIGINT UNSIGNED NOT NULL,
                ADD COLUMN committee_type_id BIGINT UNSIGNED NOT NULL,
                ADD COLUMN full_name VARCHAR(255) NOT NULL,
                ADD COLUMN designation VARCHAR(255),
                ADD COLUMN department VARCHAR(255),
                ADD COLUMN affiliation TEXT,
                ADD COLUMN role VARCHAR(100),
                ADD COLUMN role_category VARCHAR(100),
                ADD COLUMN country VARCHAR(100),
                ADD COLUMN is_international BOOLEAN DEFAULT FALSE,
                ADD COLUMN display_order INT,
                ADD COLUMN deleted_at TIMESTAMP NULL,
                ADD FOREIGN KEY (conference_id) REFERENCES conferences(id) ON DELETE CASCADE,
                ADD FOREIGN KEY (committee_type_id) REFERENCES committee_types(id) ON DELETE CASCADE');

            $this->info('✓ Committee members table configured');

            // Configure contact_persons
            DB::statement('ALTER TABLE contact_persons
                ADD COLUMN conference_id BIGINT UNSIGNED NOT NULL,
                ADD COLUMN full_name VARCHAR(255) NOT NULL,
                ADD COLUMN role VARCHAR(100) NOT NULL,
                ADD COLUMN department VARCHAR(255),
                ADD COLUMN mobile VARCHAR(20),
                ADD COLUMN phone VARCHAR(50),
                ADD COLUMN email VARCHAR(255),
                ADD COLUMN address TEXT,
                ADD COLUMN display_order INT,
                ADD COLUMN deleted_at TIMESTAMP NULL,
                ADD FOREIGN KEY (conference_id) REFERENCES conferences(id) ON DELETE CASCADE');

            $this->info('✓ Contact persons table configured');

            $this->info('Database setup completed successfully!');
            $this->info('Run: php artisan db:seed to populate with RISTCON 2026 test data');

            return 0;
        } catch (\Exception $e) {
            $this->error('Error setting up database: ' . $e->getMessage());
            return 1;
        }
    }
}
