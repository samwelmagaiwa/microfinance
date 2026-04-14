import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Loan } from '../../../core/models/loan.model';
import { environment } from '../../../core/environments/environment';

@Injectable({
  providedIn: 'root'
})
export class LoanService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) {}

  private getHeaders(): HttpHeaders {
    const token = localStorage.getItem('token');
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    });
  }

  getLoans(params?: any): Observable<any> {
    return this.http.get(`${this.apiUrl}/loans`, {
      headers: this.getHeaders(),
      params: params
    });
  }

  getLoan(id: number): Observable<Loan> {
    return this.http.get<Loan>(`${this.apiUrl}/loans/${id}`, {
      headers: this.getHeaders()
    });
  }

  createLoan(data: any): Observable<Loan> {
    return this.http.post<Loan>(`${this.apiUrl}/loans`, data, {
      headers: this.getHeaders()
    });
  }

  updateLoan(id: number, data: any): Observable<Loan> {
    return this.http.put<Loan>(`${this.apiUrl}/loans/${id}`, data, {
      headers: this.getHeaders()
    });
  }

  approveLoan(id: number, step: string, data?: any): Observable<any> {
    return this.http.patch(`${this.apiUrl}/loans/${id}/approve-step`, {
      step,
      ...data
    }, {
      headers: this.getHeaders()
    });
  }

  rejectLoan(id: number, reason: string): Observable<any> {
    return this.http.patch(`${this.apiUrl}/loans/${id}/reject`, { reason }, {
      headers: this.getHeaders()
    });
  }

  disburseLoan(id: number, data?: any): Observable<any> {
    return this.http.patch(`${this.apiUrl}/loans/${id}/disburse`, data || {}, {
      headers: this.getHeaders()
    });
  }

  getApprovalStatus(id: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/loans/${id}/approval-status`, {
      headers: this.getHeaders()
    });
  }

  deleteLoan(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/loans/${id}`, {
      headers: this.getHeaders()
    });
  }
}