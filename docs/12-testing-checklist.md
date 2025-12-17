# Testing Checklist

> **Version**: 1.0.0  
> **Last Updated**: December 2025

This document provides a comprehensive testing checklist for the Mental Health Consultation Web App.

## Pre-Testing Setup

- [ ] Development environment configured
- [ ] Test database created and seeded
- [ ] Test users created (admin, clinician)
- [ ] API base URL configured
- [ ] Browser developer tools ready

## Phase 1: Patient Management

### Patient Creation

- [ ] Create patient with all required fields
- [ ] Create patient with optional fields
- [ ] Validate required field errors
- [ ] Validate email format
- [ ] Validate phone number format
- [ ] Validate date of birth (not future date)
- [ ] Test NHIS status selection (insured/uninsured)
- [ ] Test relative information fields
- [ ] Test landmark field (replaces address_line1)
- [ ] Test town field
- [ ] Test assessment time field
- [ ] Create patient with emergency contacts
- [ ] Verify patient appears in list after creation

### Patient List

- [ ] View patient list
- [ ] Search patients by name
- [ ] Filter by active/inactive status
- [ ] Filter by age range
- [ ] Pagination works correctly
- [ ] Patient names display correctly
- [ ] NHIS status displays correctly

### Patient Edit

- [ ] Load existing patient data in edit form
- [ ] Update patient information
- [ ] Validate field updates
- [ ] Save changes successfully
- [ ] Verify updated data in list view

### Patient View

- [ ] View patient details
- [ ] All fields display correctly
- [ ] Emergency contacts display
- [ ] Related consultations visible
- [ ] Navigation works correctly

## Phase 2: Consultation Management

### Consultation Creation

- [ ] Create consultation with all required fields
- [ ] Create consultation with new history fields:
  - [ ] Personal history
  - [ ] Occupational/marital history
  - [ ] Substance abuse history
- [ ] Validate required fields (patient_id, consultation_date, clinical_summary)
- [ ] Test session type selection
- [ ] Test risk assessment selection
- [ ] Link consultation to patient
- [ ] Verify consultation appears in list

### Consultation List

- [ ] View consultation list
- [ ] Search by patient name
- [ ] Search by clinician name
- [ ] Filter by status (locked/unlocked)
- [ ] Filter by risk assessment
- [ ] Pagination works correctly
- [ ] Patient and clinician names display correctly
- [ ] Date formatting correct

### Consultation View

- [ ] View consultation details
- [ ] All fields display correctly
- [ ] Mental state exam visible (if exists)
- [ ] Diagnoses visible
- [ ] Management plan visible
- [ ] Reviews visible
- [ ] Navigation works correctly

### Mental State Examination

- [ ] Create MSE for consultation
- [ ] Test all new MSE fields:
  - [ ] Appearance/clothing options
  - [ ] Posture options
  - [ ] Grooming, behavior, movement
  - [ ] Attitude, speech, sense of self
  - [ ] Motivation
  - [ ] General information
  - [ ] Physical coordination
  - [ ] Mood, affect
  - [ ] Orientation details (person/place/time)
- [ ] Update existing MSE
- [ ] View MSE in consultation

### Consultation Reviews

- [ ] Create review for consultation
- [ ] Test new review fields:
  - [ ] Presenting complaints
  - [ ] OE (Objective Examination)
  - [ ] ODQ
  - [ ] Recommendation field
- [ ] Enter vitals (BP, weight, etc.)
- [ ] View review in consultation
- [ ] Multiple reviews display correctly

## Phase 3: Billing Module

### Billing Creation

- [ ] Create billing record
- [ ] Link to patient
- [ ] Link to consultation (optional)
- [ ] Select service type
- [ ] Enter amount
- [ ] Test NHIS coverage toggle
- [ ] Enter NHIS amount and patient amount
- [ ] Select payment status
- [ ] Enter payment method
- [ ] Add notes
- [ ] Verify invoice number generated

### Billing List

- [ ] View billing list
- [ ] Search billings
- [ ] Filter by payment status
- [ ] Filter by service type
- [ ] Filter by patient
- [ ] Pagination works correctly
- [ ] Amounts display correctly

### Billing by Patient

- [ ] View patient billings
- [ ] All billings for patient display
- [ ] Payment status visible
- [ ] Amounts correct

## Phase 4: Prescription Module

### Medication Master List

- [ ] View medication list
- [ ] Search medications
- [ ] Filter by category
- [ ] Filter by active status
- [ ] Create medication (admin only)
- [ ] Update medication (admin only)
- [ ] Deactivate medication (admin only)

### Prescription Creation

- [ ] Create prescription
- [ ] Select patient
- [ ] Link to consultation (optional)
- [ ] Use medication picker component
- [ ] Search medications in picker
- [ ] Enter dosage, frequency, duration
- [ ] Enter quantity
- [ ] Add instructions
- [ ] Set refills
- [ ] Set start/end dates
- [ ] Verify prescription appears in list

### Prescription List

- [ ] View prescription list
- [ ] Search prescriptions
- [ ] Filter by patient
- [ ] Filter by active status
- [ ] Pagination works correctly
- [ ] Medication details display

### Prescription by Patient

- [ ] View patient prescriptions
- [ ] All prescriptions display
- [ ] Active prescriptions highlighted
- [ ] Medication details visible

