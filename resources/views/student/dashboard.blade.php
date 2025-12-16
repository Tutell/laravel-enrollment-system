@extends('layouts.student')
@section('title', 'Student Dashboard')
@section('content')
<div class="row g-3">
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <div class="h5 mb-1">Welcome, {{ $student->first_name }} {{ $student->last_name }}</div>
          <div class="text-muted">LRN: {{ $student->lrn ?? '—' }}</div>
        </div>
        <div>
          <span class="badge bg-primary">Grade {{ $gradeLevel ?? 'N/A' }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-light fw-semibold">Your Section</div>
      <div class="card-body">
        @if($student->section)
          <div class="d-flex align-items-center mb-2">
            <i class="bi bi-grid-3x3 me-2 text-success"></i>
            <div>
              <div class="fw-semibold">{{ $student->section->section_name }}</div>
              <small class="text-muted">Adviser: {{ optional($student->section->teacher)->first_name }} {{ optional($student->section->teacher)->last_name }}</small>
            </div>
          </div>
        @else
          <div class="text-muted">Not assigned to a section yet.</div>
        @endif
        <hr>
        <div class="fw-semibold mb-2">Other Sections in Grade {{ $gradeLevel ?? 'N/A' }}</div>
        @forelse($sections as $sec)
          <div class="d-flex align-items-center mb-2">
            <i class="bi bi-people me-2 text-info"></i>
            <div>
              <div>{{ $sec->section_name }}</div>
              <small class="text-muted">Adviser: {{ optional($sec->teacher)->first_name }} {{ optional($sec->teacher)->last_name }}</small>
            </div>
          </div>
        @empty
          <div class="text-muted">No sections found for this grade level.</div>
        @endforelse
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-light fw-semibold">Subjects & Courses</div>
      <div class="card-body">
        <div class="fw-semibold mb-2">Grade-level Subjects</div>
        @forelse(($gradeSubjects ?? collect()) as $subj)
          <div class="d-flex align-items-center mb-2">
            <i class="bi bi-book me-2 text-primary"></i>
            <div class="fw-semibold">{{ $subj->name }}</div>
          </div>
        @empty
          <div class="text-muted">No subjects configured for your grade level.</div>
        @endforelse
        <hr>
        <div class="fw-semibold mb-2">Enrolled Courses</div>
        @forelse($courses as $course)
          <div class="d-flex align-items-center mb-2">
            <i class="bi bi-journal-text me-2 text-secondary"></i>
            <div>
              <div class="fw-semibold">{{ optional($course->subject)->name ?? 'Course' }}</div>
              <small class="text-muted d-block">Teacher: {{ optional($course->teacher)->first_name }} {{ optional($course->teacher)->last_name }}</small>
              <small class="text-muted">Schedule: {{ $course->schedule ?? '—' }} • Room: {{ $course->room_number ?? '—' }}</small>
            </div>
          </div>
        @empty
          <div class="text-muted">You are not enrolled in any courses yet.</div>
        @endforelse
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-light fw-semibold">My Grades</div>
      <div class="card-body">
        @if($grades->count())
          <div class="table-responsive">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Subject Code</th>
                  <th>Quarter</th>
                  <th>Grade</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                @foreach($grades as $g)
                  <tr>
                    @php
                      $enr = $student->enrollments->firstWhere('enrollment_id', $g->enrollment_id);
                      $code = optional(optional($enr)->course)->course_code ?? '—';
                    @endphp
                    <td>{{ $code }}</td>
                    <td>{{ $g->type }}</td>
                    <td>{{ $g->score }}</td>
                    <td>{{ optional($g->date_recorded)->format('Y-m-d') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-muted">No grades recorded yet.</div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-light fw-semibold">Announcements</div>
      <div class="card-body">
        @if(count($announcements))
          @foreach($announcements as $a)
            <div class="alert alert-info">{{ $a }}</div>
          @endforeach
        @else
          <div class="text-muted">No announcements for your grade level.</div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
