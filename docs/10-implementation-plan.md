# Implementation Plan - Enhanced Requirements

> **Version**: 2.0.0  
> **Date**: December 2025  
> **Status**: Planning Phase

## Overview

This document outlines the implementation plan for the enhanced Mental Health Consultation Web App based on updated requirements. The plan includes modifications to existing modules and the addition of new features.

---

## 1. Patient/Client Management Updates

### 1.1 New Fields Required

#### Patient Demographics Updates

| Current Field | New/Updated Field | Type | Required | Notes |
|--------------|------------------|------|----------|-------|
| `address_line1`, `address_line2` | `landmark` | Text | Yes | Replace address with landmark |
| - | `religion` | Dropdown/Text | No | Religion/faith |
| - | `town` | Text | Yes | Town/location |
| - | `nhis_status` | Enum | Yes | 'insured', 'uninsured' |
| - | `relative_name` | Text | Yes | Name of relative/contact |
| - | `relative_relationship` | Dropdown | Yes | Relationship to patient |
| - | `relative_phone` | Text | Yes | Relative's phone number |
| - | `assessment_time` | Time | Yes | Default assessment time |

#### Emergency Contact Updates

| Field | Update | Notes |
|-------|--------|-------|
| Current `emergency_contacts` table | Keep existing | Maintain backward compatibility |
| Add `relative_name`, `relative_phone`, `relative_relationship` | New fields on patient table | Primary relative information |

### 1.2 Database Changes

**Migration**: `2025_12_XX_add_patient_enhancements.php`

```php
// Add new columns to patients table
$table->string('religion', 50)->nullable();
$table->string('town', 100)->nullable();
$table->enum('nhis_status', ['insured', 'uninsured'])->default('uninsured');
$table->string('relative_name', 100)->nullable();
$table->string('relative_relationship', 50)->nullable();
$table->string('relative_phone', 20)->nullable();
$table->time('assessment_time')->nullable();

// Modify address fields
$table->renameColumn('address_line1', 'landmark');
$table->dropColumn('address_line2'); // If not needed
// Or keep both: address_line1 -> landmark, address_line2 -> additional_landmark
```

### 1.3 Frontend Updates

- Update patient create/edit forms
- Add new fields with proper validation
- Update TypeScript interfaces
- Add NHIS status dropdown
- Add relative information section
- Replace address fields with landmark field

---

## 2. Consultation Management Updates

### 2.1 New Assessment Form Fields

#### History & Background

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `presenting_complaint` | Text Area | Yes | Already exists as `chief_complaint` |
| `history_presenting_complaint` | Text Area | Yes | Already exists as `history_present_illness` |
| `past_psychiatric_illness` | Text Area | No | Already exists as `past_psychiatric_history` |
| `past_medical_history` | Text Area | No | Already exists as `medical_history` |
| `personal_history` | Text Area | No | **NEW** |
| `occupational_marital_history` | Text Area | No | **NEW** - Combined field |
| `family_history` | Text Area | No | Already exists |
| `substance_abuse_history` | Text Area | No | **NEW** |

### 2.2 Mental State Examination Updates

#### Appearance & Behavior (Enhanced)

| Field | Type | Options | Notes |
|-------|------|---------|-------|
| `appearance_clothing` | Dropdown | 'dirty', 'fairly_neat', 'very_neat' | **NEW** |
| `posture` | Dropdown | 'limping', 'walking_well' | **NEW** |
| `grooming` | Text Area | - | **NEW** - Free text |
| `behavior` | Text Area | - | **NEW** - Free text |
| `movement` | Text Area | - | **NEW** - Free text |
| `attitude` | Text Area | - | **NEW** - Free text |
| `speech` | Text Area | - | **NEW** - Free text |
| `sense_of_self` | Text Area | - | **NEW** - Free text |
| `motivation` | Text Area | - | **NEW** - Free text |

#### Cognitive Functioning (Enhanced)

