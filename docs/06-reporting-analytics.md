# Reporting & Analytics

This document provides detailed specifications for all reports, dashboards, filters, and export capabilities in the Mental Health Consultation Web App.

---

## Report Categories

The system provides four main categories of reports:

1. **Patient Reports**: Demographics, registration, and patient lists
2. **Consultation Reports**: Clinical activity, volume, and session tracking
3. **Diagnosis Reports**: Diagnostic patterns, distributions, and trends
4. **Clinical Quality Reports**: Compliance metrics, completeness, and outcomes

---

## 1. Patient Reports

### 1.1 Patient List Report

**Purpose**: Comprehensive list of all patients with demographic filters

**Output Formats**: CSV, PDF

**Columns**:
| Column | Description | Data Type |
|--------|-------------|-----------|
| Patient ID | System-generated identifier | UUID |
| Full Name | Last, First Middle | Text |
| Date of Birth | DOB | Date |
| Age | Calculated age | Integer |
| Gender | Gender identity | Enum |
| Phone Number | Primary contact | Text |
| Email | Email address | Text |
| City, State | Location | Text |
| Last Consultation Date | Most recent visit | Date |
| Primary Clinician | Most recent treating clinician | Text |
| Status | Active/Inactive | Text |

**Filter Options**:
- **Name**: Partial match on first or last name
- **Age Range**: Min and max age
- **Gender**: Multi-select
- **City/State**: Dropdown or text search
- **Last Visit Date Range**: Start and end date
- **Status**: Active, Inactive, or All
- **Clinician**: Filter by primary clinician (admin only; auto-filtered for clinicians)

**Sample Use Case**: Generate list of all female patients aged 18-30 seen in the last 6 months for targeted outreach.

---

### 1.2 Patient Details Report

**Purpose**: Complete patient profile with full consultation history

**Output Format**: PDF only (comprehensive formatted report)

**Sections**:
1. **Patient Demographics**: Full demographic information and emergency contacts
2. **Consultation Summary**: Table of all consultations with dates, session types, primary diagnoses
3. **Diagnosis History**: Timeline of all diagnoses across consultations
4. **Vital Signs Trend**: Graph of vitals over time (if recorded)
5. **Treatment History**: Summary of treatment modalities and management plans
6. **Follow-Up Adherence**: Scheduled vs. completed follow-ups

**Filter Options**:
- **Patient Selection**: Single patient by ID or name search

**Sample Use Case**: Generate comprehensive patient history for case review or transfer to another provider.

---

### 1.3 New Patient Report

**Purpose**: Track patient registrations over specified time period

**Output Formats**: CSV, PDF

**Columns**:
| Column | Description |
|--------|-------------|
| Patient ID | System identifier |
| Full Name | Patient name |
| Date of Birth | DOB |
| Gender | Gender identity |
| Registration Date | When patient record created |
| Registered By | Clinician who created record |
| First Consultation Date | Date of initial assessment (if completed) |
| Days to First Consultation | Registration → first consult (if applicable) |

**Filter Options**:
- **Date Range**: Start and end date for registration
- **Clinician**: Who registered the patient (admin: all; clinician: own)
- **Has Consultation**: Filter to patients with/without initial consultation

**Sample Use Case**: Monthly report of new patient intake volume per clinician.

---

### 1.4 Inactive Patients Report

**Purpose**: Identify patients with no recent consultations for re-engagement

**Output Formats**: CSV, PDF

**Columns**:
| Column | Description |
|--------|-------------|
| Patient ID | System identifier |
| Full Name | Patient name |
| Last Consultation Date | Most recent visit |
| Days Inactive | Days since last consultation |
| Last Primary Clinician | Treating clinician at last visit |
| Last Diagnosis | Primary diagnosis from last visit |
| Scheduled Follow-Up | Whether follow-up was scheduled but not completed |

**Filter Options**:
- **Inactivity Threshold**: 30, 60, 90, 180, 365 days
- **Clinician**: Last treating clinician
- **Diagnosis**: Filter by last primary diagnosis
- **Missed Follow-Up**: Only patients with scheduled but uncompleted follow-ups

**Sample Use Case**: Identify patients inactive for 90+ days with high-risk diagnoses for outreach.

---

## 2. Consultation Reports

### 2.1 Consultation Log Report

**Purpose**: Detailed log of all consultations with basic information

**Output Formats**: CSV, PDF

