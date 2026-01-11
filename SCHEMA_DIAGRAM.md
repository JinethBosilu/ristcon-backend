# Database Schema Diagram

## Multi-Edition Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                    CONFERENCE EDITIONS                           │
│  (Central table - manages multiple conference years)            │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ conference_editions                                       │  │
│  ├──────────────────────────────────────────────────────────┤  │
│  │ • id (PK)                                                 │  │
│  │ • year (UNIQUE) ◄── URL Parameter /api/v1/conferences/{year}
│  │ • edition_number                                          │  │
│  │ • name                                                    │  │
│  │ • slug (UNIQUE)                                           │  │
│  │ • status (draft/published/archived/cancelled)            │  │
│  │ • is_active_edition ◄── Determines default when no year  │  │
│  │ • conference_date                                         │  │
│  │ • venue_type                                              │  │
│  │ • venue_location                                          │  │
│  │ • theme                                                   │  │
│  │ • description                                             │  │
│  │ • general_email                                           │  │
│  │ • availability_hours                                      │  │
│  │ • copyright_year                                          │  │
│  │ • site_version                                            │  │
│  │ • timestamps + soft deletes                               │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ One edition has many...
                              │
         ┌────────────────────┼────────────────────┐
         │                    │                    │
         ▼                    ▼                    ▼
┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐
│ CORE DATA        │  │ CONTENT          │  │ CONFIGURATION    │
├──────────────────┤  ├──────────────────┤  ├──────────────────┤
│ • speakers       │  │ • documents      │  │ • location       │
│ • important_dates│  │ • assets         │  │ • author_config  │
│ • committees     │  │ • research_areas │  │ • submission     │
│ • contacts       │  │                  │  │ • guidelines     │
│                  │  │                  │  │ • registration   │
│                  │  │                  │  │ • payment_info   │
│                  │  │                  │  │ • policies       │
│                  │  │                  │  │ • social_media   │
│                  │  │                  │  │ • abstracts      │
└──────────────────┘  └──────────────────┘  └──────────────────┘
```

## Edition-Scoped Tables Structure

```
Each of the 18 edition-scoped tables has this structure:

┌────────────────────────────────────────┐
│ table_name                             │
├────────────────────────────────────────┤
│ • id (PK)                              │
│ • edition_id (FK) ───┐                 │
│ • conference_id      │ ◄── Both retained for backward compatibility
│ • [table-specific columns]            │
│ • timestamps                           │
└────────────────────────────────────────┘
         │
         │ Foreign Key Constraint
         ▼
┌────────────────────────────────────────┐
│ conference_editions                    │
│ ON DELETE CASCADE                      │
│ ON UPDATE CASCADE                      │
└────────────────────────────────────────┘
```

## Data Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    API REQUEST FLOW                          │
└─────────────────────────────────────────────────────────────┘

Frontend Request:
GET /api/v1/conferences/2026/speakers
         │
         ▼
┌────────────────────────┐
│ ResolveConferenceEdition│
│ Middleware              │
└────────────────────────┘
         │
         │ 1. Extract year from URL
         │ 2. Query: ConferenceEdition::where('year', 2026)
         │ 3. Attach to request attributes
         ▼
┌────────────────────────┐
│ ConferenceController   │
└────────────────────────┘
         │
         │ Get edition from request
         ▼
┌────────────────────────┐
│ ConferenceService      │
└────────────────────────┘
         │
         │ Query: $edition->speakers()
         ▼
┌────────────────────────┐
│ Database Query         │
│ SELECT * FROM speakers │
│ WHERE edition_id = X   │
└────────────────────────┘
         │
         ▼
JSON Response (identical structure to before)


Without Year Parameter:
GET /api/v1/registration
         │
         ▼
┌────────────────────────┐
│ EditionService         │
│ getActiveEdition()     │
└────────────────────────┘
         │
         │ Query: ConferenceEdition::where('is_active_edition', true)
         │ Returns: 2026 edition
         ▼
[Rest of flow same as above]
```

## Relationship Diagram

