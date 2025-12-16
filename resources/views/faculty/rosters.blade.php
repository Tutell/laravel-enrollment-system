@extends('layouts.master')
@section('title','Faculty – Class Rosters')
@section('content')
<div class="container">
  <h1 class="h4 mb-3">Class Rosters</h1>
  <div class="alert alert-info">This page will list your assigned classes and their students.</div>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead><tr><th>Section</th><th>Subject</th><th>Schedule</th><th>Students</th></tr></thead>
      <tbody><tr><td colspan="4" class="text-muted">No data yet.</td></tr></tbody>
    </table>
  </div>
</div>
@endsection

