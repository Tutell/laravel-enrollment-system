@extends('layouts.master')

@section('title', 'Student Enrollment')

@section('page-title', 'Student Enrollment by Grade Level')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-1">Total Students</h5>
                <h2 class="text-primary mb-0">{{ $totalStudents }}</h2>
            </div>
            <div class="col-md-6">
                <h5 class="mb-1">Total Enrolled</h5>
                <h2 class="text-success mb-0">{{ $totalEnrolled }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Grade Level Tabs -->
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
            @foreach($gradeLevels as $index => $gradeLevel)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                            id="grade{{ $gradeLevel }}-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#grade{{ $gradeLevel }}" 
                            type="button">
                        Grade {{ $gradeLevel }}
                        <span class="badge bg-info ms-2">{{ $enrollmentData[$gradeLevel]['totalStudents'] }}</span>
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content">
            @foreach($gradeLevels as $index => $gradeLevel)
                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                     id="grade{{ $gradeLevel }}" 
                     role="tabpanel">
                    <h5 class="mb-3">Grade {{ $gradeLevel }} - Sections</h5>

                    @if($enrollmentData[$gradeLevel]['sections']->count() > 0)
                        <div class="accordion" id="grade{{ $gradeLevel }}Accordion">
                            @foreach($enrollmentData[$gradeLevel]['sections'] as $section)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#section{{ $section->section_ID }}">
                                            <i class="bi bi-people-fill me-2 text-primary"></i>
                                            <strong>{{ $section->section_name }}</strong>
                                            <span class="badge bg-secondary ms-auto me-2">{{ $section->students->count() }} / {{ $section->capacity }}</span>
                                        </button>
                                    </h2>
                                    <div id="section{{ $section->section_ID }}" 
                                         class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                                         data-bs-parent="#grade{{ $gradeLevel }}Accordion">
                                        <div class="accordion-body p-0">
                                            @if($section->students->count() > 0)
                                                <table class="table table-hover mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Student ID</th>
                                                            <th>Name</th>
                                                            <th>Account</th>
                                                            <th>Gender</th>
                                                            <th>Birthdate</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($section->students as $student)
                                                            <tr>
                                                                <td><code>{{ $student->student_id }}</code></td>
                                                                <td>
                                                                    <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                                                </td>
                                                                <td>
                                                                    @if($student->account)
                                                                        <span class="badge bg-primary">{{ $student->account->username }}</span>
                                                                    @else
                                                                        <span class="badge bg-danger">No Account</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $student->gender }}</td>
                                                                <td>{{ $student->birthdate?->format('M d, Y') }}</td>
                                                                <td>
                                                                    <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-info" title="View">
                                                                        <i class="bi bi-eye"></i>
                                                                    </a>
                                                                    <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-warning" title="Edit">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </a>
                                                                    <form action="{{ route('students.destroy', $student) }}" method="POST" style="display:inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <div class="p-4 text-center text-muted">
                                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                                    <p class="mt-2">No students assigned to this section</p>
                                                    <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm">Add Student</a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No sections for Grade {{ $gradeLevel }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-4">
    <a href="{{ route('students.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add New Student
    </a>
    <a href="{{ route('students.index') }}" class="btn btn-secondary">
        <i class="bi bi-list me-2"></i>View All Students
    </a>
</div>

@endsection
