import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../../core/environments/environment';

@Component({
  selector: 'app-mkopo-binafsi',
  template: `
    <div class="application-form">
      <h2>{{ getFormTitle() }}</h2>
      <p class="subtitle">{{ getFormSubtitle() }}</p>
      
      <form (ngSubmit)="onSubmit()">
        <!-- Section 1: Borrower Information -->
        <fieldset>
          <legend>1. TAARIFA ZA MWOMBAJI (Borrower Information)</legend>
          
          <div class="form-row">
            <div class="form-group">
              <label>Jina kamili la mwombaji *</label>
              <input type="text" [(ngModel)]="formData.full_name" name="full_name" required>
            </div>
            <div class="form-group">
              <label>Jina maarufu</label>
              <input type="text" [(ngModel)]="formData.nick_name" name="nick_name">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Jinsia</label>
              <select [(ngModel)]="formData.gender" name="gender">
                <option value="">Chagua</option>
                <option value="male">Mume</option>
                <option value="female">Mke</option>
              </select>
            </div>
            <div class="form-group">
              <label>Tarehe ya kuzaliwa</label>
              <input type="date" [(ngModel)]="formData.date_of_birth" name="date_of_birth">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Aina ya Kitambulisho</label>
              <select [(ngModel)]="formData.id_type" name="id_type">
                <option value="">Chagua</option>
                <option value="nida">NIDA</option>
                <option value="passport">Pasipoti</option>
                <option value="license">Leseni</option>
              </select>
            </div>
            <div class="form-group">
              <label>Namba ya Kitambulisho *</label>
              <input type="text" [(ngModel)]="formData.nida_number" name="nida_number" required>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Simu *</label>
              <input type="text" [(ngModel)]="formData.phone" name="phone" required>
            </div>
            <div class="form-group">
              <label>Simu mengine</label>
              <input type="text" [(ngModel)]="formData.alt_phone" name="alt_phone">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Hali ya Ndoa</label>
              <select [(ngModel)]="formData.marital_status" name="marital_status">
                <option value="">Chagua</option>
                <option value="single">Sijaoa/olewa</option>
                <option value="married">Nimeoa/olewa</option>
                <option value="divorced">Nimeachika</option>
                <option value="widowed">Mjane/Mgane</option>
              </select>
            </div>
            <div class="form-group">
              <label>Idadi ya utegemezi</label>
              <input type="number" [(ngModel)]="formData.dependents" name="dependents">
            </div>
          </div>
        </fieldset>

        <!-- Section 2: Address -->
        <fieldset>
          <legend>2. ANUANI (Address)</legend>
          
          <div class="form-row">
            <div class="form-group">
              <label>Mkoa</label>
              <input type="text" [(ngModel)]="formData.region" name="region">
            </div>
            <div class="form-group">
              <label>Wilaya</label>
              <input type="text" [(ngModel)]="formData.district" name="district">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Kata</label>
              <input type="text" [(ngModel)]="formData.ward" name="ward">
            </div>
            <div class="form-group">
              <label>Mtaa/Kijiji</label>
              <input type="text" [(ngModel)]="formData.village" name="village">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Umiliki wa Makazi</label>
              <select [(ngModel)]="formData.residence_type" name="residence_type">
                <option value="">Chagua</option>
                <option value="own">Kwangu</option>
                <option value="rented">Kuajiri</option>
                <option value="family">Kwa familia</option>
                <option value="other">Mengine</option>
              </select>
            </div>
            <div class="form-group">
              <label>Namba ya Nyumba</label>
              <input type="text" [(ngModel)]="formData.house_number" name="house_number">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Umeishi hapo tangu lini?</label>
              <select [(ngModel)]="formData.years_at_address" name="years_at_address">
                <option value="">Chagua</option>
                <option value="<1">Chini ya mwaka 1</option>
                <option value="1-3">1-3 mwaka</option>
                <option value="4-5">4-5 mwaka</option>
                <option value="5+">Zaidi ya 5</option>
              </select>
            </div>
          </div>
        </fieldset>

        <!-- Section 3: Employment OR Business -->
        <fieldset *ngIf="isPersonalOrEmployee()">
          <legend>3. TAARIFA ZA AJIRA (Employment)</legend>
          
          <div class="form-row">
            <div class="form-group">
              <label>Jina la Mwajiri/Kampuni</label>
              <input type="text" [(ngModel)]="formData.employer_name" name="employer_name">
            </div>
            <div class="form-group">
              <label>Mahali Ofisi ilipo</label>
              <input type="text" [(ngModel)]="formData.employer_address" name="employer_address">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Wadhifa</label>
              <input type="text" [(ngModel)]="formData.employee_title" name="employee_title">
            </div>
            <div class="form-group">
              <label>Simu ya Ofisi</label>
              <input type="text" [(ngModel)]="formData.employer_phone" name="employer_phone">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Umefanya kazi hapo toka</label>
              <input type="date" [(ngModel)]="formData.contract_start_date" name="contract_start_date">
            </div>
            <div class="form-group">
              <label>Mshahara (TZS)</label>
              <input type="number" [(ngModel)]="formData.monthly_salary" name="monthly_salary">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Aina ya Ajira</label>
              <select [(ngModel)]="formData.contract_type" name="contract_type">
                <option value="">Chagua</option>
                <option value="permanent">Ya Kudumu</option>
                <option value="contract">Mkataba</option>
                <option value="temporary">Ya Muda</option>
              </select>
            </div>
            <div class="form-group">
              <label>Tarehe ya kumaliza mkataba</label>
              <input type="date" [(ngModel)]="formData.contract_end_date" name="contract_end_date">
            </div>
          </div>
        </fieldset>

        <fieldset *ngIf="isBusiness()">
          <legend>3. TAARIFA ZA BIASHARA (Business)</legend>
          
          <div class="form-row">
            <div class="form-group">
              <label>Jina la Biashara</label>
              <input type="text" [(ngModel)]="formData.business_name" name="business_name">
            </div>
            <div class="form-group">
              <label>Aina ya Biashara</label>
              <input type="text" [(ngModel)]="formData.business_type" name="business_type">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Mahali Biashara Ilipo</label>
              <input type="text" [(ngModel)]="formData.business_location" name="business_location">
            </div>
            <div class="form-group">
              <label>Umefanya Biashara hii tangu</label>
              <input type="date" [(ngModel)]="formData.business_start_date" name="business_start_date">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Wastani wa kipato kwa mwezi (TZS)</label>
              <input type="number" [(ngModel)]="formData.monthly_revenue" name="monthly_revenue">
            </div>
            <div class="form-group">
              <label>Wastani wa matumizi kwa mwezi (TZS)</label>
              <input type="number" [(ngModel)]="formData.monthly_expenses" name="monthly_expenses">
            </div>
          </div>
        </fieldset>

        <!-- Section 4: Spouse Info (if married) -->
        <fieldset *ngIf="formData.marital_status === 'married'">
          <legend>4. TAARIFA YA MWUME/MKE (Spouse Info)</legend>
          
          <div class="form-row">
            <div class="form-group">
              <label>Jina kamili la mume/mke</label>
              <input type="text" [(ngModel)]="formData.spouse_name" name="spouse_name">
            </div>
            <div class="form-group">
              <label>Simu</label>
              <input type="text" [(ngModel)]="formData.spouse_phone" name="spouse_phone">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Kazi anayofanya</label>
              <input type="text" [(ngModel)]="formData.spouse_occupation" name="spouse_occupation">
            </div>
            <div class="form-group">
              <label>Mahali ilipo Ofisi</label>
              <input type="text" [(ngModel)]="formData.spouse_workplace" name="spouse_workplace">
            </div>
          </div>
        </fieldset>

        <!-- Section 5: Loan Request -->
        <fieldset>
          <legend>{{ isGroup() ? '5' : '5' }}. KIASI CHA MKOPO (Loan Request)</legend>
          
          <div class="form-row">
            <div class="form-group">
              <label>Kiasi cha Mkopo (TZS) *</label>
              <input type="number" [(ngModel)]="formData.loan_amount" name="loan_amount" required>
            </div>
            <div class="form-group">
              <label>Muda wa kulipa Mkopo</label>
              <select [(ngModel)]="formData.repayment_period" name="repayment_period">
                <option value="6">6 Miezi</option>
                <option value="12">12 Miezi</option>
                <option value="18">18 Miezi</option>
                <option value="24">24 Miezi</option>
                <option value="36">36 Miezi</option>
              </select>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Ni kiasi gani cha rejesho unaweza kulipa bila matatizo?</label>
              <input type="number" [(ngModel)]="formData.repayment_capacity" name="repayment_capacity">
            </div>
            <div class="form-group">
              <label>Malengo ya Mkopo</label>
              <textarea [(ngModel)]="formData.loan_purpose" name="loan_purpose" rows="3"></textarea>
            </div>
          </div>
        </fieldset>

        <!-- Section 6: Loan History -->
        <fieldset>
          <legend>6. HISTORIA YA MIKOPO (Loan History)</legend>
          
          <div class="form-row">
            <div class="form-group">
              <label>Umewahi kukopa hapa au mahali pengine?</label>
              <select [(ngModel)]="formData.existing_loans" name="existing_loans" (change)="toggleLoanHistory()">
                <option [ngValue]="false">Hapana</option>
                <option [ngValue]="true">Ndiyo</option>
              </select>
            </div>
          </div>
          
          <div *ngIf="formData.existing_loans" class="loan-history">
            <div class="form-row">
              <div class="form-group">
                <label>Jina la Taasisi</label>
                <input type="text" [(ngModel)]="formData.other_institutions" name="other_institutions">
              </div>
              <div class="form-group">
                <label>Kiasi ulichokopa</label>
                <input type="number" [(ngModel)]="formData.total_existing_amount" name="total_existing_amount">
              </div>
            </div>
          </div>
        </fieldset>

        <!-- Section 7: Guarantors -->
        <fieldset>
          <legend>7. WADHAMINI (Guarantors)</legend>
          
          <h4>Mdhamini No. 1</h4>
          <div class="form-row">
            <div class="form-group">
              <label>Jina kamili la Mdhamini</label>
              <input type="text" [(ngModel)]="formData.guarantor1.full_name" name="guarantor1_name">
            </div>
            <div class="form-group">
              <label>Mahali Anapoishi</label>
              <input type="text" [(ngModel)]="formData.guarantor1.address" name="guarantor1_address">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Kazi Anayofanya</label>
              <input type="text" [(ngModel)]="formData.guarantor1.occupation" name="guarantor1_occupation">
            </div>
            <div class="form-group">
              <label>Simu</label>
              <input type="text" [(ngModel)]="formData.guarantor1.phone" name="guarantor1_phone">
            </div>
          </div>
          
          <h4>Mdhamini No. 2</h4>
          <div class="form-row">
            <div class="form-group">
              <label>Jina kamili la Mdhamini</label>
              <input type="text" [(ngModel)]="formData.guarantor2.full_name" name="guarantor2_name">
            </div>
            <div class="form-group">
              <label>Mahali Anapoishi</label>
              <input type="text" [(ngModel)]="formData.guarantor2.address" name="guarantor2_address">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Kazi Anayofanya</label>
              <input type="text" [(ngModel)]="formData.guarantor2.occupation" name="guarantor2_occupation">
            </div>
            <div class="form-group">
              <label>Simu</label>
              <input type="text" [(ngModel)]="formData.guarantor2.phone" name="guarantor2_phone">
            </div>
          </div>
        </fieldset>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" (click)="cancel()">Cancel</button>
          <button type="submit" class="btn btn-primary" [disabled]="submitting">
            {{ submitting ? 'Submitting...' : 'Submit Application' }}
          </button>
        </div>
      </form>
    </div>
  `,
  styles: [`
    .application-form { max-width: 900px; margin: 0 auto; padding: 1rem; }
    .application-form h2 { margin-bottom: 0.25rem; }
    .subtitle { color: #6b7280; margin-bottom: 1.5rem; }
    fieldset { background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; border: 1px solid #e5e7eb; }
    legend { font-weight: 700; color: #4f46e5; padding: 0 0.5rem; font-size: 1rem; }
    legend + h4 { margin-top: 1rem; margin-bottom: 0.5rem; color: #374151; font-size: 0.9rem; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
    .form-group { display: flex; flex-direction: column; }
    .form-group label { margin-bottom: 0.35rem; font-weight: 500; font-size: 0.85rem; color: #374151; }
    .form-group input, .form-group select, .form-group textarea { padding: 0.6rem; border: 1px solid #d1d5db; border-radius: 6px; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #4f46e5; }
    .form-actions { display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem; }
    .loan-history { padding-top: 1rem; border-top: 1px dashed #e5e7eb; margin-top: 1rem; }
  `]
})
export class MkopoBinafsiComponent implements OnInit {
  formData: any = {
    loan_product: 'MKOPO BINAFSI',
    repayment_period: 12,
    existing_loans: false,
    guarantor1: { full_name: '', address: '', occupation: '', phone: '' },
    guarantor2: { full_name: '', address: '', occupation: '', phone: '' }
  };
  submitting = false;
  loanType = 'personal';

