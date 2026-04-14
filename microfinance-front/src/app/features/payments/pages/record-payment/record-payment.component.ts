import { Component, OnInit, signal } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../../core/environments/environment';
import { Router } from '@angular/router';

@Component({
  selector: 'app-record-payment',
  template: `
    <div class="record-payment-page">
      <h2>Record Payment</h2>
      
      <form (ngSubmit)="onSubmit()" class="payment-form">
        <div class="form-group">
          <label>Select Loan *</label>
          <select [(ngModel)]="formData.loan_id" name="loan_id" required>
            <option value="">Select a loan</option>
            <option *ngFor="let loan of loans()" [value]="loan.id">
              {{ loan.borrowerName }} - {{ loan.amount | number }} TZS
            </option>
          </select>
        </div>

        <div class="form-group">
          <label>Amount *</label>
          <input type="number" [(ngModel)]="formData.amount" name="amount" required>
        </div>

        <div class="form-group">
          <label>Payment Date *</label>
          <input type="date" [(ngModel)]="formData.payment_date" name="payment_date" required>
        </div>

        <div class="form-group">
          <label>Payment Method *</label>
          <select [(ngModel)]="formData.payment_method" name="payment_method" required>
            <option value="Cash">Cash</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="Mobile Money">Mobile Money</option>
            <option value="Cheque">Cheque</option>
          </select>
        </div>

        <div class="form-group">
          <label>Transaction Reference</label>
          <input type="text" [(ngModel)]="formData.transaction_reference" name="transaction_reference" placeholder="Optional reference number">
        </div>

        <div class="alert alert-danger" *ngIf="error">{{ error }}</div>
        <div class="alert alert-success" *ngIf="success">{{ success }}</div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" (click)="cancel()">Cancel</button>
          <button type="submit" class="btn btn-primary" [disabled]="submitting">
            {{ submitting ? 'Recording...' : 'Record Payment' }}
          </button>
        </div>
      </form>
    </div>
  `,
  styles: [`
    .record-payment-page { max-width: 600px; margin: 0 auto; padding: 1rem; }
    .payment-form { background: white; padding: 2rem; border-radius: 8px; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
    .form-group input, .form-group select { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; }
    .form-actions { display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem; }
  `]
})
export class RecordPaymentComponent implements OnInit {
  loans = signal<any[]>([]);
  formData: any = {};
  submitting = false;
  error = '';
  success = '';

  constructor(private http: HttpClient, private router: Router) {}

  ngOnInit(): void {
    this.formData.payment_date = new Date().toISOString().split('T')[0];
    this.loadLoans();
  }

  loadLoans(): void {
    const token = localStorage.getItem('token');
    this.http.get<any>(`${environment.apiUrl}/loans`, {
      headers: { Authorization: `Bearer ${token}` }
    }).subscribe({
      next: (res) => { this.loans.set(res.data || res); }
    });
  }

  onSubmit(): void {
    this.submitting = true;
    this.error = '';
    this.success = '';
    
    const token = localStorage.getItem('token');
    this.http.post(`${environment.apiUrl}/payments`, this.formData, {
      headers: { Authorization: `Bearer ${token}` }
    }).subscribe({
      next: () => {
        this.success = 'Payment recorded successfully!';
        this.submitting = false;
        setTimeout(() => this.router.navigate(['/payments']), 1500);
      },
      error: (err) => {
        this.error = err.error?.message || 'Failed to record payment';
        this.submitting = false;
      }
    });
  }

  cancel(): void {
    this.router.navigate(['/payments']);
  }
}