**Columns**:
| Column | Description |
|--------|-------------|
| Consultation ID | System identifier |
| Patient Name | Patient full name |
| Consultation Date | Date of session |
| Consultation Time | Time of session |
| Primary Clinician | Primary treating clinician |
| Collaborators | List of collaborating clinicians (if any) |
| Session Type | Initial, follow-up, crisis, etc. |
| Session Duration | Minutes (if recorded) |
| Risk Assessment | Low, moderate, high |
| Primary Diagnosis | ICD-10 code + description |
| MSE Completed | Yes/No |

**Filter Options**:
- **Date Range**: Start and end date
- **Clinician**: Primary clinician or collaborator (admin: all; clinician: own)
- **Patient**: Specific patient
- **Session Type**: Multi-select
- **Risk Level**: Multi-select
- **MSE Completion**: Completed only, missing only, or all

**Sample Use Case**: Generate weekly log of all crisis intervention sessions for clinical director review.

---

### 2.2 Daily Activity Log Report

**Purpose**: Snapshot of one clinician's consultations on a specific date

**Output Format**: PDF (formatted for daily review)

**Sections**:
- **Header**: Clinician name, date, total consultations
- **Consultation Table**: All consultations on selected date with patient names, times, session types, primary diagnoses
- **Summary Statistics**: Total duration, session type breakdown, risk level distribution

**Filter Options**:
- **Date**: Single date selection
- **Clinician**: Specific clinician (auto-selected for non-admin users)

**Sample Use Case**: Clinician reviews own daily activity before signing off for accountability.

---

### 2.3 Consultation Volume Report

**Purpose**: Track consultation volume trends over time

**Output Formats**: CSV (data), PDF (with chart)

**Metrics**:
- **Total Consultations**: Count of consultations per period
- **Consultations per Clinician**: Volume by clinician
- **Average per Day**: Average daily consultations
- **Session Type Distribution**: Breakdown by session type

**Aggregation Options**:
- **Group By**: Day, Week, Month, Quarter, Year

**Chart Types** (PDF only):
- Line chart: Consultation volume over time
- Bar chart: Consultations by clinician
- Pie chart: Session type distribution

**Filter Options**:
- **Date Range**: Start and end date
- **Clinician**: Specific clinician or all (admin only)
- **Session Type**: Filter to specific types

**Sample Use Case**: Monthly trend report showing consultation volume growth or seasonal patterns.

---

### 2.4 Incomplete Consultations Report

**Purpose**: Identify consultations missing required documentation for quality assurance

**Output Formats**: CSV, PDF

**Columns**:
| Column | Description |
|--------|-------------|
| Consultation ID | System identifier |
| Patient Name | Patient full name |
| Consultation Date | Date of session |
| Primary Clinician | Treating clinician |
| Missing Elements | List of incomplete fields (MSE, diagnosis, management plan, etc.) |
| Days Since Consultation | Age of incomplete record |

**Filter Options**:
- **Date Range**: Consultation date range
- **Clinician**: Specific clinician (admin: all; clinician: own)
- **Missing Element Type**: MSE, Primary Diagnosis, Management Plan, Follow-Up Date

**Sample Use Case**: Weekly quality report showing consultations needing completion before 30-day lock.

---

## 3. Diagnosis Reports

### 3.1 Diagnosis Summary Report

**Purpose**: Aggregate view of all diagnoses with frequency counts

**Output Formats**: CSV, PDF

**Columns**:
| Column | Description |
|--------|-------------|
| ICD-10 Code | Diagnosis code |
| Diagnosis Description | Full diagnosis name |
| Diagnosis Count | Number of occurrences |
| Unique Patients | Number of unique patients with this diagnosis |
| % of Total Diagnoses | Percentage of all diagnoses |
| Avg Age of Patients | Average age of patients with this diagnosis |
| Gender Distribution | Male/Female/Other breakdown |

**Filter Options**:
- **Date Range**: Consultation date range
- **ICD-10 Code Range**: Filter to specific code ranges (e.g., F00-F99)
- **Clinician**: Specific clinician (admin: all; clinician: own)
- **Diagnosis Status**: Provisional, Confirmed, Rule-out
- **Minimum Count**: Only show diagnoses with ≥ N occurrences

**Sample Use Case**: Annual report of top 20 diagnoses for clinical program planning.

---

### 3.2 Diagnosis by Patient Report

**Purpose**: Patient-specific diagnosis history over time

**Output Format**: PDF (formatted timeline)

**Sections**:
- **Patient Info**: Demographics
- **Diagnosis Timeline**: Chronological list of all diagnoses with dates, statuses, treating clinicians
- **Diagnosis Changes**: Highlight changes in primary diagnosis over time
- **Current Diagnoses**: Active diagnoses with most recent status

