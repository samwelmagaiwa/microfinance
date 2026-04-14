import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';

import { AppComponent } from './app.component';
import { AppRoutingModule } from './app-routing.module';

import { DefaultLayoutComponent } from './layouts/default-layout/default-layout.component';
import { SimpleLayoutComponent } from './layouts/simple-layout/simple-layout.component';

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

@NgModule({
  declarations: [
    AppComponent,
    DefaultLayoutComponent,
    SimpleLayoutComponent,
    LoginComponent,
    DashboardComponent,
    BorrowersComponent,
    BorrowerDetailsComponent,
    MkopoBinafsiComponent,
    MkopoKikundiComponent,
    RejectedBorrowersComponent,
    LoansComponent,
    LoanDetailsComponent,
    CalculatorComponent,
    PendingApprovalsComponent,
    AuditLogsComponent,
    PaymentsComponent,
    RecordPaymentComponent,
    ReportsComponent
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    FormsModule,
    ReactiveFormsModule,
    AppRoutingModule,
    RouterModule.forRoot([], { useHash: true })
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }