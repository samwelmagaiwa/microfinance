export interface Loan {
  id?: number;
  borrower_id: number;
  borrowerName?: string;
  loan_number?: string;
  amount: number;
  interest_rate?: number;
  duration_months?: number;
  status?: string;
  approval_status?: string;
  current_approval_step?: string;
  
  disbursed_at?: string;
  first_payment_date?: string;
  monthly_payment?: number;
  total_interest?: number;
  total_payment?: number;
  loan_product?: string;
  repayment_method?: string;
  repayment_frequency?: string;
  
  // Collateral
  collateral_description?: string;
  guarantor1_name?: string;
  guarantor1_phone?: string;
  guarantor2_name?: string;
  guarantor2_phone?: string;
  
  // Employment/Business
  is_employed?: boolean;
  has_business?: boolean;
  collateral_type?: string;
  collateral_registration_number?: string;
  collateral_ownership?: string;
  collateral_current_value?: number;
  collateral_appearance?: string;
  
  // Approval workflow
  loan_officer_id?: number;
  loan_officer_signature_id?: string;
  loan_officer_approved_at?: string;
  loan_officer_hash?: string;
  
  loan_manager_id?: number;
  loan_manager_signature_id?: string;
  loan_manager_approved_at?: string;
  loan_manager_hash?: string;
  
  general_manager_id?: number;
  general_manager_signature_id?: string;
  general_manager_approved_at?: string;
  general_manager_hash?: string;
  
  managing_director_id?: number;
  managing_director_signature_id?: string;
  managing_director_approved_at?: string;
  managing_director_hash?: string;
  
  // Rejection
  rejection_reason?: string;
  rejected_by?: number;
  
  // Document integrity
  document_hash?: string;
  hash_generated_at?: string;
  
  // Relations
  schedules?: LoanSchedule[];
  payments?: Payment[];
  
  created_at?: string;
  updated_at?: string;
}

export interface LoanSchedule {
  id?: number;
  loan_id?: number;
  due_date: string;
  principal_amount: number;
  interest_amount: number;
  total_due: number;
  status: string;
}

export interface Payment {
  id?: number;
  loan_id?: number;
  amount: number;
  payment_date: string;
  payment_method?: string;
  transaction_reference?: string;
}