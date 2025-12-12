# MVP Boundaries

This document defines the scope of the Minimum Viable Product (MVP) and identifies features excluded from the initial release.

---

## MVP Philosophy

The Mental Health Consultation Web App MVP focuses on **core clinical documentation and consultation management** to deliver immediate value to clinicians while establishing a solid foundation for future enhancements.

### MVP Success Criteria

The MVP will be considered successful when it:

1. **Enables Complete Consultations**: Clinicians can document full consultation sessions from patient registration through diagnosis and management planning
2. **Ensures Data Security**: HIPAA-ready security controls protect all patient health information
3. **Supports Clinical Workflows**: Structured MSE and diagnosis documentation improve consistency and completeness
4. **Facilitates Care Continuity**: Consultation reviews track patient progress over time
5. **Provides Essential Reporting**: Clinicians and administrators can generate required clinical and quality reports

---

## In Scope for MVP

### ✅ Included Features

#### 1. **Client/Patient Management**
- ✅ Create, read, update patient demographics
- ✅ Emergency contact and next-of-kin management
- ✅ Patient search functionality
- ✅ Soft delete (archive) for inactive patients

#### 2. **Consultation Management**
- ✅ Create and document consultation sessions
- ✅ Chief complaint and history documentation
- ✅ Single-clinician and multi-clinician consultations
- ✅ Collaborator management (add/remove clinicians)
- ✅ Auto-save to prevent data loss
- ✅ 30-day edit window with automatic locking

#### 3. **Mental State Examination (MSE)**
- ✅ Structured MSE with dropdowns for standardization
- ✅ All 7 MSE categories (appearance, speech, mood, thought, perception, cognition, insight/judgment)
- ✅ Risk assessment (suicidal/homicidal ideation)
- ✅ Free-text clinical observations

#### 4. **Diagnosis & Management**
- ✅ Primary diagnosis with ICD-10 code lookup
- ✅ Differential diagnoses (up to 10)
- ✅ Treatment goal documentation
- ✅ Clinical recommendations
- ✅ Follow-up scheduling with next visit date

#### 5. **Consultation Review**
- ✅ Progress notes linked to original consultations
- ✅ Vital signs recording (BP, HR, weight, BMI, etc.)
- ✅ Treatment response assessment
- ✅ Medication adherence and therapy engagement tracking
- ✅ Plan modifications

#### 6. **Reports & Analytics**
- ✅ Patient reports (list, details, new patients, inactive patients)
- ✅ Consultation reports (log, volume, activity, incomplete)
- ✅ Diagnosis reports (summary, distribution, co-morbidity)
- ✅ Quality reports (MSE completion, follow-up adherence, documentation quality, risk assessment)
- ✅ Clinician and admin dashboards
- ✅ CSV and PDF export

#### 7. **System-Level Features**
- ✅ User authentication (username/password)
- ✅ Role-based access control (Admin, Clinician)
- ✅ Comprehensive audit logging
- ✅ Encryption at rest (AES-256)
- ✅ Encryption in transit (TLS 1.2+)
- ✅ Password policies and account lockout
- ✅ Session management and timeout

---

## Out of Scope for MVP

### ❌ Excluded Features (Future Enhancements)

> [!NOTE]
> **Future Development**: The following features are valuable but excluded from the MVP to maintain focus on core functionality. They are planned for future releases based on user feedback and priorities.

---

### 1. **Billing & Insurance Management**

**Excluded**:
- ❌ Insurance eligibility verification
- ❌ Claims generation and submission
- ❌ Payment processing and invoicing
- ❌ CPT code assignment and billing documentation
- ❌ Revenue cycle management
- ❌ Insurance authorization tracking

**Rationale**: Billing is complex and highly variable by organization. The MVP focuses on clinical documentation, which is the foundation for billing but does not include billing workflows.

**Workaround**: Organizations can use existing billing systems and manually enter data from consultation reports.

**Future Enhancement**: Integrate with third-party billing systems or build native billing module based on user requirements.

---

### 2. **Medication Management & E-Prescribing**

**Excluded**:
- ❌ Medication prescription creation
- ❌ Electronic prescribing to pharmacies
- ❌ Medication interaction checking
- ❌ Medication history from external sources
- ❌ Medication reconciliation
- ❌ Prescription refill management

**Rationale**: E-prescribing requires integration with external pharmacy networks (e.g., Surescripts) and drug interaction databases, adding significant complexity.

