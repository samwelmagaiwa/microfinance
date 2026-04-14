import { Component } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../../core/environments/environment';

@Component({
  selector: 'app-calculator',
  template: `
    <div class="calculator-page">
      <h2>Loan Calculator</h2>
      
      <div class="calculator-form">
        <div class="form-group">
          <label>Loan Amount (TZS)</label>
          <input type="number" [(ngModel)]="amount" (input)="calculate()">
        </div>
        <div class="form-group">
          <label>Interest Rate (%)</label>
          <input type="number" [(ngModel)]="interestRate" (input)="calculate()">
        </div>
        <div class="form-group">
          <label>Repayment Period (Months)</label>
          <select [(ngModel)]="period" (change)="calculate()">
            <option [value]="6">6 Months</option>
            <option [value]="12">12 Months</option>
            <option [value]="18">18 Months</option>
            <option [value]="24">24 Months</option>
            <option [value]="36">36 Months</option>
          </select>
        </div>
      </div>

      <div class="results" *ngIf="result">
        <div class="result-card">
          <span class="label">Monthly Payment</span>
          <span class="value">{{ result.monthlyPayment | number:'1.0-0' }} TZS</span>
        </div>
        <div class="result-card">
          <span class="label">Total Interest</span>
          <span class="value">{{ result.totalInterest | number:'1.0-0' }} TZS</span>
        </div>
        <div class="result-card">
          <span class="label">Total Payment</span>
          <span class="value">{{ result.totalPayment | number:'1.0-0' }} TZS</span>
        </div>
      </div>
    </div>
  `,
  styles: [`
    .calculator-page { max-width: 800px; margin: 0 auto; padding: 1rem; }
    .calculator-form { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; }
    .form-group { display: flex; flex-direction: column; }
    .form-group label { margin-bottom: 0.5rem; font-weight: 500; }
    .form-group input, .form-group select { padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; }
    .results { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    .result-card { background: white; padding: 1.5rem; border-radius: 8px; text-align: center; }
    .result-card .label { display: block; color: #6b7280; margin-bottom: 0.5rem; }
    .result-card .value { font-size: 1.5rem; font-weight: 700; color: #4f46e5; }
  `]
})
export class CalculatorComponent {
  amount = 100000;
  interestRate = 2;
  period = 6;
  result: any = null;

  constructor(private http: HttpClient) {
    this.calculate();
  }

  calculate(): void {
    const principal = this.amount || 0;
    const rate = (this.interestRate || 0) / 100;
    const months = this.period || 1;
    
    const monthlyPayment = principal * rate * Math.pow(1 + rate, months) / (Math.pow(1 + rate, months) - 1);
    const totalPayment = monthlyPayment * months;
    const totalInterest = totalPayment - principal;

    this.result = {
      monthlyPayment: isNaN(monthlyPayment) ? 0 : monthlyPayment,
      totalInterest: isNaN(totalInterest) ? 0 : totalInterest,
      totalPayment: isNaN(totalPayment) ? 0 : totalPayment
    };
  }
}