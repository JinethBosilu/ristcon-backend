# RISTCON Backend API Documentation

## Base URL
```
http://localhost:8000/api/v1
```

## Authentication
Admin endpoints require Bearer token authentication using Laravel Sanctum.

```http
Authorization: Bearer {your-token}
```

---

## Public Endpoints (No Authentication Required)

### 1. Get All Conferences

```http
GET /api/v1/conferences
```

**Query Parameters:**
- `status` (optional): Filter by status (`upcoming`, `ongoing`, `completed`)
- `year` (optional): Filter by year
- `include` (optional): Comma-separated relations (`speakers`, `important_dates`, `committees`, `documents`, `location`)

**Response 200 OK:**
```json
{
  "success": true,
  "data": [
    {
      "conference_id": 3,
      "year": 2026,
      "edition_number": 13,
      "conference_date": "2026-01-21",
      "venue_type": "physical",
      "venue_location": "University of Ruhuna, Matara, Sri Lanka",
      "theme": "Advancing Research Excellence",
      "description": "The 13th International Research Conference of RISTCON",
      "status": "upcoming",
      "general_email": "ristcon@ruh.ac.lk",
      "last_updated": "2025-11-18T10:30:00Z",
      "copyright_year": 2026,
      "site_version": "3.0",
      "countdown": {
        "days": 64,
        "hours": 5,
        "minutes": 30,
        "seconds": 45,
        "target_date": "2026-01-21T00:00:00+00:00"
      },
      "created_at": "2025-11-18T10:00:00Z",
      "updated_at": "2025-11-18T10:00:00Z"
    }
  ],
  "meta": {
    "total": 1
  }
}
```

### 2. Get Conference by Year

```http
GET /api/v1/conferences/{year}
```

**Example:**
```http
GET /api/v1/conferences/2026?include=speakers,important_dates,location
```

**Response 200 OK:**
```json
{
  "success": true,
  "data": {
    "conference_id": 3,
    "year": 2026,
    "edition_number": 13,
    "conference_date": "2026-01-21",
    "venue_type": "physical",
    "venue_location": "University of Ruhuna, Matara, Sri Lanka",
    "theme": "Advancing Research Excellence",
    "status": "upcoming",
    "countdown": {
      "days": 64,
      "hours": 5,
      "minutes": 30,
      "seconds": 45,
      "target_date": "2026-01-21T00:00:00+00:00"
    },
    "speakers": [
      {
        "speaker_id": 1,
        "speaker_type": "keynote",
        "display_order": 1,
        "full_name": "Prof. John Doe",
        "title": "PhD, FRSC",
        "affiliation": "Massachusetts Institute of Technology, USA",
        "bio": "Leading researcher in artificial intelligence...",
        "photo_url": "http://localhost:8000/storage/speakers/prof_john_doe.jpg",
        "website_url": "https://example.com",
        "email": "john.doe@mit.edu"
      }
    ],
    "important_dates": [
      {
        "date_id": 1,
        "date_type": "submission_deadline",
        "date_value": "2025-10-15",
        "is_extended": false,
        "display_label": "Abstract Submission Deadline",
        "days_remaining": -34,
        "is_passed": true
      },
      {
        "date_id": 2,
        "date_type": "conference_date",
        "date_value": "2026-01-21",
        "display_label": "Conference Date",
        "days_remaining": 64,
        "is_passed": false
      }
    ],
    "location": {
      "location_id": 1,
      "venue_name": "University of Ruhuna",
      "full_address": "Faculty of Science, University of Ruhuna, Matara, Sri Lanka",
      "city": "Matara",
      "country": "Sri Lanka",
      "latitude": "5.93971600",
      "longitude": "80.57613400",
      "google_maps_embed_url": "https://www.google.com/maps/embed?...",
      "google_maps_link": "https://goo.gl/maps/...",
      "google_maps_direction_link": "https://www.google.com/maps/dir//5.939716,80.576134"
    }
  }
}
```

**Response 404 Not Found:**
```json
{
  "success": false,
  "message": "Conference not found for year 2026"
}
```

### 3. Get Speakers

```http
GET /api/v1/conferences/{year}/speakers
```

**Query Parameters:**
- `type` (optional): Filter by speaker type (`keynote`, `plenary`, `invited`)

