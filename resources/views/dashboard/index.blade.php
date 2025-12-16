@extends('layouts.master')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Dashboard</h1>
        <div>
            <a href="{{ route('students.index') }}" class="btn btn-primary btn-sm">Students</a>
            <a href="{{ route('teachers.index') }}" class="btn btn-outline-primary btn-sm">Teachers</a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Students</div>
                    <div class="display-6">{{ $stats['students'] }}</div>
                    <a class="stretched-link" href="{{ route('students.index') }}"></a>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Teachers</div>
                    <div class="display-6">{{ $stats['teachers'] }}</div>
                    <a class="stretched-link" href="{{ route('teachers.index') }}"></a>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Subjects</div>
                    <div class="display-6">{{ $stats['subjects'] }}</div>
                    <a class="stretched-link" href="{{ route('subjects.index') }}"></a>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Sections</div>
                    <div class="display-6">{{ $stats['sections'] }}</div>
                    <a class="stretched-link" href="{{ route('sections.index') }}"></a>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Year Levels</div>
                    <div class="display-6">4</div>
                    <a class="stretched-link" href="{{ route('year-levels.index') }}"></a>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Academic Years</div>
                    <div class="display-6">{{ $stats['academicYears'] }}</div>
                    <a class="stretched-link" href="{{ route('academic-years.index') }}"></a>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Enrollments</div>
                    <div class="display-6">{{ $stats['enrollments'] }}</div>
                    <a class="stretched-link" href="{{ route('enrollment.index') }}"></a>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Grades</div>
                    <div class="display-6">{{ $stats['grades'] }}</div>
                    <a class="stretched-link" href="{{ route('grades.index') }}"></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
