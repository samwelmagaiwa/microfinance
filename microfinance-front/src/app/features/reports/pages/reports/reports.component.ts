import { Component, OnInit, signal } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { ActivatedRoute } from '@angular/router';
import { environment } from '../../../../core/environments/environment';

@Component({
  selector: 'app-reports',
  template: `
    <div class="reports-page">
      <h2>Reports</h2>
      
      <div class="tabs">
        <button [class.active]="activeTab === 'financial'" (click)="activeTab = 'financial'">
          <i class="bi bi-graph-up"></i> Financial Reports
        </button>
        <button [class.active]="activeTab === 'performance'" (click)="activeTab = 'performance'">
          <i class="bi bi-pie-chart"></i> Performance Reports
        </button>
      </div>

      <!-- Financial Reports -->
      <div class="tab-content" *ngIf="activeTab === 'financial'">
        <div class="filters">
          <input type="date" [(ngModel)]="startDate" placeholder="Start Date">
          <input type="date" [(ngModel)]="endDate" placeholder="End Date">
          <button class="btn btn-primary" (click)="loadFinancialReport()">Generate</button>
        </div>
        
        <div class="report-cards" *ngIf="!loading()">
          <div class="report-card">
            <div class="card-label">Total Disbursed</div>
            <div class="card-value">{{ report.totalDisbursed | number }} TZS</div>
          </div>
          <div class="report-card">
            <div class="card-label">Total Collected</div>
            <div class="card-value">{{ report.totalCollected | number }} TZS</div>
          </div>
          <div class="report-card">
            <div class="card-label">Total Outstanding</div>
            <div class="card-value">{{ report.totalOutstanding | number }} TZS</div>
          </div>
          <div class="report-card">
            <div class="card-label">Interest Earned</div>
            <div class="card-value">{{ report.interestEarned | number }} TZS</div>
          </div>
        </div>
      </div>

      <!-- Performance Reports -->
      <div class="tab-content" *ngIf="activeTab === 'performance'">
        <div class="filters">
          <input type="date" [(ngModel)]="startDate" placeholder="Start Date">
          <input type="date" [(ngModel)]="endDate" placeholder="End Date">
          <button class="btn btn-primary" (click)="loadPerformanceReport()">Generate</button>
        </div>
        
        <div class="report-cards" *ngIf="!loading()">
          <div class="report-card">
            <div class="card-label">Total Applications</div>
            <div class="card-value">{{ report.totalApplications }}</div>
          </div>
          <div class="report-card">
            <div class="card-label">Approved</div>
            <div class="card-value success">{{ report.approved }}</div>
          </div>
          <div class="report-card">
            <div class="card-label">Rejected</div>
            <div class="card-value danger">{{ report.rejected }}</div>
          </div>
          <div class="report-card">
            <div class="card-label">Default Rate</div>
            <div class="card-value">{{ report.defaultRate }}%</div>
          </div>
        </div>
      </div>

      <div class="loading" *ngIf="loading()">
        <div class="spinner-border text-primary"></div>
      </div>
    </div>
  `,
  styles: [`
    .reports-page { padding: 1rem; }
    .tabs { display: flex; gap: 0.5rem; margin-bottom: 1rem; }
    .tabs button {
      display: flex; align-items: center; gap: 0.5rem;
      padding: 0.75rem 1.5rem; border: none; background: white; border-radius: 6px 6px 0 0;
      cursor: pointer; font-weight: 600;
    }
    .tabs button.active { background: white; color: #4f46e5; border-bottom: 2px solid #4f46e5; }
    .tab-content { background: white; padding: 1.5rem; border-radius: 0 6px 6px; }
    .filters { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
    .filters input { padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; }
    
    .report-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
    .report-card { background: #f9fafb; padding: 1.5rem; border-radius: 8px; text-align: center; }
    .card-label { font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem; }
    .card-value { font-size: 1.5rem; font-weight: 700; color: #1f2937; }
    .card-value.success { color: #10b981; }
    .card-value.danger { color: #ef4444; }
    
    .loading { text-align: center; padding: 3rem; }
  `]
})
export class ReportsComponent implements OnInit {
  activeTab = 'financial';
  startDate = '';
  endDate = '';
  loading = signal(false);
  report: any = {
    totalDisbursed: 0, totalCollected: 0, totalOutstanding: 0, interestEarned: 0,
    totalApplications: 0, approved: 0, rejected: 0, defaultRate: 0
  };

  constructor(private http: HttpClient, private route: ActivatedRoute) {}

  ngOnInit(): void {
    this.route.queryParams.subscribe(params => {
      if (params['tab'] === 'financial' || params['tab'] === 'performance') {
        this.activeTab = params['tab'];
      }
    });
    this.loadFinancialReport();
  }

  loadFinancialReport(): void {
    this.loading.set(true);
    const token = localStorage.getItem('token');
    const params: any = {};
    if (this.startDate) params.start = this.startDate;
    if (this.endDate) params.end = this.endDate;

    this.http.get<any>(`${environment.apiUrl}/reports/financial`, {
      headers: { Authorization: `Bearer ${token}` }, params
    }).subscribe({
      next: (res) => { this.report = { ...this.report, ...res }; this.loading.set(false); },
      error: () => { this.loading.set(false); }
    });
  }

  loadPerformanceReport(): void {
    this.loading.set(true);
    const token = localStorage.getItem('token');
    const params: any = {};
    if (this.startDate) params.start = this.startDate;
    if (this.endDate) params.end = this.endDate;

    this.http.get<any>(`${environment.apiUrl}/reports/performance`, {
      headers: { Authorization: `Bearer ${token}` }, params
    }).subscribe({
      next: (res) => { this.report = { ...this.report, ...res }; this.loading.set(false); },
      error: () => { this.loading.set(false); }
    });
  }
}