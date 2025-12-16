@extends('layouts.master')

@section('title','Create Department')
@section('page-title','Create Department')

@section('content')
<div class="container">
  <h1 class="h3 mb-3">Create Department</h1>
  <form method="POST" action="{{ route('admin.departments.store') }}" class="content-wrapper">
    @csrf
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
      @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-primary" type="submit">Save</button>
      <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
