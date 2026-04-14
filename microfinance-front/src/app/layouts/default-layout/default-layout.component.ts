import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-default-layout',
  template: `
    <div class="app-container">
      <aside class="sidebar">
        <div class="brand"><h4>Orethani MFB</h4></div>
        
        <nav class="nav-menu">
          <a routerLink="/dashboard" routerLinkActive="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
          
          <!-- Borrowers -->
          <a *ngIf="canViewBorrowers()" routerLink="/borrowers" routerLinkActive="active">
            <i class="bi bi-people"></i> Borrowers
          </a>
          
          <!-- New Application Dropdown -->
          <div *ngIf="canCreate()" class="elite-dropdown" [class.open]="dropdownOpen['newApp']">
            <button class="elite-dropdown-toggle" (click)="toggleDropdown('newApp')">
              <span class="label-group">
                <i class="bi bi-plus-circle-fill"></i>
                <span>New Application</span>
              </span>
              <i class="bi bi-chevron-down caret"></i>
            </button>
            <div class="elite-dropdown-menu" [class.show]="dropdownOpen['newApp']">
              <a routerLink="/borrowers/new" [queryParams]="{type:'personal'}" (click)="closeDropdowns()" class="elite-menu-item">
                <i class="bi bi-person-bounding-box"></i>
                <div class="item-text">
                  <span class="main">Personal Loan</span>
                  <span class="sub">Mkopo Binafsi</span>
                </div>
              </a>
              <a routerLink="/borrowers/new" [queryParams]="{type:'employee'}" (click)="closeDropdowns()" class="elite-menu-item">
                <i class="bi bi-briefcase-fill"></i>
                <div class="item-text">
                  <span class="main">Employee Loan</span>
                  <span class="sub">Mkopo wa Ajira</span>
                </div>
              </a>
              <a routerLink="/borrowers/new-group" (click)="closeDropdowns()" class="elite-menu-item">
                <i class="bi bi-people-fill"></i>
                <div class="item-text">
                  <span class="main">Group Loan</span>
                  <span class="sub">Mkopo wa Kikundi</span>
                </div>
              </a>
            </div>
          </div>
          
          <!-- Rejected -->
          <a *ngIf="canCreate()" routerLink="/borrowers/rejected" routerLinkActive="active" class="sub-item">
            <i class="bi bi-x-circle"></i> Rejected Applications
          </a>
          
          <!-- Loans -->
          <a *ngIf="canViewLoans()" routerLink="/loans" routerLinkActive="active">
            <i class="bi bi-cash"></i> Loans
          </a>
          
          <!-- Payments -->
          <a *ngIf="canViewPayments()" routerLink="/payments" routerLinkActive="active">
            <i class="bi bi-credit-card"></i> Payments
          </a>
          <a *ngIf="canViewPayments()" routerLink="/payments/record" routerLinkActive="active" class="sub-item">
            <i class="bi bi-plus-square"></i> Record Repayment
          </a>
          
          <!-- Approvals -->
          <a *ngIf="canApprove()" routerLink="/approvals" routerLinkActive="active">
            <i class="bi bi-check2-square"></i> Pending Approvals
          </a>
          
          <!-- Reports -->
          <a *ngIf="canViewReports()" routerLink="/reports" routerLinkActive="active">
            <i class="bi bi-bar-chart"></i> Reports
          </a>
          <a *ngIf="canViewReports()" routerLink="/reports" [queryParams]="{tab:'financial'}" routerLinkActive="active" class="sub-item">
            <i class="bi bi-graph-up"></i> Financial Reports
          </a>
          <a *ngIf="canViewReports()" routerLink="/reports" [queryParams]="{tab:'performance'}" routerLinkActive="active" class="sub-item">
            <i class="bi bi-pie-chart"></i> Performance Reports
          </a>
          
          <!-- Audit -->
          <a *ngIf="canViewAudit()" routerLink="/audit-logs" routerLinkActive="active">
            <i class="bi bi-journal-text"></i> Audit Logs
          </a>
          
          <!-- Calculator -->
          <a *ngIf="canUseCalculator()" routerLink="/calculator" routerLinkActive="active">
            <i class="bi bi-calculator"></i> Loan Calculator
          </a>
        </nav>
        
        <div class="role-indicator">
          <span>{{ getRoleLabel(currentUser?.role) }}</span>
        </div>
      </aside>

      <main class="main-content">
        <header class="top-header">
          <div class="header-title"><h3>Orethani Microfinance Ltd</h3></div>
          <div class="user-menu">
            <div class="user-info">
              <span class="user-name">{{ currentUser?.name }}</span>
              <span class="user-role">{{ getRoleLabel(currentUser?.role) }}</span>
            </div>
            <button class="btn btn-sm btn-outline-danger" (click)="logout()">
              <i class="bi bi-box-arrow-right"></i>
            </button>
          </div>
        </header>
        <div class="content"><router-outlet></router-outlet></div>
      </main>
    </div>
  `,
  styles: [`
    .app-container { display: flex; min-height: 100vh; }
    .sidebar { width: 260px; background: #1a1f36; color: white; padding: 1rem; display: flex; flex-direction: column; }
    .brand { padding: 1rem 0.5rem; border-bottom: 1px solid #2d325a; margin-bottom: 1rem; }
    .brand h4 { margin: 0; font-weight: 700; font-size: 1.25rem; }
    .nav-menu { flex: 1; }
    .nav-menu a { display: flex; align-items: center; padding: 0.75rem 1rem; color: #a3a6b8; text-decoration: none; border-radius: 6px; margin-bottom: 0.25rem; }
    .nav-menu a:hover, .nav-menu a.active { background: #3b4261; color: white; }
    .nav-menu a.active { border-left: 3px solid #4f46e5; }
    .nav-menu a.sub-item { padding-left: 2rem; font-size: 0.875rem; }
    
    /* Elite Sidebar Dropdown */
    .elite-dropdown { margin: 0.5rem 0; border-radius: 8px; transition: all 0.3s ease; }
    .elite-dropdown.open { background: #2d325a; padding-bottom: 0.5rem; }
    
    .elite-dropdown-toggle { 
      width: 100%; display: flex; align-items: center; justify-content: space-between;
      padding: 0.85rem 1rem; color: #fff; background: #3b4261; border: none; 
      border-radius: 8px; cursor: pointer; font-size: 0.95rem; font-weight: 600;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.2s;
    }
    .elite-dropdown-toggle:hover { background: #4f46e5; transform: translateY(-1px); }
    .elite-dropdown-toggle .label-group { display: flex; align-items: center; gap: 0.75rem; }
    .elite-dropdown-toggle .label-group i { font-size: 1.1rem; color: #a5b4fc; }
    .elite-dropdown-toggle .caret { font-size: 0.8rem; transition: transform 0.3s ease; }
    .elite-dropdown.open .caret { transform: rotate(180deg); }
    
    .elite-dropdown-menu { 
      max-height: 0; overflow: hidden; opacity: 0;
      display: flex; flex-direction: column; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .elite-dropdown-menu.show { max-height: 300px; opacity: 1; padding: 0.5rem; }
    
    .elite-menu-item {
      display: flex; align-items: center; gap: 0.85rem; padding: 0.75rem 1rem;
      color: #cbd5e1; text-decoration: none; border-radius: 6px; margin-top: 0.25rem;
      transition: all 0.2s;
    }
    .elite-menu-item:hover { background: #434771; color: white; padding-left: 1.25rem; }
    .elite-menu-item i { font-size: 1rem; color: #94a3b8; }
    .elite-menu-item .item-text { display: flex; flex-direction: column; }
    .elite-menu-item .main { font-size: 0.875rem; font-weight: 600; }
    .elite-menu-item .sub { font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.025em; }
    .elite-menu-item:hover .sub { color: #a5b4fc; }
    
    .role-indicator { padding: 0.75rem; background: #2d325a; border-radius: 6px; text-align: center; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; margin-top: auto; }
    .main-content { flex: 1; display: flex; flex-direction: column; }
    .top-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem; background: white; border-bottom: 1px solid #e5e7eb; }
    .top-header h3 { margin: 0; color: #1f2937; }
    .user-menu { display: flex; align-items: center; gap: 1rem; }
    .user-info { display: flex; flex-direction: column; text-align: right; }
    .user-name { font-weight: 600; color: #1f2937; }
    .user-role { font-size: 0.75rem; color: #6b7280; }
    .content { padding: 1.5rem; background: #f3f4f6; flex: 1; }
  `]
})
export class DefaultLayoutComponent implements OnInit {
  currentUser: any;
  dropdownOpen: any = {};

