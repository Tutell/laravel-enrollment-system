@extends('layouts.master')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-1">{{ $academicYear->school_year }} – {{ $academicYear->semester }}</h1>
            <div class="text-muted small">
                Status: {{ $academicYear->is_active ? 'Active' : 'Inactive' }}
            </div>
        </div>
        <div>
            <a href="{{ route('academic-years.edit',$academicYear) }}" class="btn btn-warning">Edit Academic Year</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach($yearLevels as $yl)
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Grade {{ $yl->grade_level }}</div>
                        <div class="h4 mb-0 fw-semibold">{{ $studentsByGrade[$yl->grade_level] ?? 0 }} Students</div>
                        <div class="small text-muted">{{ $sectionsByGrade[$yl->grade_level] ?? 0 }} Section(s)</div>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                        <i class="bi bi-collection text-info"></i>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h6 mb-0 fw-semibold text-primary">Year Level Overview</h2>
                <a href="{{ route('year-levels.index') }}" class="btn btn-outline-primary btn-sm">
                    Manage Year Levels
                </a>
            </div>
            <p class="text-muted mb-0">Use Year Level Management to configure advisers and review sections per grade.</p>
        </div>
    </div>
</div>
@endsection
