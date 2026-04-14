import { Component, OnInit, signal } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../../core/environments/environment';
import { Router } from '@angular/router';

@Component({
  selector: 'app-payments',
  template: `
    <div class="payments-page">
      <div class="page-header">
        <h2>Payments</h2>
        <button class="btn btn-primary" (click)="recordPayment()">
          <i class="bi bi-plus-circle"></i> Record Payment
        </button>
      </div>

      <div class="filters">
        <select [(ngModel)]="loanFilter" (change)="loadPayments()">
          <option value="">All Loans</option>
          <option *ngFor="let loan of loans()" [value]="loan.id">{{ loan.borrowerName }} - {{ loan.amount | number }}</option>
        </select>
        <input type="date" [(ngModel)]="dateFilter" (change)="loadPayments()">
        <button class="btn btn-sm btn-secondary" (click)="loadPayments()">
          <i class="bi bi-arrow-clockwise"></i>
        </button>
      </div>

      <div class="table-container" *ngIf="!loading()">
        <table class="data-table" *ngIf="payments().length > 0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Loan</th>
              <th>Amount</th>
              <th>Date</th>
              <th>Method</th>
              <th>Reference</th>
            </tr>
          </thead>
          <tbody>
            <tr *ngFor="let p of payments()">
              <td>{{ p.id }}</td>
              <td>{{ p.loan_id }}</td>
              <td>{{ p.amount | number }} TZS</td>
              <td>{{ p.payment_date | date:'dd/MM/yyyy' }}</td>
              <td>{{ p.payment_method }}</td>
              <td>{{ p.transaction_reference }}</td>
            </tr>
          </tbody>
        </table>
        <div class="empty-state" *ngIf="payments().length === 0">
          <p>No payments recorded yet</p>
        </div>
      </div>

      <div class="loading" *ngIf="loading()">
        <div class="spinner-border text-primary"></div>
      </div>
    </div>
  `,
  styles: [`
    .payments-page { padding: 1rem; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .filters { display: flex; gap: 1rem; margin-bottom: 1rem; }
    .filters select, .filters input { padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; }
    .table-container { background: white; border-radius: 8px; overflow: hidden; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th, .data-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
    .data-table th { background: #f9fafb; font-weight: 600; }
    .empty-state { text-align: center; padding: 3rem; color: #6b7280; }
    .loading { text-align: center; padding: 3rem; }
  `]
})
export class PaymentsComponent implements OnInit {
  payments = signal<any[]>([]);
  loans = signal<any[]>([]);
  loading = signal(true);
  loanFilter = '';
  dateFilter = '';

  constructor(private http: HttpClient, private router: Router) {}

  ngOnInit(): void {
    this.loadPayments();
    this.loadLoans();
  }

  loadPayments(): void {
    this.loading.set(true);
    const token = localStorage.getItem('token');
    this.http.get<any>(`${environment.apiUrl}/payments`, {
      headers: { Authorization: `Bearer ${token}` }
    }).subscribe({
      next: (res) => {
        this.payments.set(res.data || res);
        this.loading.set(false);
      },
      error: () => { this.payments.set([]); this.loading.set(false); }
    });
  }

  loadLoans(): void {
    const token = localStorage.getItem('token');
    this.http.get<any>(`${environment.apiUrl}/loans`, {
      headers: { Authorization: `Bearer ${token}` }
    }).subscribe({
      next: (res) => { this.loans.set(res.data || res); }
    });
  }

  recordPayment(): void {
    this.router.navigate(['/payments/record']);
  }
}