  constructor(private authService: AuthService, private router: Router) {}

  ngOnInit(): void { this.currentUser = this.authService.getCurrentUser(); }

  toggleDropdown(key: string): void { this.dropdownOpen[key] = !this.dropdownOpen[key]; }
  closeDropdowns(): void { this.dropdownOpen = {}; }

  canViewBorrowers(): boolean { return this.authService.hasRole('admin', 'managing_director', 'general_manager', 'loan_manager', 'loan_officer', 'secretary'); }
  canCreate(): boolean { return this.authService.hasRole('admin', 'managing_director', 'general_manager', 'loan_manager', 'loan_officer'); }
  canViewLoans(): boolean { return this.authService.hasRole('admin', 'managing_director', 'general_manager', 'loan_manager', 'loan_officer', 'secretary'); }
  canViewPayments(): boolean { return this.authService.hasRole('admin', 'managing_director', 'general_manager', 'loan_manager', 'loan_officer', 'secretary'); }
  canApprove(): boolean { return this.authService.hasRole('admin', 'managing_director', 'general_manager', 'loan_manager'); }
  canViewReports(): boolean { return this.authService.hasRole('admin', 'managing_director', 'general_manager', 'loan_officer'); }
  canViewAudit(): boolean { return this.authService.hasRole('admin', 'managing_director', 'general_manager', 'loan_officer'); }
  canUseCalculator(): boolean { return this.authService.hasRole('admin', 'managing_director', 'general_manager', 'loan_manager', 'loan_officer', 'secretary'); }

  getRoleLabel(role: string | undefined): string {
    const labels: Record<string, string> = {'admin': 'Administrator', 'managing_director': 'Managing Director', 'general_manager': 'General Manager', 'loan_manager': 'Loan Manager', 'loan_officer': 'Loan Officer', 'secretary': 'Secretary'};
    return role ? labels[role] || role : '';
  }

  logout(): void { this.authService.logout(); this.router.navigate(['/login']); }
}