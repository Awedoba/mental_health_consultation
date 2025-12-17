# Dashboard Redesign Prompt

## Context
The Mental Health Consultation Web App has been enhanced with several new modules and features across 7 implementation phases. The dashboard needs to be completely reworked to reflect all new functionality and provide a comprehensive overview of the entire system.

## Current Dashboard State
The current dashboard (`frontend/pages/dashboard.vue`) only shows:
- Total Patients
- Consultations This Month
- Pending Reviews
- Active Clinicians (admin only)
- Recent Consultations table
- Placeholder charts

## New Features to Integrate

### Phase 1: Patient Management Updates
- NHIS status tracking (insured/uninsured)
- Relative information fields
- Landmark field (replacing address)
- Assessment time

### Phase 2: Consultation Form Updates
- Enhanced history fields (personal, occupational/marital, substance abuse)
- Enhanced MSE with new fields
- Review recommendations

### Phase 3: Billing Module
- Billing creation and tracking
- Payment status (pending, partial, paid, waived)
- NHIS coverage tracking
- Revenue tracking
- Invoice generation

### Phase 4: Prescription Module
- Medication master list
- Prescription creation and tracking
- Active prescriptions
- Prescription history

### Phase 5: Home Visits Module
- Home visit creation and tracking
- Community-based care records
- Standalone or patient-linked visits

### Phase 6: Reports Enhancement
- 7 new report types (client list, client query, consulting history, next visits, consulting data query, trends, total cases)

## Dashboard Redesign Requirements

### 1. Statistics Cards Section
Expand the stats grid to include:

**Primary Stats (Always Visible):**
- Total Patients (with breakdown: insured/uninsured)
- Total Consultations This Month
- Total Home Visits This Month
- Active Prescriptions
- Total Revenue (This Month)
- Pending Payments Amount
- Upcoming Next Visits (count)

**Admin-Only Stats:**
- Active Clinicians
- Total Billings This Month
- Total Prescriptions This Month

**Layout:**
- Use a responsive grid: 2 columns on mobile, 3 on tablet, 4 on desktop
- Add color-coded icons for each stat
- Show percentage changes or trends where applicable
- Make cards clickable to navigate to relevant pages

### 2. Quick Actions Section
Add a prominent quick actions bar with buttons:
- "New Consultation" → `/consultations/create`
- "New Patient" → `/patients/create`
- "New Billing" → `/billings/create`
- "New Prescription" → `/prescriptions/create`
- "New Home Visit" → `/home-visits/create`
- "Generate Report" → `/reports`

Style as prominent action buttons with icons, visible on all screen sizes.

### 3. Recent Activity Tabs
Replace single "Recent Consultations" table with a tabbed interface:

**Tab 1: Recent Consultations**
- Show last 10 consultations
- Include: Patient name, Date, Clinician, Status, Action link

**Tab 2: Recent Billings**
- Show last 10 billings
- Include: Patient name, Amount, Payment status, Date, Invoice number, Action link

**Tab 3: Recent Prescriptions**
- Show last 10 prescriptions
- Include: Patient name, Medication, Prescribed by, Date, Status (active/inactive), Action link

**Tab 4: Recent Home Visits**
- Show last 10 home visits
- Include: Client name, Location, Visit date, Clinician, Action link

**Tab 5: Upcoming Next Visits**
- Show next 10 scheduled visits from management plans
- Include: Patient name, Next visit date, Purpose, Clinician, Action link

### 4. Charts & Visualizations Section
Replace placeholder charts with actual data visualizations:

**Chart 1: Service Activity Overview**
- Bar or line chart showing:
  - Consultations per month (last 6 months)
  - Home visits per month (last 6 months)
  - Prescriptions per month (last 6 months)
- Use different colors for each service type

**Chart 2: Revenue & Payments**
- Line chart showing:
  - Total revenue per month (last 6 months)
  - Paid vs pending payments breakdown
