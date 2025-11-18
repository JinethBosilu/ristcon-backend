# RISTCON Backend API - Developer Guide

[![Laravel](https://img.shields.io/badge/Laravel-12.39-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![API](https://img.shields.io/badge/API-REST-green.svg)](https://restfulapi.net)

**Base URL:** `http://localhost:8000/api/v1`  
**Response Format:** JSON  
**CORS Enabled:** Yes (localhost:3000, localhost:5173)

---

## 🚀 Quick Start

### Server Status
```bash
# Server is running at: http://localhost:8000
# All endpoints tested and working ✅
```

### Test Connection
```javascript
fetch('http://localhost:8000/api/v1/conferences')
  .then(res => res.json())
  .then(data => console.log(data));
```

---

## 📋 Available Endpoints

### 1. List All Conferences
**GET** `/conferences`

Get a list of all conferences with optional filtering.

**Query Parameters:**
- `status` - Filter by status: `upcoming`, `ongoing`, `completed` (optional)
- `year` - Filter by specific year (optional)
- `include` - Eager load relationships: `speakers`, `dates`, `committees`, `location`, etc. (comma-separated)

**Example Request:**
```javascript
// Get all upcoming conferences
fetch('http://localhost:8000/api/v1/conferences?status=upcoming')

// Get all conferences with speakers included
fetch('http://localhost:8000/api/v1/conferences?include=speakers,dates,location')
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "year": 2026,
      "edition_number": 13,
      "conference_date": "2026-01-21T00:00:00.000000Z",
      "venue_type": "physical",
      "venue_location": "University of Ruhuna, Matara, Sri Lanka",
      "theme": "Advancing Research Excellence in Science and Technology",
      "description": "The 13th International Research Conference...",
      "status": "upcoming",
      "general_email": "ristcon@ruh.ac.lk",
      "copyright_year": 2026,
      "site_version": "3.0",
      "created_at": "2025-11-18T18:28:20.000000Z",
      "updated_at": "2025-11-18T18:28:20.000000Z"
    }
  ],
  "meta": {
    "total": 1
  }
}
```

---

### 2. Get Conference by Year
**GET** `/conferences/{year}`

Get detailed information about a specific conference by year.

**Path Parameters:**
- `year` - Conference year (e.g., 2026)

**Query Parameters:**
- `include` - Load relationships: `speakers`, `dates`, `committees`, `contacts`, `documents`, `assets`, `location`, `research_areas`, `author_config`, `submission_methods`, `presentation_guidelines`

**Example Request:**
```javascript
// Get 2026 conference with all related data
fetch('http://localhost:8000/api/v1/conferences/2026?include=speakers,dates,location')
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "year": 2026,
    "edition_number": 13,
    "theme": "Advancing Research Excellence in Science and Technology",
    "conference_date": "2026-01-21T00:00:00.000000Z",
    "venue_type": "physical",
    "venue_location": "University of Ruhuna, Matara, Sri Lanka",
    "status": "upcoming",
    "general_email": "ristcon@ruh.ac.lk"
  }
}
```

---

### 3. Get Speakers
**GET** `/conferences/{year}/speakers`

Get all speakers for a conference (keynote, plenary, invited).

**Path Parameters:**
- `year` - Conference year

**Query Parameters:**
- `type` - Filter by speaker type: `keynote`, `plenary`, `invited` (optional)

**Example Request:**
```javascript
// Get all speakers
fetch('http://localhost:8000/api/v1/conferences/2026/speakers')

// Get only keynote speakers
fetch('http://localhost:8000/api/v1/conferences/2026/speakers?type=keynote')
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "conference_id": 1,
      "speaker_type": "keynote",
      "display_order": 1,
      "full_name": "Prof. Michael Anderson",
      "title": "PhD, FIEEE",
      "affiliation": "Department of Computer Science, Stanford University, USA",
      "additional_affiliation": "Visiting Professor, University of Cambridge",
      "bio": "Prof. Michael Anderson is a leading expert in artificial intelligence...",
      "photo_filename": "prof_anderson.jpg",
      "photo_url": "http://localhost:8000/storage/speakers/prof_anderson.jpg",
      "website_url": "https://stanford.edu/~anderson",
      "email": "m.anderson@stanford.edu"
    }
  ]
}
```

**Speaker Types:**
- `keynote` - Main keynote speaker
- `plenary` - Plenary session speakers
- `invited` - Invited speakers

---

### 4. Get Important Dates
**GET** `/conferences/{year}/important-dates`

Get timeline/important dates for the conference.

**Example Request:**
```javascript
fetch('http://localhost:8000/api/v1/conferences/2026/important-dates')
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "conference_id": 1,
      "date_type": "submission_deadline",
      "date_value": "2025-10-15",
      "is_extended": false,
      "display_order": 1,
      "display_label": "Abstract Submission Deadline",
      "notes": "Submit via Microsoft CMT",
      "is_passed": true,
      "days_remaining": -35
    },
    {
      "id": 2,
      "date_type": "notification",
      "date_value": "2025-11-15",
      "display_label": "Notification of Acceptance",
      "is_passed": false,
      "days_remaining": 4
    }
  ]
}
```

**Date Types:**
- `submission_deadline` - Abstract/paper submission deadline
- `notification` - Acceptance notification date
- `camera_ready` - Camera-ready submission deadline
- `registration_deadline` - Registration deadline
- `conference_date` - Conference start date

**Computed Attributes:**
- `is_passed` - Boolean indicating if date has passed
- `days_remaining` - Days until the date (negative if passed)

---

### 5. Get Committees
**GET** `/conferences/{year}/committees`

Get all committee members grouped by committee type.

**Example Request:**
```javascript
fetch('http://localhost:8000/api/v1/conferences/2026/committees')
```

**Response:**
```json
{
  "success": true,
  "data": {
    "Advisory Board": [
      {
        "id": 1,
        "full_name": "Prof. K.A.S. Jayasekara",
        "designation": "Professor",
        "affiliation": "Department of Physics, University of Ruhuna",
        "role_category": "leadership",
        "is_international": false
      }
    ],
    "Editorial Board": [...],
    "Organizing Committee": [...]
  }
}
```

**Committee Types:**
- Advisory Board
- Editorial Board
- Organizing Committee

**Role Categories:**
- `leadership` - Chair, Co-chair, Secretary
- `department_rep` - Department representative
- `member` - Regular member

---

### 6. Get Contact Persons
**GET** `/conferences/{year}/contacts`

Get contact information for the conference.

**Example Request:**
```javascript
fetch('http://localhost:8000/api/v1/conferences/2026/contacts')
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "full_name": "Dr. Y.M.A.L.W. Yapa",
      "role": "Chairperson - RISTCON 2026",
      "department": "Department of Mathematics",
      "mobile": "+94 71 234 5678",
      "phone": "+94 41 222 7000",
      "email": "ristcon@ruh.ac.lk",
      "address": "Faculty of Science, University of Ruhuna, Matara, Sri Lanka",
      "display_order": 1
    }
  ]
}
```

---

### 7. Get Documents
**GET** `/conferences/{year}/documents`

Get downloadable documents (templates, forms, proceedings).

**Query Parameters:**
- `category` - Filter by category (optional)
- `available` - Filter by availability: `1` for available only (optional)

**Example Request:**
```javascript
// Get all available documents
fetch('http://localhost:8000/api/v1/conferences/2026/documents?available=1')

// Get only abstract templates
fetch('http://localhost:8000/api/v1/conferences/2026/documents?category=abstract_template')
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "document_category": "abstract_template",
      "document_title": "Abstract Template",
      "file_path": "documents/2026/abstract_template_ristcon2026.docx",
      "file_size": 52428,
      "file_size_formatted": "51.20 KB",
      "is_available": true,
      "download_url": "http://localhost:8000/storage/documents/2026/abstract_template_ristcon2026.docx",
      "description": "MS Word template for abstract submission"
    }
  ]
}
```

**Document Categories:**
- `author_form` - Author information form
- `abstract_template` - Abstract template
- `registration_form` - Registration form
- `declaration_form` - Declaration form
- `programme` - Conference programme
- `proceedings` - Conference proceedings
- `instructions` - General instructions
- `presentation_guide` - Presentation guidelines
- `camera_ready_template` - Camera-ready template
- `poster` - Conference poster
- `flyer` - Conference flyer

---

### 8. Get Research Areas
**GET** `/conferences/{year}/research-areas`

Get research categories and areas hierarchically.

**Example Request:**
```javascript
fetch('http://localhost:8000/api/v1/conferences/2026/research-areas')
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "category_code": "A",
      "category_name": "Life Sciences and Bio-medical Sciences",
      "display_order": 1,
      "research_areas": [
        {
          "id": 1,
          "area_name": "Molecular Biology and Genetics",
          "alternate_names": ["Genetics", "Molecular Biology"],
          "is_active": true
        },
        {
          "id": 2,
          "area_name": "Microbiology and Immunology",
          "is_active": true
        }
      ]
    },
    {
      "id": 2,
      "category_code": "B",
      "category_name": "Physical and Chemical Sciences",
      "research_areas": [...]
    }
  ]
}
```

**Research Categories:**
- **A** - Life Sciences and Bio-medical Sciences
- **B** - Physical and Chemical Sciences
- **C** - Mathematical and Statistical Sciences
- **D** - Computer Science and Information Technology
- **E** - Social Sciences and Humanities

---

### 9. Get Event Location
**GET** `/conferences/{year}/location`

Get venue information with GPS coordinates.

**Example Request:**
```javascript
fetch('http://localhost:8000/api/v1/conferences/2026/location')
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "venue_name": "Faculty of Science",
    "venue_full_address": "University of Ruhuna, Wellamadama, Matara, Sri Lanka",
    "city": "Matara",
    "postal_code": "81000",
    "country": "Sri Lanka",
    "latitude": 5.939716,
    "longitude": 80.576134,
    "google_maps_embed_url": "https://maps.google.com/maps?q=5.939716,80.576134&hl=en&z=15&output=embed",
    "google_maps_direction_link": "https://www.google.com/maps/dir/?api=1&destination=5.939716,80.576134",
    "is_virtual": false,
    "virtual_platform": null,
    "virtual_link": null
  }
}
```

**Use Cases:**
- Embed Google Maps: Use `google_maps_embed_url` in an iframe
- Get Directions: Link to `google_maps_direction_link`
- Custom Maps: Use `latitude` and `longitude`

---

### 10. Get Author Instructions
**GET** `/conferences/{year}/author-instructions`

Get submission configuration, methods, and presentation guidelines.

**Example Request:**
```javascript
fetch('http://localhost:8000/api/v1/conferences/2026/author-instructions')
```

**Response:**
```json
{
  "success": true,
  "data": {
    "config": {
      "cmt_url": "https://cmt3.research.microsoft.com/RISTCON2026",
      "submission_email": "submissions@ristcon.ruh.ac.lk",
      "blind_review_enabled": true,
      "camera_ready_required": true,
      "conference_format": "in_person"
    },
    "submission_methods": [
      {
        "id": 1,
        "document_type": "author_info",
        "submission_method": "email",
        "submission_url": "submissions@ristcon.ruh.ac.lk",
        "instructions": "Email your author information form to submissions@ristcon.ruh.ac.lk"
      },
      {
        "id": 2,
        "document_type": "abstract",
        "submission_method": "cmt_upload",
        "submission_url": "https://cmt3.research.microsoft.com/RISTCON2026",
        "instructions": "Upload your abstract via Microsoft CMT system"
      }
    ],
    "presentation_guidelines": [
      {
        "id": 1,
        "presentation_type": "oral",
        "duration_minutes": 15,
        "presentation_minutes": 10,
        "qa_minutes": 5,
        "duration_formatted": "15 minutes (10 presentation + 5 Q&A)",
        "equipment_provided": "Projector, Microphone, Laser Pointer"
      },
      {
        "id": 2,
        "presentation_type": "poster",
        "poster_width": 27,
        "poster_height": 40,
        "poster_unit": "inches",
        "poster_orientation": "portrait",
        "poster_dimensions": "27 × 40 inches (portrait)",
        "mounting_instructions": "Push pins will be provided"
      }
    ]
  }
}
```

---

## 🔧 React/Next.js Integration

### Setup API Client

**Create `src/lib/api.js`:**
```javascript
const API_BASE_URL = 'http://localhost:8000/api/v1';

export async function fetchAPI(endpoint, options = {}) {
  const url = `${API_BASE_URL}${endpoint}`;
  
  const response = await fetch(url, {
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...options.headers,
    },
    ...options,
  });

  if (!response.ok) {
    throw new Error(`API Error: ${response.status}`);
  }

  const data = await response.json();
  
  if (!data.success) {
    throw new Error(data.message || 'API request failed');
  }

  return data.data;
}

// Helper functions
export const getConferences = () => fetchAPI('/conferences');
export const getConference = (year) => fetchAPI(`/conferences/${year}`);
export const getSpeakers = (year) => fetchAPI(`/conferences/${year}/speakers`);
export const getDates = (year) => fetchAPI(`/conferences/${year}/important-dates`);
export const getCommittees = (year) => fetchAPI(`/conferences/${year}/committees`);
export const getContacts = (year) => fetchAPI(`/conferences/${year}/contacts`);
export const getDocuments = (year) => fetchAPI(`/conferences/${year}/documents`);
export const getResearchAreas = (year) => fetchAPI(`/conferences/${year}/research-areas`);
export const getLocation = (year) => fetchAPI(`/conferences/${year}/location`);
export const getAuthorInstructions = (year) => fetchAPI(`/conferences/${year}/author-instructions`);
```

### Example Component (React)

```jsx
import { useEffect, useState } from 'react';
import { getConference, getSpeakers } from '@/lib/api';

export default function ConferencePage() {
  const [conference, setConference] = useState(null);
  const [speakers, setSpeakers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    async function loadData() {
      try {
        setLoading(true);
        const [confData, speakersData] = await Promise.all([
          getConference(2026),
          getSpeakers(2026)
        ]);
        setConference(confData);
        setSpeakers(speakersData);
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    }
    
    loadData();
  }, []);

  if (loading) return <div>Loading...</div>;
  if (error) return <div>Error: {error}</div>;

  return (
    <div>
      <h1>{conference.theme}</h1>
      <p>Date: {new Date(conference.conference_date).toLocaleDateString()}</p>
      <p>Venue: {conference.venue_location}</p>

      <h2>Keynote Speakers</h2>
      <div className="speakers-grid">
        {speakers
          .filter(s => s.speaker_type === 'keynote')
          .map(speaker => (
            <div key={speaker.id} className="speaker-card">
              <img 
                src={speaker.photo_url} 
                alt={speaker.full_name}
                width={200}
                height={200}
              />
              <h3>{speaker.full_name}</h3>
              <p className="title">{speaker.title}</p>
              <p className="affiliation">{speaker.affiliation}</p>
              <p className="bio">{speaker.bio}</p>
              {speaker.website_url && (
                <a href={speaker.website_url} target="_blank" rel="noopener noreferrer">
                  Website
                </a>
              )}
            </div>
          ))}
      </div>
    </div>
  );
}
```

### Example with Axios

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Interceptor for responses
api.interceptors.response.use(
  (response) => response.data.data,
  (error) => Promise.reject(error)
);

export const conferenceAPI = {
  getAll: () => api.get('/conferences'),
  getByYear: (year) => api.get(`/conferences/${year}`),
  getSpeakers: (year, type) => api.get(`/conferences/${year}/speakers`, {
    params: { type }
  }),
  getDates: (year) => api.get(`/conferences/${year}/important-dates`),
  getCommittees: (year) => api.get(`/conferences/${year}/committees`),
  getContacts: (year) => api.get(`/conferences/${year}/contacts`),
  getDocuments: (year, category) => api.get(`/conferences/${year}/documents`, {
    params: { category, available: 1 }
  }),
  getResearchAreas: (year) => api.get(`/conferences/${year}/research-areas`),
  getLocation: (year) => api.get(`/conferences/${year}/location`),
  getAuthorInstructions: (year) => api.get(`/conferences/${year}/author-instructions`),
};
```

### Next.js Server Component Example

```jsx
// app/conference/[year]/page.jsx
import { conferenceAPI } from '@/lib/api';

export default async function ConferencePage({ params }) {
  const { year } = params;
  
  // Fetch data on server
  const conference = await conferenceAPI.getByYear(year);
  const speakers = await conferenceAPI.getSpeakers(year);

  return (
    <main>
      <h1>{conference.theme}</h1>
      <Speakers data={speakers} />
    </main>
  );
}
```

---

## 💡 Common Use Cases

### 1. Conference Homepage
```javascript
// Fetch main conference data with speakers and dates
const data = await fetch(
  'http://localhost:8000/api/v1/conferences/2026?include=speakers,dates,location'
).then(r => r.json());
```

### 2. Countdown Timer
```javascript
const { data } = await fetch('http://localhost:8000/api/v1/conferences/2026/important-dates')
  .then(r => r.json());

const conferenceDate = data.find(d => d.date_type === 'conference_date');
// Use conferenceDate.days_remaining for countdown
```

### 3. Speakers Grid
```javascript
const { data: speakers } = await fetch('http://localhost:8000/api/v1/conferences/2026/speakers')
  .then(r => r.json());

const keynote = speakers.filter(s => s.speaker_type === 'keynote');
const plenary = speakers.filter(s => s.speaker_type === 'plenary');
```

### 4. Download Documents
```javascript
const { data: docs } = await fetch(
  'http://localhost:8000/api/v1/conferences/2026/documents?available=1'
).then(r => r.json());

// Create download links
docs.forEach(doc => {
  console.log(`${doc.document_title}: ${doc.download_url}`);
});
```

### 5. Embedded Map
```javascript
const { data: location } = await fetch('http://localhost:8000/api/v1/conferences/2026/location')
  .then(r => r.json());

// Use in iframe
<iframe 
  src={location.google_maps_embed_url}
  width="100%"
  height="400"
  frameBorder="0"
/>
```

### 6. Research Areas Dropdown
```javascript
const { data: categories } = await fetch('http://localhost:8000/api/v1/conferences/2026/research-areas')
  .then(r => r.json());

// Create nested dropdown
categories.forEach(category => {
  console.log(category.category_name);
  category.research_areas.forEach(area => {
    console.log(`  - ${area.area_name}`);
  });
});
```

---

## 🔒 Error Handling

All endpoints return a consistent error format:

```json
{
  "success": false,
  "message": "Conference not found for year 2025"
}
```

**HTTP Status Codes:**
- `200` - Success
- `404` - Resource not found
- `500` - Server error

**Error Handling Example:**
```javascript
try {
  const response = await fetch('http://localhost:8000/api/v1/conferences/2025');
  const data = await response.json();
  
  if (!response.ok || !data.success) {
    throw new Error(data.message || 'Request failed');
  }
  
  return data.data;
} catch (error) {
  console.error('API Error:', error.message);
  // Show user-friendly error message
}
```

---

## 📊 Response Structure

All successful responses follow this format:

```json
{
  "success": true,
  "data": {}, // or []
  "meta": {} // optional, for pagination/counts
}
```

---

## 🎨 TypeScript Definitions

```typescript
// types/api.ts

export interface Conference {
  id: number;
  year: number;
  edition_number: number;
  conference_date: string;
  venue_type: 'physical' | 'virtual' | 'hybrid';
  venue_location: string;
  theme: string;
  description: string;
  status: 'upcoming' | 'ongoing' | 'completed';
  general_email: string;
  copyright_year: number;
  site_version: string;
  created_at: string;
  updated_at: string;
}

export interface Speaker {
  id: number;
  conference_id: number;
  speaker_type: 'keynote' | 'plenary' | 'invited';
  display_order: number;
  full_name: string;
  title: string;
  affiliation: string;
  additional_affiliation: string | null;
  bio: string;
  photo_filename: string;
  photo_url: string;
  website_url: string | null;
  email: string;
}

export interface ImportantDate {
  id: number;
  conference_id: number;
  date_type: 'submission_deadline' | 'notification' | 'camera_ready' | 'registration_deadline' | 'conference_date';
  date_value: string;
  is_extended: boolean;
  display_order: number;
  display_label: string;
  notes: string | null;
  is_passed: boolean;
  days_remaining: number;
}

export interface CommitteeMember {
  id: number;
  full_name: string;
  designation: string;
  affiliation: string;
  role_category: 'leadership' | 'department_rep' | 'member';
  is_international: boolean;
}

export interface Document {
  id: number;
  document_category: string;
  document_title: string;
  file_path: string;
  file_size: number;
  file_size_formatted: string;
  is_available: boolean;
  download_url: string;
  description: string | null;
}

export interface Location {
  id: number;
  venue_name: string;
  venue_full_address: string;
  city: string;
  postal_code: string;
  country: string;
  latitude: number;
  longitude: number;
  google_maps_embed_url: string;
  google_maps_direction_link: string;
  is_virtual: boolean;
  virtual_platform: string | null;
  virtual_link: string | null;
}

export interface APIResponse<T> {
  success: boolean;
  data: T;
  meta?: {
    total: number;
  };
}
```

---

## 🐛 Troubleshooting

### CORS Issues
If you get CORS errors, ensure your React app is running on:
- `http://localhost:3000` (Create React App)
- `http://localhost:5173` (Vite)

### 404 Errors
- Verify server is running: `php artisan serve`
- Check base URL: `http://localhost:8000/api/v1`
- Ensure year exists in database (currently only 2026 is seeded)

### Empty Data
- Verify database is seeded: `php artisan db:seed --class=Ristcon2026Seeder`
- Check year parameter matches seeded data

### Image/Document 404s
- Ensure storage link exists: `php artisan storage:link`
- Verify files exist in `storage/app/public/` directory

---

## 📞 Support

For backend issues or questions:
- Email: `ristcon@ruh.ac.lk`
- Check logs: `storage/logs/laravel.log`

---

## 📝 Notes

- **Test Data:** Currently only RISTCON 2026 (13th edition) is seeded
- **File Uploads:** Images and documents are stored in `storage/app/public/`
- **Soft Deletes:** Most models use soft deletes (records marked as deleted but not removed)
- **Timestamps:** All timestamps are in UTC format (ISO 8601)
- **Relationships:** Use `?include=` parameter to eager load related data and reduce API calls

---

**Last Updated:** November 19, 2025  
**API Version:** 1.0  
**Backend:** Laravel 12.39.0
