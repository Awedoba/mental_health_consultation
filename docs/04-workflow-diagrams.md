# Workflow Diagrams

This document provides visual workflows for key clinical and administrative processes in the Mental Health Consultation Web App.

---

## 1. Patient Registration Workflow

```mermaid
flowchart TD
    Start([Clinician Opens App]) --> Search{Search for<br/>Existing Patient?}
    Search -->|Yes| SearchPatient[Enter Search Criteria]
    SearchPatient --> Found{Patient<br/>Found?}
    Found -->|Yes| ViewPatient[View Patient Record]
    Found -->|No| CreateNew[Create New Patient]
    Search -->|No New Patient| CreateNew
    
    CreateNew --> EnterDemo[Enter Demographics:<br/>Name, DOB, Gender, Contact]
    EnterDemo --> EnterAddress[Enter Address Information]
    EnterAddress --> EnterEmergency[Add Emergency Contact:<br/>Name, Relationship, Phone]
    EnterEmergency --> Validate{All Required<br/>Fields Complete?}
    Validate -->|No| FixErrors[Display Validation Errors]
    FixErrors --> EnterDemo
    Validate -->|Yes| CheckDuplicate{Duplicate Check:<br/>Name + DOB exists?}
    CheckDuplicate -->|Yes| WarnDuplicate[Warn: Potential Duplicate]
    WarnDuplicate --> ConfirmCreate{Clinician<br/>Confirms Create?}
    ConfirmCreate -->|No| End1([Cancel])
    ConfirmCreate -->|Yes| SavePatient
    CheckDuplicate -->|No| SavePatient[Save Patient Record]
    SavePatient --> LogAudit[Log Creation in Audit Trail]
    LogAudit --> Success([Patient Created:<br/>Ready for Consultation])
    
    ViewPatient --> ConsultOpt{Start New<br/>Consultation?}
    ConsultOpt -->|Yes| GoToConsult([Proceed to<br/>Consultation Workflow])
    ConsultOpt -->|No| UpdateOpt{Update<br/>Patient Info?}
    UpdateOpt -->|Yes| EditPatient[Edit Patient Details]
    EditPatient --> SaveUpdates[Save Changes]
    SaveUpdates --> LogUpdate[Log Update in Audit Trail]
    LogUpdate --> End2([Done])
    UpdateOpt -->|No| End3([Done])
```

**Key Decision Points**:
- Duplicate detection prevents creating multiple records for same patient
- At least one emergency contact required before saving
- All patient creations and updates logged for audit compliance

---

## 2. Consultation Session Workflow