## Phase 5: Home Visits Module

### Home Visit Creation

- [ ] Create home visit
- [ ] Enter client name (required)
- [ ] Link to patient (optional)
- [ ] Enter age and sex
- [ ] Enter community/location
- [ ] Enter contact
- [ ] Select visit date
- [ ] Enter visit time (optional)
- [ ] Enter clinical information:
  - [ ] Diagnosis/condition
  - [ ] Medication/prescription
  - [ ] Observations
  - [ ] Impression
  - [ ] Management
  - [ ] Recommendation
- [ ] Verify visit appears in list

### Home Visit List

- [ ] View home visit list
- [ ] Search by client name, community, or contact
- [ ] Filter by date range
- [ ] Filter by clinician
- [ ] Pagination works correctly
- [ ] Client information displays correctly

### Home Visit by Patient

- [ ] View patient home visits
- [ ] All visits display
- [ ] Visit details visible
- [ ] Date sorting correct

## Phase 6: Reports

### Client List Report

- [ ] Generate client list
- [ ] Filter by name
- [ ] Filter by age range
- [ ] Filter by sex
- [ ] Filter by NHIS status
- [ ] Filter by date range
- [ ] Results display correctly
- [ ] NHIS status visible

### Client Query Report

- [ ] Generate client query
- [ ] Test advanced search
- [ ] Filter by multiple criteria
- [ ] Results accurate
- [ ] All filters work

### Consulting History Report

- [ ] Generate for specific patient
- [ ] Filter by date range
- [ ] All consultations display
- [ ] Consultation details visible
- [ ] Reviews visible
- [ ] Recommendations visible

### Next Visit Report

- [ ] Generate next visit report
- [ ] Filter by date range
- [ ] Filter by clinician
- [ ] Upcoming visits display
- [ ] Patient information visible
- [ ] Visit purpose visible

### Consulting Data Query

- [ ] Generate with filters
- [ ] Filter by date range
- [ ] Filter by session type
- [ ] Filter by patient
- [ ] Filter by clinician
- [ ] Filter by completion status
- [ ] Results accurate

### Trend Report

- [ ] Generate trend report
- [ ] Filter by date range
- [ ] Consultation trends visible
- [ ] Weight trends visible
- [ ] Summary statistics correct
- [ ] Grouping works correctly

### Total Cases Report

- [ ] Generate total cases report
- [ ] Filter by date range
- [ ] Summary statistics display
- [ ] Breakdowns visible:
  - [ ] Consultations by type
  - [ ] Patients by NHIS status
  - [ ] Billings by status
- [ ] Revenue calculations correct

## Authentication & Authorization

### Authentication

- [ ] Login with valid credentials
- [ ] Login with invalid credentials (error)
- [ ] Logout works
- [ ] Session persists
- [ ] Token refresh works

### Authorization

- [ ] Admin can access all features
- [ ] Clinician can access assigned features
- [ ] Clinician cannot access admin features
- [ ] Role-based menu items display correctly
- [ ] API endpoints enforce permissions

## User Interface

### Navigation

- [ ] Sidebar navigation works
- [ ] Mobile menu works
- [ ] All links functional
- [ ] Active route highlighted
- [ ] Breadcrumbs correct

### Forms

- [ ] All form fields functional
- [ ] Validation errors display
- [ ] Loading states work
- [ ] Success messages display
- [ ] Error messages display
- [ ] Form submission works

### Tables

- [ ] Data displays correctly
- [ ] Sorting works (where applicable)
- [ ] Pagination works
- [ ] Empty states display
- [ ] Loading states work

### Responsive Design

- [ ] Desktop layout works
- [ ] Tablet layout works
- [ ] Mobile layout works
- [ ] Forms usable on mobile
- [ ] Tables scrollable on mobile

## Performance

- [ ] Page load times acceptable (< 2s)
- [ ] API response times acceptable (< 500ms)
- [ ] Search debouncing works
- [ ] Large lists paginate correctly
- [ ] Images load efficiently

## Error Handling

- [ ] Network errors handled gracefully
- [ ] Validation errors display correctly
- [ ] 404 errors handled
- [ ] 500 errors handled
- [ ] Error messages user-friendly

## Data Integrity

- [ ] Patient data persists correctly
- [ ] Consultation data persists correctly
- [ ] Relationships maintained
- [ ] Foreign key constraints work
- [ ] Soft deletes work (where applicable)

## Security

- [ ] Passwords not visible in logs
- [ ] API tokens secure
- [ ] CSRF protection works
- [ ] XSS protection works
- [ ] SQL injection protection works
- [ ] File uploads secure (if applicable)

## Browser Compatibility

- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

## Accessibility

- [ ] Keyboard navigation works
- [ ] ARIA labels present
- [ ] Screen reader compatible
- [ ] Color contrast sufficient
- [ ] Focus indicators visible

---

## Test Results Template

```
Test Date: ___________
Tester: ___________
Environment: ___________

Module: ___________
Test Case: ___________
Status: [ ] Pass [ ] Fail [ ] Blocked
Notes: ___________
```

---

**Next Steps**: See [Deployment Guide](11-deployment-guide.md) for production deployment and [Implementation Guide](09-implementation-guide.md) for technical details.

