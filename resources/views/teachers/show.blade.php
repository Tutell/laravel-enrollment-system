@extends('layouts.master')

@section('title','Teacher Details')
@section('page-title','Teacher Details')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">{{ $teacher->first_name }} {{ $teacher->last_name }}</h1>
    <div>
      <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-warning">Edit</a>
      <a href="{{ route('teachers.index') }}" class="btn btn-secondary">Back</a>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h2 class="h6 mb-3">Profile</h2>
          @if(Auth::check() && Auth::user()->role === 'admin')
          <div class="alert alert-enhanced mb-3" style="border-left-color: var(--primary);">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <strong>Department:</strong>
                <span class="ms-1">{{ optional($teacher->department)->name ?? ($teacher->department ?? '—') }}</span>
              </div>
              <form method="POST" action="{{ route('teachers.department', $teacher) }}" class="d-flex gap-2">
                @csrf
                @method('PUT')
                <select name="department_id" class="form-select form-select-sm" style="min-width: 200px;">
                  @foreach(\App\Models\Department::orderBy('name')->get() as $d)
                    <option value="{{ $d->department_ID }}" @selected(($teacher->department_ID ?? null) === $d->department_ID)>{{ $d->name }}</option>
                  @endforeach
                </select>
                <button class="btn btn-primary btn-sm" type="submit">Change</button>
              </form>
            </div>
          </div>
          @endif
          <dl class="row mb-0">
            <dt class="col-sm-4">Department</dt>
            <dd class="col-sm-8">{{ optional($teacher->department)->name ?? ($teacher->department ?? '—') }}</dd>
            <dt class="col-sm-4">Contact</dt>
            <dd class="col-sm-8">{{ $teacher->contact_number ?? '—' }}</dd>
            <dt class="col-sm-4">Account</dt>
            <dd class="col-sm-8">
              @if(optional($teacher->account)->username)
                {{ $teacher->account->username }} (ID: {{ $teacher->account->account_ID }})
              @else
                Not linked
              @endif
            </dd>
          </dl>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h2 class="h6 mb-3">Assigned Sections</h2>
          @if($teacher->sections->isEmpty())
            <p class="text-muted mb-0">No assigned sections yet.</p>
          @else
            <ul class="mb-0">
              @foreach($teacher->sections as $section)
                <li>{{ $section->section_name }} (Grade {{ $section->grade_level }})</li>
              @endforeach
            </ul>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm mt-3">
    <div class="card-body">
      <h2 class="h6 mb-3">Courses</h2>
      @if($teacher->courses->isEmpty())
        <p class="text-muted mb-0">No courses assigned.</p>
      @else
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr><th>Code</th><th>Subject</th><th>Academic Year</th><th>Schedule</th><th>Room</th></tr>
            </thead>
            <tbody>
            @foreach($teacher->courses as $course)
              <tr>
                <td>{{ $course->course_code }}</td>
                <td>{{ optional($course->subject)->name }}</td>
                <td>
                  @php $ay = $course->academicYear; @endphp
                  {{ $ay ? ($ay->school_year.' '.$ay->semester) : '—' }}
                </td>
                <td>{{ $course->schedule }}</td>
                <td>{{ $course->room_number }}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