```mermaid
flowchart TD
    Start([Select Patient]) --> CreateConsult[Create New Consultation]
    CreateConsult --> EnterHeader[Enter Header Info:<br/>Date, Time, Session Type]
    EnterHeader --> ChiefComplaint[Document Chief Complaint]
    ChiefComplaint --> HPI[Document History of<br/>Present Illness]
    HPI --> Background[Enter Background Info:<br/>Psych History, Medical History,<br/>Family/Social History]
    Background --> CurrentMeds[List Current Medications<br/>and Allergies]
    CurrentMeds --> AssessRisk[Assess Risk Level:<br/>Low/Moderate/High]
    
    AssessRisk --> HighRisk{Risk = High?}
    HighRisk -->|Yes| SafetyPlan[Create Safety Plan]
    SafetyPlan --> CompleteMSE
    HighRisk -->|No| CompleteMSE[Complete Mental State Exam]
    
    CompleteMSE --> MSE_App[MSE: Appearance & Behavior]
    MSE_App --> MSE_Speech[MSE: Speech & Language]
    MSE_Speech --> MSE_Mood[MSE: Mood & Affect]
    MSE_Mood --> MSE_Thought[MSE: Thought Process/Content]
    MSE_Thought --> MSE_Perc[MSE: Perception]
    MSE_Perc --> MSE_Cog[MSE: Cognition & Sensorium]
    MSE_Cog --> MSE_Insight[MSE: Insight & Judgment]
    
    MSE_Insight --> SuicidalCheck{Suicidal or<br/>Homicidal Ideation?}
    SuicidalCheck -->|Yes| AdditionalSafety[Document Safety Measures]
    AdditionalSafety --> Diagnosis
    SuicidalCheck -->|No| Diagnosis[Document Diagnosis]
    
    Diagnosis --> PrimaryDx[Enter Primary Diagnosis:<br/>ICD-10 Code + Description]
    PrimaryDx --> DifferentialOpt{Add Differential<br/>Diagnoses?}
    DifferentialOpt -->|Yes| DifferentialDx[Enter Differential Dx<br/>with Rationale]
    DifferentialDx --> ManagementPlan
    DifferentialOpt -->|No| ManagementPlan[Create Management Plan]
    
    ManagementPlan --> TreatmentMod[Select Treatment Modalities:<br/>Therapy, Medication, etc.]
    TreatmentMod --> Goals[Document Treatment Goals]
    Goals --> Recommendations[Enter Clinical Recommendations]
    Recommendations --> FollowUp[Schedule Next Visit]
    
    FollowUp --> HighRiskFollow{High Risk<br/>Patient?}
    HighRiskFollow -->|Yes| UrgentFollow[Require Follow-up<br/>within 14 days]
    UrgentFollow --> Summary
    HighRiskFollow -->|No| Summary[Write Clinical Summary]
    
    Summary --> Collab{Add<br/>Collaborating<br/>Clinicians?}
    Collab -->|Yes| AddCollab[Select Collaborators]
    AddCollab --> NotifyCollab[Notify Collaborators]
    NotifyCollab --> SaveConsult
    Collab -->|No| SaveConsult[Save Consultation]
    
    SaveConsult --> LogConsult[Log in Audit Trail]
    LogConsult --> PrintOpt{Generate<br/>PDF Summary?}
    PrintOpt -->|Yes| GeneratePDF[Create PDF Report]
    GeneratePDF --> End1([Consultation Complete])
    PrintOpt -->|No| End2([Consultation Complete])
```

**Critical Requirements**:
- Chief complaint mandatory before saving
- MSE completion ensures comprehensive assessment
- High-risk patients require follow-up within 14 days
- Primary diagnosis required for consultation completion

---

## 3. Multi-Clinician Collaboration Workflow

```mermaid
flowchart TD
    Start([Primary Clinician<br/>Creates Consultation]) --> Complete[Complete Initial Assessment]
    Complete --> AddCollab[Add Collaborating Clinicians]
    AddCollab --> SelectClinicians[Select 1+ Clinicians<br/>from Active Users]
    SelectClinicians --> SendNotif[System Sends Notification<br/>to Collaborators]
    SendNotif --> Assigned([Collaborators Assigned])
    
    Assigned --> Collab2Login[Collaborator Logs In]
    Collab2Login --> ViewNotif[View Notification:<br/>New Shared Consultation]
    ViewNotif --> OpenConsult[Open Consultation]
    OpenConsult --> ViewAll[View Complete Assessment:<br/>MSE, Diagnosis, Plan]
    
    ViewAll --> EditOpt{Need to Add<br/>Notes or Update?}
    EditOpt -->|Yes| EditConsult[Edit Consultation]
    EditConsult --> AddNotes[Add Clinical Observations<br/>or Modify Assessment]
    AddNotes --> SaveEdits[Save Changes]
    SaveEdits --> LogEdit[Audit Log Records:<br/>Collaborator ID + Changes]
    LogEdit --> NotifyPrimary[Notify Primary Clinician<br/>of Updates]
    NotifyPrimary --> End1([Collaboration Complete])
    EditOpt -->|No| ReadOnly[Read-Only Review]
    ReadOnly --> End2([Collaboration Complete])
    
    NotifyPrimary --> PrimaryReview[Primary Clinician<br/>Reviews Updates]
    PrimaryReview --> Integrate{Integrate<br/>Changes?}
    Integrate -->|Yes| UpdatePlan[Update Management Plan<br/>Based on Collaboration]
    UpdatePlan --> Finalize([Finalized Consultation])
    Integrate -->|No| Discuss[Discuss with Collaborator]
    Discuss --> Resolve([Resolution])
```

