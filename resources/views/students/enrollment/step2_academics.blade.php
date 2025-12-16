@extends('layouts.master')
@section('title','Enrollment – Step 2: Academic Records')
@section('content')
<div class="container">
  <h1 class="h4 mb-3">Step 2: Academic Records</h1>
  <form method="POST" action="{{ url('/enrollment/apply/step2') }}">
    @csrf
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Last School Attended</label>
        <input type="text" name="last_school" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Last Grade Level</label>
        <input type="number" name="last_grade_level" class="form-control" min="1" max="12" required>
      </div>
      <div class="col-12">
        <label class="form-label">Awards / Honors (optional)</label>
        <textarea name="awards" class="form-control" rows="3"></textarea>
      </div>
    </div>
    <div class="d-flex justify-content-between mt-3">
      <a href="{{ url('/enrollment/apply/step1') }}" class="btn btn-outline-secondary">Back</a>
      <button class="btn btn-primary" type="submit">Save \& Continue</button>
    </div>
  </form>
</div>
@endsection

