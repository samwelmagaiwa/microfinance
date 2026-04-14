import { Component, OnInit, signal } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../../core/environments/environment';

@Component({
  selector: 'app-audit-logs',
  template: `
    <div class="audit-page">
      <h2>Audit Logs</h2>
      
      <div class="filters">
        <select [(ngModel)]="actionFilter" (change)="loadLogs()">
          <option value="">All Actions</option>
          <option value="create">Created</option>
          <option value="approve">Approved</option>
          <option value="reject">Rejected</option>
          <option value="disburse">Disbursed</option>
          <option value="update">Updated</option>
        </select>
        <input type="date" [(ngModel)]="dateFilter" (change)="loadLogs()">
        <button class="btn btn-sm btn-secondary" (click)="loadLogs()">
          <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
      </div>

      <div class="table-container" *ngIf="!loading()">
        <table class="data-table" *ngIf="logs().length > 0">
          <thead>
            <tr>
              <th>Date</th>
              <th>User</th>
              <th>Action</th>
              <th>Entity</th>
              <th>Details</th>
            </tr>
          </thead>
          <tbody>
            <tr *ngFor="let log of logs()">
              <td>{{ log.created_at | date:'dd/MM/yyyy HH:mm' }}</td>
              <td>{{ log.user_name }}</td>
              <td>
                <span class="action-badge" [class]="getActionClass(log.action)">
                  {{ log.action }}
                </span>
              </td>
              <td>{{ log.entity_type }} #{{ log.entity_id }}</td>
              <td>{{ log.description }}</td>
            </tr>
          </tbody>
        </table>
        <div class="empty-state" *ngIf="logs().length === 0">
          <p>No audit logs found</p>
        </div>
      </div>

      <div class="loading" *ngIf="loading()">
        <div class="spinner-border text-primary"></div>
      </div>
    </div>
  `,
  styles: [`
    .audit-page { padding: 1rem; }
    .filters { display: flex; gap: 1rem; margin-bottom: 1rem; }
    .filters select, .filters input { padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; }
    .table-container { background: white; border-radius: 8px; overflow: hidden; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th, .data-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
    .data-table th { background: #f9fafb; font-weight: 600; font-size: 0.875rem; }
    .action-badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
    .action-badge.create { background: #d1fae5; color: #065f46; }
    .action-badge.approve { background: #dbeafe; color: #1e40af; }
    .action-badge.reject { background: #fee2e2; color: #991b1b; }
    .action-badge.disburse { background: #fce7f3; color: #9d174d; }
    .action-badge.update { background: #fef3c7; color: #92400e; }
    .empty-state { text-align: center; padding: 3rem; color: #6b7280; }
    .loading { text-align: center; padding: 3rem; }
  `]
})
export class AuditLogsComponent implements OnInit {
  logs = signal<any[]>([]);
  loading = signal(true);
  actionFilter = '';
  dateFilter = '';

  constructor(private http: HttpClient) {}

  ngOnInit(): void {
    this.loadLogs();
  }

  loadLogs(): void {
    this.loading.set(true);
    const token = localStorage.getItem('token');
    const headers = { Authorization: `Bearer ${token}` };
    const params: any = {};
    if (this.actionFilter) params.action = this.actionFilter;
    if (this.dateFilter) params.date = this.dateFilter;

    this.http.get<any>(`${environment.apiUrl}/audit-logs`, { headers, params }).subscribe({
      next: (res) => {
        this.logs.set(res.data || res);
        this.loading.set(false);
      },
      error: () => {
        this.logs.set([]);
        this.loading.set(false);
      }
    });
  }

  getActionClass(action: string): string {
    return action?.toLowerCase() || '';
  }
}