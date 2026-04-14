import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../../core/environments/environment';

@Component({
  selector: 'app-mkopo-kikundi',
  template: `
    <div class="application-form">
      <h2>FOMU YA MAOMBI YA MKOPO WA KIKUNDI</h2>
      <p class="subtitle">Orodha ya maombi ya mkopo kwa vikundi</p>
      
      <form (ngSubmit)="onSubmit()">
        <!-- Section 1: Group Leader Info -->
        <fieldset>
          <legend>1. TAARIFA ZA MWOMBAJI (Group Leader)</legend>
          
          <div class="form-row">
            <div class="form-group">
              <label>Jina kamili la mwombaji *</label>
              <input type="text" [(ngModel)]="formData.full_name" name="full_name" required>
            </div>
            <div class="form-group">
              <label>Jinsia</label>
              <select [(ngModel)]="formData.gender" name="gender">
                <option value="">Chagua</option>
                <option value="male">Mume</option>
                <option value="female">Mke</option>
              </select>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Tarehe ya kuzaliwa</label>
              <input type="date" [(ngModel)]="formData.date_of_birth" name="date_of_birth">
            </div>
            <div class="form-group">
              <label>Aina ya Kitambulisho</label>
              <select [(ngModel)]="formData.id_type" name="id_type">
                <option value="">Chagua</option>
                <option value="nida">NIDA</option>
                <option value="passport">Pasipoti</option>
                <option value="license">Leseni</option>
              </select>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Namba ya Kitambulisho *</label>
              <input type="text" [(ngModel)]="formData.nida_number" name="nida_number" required>
            </div>
            <div class="form-group">
              <label>Simu *</label>
              <input type="text" [(ngModel)]="formData.phone" name="phone" required>
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
              <label>Cheo katika Kikundi</label>
              <select [(ngModel)]="formData.group_position" name="group_position">
                <option value="">Chagua</option>
                <option value="chairman">Mwenyekiti</option>
                <option value="secretary">Katibu</option>
                <option value="treasurer">Mhazili</option>
                <option value="member">Mwanachama</option>
              </select>
            </div>
          </div>
          
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
        </fieldset>

        <!-- Section 2: Group Information -->
        <fieldset>
          <legend>2. TAARIFA ZA KIKUNDI (Group Info)</legend>
          
          <div class="form-row">
            <div class="form-group">
              <label>Jina la Kikundi *</label>
              <input type="text" [(ngModel)]="formData.group_name" name="group_name" required>
            </div>
            <div class="form-group">
              <label>Namba ya Usajili wa Kikundi</label>
              <input type="text" [(ngModel)]="formData.group_id_number" name="group_id_number">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Idadi ya Wanachama</label>
              <input type="number" [(ngModel)]="formData.group_members_count" name="group_members_count">
            </div>
            <div class="form-group">
              <label>Tarehe ya Usajili</label>
              <input type="date" [(ngModel)]="formData.group_established_date" name="group_established_date">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Anuani ya Makazi ya Kikundi</label>
              <input type="text" [(ngModel)]="formData.group_meeting_place" name="group_meeting_place">
            </div>
            <div class="form-group">
              <label>Muda Kikundi kimekaa</label>
              <select [(ngModel)]="formData.group_years" name="group_years">
                <option value="">Chagua</option>
                <option value="<1">Chini ya mwaka 1</option>
                <option value="1-3">1-3 mwaka</option>
                <option value="4-5">4-5 mwaka</option>
                <option value="5+">Zaidi ya 5</option>
              </select>
            </div>
          </div>
        </fieldset>

        <!-- Section 3: Group Leadership -->
        <fieldset>
          <legend>3. VIONGOZI WA KIKUNDI (Leadership)</legend>
          
          <div class="form-row">
            <div class="form-group">
              <label>Jina la Mwenyekiti</label>
              <input type="text" [(ngModel)]="formData.group_chairman_name" name="group_chairman_name">
            </div>
            <div class="form-group">
              <label>Simu ya Mwenyekiti</label>
              <input type="text" [(ngModel)]="formData.group_chairman_phone" name="group_chairman_phone">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Jina la Katibu</label>
              <input type="text" [(ngModel)]="formData.group_secretary_name" name="group_secretary_name">
            </div>
            <div class="form-group">
              <label>Simu ya Katibu</label>
              <input type="text" [(ngModel)]="formData.group_secretary_phone" name="group_secretary_phone">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Jina la Mhazili</label>
              <input type="text" [(ngModel)]="formData.group_treasurer_name" name="group_treasurer_name">
            </div>
            <div class="form-group">
              <label>Simu ya Mhazili</label>
              <input type="text" [(ngModel)]="formData.group_treasurer_phone" name="group_treasurer_phone">
            </div>
          </div>
        </fieldset>

        <!-- Section 4: Business Info -->
        <fieldset>
          <legend>4. TAARIFA ZA BIASHARA (Business)</legend>
          
          <div class="form-row">
            <div class="form-group">
              <label>Eneo la Biashara</label>
              <input type="text" [(ngModel)]="formData.business_location" name="business_location">
            </div>
            <div class="form-group">
              <label>Aina ya Biashara</label>
              <input type="text" [(ngModel)]="formData.business_type" name="business_type">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Umefanya Biashara hii tangu</label>
              <input type="date" [(ngModel)]="formData.business_start_date" name="business_start_date">
            </div>
            <div class="form-group">
              <label>Wastani wa kipato kwa mwezi (TZS)</label>
              <input type="number" [(ngModel)]="formData.monthly_revenue" name="monthly_revenue">
            </div>
          </div>
        </fieldset>

        <!-- Section 5: Loan Request -->
        <fieldset>
          <legend>5. KIASI CHA MKOPO (Loan Request)</legend>
          
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
              </select>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Kikundi kimewahi kukopa?</label>
              <select [(ngModel)]="formData.group_previously_loaned" name="group_previously_loaned">
                <option value="">Chagua</option>
                <option value="yes">Ndiyo</option>
                <option value="no">Hapana</option>
              </select>
            </div>
            <div class="form-group">
              <label>Ni kiasi gani cha rejesho kikundi kinaweza kulipa?</label>
              <input type="number" [(ngModel)]="formData.repayment_capacity" name="repayment_capacity">
            </div>
          </div>
          
          <div class="form-group">
            <label>Malengo ya Mkopo</label>
            <textarea [(ngModel)]="formData.loan_purpose" name="loan_purpose" rows="3"></textarea>
          </div>
        </fieldset>

        <!-- Section 6: Group Guarantors -->
        <fieldset>
          <legend>6. WADHAMINI (Guarantors)</legend>
          
          <h4>Mdhamini No. 1 (Mwenyekiti)</h4>
          <div class="form-row">
            <div class="form-group">
              <label>Jina kamili</label>
              <input type="text" [(ngModel)]="formData.guarantor1.full_name" name="guarantor1_name">
            </div>
            <div class="form-group">
              <label>Simu</label>
              <input type="text" [(ngModel)]="formData.guarantor1.phone" name="guarantor1_phone">
            </div>
          </div>
          
          <h4>Mdhamini No. 2 (Mke wa Mwenyekiti/Mme)</h4>
          <div class="form-row">
            <div class="form-group">
              <label>Jina kamili</label>
              <input type="text" [(ngModel)]="formData.guarantor2.full_name" name="guarantor2_name">
            </div>
            <div class="form-group">
              <label>Simu</label>
              <input type="text" [(ngModel)]="formData.guarantor2.phone" name="guarantor2_phone">
            </div>
          </div>
        </fieldset>
        
        <!-- Section 7: Bank Information -->
        <fieldset>
          <legend>7. TAARIFA ZA BENKI (Bank Info)</legend>
          <div class="form-row">
            <div class="form-group">
              <label>Jina la Benki</label>
              <input type="text" [(ngModel)]="formData.group_bank_name" name="group_bank_name">
            </div>
            <div class="form-group">
              <label>Namba ya Akaunti</label>
              <input type="text" [(ngModel)]="formData.group_bank_account" name="group_bank_account">
            </div>
          </div>
        </fieldset>

        <!-- Section 8: Signatories (Simplified) -->
        <fieldset>
          <legend>8. WANACHAMA NA SAINI (Members & Signatories)</legend>
          <p class="form-instruction">Tafadhali orodhesha wanachama wakuu wenye mamlaka ya saini</p>
          
          <div class="signatory-list">
            <div class="form-row">
              <div class="form-group">
                <label>Jina la Mwanachama 1</label>
                <input type="text" [(ngModel)]="formData.signatory1_name" name="sign_name_1">
              </div>
              <div class="form-group">
                <label>Simu</label>
                <input type="text" [(ngModel)]="formData.signatory1_phone" name="sign_phone_1">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Jina la Mwanachama 2</label>
                <input type="text" [(ngModel)]="formData.signatory2_name" name="sign_name_2">
              </div>
              <div class="form-group">
                <label>Simu</label>
                <input type="text" [(ngModel)]="formData.signatory2_phone" name="sign_phone_2">
              </div>
            </div>
          </div>
        </fieldset>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" (click)="cancel()">Cancel</button>
          <button type="submit" class="btn btn-primary" [disabled]="submitting">
            {{ submitting ? 'Submitting...' : 'Submit Group Application' }}
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
    .form-instruction { font-size: 0.8rem; color: #6b7280; margin-bottom: 1rem; font-style: italic; }
    .signatory-list { border-top: 1px dashed #e5e7eb; padding-top: 1rem; }
  `]
})
export class MkopoKikundiComponent implements OnInit {
  formData: any = {
    loan_product: 'MKOPO WA KIKUNDI',
    repayment_period: 12,
    guarantor1: { full_name: '', phone: '' },
    guarantor2: { full_name: '', phone: '' }
  };
  submitting = false;

  constructor(private http: HttpClient, private router: Router) {}

  ngOnInit(): void {}

  onSubmit(): void {
    this.submitting = true;
    const token = localStorage.getItem('token');
    
    this.http.post(`${environment.apiUrl}/borrowers`, this.formData, {
      headers: { Authorization: `Bearer ${token}` }
    }).subscribe({
      next: () => {
        alert('Group application submitted successfully!');
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