**Collaboration Features**:
- All collaborators have equal edit rights for shared consultation
- Audit trail tracks specific clinician for each edit
- Notifications ensure timely communication
- Primary clinician retains ownership and final decision authority

---

## 4. Consultation Review (Follow-Up) Workflow

```mermaid
flowchart TD
    Start([Patient Returns<br/>for Follow-Up]) --> SelectPatient[Select Patient]
    SelectPatient --> ViewHistory[View Consultation History]
    ViewHistory --> SelectConsult[Select Original Consultation<br/>to Review]
    SelectConsult --> CreateReview[Create Consultation Review]
    
    CreateReview --> EnterDate[Enter Review Date]
    EnterDate --> SelectType[Select Visit Type:<br/>Scheduled/Unscheduled/Crisis]
    SelectType --> Vitals[Record Vital Signs:<br/>BP, HR, Weight, etc.]
    Vitals --> AutoCalc[System Auto-Calculates BMI]
    AutoCalc --> ChartVitals[Display Vital Trends Chart]
    
    ChartVitals --> Subjective[Document Subjective:<br/>Patient's Report of Progress]
    Subjective --> Objective[Document Objective:<br/>Clinician's Observations]
    Objective --> AssessResponse[Assess Treatment Response:<br/>Improvement/No Change/Worsening]
    
    AssessResponse --> Improved{Significant<br/>Improvement?}
    Improved -->|Yes| ContinuePlan[Continue Current Treatment]
    ContinuePlan --> SetGoals
    Improved -->|No| Worsened{Worsening<br/>or No Change?}
    Worsened -->|Yes| ModifyPlan[Modify Treatment Plan]
    ModifyPlan --> NewInterventions[Document New Interventions]
    NewInterventions --> SetGoals
    Worsened -->|No| MinorAdjust[Minor Plan Adjustments]
    MinorAdjust --> SetGoals[Update Progress Toward Goals]
    
    SetGoals --> Adherence{Assess<br/>Adherence}
    Adherence --> MedAdherence[Rate Medication Adherence]
    MedAdherence --> TherapyEng[Rate Therapy Engagement]
    TherapyEng --> SideEffects{Any Side Effects<br/>or New Symptoms?}
    SideEffects -->|Yes| DocumentSE[Document Side Effects/<br/>New Symptoms]
    DocumentSE --> ClinicalAssess
    SideEffects -->|No| ClinicalAssess[Write Updated<br/>Clinical Assessment]
    
    ClinicalAssess --> NextSteps[Document Next Steps<br/>Until Next Visit]
    NextSteps --> ScheduleNext{Schedule<br/>Next Visit?}
    ScheduleNext -->|Yes| SetNextDate[Set Next Visit Date]
    SetNextDate --> CreateNextConsult{Create Next<br/>Consultation Now?}
    CreateNextConsult -->|Yes| NewConsult([Start New Consultation<br/>Workflow])
    CreateNextConsult -->|No| SaveReview
    ScheduleNext -->|No| DischargeOpt{Discharge<br/>Patient?}
    DischargeOpt -->|Yes| DischargePlan[Document Discharge Plan]
    DischargePlan --> SaveReview
    DischargeOpt -->|No| SaveReview[Save Consultation Review]
    
    SaveReview --> LogReview[Log Review in Audit Trail]
    LogReview --> PrintOpt{Print Progress<br/>Summary?}
    PrintOpt -->|Yes| GeneratePDF[Create PDF Progress Report]
    GeneratePDF --> End1([Review Complete])
    PrintOpt -->|No| End2([Review Complete])
```

