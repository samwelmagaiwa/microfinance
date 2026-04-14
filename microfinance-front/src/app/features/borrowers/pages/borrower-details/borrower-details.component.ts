import { Component, OnInit, signal } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { BorrowerService } from '../../services/borrower.service';
import { LoanService } from '../../../loans/services/loan.service';
import { AuthService } from '../../../../core/services/auth.service';
import { Borrower } from '../../../../core/models/borrower.model';

@Component({
  selector: 'app-borrower-details',
  template: `
    <div class="borrower-details" *ngIf="borrower()">
      <!-- Header -->
      <header class="detail-header">
        <div>
          <h2>{{ borrower()?.full_name }}</h2>
          <p>NIDA: {{ borrower()?.nida_number }} | Phone: {{ borrower()?.phone }}</p>
        </div>
        <span class="badge" [class]="borrower()?.status">{{ borrower()?.status }}</span>
      </header>

      <!-- Loan Summary -->
      <div class="loan-summary" *ngIf="borrower()?.loan_amount">
        <div class="summary-item">
          <span class="label">Loan Product</span>
          <span class="value">{{ borrower()?.loan_product }}</span>
        </div>
        <div class="summary-item">
          <span class="label">Amount</span>
          <span class="value">{{ borrower()?.loan_amount | number }} TZS</span>
        </div>
        <div class="summary-item">
          <span class="label">Repayment Period</span>
          <span class="value">{{ borrower()?.repayment_period }} months</span>
        </div>
        <div class="summary-item">
          <span class="label">Interest Rate</span>
          <span class="value">{{ borrower()?.interest_rate }}%</span>
        </div>
      </div>

      <!-- Approval Workflow -->
      <div class="approval-workflow" *ngIf="canApprove()">
        <h3>Approval Workflow</h3>
        <div class="workflow-steps">
          <div class="workflow-step" [class.completed]="borrower()?.loan_officer_hash" [class.active]="isLoanOfficerStep()">
            <div class="step-icon"><i class="bi bi-person"></i></div>
            <div class="step-content">
              <span class="step-title">Loan Officer</span>
              <span class="step-date">{{ borrower()?.loan_officer_signed_at | date:'short' }}</span>
              <span class="step-status">{{ borrower()?.loan_officer_hash ? 'Approved' : 'Pending' }}</span>
            </div>
          </div>
          <div class="workflow-step" [class.completed]="borrower()?.loan_manager_hash" [class.active]="isLoanManagerStep()">
            <div class="step-icon"><i class="bi bi-shield-check"></i></div>
            <div class="step-content">
              <span class="step-title">Loan Manager</span>
              <span class="step-date">{{ borrower()?.loan_manager_signed_at | date:'short' }}</span>
              <span class="step-status">{{ borrower()?.loan_manager_hash ? 'Approved' : 'Pending' }}</span>
            </div>
          </div>
          <div class="workflow-step" [class.completed]="borrower()?.gm_hash" [class.active]="isGeneralManagerStep()">
            <div class="step-icon"><i class="bi bi-briefcase"></i></div>
            <div class="step-content">
              <span class="step-title">General Manager</span>
              <span class="step-date">{{ borrower()?.gm_signed_at | date:'short' }}</span>
              <span class="step-status">{{ borrower()?.gm_hash ? 'Approved' : 'Pending' }}</span>
            </div>
          </div>
          <div class="workflow-step" [class.completed]="borrower()?.md_hash" [class.active]="isManagingDirectorStep()">
            <div class="step-icon"><i class="bi bi-award"></i></div>
            <div class="step-content">
              <span class="step-title">Managing Director</span>
              <span class="step-date">{{ borrower()?.md_signed_at | date:'short' }}</span>
              <span class="step-status">{{ borrower()?.md_hash ? 'Approved' : 'Pending' }}</span>
            </div>
          </div>
        </div>

        <!-- Approve/Reject Actions -->
        <div class="approval-actions">
          <button class="btn btn-success" (click)="approve()" [disabled]="!canApproveAtCurrentStep()">
            <i class="bi bi-check-circle"></i> Approve
          </button>
          <button class="btn btn-danger" (click)="openRejectModal()" [disabled]="!canApproveAtCurrentStep()">
            <i class="bi bi-x-circle"></i> Reject
          </button>
        </div>
      </div>

      <!-- Rejection Note -->
      <div class="rejection-note" *ngIf="borrower()?.rejection_reason">
        <h4>Rejection Reason</h4>
        <p>{{ borrower()?.rejection_reason }}</p>
      </div>

      <!-- Back Button -->
      <div class="back-actions">
        <button class="btn btn-outline-secondary" (click)="goBack()">
          <i class="bi bi-arrow-left"></i> Back to List
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div class="loading" *ngIf="loading()">
      <div class="spinner-border text-primary"></div>
    </div>

    <!-- Reject Modal -->
    <div class="modal" *ngIf="showRejectModal()">
      <div class="modal-content">
        <h4>Reject Application</h4>
        <textarea [(ngModel)]="rejectReason" placeholder="Enter rejection reason..." rows="4"></textarea>
        <div class="modal-actions">
          <button class="btn btn-secondary" (click)="closeRejectModal()">Cancel</button>
          <button class="btn btn-danger" (click)="reject()">Confirm Reject</button>
        </div>
      </div>
    </div>
  `,
  styles: [`
    .borrower-details { max-width: 1200px; margin: 0 auto; padding: 1rem; }
    .detail-header { display: flex; justify-content: space-between; align-items: center; background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; }
    .loan-summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; }
    .summary-item { display: flex; flex-direction: column; }
    .summary-item .label { color: #6b7280; font-size: 0.875rem; }
    .summary-item .value { font-size: 1.25rem; font-weight: 700; }
    .approval-workflow { background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; }
    .workflow-steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin: 1rem 0; }
    .workflow-step { padding: 1rem; border: 2px solid #e5e7eb; border-radius: 8px; text-align: center; }
    .workflow-step.completed { border-color: #10b981; background: #d1fae5; }
    .workflow-step.active { border-color: #4f46e5; background: #e0e7ff; }
    .step-icon { font-size: 2rem; margin-bottom: 0.5rem; }
    .step-content { display: flex; flex-direction: column; }
    .step-title { font-weight: 600; }
    .step-date { font-size: 0.75rem; color: #6b7280; }
    .step-status { font-size: 0.75rem; font-weight: 600; }
    .approval-actions { display: flex; gap: 1rem; justify-content: center; margin-top: 1rem; }
    .rejection-note { background: #fee2e2; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; }
    .back-actions { margin-top: 1rem; }
    .loading { text-align: center; padding: 3rem; }
    .badge { padding: 0.5rem 1rem; border-radius: 4px; font-weight: 600; }
    .badge.pending_loan_manager { background: #fef3c7; }
    .badge.approved { background: #d1fae5; }
    .badge.rejected { background: #fee2e2; }
    .modal { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; }
    .modal-content { background: white; padding: 2rem; border-radius: 8px; width: 100%; max-width: 500px; }
    .modal-content textarea { width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 4px; margin: 1rem 0; }
    .modal-actions { display: flex; gap: 1rem; justify-content: flex-end; }
  `]
})
export class BorrowerDetailsComponent implements OnInit {
  borrower = signal<Borrower | null>(null);
  loading = signal(true);
  showRejectModal = signal(false);
  rejectReason = '';

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private borrowerService: BorrowerService,
    private loanService: LoanService,
    private authService: AuthService
  ) {}

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    if (id) {
      this.loadBorrower(id);
    } else {
      this.loading.set(false);
    }
  }

  loadBorrower(id: number): void {
    this.borrowerService.getBorrower(id).subscribe({
      next: (data) => { this.borrower.set(data); this.loading.set(false); },
      error: () => { this.loading.set(false); this.router.navigate(['/borrowers']); }
    });
  }

  canApprove(): boolean {
    const user = this.authService.getCurrentUser();
    if (!user) return false;
    return ['admin', 'managing_director', 'general_manager', 'loan_manager', 'loan_officer'].includes(user.role);
  }

  isLoanOfficerStep(): boolean { return this.borrower()?.status === 'pending_loan_manager'; }
  isLoanManagerStep(): boolean { return this.borrower()?.status === 'pending_general_manager'; }
  isGeneralManagerStep(): boolean { return this.borrower()?.status === 'pending_managing_director'; }
  isManagingDirectorStep(): boolean { return this.borrower()?.status === 'pending_managing_director'; }

  canApproveAtCurrentStep(): boolean {
    const user = this.authService.getCurrentUser();
    if (!user) return false;
    const status = this.borrower()?.status;
    if (status === 'pending_loan_manager' && user.role === 'loan_officer') return true;
    if (status === 'pending_general_manager' && user.role === 'general_manager') return true;
    if (status === 'pending_managing_director' && user.role === 'managing_director') return true;
    if (user.role === 'admin') return true;
    return false;
  }

  approve(): void {
    const id = this.borrower()?.id;
    if (!id) return;
    this.borrowerService.approveBorrower(id).subscribe({
      next: () => { this.loadBorrower(id); },
      error: (err) => alert(err.error?.message || 'Failed to approve')
    });
  }

  openRejectModal(): void { this.showRejectModal.set(true); }
  closeRejectModal(): void { this.showRejectModal.set(false); this.rejectReason = ''; }

  reject(): void {
    const id = this.borrower()?.id;
    if (!id || !this.rejectReason) return;
    this.borrowerService.rejectBorrower(id, this.rejectReason).subscribe({
      next: () => { this.closeRejectModal(); this.loadBorrower(id); },
      error: (err) => alert(err.error?.message || 'Failed to reject')
    });
  }

  goBack(): void { this.router.navigate(['/borrowers']); }
}