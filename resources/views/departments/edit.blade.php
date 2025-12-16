@extends('layouts.master')

@section('title','Edit Department')
@section('page-title','Edit Department')

@section('content')
<div class="container">
  <h1 class="h3 mb-3">Edit Department</h1>
  <form method="POST" action="{{ route('admin.departments.update', $department) }}" class="content-wrapper">
    @csrf
    @method('PUT')
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" value="{{ old('name', $department->name) }}" required>
      @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-primary" type="submit">Update</button>
      <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">Cancel</a>
      <form method="POST" action="{{ route('admin.departments.destroy', $department) }}" onsubmit="return confirm('Delete department?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-danger" type="submit">Delete</button>
      </form>
    </div>
  </form>
</div>
@endsection