| Field | Type | Options | Notes |
|-------|------|---------|-------|
| `general_information` | Text Area | - | **NEW** |
| `attention` | Dropdown | 'intact', 'impaired', 'grossly_impaired' | Already exists |
| `concentration` | Dropdown | 'intact', 'impaired', 'grossly_impaired' | Already exists |
| `memory` | Dropdown | 'intact', 'impaired', 'grossly_impaired' | Already exists (multiple types) |
| `physical_coordination` | Text Area | - | **NEW** |
| `mood` | Text Area | - | **NEW** |
| `affect` | Text Area | - | **NEW** |

#### Perceptual Disturbances

| Field | Type | Options | Notes |
|-------|------|---------|-------|
| `hallucination_type` | Multi-select | 'auditory', 'visual', 'tactile', 'olfactory', 'gustatory' | Already exists as JSON |
| `illusion` | Checkbox/Text | - | Already exists |

#### Thought Disturbances

| Field | Type | Options | Notes |
|-------|------|---------|-------|
| `delusion_type` | Multi-select | 'persecutory', 'grandiose', 'somatic', etc. | Already exists as JSON |
| `insight` | Dropdown | 'good', 'fair', 'poor', 'absent' | Already exists |
| `orientation` | Text Area | - | **NEW** - Free text for person/place/time details |
| `judgment` | Dropdown | 'good', 'fair', 'poor', 'grossly_impaired' | Already exists |

#### Orientation Details (NEW)

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `orientation_person` | Text Area | No | Space to write if oriented to person |
| `orientation_place` | Text Area | No | Space to write if oriented to place |
| `orientation_time` | Text Area | No | Space to write if oriented to time |

### 2.3 Review Section Updates

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `review_date` | Date | Yes | Already exists |
| `review_time` | Time | Yes | Already exists |
| `bp` | Text | No | Blood pressure (systolic/diastolic) |
| `weight` | Decimal | No | **ENHANCED** - Track weight |
| `presenting_complaints` | Text Area | No | **NEW** |
| `oe` | Text Area | No | **NEW** - Objective examination |
| `odq` | Text Area | No | **NEW** - Objective data/quantitative |
| `diagnostic_impression` | Text Area | Yes | Already exists |
| `management_plan` | Text Area | Yes | Already exists |
| `recommendation` | Text Area | No | **NEW** - For observation time, etc. |

### 2.4 Database Changes

**Migration**: `2025_12_XX_enhance_consultations.php`

```php
// Add new consultation fields
$table->text('personal_history')->nullable();
$table->text('occupational_marital_history')->nullable();
$table->text('substance_abuse_history')->nullable();

// Enhance mental_state_exam table
$table->string('appearance_clothing')->nullable();
$table->string('posture')->nullable();
$table->text('grooming')->nullable();
$table->text('behavior')->nullable();
$table->text('movement')->nullable();
$table->text('attitude')->nullable();
$table->text('speech')->nullable();
$table->text('sense_of_self')->nullable();
$table->text('motivation')->nullable();
$table->text('general_information')->nullable();
$table->text('physical_coordination')->nullable();
$table->text('mood')->nullable();
$table->text('affect')->nullable();
$table->text('orientation_person')->nullable();
$table->text('orientation_place')->nullable();
$table->text('orientation_time')->nullable();
$table->text('orientation_details')->nullable(); // Combined field

// Enhance consultation_reviews table
$table->text('presenting_complaints')->nullable();
$table->text('oe')->nullable(); // Objective examination
$table->text('odq')->nullable(); // Objective data/quantitative
$table->text('recommendation')->nullable();
```

---

## 3. New Module: Billing

### 3.1 Overview

Track billing information for clients to support financial management and insurance claims.

### 3.2 Database Schema