**Workaround**: Clinicians document current medications in free-text field in consultation notes. Prescriptions written manually or via external e-prescribing system.

**Future Enhancement**: Integrate e-prescribing API for controlled and non-controlled substance prescribing.

---

### 3. **Patient Portal (Patient Self-Service)**

**Excluded**:
- ❌ Patient login and authentication
- ❌ Patient access to consultation notes
- ❌ Appointment scheduling by patients
- ❌ Secure messaging with clinicians
- ❌ Patient education materials
- ❌ Intake form completion by patients

**Rationale**: MVP is clinician-only to simplify security model and focus on provider workflows. Patient portal requires additional infrastructure for patient authentication and PHI access controls.

**Workaround**: All patient interactions handled via phone, email, or in-person. Intake forms completed on paper or via third-party form tools.

**Future Enhancement**: Build patient portal with self-scheduling, secure messaging, and access to visit summaries.

---

### 4. **Telemedicine / Video Consultations**

**Excluded**:
- ❌ Video conferencing integration (Zoom, WebRTC)
- ❌ Virtual waiting room
- ❌ Screen sharing for patient education
- ❌ Remote session recording (if legally permitted)
- ❌ Telehealth-specific documentation templates

**Rationale**: Video conferencing adds technical complexity and third-party dependencies. MVP focuses on in-person consultation documentation.

**Workaround**: Clinicians use external video conferencing tools (Zoom, Google Meet) and document as standard consultation in the app.

**Future Enhancement**: Integrate HIPAA-compliant video conferencing for telehealth consultations.

---

### 5. **External EHR Integration**

**Excluded**:
- ❌ HL7/FHIR interfaces with external EHR systems
- ❌ Automated data import from hospital EHRs
- ❌ Real-time data synchronization
- ❌ CCD (Continuity of Care Document) import/export
- ❌ Lab result integration
- ❌ Radiology image access

**Rationale**: EHR integration is organization-specific and requires extensive customization. MVP operates as standalone system.

**Workaround**: Clinicians manually enter relevant information from external EHRs into consultation notes.

**Future Enhancement**: Build HL7/FHIR integration layer for bidirectional data exchange with major EHR systems (Epic, Cerner, Allscripts).

---

### 6. **Advanced Analytics & Machine Learning**

**Excluded**:
- ❌ Predictive analytics (e.g., readmission risk, suicide risk prediction)
- ❌ Natural language processing (NLP) on clinical notes
- ❌ Treatment outcome predictions
- ❌ Automated diagnosis suggestions
- ❌ Population health analytics
- ❌ Cohort analysis and risk stratification

**Rationale**: Machine learning requires large datasets and sophisticated algorithms. MVP provides foundational data collection for future analytics.

**Workaround**: Basic reporting and dashboards provide manual insights. Advanced analytics performed in external tools (Excel, R, Python) via CSV export.

**Future Enhancement**: Build ML models for risk prediction, outcome forecasting, and clinical decision support.

---

### 7. **Appointment Scheduling & Calendar Management**

**Excluded**:
- ❌ Calendar view of appointments
- ❌ Appointment booking and cancellation
- ❌ Automated appointment reminders (email/SMS)
- ❌ Scheduling rules (availability, appointment types, duration)
- ❌ Waitlist management
- ❌ Calendar sync with external calendars (Google, Outlook)

**Rationale**: Scheduling is often handled by practice management systems. MVP focuses on clinical documentation after appointments occur.

**Workaround**: Organizations use existing scheduling systems (Google Calendar, EMR scheduling module, practice management software).

**Future Enhancement**: Build native scheduling module or integrate with popular calendar systems.

---

### 8. **Mobile Applications**

**Excluded**:
- ❌ Native iOS app
- ❌ Native Android app
- ❌ Offline mode for consultations
- ❌ Mobile-optimized UI beyond responsive design

**Rationale**: MVP is web-based application accessible via mobile browsers. Native apps require additional development resources.

**Workaround**: Clinicians access app via mobile web browser (responsive design provides usable mobile experience).

**Future Enhancement**: Develop native mobile apps with offline consultation support for low-connectivity environments.

---

### 9. **Multi-Location / Multi-Organization Support**

**Excluded**:
- ❌ Multiple clinic/location management
- ❌ Organization-level branding customization
- ❌ Multi-tenant architecture (separate databases per organization)
- ❌ Location-based user assignment
- ❌ Cross-location reporting

