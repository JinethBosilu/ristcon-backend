-- RISTCON Database Complete Schema
-- Run this after: php artisan migrate

-- Conferences table
ALTER TABLE conferences 
ADD COLUMN year INT NOT NULL UNIQUE COMMENT 'Conference year',
ADD COLUMN edition_number INT NOT NULL,
ADD COLUMN conference_date DATE NOT NULL,
ADD COLUMN venue_type ENUM('virtual', 'physical', 'hybrid') NOT NULL,
ADD COLUMN venue_location VARCHAR(255),
ADD COLUMN theme TEXT NOT NULL,
ADD COLUMN description TEXT,
ADD COLUMN status ENUM('upcoming', 'ongoing', 'completed') DEFAULT 'upcoming',
ADD COLUMN general_email VARCHAR(255),
ADD COLUMN last_updated TIMESTAMP NULL DEFAULT NULL,
ADD COLUMN copyright_year INT,
ADD COLUMN site_version VARCHAR(20),
ADD COLUMN deleted_at TIMESTAMP NULL,
ADD INDEX idx_year_status(year, status);

-- Important dates table
ALTER TABLE important_dates
ADD COLUMN conference_id BIGINT UNSIGNED NOT NULL AFTER id,
ADD COLUMN date_type ENUM('submission_deadline', 'notification', 'camera_ready', 'conference_date', 'registration_deadline') NOT NULL,
ADD COLUMN date_value DATE NOT NULL,
ADD COLUMN is_extended BOOLEAN DEFAULT FALSE,
ADD COLUMN original_date DATE NULL,
ADD COLUMN display_order INT,
ADD COLUMN display_label VARCHAR(255),
ADD COLUMN notes TEXT,
ADD COLUMN deleted_at TIMESTAMP NULL,
ADD INDEX idx_conf_type (conference_id, date_type),
ADD CONSTRAINT fk_dates_conference FOREIGN KEY (conference_id) REFERENCES conferences(id) ON DELETE CASCADE;

-- Speakers table
ALTER TABLE speakers
ADD COLUMN conference_id BIGINT UNSIGNED NOT NULL AFTER id,
ADD COLUMN speaker_type ENUM('keynote', 'plenary', 'invited') NOT NULL,
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
ADD INDEX idx_conf_speaker (conference_id, speaker_type),
ADD CONSTRAINT fk_speakers_conference FOREIGN KEY (conference_id) REFERENCES conferences(id) ON DELETE CASCADE;

-- Committee types table
ALTER TABLE committee_types
ADD COLUMN committee_name VARCHAR(100) NOT NULL UNIQUE AFTER id,
ADD COLUMN display_order INT;