**Table**: `billings`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Unique billing ID |
| `patient_id` | UUID | FOREIGN KEY, NOT NULL | Patient |
| `consultation_id` | UUID | FOREIGN KEY, NULL | Linked consultation (if applicable) |
| `billing_date` | DATE | NOT NULL | Date of billing |
| `service_type` | ENUM | NOT NULL | 'consultation', 'home_visit', 'medication', 'other' |
| `amount` | DECIMAL(10,2) | NOT NULL | Billing amount |
| `nhis_covered` | BOOLEAN | NOT NULL | Whether NHIS covers this |
| `nhis_amount` | DECIMAL(10,2) | NULL | Amount covered by NHIS |
| `patient_amount` | DECIMAL(10,2) | NULL | Amount paid by patient |
| `payment_status` | ENUM | NOT NULL | 'pending', 'partial', 'paid', 'waived' |
| `payment_date` | DATE | NULL | Date payment received |
| `payment_method` | ENUM | NULL | 'cash', 'mobile_money', 'bank_transfer', 'nhis' |
| `invoice_number` | VARCHAR(50) | UNIQUE | Auto-generated invoice number |
| `notes` | TEXT | NULL | Additional notes |
| `created_by` | UUID | FOREIGN KEY | User who created billing |
| `created_at` | TIMESTAMP | NOT NULL | Creation timestamp |
| `updated_at` | TIMESTAMP | NOT NULL | Update timestamp |

### 3.3 Features

- Create billing record for consultation/home visit
- Track NHIS coverage
- Payment tracking
- Invoice generation
- Billing reports

---

## 4. New Module: Prescription

### 4.1 Overview

Track prescriptions and medications prescribed to patients, with a searchable medication list.

### 4.2 Database Schema

**Table**: `medications` (Master list)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Unique medication ID |
| `name` | VARCHAR(200) | NOT NULL, UNIQUE | Medication name |
| `generic_name` | VARCHAR(200) | NULL | Generic name |
| `dosage_form` | ENUM | NULL | 'tablet', 'capsule', 'syrup', 'injection', 'other' |
| `strength` | VARCHAR(50) | NULL | e.g., '10mg', '5ml' |
| `category` | VARCHAR(100) | NULL | Medication category |
| `is_active` | BOOLEAN | NOT NULL | Active medication |
| `created_at` | TIMESTAMP | NOT NULL | Creation timestamp |
| `updated_at` | TIMESTAMP | NOT NULL | Update timestamp |

**Table**: `prescriptions`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Unique prescription ID |
| `patient_id` | UUID | FOREIGN KEY, NOT NULL | Patient |
| `consultation_id` | UUID | FOREIGN KEY, NULL | Linked consultation |
| `prescription_date` | DATE | NOT NULL | Date prescribed |
| `prescribed_by` | UUID | FOREIGN KEY, NOT NULL | Prescribing clinician |
| `medication_id` | UUID | FOREIGN KEY, NOT NULL | Medication from master list |
| `dosage` | VARCHAR(100) | NOT NULL | e.g., '10mg twice daily' |
| `frequency` | VARCHAR(100) | NOT NULL | e.g., 'BID', 'TID', 'QID' |
| `duration` | VARCHAR(100) | NOT NULL | e.g., '7 days', '1 month' |
| `quantity` | INTEGER | NULL | Number of units |
| `instructions` | TEXT | NULL | Special instructions |
| `refills` | INTEGER | DEFAULT 0 | Number of refills allowed |
| `is_active` | BOOLEAN | NOT NULL | Active prescription |
| `start_date` | DATE | NULL | When to start |
| `end_date` | DATE | NULL | When to stop |
| `created_at` | TIMESTAMP | NOT NULL | Creation timestamp |
| `updated_at` | TIMESTAMP | NOT NULL | Update timestamp |

### 4.3 Features

- Medication master list (searchable, clickable)
- Create prescription from medication list
- Link prescription to consultation
- Track active prescriptions
- Prescription history
- Medication adherence tracking (in reviews)

---

## 5. New Module: Home Visits

### 5.1 Overview

Track home visits similar to consultations but with location-based information.

### 5.2 Database Schema

