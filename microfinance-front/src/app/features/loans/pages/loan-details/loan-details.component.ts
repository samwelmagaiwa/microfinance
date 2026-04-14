import { Component, OnInit, signal } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { LoanService } from '../../services/loan.service';
import { AuthService } from '../../../../core/services/auth.service';

@Component({
  selector: 'app-loan-details',
  template: `
    <div class="loan-details" *ngIf="loan()">
      <header class="detail-header">
        <div>
          <h2>Loan #{{ loan()?.id }}</h2>
          <p>Borrower: {{ loan()?.borrowerName }}</p>
        </div>
        <span class="badge" [class]="loan()?.status">{{ loan()?.status }}</span>
      </header>

      <div class="metrics-row">
        <div class="metric-box">
          <span class="m-label">Principal</span>
          <div class="m-value">{{ loan()?.amount | number }} TZS</div>
        </div>
        <div class="metric-box">
          <span class="m-label">Monthly Payment</span>
          <div class="m-value">{{ loan()?.monthly_payment | number }} TZS/mo</div>
        </div>
        <div class="metric-box">
          <span class="m-label">Total Payment</span>
          <div class="m-value">{{ loan()?.total_payment | number }} TZS</div>
        </div>
        <div class="metric-box">
          <span class="m-label">Duration</span>
          <div class="m-value">{{ loan()?.duration_months }} Months</div>
        </div>
      </div>

      <div class="approval-workflow">
        <h3>Approval Status</h3>
        <div class="workflow-steps">
          <div class="workflow-step" [class.completed]="loan()?.loan_officer_hash">
            <i class="bi bi-person"></i><span>Loan Officer</span>
          </div>
          <div class="workflow-step" [class.completed]="loan()?.loan_manager_hash">
            <i class="bi bi-shield-check"></i><span>Loan Manager</span>
          </div>
          <div class="workflow-step" [class.completed]="loan()?.general_manager_hash">
            <i class="bi bi-briefcase"></i><span>General Manager</span>
          </div>
          <div class="workflow-step" [class.completed]="loan()?.managing_director_hash">
            <i class="bi bi-award"></i><span>Managing Director</span>
          </div>
        </div>
      </div>

      <div class="back-actions">
        <button class="btn btn-outline-secondary" (click)="goBack()">
          <i class="bi bi-arrow-left"></i> Back
        </button>
      </div>
    </div>

    <div class="loading" *ngIf="loading()">
      <div class="spinner-border text-primary"></div>
    </div>
  `,
  styles: [`
    .loan-details { max-width: 1000px; margin: 0 auto; padding: 1rem; }
    .detail-header { display: flex; justify-content: space-between; align-items: center; background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; }
    .metrics-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1rem; }
    .metric-box { background: white; padding: 1.5rem; border-radius: 8px; text-align: center; }
    .m-label { color: #6b7280; font-size: 0.875rem; }
    .m-value { font-size: 1.5rem; font-weight: 700; }
    .approval-workflow { background: white; padding: 1.5rem; border-radius: 8px; }
    .workflow-steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-top: 1rem; }
    .workflow-step { padding: 1rem; text-align: center; border: 2px solid #e5e7eb; border-radius: 8px; }
    .workflow-step.completed { border-color: #10b981; background: #d1fae5; }
    .badge { padding: 0.5rem 1rem; border-radius: 4px; }
    .badge.active { background: #d1fae5; }
    .badge.pending { background: #fef3c7; }
    .loading { text-align: center; padding: 3rem; }
  `]
})
export class LoanDetailsComponent implements OnInit {
  loan = signal<any>(null);
  loading = signal(true);

  constructor(private route: ActivatedRoute, private router: Router, private loanService: LoanService) {}

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    if (id) this.loadLoan(id);
  }

  loadLoan(id: number): void {
    this.loanService.getLoan(id).subscribe({
      next: (data) => { this.loan.set(data); this.loading.set(false); },
      error: () => { this.loading.set(false); this.router.navigate(['/loans']); }
    });
  }

  goBack(): void { this.router.navigate(['/loans']); }
}