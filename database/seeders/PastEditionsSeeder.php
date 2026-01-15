<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ConferenceEdition;
use Carbon\Carbon;

class PastEditionsSeeder extends Seeder
{
    /**
     * Seed historical conference editions (2020-2025) with legacy site URLs.
     * These editions use the old website architecture and link to external sites.
     */
    public function run(): void
    {
        $pastEditions = [
            [
                'year' => 2025,
                'edition_number' => 12,
                'name' => 'RISTCON 2025',
                'slug' => '2025',
                'status' => 'archived',
                'is_active_edition' => false,
                'conference_date' => '2025-01-15',
                'venue_type' => 'physical',
                'venue_location' => 'University of Ruhuna, Matara, Sri Lanka',
                'theme' => 'Innovation in Science and Technology for Sustainable Development',
                'description' => 'The 12th International Research Conference organized by the Faculty of Science, University of Ruhuna.',
                'general_email' => 'ristcon2025@ruh.ac.lk',
                'availability_hours' => 'Mon-Fri, 9AM-5PM',
                'copyright_year' => 2025,
                'site_version' => '2.0',
                'is_legacy_site' => true,
                'legacy_website_url' => 'https://ristcon.uom.lk/2025',
                'last_updated' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'year' => 2024,
                'edition_number' => 11,
                'name' => 'RISTCON 2024',
                'slug' => '2024',
                'status' => 'archived',
                'is_active_edition' => false,
                'conference_date' => '2024-01-20',
                'venue_type' => 'physical',
                'venue_location' => 'University of Ruhuna, Matara, Sri Lanka',
                'theme' => 'Research and Innovation for a Better Tomorrow',
                'description' => 'The 11th International Research Conference organized by the Faculty of Science, University of Ruhuna.',
                'general_email' => 'ristcon2024@ruh.ac.lk',
                'availability_hours' => 'Mon-Fri, 9AM-5PM',
                'copyright_year' => 2024,
                'site_version' => '2.0',
                'is_legacy_site' => true,
                'legacy_website_url' => 'https://ristcon.uom.lk/2024',
                'last_updated' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'year' => 2023,
                'edition_number' => 10,
                'name' => 'RISTCON 2023',
                'slug' => '2023',
                'status' => 'archived',
                'is_active_edition' => false,
                'conference_date' => '2023-01-18',
                'venue_type' => 'hybrid',
                'venue_location' => 'University of Ruhuna, Matara, Sri Lanka',
                'theme' => 'Science and Technology in the Post-Pandemic Era',
                'description' => 'The 10th International Research Conference organized by the Faculty of Science, University of Ruhuna.',
                'general_email' => 'ristcon2023@ruh.ac.lk',
                'availability_hours' => 'Mon-Fri, 9AM-5PM',
                'copyright_year' => 2023,
                'site_version' => '2.0',
                'is_legacy_site' => true,
                'legacy_website_url' => 'https://ristcon.uom.lk/2023',
                'last_updated' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'year' => 2022,
                'edition_number' => 9,
                'name' => 'RISTCON 2022',
                'slug' => '2022',
                'status' => 'archived',
                'is_active_edition' => false,
                'conference_date' => '2022-01-22',
                'venue_type' => 'virtual',
                'venue_location' => null,
                'theme' => 'Virtual Collaboration in Scientific Research',
                'description' => 'The 9th International Research Conference organized by the Faculty of Science, University of Ruhuna.',
                'general_email' => 'ristcon2022@ruh.ac.lk',
                'availability_hours' => 'Mon-Fri, 9AM-5PM',
                'copyright_year' => 2022,
                'site_version' => '1.0',
                'is_legacy_site' => true,
                'legacy_website_url' => 'https://ristcon.uom.lk/2022',
                'last_updated' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'year' => 2021,
                'edition_number' => 8,
                'name' => 'RISTCON 2021',
                'slug' => '2021',
                'status' => 'archived',
                'is_active_edition' => false,
                'conference_date' => '2021-01-25',
                'venue_type' => 'virtual',
                'venue_location' => null,
                'theme' => 'Adapting Research to a Changing World',
                'description' => 'The 8th International Research Conference organized by the Faculty of Science, University of Ruhuna.',
                'general_email' => 'ristcon2021@ruh.ac.lk',
                'availability_hours' => 'Mon-Fri, 9AM-5PM',
                'copyright_year' => 2021,
                'site_version' => '1.0',
                'is_legacy_site' => true,
                'legacy_website_url' => 'https://ristcon.uom.lk/2021',
                'last_updated' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'year' => 2020,
                'edition_number' => 7,
                'name' => 'RISTCON 2020',
                'slug' => '2020',
                'status' => 'archived',
                'is_active_edition' => false,
                'conference_date' => '2020-01-20',
                'venue_type' => 'physical',
                'venue_location' => 'University of Ruhuna, Matara, Sri Lanka',
                'theme' => 'Science for Sustainable Development',
                'description' => 'The 7th International Research Conference organized by the Faculty of Science, University of Ruhuna.',
                'general_email' => 'ristcon2020@ruh.ac.lk',
                'availability_hours' => 'Mon-Fri, 9AM-5PM',
                'copyright_year' => 2020,
                'site_version' => '1.0',
                'is_legacy_site' => true,
                'legacy_website_url' => 'https://ristcon.uom.lk/2020',
                'last_updated' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($pastEditions as $edition) {
            ConferenceEdition::create($edition);
        }

        $this->command->info('Successfully seeded ' . count($pastEditions) . ' historical conference editions.');
    }
}
