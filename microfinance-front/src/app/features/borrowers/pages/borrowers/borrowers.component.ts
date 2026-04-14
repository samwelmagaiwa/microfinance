import { Component, OnInit, signal } from '@angular/core';
import { Router } from '@angular/router';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { BorrowerService } from '../../services/borrower.service';
import { environment } from '../../../../core/environments/environment';

@Component({
  selector: 'app-borrowers',
  template: `
    <div class="borrowers-page">
      <div class="page-header">
        <h2>Borrowers</h2>
        <div class="header-actions">
          <button class="btn btn-outline-secondary" (click)="loadBorrowers()">
            <i class="bi bi-arrow-clockwise"></i> Refresh
          </button>
          <button class="btn btn-primary" (click)="newBorrower()">
            <i class="bi bi-plus-circle"></i> New Application
          </button>
        </div>
      </div>

      <div class="filters">
        <select [(ngModel)]="statusFilter" (change)="loadBorrowers()">
          <option value="">All Status</option>
          <option value="pending_loan_manager">Pending Loan Manager</option>
          <option value="pending_general_manager">Pending GM</option>
          <option value="pending_managing_director">Pending MD</option>
          <option value="approved">Approved</option>
          <option value="rejected">Rejected</option>
        </select>
        <input type="text" [(ngModel)]="searchQuery" placeholder="Search..." (input)="loadBorrowers()">
      </div>

      <div class="table-container" *ngIf="!loading()">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>NIDA</th>
              <th>Phone</th>
              <th>Product</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr *ngFor="let b of borrowers()">
              <td>{{ b.id }}</td>
              <td>{{ b.full_name }}</td>
              <td>{{ b.nida_number }}</td>
              <td>{{ b.phone }}</td>
              <td>{{ b.loan_product }}</td>
              <td>{{ b.loan_amount | number }}</td>
              <td>
                <span class="badge" [class]="getStatusClass(b.status)">
                  {{ b.status }}
                </span>
              </td>
              <td>
                <button class="btn btn-sm btn-outline-primary" (click)="viewBorrower(b.id)">
                  <i class="bi bi-eye"></i>
                </button>
              </td>
            </tr>
            <tr *ngIf="borrowers().length === 0">
              <td colspan="8" class="text-center">No records found</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="loading" *ngIf="loading()">
        <div class="spinner-border text-primary"></div>
        <p>Loading borrowers...</p>
      </div>
    </div>
  `,
  styles: [`
    .borrowers-page { padding: 1rem; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .header-actions { display: flex; gap: 0.5rem; }
    .filters { display: flex; gap: 1rem; margin-bottom: 1rem; }
    .filters select, .filters input { padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; }
    .table-container { background: white; border-radius: 8px; overflow: hidden; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th, .data-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
    .data-table th { background: #f9fafb; font-weight: 600; font-size: 0.875rem; }
    .badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
    .badge.pending_loan_manager { background: #fef3c7; color: #92400e; }
    .badge.pending_general_manager { background: #dbeafe; color: #1e40af; }
    .badge.pending_managing_director { background: #e0e7ff; color: #3730a3; }
    .badge.approved { background: #d1fae5; color: #065f46; }
    .badge.rejected { background: #fee2e2; color: #991b1b; }
    .loading { text-align: center; padding: 3rem; }
    .text-center { text-align: center; }
  `]
})
export class BorrowersComponent implements OnInit {
  borrowers = signal<any[]>([]);
  loading = signal(true);
  statusFilter = '';
  searchQuery = '';

  constructor(private borrowerService: BorrowerService, private router: Router) {}

  ngOnInit(): void {
    this.loadBorrowers();
  }

  loadBorrowers(): void {
    this.loading.set(true);
    const params: any = {};
    if (this.statusFilter) params.status = this.statusFilter;
    if (this.searchQuery) params.search = this.searchQuery;

    this.borrowerService.getBorrowers(params).subscribe({
      next: (response: any) => {
        this.borrowers.set(response.data || response);
        this.loading.set(false);
      },
      error: () => {
        this.borrowers.set([]);
        this.loading.set(false);
      }
    });
  }

  getStatusClass(status: string): string {
    return status || 'pending';
  }

  viewBorrower(id: number): void {
    this.router.navigate(['/borrowers', id]);
  }

  newBorrower(): void {
    this.router.navigate(['/borrowers/new']);
  }
}