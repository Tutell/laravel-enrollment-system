@extends('layouts.master')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Courses</h1>
        <a href="{{ route('courses.create') }}" class="btn btn-primary">Add Course</a>
    </div>
    <div class="alert alert-info">This page will list courses. Content coming soon.</div>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead><tr><th>#</th><th>Code</th><th>Teacher</th><th>Schedule</th><th>Actions</th></tr></thead>
            <tbody>
                <tr><td colspan="5" class="text-muted">No data yet.</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

