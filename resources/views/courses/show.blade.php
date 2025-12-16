@extends('layouts.master')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ $course->course_code }}</h1>
    <div>
      <a href="{{ route('courses.edit', $course) }}" class="btn btn-warning">Edit</a>
      <a href="{{ route('courses.index') }}" class="btn btn-secondary">Back</a>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h2 class="h6 mb-3">Details</h2>
          <dl class="row mb-0">
            <dt class="col-sm-4">Subject</dt>
            <dd class="col-sm-8">{{ optional($course->subject)->name }}</dd>

            <dt class="col-sm-4">Teacher</dt>
            <dd class="col-sm-8">
              @if($course->teacher)
                {{ $course->teacher->last_name }}, {{ $course->teacher->first_name }}
              @else
                —
              @endif
            </dd>

            <dt class="col-sm-4">Academic Year</dt>
            <dd class="col-sm-8">{{ optional($course->academicYear)->school_year }} – {{ optional($course->academicYear)->semester }}</dd>

            <dt class="col-sm-4">Schedule</dt>
            <dd class="col-sm-8">{{ $course->schedule }}</dd>

            <dt class="col-sm-4">Room</dt>
            <dd class="col-sm-8">{{ $course->room_number }}</dd>

            <dt class="col-sm-4">Capacity</dt>
            <dd class="col-sm-8">{{ $course->max_capacity }}</dd>
          </dl>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h2 class="h6 mb-3">Enrollments</h2>
          @if($course->enrollments->isEmpty())
            <p class="text-muted mb-0">No students enrolled.</p>
          @else
            <div class="table-responsive">
              <table class="table table-striped">
                <thead><tr><th>Student</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                @foreach($course->enrollments as $enr)
                  <tr>
                    <td>{{ optional($enr->student)->first_name }} {{ optional($enr->student)->last_name }}</td>
                    <td>{{ $enr->status }}</td>
                    <td>{{ optional($enr->enrollment_date)->format('Y-m-d') }}</td>
                  </tr>
                @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