  constructor(private http: HttpClient, private router: Router, private route: ActivatedRoute) {}

  ngOnInit(): void {
    this.route.queryParams.subscribe(params => {
      this.loanType = params['type'] || 'personal';
      this.setLoanProduct();
    });
  }

  setLoanProduct(): void {
    if (this.loanType === 'employee') {
      this.formData.loan_product = 'MKOPO BINAFSI (Employee)';
      this.formData.is_employed = true;
      this.formData.has_business = false;
    } else if (this.loanType === 'personal') {
      this.formData.loan_product = 'MKOPO BINAFSI (Personal)';
      this.formData.is_employed = false;
      this.formData.has_business = true;
    }
  }

  getFormTitle(): string {
    if (this.loanType === 'employee') return 'FOMU YA MAOMBI YA MKOPO BINAFSI (Employed)';
    return 'FOMU YA MAOMBI YA MKOPO BINAFSI (Personal)';
  }

  getFormSubtitle(): string {
    return 'Orodha ya maombi ya mkopo binafsi kwa wateja wenye biashara au wasio na ajira rasmi';
  }

  isPersonalOrEmployee(): boolean { return this.loanType !== 'group'; }
  isBusiness(): boolean { return this.loanType === 'personal'; }
  isGroup(): boolean { return this.loanType === 'group'; }

  toggleLoanHistory(): void {}

  onSubmit(): void {
    this.submitting = true;
    const token = localStorage.getItem('token');
    
    this.http.post(`${environment.apiUrl}/borrowers`, this.formData, {
      headers: { Authorization: `Bearer ${token}` }
    }).subscribe({
      next: () => {
        alert('Application submitted successfully!');
        this.router.navigate(['/borrowers']);
      },
      error: (err) => {
        alert(err.error?.message || 'Failed to submit application');
        this.submitting = false;
      }
    });
  }

  cancel(): void { this.router.navigate(['/borrowers']); }
}