**Key Features**:
- Vital trends displayed graphically for clinical decision-making
- Treatment response assessment guides plan adjustments
- Automatic BMI calculation from height and weight
- Progress notes link back to original consultation for context

---

## 5. Reporting & Analytics Workflow

### Report Generation

```mermaid
flowchart TD
    Start([User Opens<br/>Reports Module]) --> SelectType{Select<br/>Report Type}
    
    SelectType -->|Patient Reports| PatientRpt[Select Patient Report:<br/>List/Details/New Patients]
    SelectType -->|Consultation Reports| ConsultRpt[Select Consultation Report:<br/>Log/Volume/Activity]
    SelectType -->|Diagnosis Reports| DiagRpt[Select Diagnosis Report:<br/>Summary/Distribution]
    SelectType -->|Quality Reports| QualityRpt[Select Quality Report:<br/>MSE Completion/Follow-up]
    
    PatientRpt --> SetFilters1
    ConsultRpt --> SetFilters1
    DiagRpt --> SetFilters1
    QualityRpt --> SetFilters1[Configure Filters]
    
    SetFilters1 --> DateRange[Select Date Range:<br/>Preset or Custom]
    DateRange --> ClinicianOpt{Filter by<br/>Clinician?}
    ClinicianOpt -->|Yes| SelectClinician[Select Clinician s]
    SelectClinician --> MoreFilters
    ClinicianOpt -->|No| MoreFilters{Additional<br/>Filters?}
    
    MoreFilters -->|Yes, Patient| PatientFilter[Select Patient]
    MoreFilters -->|Yes, Diagnosis| DiagFilter[Select ICD-10 Code/Range]
    MoreFilters -->|Yes, Risk| RiskFilter[Select Risk Level]
    PatientFilter --> Preview
    DiagFilter --> Preview
    RiskFilter --> Preview
    MoreFilters -->|No| Preview[Preview Report]
    
    Preview --> CheckAccess{User Has<br/>Access to Data?}
    CheckAccess -->|No Admin| OwnDataOnly[Filter to Own Patients Only]
    OwnDataOnly --> RunQuery
    CheckAccess -->|Admin| RunQuery[Run Database Query]
    
    RunQuery --> DataCheck{Results<br/>Found?}
    DataCheck -->|No| NoData[Display: No Data<br/>for Selected Criteria]
    NoData --> End1([Done])
    DataCheck -->|Yes| DisplayResults[Display Tabular Results]
    
    DisplayResults --> ExportOpt{Export<br/>Report?}
    ExportOpt -->|CSV| ExportCSV[Generate CSV File]
    ExportCSV --> CheckLimit{Results > 10,000<br/>Rows?}
    CheckLimit -->|Yes| LimitWarning[Warn: Limit Exceeded,<br/>Export First 10,000]
    LimitWarning --> DownloadCSV
    CheckLimit -->|No| DownloadCSV[Download CSV]
    DownloadCSV --> LogExport1[Log Export in Audit Trail]
    LogExport1 --> End2([Done])
    
    ExportOpt -->|PDF| FormatPDF[Format Report:<br/>Headers, Tables, Charts]
    FormatPDF --> GeneratePDF[Generate PDF Document]
    GeneratePDF --> DownloadPDF[Download PDF]
    DownloadPDF --> LogExport2[Log Export in Audit Trail]
    LogExport2 --> End3([Done])
    
    ExportOpt -->|No| End4([View Only])
```

### Dashboard Viewing

