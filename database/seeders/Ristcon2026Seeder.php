<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Ristcon2026Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * RISTCON 2026 - 13th Edition Test Data
     */
    public function run(): void
    {
        // 1. Create Conference
        $conferenceId = DB::table('conferences')->insertGetId([
            'year' => 2026,
            'edition_number' => 13,
            'conference_date' => '2026-01-21',
            'venue_type' => 'physical',
            'venue_location' => 'University of Ruhuna, Matara, Sri Lanka',
            'theme' => 'Advancing Research Excellence in Science and Technology',
            'description' => 'The 13th International Research Conference organized by the Faculty of Science, University of Ruhuna, aims to provide a platform for researchers to present their innovative work and foster collaboration across various scientific disciplines.',
            'status' => 'upcoming',
            'general_email' => 'ristcon@ruh.ac.lk',
            'availability_hours' => 'Available Mon-Fri, 9AM-5PM',
            'last_updated' => now(),
            'copyright_year' => 2026,
            'site_version' => '3.0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Create Important Dates
        DB::table('important_dates')->insert([
            [
                'conference_id' => $conferenceId,
                'date_type' => 'submission_deadline',
                'date_value' => '2025-10-15',
                'is_extended' => false,
                'display_order' => 1,
                'display_label' => 'Abstract Submission Deadline',
                'notes' => 'Submit via Microsoft CMT',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'date_type' => 'notification',
                'date_value' => '2025-11-15',
                'is_extended' => false,
                'display_order' => 2,
                'display_label' => 'Notification of Acceptance',
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'date_type' => 'camera_ready',
                'date_value' => '2025-12-15',
                'is_extended' => false,
                'display_order' => 3,
                'display_label' => 'Camera-Ready Submission',
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'date_type' => 'conference_date',
                'date_value' => '2026-01-21',
                'is_extended' => false,
                'display_order' => 4,
                'display_label' => 'Conference Date',
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 3. Create Speakers
        DB::table('speakers')->insert([
            [
                'conference_id' => $conferenceId,
                'speaker_type' => 'keynote',
                'display_order' => 1,
                'full_name' => 'Prof. Michael Anderson',
                'title' => 'PhD, FIEEE',
                'affiliation' => 'Department of Computer Science, Stanford University, USA',
                'additional_affiliation' => 'Visiting Professor, University of Cambridge',
                'bio' => 'Prof. Michael Anderson is a leading expert in artificial intelligence and machine learning with over 25 years of research experience. He has published more than 200 papers in top-tier conferences and journals.',
                'photo_filename' => 'prof_anderson.jpg',
                'website_url' => 'https://stanford.edu/~anderson',
                'email' => 'm.anderson@stanford.edu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'speaker_type' => 'plenary',
                'display_order' => 1,
                'full_name' => 'Dr. Sarah Chen',
                'title' => 'PhD, FRS',
                'affiliation' => 'Institute of Biotechnology, National University of Singapore',
                'additional_affiliation' => null,
                'bio' => 'Dr. Sarah Chen specializes in molecular biology and genetic engineering. Her groundbreaking work on CRISPR applications has earned international recognition.',
                'photo_filename' => 'dr_chen.jpg',
                'website_url' => 'https://nus.edu.sg/~chen',
                'email' => 's.chen@nus.edu.sg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'speaker_type' => 'plenary',
                'display_order' => 2,
                'full_name' => 'Prof. Rajesh Kumar',
                'title' => 'PhD, FRSC',
                'affiliation' => 'Department of Physics, Indian Institute of Technology Delhi, India',
                'additional_affiliation' => null,
                'bio' => 'Prof. Rajesh Kumar is renowned for his contributions to quantum physics and nanotechnology. He has received numerous awards including the Shanti Swarup Bhatnagar Prize.',
                'photo_filename' => 'prof_kumar.jpg',
                'website_url' => 'https://iitd.ac.in/~kumar',
                'email' => 'r.kumar@iitd.ac.in',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 4. Create Committee Types
        $committeeTypes = [
            ['committee_name' => 'Advisory Board', 'display_order' => 1],
            ['committee_name' => 'Editorial Board', 'display_order' => 2],
            ['committee_name' => 'Organizing Committee', 'display_order' => 3],
        ];

        foreach ($committeeTypes as $type) {
            DB::table('committee_types')->insert([
                'committee_name' => $type['committee_name'],
                'display_order' => $type['display_order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Get committee type IDs
        $advisoryBoardId = DB::table('committee_types')->where('committee_name', 'Advisory Board')->value('id');
        $editorialBoardId = DB::table('committee_types')->where('committee_name', 'Editorial Board')->value('id');
        $organizingCommitteeId = DB::table('committee_types')->where('committee_name', 'Organizing Committee')->value('id');

        // 5. Create Committee Members - Advisory Board
        $advisoryMembers = [
            ['Prof. A.M. Jayasekara', 'Professor', 'Department of Physics', 'University of Colombo', 1],
            ['Prof. K.A.S. Abeysinghe', 'Professor', 'Department of Chemistry', 'University of Peradeniya', 2],
            ['Prof. N.D. Bulugahapitiya', 'Professor', 'Department of Mathematics', 'University of Kelaniya', 3],
            ['Prof. Archana Sharma', 'Professor', 'School of Physics', 'University of Delhi, India', 4],
            ['Dr. Ajith Karunaratne', 'Senior Lecturer', 'Department of Computer Science', 'University of Moratuwa', 5],
        ];

        foreach ($advisoryMembers as $member) {
            DB::table('committee_members')->insert([
                'conference_id' => $conferenceId,
                'committee_type_id' => $advisoryBoardId,
                'full_name' => $member[0],
                'designation' => $member[1],
                'department' => $member[2],
                'affiliation' => $member[3],
                'role' => 'Member',
                'country' => strpos($member[3], 'India') !== false ? 'India' : 'Sri Lanka',
                'is_international' => strpos($member[3], 'India') !== false,
                'display_order' => $member[4],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 6. Create Committee Members - Editorial Board
        $editorialMembers = [
            ['Dr. P.G.D. Jayasinghe', 'Senior Lecturer', 'Department of Botany', 'University of Ruhuna', 1],
            ['Dr. R.M.K. Ratnayake', 'Senior Lecturer', 'Department of Zoology', 'University of Ruhuna', 2],
            ['Dr. W.A.N.M. Wijesundara', 'Senior Lecturer', 'Department of Mathematics', 'University of Ruhuna', 3],
            ['Dr. H.M.T.G.A. Pitawala', 'Senior Lecturer', 'Department of Geology', 'University of Ruhuna', 4],
            ['Dr. K.H.M. Ashoka Deepananda', 'Senior Lecturer', 'Department of Oceanography', 'University of Ruhuna', 5],
            ['Dr. S.P. Kumara', 'Senior Lecturer', 'Department of Computer Science', 'University of Ruhuna', 6],
            ['Dr. A.M.P. Anuruddha', 'Senior Lecturer', 'Department of Physics', 'University of Ruhuna', 7],
        ];

        foreach ($editorialMembers as $member) {
            DB::table('committee_members')->insert([
                'conference_id' => $conferenceId,
                'committee_type_id' => $editorialBoardId,
                'full_name' => $member[0],
                'designation' => $member[1],
                'department' => $member[2],
                'affiliation' => $member[3],
                'role' => 'Editor',
                'country' => 'Sri Lanka',
                'is_international' => false,
                'display_order' => $member[4],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 7. Create Organizing Committee Members
        $organizingMembers = [
            ['Dr. Y.M.A.L.W. Yapa', 'Senior Lecturer', 'Department of Chemistry', 'University of Ruhuna', 'Chairperson', 'leadership', 1],
            ['Dr. H.W.M.A.C. Wijayasinghe', 'Senior Lecturer', 'Department of Computer Science', 'University of Ruhuna', 'Joint Secretary', 'leadership', 2],
            ['Dr. K.G.S.U. Ariyawansa', 'Senior Lecturer', 'Department of Mathematics', 'University of Ruhuna', 'Joint Secretary', 'leadership', 3],
            ['Dr. R.P.N.P. Rajapakse', 'Senior Lecturer', 'Department of Botany', 'University of Ruhuna', 'Member', 'department_rep', 4],
            ['Dr. W.M.G.I. Wijesinghe', 'Senior Lecturer', 'Department of Zoology', 'University of Ruhuna', 'Member', 'department_rep', 5],
            ['Dr. A.A.D. Amarathunga', 'Senior Lecturer', 'Department of Physics', 'University of Ruhuna', 'Member', 'department_rep', 6],
            ['Dr. M.H.F. Nawaz', 'Senior Lecturer', 'Department of Chemistry', 'University of Ruhuna', 'Member', 'department_rep', 7],
            ['Dr. P.L.K.S. Dharmaratne', 'Senior Lecturer', 'Department of Mathematics', 'University of Ruhuna', 'Member', 'department_rep', 8],
            ['Dr. S.M.N. Siriwardana', 'Lecturer', 'Department of Computer Science', 'University of Ruhuna', 'Member', 'department_rep', 9],
            ['Dr. H.M.D.P. Herath', 'Lecturer', 'Department of Statistics', 'University of Ruhuna', 'Member', 'department_rep', 10],
            ['Dr. R.A.T.M. Rajapaksha', 'Lecturer', 'Department of Geography', 'University of Ruhuna', 'Member', 'department_rep', 11],
        ];

        foreach ($organizingMembers as $member) {
            DB::table('committee_members')->insert([
                'conference_id' => $conferenceId,
                'committee_type_id' => $organizingCommitteeId,
                'full_name' => $member[0],
                'designation' => $member[1],
                'department' => $member[2],
                'affiliation' => $member[3],
                'role' => $member[4],
                'role_category' => $member[5],
                'country' => 'Sri Lanka',
                'is_international' => false,
                'display_order' => $member[6],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 8. Create Contact Persons
        DB::table('contact_persons')->insert([
            [
                'conference_id' => $conferenceId,
                'full_name' => 'Dr. Y.M.A.L.W. Yapa',
                'role' => 'Chairperson',
                'department' => 'Department of Chemistry',
                'mobile' => '+94 71 234 5678',
                'phone' => '+94 41 222 7000',
                'email' => 'yapa@che.ruh.ac.lk',
                'address' => 'Department of Chemistry, Faculty of Science, University of Ruhuna, Matara, Sri Lanka',
                'display_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'full_name' => 'Dr. H.W.M.A.C. Wijayasinghe',
                'role' => 'Joint Secretary',
                'department' => 'Department of Computer Science',
                'mobile' => '+94 77 345 6789',
                'phone' => '+94 41 222 7001',
                'email' => 'wijayasinghe@dcs.ruh.ac.lk',
                'address' => 'Department of Computer Science, Faculty of Science, University of Ruhuna, Matara, Sri Lanka',
                'display_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 9. Create Documents
        DB::table('conference_documents')->insert([
            [
                'conference_id' => $conferenceId,
                'document_category' => 'abstract_template',
                'file_name' => 'Abstract_Template_RISTCON2026.docx',
                'file_path' => 'documents/2026/Abstract_Template_RISTCON2026.docx',
                'display_name' => 'Download Abstract Template',
                'is_available' => true,
                'button_width_percent' => 70,
                'display_order' => 1,
                'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'file_size' => 45678,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'document_category' => 'author_form',
                'file_name' => 'Author_Declaration_Form_RISTCON2026.pdf',
                'file_path' => 'documents/2026/Author_Declaration_Form_RISTCON2026.pdf',
                'display_name' => 'Author Declaration Form',
                'is_available' => true,
                'button_width_percent' => 70,
                'display_order' => 2,
                'mime_type' => 'application/pdf',
                'file_size' => 123456,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'document_category' => 'registration_form',
                'file_name' => 'Registration_Form_RISTCON2026.pdf',
                'file_path' => 'documents/2026/Registration_Form_RISTCON2026.pdf',
                'display_name' => 'Download Registration Form',
                'is_available' => false,
                'button_width_percent' => 70,
                'display_order' => 3,
                'mime_type' => 'application/pdf',
                'file_size' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'document_category' => 'camera_ready_template',
                'file_name' => 'Camera_Ready_Template_RISTCON2026.docx',
                'file_path' => 'documents/2026/Camera_Ready_Template_RISTCON2026.docx',
                'display_name' => 'Template for Camera Ready Submission',
                'is_available' => true,
                'button_width_percent' => 70,
                'display_order' => 4,
                'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'file_size' => 52341,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'document_category' => 'flyer',
                'file_name' => 'RISTCON2026_Flyer.pdf',
                'file_path' => 'documents/2026/RISTCON2026_Flyer.pdf',
                'display_name' => 'Conference Flyer',
                'is_available' => true,
                'button_width_percent' => 70,
                'display_order' => 5,
                'mime_type' => 'application/pdf',
                'file_size' => 987654,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 10. Create Assets
        DB::table('conference_assets')->insert([
            [
                'conference_id' => $conferenceId,
                'asset_type' => 'logo',
                'file_name' => 'ristcon_2026_logo.png',
                'file_path' => 'assets/2026/ristcon_2026_logo.png',
                'alt_text' => 'RISTCON 2026 Logo',
                'usage_context' => 'main_logo',
                'mime_type' => 'image/png',
                'file_size' => 234567,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'asset_type' => 'poster',
                'file_name' => 'ristcon_2026_poster.jpg',
                'file_path' => 'assets/2026/ristcon_2026_poster.jpg',
                'alt_text' => 'RISTCON 2026 Conference Poster',
                'usage_context' => 'main_poster',
                'mime_type' => 'image/jpeg',
                'file_size' => 567890,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 11. Create Research Categories
        $categories = [
            ['A', 'Life Sciences', 'Biological and health sciences research', 1],
            ['B', 'Physical and Chemical Sciences', 'Physics, Chemistry, and related disciplines', 2],
            ['C', 'Mathematical and Statistical Sciences', 'Mathematics, Statistics, and Computational Sciences', 3],
            ['D', 'Computer Science and Information Technology', 'Computing, IT, and Digital Technologies', 4],
            ['E', 'Social Sciences and Humanities', 'Geography, Economics, and Social Studies', 5],
        ];

        foreach ($categories as $cat) {
            DB::table('research_categories')->insert([
                'conference_id' => $conferenceId,
                'category_code' => $cat[0],
                'category_name' => $cat[1],
                'description' => $cat[2],
                'display_order' => $cat[3],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 12. Create Research Areas (sample for each category)
        // Category A: Life Sciences
        $categoryAId = DB::table('research_categories')->where('category_code', 'A')->value('id');
        $areasA = [
            ['Biochemistry', ['Clinical Biochemistry'], 1],
            ['Botany', ['Plant Biology'], 2],
            ['Microbiology', [], 3],
            ['Molecular Biology', [], 4],
            ['Zoology', ['Animal Science'], 5],
            ['Environmental Biology', [], 6],
        ];

        foreach ($areasA as $area) {
            DB::table('research_areas')->insert([
                'category_id' => $categoryAId,
                'area_name' => $area[0],
                'alternate_names' => json_encode($area[1]),
                'display_order' => $area[2],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Category B: Physical and Chemical Sciences
        $categoryBId = DB::table('research_categories')->where('category_code', 'B')->value('id');
        $areasB = [
            ['Chemistry', ['Organic Chemistry', 'Inorganic Chemistry'], 1],
            ['Physics', ['Applied Physics'], 2],
            ['Material Science', [], 3],
        ];

        foreach ($areasB as $area) {
            DB::table('research_areas')->insert([
                'category_id' => $categoryBId,
                'area_name' => $area[0],
                'alternate_names' => json_encode($area[1]),
                'display_order' => $area[2],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Category C: Mathematical and Statistical Sciences
        $categoryCId = DB::table('research_categories')->where('category_code', 'C')->value('id');
        $areasC = [
            ['Mathematics', ['Pure Mathematics', 'Applied Mathematics'], 1],
            ['Statistics', ['Biostatistics'], 2],
            ['Operations Research', [], 3],
        ];

        foreach ($areasC as $area) {
            DB::table('research_areas')->insert([
                'category_id' => $categoryCId,
                'area_name' => $area[0],
                'alternate_names' => json_encode($area[1]),
                'display_order' => $area[2],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Category D: Computer Science
        $categoryDId = DB::table('research_categories')->where('category_code', 'D')->value('id');
        $areasD = [
            ['Artificial Intelligence', ['Machine Learning', 'Deep Learning'], 1],
            ['Software Engineering', [], 2],
            ['Data Science', ['Big Data Analytics'], 3],
            ['Cyber Security', ['Information Security'], 4],
        ];

        foreach ($areasD as $area) {
            DB::table('research_areas')->insert([
                'category_id' => $categoryDId,
                'area_name' => $area[0],
                'alternate_names' => json_encode($area[1]),
                'display_order' => $area[2],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Category E: Social Sciences
        $categoryEId = DB::table('research_categories')->where('category_code', 'E')->value('id');
        $areasE = [
            ['Geography', ['Human Geography', 'Physical Geography'], 1],
            ['Economics', [], 2],
            ['Social Studies', [], 3],
        ];

        foreach ($areasE as $area) {
            DB::table('research_areas')->insert([
                'category_id' => $categoryEId,
                'area_name' => $area[0],
                'alternate_names' => json_encode($area[1]),
                'display_order' => $area[2],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 13. Create Event Location
        DB::table('event_locations')->insert([
            'conference_id' => $conferenceId,
            'venue_name' => 'University of Ruhuna',
            'full_address' => 'Faculty of Science, University of Ruhuna, Matara, Sri Lanka',
            'city' => 'Matara',
            'country' => 'Sri Lanka',
            'latitude' => 5.93971600,
            'longitude' => 80.57613400,
            'google_maps_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3967.234!2d80.576134!3d5.939716!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1',
            'google_maps_link' => 'https://goo.gl/maps/xyz123abc',
            'is_virtual' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 14. Create Author Page Configuration
        DB::table('author_page_config')->insert([
            'conference_id' => $conferenceId,
            'conference_format' => 'in_person',
            'cmt_url' => 'https://cmt3.research.microsoft.com/User/Login?ReturnUrl=%2FRISTCON2026',
            'submission_email' => 'ristcon2026@sci.ruh.ac.lk',
            'blind_review_enabled' => true,
            'camera_ready_required' => true,
            'special_instructions' => 'No identifying information should be included in the abstract/extended abstract, its file name (e.g., avoid using names like \'Abstract_James\'), or in the content. File formats: .docx or .doc only. PDF allowed if created by LaTeX/TeX. No acknowledgements in initial submission. Once the abstract is approved, you can include the acknowledgement (if any) in the final submission. Only one figure and one table could be included (if any) in the extended abstract. Reviews will not be accepting.',
            'acknowledgment_text' => 'The Microsoft CMT service was used for managing the peer-reviewing process for this conference. This service was provided for free by Microsoft and they bore all expenses, including costs for Azure cloud services as well as for software development and support.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 15. Create Submission Methods
        DB::table('submission_methods')->insert([
            [
                'conference_id' => $conferenceId,
                'document_type' => 'author_info',
                'submission_method' => 'email',
                'email_address' => 'ristcon2026@sci.ruh.ac.lk',
                'notes' => 'Email the Author Information Form to ristcon2026@sci.ruh.ac.lk',
                'display_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'document_type' => 'abstract',
                'submission_method' => 'cmt_upload',
                'email_address' => null,
                'notes' => 'Upload abstract via Microsoft CMT system',
                'display_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'document_type' => 'extended_abstract',
                'submission_method' => 'cmt_upload',
                'email_address' => null,
                'notes' => 'Upload camera-ready extended abstract via CMT',
                'display_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 16. Create Presentation Guidelines
        DB::table('presentation_guidelines')->insert([
            [
                'conference_id' => $conferenceId,
                'presentation_type' => 'oral',
                'duration_minutes' => 15,
                'presentation_minutes' => 10,
                'qa_minutes' => 5,
                'poster_width' => null,
                'poster_height' => null,
                'poster_unit' => null,
                'poster_orientation' => null,
                'physical_presence_required' => true,
                'detailed_requirements' => 'Oral presentations are limited to 15 minutes total (10 minutes presentation + 5 minutes Q&A). Presenters must bring their own laptops. Projector and audio system will be provided.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'presentation_type' => 'poster',
                'duration_minutes' => null,
                'presentation_minutes' => null,
                'qa_minutes' => null,
                'poster_width' => 27.00,
                'poster_height' => 40.00,
                'poster_unit' => 'inches',
                'poster_orientation' => 'portrait',
                'physical_presence_required' => true,
                'detailed_requirements' => 'Size: 27" × 40" (Portrait). Digitally printed. Reference number displayed at top-left. Include title, author(s), and affiliation(s) as per accepted abstract. Sections: Abstract, Introduction, Methodology, Results, Discussion/Conclusion, References. Text must be legible from 1–1.5 meters. Use enlarged figures, graphs, or photos; minimize tables. Each visual must have a descriptive title. Design should be self-explanatory for viewers.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 17. Create Payment Information
        DB::table('payment_information')->insert([
            [
                'conference_id' => $conferenceId,
                'payment_type' => 'local',
                'beneficiary_name' => 'University of Ruhuna',
                'bank_name' => 'Peoples Bank',
                'account_number' => '032-1-001-1-2477589',
                'swift_code' => null,
                'branch_code' => null,
                'branch_name' => 'Uyanwatta Road, Matara',
                'bank_address' => 'University of Ruhuna, Matara, Sri Lanka',
                'currency' => 'LKR',
                'additional_info' => null,
                'display_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'payment_type' => 'foreign',
                'beneficiary_name' => 'University of Ruhuna',
                'bank_name' => 'Peoples Bank',
                'account_number' => '032-1-001-1-2477589',
                'swift_code' => 'PSBKLKLX',
                'branch_code' => null,
                'branch_name' => 'Uyanwatta Road, Matara',
                'bank_address' => null,
                'currency' => 'USD',
                'additional_info' => null,
                'display_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 18. Create Registration Fees
        DB::table('registration_fees')->insert([
            [
                'conference_id' => $conferenceId,
                'attendee_type' => 'Foreign Attendees',
                'currency' => 'USD',
                'amount' => 50.00,
                'early_bird_amount' => null,
                'early_bird_deadline' => null,
                'display_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'attendee_type' => 'Local Attendees',
                'currency' => 'LKR',
                'amount' => 2500.00,
                'early_bird_amount' => null,
                'early_bird_deadline' => null,
                'display_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 19. Create Payment Policies
        DB::table('payment_policies')->insert([
            [
                'conference_id' => $conferenceId,
                'policy_text' => 'Presenters who make payments outside Sri Lanka should pay in USD.',
                'policy_type' => 'requirement',
                'is_highlighted' => false,
                'display_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'policy_text' => 'All bank charges should be borne by the presenters making the payment.',
                'policy_type' => 'requirement',
                'is_highlighted' => false,
                'display_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'policy_text' => 'Registration fees are non-refundable.',
                'policy_type' => 'restriction',
                'is_highlighted' => true,
                'display_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 20. Create Social Media Links
        DB::table('social_media_links')->insert([
            [
                'conference_id' => $conferenceId,
                'platform' => 'facebook',
                'url' => 'https://www.facebook.com/ristcon',
                'label' => 'Facebook',
                'display_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'platform' => 'twitter',
                'url' => 'https://www.twitter.com/ristcon',
                'label' => 'Twitter',
                'display_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'platform' => 'linkedin',
                'url' => 'https://www.linkedin.com/company/ristcon',
                'label' => 'LinkedIn',
                'display_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'conference_id' => $conferenceId,
                'platform' => 'email',
                'url' => 'mailto:info@ristcon2026.lk',
                'label' => 'Email',
                'display_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('RISTCON 2026 test data seeded successfully!');
    }
}
