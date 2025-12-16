@extends('layouts.student')
@section('title','Student Profile')
@section('content')
<div class="row g-3">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-light fw-semibold">Personal Information</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">First name</label>
            <input type="text" class="form-control" value="{{ $student->first_name ?? '—' }}" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Last name</label>
            <input type="text" class="form-control" value="{{ $student->last_name ?? '—' }}" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Student ID</label>
            <input type="text" class="form-control" value="{{ $student->student_ID }}" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">LRN</label>
            <input type="text" class="form-control" value="{{ $student->lrn ?? '—' }}" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email address</label>
            <input type="email" class="form-control" value="{{ $account->email ?? $student->email ?? '—' }}" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="text" class="form-control" value="{{ $student->phone ?? '—' }}" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Address</label>
            <input type="text" class="form-control" value="{{ $student->address ?? '—' }}" disabled>
          </div>
          <div class="col-md-3">
            <label class="form-label">Gender</label>
            <input type="text" class="form-control" value="{{ ucfirst($student->gender ?? '—') }}" disabled>
          </div>
          <div class="col-md-3">
            <label class="form-label">Birthdate</label>
            <input type="text" class="form-control" value="{{ optional($student->birthdate)->format('Y-m-d') ?? '—' }}" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Section</label>
            <input type="text" class="form-control" value="{{ optional($student->section)->section_name ?? '—' }}" disabled>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-light fw-semibold">Academic Details</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Grade level</label>
            <input type="text" class="form-control" value="{{ optional($student->section)->grade_level ? 'Grade '.optional($student->section)->grade_level : '—' }}" disabled>
          </div>
        </div>
        <hr>
        <div class="fw-semibold mb-2">Enrolled Courses</div>
        @forelse($student->enrollments as $enr)
          @php($course = $enr->course)
          <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
            <div>
              <div class="fw-semibold">{{ optional($course->subject)->name ?? 'Course' }}</div>
              <small class="text-muted">Teacher: {{ optional($course->teacher)->first_name }} {{ optional($course->teacher)->last_name }}</small>
            </div>
            <div class="text-end">
              <small class="text-muted d-block">Schedule: {{ $course->schedule ?? '—' }}</small>
              <small class="text-muted">Room: {{ $course->room_number ?? '—' }}</small>
            </div>
          </div>
        @empty
          <div class="text-muted">No enrolled courses.</div>
        @endforelse
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-light fw-semibold">Parent/Guardian Information</div>
      <div class="card-body">
        @forelse($student->guardians as $g)
          <div class="row g-3 mb-2">
            <div class="col-md-4">
              <label class="form-label">Full name</label>
              <input type="text" class="form-control" value="{{ $g->full_name }}" disabled>
            </div>
            <div class="col-md-3">
              <label class="form-label">Relationship</label>
              <input type="text" class="form-control" value="{{ $g->relationship }}" disabled>
            </div>
            <div class="col-md-3">
              <label class="form-label">Contact number</label>
              <input type="text" class="form-control" value="{{ $g->contact_number ?? '—' }}" disabled>
            </div>
            <div class="col-md-2">
              <label class="form-label">Email</label>
              <input type="text" class="form-control" value="{{ $g->email ?? '—' }}" disabled>
            </div>
          </div>
        @empty
          <div class="text-muted">No guardian records.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