```mermaid
flowchart TD
    Start([User Opens<br/>Dashboard]) --> CheckRole{User Role?}
    
    CheckRole -->|Clinician| ClinicianDash[Load Clinician Dashboard:<br/>Personal Metrics Only]
    CheckRole -->|Admin| AdminDash[Load Admin Dashboard:<br/>System-Wide Metrics]
    
    ClinicianDash --> LoadWidget1[Widget: Active Patients Count]
    LoadWidget1 --> LoadWidget2[Widget: Consultations This Month]
    LoadWidget2 --> LoadWidget3[Widget: Follow-Up Rate]
    LoadWidget3 --> LoadWidget4[Chart: Daily Consultation Trend]
    LoadWidget4 --> LoadWidget5[Chart: Top Diagnoses]
    LoadWidget5 --> DisplayClinician[Display Clinician Dashboard]
    
    AdminDash --> LoadAdminW1[Widget: Total Active Patients]
    LoadAdminW1 --> LoadAdminW2[Widget: Active Clinicians]
    LoadAdminW2 --> LoadAdminW3[Chart: Consultations by Clinician]
    LoadAdminW3 --> LoadAdminW4[Chart: New Patients Trend]
    LoadAdminW4 --> LoadAdminW5[Widget: Incomplete Records Alert]
    LoadAdminW5 --> LoadAdminW6[Table: Recent Audit Activity]
    LoadAdminW6 --> DisplayAdmin[Display Admin Dashboard]
    
    DisplayClinician --> Refresh
    DisplayAdmin --> Refresh{Auto-Refresh<br/>Enabled?}
    Refresh -->|Yes| Schedule[Refresh Every 5 Minutes]
    Schedule --> Interact
    Refresh -->|No| Interact[User Interacts with Widgets]
    
    Interact --> ClickWidget{Click<br/>Widget?}
    ClickWidget -->|Yes| DrillDown[Drill Down to<br/>Detailed Report]
    DrillDown --> DetailView[View Detailed Data]
    DetailView --> BackOpt{Return to<br/>Dashboard?}
    BackOpt -->|Yes| Interact
    BackOpt -->|No| End1([Done])
    
    ClickWidget -->|No| ExportWidget{Export<br/>Widget Data?}
    ExportWidget -->|Yes| QuickExport[Quick Export Widget as CSV/PDF]
    QuickExport --> LogWidgetExport[Log Export in Audit Trail]
    LogWidgetExport --> End2([Done])
    ExportWidget -->|No| End3([Done])
```

**Reporting Features**:
- Role-based data filtering (clinicians see own data, admins see all)
- 10,000-row export limit for CSV to prevent performance issues
- All exports logged in audit trail for compliance
- Dashboards auto-refresh for real-time insights

---

## 6. Authentication & Security Workflow