**Response 200 OK:**
```json
{
  "success": true,
  "data": [
    {
      "speaker_id": 1,
      "conference_id": 3,
      "speaker_type": "keynote",
      "display_order": 1,
      "full_name": "Prof. John Doe",
      "title": "PhD, FRSC",
      "affiliation": "Massachusetts Institute of Technology, USA",
      "additional_affiliation": "Visiting Professor, Oxford University",
      "bio": "Prof. Doe has 30 years of experience...",
      "photo_url": "http://localhost:8000/storage/speakers/prof_john_doe.jpg",
      "website_url": "https://johndoe.mit.edu",
      "email": "john.doe@mit.edu"
    }
  ]
}
```

### 4. Get Important Dates

```http
GET /api/v1/conferences/{year}/important-dates
```

**Response 200 OK:**
```json
{
  "success": true,
  "data": [
    {
      "date_id": 1,
      "conference_id": 3,
      "date_type": "submission_deadline",
      "date_value": "2025-10-15",
      "is_extended": false,
      "original_date": null,
      "display_order": 1,
      "display_label": "Abstract Submission Deadline",
      "notes": null,
      "is_passed": true,
      "days_remaining": null
    }
  ]
}
```

### 5. Get Committee Members

```http
GET /api/v1/conferences/{year}/committees
```

**Query Parameters:**
- `type` (optional): Filter by committee type name (e.g., "Advisory Board", "Editorial Board")

**Response 200 OK:**
```json
{
  "success": true,
  "data": {
    "Advisory Board": [
      {
        "member_id": 1,
        "full_name": "Prof. Jayasekara",
        "designation": "Professor",
        "department": "Department of Physics",
        "affiliation": "University of Colombo",
        "role": "Member",
        "country": "Sri Lanka",
        "is_international": false,
        "display_order": 1
      }
    ],
    "Editorial Board": [
      {
        "member_id": 8,
        "full_name": "Dr. Smith",
        "designation": "Senior Lecturer",
        "department": "Department of Computer Science",
        "affiliation": "University of Ruhuna",
        "role": "Editor",
        "country": "Sri Lanka",
        "is_international": false,
        "display_order": 1
      }
    ],
    "Organizing Committee": [
      {
        "member_id": 15,
        "full_name": "Dr. Y.M.A.L.W. Yapa",
        "designation": "Senior Lecturer",
        "department": "Department of Chemistry",
        "affiliation": "University of Ruhuna",
        "role": "Chairperson",
        "role_category": "leadership",
        "country": "Sri Lanka",
        "is_international": false,
        "display_order": 1
      }
    ]
  }
}
```

### 6. Get Contact Persons

```http
GET /api/v1/conferences/{year}/contacts
```

**Response 200 OK:**
```json
{
  "success": true,
  "data": [
    {
      "contact_id": 1,
      "conference_id": 3,
      "full_name": "Dr. Y.M.A.L.W. Yapa",
      "role": "Chairperson",
      "department": "Department of Chemistry",
      "mobile": "+94 71 234 5678",
      "phone": "+94 41 222 7000",
      "email": "yapa@che.ruh.ac.lk",
      "address": "Department of Chemistry, Faculty of Science, University of Ruhuna, Matara, Sri Lanka",
      "display_order": 1
    }
  ]
}
```

### 7. Get Conference Documents

```http
GET /api/v1/conferences/{year}/documents
```

**Query Parameters:**
- `category` (optional): Filter by document category
- `available` (optional): Filter by availability (`true`, `false`)

**Response 200 OK:**
```json
{
  "success": true,
  "data": [
    {
      "document_id": 1,
      "conference_id": 3,
      "document_category": "abstract_template",
      "file_name": "Abstract_Template_RISTCON2026.docx",
      "file_path": "documents/2026/Abstract_Template_RISTCON2026.docx",
      "display_name": "Download Abstract Template",
      "is_available": true,
      "button_width_percent": 70,
      "display_order": 1,
      "mime_type": "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
      "file_size": 45678,
      "file_size_formatted": "44.61 KB",
      "download_url": "http://localhost:8000/storage/documents/2026/Abstract_Template_RISTCON2026.docx"
    }
  ]
}
```

### 8. Get Research Categories and Areas

```http
GET /api/v1/conferences/{year}/research-areas
```

