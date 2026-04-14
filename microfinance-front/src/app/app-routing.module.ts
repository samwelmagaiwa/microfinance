import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { LoginComponent } from './features/auth/pages/login/login.component';
import { DashboardComponent } from './features/dashboard/pages/dashboard/dashboard.component';
import { BorrowersComponent } from './features/borrowers/pages/borrowers/borrowers.component';
import { BorrowerDetailsComponent } from './features/borrowers/pages/borrower-details/borrower-details.component';
import { MkopoBinafsiComponent } from './features/borrowers/pages/mkopo-binafsi/mkopo-binafsi.component';
import { MkopoKikundiComponent } from './features/borrowers/pages/mkopo-kikundi/mkopo-kikundi.component';
import { RejectedBorrowersComponent } from './features/borrowers/pages/rejected-borrowers/rejected-borrowers.component';
import { LoansComponent } from './features/loans/pages/loans/loans.component';
import { LoanDetailsComponent } from './features/loans/pages/loan-details/loan-details.component';
import { CalculatorComponent } from './features/calculator/pages/calculator/calculator.component';
import { PendingApprovalsComponent } from './features/approvals/pages/pending-approvals/pending-approvals.component';
import { AuditLogsComponent } from './features/audit/pages/audit-logs/audit-logs.component';
import { PaymentsComponent } from './features/payments/pages/payments/payments.component';
import { RecordPaymentComponent } from './features/payments/pages/record-payment/record-payment.component';
import { ReportsComponent } from './features/reports/pages/reports/reports.component';

import { DefaultLayoutComponent } from './layouts/default-layout/default-layout.component';
import { SimpleLayoutComponent } from './layouts/simple-layout/simple-layout.component';

import { AuthGuard } from './core/guards/auth.guard';
import { RoleGuard } from './core/guards/role.guard';

const routes: Routes = [
  // Login - Public
  { path: 'login', component: SimpleLayoutComponent, children: [{ path: '', component: LoginComponent }] },
  
  // Protected routes
  {
    path: '',
    component: DefaultLayoutComponent,
    canActivate: [AuthGuard],
    children: [
      // Dashboard - All staff
      { path: 'dashboard', component: DashboardComponent },
      
      // Loan Calculator - All staff
      { path: 'calculator', component: CalculatorComponent },
      
      // Borrowers - Secretary+
      { path: 'borrowers', component: BorrowersComponent },
      { path: 'borrowers/new', component: MkopoBinafsiComponent },
      { path: 'borrowers/new-group', component: MkopoKikundiComponent },
      { path: 'borrowers/:id', component: BorrowerDetailsComponent },
      { path: 'borrowers/rejected', component: RejectedBorrowersComponent },
      
      // Loans - Secretary+
      { path: 'loans', component: LoansComponent },
      { path: 'loans/:id', component: LoanDetailsComponent },
      
      // Payments - Secretary+
      { path: 'payments', component: PaymentsComponent },
      { path: 'payments/record', component: RecordPaymentComponent },
      
      // Pending Approvals - Loan Manager+
      { path: 'approvals', component: PendingApprovalsComponent },
      
      // Reports - Admin/MD/GM/Loan Officer
      { path: 'reports', component: ReportsComponent },
      
      // Audit Logs - Admin/MD/GM/Loan Officer
      { path: 'audit-logs', component: AuditLogsComponent },
      
      // Default
      { path: '', redirectTo: 'dashboard', pathMatch: 'full' }
    ]
  },
  
  { path: '**', redirectTo: 'dashboard' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes, { useHash: true })],
  exports: [RouterModule]
})
export class AppRoutingModule { }