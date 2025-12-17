export interface User {
  id: string
  username: string
  email: string
  first_name: string
  last_name: string
  role: 'admin' | 'clinician'
  professional_title?: string
  license_number?: string
  phone?: string
  is_active: boolean
  last_login?: string
  created_at: string
  updated_at: string
}

export interface Patient {
  id: string
  first_name: string
  middle_name?: string
  last_name: string
  date_of_birth: string
  gender: string
  phone_number: string
  email?: string
  address_line1?: string
  address_line2?: string
  landmark?: string
  city: string
  state_province: string
  postal_code: string
  country: string
  town?: string
  marital_status?: string
  occupation?: string
  education_level?: string
  religion?: string
  nhis_status?: 'insured' | 'uninsured'
  relative_name?: string
  relative_relationship?: string
  relative_phone?: string
  assessment_time?: string
  is_active: boolean
  created_at: string
  updated_at: string
  emergency_contacts?: EmergencyContact[]
}

export interface EmergencyContact {
  id: string
  patient_id: string
  contact_name: string
  relationship: string
  phone_number: string
  email?: string
  is_primary: boolean
}

export interface Consultation {
  id: string
  patient_id: string
  primary_clinician_id: string
  consultation_date: string
  consultation_time: string
  session_type: string
  session_duration?: number
  chief_complaint: string
  history_present_illness: string
  risk_assessment: 'low' | 'moderate' | 'high'
  is_locked: boolean
  created_at: string
  updated_at: string
  patient_name?: string
  clinician_name?: string
  clinical_summary?: string
  mental_status_exam?: string
  diagnosis?: string
  treatment_plan?: string
}

export interface ErrorResponse {
  error: {
    message: string
    code?: string
    errors?: Record<string, string[]>
  }
}

export interface ApiResponse<T> {
  data: T
  meta?: {
    pagination?: {
      page: number
      per_page: number
      total: number
      total_pages: number
    }
    [key: string]: any
  }
  message?: string
}

export type ToastType = 'success' | 'error' | 'warning' | 'info'

export interface ToastNotification {
  id: string
  type: ToastType
  message: string
  duration?: number
}

export interface DashboardStats {
  totalPatients?: number
  consultationsThisMonth?: number
  pendingReviews?: number
  activeClinicians?: number
}

export interface DashboardResponse {
  stats: DashboardStats
  recentConsultations: Consultation[]
}

export interface Billing {
  id: string
  patient_id: string
  consultation_id?: string
  billing_date: string
  service_type: 'consultation' | 'home_visit' | 'medication' | 'other'
  amount: number
  nhis_covered: boolean
  nhis_amount?: number
  patient_amount?: number
  payment_status: 'pending' | 'partial' | 'paid' | 'waived'
  payment_date?: string
  payment_method?: 'cash' | 'mobile_money' | 'bank_transfer' | 'nhis'
  invoice_number: string
  notes?: string
  created_by?: string
  created_at: string
  updated_at: string
  patient?: Patient
  consultation?: Consultation
}

export interface Medication {
  id: string
  name: string
  generic_name?: string
  dosage_form?: 'tablet' | 'capsule' | 'syrup' | 'injection' | 'other'
  strength?: string
  category?: string
  is_active: boolean
  created_at: string
  updated_at: string
}

export interface Prescription {
  id: string
  patient_id: string
  consultation_id?: string
  prescription_date: string
  prescribed_by: string
  medication_id: string
  dosage: string
  frequency: string
  duration: string
  quantity?: number
  instructions?: string
  refills: number
  is_active: boolean
  start_date?: string
  end_date?: string
  created_at: string
  updated_at: string
  patient?: Patient
  consultation?: Consultation
  medication?: Medication
  prescribed_by_user?: User
}

export interface HomeVisit {
  id: string
  patient_id?: string
  client_name: string
  age?: number
  sex?: 'male' | 'female' | 'other'
  community_location: string
  contact: string
  visit_date: string
  visit_time?: string
  clinician_id: string
  diagnosis_condition?: string
  medication_prescription?: string
  observations?: string
  impression?: string
  management?: string
  recommendation?: string
  created_at: string
  updated_at: string
  patient?: Patient
  clinician?: User
}