**Rationale**: MVP designed for single-organization deployment. Multi-tenancy adds architectural complexity.

**Workaround**: Each organization deploys separate instance of application.

**Future Enhancement**: Build multi-tenant architecture for SaaS deployment.

---

### 10. **Advanced Reporting Features**

**Excluded from MVP**:
- ❌ Custom report builder (drag-and-drop)
- ❌ Scheduled/automated report delivery
- ❌ Report templates and customization
- ❌ Dashboard widget customization (drag-and-drop)
- ❌ Real-time dashboard updates (manual refresh required)
- ❌ Report sharing and collaboration

**Rationale**: MVP provides fixed set of essential reports. Custom reporting requires complex query builder.

**Workaround**: Users export data to CSV and create custom reports in Excel or BI tools.

**Future Enhancement**: Build report builder allowing users to create custom reports without coding.

---

### 11. **Group Therapy & Family Therapy Documentation**

**Excluded**:
- ❌ Group session documentation (multiple patients in single session)
- ❌ Family therapy templates (multiple family members)
- ❌ Shared treatment plans across family members
- ❌ Group attendance tracking

**Rationale**: MVP focuses on individual patient consultations. Group/family therapy has different documentation workflows.

**Workaround**: Document group/family sessions as individual consultations for each participant.

**Future Enhancement**: Add group therapy module with attendance tracking and shared treatment plans.

---

### 12. **Multi-Factor Authentication (MFA)**

**Excluded from MVP**:
- ❌ TOTP (Time-based One-Time Password) via Google Authenticator, Authy
- ❌ SMS-based two-factor authentication
- ❌ Biometric authentication (fingerprint, face ID)
- ❌ Hardware token support (YubiKey)

**Rationale**: MFA adds complexity to authentication flow. MVP relies on strong password policies and session controls.

**Workaround**: Enforce strong password requirements (12+ characters, complexity) and short session timeout (30 minutes).

**Future Enhancement**: Add TOTP-based MFA as optional or mandatory security enhancement.

> [!WARNING]
> **Security Recommendation**: Organizations handling highly sensitive data should plan to implement MFA immediately post-MVP for enhanced security.

---

### 13. **Localization & Internationalization**

**Excluded**:
- ❌ Multi-language support (non-English)
- ❌ Regional date/time formats
- ❌ International diagnosis code systems (ICD-11, DSM-5-TR codes)
- ❌ Currency localization (for future billing)
- ❌ Right-to-left (RTL) language support

**Rationale**: MVP targets English-speaking US market. Localization requires translation and cultural adaptation.

**Workaround**: Application available in English only. International users must use English interface.

**Future Enhancement**: Add multi-language support and regional customization for global expansion.

---

## MVP Technical Limitations

### Performance Limitations

| Limitation | MVP Specification | Future Target |
|------------|-------------------|---------------|
| **Max Concurrent Users** | 50-100 | 500+ |
| **Database Size** | Up to 100,000 patients | Millions of patients |
| **Report Export Limit** | 10,000 rows | Unlimited (streaming export) |
| **Dashboard Refresh** | Manual refresh | Real-time updates |
| **Session Duration** | 30 minutes inactivity timeout | Configurable |

### Browser Support

**Supported Browsers** (MVP):
- ✅ Chrome 100+
- ✅ Firefox 100+
- ✅ Safari 15+
- ✅ Edge 100+

**Not Supported**:
- ❌ Internet Explorer (all versions)
- ❌ Outdated browser versions (>2 years old)

---

## Data Migration & Import

### Not Included in MVP

- ❌ Bulk patient import from CSV/Excel
- ❌ Data migration tools from other EHR/EMR systems
- ❌ Automated data cleanup and validation
- ❌ Import wizards with field mapping

**Workaround**: Manual patient registration or custom import scripts (requires developer support)

**Future Enhancement**: Build import wizard for bulk data migration.

---

## Deployment & Infrastructure

### MVP Deployment Model

**Supported**:
- ✅ Single-server deployment (application + database on same server)
- ✅ Cloud hosting (AWS, Azure, GCP)
- ✅ On-premise deployment (customer-managed infrastructure)

**Not Included**:
- ❌ Managed SaaS offering (vendor-hosted multi-tenant)
- ❌ High-availability clustering
- ❌ Auto-scaling infrastructure
- ❌ Containerization (Docker/Kubernetes) - optional but not required

**Rationale**: MVP designed for organizations to self-host or deploy with IT support. Managed SaaS requires operational infrastructure beyond MVP scope.