- Stacked area or grouped bars

**Chart 3: NHIS Coverage Distribution**
- Pie or donut chart showing:
  - Insured patients percentage
  - Uninsured patients percentage

**Chart 4: Payment Status Distribution**
- Pie or donut chart showing:
  - Paid, Pending, Partial, Waived percentages

**Implementation Note:** Use Chart.js or similar library. If not installed, add it to the project.

### 5. Key Metrics Summary Cards
Add a section with key performance indicators:

**For Clinicians:**
- My Consultations This Month
- My Home Visits This Month
- My Active Prescriptions
- My Pending Billings

**For Admins:**
- System-wide totals
- All metrics above plus:
  - Total Revenue (All Time)
  - Average Consultation Rate
  - Patient Retention Rate

### 6. Quick Links to Reports
Add a "Quick Reports" section with cards linking to:
- Client List Report
- Next Visit Report
- Total Cases Report
- Trend Report
- Consulting Data Query

Each card should show a brief description and link to the report page.

### 7. Alerts & Notifications Section
Add an alerts section showing:
- Overdue payments (billing with payment_status = 'pending' and payment_date in past)
- Upcoming visits (next 7 days)
- Expiring prescriptions (end_date within 7 days)
- Incomplete consultations (consultations without MSE or management plan)

### 8. Backend API Updates Required

Update `DashboardController` to return:

```php
{
  "stats": {
    "totalPatients": int,
    "insuredPatients": int,
    "uninsuredPatients": int,
    "consultationsThisMonth": int,
    "homeVisitsThisMonth": int,
    "activePrescriptions": int,
    "totalPrescriptionsThisMonth": int,
    "totalRevenueThisMonth": decimal,
    "totalRevenueAllTime": decimal,
    "pendingPaymentsAmount": decimal,
    "upcomingNextVisits": int,
    "totalBillingsThisMonth": int,
    "activeClinicians": int,
    // Role-specific stats
    "myConsultationsThisMonth": int, // for clinicians
    "myHomeVisitsThisMonth": int, // for clinicians
    "myActivePrescriptions": int, // for clinicians
    "myPendingBillings": int // for clinicians
  },
  "recentConsultations": [...],
  "recentBillings": [...],
  "recentPrescriptions": [...],
  "recentHomeVisits": [...],
  "upcomingNextVisits": [...],
  "alerts": {
    "overduePayments": [...],
    "upcomingVisits": [...],
    "expiringPrescriptions": [...],
    "incompleteConsultations": [...]
  },
  "charts": {
    "serviceActivity": {
      "labels": ["Month 1", "Month 2", ...],
      "consultations": [10, 15, ...],
      "homeVisits": [5, 8, ...],
      "prescriptions": [20, 25, ...]
    },
    "revenue": {
      "labels": ["Month 1", "Month 2", ...],
      "totalRevenue": [1000, 1500, ...],
      "paid": [800, 1200, ...],
      "pending": [200, 300, ...]
    },
    "nhisCoverage": {
      "insured": 65,
      "uninsured": 35
    },
    "paymentStatus": {
      "paid": 70,
      "pending": 20,
      "partial": 5,
      "waived": 5
    }
  }
}
```

### 9. TypeScript Types Update

Update `frontend/types/index.ts` to include:

