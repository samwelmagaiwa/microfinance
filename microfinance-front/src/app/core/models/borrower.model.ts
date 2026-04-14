export interface Borrower {
  id?: number;
  user_id?: number;
  full_name: string;
  gender?: string;
  date_of_birth?: string;
  age?: number;
  id_type?: string;
  nida_number: string;
  tin_number?: string;
  phone: string;
  alt_phone?: string;
  email?: string;
  marital_status?: string;
  dependents?: number;
  
  // Address
  region?: string;
  district?: string;
  ward?: string;
  village?: string;
  residence_type?: string;
  years_at_address?: number;
  
  // Employment
  employment_status?: string;
  employer_name?: string;
  employer_address?: string;
  employer_phone?: string;
  occupation?: string;
  monthly_salary?: number;
  
  // Business
  business_name?: string;
  business_type?: string;
  business_location?: string;
  years_in_business?: number;
  monthly_revenue?: number;
  
  // Financial
  existing_loans?: boolean;
  other_institutions?: string;
  total_existing_amount?: number;
  current_savings?: number;
  
  // Banking
  bank_name?: string;
  bank_account?: string;
  mobile_money_number?: string;
  
  // Loan Request
  loan_product?: string;
  loan_amount?: number;
  loan_purpose?: string;
  repayment_period?: number;
  interest_rate?: number;
  mandatory_savings?: number;
  
  // Status
  borrower_account_number?: string;
  branch?: string;
  risk_assessment?: string;
  status?: string;
  officer_remarks?: string;
  officer_confirmed?: boolean;
  
  // Approval workflow
  reviewed_by_loan_manager_id?: number;
  reviewed_by_gm_id?: number;
  reviewed_by_md_id?: number;
  loan_manager_reviewed_at?: string;
  gm_reviewed_at?: string;
  md_reviewed_at?: string;
  loan_manager_remarks?: string;
  gm_remarks?: string;
  md_remarks?: string;
  
  // Rejection
  rejected_by_id?: number;
  rejected_at?: string;
  rejection_reason?: string;
  
  // Digital signatures
  loan_officer_hash?: string;
  loan_officer_signed_at?: string;
  loan_manager_hash?: string;
  loan_manager_signed_at?: string;
  gm_hash?: string;
  gm_signed_at?: string;
  md_hash?: string;
  md_signed_at?: string;
  
  created_at?: string;
  updated_at?: string;
}