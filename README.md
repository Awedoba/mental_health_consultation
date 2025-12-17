# Mental Health Consultation Web App - MVP Documentation

> **Version**: 1.0.0 (MVP)  
> **Last Updated**: December 2025  
> **Target Users**: Healthcare Clinicians Only

## Overview

This documentation set provides comprehensive specifications for a Mental Health Consultation Web Application designed exclusively for healthcare clinicians. The system enables efficient patient management, structured consultation documentation, mental state examinations, diagnosis tracking, and clinical reporting.

## Documentation Structure

### Core Documentation

1. **[System Overview](docs/01-system-overview.md)**  
   Purpose, scope, target users, and key capabilities of the platform

2. **[Feature Breakdown](docs/02-feature-breakdown.md)**  
   Detailed specifications for all 7 core modules with CRUD operations and form fields

3. **[Data Model](docs/03-data-model.md)**  
   Entity-relationship diagrams and database schema specifications

4. **[Workflow Diagrams](docs/04-workflow-diagrams.md)**  
   Visual flowcharts for patient registration, consultation, and reporting processes

5. **[Roles & Permissions](docs/05-roles-permissions.md)**  
   User role definitions and permission matrices

6. **[Reporting & Analytics](docs/06-reporting-analytics.md)**  
   Report specifications, dashboard metrics, and export capabilities

7. **[Security & Compliance](docs/07-security-compliance.md)**  
   Authentication, encryption, audit logging, and HIPAA/GDPR readiness

8. **[MVP Boundaries](docs/08-mvp-boundaries.md)**  
   Clear scope definitions for MVP and future enhancements

9. **[Implementation Guide](docs/09-implementation-guide.md)**  
   Technical implementation details, architecture, API reference, and recent improvements

10. **[Implementation Plan - Enhanced Requirements](docs/10-implementation-plan.md)**  
   Comprehensive plan for implementing enhanced features including billing, prescriptions, home visits, and updated patient/consultation forms

11. **[Deployment Guide](docs/11-deployment-guide.md)**  
   Step-by-step instructions for deploying the application to production

12. **[Testing Checklist](docs/12-testing-checklist.md)**  
   Comprehensive testing checklist for all features and modules

## Quick Start

### For Project Stakeholders
Start with [System Overview](docs/01-system-overview.md) to understand the platform's purpose and scope.

### For Developers
1. Review [System Overview](docs/01-system-overview.md)
2. Read [Implementation Guide](docs/09-implementation-guide.md) for technical architecture
3. Study [Data Model](docs/03-data-model.md) for database design
4. Examine [Feature Breakdown](docs/02-feature-breakdown.md) for implementation details
5. Check [Security & Compliance](docs/07-security-compliance.md) for security requirements

### For Clinical Stakeholders
1. Read [System Overview](docs/01-system-overview.md)
2. Review [Workflow Diagrams](docs/04-workflow-diagrams.md) for clinical processes
3. Examine [Feature Breakdown](docs/02-feature-breakdown.md) for functional capabilities
4. Check [MVP Boundaries](docs/08-mvp-boundaries.md) for scope limitations

## Key Features

- **Patient Management**: Comprehensive demographic and contact information
- **Structured Consultations**: Single and multi-clinician consultation notes
- **Mental State Examination**: Standardized MSE with structured dropdowns
- **Diagnosis & Treatment Planning**: Diagnostic impressions and management plans
- **Progress Tracking**: Consultation reviews with vitals and follow-up notes
- **Clinical Reporting**: Tabular reports, dashboards, CSV/PDF exports
- **Security & Compliance**: HIPAA/GDPR-ready with encryption and audit logs

## MVP Scope

> [!IMPORTANT]
> This is an **MVP (Minimum Viable Product)** focused on core consultation and documentation features.

### Included in MVP
✅ Client/patient management  
✅ Consultation documentation  
✅ Mental state examination  
✅ Diagnosis and treatment planning  
✅ Progress reviews  
✅ Basic reporting and analytics  
✅ Role-based access control  

### Excluded from MVP
❌ Billing and insurance claims  
❌ Medication management and e-prescribing  
❌ Patient portal  
❌ Telemedicine/video consultations  
❌ External EHR integrations  

See [MVP Boundaries](docs/08-mvp-boundaries.md) for complete details.

## Target Users

**Primary Users**: Healthcare Clinicians
- Psychiatrists
- Psychologists
- Licensed Clinical Social Workers
- Mental Health Counselors
- Psychiatric Nurse Practitioners

**Administrative Users**: Clinical Administrators
- System configuration
- User management
- Audit log review

> [!NOTE]
> This application is **NOT** designed for patient self-service. All access is restricted to credentialed healthcare professionals.

## Technology Stack

### Frontend
- **Framework**: Nuxt 4.2.2 with Vue 3.5.25
- **Language**: TypeScript
- **Styling**: Tailwind CSS 4.x
- **Package Manager**: pnpm

### Backend
- **Framework**: Laravel 12
- **Language**: PHP 8.3.28
- **Authentication**: Laravel Sanctum 4.x
- **Database**: SQLite (development), supports PostgreSQL/MySQL
- **Testing**: Pest 4.x, PHPUnit 12.x

### Architecture
- **API**: RESTful JSON API
- **Authentication**: JWT tokens via Laravel Sanctum
- **State Management**: Vue 3 Composition API with composables
- **Error Handling**: Centralized error handling system
- **Form Validation**: Field-specific validation error display

See [Implementation Guide](docs/09-implementation-guide.md) for detailed technical documentation.

## Getting Help

For questions about specific modules or features, refer to the relevant documentation section linked above.

## License & Compliance

> [!CAUTION]
> This system handles Protected Health Information (PHI). All implementations must comply with:
> - HIPAA (Health Insurance Portability and Accountability Act)
> - GDPR (General Data Protection Regulation) where applicable
> - Local healthcare data privacy regulations

See [Security & Compliance](docs/07-security-compliance.md) for detailed requirements.
