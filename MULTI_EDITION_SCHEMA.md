# Multi-Edition Database Schema Implementation

## Overview

This document describes the implementation of a multi-edition database schema for the RISTCON conference management system. The new architecture supports unlimited conference years (2026, 2027, 2028+) while maintaining full backward compatibility with the existing frontend and API.

---

## Table of Contents

1. [Current Schema Analysis](#current-schema-analysis)
2. [New Schema Design](#new-schema-design)
3. [Migration Strategy](#migration-strategy)
4. [Backward Compatibility](#backward-compatibility)
5. [Implementation Guide](#implementation-guide)
6. [API Usage](#api-usage)
7. [Future Enhancements](#future-enhancements)

---

## Current Schema Analysis

### Existing Tables Classification

#### Global Tables (Shared Across All Editions)
- `committee_types` - Committee type definitions
- `users` - Authentication and authorization

#### Edition-Scoped Tables (Data Varies Per Year)
The following 18 tables contain year-specific data and have been updated to support multiple editions:

1. `important_dates`
2. `speakers`
3. `committee_members`
4. `contact_persons`
5. `conference_documents`
6. `conference_assets`
7. `research_categories`
8. `research_areas` (via `research_categories`)
9. `submission_methods`
10. `presentation_guidelines`
11. `payment_information`
12. `registration_fees`
13. `payment_policies`
14. `social_media_links`
15. `abstract_formats`
16. `event_locations`
17. `author_page_config`

#### Legacy Table (Preserved for Transition)
- `conferences` - Original conference table (retained for backward compatibility during migration phase)

---

## New Schema Design

### 1. Central Conference Editions Table

**Table:** `conference_editions`

This table serves as the central entity for managing multiple conference years/editions.

#### Columns

| Column | Type | Constraints | Purpose |
|--------|------|-------------|---------|
| `id` | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| `year` | YEAR | UNIQUE, NOT NULL | Conference year (2026, 2027, etc.) |
| `edition_number` | INT | NOT NULL | Sequential edition (13th, 14th, etc.) |
| `name` | VARCHAR(255) | NOT NULL | Display name (e.g., "RISTCON 2027") |
| `slug` | VARCHAR(100) | UNIQUE, NOT NULL | URL-friendly identifier |
| `status` | ENUM | NOT NULL, DEFAULT 'draft' | Lifecycle status |
| `is_active_edition` | BOOLEAN | NOT NULL, DEFAULT FALSE | Marks default edition for API |
| `conference_date` | DATE | NOT NULL | Main conference date |
| `venue_type` | ENUM | NOT NULL | physical/virtual/hybrid |
| `venue_location` | VARCHAR(255) | NULLABLE | Physical location |
| `theme` | TEXT | NOT NULL | Conference theme |
| `description` | TEXT | NULLABLE | Detailed description |
| `general_email` | VARCHAR(255) | NOT NULL | Contact email |
| `availability_hours` | VARCHAR(255) | NULLABLE | Support hours |
| `copyright_year` | YEAR | NOT NULL | Copyright year |
| `site_version` | VARCHAR(20) | DEFAULT '1.0' | Website version |
| `last_updated` | DATETIME | NULLABLE | Custom update timestamp |
| `created_at` | TIMESTAMP | | |
| `updated_at` | TIMESTAMP | | |
| `deleted_at` | TIMESTAMP | NULLABLE | Soft delete |

#### Status Enum Values

- `draft` - Being prepared, not visible to public
- `published` - Live and visible to public
- `archived` - Past conference, read-only
- `cancelled` - Conference was cancelled

#### Indexes

- PRIMARY KEY on `id`
- UNIQUE on `year`
- UNIQUE on `slug`
- INDEX on `status`
- INDEX on `is_active_edition`
- COMPOSITE INDEX on `(status, is_active_edition)`
- COMPOSITE INDEX on `(year, status)`

### 2. Edition-Scoped Tables Updates

All 18 edition-scoped tables have been updated with:

#### New Column
- `edition_id` (BIGINT UNSIGNED, NOT NULL, FOREIGN KEY → `conference_editions.id`)

#### Foreign Key Constraint
- `FOREIGN KEY (edition_id) REFERENCES conference_editions(id) ON DELETE CASCADE ON UPDATE CASCADE`

#### Indexes
- Basic index on `edition_id`
- Composite indexes for frequently queried combinations:
  - `important_dates`: `(edition_id, date_type)`
  - `speakers`: `(edition_id, speaker_type)`
  - `committee_members`: `(edition_id, committee_type_id)`
  - `conference_documents`: `(edition_id, document_category, is_active)`
  - `conference_assets`: `(edition_id, asset_type)`
  - `research_categories`: `(edition_id, category_code)`
  - `registration_fees`: `(edition_id, attendee_type)`
  - `payment_information`: `(edition_id, payment_type)`
  - `payment_policies`: `(edition_id, policy_type)`
  - `social_media_links`: `(edition_id, is_active, display_order)`

#### Backward Compatibility
- `conference_id` column is **retained** in all tables
- Both `conference_id` and `edition_id` coexist during transition period
- Future migrations can deprecate `conference_id` once full migration is verified

---

## Migration Strategy

### Critical Execution Order

**⚠️ IMPORTANT:** Follow this exact sequence:

1. **Backup Database**
   ```bash
   php artisan db:backup  # or manual backup
   ```

2. **Run First Migration** - Create `conference_editions` table
   ```bash
   php artisan migrate --path=database/migrations/2026_01_11_135551_create_conference_editions_table.php
   ```

3. **Run Second Migration** - Add `edition_id` columns (nullable)
   ```bash
   php artisan migrate --path=database/migrations/2026_01_11_135552_add_edition_id_to_scoped_tables.php
   ```

4. **Run Data Migration Seeder** - Migrate existing data and backfill
   ```bash
   php artisan db:seed --class=EditionDataMigrationSeeder
   ```

5. **Run Third Migration** - Add foreign key constraints and indexes
   ```bash
   php artisan migrate --path=database/migrations/2026_01_11_135553_add_edition_foreign_keys_and_indexes.php
   ```

6. **Verify Data Integrity**
   ```bash
   php artisan tinker
   >>> App\Models\ConferenceEdition::count()
   >>> App\Models\ConferenceEdition::where('year', 2026)->first()
   ```

### Data Migration Process

The `EditionDataMigrationSeeder` performs the following operations:

1. **Conference Migration**
   - Copies all data from `conferences` table to `conference_editions`
   - Determines status based on conference date:
     - Future dates → `published`
     - Past dates → `archived`
     - Cancelled → `cancelled`
   - Marks 2026 edition as active (`is_active_edition = true`)

2. **Edition ID Backfilling**
   - Creates mapping: `conference.id` → `conference_editions.id`
   - Updates all 18 edition-scoped tables
   - Sets `edition_id` for each record based on its `conference_id`

3. **Validation**
   - Confirms all conferences have corresponding editions
   - Verifies all edition-scoped records have `edition_id` populated
   - Ensures exactly one active edition exists
   - Confirms 2026 is the active edition

### Rollback Strategy

If issues occur, rollback in reverse order:

```bash
# 1. Rollback foreign key constraints
php artisan migrate:rollback --step=1

# 2. Run cleanup (if needed)
php artisan tinker
>>> DB::table('important_dates')->update(['edition_id' => null]);
>>> # Repeat for other tables

# 3. Rollback edition_id columns
php artisan migrate:rollback --step=1

# 4. Rollback conference_editions table
php artisan migrate:rollback --step=1

# 5. Restore from backup if necessary
```

---

## Backward Compatibility

### API Response Structure

**✅ NO CHANGES** to existing API response structure. The frontend will receive identical JSON responses.

### How It Works

1. **ConferenceService** updated to use `ConferenceEdition` internally
2. **Type flexibility** - Methods accept both `Conference` and `ConferenceEdition` objects
3. **Response mapping** - `edition_id` relationships return data in same format
4. **Conference ID preservation** - Original `conference_id` still present in responses

### Example: Before vs After

**Before (using `Conference`):**
```json
{
  "id": 1,
  "year": 2026,
  "conference_date": "2026-01-21",
  "speakers": [...]
}
```

**After (using `ConferenceEdition`):**
```json
{
  "id": 1,
  "year": 2026,
  "conference_date": "2026-01-21",
  "speakers": [...]
}
```

### Default Edition Behavior

When no year parameter is provided:
- API defaults to `is_active_edition = true` (currently 2026)
- EditionService resolves the active edition
- Responses use active edition data

---

## Implementation Guide

### 1. Service Layer

#### EditionService

Central service for edition management:

```php
use App\Services\EditionService;

$editionService = app(EditionService::class);

// Get active edition
$active = $editionService->getActiveEdition();

// Get edition by year
$edition2027 = $editionService->getEditionByYear(2027);

// Resolve edition (year or active)
$edition = $editionService->resolveEdition($year); // null = active

// Get published editions
$published = $editionService->getPublishedEditions();
```

#### ConferenceService (Updated)

Now uses `ConferenceEdition` internally:

```php
use App\Services\ConferenceService;

$conferenceService = app(ConferenceService::class);

// Get conference by year (returns ConferenceEdition)
$conference = $conferenceService->getConferenceByYear(2026);

// Get all conferences
$all = $conferenceService->getAllConferences();

// Get speakers
$speakers = $conferenceService->getConferenceSpeakers($conference);
```

### 2. Eloquent Models

#### ConferenceEdition Model

```php
use App\Models\ConferenceEdition;

// Query scopes
$published = ConferenceEdition::published()->get();
$active = ConferenceEdition::active()->first();
$upcoming = ConferenceEdition::upcoming()->get();
$past = ConferenceEdition::past()->get();

// Computed attributes
$edition->is_upcoming;           // boolean
$edition->is_past;               // boolean
$edition->days_until_conference; // int
$edition->formatted_date;        // "January 21, 2026"
$edition->full_name;            // "13th RISTCON 2026"

// Relationships
$edition->speakers;
$edition->importantDates;
$edition->committeeMembers;
// ... all 18 relationships

// Methods
$edition->markAsActive();    // Set as active edition
$edition->publish();         // Publish edition
$edition->archive();         // Archive edition
$edition->canBeDeleted();    // Check if deletable
```

#### Updated Edition-Scoped Models

All 18 models now have both relationships:

```php
use App\Models\Speaker;

$speaker = Speaker::find(1);

// Legacy relationship (still works)
$conference = $speaker->conference;

// New relationship
$edition = $speaker->edition;
```

### 3. Middleware

#### ResolveConferenceEdition

Automatically resolves edition from URL:

```php
// In routes/api.php
Route::middleware(['resolve.edition'])->group(function () {
    Route::get('/conferences/{year}/speakers', [ConferenceController::class, 'speakers']);
});

// In controller
public function speakers(Request $request)
{
    $edition = $request->attributes->get('conference_edition');
    $editionId = $request->attributes->get('conference_edition_id');
    $year = $request->attributes->get('conference_year');
}
```

### 4. Caching

EditionService implements caching:

- Active edition cached for 1 hour
- Year-based lookups cached for 1 hour
- Cache cleared automatically on edition updates

```php
// Manual cache clear if needed
$editionService->clearCache();
```

---

## API Usage

### Public Endpoints (Unchanged)

All existing endpoints work identically:

```
GET /api/v1/conferences
GET /api/v1/conferences/{year}
GET /api/v1/conferences/{year}/speakers
GET /api/v1/conferences/{year}/important-dates
GET /api/v1/conferences/{year}/committees
GET /api/v1/conferences/{year}/documents
GET /api/v1/registration
GET /api/v1/registration/fees
```

### New Edition Management Endpoints (Future Admin Panel)

These can be added when building the admin panel:

```
POST   /api/v1/admin/editions                    - Create new edition
GET    /api/v1/admin/editions                    - List all editions
GET    /api/v1/admin/editions/{id}               - Get edition details
PUT    /api/v1/admin/editions/{id}               - Update edition
DELETE /api/v1/admin/editions/{id}               - Delete edition
POST   /api/v1/admin/editions/{id}/activate      - Set as active
POST   /api/v1/admin/editions/{id}/publish       - Publish edition
POST   /api/v1/admin/editions/{id}/archive       - Archive edition
GET    /api/v1/admin/editions/{id}/statistics    - Get statistics
```

### Query Parameters

Existing query parameters continue to work:

- `?status=published` - Filter by status
- `?include=speakers,documents` - Eager load relations
- `?type=keynote` - Filter speakers by type
- `?category=abstract_template` - Filter documents

---

## Future Enhancements

### Phase 1: Master Data Extraction (Optional)

Extract reusable entities to reduce duplication:

1. **People/Persons Table**
   - Store speaker profiles, committee members
   - Junction tables link people to editions with roles
   - Benefits: Reusable profiles, historical tracking

2. **Master Research Categories**
   - Global category definitions
   - Junction table for edition-specific selections
   - Benefits: Consistency across years

3. **Organization/Affiliation Registry**
   - Store institutions/organizations
   - Link to people and events
   - Benefits: Standardized affiliations

### Phase 2: Advanced Features

1. **Edition Templates**
   - Copy structure from previous editions
   - Pre-populate common data
   - Customizable template sets

2. **Multi-Conference Support**
   - Support different conference series
   - RISTCON 2027, SPECIAL-CONF 2027 in same year
   - Add `series_id` to editions

3. **Workflow Management**
   - Draft → Review → Published → Archived
   - Approval processes
   - Scheduled publishing

4. **Versioning**
   - Track changes to edition data
   - Audit trail for compliance
   - Rollback capabilities

### Phase 3: Enhanced Edition Features

1. **Cloning**
   - Clone edition with all related data
   - Selective cloning (choose what to copy)
   - Year auto-increment

2. **Comparison**
   - Compare editions side-by-side
   - Highlight differences
   - Migration reports

3. **Analytics**
   - Per-edition statistics
   - Cross-edition comparisons
   - Trend analysis

---

## Testing Checklist

### Pre-Migration Testing

- [ ] Backup database
- [ ] Test migrations on development database
- [ ] Verify seeder logic with sample data
- [ ] Confirm rollback procedures work

### Post-Migration Testing

- [ ] Verify 2026 edition created and marked active
- [ ] Check all edition-scoped tables have edition_id populated
- [ ] Confirm no NULL edition_id values
- [ ] Test public API endpoints return identical responses
- [ ] Verify frontend displays data correctly
- [ ] Test edition resolution (with/without year parameter)
- [ ] Confirm caching works correctly
- [ ] Test edition CRUD operations

### Regression Testing

- [ ] Existing frontend functionality unchanged
- [ ] No broken API contracts
- [ ] Performance comparable or improved
- [ ] All queries use proper indexes

---

## Performance Considerations

### Indexing Strategy

All frequently queried columns have indexes:
- Single column indexes on `edition_id`
- Composite indexes for common query patterns
- Unique indexes on `year` and `slug`

### Query Optimization

```php
// Good - Uses index
ConferenceEdition::where('year', 2026)->first();

// Better - Uses cache
$editionService->getEditionByYear(2026);

// Best - Single query with eager loading
ConferenceEdition::where('year', 2026)
    ->with(['speakers', 'documents', 'importantDates'])
    ->first();
```

### Caching Strategy

- Active edition cached globally (1 hour TTL)
- Year-based lookups cached (1 hour TTL)
- Cache invalidated on edition updates
- Consider Redis for production

---

## Troubleshooting

### Common Issues

**Issue:** NULL edition_id after migration
- **Cause:** Seeder not run or failed
- **Solution:** Run `EditionDataMigrationSeeder` again

**Issue:** Multiple active editions
- **Cause:** Direct database manipulation
- **Solution:** Use `setActiveEdition()` method

**Issue:** Foreign key constraint errors
- **Cause:** Orphaned records or incorrect backfilling
- **Solution:** Check data integrity, re-run seeder

**Issue:** API returns 404 for valid year
- **Cause:** Edition not created or archived
- **Solution:** Verify edition exists and status is published

### Debug Commands

```bash
# Check active edition
php artisan tinker
>>> App\Models\ConferenceEdition::active()->first()

# Verify edition_id population
>>> App\Models\Speaker::whereNull('edition_id')->count()

# Clear cache
>>> app(App\Services\EditionService::class)->clearCache()

# Check edition statistics
>>> app(App\Services\EditionService::class)->getEditionStatistics(1)
```

---

## Security Considerations

1. **Soft Deletes**
   - All editions use soft deletes
   - Data preserved for audit trail
   - Recoverable if needed

2. **Access Control**
   - Active edition flag prevents accidental exposure
   - Status enum controls visibility
   - Admin endpoints require authentication

3. **Data Integrity**
   - Foreign key constraints ensure referential integrity
   - Cascade deletes prevent orphaned records
   - Validation in service layer

---

## Deployment Checklist

### Pre-Deployment

- [ ] Code review completed
- [ ] All tests passing
- [ ] Database backup created
- [ ] Migration tested on staging
- [ ] Rollback procedure documented

### Deployment Steps

1. [ ] Enable maintenance mode
2. [ ] Create database backup
3. [ ] Run migrations in correct order
4. [ ] Run data migration seeder
5. [ ] Verify data integrity
6. [ ] Clear all caches
7. [ ] Test critical endpoints
8. [ ] Disable maintenance mode
9. [ ] Monitor for errors

### Post-Deployment

- [ ] Verify frontend functionality
- [ ] Check API response times
- [ ] Monitor error logs
- [ ] Confirm cache hit rates
- [ ] Test admin operations

---

## Support and Maintenance

### Monitoring

Monitor these metrics:
- Query performance on edition_id indexes
- Cache hit rates for active edition
- API response times
- Database growth

### Regular Tasks

- Archive past editions quarterly
- Clear orphaned cache keys
- Review and optimize slow queries
- Update documentation

---

## Conclusion

This multi-edition architecture provides:

✅ **Scalability** - Unlimited conference years
✅ **Backward Compatibility** - No frontend changes required
✅ **Data Integrity** - Foreign key constraints and validation
✅ **Performance** - Optimized indexes and caching
✅ **Maintainability** - Clean service layer and documented API
✅ **Future-Proof** - Foundation for admin panel and advanced features

The implementation is complete and ready for deployment following the migration strategy outlined above.

---

## Additional Resources

- **Eloquent Relationships:** https://laravel.com/docs/eloquent-relationships
- **Database Migrations:** https://laravel.com/docs/migrations
- **Query Optimization:** https://laravel.com/docs/queries#optimization
- **Caching:** https://laravel.com/docs/cache

---

**Document Version:** 1.0  
**Last Updated:** January 11, 2026  
**Author:** RISTCON Development Team
