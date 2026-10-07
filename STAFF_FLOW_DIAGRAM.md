# Staff User Flow Diagram

## System Architecture for Staff Users

```
┌─────────────────────────────────────────────────────────────────┐
│                      STAFF USER (user_type = 2)                 │
│                                                                  │
│  ┌──────────────────────┐         ┌──────────────────────┐     │
│  │                      │         │                      │     │
│  │   STAFF DUTIES MODE  │◄────────┤  PERSONAL MODE       │     │
│  │   (Doctor-like)      │         │  (Patient-like)      │     │
│  │                      │────────►│                      │     │
│  └──────────────────────┘  Toggle └──────────────────────┘     │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                    STAFF DUTIES MODE                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Navigation Bar:                                                │
│  ┌──────────────────────────────────────────────────┐          │
│  │ [Dashboard] [Appointments] [Patients] [Health]   │          │
│  │ [Medicines] [Prescriptions] [Reports]            │          │
│  │                           [🔵 My Profile] [User▼]│          │
│  └──────────────────────────────────────────────────┘          │
│                                                                  │
│  Routes: /staff/*                                               │
│  Controller: StaffDashboardController                           │
│                                                                  │
│  Features:                                                      │
│  ✓ View all appointments (all doctors)                         │
│  ✓ Manage all patients                                         │
│  ✓ View all health records                                     │
│  ✓ Manage medicine inventory                                   │
│  ✓ Create/manage prescriptions                                 │
│  ✓ Generate clinic reports                                     │
│  ✓ Update appointment statuses                                 │
│                                                                  │
│  Visual Indicator:                                              │
│  ┌──────────────────────────────────────────────┐              │
│  │ 🟢 Staff Duties Mode - Managing clinic ops   │              │
│  └──────────────────────────────────────────────┘              │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                     PERSONAL MODE                                │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Navigation Bar:                                                │
│  ┌──────────────────────────────────────────────────┐          │
│  │ [Dashboard] [Health Records] [Appointments]      │          │
│  │                         [🟢 Staff Duties] [User▼]│          │
│  └──────────────────────────────────────────────────┘          │
│                                                                  │
│  Routes: /patients/*                                            │
│  Controller: PatientsViewPersonalHealthRecord, etc.             │
│                                                                  │
│  Features:                                                      │
│  ✓ View own health records                                     │
│  ✓ Schedule personal appointments                              │
│  ✓ View own medical history                                    │
│  ✓ Update personal profile                                     │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘

## Database Structure

users table:
┌──────┬─────────────┬──────────┬────────────┐
│ id   │ name        │ email    │ user_type  │
├──────┼─────────────┼──────────┼────────────┤
│ 1    │ John Doe    │ student@ │ 1 (Student)│
│ 2    │ Jane Staff  │ staff@   │ 2 (Staff)  │ ◄── Can toggle!
│ 3    │ Dr. Smith   │ doctor@  │ 3 (Doctor) │
└──────┴─────────────┴──────────┴────────────┘

patients table:
┌──────┬─────────┬──────────┬──────────────┐
│ id   │ user_id │ f_name   │ patient_type │
├──────┼─────────┼──────────┼──────────────┤
│ 1    │ 1       │ John     │ 1 (Student)  │
│ 2    │ 2       │ Jane     │ 2 (Staff)    │ ◄── Has patient record
└──────┴─────────┴──────────┴──────────────┘

## Toggle Mechanism

┌─────────────────────────────────────────────┐
│ request()->routeIs('staff.*')               │
│              ?                              │
│  ┌──────────┴──────────┐                   │
│  │                     │                   │
│  ▼ TRUE               ▼ FALSE              │
│ STAFF DUTIES        PERSONAL MODE          │
│ Show:               Show:                  │
│ - staff routes      - patients routes      │
│ - Blue button       - Green button         │
│   "My Profile"        "Staff Duties"       │
│                                             │
└─────────────────────────────────────────────┘

## Access Control Matrix

┌──────────────┬────────────┬─────────────┬──────────────┐
│ Feature      │ Student(1) │ Staff(2)    │ Doctor(3)    │
├──────────────┼────────────┼─────────────┼──────────────┤
│ Personal     │ ✓          │ ✓ (toggle)  │ ✗            │
│ Records      │            │             │              │
├──────────────┼────────────┼─────────────┼──────────────┤
│ Manage       │ ✗          │ ✓ (toggle)  │ ✓            │
│ Patients     │            │             │              │
├──────────────┼────────────┼─────────────┼──────────────┤
│ Prescriptions│ ✗          │ ✓ (toggle)  │ ✓            │
├──────────────┼────────────┼─────────────┼──────────────┤
│ Medicine     │ ✗          │ ✓ (toggle)  │ ✓            │
│ Inventory    │            │             │              │
├──────────────┼────────────┼─────────────┼──────────────┤
│ Reports      │ ✗          │ ✓ (toggle)  │ ✓            │
├──────────────┼────────────┼─────────────┼──────────────┤
│ Toggle       │ ✗          │ ✓           │ ✗            │
│ Modes        │            │             │              │
└──────────────┴────────────┴─────────────┴──────────────┘

## Navigation Code Logic

```php
@if(Auth::user()->user_type == 1)
    // Student - limited patient view only
    
@elseif(Auth::user()->user_type == 2)
    // Staff - toggle between modes
    @if(request()->routeIs('staff.*'))
        // Show staff navigation + "My Profile" button
    @else
        // Show patient navigation + "Staff Duties" button
    @endif
    
@else
    // Doctor (user_type == 3)
    // Show doctor navigation only
@endif
```

## Key Differences: Staff vs Doctor

STAFF:
- Manages ALL clinic operations
- Can view/manage appointments for ALL doctors
- Statistics show clinic-wide data
- Has personal patient record
- Can toggle between work and personal view

DOCTOR:
- Manages only THEIR patients
- Can view/manage only THEIR appointments  
- Statistics filtered by doctor_id
- No personal patient record
- No toggle functionality
- Professional role only
```