**Table**: `home_visits`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Unique home visit ID |
| `patient_id` | UUID | FOREIGN KEY, NULL | Patient (if registered) |
| `client_name` | VARCHAR(200) | NOT NULL | Name of client |
| `age` | INTEGER | NULL | Age of client |
| `sex` | ENUM | NULL | 'male', 'female', 'other' |
| `community_location` | VARCHAR(200) | NOT NULL | Community or location |
| `contact` | VARCHAR(20) | NOT NULL | Contact number |
| `visit_date` | DATE | NOT NULL | Date of visit |
| `visit_time` | TIME | NULL | Time of visit |
| `clinician_id` | UUID | FOREIGN KEY, NOT NULL | Visiting clinician |
| `diagnosis_condition` | TEXT | NULL | Diagnosis or condition |
| `medication_prescription` | TEXT | NULL | Medication or prescription |
| `observations` | TEXT | NULL | Observations seen |
| `impression` | TEXT | NULL | Clinical impression |
| `management` | TEXT | NULL | Management plan |
| `recommendation` | TEXT | NULL | Recommendations |
| `created_at` | TIMESTAMP | NOT NULL | Creation timestamp |
| `updated_at` | TIMESTAMP | NOT NULL | Update timestamp |

### 5.3 Features

- Create home visit record
- Link to patient (if registered) or standalone
- Track location and community
- Document visit details
- Home visit reports

---

## 6. Reports & Analytics Updates

### 6.1 New Reports Required

| Report Name | Description | Filters | Output |
|-------------|-------------|---------|--------|
| **Client List** | All clients with demographics | Name, Age, Sex, NHIS status | CSV, PDF |
| **Client List Query** | Advanced client search | Multiple filters | CSV, PDF |
| **Client Consulting History** | All consultations for a client | Patient ID, Date range | PDF |
| **Next Visit Report** | Upcoming scheduled visits | Date range, Clinician | CSV, PDF |
| **Consulting Data Query** | Consultation data with filters | Date range, Type, Status | CSV, PDF |
| **Trend Report** | Consultation trends over time | Date range, Group by period | PDF with charts |
| **Total Cases** | Summary statistics | Date range | PDF |

### 6.2 Report Enhancements

- Add recommendation section to all consultation reports
- Include weight tracking in trend reports
- Add NHIS status to client reports
- Include billing information in reports (if applicable)

---

## 7. Implementation Phases

### Phase 1: Patient Management Updates (Week 1-2)

**Priority**: High

1. ✅ Create database migration for patient enhancements
2. ✅ Update Patient model
3. ✅ Update PatientController API
4. ✅ Update frontend patient forms
5. ✅ Update TypeScript interfaces
6. ✅ Test patient CRUD operations

**Deliverables**:
- Updated patient registration form
- NHIS status tracking
- Relative information fields
- Landmark field (replacing address)

### Phase 2: Consultation Form Updates (Week 2-3)

**Priority**: High

1. ✅ Create database migration for consultation enhancements
2. ✅ Update Consultation model
3. ✅ Update MentalStateExam model
4. ✅ Update ConsultationController API
5. ✅ Update frontend consultation forms
6. ✅ Add new MSE fields
7. ✅ Add orientation details section
8. ✅ Add recommendation field to reviews

**Deliverables**:
- Enhanced consultation form
- Updated MSE form with all new fields
- Review form with recommendation section

### Phase 3: Billing Module (Week 3-4)

**Priority**: Medium

1. ✅ Create billing migration
2. ✅ Create Billing model
3. ✅ Create BillingController
4. ✅ Create billing API endpoints
5. ✅ Create frontend billing pages
6. ✅ Add billing to consultation workflow

**Deliverables**:
- Billing creation form
- Billing list view
- Payment tracking
- Invoice generation

### Phase 4: Prescription Module (Week 4-5)

**Priority**: Medium

1. ✅ Create medications master list migration
2. ✅ Create prescriptions migration
3. ✅ Create Medication and Prescription models
4. ✅ Create PrescriptionController
5. ✅ Create medication search API
6. ✅ Create frontend prescription pages
7. ✅ Add medication picker component

**Deliverables**:
- Medication master list management
- Prescription creation form
- Prescription history view
- Medication picker with search

### Phase 5: Home Visits Module (Week 5-6)

**Priority**: Medium

