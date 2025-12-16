@extends('layouts.master')

@section('title', 'Archived Students')
@section('page-title', 'Archived Students')
@section('page-description', 'Browse and restore archived student records')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Archive</li>
@endsection

@section('content')
<div class="card border-0 shadow">
  <div class="card-body p-0">
    <div class="p-4 border-bottom">
      <form method="GET" action="{{ route('students.archive') }}" aria-label="Filter archived students">
        <div class="row g-2 align-items-end">
          <div class="col-md-4">
            <label class="form-label">Search</label>
            <div class="input-group">
              <span class="input-group-text bg-transparent border-end-0">
                <i class="bi bi-search"></i>
              </span>
              <input type="text" class="form-control border-start-0" name="q" value="{{ $q }}" placeholder="Name, email, or username">
            </div>
          </div>
          <div class="col-md-2">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select">
              <option value="">All</option>
              <option value="Male" @selected($gender==='Male')>Male</option>
              <option value="Female" @selected($gender==='Female')>Female</option>
              <option value="Other" @selected($gender==='Other')>Other</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Grade</label>
            <select name="grade_level" class="form-select">
              <option value="">All</option>
              @foreach(($grades ?? []) as $g)
                <option value="{{ $g }}" @selected(($gradeLevel ?? null)==$g)>Grade {{ $g }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Section</label>
            <select name="section_id" class="form-select">
              <option value="">All</option>
              @foreach(($sectionsList ?? []) as $sec)
                <option value="{{ $sec->section_ID }}" @selected(($sectionId ?? null)==$sec->section_ID)>{{ $sec->section_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12 d-flex justify-content-end gap-2">
            <a href="{{ route('students.archive') }}" class="btn btn-outline-secondary">
              Reset
            </a>
            <button class="btn btn-primary" type="submit">Apply</button>
            <a href="{{ route('students.archive', request()->query()) }}" class="btn btn-outline-info">
              <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </a>
          </div>
        </div>
      </form>
    </div>

    <div class="table-responsive">
      <form id="bulkDeleteForm" method="POST" action="{{ route('students.archive.bulk-delete') }}">
        @csrf
      <table class="table table-hover mb-0">
        <thead class="bg-light">
          <tr>
            <th style="width:40px;"><input type="checkbox" id="selectAllArchived"></th>
            <th>Student ID</th>
            <th>Full Name</th>
            <th>Section</th>
            <th>Status</th>
            <th>Archived At</th>
            <th>Reason</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
        @forelse($students as $student)
          <tr class="align-middle">
            <td>
              <input type="checkbox" name="student_ids[]" value="{{ $student->student_ID }}">
            </td>
            <td>{{ $student->student_ID }}</td>
            <td>
              <div class="fw-semibold">{{ $student->first_name }} {{ $student->last_name }}</div>
              <small class="text-muted">{{ $student->email ?? 'No email' }}</small>
            </td>
            <td>{{ optional($student->section)->section_name ?? '—' }}</td>
            <td>
              <span class="badge bg-dark bg-opacity-10 text-dark border border-dark">
                <i class="bi bi-archive-fill me-1"></i> Archived
              </span>
            </td>
            <td><small class="text-muted">{{ optional($student->archived_at) ? $student->archived_at->toDateTimeString() : '—' }}</small></td>
            <td><small class="text-muted">{{ $student->archive_reason ?: '—' }}</small></td>
            <td>
              <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View Details" aria-label="View details">
                  <i class="bi bi-eye"></i>
                </a>
                @if(auth()->check() && strtolower(auth()->user()->role ?? '') === 'admin')
                <form action="{{ route('students.restore', $student) }}" method="POST" class="d-inline" onsubmit="return confirm('Restore this student to active roster?');">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Restore student" aria-label="Restore student">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                  </button>
                </form>
                <form action="{{ route('students.permanent-delete', $student) }}" method="POST" class="d-inline" onsubmit="return confirm('PERMANENTLY delete this student and all related data? This cannot be undone.');">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Permanent Delete" aria-label="Permanent delete">
                    <i class="bi bi-trash-fill me-1"></i> Delete
                  </button>
                </form>
                @else
                <button type="button" class="btn btn-sm btn-outline-secondary" disabled aria-disabled="true" data-bs-toggle="tooltip" title="Only admins can restore">
                  <i class="bi bi-lock"></i>
                </button>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center py-5">
              <div class="py-4 text-muted">No archived students found.</div>
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
      </form>
    </div>

    @if($students->hasPages())
    <div class="p-4 border-top">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <small class="text-muted">
            Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} archived students
          </small>
        </div>
        <nav aria-label="Page navigation">
          {{ $students->links() }}
        </nav>
      </div>
    </div>
    @endif
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
  const selectAll = document.getElementById('selectAllArchived');
  const checkboxes = document.querySelectorAll('input[name="student_ids[]"]');
  if (selectAll) {
    selectAll.addEventListener('change', function() {
      checkboxes.forEach(cb => cb.checked = selectAll.checked);
    });
  }
  const bulkForm = document.getElementById('bulkDeleteForm');
  if (bulkForm) {
    const bulkBtn = document.createElement('button');
    bulkBtn.type = 'submit';
    bulkBtn.className = 'btn btn-danger mt-3';
    bulkBtn.innerHTML = '<span class=\"bulk-label\"><i class=\"bi bi-trash-fill me-1\"></i> Delete Selected</span><span class=\"spinner-border spinner-border-sm d-none\" role=\"status\" aria-hidden=\"true\"></span>';
    bulkForm.parentElement.appendChild(bulkBtn);
    bulkForm.addEventListener('submit', function(e) {
      if (!confirm('PERMANENTLY delete selected students and related data? This cannot be undone.')) {
        e.preventDefault();
        return;
      }
      const label = bulkBtn.querySelector('.bulk-label');
      const spinner = bulkBtn.querySelector('.spinner-border');
      label.classList.add('d-none');
      spinner.classList.remove('d-none');
      bulkBtn.setAttribute('disabled', 'disabled');
    });
  }
});
</script>
@endsection
