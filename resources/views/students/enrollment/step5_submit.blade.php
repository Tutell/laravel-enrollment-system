@extends('layouts.master')
@section('title','Enrollment – Step 5: Submit')
@section('content')
<div class="container">
  <h1 class="h4 mb-3">Step 5: Submit Application</h1>
  <div class="alert alert-success">You're ready to submit your application.</div>
  <form method="POST" action="{{ url('/enrollment/apply/submit') }}">
    @csrf
    <button class="btn btn-success" type="submit">Submit Application</button>
  </form>
</div>
@endsection

