@extends('layouts.master')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Edit Course</h1>
    <a href="{{ route('courses.show', $course) }}" class="btn btn-secondary">Cancel</a>
  </div>
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <form method="POST" action="{{ route('courses.update', $course) }}">
        @csrf
        @method('PUT')
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Subject</label>
            <select name="subject_id" class="form-select" aria-label="Select subject">
              @foreach($subjects as $s)
                <option value="{{ $s->subject_ID }}" {{ ($course->subject_ID ?? $course->subject_id) == $s->subject_ID ? 'selected' : '' }}>{{ $s->name }}</option>
              @endforeach
            </select>
            @error('subject_id') <div class="text-danger small">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Teacher</label>
            <select name="teacher_id" class="form-select" aria-label="Select teacher">
              @foreach($teachers as $t)
                <option value="{{ $t->teacher_ID ?? $t->teacher_id }}" {{ ($course->teacher_ID ?? $course->teacher_id) == ($t->teacher_ID ?? $t->teacher_id) ? 'selected' : '' }}>{{ $t->last_name }}, {{ $t->first_name }}</option>
              @endforeach
            </select>
            @error('teacher_id') <div class="text-danger small">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Academic Year</label>
            <select name="academic_year_id" class="form-select" aria-label="Select academic year">
              @foreach($years as $y)
                <option value="{{ $y->academic_year_ID ?? $y->academic_year_id }}" {{ ($course->academic_year_ID ?? $course->academic_year_id) == ($y->academic_year_ID ?? $y->academic_year_id) ? 'selected' : '' }}>{{ $y->school_year }} – {{ $y->semester }}</option>
              @endforeach
            </select>
            @error('academic_year_id') <div class="text-danger small">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Course Code</label>
            <input type="text" name="course_code" class="form-control" value="{{ old('course_code', $course->course_code) }}">
            @error('course_code') <div class="text-danger small">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">Schedule</label>
            <input type="text" name="schedule" class="form-control" value="{{ old('schedule', $course->schedule) }}">
            @error('schedule') <div class="text-danger small">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-2">
            <label class="form-label">Room</label>
            <input type="text" name="room_number" class="form-control" value="{{ old('room_number', $course->room_number) }}">
            @error('room_number') <div class="text-danger small">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-2">
            <label class="form-label">Max Capacity</label>
            <input type="number" name="max_capacity" class="form-control" min="1" value="{{ old('max_capacity', $course->max_capacity) }}">
            @error('max_capacity') <div class="text-danger small">{{ $message }}</div> @enderror
          </div>
        </div>
        <div class="mt-3">
          <button class="btn btn-primary" type="submit">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