**Response 200 OK:**
```json
{
  "success": true,
  "data": [
    {
      "category_id": 1,
      "category_code": "A",
      "category_name": "Life Sciences",
      "description": "Biological and health sciences research",
      "display_order": 1,
      "is_active": true,
      "research_areas": [
        {
          "area_id": 1,
          "area_name": "Biochemistry",
          "alternate_names": ["Clinical Biochemistry"],
          "display_order": 1,
          "is_active": true
        },
        {
          "area_id": 2,
          "area_name": "Botany",
          "alternate_names": ["Plant Biology"],
          "display_order": 2,
          "is_active": true
        }
      ]
    },
    {
      "category_id": 2,
      "category_code": "B",
      "category_name": "Physical and Chemical Sciences",
      "research_areas": [
        {
          "area_id": 8,
          "area_name": "Chemistry",
          "alternate_names": [],
          "is_active": true
        }
      ]
    }
  ]
}
```

### 9. Get Event Location

```http
GET /api/v1/conferences/{year}/location
```

**Response 200 OK:**
```json
{
  "success": true,
  "data": {
    "location_id": 1,
    "conference_id": 3,
    "venue_name": "University of Ruhuna",
    "full_address": "Faculty of Science, University of Ruhuna, Matara, Sri Lanka",
    "city": "Matara",
    "country": "Sri Lanka",
    "latitude": "5.93971600",
    "longitude": "80.57613400",
    "google_maps_embed_url": "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3967.234!2d80.576134!3d5.939716!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNcKwNTYnMjMuMCJOIDgwwrAzNCczNC4wIkU!5e0!3m2!1sen!2slk!4v1234567890",
    "google_maps_link": "https://goo.gl/maps/xyz123",
    "google_maps_direction_link": "https://www.google.com/maps/dir//5.939716,80.576134",
    "is_virtual": false
  }
}
```

### 10. Get Author Instructions

```http
GET /api/v1/conferences/{year}/author-instructions
```

**Response 200 OK:**
```json
{
  "success": true,
  "data": {
    "config": {
      "config_id": 1,
      "conference_id": 3,
      "conference_format": "in_person",
      "cmt_url": "https://cmt3.research.microsoft.com/RISTCON2026",
      "submission_email": "submissions@ristcon.ruh.ac.lk",
      "blind_review_enabled": true,
      "camera_ready_required": true,
      "special_instructions": "All submissions must be original work...",
      "acknowledgment_text": "RISTCON 2026 will be using Microsoft CMT for submission management."
    },
    "submission_methods": [
      {
        "method_id": 1,
        "document_type": "author_info",
        "submission_method": "email",
        "email_address": "submissions@ristcon.ruh.ac.lk",
        "notes": "Please send completed author form via email",
        "display_order": 1
      },
      {
        "method_id": 2,
        "document_type": "abstract",
        "submission_method": "cmt_upload",
        "email_address": null,
        "notes": "Upload via CMT system",
        "display_order": 2
      }
    ],
    "presentation_guidelines": [
      {
        "guideline_id": 1,
        "presentation_type": "oral",
        "duration_minutes": 15,
        "presentation_minutes": 10,
        "qa_minutes": 5,
        "physical_presence_required": true,
        "duration_formatted": "15 minutes (10 presentation + 5 Q&A)",
        "detailed_requirements": "Presenters must bring their own laptops..."
      },
      {
        "guideline_id": 2,
        "presentation_type": "poster",
        "poster_width": "27.00",
        "poster_height": "40.00",
        "poster_unit": "inches",
        "poster_orientation": "portrait",
        "poster_dimensions": "27.00inches × 40.00inches (portrait)",
        "physical_presence_required": true,
        "detailed_requirements": "Posters must be printed on quality paper..."
      }
    ]
  }
}
```

---

## Admin Endpoints (Authentication Required)

### 11. Create Conference

```http
POST /api/v1/admin/conferences
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "year": 2027,
  "edition_number": 14,
  "conference_date": "2027-01-20",
  "venue_type": "physical",
  "venue_location": "University of Peradeniya, Sri Lanka",
  "theme": "Innovation in Science and Technology",
  "description": "The 14th International Research Conference",
  "status": "upcoming",
  "general_email": "ristcon2027@pdn.ac.lk",
  "copyright_year": 2027,
  "site_version": "4.0"
}
```

**Response 201 Created:**
```json
{
  "success": true,
  "message": "Conference created successfully",
  "data": {
    "conference_id": 4,
    "year": 2027,
    "edition_number": 14,
    "conference_date": "2027-01-20",
    "created_at": "2025-11-18T10:00:00Z"
  }
}
```

