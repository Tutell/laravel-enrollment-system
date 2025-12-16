@extends('layouts.master')

@section('title','Year Level Management')
@section('page-title','Year Level Management')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">High School Year Levels</h1>
  </div>

  <div class="row g-3">
    @foreach($summary as $info)
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card h-100">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="text-muted small">Grade {{ $info['grade'] }}</div>
              <div class="h4 mb-0 fw-semibold">{{ $info['students'] }} Students</div>
              <div class="small text-muted">{{ $info['approvedTeachers'] }} Adviser(s) • {{ $info['pendingAssignments'] }} Pending</div>
            </div>
            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
              <i class="bi bi-collection text-primary"></i>
            </div>
          </div>
          <div class="mt-3">
            <a href="{{ route('year-levels.show', $info['id']) }}" class="btn btn-outline-primary btn-sm">View</a>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection

