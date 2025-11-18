# RISTCON Backend - Laravel Project Setup Guide

## Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 8.0 or MariaDB 10.3+
- Node.js & npm (optional, for frontend assets)

## Installation Steps

### 1. Install Dependencies
```bash
cd ristcon-backend
composer install
```

### 2. Configure Environment
Copy `.env.example` to `.env` and update database credentials:
```env
APP_NAME="RISTCON Backend API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ristcon_db
DB_USERNAME=root
DB_PASSWORD=your_password

FILESYSTEM_DISK=public
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Create Database
```sql
CREATE DATABASE ristcon_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Run Migrations
```bash
php artisan migrate
```

This will create all 14 tables:
- conferences
- important_dates
- speakers
- committee_types
- committee_members
- contact_persons
- conference_documents
- conference_assets
- research_categories
- research_areas
- event_locations
- author_page_config
- submission_methods
- presentation_guidelines

### 6. Create Storage Symlink
```bash
php artisan storage:link
```

### 7. Seed Test Data (RISTCON 2026)
```bash
php artisan db:seed
```

This seeds complete RISTCON 2026 conference data including:
- Conference details (13th edition, Jan 21, 2026)
- 4 important dates
- 3 speakers (1 keynote + 2 plenary)
- 5 advisory board members
- 7 editorial board members
- 11 organizing committee members
- 2 contact persons
- 3 documents (templates & forms)
- 2 assets (logo & poster)
- 5 research categories (A-E)
- 20+ research areas
- Event location with GPS coordinates
- Author page configuration (CMT URL, blind review)
- 3 submission methods
- 2 presentation guidelines (oral & poster)

### 8. Start Development Server
```bash
php artisan serve
```

Server will run on: `http://localhost:8000`

## API Endpoints

### Public Endpoints (No Authentication)
```
GET /api/v1/conferences
GET /api/v1/conferences/2026
GET /api/v1/conferences/2026/speakers
GET /api/v1/conferences/2026/important-dates
GET /api/v1/conferences/2026/committees
GET /api/v1/conferences/2026/contacts
GET /api/v1/conferences/2026/documents
GET /api/v1/conferences/2026/research-areas
GET /api/v1/conferences/2026/location
GET /api/v1/conferences/2026/author-instructions
```

### Test the API
```bash
# Get RISTCON 2026 conference with all relations
curl http://localhost:8000/api/v1/conferences/2026?include=speakers,important_dates,committees,location,research_areas

# Get speakers only
curl http://localhost:8000/api/v1/conferences/2026/speakers

# Get research areas
curl http://localhost:8000/api/v1/conferences/2026/research-areas
```

## CORS Configuration for React Frontend

The `bootstrap/app.php` includes CORS middleware for React development:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->api(prepend: [
        \Illuminate\Http\Middleware\HandleCors::class,
    ]);
})
```

Allowed origins (configured in `.env`):
- `http://localhost:3000` (Create React App)
- `http://localhost:5173` (Vite)

## File Storage Structure

```
storage/app/public/
├── speakers/          # Speaker photos
├── documents/         # Conference documents
│   └── 2026/
├── assets/            # Logos, posters, banners
│   └── 2026/
└── uploads/           # Temporary uploads
```

## Admin Authentication (Laravel Sanctum)

### Default Admin Credentials
```
Email: admin@ristcon.ruh.ac.lk
Password: password
```

### Get API Token (Future Implementation)
```bash
POST /api/v1/login
Content-Type: application/json

{
  "email": "admin@ristcon.ruh.ac.lk",
  "password": "password"
}
```

Use token in admin requests:
```bash
curl -H "Authorization: Bearer {token}" \
     -H "Content-Type: application/json" \
     http://localhost:8000/api/v1/admin/conferences
```

## React Frontend Integration

### Install axios
```bash
npm install axios
```

### Create API Service
```javascript
// src/services/api.js
import axios from 'axios';

const API = axios.create({
  baseURL: 'http://localhost:8000/api/v1',
  headers: {
    'Content-Type': 'application/json',
  },
});

export const getConference = (year, includes = []) => {
  return API.get(`/conferences/${year}`, {
    params: { include: includes.join(',') }
  });
};

export const getSpeakers = (year) => {
  return API.get(`/conferences/${year}/speakers`);
};

export const getImportantDates = (year) => {
  return API.get(`/conferences/${year}/important-dates`);
};

export const getCommittees = (year) => {
  return API.get(`/conferences/${year}/committees`);
};

export const getResearchAreas = (year) => {
  return API.get(`/conferences/${year}/research-areas`);
};

export const getLocation = (year) => {
  return API.get(`/conferences/${year}/location`);
};

export const getAuthorInstructions = (year) => {
  return API.get(`/conferences/${year}/author-instructions`);
};

export default API;
```