**Filter Options**:
- **Patient**: Single patient selection by ID or name

**Sample Use Case**: Review diagnostic evolution for complex patient with multiple diagnoses.

---

### 3.3 Primary Diagnosis Distribution Report

**Purpose**: Visual breakdown of most common primary diagnoses

**Output Format**: PDF with chart (bar or pie chart)

**Chart Contents**:
- **X-Axis**: Diagnosis description
- **Y-Axis**: Count or percentage
- **Top N**: Configurable (default 10)

**Data Table** (below chart):
| Rank | ICD-10 Code | Diagnosis | Count | % of Total |
|------|-------------|-----------|-------|------------|
| 1 | F32.1 | Major Depressive Disorder, Moderate | 145 | 18.2% |
| 2 | F41.1 | Generalized Anxiety Disorder | 132 | 16.5% |
| ... | ... | ... | ... | ... |

**Filter Options**:
- **Date Range**: Consultation date range
- **Clinician**: Specific clinician or all
- **Top N**: Show top 10, 20, or 50 diagnoses

**Sample Use Case**: Quarterly presentation to leadership on clinical caseload composition.

---

### 3.4 Co-Morbidity Report

**Purpose**: Identify patients with multiple diagnoses for complex case management

**Output Formats**: CSV, PDF

**Columns**:
| Column | Description |
|--------|-------------|
| Patient Name | Patient full name |
| Patient ID | System identifier |
| Number of Diagnoses | Count of unique diagnoses |
| Primary Diagnosis | Most recent primary diagnosis |
| All Diagnoses | List of all ICD-10 codes |
| Last Consultation Date | Most recent visit |
| Primary Clinician | Treating clinician |

**Filter Options**:
- **Minimum Diagnose Count**: 2, 3, 4, or more
- **Date Range**: Consultation date range
- **Specific Diagnosis**: Must include specific ICD-10 code
- **Clinician**: Treating clinician

**Sample Use Case**: Identify patients with 3+ diagnoses for integrated care coordination program.

---

## 4. Clinical Quality Reports

### 4.1 MSE Completion Rate Report

**Purpose**: Track compliance with MSE documentation requirements

**Output Format**: PDF with metrics and chart

**Metrics**:
- **Total Consultations**: Count within date range
- **MSE Completed**: Count with completed MSE
- **Completion Rate**: Percentage
- **Incomplete MSE List**: Consultations missing MSE

**Chart**: Line chart of completion rate over time (by month)

**Filter Options**:
- **Date Range**: Consultation date range
- **Clinician**: Specific clinician or all (admin only)
- **Session Type**: Filter to specific session types

**Sample Use Case**: Monthly quality metric for clinical directors to track documentation compliance.

---

### 4.2 Follow-Up Adherence Report

**Purpose**: Measure whether patients attend scheduled follow-up appointments

**Output Format**: PDF with metrics and patient list

**Metrics**:
- **Scheduled Follow-Ups**: Count of consultations with scheduled next visit
- **Completed Follow-Ups**: Count where patient returned within 30 days of scheduled date
- **Adherence Rate**: Percentage
- **Missed Follow-Ups List**: Patients who were scheduled but did not return

**Filter Options**:
- **Date Range**: Original consultation date range
- **Clinician**: Specific clinician
- **Risk Level**: Filter by risk assessment (high-risk follow-ups prioritized)
- **Days Overdue**: Show only follow-ups overdue by ≥ N days

**Sample Use Case**: Identify high-risk patients who missed scheduled follow-ups for outreach intervention.

---

### 4.3 Risk Assessment Summary Report

**Purpose**: Distribution of risk levels across consultations

**Output Format**: PDF with chart

**Chart**: Pie chart showing:
- Low Risk: X consultations (Y%)
- Moderate Risk: X consultations (Y%)
- High Risk: X consultations (Y%)

**Data Tables**:
1. **Risk Level Counts**: Total consultations by risk level
2. **High-Risk Patient List**: Patients with most recent consultation rated high-risk

**Filter Options**:
- **Date Range**: Consultation date range
- **Clinician**: Specific clinician or all

**Sample Use Case**: Monthly quality report on risk distribution for clinical supervision.

---

### 4.4 Documentation Quality Report

**Purpose**: Comprehensive quality metrics for consultation completeness

**Output Format**: PDF with multiple sections

**Sections**:
1. **MSE Completion**: Percentage of consultations with complete MSE
2. **Diagnosis Documentation**: Percentage with primary diagnosis
3. **Management Plan**: Percentage with treatment goals and recommendations
4. **Risk Assessment**: Percentage with documented risk level
5. **Follow-Up Planning**: Percentage with next visit date (for high-risk patients)

