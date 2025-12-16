@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Edit Section</h1>
    <form method="POST" action="{{ route('sections.update', $section) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Teacher ID</label>
            <input type="number" name="teacher_id" class="form-control" value="{{ old('teacher_id', $section->teacher_id) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Section Name</label>
            <input type="text" name="section_name" class="form-control" value="{{ old('section_name', $section->section_name) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Grade Level</label>
            <input type="number" name="grade_level" class="form-control" value="{{ old('grade_level', $section->grade_level) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Capacity</label>
            <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $section->capacity) }}">
        </div>
        <button class="btn btn-primary" type="submit">Update</button>
    </form>
</div>
@endsection
