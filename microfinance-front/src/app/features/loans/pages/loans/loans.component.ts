import { Component, OnInit, signal } from '@angular/core';
import { Router } from '@angular/router';
import { LoanService } from '../../services/loan.service';

@Component({
  selector: 'app-loans',
  template: `
    <div class="loans-page">
      <div class="page-header">
        <h2>Loans</h2>
        <button class="btn btn-outline-secondary" (click)="loadLoans()">
          <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
      </div>

      <div class="filters">
        <select [(ngModel)]="statusFilter" (change)="loadLoans()">
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="active">Active</option>
          <option value="completed">Completed</option>
          <option value="defaulted">Defaulted</option>
        </select>
        <input type="text" [(ngModel)]="searchQuery" placeholder="Search..." (input)="loadLoans()">
      </div>

      <div class="table-container" *ngIf="!loading()">
        <table class="data-table">
          <thead>
            <tr>
              <th>LAN</th>
              <th>Borrower</th>
              <th>Product</th>
              <th>Amount</th>
              <th>Monthly</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr *ngFor="let loan of loans()">
              <td>{{ loan.loan_number || 'N/A' }}</td>
              <td>{{ loan.borrowerName }}</td>
              <td>{{ loan.loan_product }}</td>
              <td>{{ loan.amount | number }}</td>
              <td>{{ loan.monthly_payment | number }}</td>
              <td>
                <span class="badge" [class]="loan.status">{{ loan.status }}</span>
              </td>
              <td>
                <button class="btn btn-sm btn-outline-primary" (click)="viewLoan(loan.id)">
                  <i class="bi bi-eye"></i>
                </button>
              </td>
            </tr>
            <tr *ngIf="loans().length === 0">
              <td colspan="7" class="text-center">No records found</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="loading" *ngIf="loading()">
        <div class="spinner-border text-primary"></div>
      </div>
    </div>
  `,
  styles: [`
    .loans-page { padding: 1rem; }
    .page-header { display: flex; justify-content: space-between; margin-bottom: 1rem; }
    .filters { display: flex; gap: 1rem; margin-bottom: 1rem; }
    .filters select, .filters input { padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; }
    .table-container { background: white; border-radius: 8px; overflow: hidden; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th, .data-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
    .data-table th { background: #f9fafb; font-weight: 600; }
    .badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; }
    .badge.pending { background: #fef3c7; }
    .badge.active { background: #d1fae5; }
    .badge.completed { background: #dbeafe; }
    .loading { text-align: center; padding: 3rem; }
    .text-center { text-align: center; }
  `]
})
export class LoansComponent implements OnInit {
  loans = signal<any[]>([]);
  loading = signal(true);
  statusFilter = '';
  searchQuery = '';

  constructor(private loanService: LoanService, private router: Router) {}

  ngOnInit(): void { this.loadLoans(); }

  loadLoans(): void {
    this.loading.set(true);
    const params: any = {};
    if (this.statusFilter) params.status = this.statusFilter;
    if (this.searchQuery) params.search = this.searchQuery;

    this.loanService.getLoans(params).subscribe({
      next: (response: any) => { this.loans.set(response.data || response); this.loading.set(false); },
      error: () => { this.loans.set([]); this.loading.set(false); }
    });
  }

  viewLoan(id: number): void { this.router.navigate(['/loans', id]); }
}