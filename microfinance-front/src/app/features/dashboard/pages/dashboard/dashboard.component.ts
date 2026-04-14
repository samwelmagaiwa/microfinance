import { Component, OnInit } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Router } from '@angular/router';
import { environment } from '../../../../core/environments/environment';
import { AuthService } from '../../../../core/services/auth.service';

interface DashboardData {
  totalBorrowers: number;
  pendingBorrowers: number;
  activeLoans: number;
  totalDisbursed: number;
  totalCollected: number;
  defaultRate: number;
}

@Component({
  selector: 'app-dashboard',
  template: `
    <div class="dashboard">
      <h2>Dashboard</h2>
      
      <div class="stats-grid" *ngIf="data">
        <div class="stat-card">
          <div class="stat-label">Total Borrowers</div>
          <div class="stat-value">{{ data.totalBorrowers }}</div>
        </div>
        <div class="stat-card warning">
          <div class="stat-label">Pending Applications</div>
          <div class="stat-value">{{ data.pendingBorrowers }}</div>
        </div>
        <div class="stat-card success">
          <div class="stat-label">Active Loans</div>
          <div class="stat-value">{{ data.activeLoans }}</div>
        </div>
        <div class="stat-card info">
          <div class="stat-label">Total Disbursed</div>
          <div class="stat-value">{{ data.totalDisbursed | number:'1.0-0' }} TZS</div>
        </div>
      </div>

      <div class="quick-actions">
        <h3>Quick Actions</h3>
        <div class="action-buttons">
          <button class="btn btn-primary" (click)="newApplication()">
            <i class="bi bi-plus-circle"></i> New Application
          </button>
          <button class="btn btn-outline-primary" (click)="viewBorrowers()">
            <i class="bi bi-people"></i> View Borrowers
          </button>
          <button class="btn btn-outline-primary" (click)="viewLoans()">
            <i class="bi bi-cash"></i> View Loans
          </button>
        </div>
      </div>
    </div>
  `,
  styles: [`
    .dashboard { padding: 1rem; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .stat-card {
      background: white;
      padding: 1.5rem;
      border-radius: 8px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .stat-card.warning { border-left: 4px solid #f59e0b; }
    .stat-card.success { border-left: 4px solid #10b981; }
    .stat-card.info { border-left: 4px solid #3b82f6; }
    .stat-label { color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem; }
    .stat-value { font-size: 1.75rem; font-weight: 700; color: #1f2937; }
    .quick-actions { background: white; padding: 1.5rem; border-radius: 8px; }
    .quick-actions h3 { margin-bottom: 1rem; }
    .action-buttons { display: flex; gap: 1rem; flex-wrap: wrap; }
  `]
})
export class DashboardComponent implements OnInit {
  data: DashboardData | null = null;

  constructor(
    private http: HttpClient,
    private authService: AuthService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.loadDashboard();
  }

  loadDashboard(): void {
    const token = localStorage.getItem('token');
    this.http.get<any>(`${environment.apiUrl}/dashboard`, {
      headers: { Authorization: `Bearer ${token}` }
    }).subscribe({
      next: (data) => { this.data = data; },
      error: () => { this.data = { totalBorrowers: 0, pendingBorrowers: 0, activeLoans: 0, totalDisbursed: 0, totalCollected: 0, defaultRate: 0 }; }
    });
  }

  newApplication(): void {
    this.router.navigate(['/borrowers/new']);
  }

  viewBorrowers(): void {
    this.router.navigate(['/borrowers']);
  }

  viewLoans(): void {
    this.router.navigate(['/loans']);
  }
}