**Scorecards** (by Clinician):
| Clinician | MSE % | Diagnosis % | Mgmt Plan % | Risk % | Follow-Up % | Overall Score |
|-----------|-------|-------------|-------------|--------|-------------|---------------|
| Dr. Smith | 98% | 100% | 95% | 100% | 90% | 96.6% |
| Dr. Jones | 92% | 98% | 88% | 100% | 85% | 92.6% |

**Overall Score**: Average of all five metrics

**Filter Options**:
- **Date Range**: Consultation date range
- **Clinician**: Specific clinician or all (admin only)

**Sample Use Case**: Quarterly quality assurance report for accreditation preparation.

---

## Dashboard Metrics

### Clinician Dashboard

**Purpose**: Personal performance metrics for individual clinicians

**Layout**: 4 widgets + 3 charts

#### Widgets

| Widget | Description | Update Frequency |
|--------|-------------|------------------|
| **Active Patients** | Count of patients with consultation in last 12 months | Daily |
| **Consultations This Month** | Count for current calendar month with trend vs. last month | Daily |
| **Avg Daily Consultations** | Rolling 30-day average | Daily |
| **High-Risk Patients** | Count of patients with most recent high-risk assessment | Real-time |

#### Charts

| Chart | Type | Description |
|-------|------|-------------|
| **Daily Consultation Volume** | Line chart | 30-day trend of daily consultations |
| **Consultation by Type** | Pie chart | Distribution of session types (last 90 days) |
| **Top Diagnoses** | Horizontal bar chart | Top 10 primary diagnoses (last 12 months) |

**Data Scope**: Own patients and consultations only

---

### Admin Dashboard

**Purpose**: System-wide oversight for administrators

**Layout**: 6 widgets + 4 charts + 1 table

#### Widgets

| Widget | Description | Update Frequency |
|--------|-------------|------------------|
| **Total Active Patients** | System-wide active patient count | Hourly |
| **Active Clinicians** | Count of active user accounts with role=clinician | Hourly |
| **New Patients (30 Days)** | Patients registered in last 30 days with trend | Daily |
| **Consultations This Month** | System-wide monthly count with trend | Daily |
| **Incomplete Records** | Consultations missing required documentation | Hourly |
| **System Alerts** | Critical issues (lockouts, failed logins, errors) | Real-time |

#### Charts

| Chart | Type | Description |
|-------|------|-------------|
| **Consultations by Clinician** | Bar chart | Volume per clinician (last 30 days) |
| **Diagnosis Distribution** | Pie chart | Top 10 diagnoses system-wide (last 90 days) |
| **New Patients Trend** | Line chart | Monthly new patient registrations (last 12 months) |
| **Follow-Up Adherence** | Gauge chart | System-wide follow-up completion rate |

#### Table Widget

| Widget | Description |
|--------|-------------|
| **Recent Audit Activity** | Last 20 critical audit events (user creation, role changes, data exports) |

**Data Scope**: All patients, consultations, and users

---

## Export Capabilities

### CSV Export

**Characteristics**:
- **Maximum Rows**: 10,000 per export (performance limit)
- **Encoding**: UTF-8 with BOM (Excel compatibility)
- **Delimiters**: Comma-separated, quoted strings for text with commas
- **Headers**: Column names in first row
- **Date Format**: ISO 8601 (YYYY-MM-DD)
- **Time Format**: 24-hour (HH:MM:SS)

**Available For**:
- All tabular reports (patient lists, consultation logs, diagnosis summaries)
- Dashboard widget data
- Audit logs (admin only)

**Use Cases**:
- Import into Excel or Google Sheets for further analysis
- Integration with statistical software (SPSS, R, Python)
- Data warehousing and business intelligence tools

**Audit Logging**: All CSV exports logged with:
- User ID
- Report type
- Filter criteria
- Row count
- Timestamp

---

### PDF Export

**Characteristics**:
- **Format**: PDF/A (archival standard)
- **Orientation**: Auto-select (portrait for narrow tables, landscape for wide)
- **Page Size**: Letter (8.5" x 11")
- **Fonts**: Embedded for portability
- **Headers**: Organization name/logo, report title, generation date
- **Footers**: Page numbers (Page X of Y)
- **Charts**: Embedded as high-resolution PNG images

**Formatting Features**:
- **Tables**: Bordered cells, alternating row shading for readability
- **Charts**: Full-color with legends
- **Sections**: Clear headings with hierarchical formatting
- **Line Wrapping**: Text wraps within table cells
- **Professional Styling**: Clinical documentation appearance