```
┌────────────────────────────────────────────────────────────────┐
│                CONFERENCE EDITION RELATIONSHIPS                 │
└────────────────────────────────────────────────────────────────┘

conference_editions (1)
    │
    ├─► importantDates (Many)
    │   └─► Important dates for this edition
    │
    ├─► speakers (Many)
    │   └─► Keynote, plenary, invited speakers
    │
    ├─► committeeMembers (Many)
    │   └─► BelongsTo: committeeType (Global)
    │
    ├─► contactPersons (Many)
    │   └─► Contact information for this edition
    │
    ├─► documents (Many)
    │   └─► PDFs, Word docs, forms
    │
    ├─► assets (Many)
    │   └─► Logos, banners, images
    │
    ├─► researchCategories (Many)
    │   └─► researchAreas (Many)
    │       └─► Nested research topics
    │
    ├─► submissionMethods (Many)
    │   └─► How to submit papers
    │
    ├─► presentationGuidelines (Many)
    │   └─► Oral/poster requirements
    │
    ├─► registrationFees (Many)
    │   └─► Pricing for different attendee types
    │
    ├─► paymentInformation (Many)
    │   └─► Bank account details
    │
    ├─► paymentPolicies (Many)
    │   └─► Payment rules and requirements
    │
    ├─► socialMediaLinks (Many)
    │   └─► Facebook, Twitter, etc.
    │
    ├─► abstractFormats (Many)
    │   └─► Abstract/extended abstract specs
    │
    ├─► eventLocation (One)
    │   └─► Venue details, maps
    │
    └─► authorPageConfig (One)
        └─► Author submission configuration
```

## Index Strategy

```
┌─────────────────────────────────────────────────────────────┐
│                    INDEXING STRATEGY                         │
└─────────────────────────────────────────────────────────────┘

conference_editions:
  • PRIMARY KEY (id)
  • UNIQUE (year)              ◄── Fast year-based lookups
  • UNIQUE (slug)              ◄── URL-friendly routing
  • INDEX (status)             ◄── Filter by published/draft
  • INDEX (is_active_edition)  ◄── Fast active edition lookup
  • COMPOSITE (status, is_active_edition)
  • COMPOSITE (year, status)

Edition-Scoped Tables:
  • PRIMARY KEY (id)
  • INDEX (edition_id)         ◄── Fast edition filtering
  • FOREIGN KEY (edition_id → conference_editions.id)

Frequently Queried Combinations:
  speakers:            (edition_id, speaker_type)
  documents:           (edition_id, document_category, is_active)
  committee_members:   (edition_id, committee_type_id)
  important_dates:     (edition_id, date_type)
  research_categories: (edition_id, category_code)
  registration_fees:   (edition_id, attendee_type)
  payment_information: (edition_id, payment_type)

Performance Benefit:
  ✓ Year-based queries: O(1) via UNIQUE index
  ✓ Active edition: Cached + indexed
  ✓ Edition data: Composite indexes for common filters
  ✓ Relationships: Foreign key indexes
```

## Migration Sequence

```
┌─────────────────────────────────────────────────────────────┐
│              SAFE MIGRATION SEQUENCE                         │
└─────────────────────────────────────────────────────────────┘

BEFORE:
┌──────────────┐
│ conferences  │ ◄── Single table, year-based
└──────────────┘
    ↓ 1:Many
┌──────────────┐
│ speakers     │
│ • conference_id
└──────────────┘

STEP 1: Create New Table
┌──────────────────────┐
│ conference_editions  │ ◄── New central table
└──────────────────────┘
┌──────────────┐
│ conferences  │ ◄── Still exists
└──────────────┘
    ↓
┌──────────────┐
│ speakers     │ ◄── Unchanged
└──────────────┘

STEP 2: Add edition_id (nullable)
┌──────────────────────┐
│ conference_editions  │
└──────────────────────┘
┌──────────────┐
│ conferences  │
└──────────────┘
    ↓
┌──────────────┐
│ speakers     │
│ • conference_id
│ • edition_id (NULL) ◄── New column
└──────────────┘

STEP 3: Migrate Data
┌──────────────────────┐
│ conference_editions  │ ◄── 2026 data copied
└──────────────────────┘
         ↓
┌──────────────┐
│ conferences  │ ◄── Original data
└──────────────┘
    ↓
┌──────────────┐
│ speakers     │
│ • conference_id
│ • edition_id = 1   ◄── Backfilled
└──────────────┘

STEP 4: Add Constraints
┌──────────────────────┐
│ conference_editions  │
└──────────────────────┘
         ↓ FK Constraint
┌──────────────┐
│ speakers     │
│ • conference_id      ◄── Retained
│ • edition_id NOT NULL ◄── Constrained
└──────────────┘

AFTER:
Both relationships exist:
  • edition_id (new, primary)
  • conference_id (legacy, backward compat)
```

## Status Lifecycle