**Validation Errors (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "year": ["The year field is required."],
    "edition_number": ["The edition number must be an integer."],
    "conference_date": ["The conference date must be a valid date."]
  }
}
```

### 12. Update Conference

```http
PUT /api/v1/admin/conferences/{year}
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "theme": "Updated Theme",
  "status": "ongoing",
  "description": "Updated description"
}
```

**Response 200 OK:**
```json
{
  "success": true,
  "message": "Conference updated successfully",
  "data": {
    "conference_id": 3,
    "year": 2026,
    "theme": "Updated Theme",
    "status": "ongoing"
  }
}
```

### 13. Add Speaker

```http
POST /api/v1/admin/conferences/{year}/speakers
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "speaker_type": "keynote",
  "display_order": 1,
  "full_name": "Prof. Jane Smith",
  "title": "PhD, FRS",
  "affiliation": "Stanford University, USA",
  "additional_affiliation": "Adjunct Professor, Cambridge",
  "bio": "Prof. Smith is a renowned researcher...",
  "website_url": "https://janesmith.stanford.edu",
  "email": "jane.smith@stanford.edu"
}
```

**Response 201 Created:**
```json
{
  "success": true,
  "message": "Speaker added successfully",
  "data": {
    "speaker_id": 2,
    "conference_id": 3,
    "full_name": "Prof. Jane Smith"
  }
}
```

### 14. Upload Speaker Photo

```http
POST /api/v1/admin/speakers/{speaker_id}/photo
Content-Type: multipart/form-data
Authorization: Bearer {token}
```

**Form Data:**
- `photo`: File (jpg, jpeg, png, gif, max 5MB)

**Response 200 OK:**
```json
{
  "success": true,
  "message": "Speaker photo uploaded successfully",
  "data": {
    "speaker_id": 2,
    "photo_filename": "prof_jane_smith_1637243567.jpg",
    "photo_url": "http://localhost:8000/storage/speakers/prof_jane_smith_1637243567.jpg"
  }
}
```

### 15. Upload Document

```http
POST /api/v1/admin/conferences/{year}/documents
Content-Type: multipart/form-data
Authorization: Bearer {token}
```

**Form Data:**
- `document`: File (pdf, doc, docx, max 10MB)
- `document_category`: String (author_form, abstract_template, etc.)
- `display_name`: String
- `button_width_percent`: Integer (default: 70)
- `display_order`: Integer
- `is_available`: Boolean (default: true)

**Response 201 Created:**
```json
{
  "success": true,
  "message": "Document uploaded successfully",
  "data": {
    "document_id": 5,
    "document_category": "abstract_template",
    "file_name": "Abstract_Template_RISTCON2026.docx",
    "file_size": 45678,
    "file_size_formatted": "44.61 KB",
    "download_url": "http://localhost:8000/storage/documents/2026/Abstract_Template_RISTCON2026.docx"
  }
}
```

### 16. Delete Document

```http
DELETE /api/v1/admin/documents/{document_id}
Authorization: Bearer {token}
```

**Response 200 OK:**
```json
{
  "success": true,
  "message": "Document deleted successfully"
}
```

### 17. Add Important Date

```http
POST /api/v1/admin/conferences/{year}/important-dates
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "date_type": "submission_deadline",
  "date_value": "2026-10-15",
  "is_extended": false,
  "display_order": 1,
  "display_label": "Abstract Submission Deadline",
  "notes": "Strictly enforced"
}
```

**Response 201 Created:**
```json
{
  "success": true,
  "message": "Important date added successfully",
  "data": {
    "date_id": 5,
    "conference_id": 3,
    "date_type": "submission_deadline",
    "date_value": "2026-10-15"
  }
}
```

### 18. Add Research Category

```http
POST /api/v1/admin/conferences/{year}/research-categories
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "category_code": "F",
  "category_name": "Engineering and Technology",
  "description": "Engineering disciplines and applied technology",
  "display_order": 6,
  "is_active": true
}
```

**Response 201 Created:**
```json
{
  "success": true,
  "message": "Research category created successfully",
  "data": {
    "category_id": 6,
    "category_code": "F",
    "category_name": "Engineering and Technology"
  }
}
```

### 19. Add Research Area

```http
POST /api/v1/admin/research-categories/{category_id}/areas
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "area_name": "Mechanical Engineering",
  "alternate_names": ["Mechatronics", "Manufacturing Engineering"],
  "display_order": 1,
  "is_active": true
}
```

**Response 201 Created:**
```json
{
  "success": true,
  "message": "Research area added successfully",
  "data": {
    "area_id": 34,
    "category_id": 6,
    "area_name": "Mechanical Engineering"
  }
}
```

### 20. Bulk Import Committee Members

```http
POST /api/v1/admin/conferences/{year}/committees/import
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "committee_type": "Advisory Board",
  "members": [
    {
      "full_name": "Prof. John Doe",
      "designation": "Professor",
      "department": "Department of Physics",
      "affiliation": "University of Colombo",
      "role": "Member",
      "country": "Sri Lanka",
      "is_international": false,
      "display_order": 1
    },
    {
      "full_name": "Dr. Jane Smith",
      "designation": "Associate Professor",
      "department": "Department of Chemistry",
      "affiliation": "Oxford University",
      "role": "Member",
      "country": "United Kingdom",
      "is_international": true,
      "display_order": 2
    }
  ]
}
```

**Response 201 Created:**
```json
{
  "success": true,
  "message": "5 committee members imported successfully",
  "data": {
    "imported_count": 5,
    "committee_type": "Advisory Board"
  }
}
```

---

## Error Responses

### Authentication Error (401)
```json
{
  "success": false,
  "message": "Unauthenticated"
}
```

### Authorization Error (403)
```json
{
  "success": false,
  "message": "Unauthorized action"
}
```

### Not Found (404)
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "field_name": [
      "Error message 1",
      "Error message 2"
    ]
  }
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Internal server error",
  "error": "Error details (only in development mode)"
}
```

