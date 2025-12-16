@extends('layouts.master')
@section('title','Manual Subject Assignment')
@section('page-title','Manual Subject Assignment')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.show', $student) }}">Student</a></li>
    <li class="breadcrumb-item active">Subjects</li>
@endsection
@section('content')
<div class="card border-0 shadow-sm">
  <div class="card-body">
    <div class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Grade level</label>
        <form method="GET" action="{{ route('students.subjects', $student) }}">
          <div class="input-group">
            <select name="grade" class="form-select">
              <option value="">— Select grade —</option>
              @foreach($gradeLevels as $gl)
                <option value="{{ $gl }}" {{ (string)$selectedGrade === (string)$gl ? 'selected' : '' }}>Grade {{ $gl }}</option>
              @endforeach
            </select>
            <button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i>Load</button>
          </div>
        </form>
      </div>
      <div class="col-md-8 text-end">
        <button class="btn btn-success" type="submit" form="assignForm" @if(!$selectedGrade) disabled @endif>
          <i class="bi bi-plus-circle me-1"></i>Assign Selected Subjects
        </button>
        <a class="btn btn-outline-secondary" href="{{ route('students.show',$student) }}">Back to Student</a>
        @if(optional($student->section)->grade_level)
        <form class="d-inline" method="POST" action="{{ route('students.auto-enroll', $student) }}">
          @csrf
          <button class="btn btn-outline-primary">
            <i class="bi bi-lightning-charge me-1"></i>Auto-enroll Grade {{ $student->section->grade_level }}
          </button>
        </form>
        @endif
      </div>
    </div>
    <hr>
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
    <form id="assignForm" method="POST" action="{{ route('students.subjects.assign', $student) }}">
      @csrf
      <input type="hidden" name="grade" value="{{ $selectedGrade }}">
      <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th style="width:40px;">
              @if($subjects->count())
              <input type="checkbox" id="checkAll">
              @endif
            </th>
            <th>Subject</th>
            <th>Status</th>
            <th>Courses in Active Year</th>
            <th>Assign Teacher</th>
          </tr>
        </thead>
        <tbody>
          @forelse($subjects as $s)
            <tr>
              <td>
                @php $enrolled = in_array($s->getKey(), $existingSubjectIds, true); @endphp
                <input type="checkbox" name="subject_ids[]" value="{{ $s->getKey() }}" @if($enrolled) disabled @endif>
              </td>
              <td>{{ $s->name }}</td>
              <td>
                @if($enrolled)
                  <span class="badge bg-secondary">Already assigned</span>
                @else
                  <span class="badge bg-success">Available</span>
                @endif
              </td>
              <td>
                @forelse(($coursesBySubject[$s->getKey()] ?? collect()) as $c)
                  <div>
                    <span class="fw-semibold">{{ optional($c->subject)->name }}</span>
                    <small class="text-muted">Teacher: {{ optional($c->teacher)->first_name }} {{ optional($c->teacher)->last_name }}</small>
                  </div>
                @empty
                  <span class="text-muted">No course configured</span>
                @endforelse
              </td>
              <td>
                <form method="POST" action="{{ route('students.subjects.assign-teacher', $student) }}" class="d-flex gap-2 align-items-center">
                  @csrf
                  <input type="hidden" name="subject_id" value="{{ $s->getKey() }}">
                  <select name="teacher_id" class="form-select form-select-sm" style="max-width: 220px;">
                    @foreach($teachers as $t)
                      @php
                        $currentCourse = ($coursesBySubject[$s->getKey()] ?? collect())->first();
                        $currentTeacherId = optional($currentCourse)->teacher_id;
                      @endphp
                      <option value="{{ $t->teacher_id }}" {{ (string)$currentTeacherId === (string)$t->teacher_id ? 'selected' : '' }}>
                        {{ $t->last_name }}, {{ $t->first_name }}
                      </option>
                    @endforeach
                  </select>
                  <button class="btn btn-sm btn-outline-primary" type="submit">
                    <i class="bi bi-person-check me-1"></i>Set
                  </button>
                </form>
                <div class="small text-muted mt-1">Tip: Select subjects above to assign & enroll.</div>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-muted">Select a grade level to view subjects.</td></tr>
          @endforelse
        </tbody>
      </table>
      </div>
    </form>
  </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const checkAll = document.getElementById('checkAll');
  if (checkAll) {
    checkAll.addEventListener('change', function() {
      document.querySelectorAll('input[name="subject_ids[]"]:not([disabled])').forEach(cb => cb.checked = checkAll.checked);
    });
  }
});
</script>
@endpush
@endsection
