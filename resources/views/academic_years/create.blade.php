@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Create Academic Year</h1>
    <form method="POST" action="{{ route('academic-years.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">School Year</label>
            <input type="text" name="school_year" class="form-control" value="{{ old('school_year') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Semester</label>
            <select name="semester" class="form-select" aria-label="Select semester">
                @foreach(['1st Semester','2nd Semester','Summer'] as $sem)
                    <option value="{{ $sem }}" {{ old('semester') === $sem ? 'selected' : '' }}>{{ $sem }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Active</label>
            <select name="is_active" class="form-select">
                <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>No</option>
            </select>
        </div>
        <button class="btn btn-primary" type="submit">Save</button>
    </form>
</div>
@endsection