-- Committee members table
ALTER TABLE committee_members
ADD COLUMN conference_id BIGINT UNSIGNED NOT NULL AFTER id,
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
ADD INDEX idx_conf_type (conference_id, committee_type_id),
ADD CONSTRAINT fk_members_conference FOREIGN KEY (conference_id) REFERENCES conferences(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_members_type FOREIGN KEY (committee_type_id) REFERENCES committee_types(id) ON DELETE CASCADE;

-- Contact persons table
ALTER TABLE contact_persons
ADD COLUMN conference_id BIGINT UNSIGNED NOT NULL AFTER id,
ADD COLUMN full_name VARCHAR(255) NOT NULL,
ADD COLUMN role VARCHAR(100) NOT NULL,
ADD COLUMN department VARCHAR(255),
ADD COLUMN mobile VARCHAR(20),
ADD COLUMN phone VARCHAR(50),
ADD COLUMN email VARCHAR(255),
ADD COLUMN address TEXT,
ADD COLUMN display_order INT,
ADD COLUMN deleted_at TIMESTAMP NULL,
ADD INDEX idx_conference (conference_id),
ADD CONSTRAINT fk_contacts_conference FOREIGN KEY (conference_id) REFERENCES conferences(id) ON DELETE CASCADE;

-- Conference documents table
ALTER TABLE conference_documents
ADD COLUMN conference_id BIGINT UNSIGNED NOT NULL AFTER id,
ADD COLUMN document_category ENUM('author_form', 'abstract_template', 'registration_form', 'declaration_form', 'programme', 'proceedings', 'instructions', 'presentation_guide', 'camera_ready_template', 'poster', 'flyer') NOT NULL,
ADD COLUMN file_name VARCHAR(255) NOT NULL,
ADD COLUMN file_path VARCHAR(500) NOT NULL,
ADD COLUMN display_name VARCHAR(255) NOT NULL,
ADD COLUMN is_available BOOLEAN DEFAULT TRUE,
ADD COLUMN button_width_percent INT DEFAULT 70,
ADD COLUMN display_order INT,
ADD COLUMN mime_type VARCHAR(100),
ADD COLUMN file_size INT,
ADD COLUMN deleted_at TIMESTAMP NULL,
ADD INDEX idx_conf_category (conference_id, document_category, is_available),
ADD CONSTRAINT fk_docs_conference FOREIGN KEY (conference_id) REFERENCES conferences(id) ON DELETE CASCADE;

-- Conference assets table
ALTER TABLE conference_assets
ADD COLUMN conference_id BIGINT UNSIGNED NOT NULL AFTER id,
ADD COLUMN asset_type ENUM('logo', 'banner', 'poster', 'speaker_photo', 'icon', 'location_map', 'flyer', 'thumbnail') NOT NULL,
ADD COLUMN file_name VARCHAR(255) NOT NULL,
ADD COLUMN file_path VARCHAR(500) NOT NULL,
ADD COLUMN alt_text VARCHAR(255),
ADD COLUMN usage_context VARCHAR(255),
ADD COLUMN mime_type VARCHAR(100),
ADD COLUMN file_size INT,
ADD COLUMN deleted_at TIMESTAMP NULL,
ADD INDEX idx_conf_type (conference_id, asset_type),
ADD CONSTRAINT fk_assets_conference FOREIGN KEY (conference_id) REFERENCES conferences(id) ON DELETE CASCADE;

-- Research categories table
ALTER TABLE research_categories
ADD COLUMN conference_id BIGINT UNSIGNED NOT NULL AFTER id,
ADD COLUMN category_code VARCHAR(10),
ADD COLUMN category_name VARCHAR(255) NOT NULL,
ADD COLUMN description TEXT,
ADD COLUMN display_order INT NOT NULL,
ADD COLUMN is_active BOOLEAN DEFAULT TRUE,
ADD UNIQUE KEY uniq_conf_code (conference_id, category_code),
ADD INDEX idx_conf_code (conference_id, category_code),
ADD CONSTRAINT fk_categories_conference FOREIGN KEY (conference_id) REFERENCES conferences(id) ON DELETE CASCADE;

-- Research areas table
ALTER TABLE research_areas
ADD COLUMN category_id BIGINT UNSIGNED NOT NULL AFTER id,
ADD COLUMN area_name VARCHAR(255) NOT NULL,
ADD COLUMN alternate_names TEXT,
ADD COLUMN display_order INT,
ADD COLUMN is_active BOOLEAN DEFAULT TRUE,
ADD INDEX idx_category (category_id, is_active),
ADD INDEX idx_name (area_name),
ADD CONSTRAINT fk_areas_category FOREIGN KEY (category_id) REFERENCES research_categories(id) ON DELETE CASCADE;

-- Event locations table
ALTER TABLE event_locations
ADD COLUMN conference_id BIGINT UNSIGNED NOT NULL AFTER id,
ADD COLUMN venue_name VARCHAR(255) NOT NULL,
ADD COLUMN full_address TEXT,
ADD COLUMN city VARCHAR(100),
ADD COLUMN country VARCHAR(100),
ADD COLUMN latitude DECIMAL(10,8),
ADD COLUMN longitude DECIMAL(11,8),
ADD COLUMN google_maps_embed_url TEXT,
ADD COLUMN google_maps_link VARCHAR(500),
ADD COLUMN is_virtual BOOLEAN DEFAULT FALSE,
ADD COLUMN deleted_at TIMESTAMP NULL,
ADD INDEX idx_conference (conference_id),
ADD CONSTRAINT fk_location_conference FOREIGN KEY (conference_id) REFERENCES conferences(id) ON DELETE CASCADE;

-- Author page config table
ALTER TABLE author_page_config
ADD COLUMN conference_id BIGINT UNSIGNED NOT NULL AFTER id,
ADD COLUMN conference_format ENUM('virtual', 'in_person', 'hybrid') NOT NULL,
ADD COLUMN cmt_url VARCHAR(500) NOT NULL,
ADD COLUMN submission_email VARCHAR(255),
ADD COLUMN blind_review_enabled BOOLEAN DEFAULT FALSE,
ADD COLUMN camera_ready_required BOOLEAN DEFAULT TRUE,
ADD COLUMN special_instructions TEXT,
ADD COLUMN acknowledgment_text TEXT,
ADD COLUMN deleted_at TIMESTAMP NULL,
ADD INDEX idx_conference (conference_id),
ADD CONSTRAINT fk_author_config_conference FOREIGN KEY (conference_id) REFERENCES conferences(id) ON DELETE CASCADE;

-- Submission methods table
ALTER TABLE submission_methods
ADD COLUMN conference_id BIGINT UNSIGNED NOT NULL AFTER id,
ADD COLUMN document_type VARCHAR(100) NOT NULL,
ADD COLUMN submission_method ENUM('cmt_upload', 'email', 'both') NOT NULL,
ADD COLUMN email_address VARCHAR(255),
ADD COLUMN notes TEXT,
ADD COLUMN display_order INT,
ADD INDEX idx_conf_type (conference_id, document_type),
ADD CONSTRAINT fk_submission_conference FOREIGN KEY (conference_id) REFERENCES conferences(id) ON DELETE CASCADE;

-- Presentation guidelines table
ALTER TABLE presentation_guidelines
ADD COLUMN conference_id BIGINT UNSIGNED NOT NULL AFTER id,
ADD COLUMN presentation_type ENUM('oral', 'poster') NOT NULL,
ADD COLUMN duration_minutes INT,
ADD COLUMN presentation_minutes INT,
ADD COLUMN qa_minutes INT,
ADD COLUMN poster_width DECIMAL(10,2),
ADD COLUMN poster_height DECIMAL(10,2),
ADD COLUMN poster_unit VARCHAR(10),
ADD COLUMN poster_orientation VARCHAR(20),
ADD COLUMN physical_presence_required BOOLEAN DEFAULT FALSE,
ADD COLUMN detailed_requirements TEXT,
ADD COLUMN deleted_at TIMESTAMP NULL,
ADD INDEX idx_conf_type (conference_id, presentation_type),
ADD CONSTRAINT fk_guidelines_conference FOREIGN KEY (conference_id) REFERENCES conferences(id) ON DELETE CASCADE;
