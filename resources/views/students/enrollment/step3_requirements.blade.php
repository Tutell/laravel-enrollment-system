@extends('layouts.master')
@section('title','Enrollment – Step 3: Requirements')
@section('content')
<div class="container">
  <h1 class="h4 mb-3">Step 3: Requirements Submission</h1>
  <form method="POST" action="{{ url('/enrollment/apply/step3') }}" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Form 137 / Report Card (PDF/JPG)</label>
        <input type="file" name="report_card" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Birth Certificate (PDF/JPG)</label>
        <input type="file" name="birth_certificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Good Moral Certificate (optional)</label>
        <input type="file" name="good_moral" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
      </div>
    </div>
    <div class="d-flex justify-content-between mt-3">
      <a href="{{ url('/enrollment/apply/step2') }}" class="btn btn-outline-secondary">Back</a>
      <button class="btn btn-primary" type="submit">Save \& Continue</button>
    </div>
  </form>
</div>
@endsection

