@extends('layouts.master')

@section('title','Edit Teacher')
@section('page-title','Edit Teacher')

@section('content')
<div class="container">
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('teachers.update', $teacher) }}">
        @csrf
        @method('PUT')

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">First name</label>
            <input type="text" name="first_name" class="form-control" value="{{ old('first_name',$teacher->first_name) }}">
            @error('first_name') <div class="text-danger small">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Last name</label>
            <input type="text" name="last_name" class="form-control" value="{{ old('last_name',$teacher->last_name) }}">
            @error('last_name') <div class="text-danger small">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Contact number</label>
            <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number',$teacher->contact_number) }}">
            @error('contact_number') <div class="text-danger small">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Department</label>
            <select name="department_id" class="form-select">
              <option value="">Select a department</option>
              @foreach(($departments ?? []) as $dept)
                <option value="{{ $dept->department_ID }}" @selected(old('department_id',$teacher->department_ID ?? null) == $dept->department_ID)>{{ $dept->name }}</option>
              @endforeach
            </select>
            @error('department_id') <div class="text-danger small">{{ $message }}</div> @enderror
            <small class="text-muted">If not listed, type a new department name:</small>
            <input type="text" name="department" class="form-control mt-1" value="{{ old('department',$teacher->department) }}" placeholder="Optional">
            @error('department') <div class="text-danger small">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="mt-3">
          <button class="btn btn-primary">Save changes</button>
          <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-secondary ms-2">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
