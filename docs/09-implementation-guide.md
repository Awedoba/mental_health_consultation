# Implementation Guide

> **Version**: 1.0.0  
> **Last Updated**: December 2025  
> **Status**: Active Development

This document describes the technical implementation details, architecture, and recent improvements to the Mental Health Consultation Web App.

## Technology Stack

### Frontend

- **Framework**: Nuxt 4.2.2
- **UI Library**: Vue 3.5.25
- **Language**: TypeScript
- **Styling**: Tailwind CSS 4.x
- **Package Manager**: pnpm
- **Router**: Vue Router 4.6.3

### Backend

- **Framework**: Laravel 12
- **Language**: PHP 8.3.28
- **Authentication**: Laravel Sanctum 4.x
- **Database**: SQLite (development), supports PostgreSQL/MySQL
- **Testing**: Pest 4.x, PHPUnit 12.x
- **Code Style**: Laravel Pint

## Frontend Architecture

### Project Structure

```
frontend/
├── app.vue                 # Root component
├── assets/                 # Static assets (CSS)
├── components/            # Reusable Vue components
│   ├── ConfirmModal.vue
│   ├── EmptyState.vue
│   ├── FormError.vue
│   ├── LoadingSpinner.vue
│   └── Toast.vue
├── composables/           # Vue composables (shared logic)
│   ├── useAuth.ts
│   ├── useConsultations.ts
│   ├── useErrorHandler.ts
│   ├── usePatients.ts
│   ├── useToast.ts
│   └── useUsers.ts
├── layouts/               # Page layouts
│   └── default.vue        # Main application layout with sidebar
├── middleware/            # Route middleware
│   ├── auth.ts           # Authentication check
│   └── role.ts           # Role-based access control
├── pages/                 # Nuxt pages (file-based routing)
│   ├── admin/            # Admin-only pages
│   ├── consultations/    # Consultation management
│   ├── patients/         # Patient management
│   ├── dashboard.vue
│   ├── index.vue
│   └── login.vue
├── plugins/               # Nuxt plugins
│   └── auth.ts           # Auto-fetch user on app init
├── types/                 # TypeScript type definitions
│   └── index.ts
└── utils/                 # Utility functions
    └── api.ts            # (Removed - using $fetch directly)
```

### Key Architectural Patterns

#### 1. Composables Pattern

All API interactions are abstracted through composables:

- **`useAuth()`**: Authentication state and operations
- **`usePatients()`**: Patient CRUD operations
- **`useConsultations()`**: Consultation CRUD operations
- **`useUsers()`**: User management (admin only)
- **`useBillings()`**: Billing CRUD operations
- **`useMedications()`**: Medication master list operations
- **`usePrescriptions()`**: Prescription CRUD operations
- **`useHomeVisits()`**: Home visit CRUD operations
- **`useReports()`**: Report generation
- **`useErrorHandler()`**: Centralized error handling
- **`useToast()`**: Toast notification system

**Example Usage:**
```typescript
const { list, create, update } = usePatients()
const { showToast } = useToast()
const { handleApiError } = useErrorHandler()

const result = await create(patientData)
if (result.success) {
  showToast('success', 'Patient created successfully')
} else {
  handleApiError(result, 'Failed to create patient')
}
```

#### 2. Error Handling

Standardized error handling across all pages:

- **`useErrorHandler()`** composable provides:
  - `handleApiError()`: Parse and display API errors
  - `getValidationErrors()`: Extract validation errors
  - `showSuccess()`, `showWarning()`, `showInfo()`: Toast helpers

- **`FormError`** component displays:
  - Single error messages
  - Field-specific validation errors
  - Formatted field names (snake_case → Title Case)

#### 3. Authentication Flow

1. **Login**: User authenticates via `/login` page
2. **Token Storage**: JWT token stored in secure cookie
3. **Auto-fetch**: `plugins/auth.ts` fetches user data on app init
4. **Middleware**: `middleware/auth.ts` protects routes
5. **Role Check**: `middleware/role.ts` enforces admin-only routes

#### 4. API Communication

