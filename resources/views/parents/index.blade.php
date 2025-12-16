@extends('layouts.master')
@section('title','Parent Portal')
@section('content')
<div class="container">
  <h1 class="h4 mb-3">Parent \& Guardian Portal</h1>
  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h2 class="h6">Student Progress</h2>
          <p class="text-muted mb-0">View grades, attendance, and assignments.</p>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h2 class="h6">Billing \& Payments</h2>
          <p class="text-muted mb-0">Review statements and pay online via GCash/Maya.</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