---

## Training & Support

### Included

- ✅ Comprehensive documentation (this documentation set)
- ✅ User manual (basic usage instructions)
- ✅ Admin guide (system configuration)

### Not Included

- ❌ Interactive tutorials or in-app guidance
- ❌ Video training materials
- ❌ Live training sessions
- ❌ 24/7 technical support
- ❌ Dedicated customer success manager

**Workaround**: Organizations provide internal training using documentation. Community support forum (if applicable).

**Future Enhancement**: Build in-app tutorial system and video training library.

---

## Compliance Certifications

### MVP Status

**Compliance-Ready** (technical controls in place):
- ✅ HIPAA Security Rule technical safeguards
- ✅ GDPR data protection requirements (encryption, access controls, audit logs)
- ✅ Encryption at rest and in transit

**Not Included**:
- ❌ HITRUST certification
- ❌ SOC 2 Type II audit
- ❌ ISO 27001 certification
- ❌ FedRAMP authorization (for US government use)

**Rationale**: Certifications are expensive and time-consuming. MVP provides technical foundation for compliance, but organizations must complete administrative and physical safeguards.

> [!IMPORTANT]
> **Compliance Responsibility**: Organizations deploying this application are responsible for:
> - HIPAA administrative safeguards (policies, training, business associate agreements)
> - Physical safeguards (facility security, workstation controls)
> - Completion of risk assessments and security audits
> - Implementation of organization-specific compliance policies

---

## Post-MVP Roadmap (Prioritized)

### Phase 2 (3-6 months post-MVP)
1. **Multi-Factor Authentication (MFA)**: Enhanced security
2. **Bulk Data Import**: CSV import wizard for patient migration
3. **Scheduled Reports**: Automated report generation and email delivery
4. **Advanced Search**: Full-text search across consultations
5. **Medication Management (Basic)**: Medication list management without e-prescribing

### Phase 3 (6-12 months post-MVP)
1. **E-Prescribing**: Integration with pharmacy networks
2. **Patient Portal**: Self-scheduling and secure messaging
3. **Mobile Apps**: Native iOS/Android applications
4. **Telemedicine**: Video consultation integration
5. **Custom Report Builder**: User-defined reports

### Phase 4 (12+ months post-MVP)
1. **EHR Integration**: HL7/FHIR interfaces
2. **Advanced Analytics**: Predictive modeling and ML
3. **Billing Module**: Claims generation and submission
4. **Multi-Tenant SaaS**: Vendor-hosted managed service
5. **Localization**: Multi-language support

---

## User Feedback & Iteration

The MVP is designed to gather user feedback for prioritizing future enhancements. Key feedback areas:

1. **Feature Gaps**: What critical features are missing from MVP?
2. **Workflow Pain Points**: Where does the app slow down clinical workflows?
3. **Usability Issues**: What UI/UX improvements would have biggest impact?
4. **Integration Needs**: What external systems need to connect with the app?
5. **Performance**: Are there speed or capacity issues in real-world use?

**Feedback Collection**:
- In-app feedback widget (future enhancement)
- Periodic user surveys
- Analytics on feature usage
- Direct user interviews

---

## Summary

The MVP delivers a **complete, HIPAA-ready clinical consultation platform** that enables clinicians to:

✅ Register and manage patients  
✅ Document comprehensive consultations with structured MSE  
✅ Record diagnoses and treatment plans  
✅ Track patient progress through reviews  
✅ Generate clinical and quality reports  
✅ Maintain security and audit compliance  

While advanced features like e-prescribing, patient portals, and integrations are excluded, the MVP provides immediate value and establishes a solid foundation for future growth.

> [!TIP]
> **Success Strategy**: Deploy the MVP, gather user feedback, and prioritize enhancements based on real-world usage patterns and user needs.

---

**Documentation Set Complete**: You have reviewed all 8 documentation files for the Mental Health Consultation Web App MVP.

- [README](../README.md) - Documentation index
- [System Overview](01-system-overview.md)
- [Feature Breakdown](02-feature-breakdown.md)
- [Data Model](03-data-model.md)
- [Workflow Diagrams](04-workflow-diagrams.md)
- [Roles & Permissions](05-roles-permissions.md)
- [Reporting & Analytics](06-reporting-analytics.md)
- [Security & Compliance](07-security-compliance.md)
- **[MVP Boundaries](08-mvp-boundaries.md)** ← You are here