- Uses Nuxt's built-in `$fetch` utility
- Automatic token injection via request interceptors
- Centralized base URL via `runtimeConfig.public.apiBase`
- Automatic 401 handling (redirects to login)

### Recent Improvements

#### Form Data Population

All edit forms now properly populate with existing data:

- **Patient Edit**: Loads patient data including emergency contacts
- **User Edit**: Loads user profile data
- **Consultation Edit**: Loads consultation data with proper date/time formatting

**Date/Time Formatting:**
- Dates formatted as `YYYY-MM-DD` for date inputs
- Times formatted as `HH:MM` for time inputs
- Handles various input formats gracefully

#### Search & Filtering

- **Debounced Search**: 300-500ms delay to reduce API calls
- **Real-time Filters**: Status and risk assessment filters
- **Backend Support**: Search by patient/clinician name, filter by status/risk

#### Error Display

- Consistent use of `FormError` component
- Validation errors displayed per field
- Toast notifications for success/error messages

#### Accessibility

- ARIA labels on interactive elements
- Keyboard navigation (Escape key closes modals)
- Screen reader friendly structure

## Backend Architecture

### API Structure

```
/api
├── /auth                    # Authentication endpoints
│   ├── POST /login
│   ├── POST /logout
│   ├── GET /me
│   └── POST /password/change
├── /patients                # Patient management
│   ├── GET /                # List with filters
│   ├── POST /               # Create
│   ├── GET /{id}            # Show
│   ├── PUT /{id}            # Update
│   └── DELETE /{id}         # Delete
├── /consultations           # Consultation management
│   ├── GET /                # List with filters
│   ├── POST /               # Create
│   ├── GET /{id}            # Show
│   ├── PUT /{id}            # Update
│   ├── POST /{id}/lock      # Finalize
│   └── DELETE /{id}         # Delete
├── /admin/users             # User management (admin only)
│   ├── GET /
│   ├── POST /
│   ├── GET /{id}
│   ├── PUT /{id}
│   ├── POST /{id}/toggle-active
│   └── DELETE /{id}
└── /dashboard               # Dashboard statistics
    └── GET /
```

### API Response Format

#### Success Response

```json
{
  "message": "Success message",
  "data": { /* resource data */ },
  "meta": { /* pagination or metadata */ }
}
```

#### Error Response

```json
{
  "error": {
    "message": "Error message",
    "errors": {
      "field_name": ["Validation error message"]
    }
  }
}
```

#### Paginated Response

```json
{
  "message": "Success message",
  "data": [ /* array of resources */ ],
  "meta": {
    "pagination": {
      "page": 1,
      "current_page": 1,
      "last_page": 5,
      "total_pages": 5,
      "per_page": 20,
      "total": 100,
      "from": 1,
      "to": 20
    }
  }
}
```

### Computed Fields

The API automatically adds computed fields for frontend convenience:

#### Consultation Responses

- **`patient_name`**: Full name from patient relationship
- **`clinician_name`**: Full name from primary clinician relationship
- **`diagnosis`**: Primary diagnosis summary (if exists)
- **`treatment_plan`**: Treatment plan summary (if exists)

These fields are added in:
- `ConsultationController::index()` - For list views
- `ConsultationController::show()` - For detail views

### Database Models

#### Key Model Relationships

```php
Consultation
├── belongsTo Patient
├── belongsTo User (primaryClinician)
├── hasMany ConsultationCollaborator
├── hasOne MentalStateExam
├── hasMany Diagnosis
├── hasOne ManagementPlan
└── hasMany ConsultationReview

Patient
├── belongsTo User (createdBy)
├── hasMany EmergencyContact
├── hasMany Consultation
└── hasMany ConsultationReview
```

#### Table Naming

- Most tables use plural names (e.g., `consultations`, `patients`)
- Exception: `mental_state_exam` (singular) - model specifies `protected $table = 'mental_state_exam'`

### Recent Backend Improvements

#### Consultation List Enhancements

1. **Computed Fields**: Added `patient_name` and `clinician_name` to list responses
2. **Search Filter**: Search by patient or clinician name
3. **Status Filter**: Filter by locked/unlocked status
4. **Risk Filter**: Filter by risk assessment level

