import { Component, OnInit, signal } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../../core/environments/environment';
import { AuthService } from '../../../../core/services/auth.service';

@Component({
  selector: 'app-pending-approvals',
  template: `
    <div class="approvals-page">
      <h2>Pending Approvals</h2>
      
      <div class="tabs">
        <button [class.active]="activeTab === 'borrowers'" (click)="activeTab = 'borrowers'">
          Borrowers <span class="count">{{ borrowers().length }}</span>
        </button>
        <button [class.active]="activeTab === 'loans'" (click)="activeTab = 'loans'">
          Loans <span class="count">{{ loans().length }}</span>
        </button>
      </div>

      <!-- Borrowers Pending -->
      <div class="tab-content" *ngIf="activeTab === 'borrowers'">
        <div class="table-container" *ngIf="!loading()">
          <table class="data-table" *ngIf="borrowers().length > 0">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Product</th>
                <th>Amount</th>
                <th>Current Stage</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr *ngFor="let b of borrowers()">
                <td>{{ b.id }}</td>
                <td>{{ b.full_name }}</td>
                <td>{{ b.loan_product }}</td>
                <td>{{ b.loan_amount | number }} TZS</td>
                <td>
                  <span class="status-badge" [class]="getStageClass(b.status)">
                    {{ getStageLabel(b.status) }}
                  </span>
                </td>
                <td>
                  <button class="btn btn-sm btn-primary" (click)="viewBorrower(b.id)">Review</button>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="empty-state" *ngIf="borrowers().length === 0">
            <p>No pending borrower applications</p>
          </div>
        </div>
      </div>

      <!-- Loans Pending -->
      <div class="tab-content" *ngIf="activeTab === 'loans'">
        <div class="table-container" *ngIf="!loading()">
          <table class="data-table" *ngIf="loans().length > 0">
            <thead>
              <tr>
                <th>ID</th>
                <th>LAN</th>
                <th>Borrower</th>
                <th>Amount</th>
                <th>Stage</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr *ngFor="let l of loans()">
                <td>{{ l.id }}</td>
                <td>{{ l.loan_number || 'N/A' }}</td>
                <td>{{ l.borrowerName }}</td>
                <td>{{ l.amount | number }} TZS</td>
                <td>
                  <span class="status-badge pending">{{ getStageLabel(l.current_approval_step) }}</span>
                </td>
                <td>
                  <button class="btn btn-sm btn-primary" (click)="viewLoan(l.id)">Review</button>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="empty-state" *ngIf="loans().length === 0">
            <p>No pending loan approvals</p>
          </div>
        </div>
      </div>

      <div class="loading" *ngIf="loading()">
        <div class="spinner-border text-primary"></div>
      </div>
    </div>
  `,
  styles: [`
    .approvals-page { padding: 1rem; }
    .tabs { display: flex; gap: 0.5rem; margin-bottom: 1rem; }
    .tabs button {
      padding: 0.75rem 1.5rem;
      border: none;
      background: white;
      border-radius: 6px 6px 0 0;
      cursor: pointer;
      font-weight: 600;
    }
    .tabs button.active {
      background: white;
      color: #4f46e5;
      border-bottom: 2px solid #4f46e5;
    }
    .tabs .count {
      background: #ef4444;
      color: white;
      padding: 0.125rem 0.5rem;
      border-radius: 999px;
      font-size: 0.75rem;
      margin-left: 0.5rem;
    }
    .tab-content { background: white; border-radius: 0 6px 6px 6px; padding: 1rem; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th, .data-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
    .data-table th { background: #f9fafb; font-weight: 600; }
    .status-badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
    .status-badge.pending { background: #fef3c7; color: #92400e; }
    .status-badge.loan_manager { background: #dbeafe; color: #1e40af; }
    .status-badge.general_manager { background: #e0e7ff; color: #3730a3; }
    .status-badge.managing_director { background: #fce7f3; color: #9d174d; }
    .empty-state { text-align: center; padding: 3rem; color: #6b7280; }
    .loading { text-align: center; padding: 3rem; }
  `]
})
export class PendingApprovalsComponent implements OnInit {
  borrowers = signal<any[]>([]);
  loans = signal<any[]>([]);
  loading = signal(true);
  activeTab: 'borrowers' | 'loans' = 'borrowers';

  constructor(private http: HttpClient, private authService: AuthService) {}

  ngOnInit(): void {
    this.loadPending();
  }

  loadPending(): void {
    this.loading.set(true);
    const token = localStorage.getItem('token');
    const headers = { Authorization: `Bearer ${token}` };

    // Load pending borrowers (status starts with pending_)
    this.http.get<any>(`${environment.apiUrl}/borrowers`, { headers }).subscribe({
      next: (res) => {
        const all = res.data || res;
        const pending = all.filter((b: any) => b.status?.startsWith('pending_'));
        this.borrowers.set(pending);
      }
    });

    // Load pending loans
    this.http.get<any>(`${environment.apiUrl}/loans`, { headers }).subscribe({
      next: (res) => {
        const all = res.data || res;
        const pending = all.filter((l: any) => l.approval_status?.startsWith('pending_'));
        this.loans.set(pending);
        this.loading.set(false);
      }
    });
  }

  getStageLabel(status: string): string {
    const labels: Record<string, string> = {
      'pending_loan_manager': 'Loan Officer',
      'pending_general_manager': 'Loan Manager',
      'pending_managing_director': 'General Manager',
      'pending': 'Pending',
      'loan_officer': 'Loan Officer',
      'loan_manager': 'Loan Manager',
      'general_manager': 'General Manager',
      'managing_director': 'Managing Director'
    };
    return labels[status || ''] || status || '';
  }

  getStageClass(status: string): string {
    if (status?.includes('loan_manager')) return 'loan_manager';
    if (status?.includes('general_manager')) return 'general_manager';
    if (status?.includes('managing_director')) return 'managing_director';
    return 'pending';
  }

  viewBorrower(id: number): void {
    window.location.href = `/#/borrowers/${id}`;
  }

  viewLoan(id: number): void {
    window.location.href = `/#/loans/${id}`;
  }
}