```typescript
export interface DashboardStats {
  totalPatients: number
  insuredPatients: number
  uninsuredPatients: number
  consultationsThisMonth: number
  homeVisitsThisMonth: number
  activePrescriptions: number
  totalPrescriptionsThisMonth: number
  totalRevenueThisMonth: number
  totalRevenueAllTime: number
  pendingPaymentsAmount: number
  upcomingNextVisits: number
  totalBillingsThisMonth: number
  activeClinicians: number
  myConsultationsThisMonth?: number
  myHomeVisitsThisMonth?: number
  myActivePrescriptions?: number
  myPendingBillings?: number
}

export interface DashboardResponse {
  stats: DashboardStats
  recentConsultations: Consultation[]
  recentBillings: Billing[]
  recentPrescriptions: Prescription[]
  recentHomeVisits: HomeVisit[]
  upcomingNextVisits: ManagementPlan[]
  alerts: {
    overduePayments: Billing[]
    upcomingVisits: ManagementPlan[]
    expiringPrescriptions: Prescription[]
    incompleteConsultations: Consultation[]
  }
  charts: {
    serviceActivity: {
      labels: string[]
      consultations: number[]
      homeVisits: number[]
      prescriptions: number[]
    }
    revenue: {
      labels: string[]
      totalRevenue: number[]
      paid: number[]
      pending: number[]
    }
    nhisCoverage: {
      insured: number
      uninsured: number
    }
    paymentStatus: {
      paid: number
      pending: number
      partial: number
      waived: number
    }
  }
}
```

### 10. Design Requirements

**Visual Design:**
- Use consistent color scheme:
  - Indigo: Patients, Consultations
  - Green: Revenue, Payments
  - Blue: Prescriptions
  - Orange: Home Visits
  - Yellow: Alerts, Pending items
  - Purple: Admin features
- Ensure responsive design (mobile-first)
- Use Tailwind CSS utility classes
- Maintain accessibility (ARIA labels, keyboard navigation)

**Performance:**
- Lazy load chart data
- Use loading skeletons for all async data
- Implement error boundaries
- Cache dashboard data appropriately

**User Experience:**
- Show empty states when no data
- Provide refresh button
- Add filters for date ranges (optional)
- Make all cards/items clickable for navigation
- Show tooltips for complex metrics

### 11. Implementation Steps

1. **Update Backend DashboardController**
   - Add queries for all new statistics
   - Include billing, prescription, home visit data
   - Calculate chart data
   - Generate alerts

2. **Update TypeScript Types**
   - Add new interfaces for dashboard data
   - Update existing types if needed

3. **Install Chart Library** (if not present)
   - Add Chart.js or similar
   - Configure for Vue 3

4. **Redesign Dashboard Component**
   - Restructure layout
   - Add new stat cards
   - Implement tabbed recent activity
   - Add charts with real data
   - Add quick actions section
   - Add alerts section
   - Add quick reports section

5. **Test Dashboard**
   - Test with different user roles
   - Test with empty data
   - Test responsive design
   - Test performance with large datasets

### 12. Success Criteria

- [ ] All new modules represented in dashboard
- [ ] Statistics are accurate and role-appropriate
- [ ] Charts display real data
- [ ] Quick actions work correctly
- [ ] Recent activity tabs show correct data
- [ ] Alerts are functional
- [ ] Dashboard is responsive
- [ ] Performance is acceptable (< 2s load time)
- [ ] Error handling is robust
- [ ] Empty states are user-friendly

---

## Example Prompt for AI Assistant

"Rework the dashboard page (`frontend/pages/dashboard.vue`) to reflect all the new features added in the 7 implementation phases. The dashboard should include:

1. Expanded statistics cards showing: total patients (with NHIS breakdown), consultations, home visits, prescriptions, revenue, pending payments, and upcoming visits
2. Quick action buttons for creating new consultations, patients, billings, prescriptions, and home visits
3. Tabbed recent activity section showing recent consultations, billings, prescriptions, home visits, and upcoming next visits
4. Real charts showing service activity trends, revenue trends, NHIS coverage, and payment status distribution
5. Alerts section for overdue payments, upcoming visits, expiring prescriptions, and incomplete consultations
6. Quick links to key reports

Update the backend `DashboardController` to provide all necessary data. Use Chart.js for visualizations. Ensure the design is responsive and follows the existing Tailwind CSS patterns. Make all cards and items clickable for navigation."

---

**Note:** This prompt provides comprehensive guidance for completely reworking the dashboard to be a true command center for the Mental Health Consultation Web App.

