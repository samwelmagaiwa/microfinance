import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Borrower } from '../../../core/models/borrower.model';
import { environment } from '../../../core/environments/environment';

@Injectable({
  providedIn: 'root'
})
export class BorrowerService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) {}

  private getHeaders(): HttpHeaders {
    const token = localStorage.getItem('token');
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    });
  }

  getBorrowers(params?: any): Observable<any> {
    return this.http.get(`${this.apiUrl}/borrowers`, {
      headers: this.getHeaders(),
      params: params
    });
  }

  getBorrower(id: number): Observable<Borrower> {
    return this.http.get<Borrower>(`${this.apiUrl}/borrowers/${id}`, {
      headers: this.getHeaders()
    });
  }

  createBorrower(data: any): Observable<Borrower> {
    return this.http.post<Borrower>(`${this.apiUrl}/borrowers`, data, {
      headers: this.getHeaders()
    });
  }

  updateBorrower(id: number, data: any): Observable<Borrower> {
    return this.http.put<Borrower>(`${this.apiUrl}/borrowers/${id}`, data, {
      headers: this.getHeaders()
    });
  }

  approveBorrower(id: number, data?: any): Observable<any> {
    return this.http.patch(`${this.apiUrl}/borrowers/${id}/approve`, data || {}, {
      headers: this.getHeaders()
    });
  }

  rejectBorrower(id: number, reason: string): Observable<any> {
    return this.http.patch(`${this.apiUrl}/borrowers/${id}/reject`, { reason }, {
      headers: this.getHeaders()
    });
  }

  getReviewHistory(id: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/borrowers/${id}/review-history`, {
      headers: this.getHeaders()
    });
  }

  deleteBorrower(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/borrowers/${id}`, {
      headers: this.getHeaders()
    });
  }
}