```mermaid
flowchart TD
    Start([User Visits App]) --> LoginPage[Display Login Page]
    LoginPage --> EnterCreds[Enter Username/Email<br/>and Password]
    EnterCreds --> SubmitLogin[Submit Credentials]
    
    SubmitLogin --> CheckLockout{Account<br/>Locked?}
    CheckLockout -->|Yes| LockoutMsg[Display: Account Locked<br/>Try Again After 30 Min]
    LockoutMsg --> ContactAdmin{Contact<br/>Admin?}
    ContactAdmin -->|Yes| AdminUnlock[Admin Unlocks Account]
    AdminUnlock --> LoginPage
    ContactAdmin -->|No| End1([Access Denied])
    
    CheckLockout -->|No| ValidateCreds{Credentials<br/>Valid?}
    ValidateCreds -->|No| IncrementFail[Increment Failed<br/>Login Attempts]
    IncrementFail --> CheckAttempts{Attempts >= 5?}
    CheckAttempts -->|Yes| LockAccount[Lock Account for 30 Minutes]
    LockAccount --> LogFail[Log Failed Login<br/>in Audit Trail]
    LogFail --> LockoutMsg
    CheckAttempts -->|No| ErrorMsg[Display: Invalid Credentials]
    ErrorMsg --> LogFail2[Log Failed Attempt]
    LogFail2 --> LoginPage
    
    ValidateCreds -->|Yes| CheckActive{Account<br/>Active?}
    CheckActive -->|No| InactiveMsg[Display: Account Deactivated<br/>Contact Administrator]
    InactiveMsg --> End2([Access Denied])
    CheckActive -->|Yes| ResetFailCount[Reset Failed Login Count]
    ResetFailCount --> CreateSession[Create Secure Session]
    CreateSession --> GenerateToken[Generate Session Token]
    GenerateToken --> LogSuccess[Log Successful Login<br/>with IP Address]
    LogSuccess --> CheckPwdAge{Password > 90<br/>Days Old?}
    CheckPwdAge -->|Yes| PwdExpireWarn[Warn: Password Expires Soon,<br/>Please Change]
    PwdExpireWarn --> LoadDashboard
    CheckPwdAge -->|No| LoadDashboard[Load User Dashboard]
    
    LoadDashboard --> ActiveSession[Active Session]
    ActiveSession --> UserActivity{User<br/>Activity?}
    UserActivity -->|Active| AccessResource[Access Protected Resources]
    AccessResource --> CheckPermission{User Has<br/>Permission?}
    CheckPermission -->|No| AccessDenied[Display: Access Denied]
    AccessDenied --> LogDenied[Log Access Attempt<br/>in Audit Trail]
    LogDenied --> UserActivity
    CheckPermission -->|Yes| GrantAccess[Grant Access]
    GrantAccess --> LogAccess[Log Data Access]
    LogAccess --> UserActivity
    
    UserActivity -->|Inactive 30min| Timeout[Session Timeout]
    Timeout --> LogLogout[Log Session Timeout]
    LogLogout --> DestroySession[Destroy Session Token]
    DestroySession --> RedirectLogin[Redirect to Login Page]
    RedirectLogin --> TimeoutMsg[Display: Session Expired]
    TimeoutMsg --> End3([Logged Out])
    
    UserActivity -->|Manual Logout| ManualLogout[User Clicks Logout]
    ManualLogout --> LogManual[Log Logout Event]
    LogManual --> DestroySession
```

**Security Controls**:
- 5 failed attempts trigger 30-minute account lockout
- Session timeout after 30 minutes of inactivity
- All login attempts (success and failure) logged with IP address
- Password expiry warning at 90 days

---

## 7. User Management Workflow (Admin Only)

