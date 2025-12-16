@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Edit Academic Year</h1>
    <form method="POST" action="{{ route('academic-years.update', $academicYear) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">School Year</label>
            <input type="text" name="school_year" class="form-control" value="{{ old('school_year', $academicYear->school_year) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Semester</label>
            <select name="semester" class="form-select" aria-label="Select semester">
                @foreach(['1st Semester','2nd Semester','Summer'] as $sem)
                    <option value="{{ $sem }}" {{ old('semester', $academicYear->semester) === $sem ? 'selected' : '' }}>{{ $sem }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Active</label>
            <select name="is_active" class="form-select">
                <option value="1" {{ old('is_active', $academicYear->is_active) ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ old('is_active', $academicYear->is_active) ? '' : 'selected' }}>No</option>
            </select>
        </div>
        <button class="btn btn-primary" type="submit">Update</button>
    </form>
</div>
@endsection