1. ✅ Create home_visits migration
2. ✅ Create HomeVisit model
3. ✅ Create HomeVisitController
4. ✅ Create home visit API endpoints
5. ✅ Create frontend home visit pages
6. ✅ Link home visits to patients (optional)

**Deliverables**:
- Home visit creation form
- Home visit list view
- Home visit detail view
- Home visit reports

### Phase 6: Reports Enhancement (Week 6-7)

**Priority**: Medium

1. ✅ Create new report endpoints
2. ✅ Implement client list query
3. ✅ Implement consulting history report
4. ✅ Implement next visit report
5. ✅ Implement trend report
6. ✅ Implement total cases report
7. ✅ Add recommendation section to reports
8. ✅ Update frontend reports pages

**Deliverables**:
- All new report types
- Enhanced report filters
- Report export (CSV, PDF)
- Dashboard updates

### Phase 7: Testing & Documentation (Week 7-8)

**Priority**: High

1. ✅ End-to-end testing
2. ✅ Update API documentation
3. ✅ Update user documentation
4. ✅ Update data model documentation
5. ✅ Performance testing
6. ✅ Security review

**Deliverables**:
- ✅ Test reports
- ✅ Updated documentation
- ✅ Deployment guide
- ✅ Testing checklist

---

## 8. Database Migration Strategy

### 8.1 Backward Compatibility

- Keep existing fields where possible
- Add new fields as nullable initially
- Migrate data gradually
- Maintain API backward compatibility

### 8.2 Migration Order

1. Patient enhancements (additive)
2. Consultation enhancements (additive)
3. Mental state exam enhancements (additive)
4. Review enhancements (additive)
5. New tables (billing, medications, prescriptions, home_visits)

### 8.3 Data Migration

- No data loss during migration
- Default values for new required fields
- Preserve existing relationships

---

## 9. API Endpoints to Add/Update

### 9.1 Patient Endpoints (Updates)

```
PUT /api/patients/{id} - Update with new fields
GET /api/patients/{id} - Return new fields
```

### 9.2 Consultation Endpoints (Updates)

```
PUT /api/consultations/{id} - Update with new fields
GET /api/consultations/{id} - Return new fields
POST /api/consultations/{id}/mse - Update MSE with new fields
```

### 9.3 New Billing Endpoints

```
GET    /api/billings
POST   /api/billings
GET    /api/billings/{id}
PUT    /api/billings/{id}
DELETE /api/billings/{id}
GET    /api/billings/patient/{patient_id}
```

### 9.4 New Prescription Endpoints

```
GET    /api/medications - Master medication list
POST   /api/medications - Add medication (admin)
GET    /api/prescriptions
POST   /api/prescriptions
GET    /api/prescriptions/{id}
PUT    /api/prescriptions/{id}
DELETE /api/prescriptions/{id}
GET    /api/prescriptions/patient/{patient_id}
```

### 9.5 New Home Visit Endpoints

```
GET    /api/home-visits
POST   /api/home-visits
GET    /api/home-visits/{id}
PUT    /api/home-visits/{id}
DELETE /api/home-visits/{id}
GET    /api/home-visits/patient/{patient_id}
```

### 9.6 New Report Endpoints

```
GET /api/reports/client-list
GET /api/reports/client-query
GET /api/reports/consulting-history/{patient_id}
GET /api/reports/next-visits
GET /api/reports/consulting-data-query
GET /api/reports/trends
GET /api/reports/total-cases
```

---

## 10. Frontend Components to Create/Update

### 10.1 Patient Components

- ✅ Update `PatientForm.vue` (or create/edit pages)
- ✅ Add NHIS status dropdown
- ✅ Add relative information section
- ✅ Replace address with landmark field

### 10.2 Consultation Components

- ✅ Update `ConsultationForm.vue`
- ✅ Add personal history field
- ✅ Add occupational/marital history field
- ✅ Add substance abuse history field
- ✅ Update `MSEForm.vue` with all new fields
- ✅ Add orientation details section
- ✅ Update `ReviewForm.vue` with recommendation field

### 10.3 New Components