```
┌─────────────────────────────────────────────────────────────┐
│            EDITION STATUS LIFECYCLE                          │
└─────────────────────────────────────────────────────────────┘

┌───────────┐
│   DRAFT   │  Created but not public
└───────────┘
     │
     │ Admin clicks "Publish"
     ▼
┌───────────┐
│ PUBLISHED │  Visible to public, can be active edition
└───────────┘
     │
     │ Conference date passes
     ▼
┌───────────┐
│ ARCHIVED  │  Past conference, read-only
└───────────┘

Alternative path:
┌───────────┐
│   DRAFT   │
└───────────┘
     │
     │ Admin clicks "Cancel"
     ▼
┌───────────┐
│ CANCELLED │  Conference cancelled, not deleted
└───────────┘

Active Edition Flag:
  • Only ONE edition can be is_active_edition = true
  • When marking an edition active, all others auto-deactivate
  • Active edition = default when no year specified in API
```

## Cache Strategy

```
┌─────────────────────────────────────────────────────────────┐
│                  CACHING ARCHITECTURE                        │
└─────────────────────────────────────────────────────────────┘

Cache Keys:
  • conference:active_edition      ◄── Most frequently accessed
  • conference:edition:{year}      ◄── Year-specific lookups
  • conference:edition:slug:{slug} ◄── Slug-based lookups

Cache TTL: 1 hour (3600 seconds)

Cache Flow:
┌─────────────────┐
│ API Request     │
└─────────────────┘
        │
        ▼
┌─────────────────┐
│ Check Cache     │ ◄── conference:edition:2026
└─────────────────┘
   │           │
   │ Hit       │ Miss
   ▼           ▼
┌──────┐   ┌──────────────┐
│Return│   │Query Database│
└──────┘   └──────────────┘
               │
               │ Store in cache (1 hour)
               ▼
           ┌──────┐
           │Return│
           └──────┘

Cache Invalidation:
  • Automatic on edition create/update/delete
  • Manual via EditionService::clearCache()
  • Recommended for production: Redis
```

## Query Patterns

```
┌─────────────────────────────────────────────────────────────┐
│              COMMON QUERY PATTERNS                           │
└─────────────────────────────────────────────────────────────┘

1. Get Active Edition Data:
   ConferenceEdition::active()->first()
   └─► Uses: is_active_edition index
   └─► Cached: Yes

2. Get Specific Year:
   ConferenceEdition::where('year', 2027)->first()
   └─► Uses: UNIQUE index on year
   └─► Cached: Yes

3. Get Edition with Relations:
   ConferenceEdition::with([
       'speakers',
       'importantDates',
       'documents'
   ])->where('year', 2026)->first()
   └─► Uses: Eager loading (N+1 prevention)
   └─► Indexed: Yes on edition_id

4. Filter Edition-Scoped Data:
   $edition->speakers()->where('speaker_type', 'keynote')->get()
   └─► Uses: Composite index (edition_id, speaker_type)

5. Published Editions:
   ConferenceEdition::published()->latestFirst()->get()
   └─► Uses: Index on status + year

Performance: All common queries use indexes ✓
```

## Legacy Compatibility

```
┌─────────────────────────────────────────────────────────────┐
│         BACKWARD COMPATIBILITY MECHANISM                     │
└─────────────────────────────────────────────────────────────┘

Old Code (Still Works):
  Conference::where('year', 2026)->first()
  
New Code (Preferred):
  ConferenceEdition::where('year', 2026)->first()

Service Layer Handles Both:
┌──────────────────────────────────────┐
│ ConferenceService::getConferenceByYear()│
└──────────────────────────────────────┘
          │
          │ Now queries ConferenceEdition
          ▼
┌──────────────────────────────────────┐
│ Returns: ConferenceEdition object    │
└──────────────────────────────────────┘
          │
          │ Has same relationships
          ▼
┌──────────────────────────────────────┐
│ $conference->speakers                │
│ $conference->documents               │
│ $conference->importantDates          │
│ [All work identically]               │
└──────────────────────────────────────┘

Response Format (Identical):
{
  "id": 1,
  "year": 2026,
  "conference_date": "2026-01-21",
  "theme": "...",
  "speakers": [...],
  "documents": [...]
}

Frontend: No changes needed ✓
```

---

This diagram provides a visual understanding of:
- Database structure
- Relationships between tables
- Data flow through the application
- Migration sequence
- Caching strategy
- Query patterns
- Backward compatibility mechanism
