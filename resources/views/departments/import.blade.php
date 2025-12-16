@extends('layouts.master')

@section('title','Import Teachers')
@section('page-title','Import Teachers to Departments')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Import Teachers CSV</h1>
    <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
  </div>

  <div class="content-wrapper">
    <form method="POST" action="{{ route('admin.departments.import.post') }}" enctype="multipart/form-data">
      @csrf
      <div class="mb-3">
        <label class="form-label">CSV File</label>
        <input type="file" name="file" class="form-control" accept=".csv,text/csv" required>
        <small class="text-muted">Columns: username,email,department (department optional if default selected)</small>
        @error('file')<div class="text-danger small">{{ $message }}</div>@enderror
      </div>
      <div class="mb-3">
        <label class="form-label">Default Department</label>
        <select class="form-select" name="default_department_id">
          <option value="">— None —</option>
          @foreach($departments as $d)
            <option value="{{ $d->department_ID }}">{{ $d->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit">Import</button>
        <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
