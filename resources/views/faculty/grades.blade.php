@extends('layouts.master')
@section('title','Faculty – Grade Submission')
@section('content')
<div class="container">
  <h1 class="h4 mb-3">Grade Submission</h1>
  <div class="alert alert-info">Select a class and submit grades for enrolled students.</div>
  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label">Class</label>
      <select class="form-select"><option>—</option></select>
    </div>
  </div>
  <div class="table-responsive mt-3">
    <table class="table table-striped">
      <thead><tr><th>Student</th><th>Type</th><th>Score</th><th>Actions</th></tr></thead>
      <tbody><tr><td colspan="4" class="text-muted">No data yet.</td></tr></tbody>
    </table>
  </div>
</div>
@endsection

