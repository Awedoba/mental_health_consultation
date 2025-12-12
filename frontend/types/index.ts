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
  address_line1: string
  address_line2?: string
  city: string
  state_province: string
  postal_code: string
  country: string
  marital_status?: string
  occupation?: string
  education_level?: string
  is_active: boolean
  created_at: string
  updated_at: string
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
}

