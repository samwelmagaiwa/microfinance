import { Component, OnInit, signal } from '@angular/core';
import { Router } from '@angular/router';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../../core/environments/environment';

@Component({
  selector: 'app-rejected-borrowers',
  template: `
    <div class="rejected-page">
      <h2>Rejected Applications</h2>
      <div class="table-container" *ngIf="!loading()">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>NIDA</th>
              <th>Phone</th>
              <th>Product</th>
              <th>Amount</th>
              <th>Rejection Reason</th>
            </tr>
          </thead>
          <tbody>
            <tr *ngFor="let b of borrowers()">
              <td>{{ b.id }}</td>
              <td>{{ b.full_name }}</td>
              <td>{{ b.nida_number }}</td>
              <td>{{ b.phone }}</td>
              <td>{{ b.loan_product }}</td>
              <td>{{ b.loan_amount | number }}</td>
              <td>{{ b.rejection_reason }}</td>
            </tr>
            <tr *ngIf="borrowers().length === 0">
              <td colspan="7" class="text-center">No rejected applications</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="loading" *ngIf="loading()">
        <div class="spinner-border text-primary"></div>
      </div>
    </div>
  `,
  styles: [`
    .rejected-page { padding: 1rem; }
    .data-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
    .data-table th, .data-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
    .data-table th { background: #f9fafb; font-weight: 600; }
    .text-center { text-align: center; }
    .loading { text-align: center; padding: 3rem; }
  `]
})
export class RejectedBorrowersComponent implements OnInit {
  borrowers = signal<any[]>([]);
  loading = signal(true);

  constructor(private http: HttpClient, private router: Router) {}

  ngOnInit(): void {
    this.loadRejected();
  }

  loadRejected(): void {
    const token = localStorage.getItem('token');
    this.http.get<any>(`${environment.apiUrl}/borrowers`, {
      headers: { Authorization: `Bearer ${token}` },
      params: { status: 'rejected' }
    }).subscribe({
      next: (res) => { this.borrowers.set(res.data || res); this.loading.set(false); },
      error: () => { this.borrowers.set([]); this.loading.set(false); }
    });
  }
}