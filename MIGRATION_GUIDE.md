# Quick Migration Guide

## Prerequisites
- ✅ Database backup created
- ✅ Testing environment verified
- ✅ All code changes committed

## Execution Steps

### Step 1: Run Migrations
```bash
cd "D:\Project\Ristcon YearWise\ristcon-backend"

# Create conference_editions table
php artisan migrate --path=database/migrations/2026_01_11_135551_create_conference_editions_table.php

# Add edition_id columns (nullable)
php artisan migrate --path=database/migrations/2026_01_11_135552_add_edition_id_to_scoped_tables.php
```

### Step 2: Migrate Data
```bash
# Run the data migration seeder
php artisan db:seed --class=EditionDataMigrationSeeder
```

Expected output:
```
Starting edition data migration...
Migrating conferences to conference_editions...
✓ Migrated 2026 (ID: 1) - Status: published
Backfilling edition_id in related tables...
  ✓ important_dates: Updated X rows for edition 1
  ✓ speakers: Updated X rows for edition 1
  ... (18 tables total)
Validating migration...
✓ All validation checks passed!
✓ Edition data migration completed successfully!
```

### Step 3: Add Constraints
```bash
# Add foreign keys and indexes
php artisan migrate --path=database/migrations/2026_01_11_135553_add_edition_foreign_keys_and_indexes.php
```

### Step 4: Verify
```bash
# Open tinker
php artisan tinker

# Run verification commands
>>> App\Models\ConferenceEdition::count()
=> 1

>>> App\Models\ConferenceEdition::where('year', 2026)->first()->is_active_edition
=> true

>>> App\Models\Speaker::whereNull('edition_id')->count()
=> 0  // Should be 0

>>> exit
```

### Step 5: Test API
```bash
# Test the API endpoint (if server running)
curl http://localhost:8000/api/v1/conferences/2026

# Should return conference data with speakers, dates, etc.
```

## Rollback (If Needed)

```bash
# Rollback in reverse order
php artisan migrate:rollback --step=1  # Foreign keys
php artisan migrate:rollback --step=1  # edition_id columns
php artisan migrate:rollback --step=1  # conference_editions table
```

## Success Indicators

✅ No errors during migration
✅ Seeder reports success
✅ All 18 tables have edition_id populated
✅ Exactly 1 active edition exists
✅ API endpoints return data correctly
✅ No NULL edition_id values

## Common Issues

### Issue: Seeder fails with constraint error
**Solution:** Run Step 3 migration AFTER seeder, not before

### Issue: NULL edition_id values remain
**Solution:** Re-run the seeder:
```bash
php artisan db:seed --class=EditionDataMigrationSeeder
```

### Issue: Multiple active editions
**Solution:** Fix via tinker:
```bash
php artisan tinker
>>> App\Models\ConferenceEdition::where('id', '!=', 1)->update(['is_active_edition' => false])
>>> App\Models\ConferenceEdition::find(1)->update(['is_active_edition' => true])
```

## Next Steps After Migration

1. ✅ Clear application cache: `php artisan cache:clear`
2. ✅ Test all public API endpoints
3. ✅ Verify frontend displays data
4. ✅ Monitor logs for errors
5. ✅ Document any issues

## Files Created

### Migrations
- `2026_01_11_135551_create_conference_editions_table.php`
- `2026_01_11_135552_add_edition_id_to_scoped_tables.php`
- `2026_01_11_135553_add_edition_foreign_keys_and_indexes.php`

### Models
- `app/Models/ConferenceEdition.php`

### Services
- `app/Services/EditionService.php`
- `app/Services/ConferenceService.php` (updated)

### Middleware
- `app/Http/Middleware/ResolveConferenceEdition.php`

### Seeders
- `database/seeders/EditionDataMigrationSeeder.php`

### Documentation
- `MULTI_EDITION_SCHEMA.md` (complete specification)
- `MIGRATION_GUIDE.md` (this file)

## Support

If issues arise:
1. Check logs: `storage/logs/laravel.log`
2. Review documentation: `MULTI_EDITION_SCHEMA.md`
3. Run validation queries in tinker
4. Create backup before attempting fixes

---

**Ready to proceed?** Follow steps 1-5 above in order.