**Available For**:
- All reports (formatted for presentation and archival)
- Patient details report
- Consultation summaries
- Dashboard snapshots

**Use Cases**:
- Clinical documentation for medical records
- Printed reports for meetings or presentations
- Archival for compliance (PDF/A format)
- Patient transfer documentation

**Audit Logging**: All PDF exports logged (same as CSV)

---

## Filter & Search Features

### Common Filters (Available Across Reports)

| Filter | Type | Description |
|--------|------|-------------|
| **Date Range** | Date picker | Preset options (Today, This Week, This Month, This Year, Custom) |
| **Clinician** | Dropdown | Single or multi-select (auto-filtered for non-admin users) |
| **Patient** | Type-ahead search | Search by name or patient ID |
| **Session Type** | Multi-select | Initial, follow-up, crisis, therapy, medication review |
| **Risk Level** | Multi-select | Low, moderate, high |
| **Diagnosis** | Type-ahead search | Search ICD-10 code or description |

### Advanced Filters (Report-Specific)

| Report Type | Additional Filters |
|-------------|--------------------|
| **Patient Reports** | Age range, gender, city/state, marital status, active/inactive |
| **Consultation Reports** | Session duration, MSE completion, collaborators |
| **Diagnosis Reports** | Diagnosis status, severity, ICD-10 code range |
| **Quality Reports** | Completion thresholds, overdue days, quality score range |

### Filter Presets

**Save Filter Combinations**: Users can save commonly used filter configurations with custom names (e.g., "High-Risk Patients Last 30 Days").

**Quick Access**: Saved presets appear in dropdown for instant report generation without re-configuring filters.

---

## Scheduled Reports (Future Enhancement)

**Purpose**: Automate recurring report generation and delivery

**Features**:
- **Frequency**: Daily, weekly, monthly, quarterly
- **Delivery**: Email attachment (CSV/PDF)
- **Recipients**: Multiple email addresses
- **Filters**: Pre-configured filter combinations
- **Retention**: Automatically archive generated reports

**Use Cases**:
- Weekly consultation volume report emailed to clinical director
- Monthly quality metrics sent to accreditation team
- Quarterly diagnosis distribution for strategic planning

> [!NOTE]
> **MVP Limitation**: Scheduled reports are not included in the MVP. Users must manually generate reports on-demand. This feature is planned for a future release.

---

## Performance Considerations

### Report Generation Optimization

| Optimization | Description |
|--------------|-------------|
| **Database Indexing** | Optimize queries with indexes on date, clinician_id, patient_id, diagnosis code |
| **Query Pagination** | Large result sets fetched in batches to prevent timeout |
| **Caching**: Dashboard widgets cache results for 5 minutes to reduce database load |
| **Asynchronous Generation** | PDF generation happens in background for large reports; user notified when ready |
| **Export Limits** | 10,000-row CSV limit prevents excessive memory usage |

### Expected Performance

| Report Type | Typical Size | Expected Generation Time |
|-------------|--------------|--------------------------|
| **Patient List (100 patients)** | 1 page PDF, 100 rows CSV | < 2 seconds |
| **Consultation Log (500 consultations)** | 10 pages PDF, 500 rows CSV | < 5 seconds |
| **Dashboard (clinician)** | 7 widgets/charts | < 1 second (with caching) |
| **Dashboard (admin)** | 11 widgets/charts/table | < 3 seconds (with caching) |
| **Patient Details (1 patient, 20 consultations)** | 5 pages PDF | < 3 seconds |

---

## Reporting Security & Compliance

### Data Access Controls

- **Role-Based Filtering**: Clinicians see only own data; admins see all
- **Audit Trail**: All report generation and exports logged
- **PHI Protection**: Reports contain protected health information and must be handled per HIPAA

### Export Security

- **Download Encryption**: HTTPS/TLS for all file downloads
- **File Deletion**: Temporary files deleted from server after download
- **Access Logs**: Track who exported what data and when
- **De-Identification Option** (Future): Admins can generate de-identified reports for research

### Compliance Readiness

| Regulation | Reporting Support |
|------------|-------------------|
| **HIPAA** | Audit trail of all PHI access via reports; minimum necessary principle enforced by role-based filtering |
| **GDPR** | Data subject access reports (patient details); export capability for data portability |
| **Quality Reporting** | MSE completion, follow-up adherence, documentation quality metrics support Joint Commission standards |

---

**Next Steps**: Review [Security & Compliance](07-security-compliance.md) for detailed security specifications.