- ✅ `BillingForm.vue`
- ✅ `BillingList.vue`
- ✅ `MedicationPicker.vue` (searchable dropdown)
- ✅ `PrescriptionForm.vue`
- ✅ `PrescriptionList.vue`
- ✅ `HomeVisitForm.vue`
- ✅ `HomeVisitList.vue`
- ✅ `ReportBuilder.vue` (enhanced)

---

## 11. Testing Strategy

### 11.1 Unit Tests

- Model validations
- API endpoint tests
- Form validation tests

### 11.2 Integration Tests

- Patient CRUD with new fields
- Consultation CRUD with new fields
- Billing workflow
- Prescription workflow
- Home visit workflow

### 11.3 E2E Tests

- Complete patient registration flow
- Complete consultation creation flow
- Billing creation and payment
- Prescription creation
- Home visit creation
- Report generation

---

## 12. Risk Assessment

### 12.1 High Risk

- **Data Migration**: Ensure no data loss during patient/consultation field updates
- **API Breaking Changes**: Maintain backward compatibility
- **Performance**: New reports may be slow with large datasets

### 12.2 Medium Risk

- **Complex Forms**: MSE form will be very long, need good UX
- **Medication List**: Need to seed initial medication list
- **Billing Integration**: May need payment gateway integration later

### 12.3 Mitigation Strategies

- Incremental migrations
- Feature flags for new modules
- Comprehensive testing
- Performance monitoring
- User training documentation

---

## 13. Success Criteria

### 13.1 Functional Requirements

- ✅ All new patient fields captured
- ✅ All new consultation fields captured
- ✅ Billing module functional
- ✅ Prescription module functional
- ✅ Home visits module functional
- ✅ All reports generated correctly

### 13.2 Non-Functional Requirements

- ✅ No data loss during migration
- ✅ API response times < 500ms
- ✅ Form submission < 2 seconds
- ✅ Reports generate in < 10 seconds
- ✅ 100% test coverage for new features

---

## 14. Next Steps

1. **Review & Approve Plan**: Stakeholder review of this implementation plan
2. **Create Detailed Tasks**: Break down each phase into specific tasks
3. **Set Up Development Environment**: Ensure all tools and dependencies are ready
4. **Begin Phase 1**: Start with patient management updates
5. **Regular Updates**: Weekly progress updates and adjustments

---

## Appendix A: Field Mapping Reference

### Patient Fields

| Requirement | Current Field | New Field | Action |
|-------------|--------------|-----------|--------|
| id | `id` | `id` | Keep |
| name | `first_name`, `last_name` | `first_name`, `last_name` | Keep |
| age | Calculated from `date_of_birth` | Calculated | Keep |
| sex | `gender` | `gender` | Keep |
| marital status | `marital_status` | `marital_status` | Keep |
| occupation | `occupation` | `occupation` | Keep |
| education | `education_level` | `education_level` | Keep |
| religion | - | `religion` | **ADD** |
| relative name | - | `relative_name` | **ADD** |
| number of relative | - | `relative_phone` | **ADD** |
| relationship | - | `relative_relationship` | **ADD** |
| number | `phone_number` | `phone_number` | Keep |
| town | - | `town` | **ADD** |
| address | `address_line1`, `address_line2` | `landmark` | **CHANGE** |
| NHIS | - | `nhis_status` | **ADD** |
| assessment time | - | `assessment_time` | **ADD** |

### Consultation Fields

| Requirement | Current Field | New Field | Action |
|-------------|--------------|-----------|--------|
| Presenting complaint | `chief_complaint` | `chief_complaint` | Keep |
| History of presenting complaint | `history_present_illness` | `history_present_illness` | Keep |
| Past psychiatric illness | `past_psychiatric_history` | `past_psychiatric_history` | Keep |
| Past medical history | `medical_history` | `medical_history` | Keep |
| Personal history | - | `personal_history` | **ADD** |
| Occupational/marital history | - | `occupational_marital_history` | **ADD** |
| Family history | `family_history` | `family_history` | Keep |
| Substance abuse history | - | `substance_abuse_history` | **ADD** |

---

**End of Implementation Plan**