#### Date/Time Formatting

- Consultation dates formatted as `Y-m-d` for form compatibility
- Consultation times formatted as `H:i` for form compatibility
- Proper handling of date/time objects vs strings

#### Error Handling

- Graceful handling of missing optional relationships
- Try-catch blocks for optional table loading
- Proper error responses with validation details

## API Endpoints Reference

### Authentication

#### POST `/api/auth/login`
```json
{
  "username": "string",
  "password": "string"
}
```

**Response:**
```json
{
  "data": {
    "user": { /* User object */ },
    "token": "jwt_token_string"
  },
  "message": "Login successful"
}
```

#### GET `/api/auth/me`
**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "data": { /* User object */ }
}
```

### Patients

#### GET `/api/patients?search={query}&is_active={bool}&age_min={int}&age_max={int}`
**Query Parameters:**
- `search`: Search by name
- `is_active`: Filter by active status
- `age_min`, `age_max`: Age range filter

#### POST `/api/patients`
**Body:**
```json
{
  "first_name": "string",
  "last_name": "string",
  "date_of_birth": "YYYY-MM-DD",
  "gender": "male|female|non_binary|prefer_not_say|other",
  "phone_number": "string",
  "email": "string",
  "landmark": "string",
  "city": "string",
  "town": "string (optional)",
  "state_province": "string",
  "postal_code": "string",
  "country": "string",
  "marital_status": "single|married|divorced|widowed|separated",
  "occupation": "string (optional)",
  "education_level": "none|primary|secondary|tertiary|university|postgraduate",
  "religion": "string (optional)",
  "nhis_status": "insured|uninsured",
  "relative_name": "string (optional)",
  "relative_relationship": "string (optional)",
  "relative_phone": "string (optional)",
  "assessment_time": "HH:MM (optional)",
  "emergency_contacts": [
    {
      "contact_name": "string",
      "relationship": "string",
      "phone_number": "string",
      "is_primary": boolean
    }
  ]
}
```

### Consultations

#### GET `/api/consultations?search={query}&status={locked|unlocked}&risk_assessment={low|moderate|high}&page={int}&per_page={int}`
**Query Parameters:**
- `search`: Search by patient or clinician name
- `status`: Filter by locked/unlocked
- `risk_assessment`: Filter by risk level
- `page`, `per_page`: Pagination

**Response includes:**
- `patient_name`: Computed from patient relationship
- `clinician_name`: Computed from primaryClinician relationship

#### POST `/api/consultations`
**Body:**
```json
{
  "patient_id": "uuid",
  "consultation_date": "YYYY-MM-DD",
  "consultation_time": "HH:MM",
  "session_type": "initial_assessment|follow_up|crisis_intervention|therapy_session|medication_management",
  "session_duration": integer,
  "chief_complaint": "string",
  "history_present_illness": "string",
  "past_psychiatric_history": "string (optional)",
  "medical_history": "string (optional)",
  "personal_history": "string (optional)",
  "occupational_marital_history": "string (optional)",
  "family_history": "string (optional)",
  "substance_abuse_history": "string (optional)",
  "risk_assessment": "low|moderate|high",
  "clinical_summary": "string" // Required
}
```

#### GET `/api/consultations/{id}`
**Response includes:**
- All consultation fields
- `patient_name` and `clinician_name` (computed)
- `diagnosis` (from diagnoses relationship)
- `treatment_plan` (from managementPlan relationship)
- Formatted dates/times for form inputs

### Billing

#### GET `/api/billings?patient_id={uuid}&payment_status={status}&service_type={type}&page={int}`
**Query Parameters:**
- `patient_id`: Filter by patient
- `payment_status`: Filter by payment status (pending, partial, paid, waived)
- `service_type`: Filter by service type (consultation, home_visit, medication, other)
- `page`, `per_page`: Pagination

#### POST `/api/billings`
**Body:**
```json
{
  "patient_id": "uuid",
  "consultation_id": "uuid (optional)",
  "billing_date": "YYYY-MM-DD",
  "service_type": "consultation|home_visit|medication|other",
  "amount": decimal,
  "nhis_covered": boolean,
  "nhis_amount": decimal (optional),
  "patient_amount": decimal (optional),
  "payment_status": "pending|partial|paid|waived",
  "payment_date": "YYYY-MM-DD (optional)",
  "payment_method": "cash|mobile_money|bank_transfer|nhis (optional)",
  "notes": "string (optional)"
}
```

#### GET `/api/billings/patient/{patientId}`
Get all billings for a specific patient.

### Medications (Master List)

#### GET `/api/medications?search={query}&category={category}&is_active={bool}`
**Query Parameters:**
- `search`: Search by name or generic name
- `category`: Filter by medication category
- `is_active`: Filter by active status

#### POST `/api/medications` (Admin only)
**Body:**
```json
{
  "name": "string",
  "generic_name": "string (optional)",
  "dosage_form": "tablet|capsule|syrup|injection|other",
  "strength": "string (optional)",
  "category": "string (optional)",
  "is_active": boolean
}
```

### Prescriptions

#### GET `/api/prescriptions?patient_id={uuid}&is_active={bool}&page={int}`
**Query Parameters:**
- `patient_id`: Filter by patient
- `is_active`: Filter by active status
- `page`, `per_page`: Pagination

#### POST `/api/prescriptions`
**Body:**
```json
{
  "patient_id": "uuid",
  "consultation_id": "uuid (optional)",
  "prescription_date": "YYYY-MM-DD",
  "medication_id": "uuid",
  "dosage": "string",
  "frequency": "string",
  "duration": "string",
  "quantity": integer (optional),
  "instructions": "string (optional)",
  "refills": integer,
  "start_date": "YYYY-MM-DD (optional)",
  "end_date": "YYYY-MM-DD (optional)"
}
```

#### GET `/api/prescriptions/patient/{patientId}`
Get all prescriptions for a specific patient.

### Home Visits

#### GET `/api/home-visits?patient_id={uuid}&clinician_id={uuid}&date_from={date}&date_to={date}&search={query}&page={int}`
**Query Parameters:**
- `patient_id`: Filter by patient
- `clinician_id`: Filter by clinician
- `date_from`, `date_to`: Date range filter
- `search`: Search by client name, community, or contact
- `page`, `per_page`: Pagination

#### POST `/api/home-visits`
**Body:**
```json
{
  "patient_id": "uuid (optional)",
  "client_name": "string",
  "age": integer (optional),
  "sex": "male|female|other (optional)",
  "community_location": "string",
  "contact": "string",
  "visit_date": "YYYY-MM-DD",
  "visit_time": "HH:MM (optional)",
  "diagnosis_condition": "string (optional)",
  "medication_prescription": "string (optional)",
  "observations": "string (optional)",
  "impression": "string (optional)",
  "management": "string (optional)",
  "recommendation": "string (optional)"
}
```

#### GET `/api/home-visits/patient/{patientId}`
Get all home visits for a specific patient.

### Reports

#### GET `/api/reports/client-list?name={query}&age_from={int}&age_to={int}&sex={sex}&nhis_status={status}&date_from={date}&date_to={date}&is_active={bool}`
Generate client list report with demographics and NHIS status.

#### GET `/api/reports/client-query?search={query}&age_from={int}&age_to={int}&sex={sex}&marital_status={status}&nhis_status={status}&city={city}&town={town}&created_from={date}&created_to={date}&is_active={bool}`
Advanced client search with multiple filters.

#### GET `/api/reports/consulting-history/{patientId}?date_from={date}&date_to={date}`
Get complete consultation history for a patient.

#### GET `/api/reports/next-visits?date_from={date}&date_to={date}&clinician_id={uuid}`
Get upcoming scheduled visits from management plans.

#### GET `/api/reports/consulting-data-query?date_from={date}&date_to={date}&session_type={type}&patient_id={uuid}&clinician_id={uuid}&has_mse={bool}&has_diagnosis={bool}&has_management_plan={bool}`
Query consultation data with advanced filters.

#### GET `/api/reports/trends?date_from={date}&date_to={date}&group_by={period}`
Generate trend report with consultation trends and weight tracking.

#### GET `/api/reports/total-cases?date_from={date}&date_to={date}`
Generate summary statistics report with breakdowns.

## Frontend Components

### Reusable Components

#### `FormError`
Displays validation errors and error messages.

**Props:**
- `error`: String error message
- `errors`: Object with field-specific errors
- `title`: Optional error title

**Usage:**
```vue
<FormError :error="error" :errors="validationErrors" />
```

#### `LoadingSpinner`
Displays loading state.

**Props:**
- `size`: "sm" | "md" | "lg"
- `color`: "white" | default
- `text`: Optional loading text

#### `Toast`
Global toast notification system.

**Usage:**
```typescript
const { showToast } = useToast()
showToast('success', 'Operation completed')
showToast('error', 'Something went wrong')
```

#### `EmptyState`
Displays empty state when no data.

**Props:**
- `title`: Empty state title
- `description`: Description text
- `action-label`: Button label
- `@action`: Action handler

### Layout Components

#### `default.vue` Layout

Features:
- Responsive sidebar navigation
- Mobile-friendly hamburger menu
- User profile dropdown
- Role-based menu items
- Keyboard navigation (Escape key)

## Development Workflow

### Running the Application

#### Frontend
```bash
cd frontend
pnpm install
pnpm dev  # Development server on http://localhost:3000
```

#### Backend
```bash
cd backend
composer install
php artisan migrate
php artisan serve  # API server on http://localhost:8000
```

### Environment Variables

#### Frontend (`.env`)
```
API_BASE_URL=http://localhost:8000/api
```

#### Backend (`.env`)
```
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### Code Quality

