@extends('layouts.master')
@section('title','Enrollment – Step 1: Personal Info')
@section('content')
<div class="container">
  <h1 class="h4 mb-3">Step 1: Personal Information</h1>
  <form method="POST" action="{{ url('/enrollment/apply/step1') }}" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">First Name</label>
        <input type="text" name="first_name" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Last Name</label>
        <input type="text" name="last_name" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Gender</label>
        <select class="form-select" name="gender" required>
          <option>Male</option>
          <option>Female</option>
          <option>Other</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Birthdate</label>
        <input type="date" name="birthdate" class="form-control" required>
      </div>
    </div>
    <div class="d-flex justify-content-end mt-3">
      <button class="btn btn-primary" type="submit">Save \& Continue</button>
    </div>
  </form>
</div>
@endsection

