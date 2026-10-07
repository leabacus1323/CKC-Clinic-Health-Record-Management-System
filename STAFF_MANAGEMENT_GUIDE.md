# Staff Management Functionality - Implementation Guide

## Overview
Staff users (user_type = 2) now have access to doctor-like functionalities for managing clinic operations, with the ability to toggle between "Staff Duties" mode and their personal patient profile.

## Features Implemented

### 1. **Staff Dashboard Controller** (`StaffDashboardController.php`)
Located: `app/Http/Controllers/StaffDashboardController.php`

Provides complete clinic management functionality:
- **Dashboard**: Overview statistics (appointments, patients, pending items)
- **Appointments**: View and manage all appointments
- **Patients**: View and manage patient records
- **Health Records**: Access patient health records
- **Medicines**: Manage medicine inventory
- **Prescriptions**: Create and manage prescriptions
- **Reports**: Generate clinic reports

### 2. **Routes Configuration**
Added staff routes with middleware protection:
```php
Route::prefix('staff')->name('staff.')->middleware(['auth', 'check.user.type:2'])->group(function () {
    // All staff management routes
});
```

Key routes:
- `/staff/dashboard` - Staff dashboard
- `/staff/appointments` - Appointment management
- `/staff/patients` - Patient management
- `/staff/health-records` - Health records
- `/staff/medications` - Medicine inventory
- `/staff/prescriptions` - Prescription management
- `/staff/reports` - Reports and statistics

### 3. **Navigation Toggle**
The navigation bar now includes a toggle button for staff users:

**When in Staff Duties Mode:**
- Shows: Dashboard, Appointments, Patients, Health Records, Medicines, Prescriptions, Reports
- Toggle button: "My Profile" (blue) - switches to personal patient view

**When in Personal Profile Mode:**
- Shows: Dashboard, Health Records, Appointments (patient view)
- Toggle button: "Staff Duties" (green) - switches to staff management view

### 4. **Staff Views**
Created 8 staff-specific blade templates in `resources/views/staff/`:
- `dashboard.blade.php` - Staff dashboard with statistics
- `appointments.blade.php` - Appointment management
- `patients.blade.php` - Patient listing and search
- `patient-details.blade.php` - Individual patient details
- `health-records.blade.php` - Health records management
- `medicines.blade.php` - Medicine inventory
- `prescriptions.blade.php` - Prescription management
- `reports.blade.php` - Clinic reports

All views include a green "Staff Duties Mode" indicator banner at the top.

## User Experience

### For Staff Users (user_type = 2):

**Scenario 1: Staff performing administrative duties**
1. Log in as staff
2. Navigation shows doctor-like menu
3. Can manage all clinic operations
4. Green banner shows "Staff Duties Mode"

**Scenario 2: Staff viewing personal health records**
1. Click "My Profile" toggle button (blue)
2. Navigation switches to patient view
3. Can view own health records, appointments
4. Green button shows "Staff Duties" to switch back

### Visual Indicators:
- **Staff Duties Mode**: Green badge indicator on each page
- **Toggle Button Colors**:
  - Blue "My Profile" button when in staff mode
  - Green "Staff Duties" button when in personal mode

## Access Control
- Routes protected by middleware `check.user.type:2`
- Only users with `user_type = 2` can access staff routes
- Staff can still access all patient routes for their personal profile

## Database Considerations
Staff users in the system should:
- Have `user_type = 2` in the `users` table
- Have a corresponding patient record for personal health info
- Can prescribe medications (tracked via `prescribed_by` field)

## How Staff Differs from Doctors

**Staff (user_type = 2)**:
- Can toggle between staff duties and personal profile
- Has access to ALL clinic operations (not doctor-specific)
- Can manage appointments for all doctors
- Views aggregate statistics for the entire clinic
- Also has a patient record for personal health tracking

**Doctors (user_type = 3)**:
- Only see doctor-specific views
- Statistics filtered by their doctor ID
- Cannot toggle to patient view
- Professional role only

## Testing the Implementation

### Test as Staff User:
1. Create/use a user with `user_type = 2`
2. Login to the system
3. Verify navigation shows doctor-like menu
4. Click "Staff Duties" green button (if in personal mode)
5. Test all staff functionalities:
   - View dashboard statistics
   - Manage appointments
   - View patients list
   - Check medicine inventory
   - Create prescriptions
   - View reports
6. Click "My Profile" blue button
7. Verify switch to patient view
8. Confirm can view personal health records

### Required Database Setup:
```sql
-- Example staff user
INSERT INTO users (name, email, password, user_type) 
VALUES ('Jane Smith', 'staff@ckc.edu', '$2y$10$...', 2);

-- Corresponding patient record
INSERT INTO patients (user_id, f_name, l_name, patient_type, ...)
VALUES (user_id_from_above, 'Jane', 'Smith', 2, ...);
```

## Troubleshooting

**Issue**: Staff can't access /staff routes
- Check `user_type = 2` in database
- Verify middleware is working: `check.user.type:2`

**Issue**: Toggle button not showing
- Ensure using updated `navigation.blade.php`
- Check user is logged in with `user_type = 2`

**Issue**: Routes return 404
- Run: `php artisan route:clear`
- Run: `php artisan config:clear`

**Issue**: Views not found
- Verify all 8 blade files exist in `resources/views/staff/`
- Check file permissions

## Next Steps / Enhancements
- Add permission levels within staff (senior staff vs regular staff)
- Track staff actions with audit logs
- Add staff-specific reporting
- Implement staff scheduling system
- Add notification preferences for staff

## Files Modified/Created

### Created:
- `app/Http/Controllers/StaffDashboardController.php`
- `resources/views/staff/dashboard.blade.php`
- `resources/views/staff/appointments.blade.php`
- `resources/views/staff/patients.blade.php`
- `resources/views/staff/patient-details.blade.php`
- `resources/views/staff/health-records.blade.php`
- `resources/views/staff/medicines.blade.php`
- `resources/views/staff/prescriptions.blade.php`
- `resources/views/staff/reports.blade.php`

### Modified:
- `routes/web.php` - Added staff routes
- `resources/views/layouts/navigation.blade.php` - Added toggle functionality

---

**Implementation Date**: December 2024
**Laravel Version**: 10.x
**Status**: ✅ Complete and Ready for Testing