#### Frontend
- TypeScript strict mode enabled
- ESLint for code quality
- No linting errors in current codebase

#### Backend
- Laravel Pint for code formatting
- Pest for testing
- PHP 8.3 type hints throughout

## Recent Bug Fixes

### Fixed Issues

1. **Dashboard Token Bug**: Fixed incorrect token reference (`user.token` → `token.value`)
2. **Consultation List**: Added patient/clinician names, fixed pagination
3. **Consultation View**: Added computed fields for patient/clinician names
4. **Form Population**: All edit forms now properly load existing data
5. **Date/Time Formatting**: Proper formatting for form inputs
6. **Error Handling**: Standardized across all pages
7. **Search Debouncing**: Added to reduce API calls
8. **Validation Errors**: Proper display in all forms
9. **Table Name Fix**: Fixed `MentalStateExam` model table name
10. **Pagination**: Fixed NaN errors in pagination display

## Best Practices

### Frontend

1. **Always use composables** for API calls
2. **Use `FormError` component** for error display
3. **Debounce search inputs** (300-500ms)
4. **Format dates/times** before displaying
5. **Handle loading states** with `LoadingSpinner`
6. **Use TypeScript types** from `types/index.ts`

### Backend

1. **Use Form Requests** for validation (when creating new endpoints)
2. **Add computed fields** for frontend convenience
3. **Handle optional relationships** gracefully
4. **Return consistent error format**
5. **Use Eloquent relationships** instead of manual joins
6. **Format dates/times** in API responses when needed

## Testing

### Frontend Testing
- Manual testing recommended for UI components
- TypeScript provides compile-time type checking

### Backend Testing
```bash
php artisan test
php artisan test --filter=ConsultationTest
```

## Deployment Considerations

### Frontend
- Build command: `pnpm build`
- Output: `.output/public/`
- Requires Node.js 18+ and pnpm

### Backend
- Requires PHP 8.3+
- Run migrations: `php artisan migrate`
- Set up environment variables
- Configure database connection
- Set up SSL/TLS for production

## Security Notes

- JWT tokens stored in secure, httpOnly cookies
- CSRF protection via Laravel Sanctum
- Role-based access control enforced
- Input validation on all endpoints
- SQL injection protection via Eloquent ORM
- XSS protection via Vue's template escaping

---

**Next Steps**: See [System Overview](01-system-overview.md) for business requirements and [Feature Breakdown](02-feature-breakdown.md) for detailed feature specifications.