### Usage in React Component
```javascript
import { useEffect, useState } from 'react';
import { getConference } from './services/api';

function ConferenceHome() {
  const [conference, setConference] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getConference(2026, ['speakers', 'important_dates', 'location'])
      .then(response => {
        setConference(response.data.data);
        setLoading(false);
      })
      .catch(error => {
        console.error('Error:', error);
        setLoading(false);
      });
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

      <div className="speakers">
        {conference.speakers.map(speaker => (
          <div key={speaker.speaker_id}>
            <img src={speaker.photo_url} alt={speaker.full_name} />
            <h3>{speaker.full_name}</h3>
            <p>{speaker.affiliation}</p>
          </div>
        ))}
      </div>
    </div>
  );
}

export default ConferenceHome;
```

## Database Backup & Restore

### Backup
```bash
mysqldump -u root -p ristcon_db > backup_2026.sql
```

### Restore
```bash
mysql -u root -p ristcon_db < backup_2026.sql
```

## Performance Optimization

### Enable Caching
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Database Indexing
Already optimized with indexes on:
- `conferences.year` + `status`
- `important_dates.conference_id` + `date_type`
- `speakers.conference_id` + `speaker_type`
- `committee_members.conference_id` + `committee_type_id`
- `conference_documents.conference_id` + `document_category`
- `research_areas.category_id` + `is_active`

## Troubleshooting

### Error: Class not found
```bash
composer dump-autoload
```

### Storage permission denied
```bash
chmod -R 775 storage bootstrap/cache
```

### Migration errors
```bash
php artisan migrate:fresh --seed
```

### CORS issues
Check that your React app origin is in the allowed list

## API Testing

### Using Postman
1. Import `API_DOCUMENTATION.md` endpoints
2. Set base URL: `http://localhost:8000/api/v1`
3. Test public endpoints (no auth required)

### Using cURL
```bash
# Get all conferences
curl http://localhost:8000/api/v1/conferences

# Get conference with relations
curl "http://localhost:8000/api/v1/conferences/2026?include=speakers,important_dates"

# Get speakers
curl http://localhost:8000/api/v1/conferences/2026/speakers

# Get research areas
curl http://localhost:8000/api/v1/conferences/2026/research-areas
```

## Project Structure

```
ristcon-backend/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/
│   │           └── ConferenceController.php
│   └── Models/
│       ├── Conference.php
│       ├── Speaker.php
│       ├── ImportantDate.php
│       ├── CommitteeMember.php
│       ├── ConferenceDocument.php
│       └── ... (14 models total)
├── database/
│   ├── migrations/
│   │   ├── 001_create_conferences_table.php
│   │   ├── 002_create_important_dates_table.php
│   │   └── ... (14 migrations)
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── Ristcon2026Seeder.php
├── routes/
│   └── api.php
├── storage/
│   └── app/
│       └── public/
│           ├── speakers/
│           ├── documents/
│           └── assets/
├── API_DOCUMENTATION.md
└── README.md
```

## Documentation Files

- **README.md** (this file): Setup and usage guide
- **API_DOCUMENTATION.md**: Complete API reference with examples
- **Migration Files**: Database schema definitions
- **Model Files**: Eloquent relationships and business logic

## Environment Variables for Production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.ristcon.ruh.ac.lk

DB_CONNECTION=mysql
DB_HOST=your_production_host
DB_PORT=3306
DB_DATABASE=ristcon_prod
DB_USERNAME=your_prod_user
DB_PASSWORD=strong_production_password

SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

## Support

For issues or questions:
- Email: ristcon@ruh.ac.lk
- Documentation: See `API_DOCUMENTATION.md`

## Next Steps

1. ✅ Database schema created (14 tables)
2. ✅ Models with relationships
3. ✅ API controllers with public endpoints
4. ✅ 2026 test data seeded
5. ✅ Complete API documentation
6. ⏳ Admin authentication endpoints (future)
7. ⏳ File upload functionality (future)
8. ⏳ React frontend development

## License

© 2026 University of Ruhuna - RISTCON
