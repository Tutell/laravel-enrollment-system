@extends('layouts.master')
@section('title','Enrollment – Step 4: Review')
@section('content')
<div class="container">
  <h1 class="h4 mb-3">Step 4: Review Application</h1>
  <div class="alert alert-info">Please review your information before submitting. Changes can be made by going back to previous steps.</div>
  <div class="d-flex justify-content-between mt-3">
    <a href="{{ url('/enrollment/apply/step3') }}" class="btn btn-outline-secondary">Back</a>
    <a href="{{ url('/enrollment/apply/step5') }}" class="btn btn-primary">Proceed to Submit</a>
  </div>
</div>
@endsection

