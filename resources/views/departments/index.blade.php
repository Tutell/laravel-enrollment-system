@extends('layouts.master')

@section('title','Departments')
@section('page-title','Departments')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Departments</h1>
    @if(Auth::check() && Auth::user()->role === 'admin')
      <div class="d-flex gap-2">
        <a href="{{ route('admin.departments.bulk') }}" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-people"></i> Bulk Assign
        </a>
        <a href="{{ route('admin.departments.import') }}" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-filetype-csv"></i> Import CSV
        </a>
        <form method="POST" action="{{ route('admin.departments.repair') }}" class="d-inline">
          @csrf
          <button class="btn btn-outline-success btn-sm">
            <i class="bi bi-tools"></i> Fix Missing
          </button>
        </form>
        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg"></i> New Department
        </a>
      </div>
    @endif
  </div>
  <div class="row g-3">
    @forelse($departments as $dept)
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card h-100">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="text-muted small">Department</div>
              <div class="h5 mb-0 fw-semibold">{{ $dept->name }}</div>
            </div>
            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
              <i class="bi bi-building text-primary"></i>
            </div>
          </div>
          <div class="mt-3">
            <a href="{{ route('departments.show', $dept) }}" class="btn btn-outline-primary btn-sm">View</a>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="alert alert-info">No departments found.</div>
      </div>
    @endforelse
  </div>
  <div class="mt-3">
    <small class="text-muted">Data cached for performance.</small>
  </div>
  </div>
@endsection