```mermaid
flowchart TD
    Start([Admin Opens<br/>User Management]) --> ViewUsers[Display All Users:<br/>Active and Inactive]
    ViewUsers --> SelectAction{Select<br/>Action}
    
    SelectAction -->|Create User| CreateUser[Click Create New User]
    CreateUser --> EnterUserInfo[Enter User Info:<br/>Name, Email, Username]
    EnterUserInfo --> SelectRole[Select Role:<br/>Admin or Clinician]
    SelectRole --> ProfInfo[Enter Professional Info:<br/>Title, License Number]
    ProfInfo --> GenPassword[Generate Temporary Password]
    GenPassword --> ValidateUser{All Required<br/>Fields?}
    ValidateUser -->|No| UserErrors[Display Validation Errors]
    UserErrors --> EnterUserInfo
    ValidateUser -->|Yes| SaveUser[Save User Account]
    SaveUser --> SendWelcome[Send Welcome Email<br/>with Temp Password]
    SendWelcome --> LogCreate[Log User Creation<br/>in Audit Trail]
    LogCreate --> ForceChange[Force Password Change<br/>on First Login]
    ForceChange --> End1([User Created])
    
    SelectAction -->|Update User| SelectUser[Select User to Update]
    SelectUser --> EditUserInfo[Edit User Details]
    EditUserInfo --> ChangeRoleOpt{Change<br/>Role?}
    ChangeRoleOpt -->|Yes| ConfirmRole[Confirm Role Change:<br/>Impacts Permissions]
    ConfirmRole --> SaveUpdates
    ChangeRoleOpt -->|No| SaveUpdates[Save Updates]
    SaveUpdates --> LogUpdate[Log Update in Audit Trail]
    LogUpdate --> End2([User Updated])
    
    SelectAction -->|Deactivate User| SelectDeactivate[Select User to Deactivate]
    SelectDeactivate --> ConfirmDeactivate{Confirm:<br/>Deactivate User?}
    ConfirmDeactivate -->|No| End3([Cancelled])
    ConfirmDeactivate -->|Yes| SetInactive[Set is_active = FALSE]
    SetInactive --> TerminateSessions[Terminate All Active Sessions]
    TerminateSessions --> LogDeactivate[Log Deactivation]
    LogDeactivate --> End4([User Deactivated])
    
    SelectAction -->|Reset Password| SelectReset[Select User for<br/>Password Reset]
    SelectReset --> GenResetToken[Generate Secure<br/>Reset Token]
    GenResetToken --> SendResetEmail[Email Password<br/>Reset Link]
    SendResetEmail --> LogReset[Log Password Reset Request]
    LogReset --> WaitUser[User Clicks Link<br/>and Sets New Password]
    WaitUser --> ExpireToken[Reset Token Expires<br/>After 24 Hours]
    ExpireToken --> End5([Password Reset])
    
    SelectAction -->|Unlock Account| SelectLocked[Select Locked User]
    SelectLocked --> ClearLockout[Clear Lockout:<br/>Reset Failed Attempts]
    ClearLockout --> NotifyUser[Notify User:<br/>Account Unlocked]
    NotifyUser --> LogUnlock[Log Unlock Action]
    LogUnlock --> End6([Account Unlocked])
    
    SelectAction -->|View Audit Log| SelectAuditUser[Select User to Audit]
    SelectAuditUser --> LoadAudit[Load User's Audit Trail]
    LoadAudit --> FilterAudit{Filter<br/>Events?}
    FilterAudit -->|Yes| SetAuditFilters[Set Date Range,<br/>Event Type]
    SetAuditFilters --> DisplayAudit
    FilterAudit -->|No| DisplayAudit[Display Audit Log]
    DisplayAudit --> ExportAudit{Export<br/>Audit Log?}
    ExportAudit -->|Yes| ExportCSV[Export as CSV]
    ExportCSV --> LogAuditExport[Log Audit Export]
    LogAuditExport --> End7([Done])
    ExportAudit -->|No| End8([Done])
```

**Admin Controls**:
- Only admins can create, deactivate, or modify user roles
- Password resets use secure time-limited tokens (24-hour expiry)
- Deactivating user immediately terminates all active sessions
- All admin actions comprehensively logged

---

## Integration Summary

### Workflow Interconnections

```mermaid
graph LR
    A[Patient Registration] --> B[Consultation Session]
    B --> C[Mental State Exam]
    C --> D[Diagnosis & Management]
    B --> E[Collaboration]
    D --> F[Consultation Review]
    F --> B
    B --> G[Reports & Analytics]
    F --> G
    H[Authentication] --> A
    H --> B
    H --> E
    H --> F
    H --> G
    I[User Management] --> H
```

### Typical End-to-End Patient Journey

1. **Intake**: Patient registered → Demographics captured → Emergency contact added
2. **Initial Assessment**: Consultation created → MSE completed → Diagnosis documented → Treatment plan established
3. **Collaboration** (if needed): Collaborating clinicians added → Shared review and input → Integrated care plan
4. **Follow-Up**: Patient returns → Review created → Vitals recorded → Progress assessed → Plan adjusted
5. **Ongoing Care**: Repeat reviews → Treatment modifications → Goal progress tracking
6. **Reporting**: Clinician generates reports → Analyzes outcomes → Quality improvement

---

**Next Steps**: Review [Roles & Permissions](05-roles-permissions.md) for access control specifications.