---

## Rate Limiting

Public API endpoints are rate-limited to **60 requests per minute** per IP address.

Admin API endpoints are rate-limited to **120 requests per minute** per authenticated user.

**Rate Limit Headers:**
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1637245200
```

---

## CORS Configuration

The API supports Cross-Origin Resource Sharing (CORS) for React frontend integration.

**Allowed Origins:**
- `http://localhost:3000` (React development)
- `http://localhost:5173` (Vite development)
- Production domain (to be configured)

**Allowed Methods:** GET, POST, PUT, PATCH, DELETE, OPTIONS

**Allowed Headers:** Content-Type, Authorization, X-Requested-With

---

## File Upload Limits

- **Documents:** Max 10MB (.pdf, .doc, .docx)
- **Images (Speaker Photos, Assets):** Max 5MB (.jpg, .jpeg, .png, .gif)

---

## Pagination

For large datasets, pagination is applied automatically:

```http
GET /api/v1/conferences?page=2&per_page=10
```

**Response:**
```json
{
  "success": true,
  "data": [...],
  "meta": {
    "current_page": 2,
    "per_page": 10,
    "total": 45,
    "last_page": 5,
    "from": 11,
    "to": 20
  },
  "links": {
    "first": "http://localhost:8000/api/v1/conferences?page=1",
    "last": "http://localhost:8000/api/v1/conferences?page=5",
    "prev": "http://localhost:8000/api/v1/conferences?page=1",
    "next": "http://localhost:8000/api/v1/conferences?page=3"
  }
}
```

---

## Timestamp Format

All timestamps follow **ISO 8601** format: `YYYY-MM-DDTHH:MM:SSZ`

Example: `2026-01-21T10:30:00Z`

---

## React Integration Example

```typescript
// api/conferences.ts
import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api/v1';

export const getConference = async (year: number) => {
  const response = await axios.get(
    `${API_BASE_URL}/conferences/${year}`,
    {
      params: {
        include: 'speakers,important_dates,location,committees'
      }
    }
  );
  return response.data;
};

export const getSpeakers = async (year: number) => {
  const response = await axios.get(
    `${API_BASE_URL}/conferences/${year}/speakers`
  );
  return response.data.data;
};

// React Component Example
import { useEffect, useState } from 'react';
import { getConference } from './api/conferences';

function ConferenceHome() {
  const [conference, setConference] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchConference = async () => {
      try {
        const data = await getConference(2026);
        setConference(data.data);
      } catch (error) {
        console.error('Error fetching conference:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchConference();
  }, []);

  if (loading) return <div>Loading...</div>;

  return (
    <div>
      <h1>{conference.theme}</h1>
      <p>Date: {conference.conference_date}</p>
      <p>Location: {conference.venue_location}</p>
      {conference.countdown && (
        <div className="countdown">
          <span>{conference.countdown.days} days</span>
          <span>{conference.countdown.hours} hours</span>
          <span>{conference.countdown.minutes} minutes</span>
        </div>
      )}
    </div>
  );
}
```

---

## Postman Collection

Import the Postman collection for easy API testing:

[Download RISTCON API Postman Collection](./RISTCON_API.postman_collection.json)

---

## Contact

For API support, contact: **ristcon@ruh.ac.lk**
