@extends('layouts.master')

@section('title','Year Level')
@section('page-title','Year Level')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 mb-1">Grade {{ $year_level->grade_level }}</h1>
      <div class="text-muted small">Total Students: {{ $totalStudents }}</div>
    </div>
    <div>
      @if(auth()->check() && auth()->user()->role === 'teacher')
        <form method="POST" action="{{ route('year-levels.request', $year_level) }}" class="d-inline">
          @csrf
          <button class="btn btn-primary btn-sm">Request Assignment</button>
        </form>
      @endif
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h2 class="h6 mb-3">Sections</h2>
          @if($sections->isEmpty())
            <p class="text-muted mb-0">No sections for this grade.</p>
          @else
            <div class="table-responsive">
              <table class="table table-striped">
                <thead><tr><th>Section</th><th>Adviser</th><th>Students/Capacity</th></tr></thead>
                <tbody>
                @foreach($sections as $section)
                  @php
                    $count = $section->students->count();
                    $cap = $section->capacity ?? 40;
                  @endphp
                  <tr>
                    <td>{{ $section->section_name }}</td>
                    <td>
                      @php $adviser = $section->teacher ?: $section->teachers->first(); @endphp
                      @if($adviser)
                        {{ $adviser->first_name }} {{ $adviser->last_name }}
                      @else
                        <span class="text-danger">Unassigned</span>
                      @endif
                    </td>
                    <td>{{ $count }}/{{ $cap }}</td>
                  </tr>
                @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h2 class="h6 mb-3">Assignments</h2>
          <div class="mb-2">
            <span class="badge bg-success">Approved: {{ $approved->count() }}</span>
            <span class="badge bg-warning text-dark ms-2">Pending: {{ $pending->count() }}</span>
          </div>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead><tr><th>Teacher</th><th>Status</th><th>Actions</th></tr></thead>
              <tbody>
              @foreach($year_level->assignments as $a)
                <tr>
                  <td>{{ $a->teacher?->last_name }}, {{ $a->teacher?->first_name }}</td>
                  <td>
                    @if($a->status === 'approved')
                      <span class="badge bg-success">Approved</span>
                    @elseif($a->status === 'pending')
                      <span class="badge bg-warning text-dark">Pending</span>
                    @else
                      <span class="badge bg-danger">Rejected</span>
                    @endif
                  </td>
                  <td class="text-nowrap">
                    @if(auth()->check() && auth()->user()->role === 'admin' && $a->status === 'pending')
                      <form method="POST" action="{{ route('year-levels.approve', $a) }}" class="d-inline">
                        @csrf
                        @method('PUT')
                        <button class="btn btn-sm btn-outline-success">Approve</button>
                      </form>
                      <form method="POST" action="{{ route('year-levels.reject', $a) }}" class="d-inline ms-1">
                        @csrf
                        @method('PUT')
                        <button class="btn btn-sm btn-outline-danger">Reject</button>
                      </form>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
