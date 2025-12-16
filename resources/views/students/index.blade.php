@extends('layouts.master')

@section('title', 'Students Management')

@section('page-title', 'Students Management')

@section('page-description', 'View and manage all student records in the enrollment system')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Students</li>
@endsection

@section('page-actions')
    <a href="{{ route('students.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add New Student
    </a>
@endsection

@section('content')
<div class="row mb-4">
    <!-- Statistics Cards -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                    <i class="bi bi-people-fill text-primary fs-4"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ $totalStudents ?? '0' }}</h3>
                    <small class="text-muted">Total Students</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                    <i class="bi bi-check-circle-fill text-success fs-4"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ $activeStudents ?? '0' }}</h3>
                    <small class="text-muted">Active Students</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                    <i class="bi bi-gender-ambiguous text-info fs-4"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ $maleStudents ?? '0' }}</h3>
                    <small class="text-muted">Male Students</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 p-3 rounded me-3">
                    <i class="bi bi-gender-female text-warning fs-4"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ $femaleStudents ?? '0' }}</h3>
                    <small class="text-muted">Female Students</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow">
    <div class="card-body p-0">
        <!-- Filters and Search -->
        <div class="p-4 border-bottom">
            <form method="GET" action="{{ route('students.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" name="q" value="{{ $q }}" placeholder="Name, email, or username">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">All</option>
                            <option value="Male" @selected($gender==='Male')>Male</option>
                            <option value="Female" @selected($gender==='Female')>Female</option>
                            <option value="Other" @selected($gender==='Other')>Other</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="active" @selected($status==='active')>Active</option>
                            <option value="inactive" @selected($status==='inactive')>Inactive</option>
                            <option value="pending" @selected($status==='pending')>Pending</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Grade</label>
                        <select name="grade_level" class="form-select">
                            <option value="">All</option>
                            @foreach(($grades ?? []) as $g)
                                <option value="{{ $g }}" @selected($gradeLevel==$g)>Grade {{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Section</label>
                        <select name="section_id" class="form-select">
                            <option value="">All</option>
                            @foreach(($sectionsList ?? []) as $sec)
                                <option value="{{ $sec->section_ID }}" @selected($sectionId==$sec->section_ID)>{{ $sec->section_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Reset</a>
                        <button class="btn btn-primary" type="submit">Apply</button>
                        <a href="{{ route('students.export', request()->query()) }}" class="btn btn-outline-primary">
                            <i class="bi bi-download me-1"></i> Export CSV
                        </a>
                        <a href="{{ route('students.index', request()->query()) }}" class="btn btn-outline-info">
                            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                        </a>
                    </div>
                    <div class="col-12 d-flex justify-content-between align-items-center mt-2">
                        <div class="text-muted small">Manage students from this list. Archived students are accessible in the Archive Bin.</div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('students.archive') }}" class="btn btn-outline-dark" id="archiveBinDropZone" aria-label="Archive Bin (drop students here)">
                                <i class="bi bi-archive me-1"></i> Archive Bin
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Students Table -->
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                            </div>
                        </th>
                        <th class="border-0 text-primary fw-semibold">
                            <a href="{{ route('students.index', array_merge(request()->query(), ['sort'=>'student_id','dir'=> ($sort==='student_id' && ($dir==='asc')) ? 'desc' : 'asc'])) }}" class="text-decoration-none text-primary">Student ID</a>
                        </th>
                        <th class="border-0 text-primary fw-semibold">
                            <a href="{{ route('students.index', array_merge(request()->query(), ['sort'=>'name','dir'=> ($sort==='name' && ($dir==='asc')) ? 'desc' : 'asc'])) }}" class="text-decoration-none text-primary">Full Name</a>
                        </th>
                        <th class="border-0 text-primary fw-semibold">
                            <a href="{{ route('students.index', array_merge(request()->query(), ['sort'=>'grade_level','dir'=> ($sort==='grade_level' && ($dir==='asc')) ? 'desc' : 'asc'])) }}" class="text-decoration-none text-primary">Grade Level</a>
                        </th>
                        <th class="border-0 text-primary fw-semibold">
                            <a href="{{ route('students.index', array_merge(request()->query(), ['sort'=>'section','dir'=> ($sort==='section' && ($dir==='asc')) ? 'desc' : 'asc'])) }}" class="text-decoration-none text-primary">Section</a>
                        </th>
                        <th class="border-0 text-primary fw-semibold">Contact</th>
                        <th class="border-0 text-primary fw-semibold">Status</th>
                        <th class="border-0 text-primary fw-semibold">Last Loaded</th>
                        <th class="border-0 text-primary fw-semibold text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr class="align-middle draggable-student" @if($student->status !== 'archived') draggable="true" @endif data-student-id="{{ $student->student_ID }}">
                        <td>
                            <div class="form-check">
                                <input class="form-check-input student-check" type="checkbox" value="{{ $student->student_ID }}" aria-label="Select student {{ $student->student_ID }}">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                    <i class="bi bi-person-badge text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $student->student_ID }}</div>
                                    <small class="text-muted">ID</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $student->first_name }} {{ $student->last_name }}</div>
                            <small class="text-muted">{{ $student->email ?? 'No email' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                Grade {{ $student->grade_level ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            @if($student->section)
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 p-1 rounded me-2">
                                        <i class="bi bi-grid-3x3 text-success"></i>
                                    </div>
                                    <span>{{ $student->section->section_name }}</span>
                                </div>
                            @else
                                <span class="text-muted">Not assigned</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $student->phone ?? 'No phone' }}</small><br>
                            <small class="text-muted">{{ $student->address ? Str::limit($student->address, 20) : 'No address' }}</small>
                        </td>
                        <td>
                            @if($student->status === 'archived')
                                <span class="badge bg-dark bg-opacity-10 text-dark border border-dark" data-bs-toggle="tooltip" title="Archived – hidden from active roster">
                                    <i class="bi bi-archive-fill me-1"></i> Archived
                                </span>
                            @else
                                @php
                                    $enrolledCount = $student->enrollments->where('status','Enrolled')->count();
                                    $issues = !$student->section || !optional($student->section)->grade_level;
                                    $statusIcon = $enrolledCount > 0 ? 'bi-check-circle-fill text-success' : ($issues ? 'bi-exclamation-triangle-fill text-warning' : 'bi-dash-circle text-muted');
                                    $statusLabel = $enrolledCount > 0 ? 'Enrolled' : ($issues ? 'Enrollment issue' : 'Not enrolled');
                                    $lastProcessed = $student->enrollments->max('processed_at');
                                    $tooltipText = ($enrolledCount.' enrolled subject(s)') . ($lastProcessed ? ' • updated '.$lastProcessed : '');
                                @endphp
                                <span class="d-inline-flex align-items-center" data-bs-toggle="tooltip" title="{{ $tooltipText }}" aria-label="{{ $statusLabel }}">
                                    <i class="bi {{ $statusIcon }} me-1"></i> {{ $statusLabel }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @php $lp = $student->enrollments->max('processed_at'); @endphp
                            <small class="text-muted">{{ $lp ?: '—' }}</small>
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View Details" aria-label="View details">
                                    <i class="bi bi-eye"></i>
                                </a>

                                @if($student->status !== 'active')
                                <form action="{{ route('students.approve', $student) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Approve & activate" aria-label="Approve and activate">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                @endif
                                
                                <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Edit" aria-label="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                <form action="{{ route('students.auto-enroll', $student) }}" method="POST" class="d-inline auto-enroll-form">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Add subjects" aria-label="Add subjects">
                                        <span class="add-label"><i class="bi bi-plus-circle me-1"></i>Add</span>
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    </button>
                                </form>

                                <form action="{{ route('students.drop-all', $student) }}" method="POST" class="d-inline drop-all-form">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Drop all subjects" aria-label="Drop all subjects">
                                        <span class="drop-label"><i class="bi bi-dash-circle me-1"></i>Drop</span>
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    </button>
                                </form>

                                @php $droppedCount = $student->enrollments->where('status','Dropped')->count(); @endphp
                                @if($droppedCount > 0 && $enrolledCount === 0 && auth()->check() && strtolower(auth()->user()->role ?? '') === 'admin')
                                <form action="{{ route('students.re-enroll', $student) }}" method="POST" class="d-inline re-enroll-form">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Re-enroll and restore previous enrollments" aria-label="Re-enroll">
                                        <span class="reenroll-label"><i class="bi bi-arrow-repeat me-1"></i>Re-enroll</span>
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    </button>
                                </form>
                                @endif

                                <form action="{{ route('students.archive.post', $student) }}" method="POST" class="d-inline archive-form">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Archive" aria-label="Archive">
                                        <span class="archive-label"><i class="bi bi-archive me-1"></i>Archive</span>
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="py-5">
                                <i class="bi bi-people display-4 text-muted mb-3"></i>
                                <h5 class="text-muted">No students found</h5>
                                <p class="text-muted mb-4">Add your first student to get started</p>
                                <a href="{{ route('students.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-lg me-1"></i> Add Student
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($students->hasPages())
        <div class="p-4 border-top">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
                    </small>
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Page Link --}}
                        @if($students->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">&laquo;</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $students->previousPageUrl() }}" aria-label="Previous">
                                    &laquo;
                                </a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach(range(1, $students->lastPage()) as $page)
                            @if($page == $students->currentPage())
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $students->url($page) }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if($students->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $students->nextPageUrl() }}" aria-label="Next">
                                    &raquo;
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">&raquo;</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));

        // Select All functionality
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('tbody input.student-check[type="checkbox"]');
        
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        });

        const attachLoading = (form, labelSelector) => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Proceed with this action?')) {
                    e.preventDefault();
                    return;
                }
                const btn = form.querySelector('button');
                const label = form.querySelector(labelSelector);
                const spinner = form.querySelector('.spinner-border');
                if (btn && spinner && label) {
                    label.classList.add('d-none');
                    spinner.classList.remove('d-none');
                    btn.setAttribute('disabled', 'disabled');
                }
            });
        };
        document.querySelectorAll('.archive-form').forEach(f => attachLoading(f, '.archive-label'));
        document.querySelectorAll('.auto-enroll-form').forEach(f => attachLoading(f, '.add-label'));
        document.querySelectorAll('.drop-all-form').forEach(f => attachLoading(f, '.drop-label'));
        document.querySelectorAll('.re-enroll-form').forEach(f => attachLoading(f, '.reenroll-label'));

        // Removed batch Add/Drop; use Archive Bin for archived records.

        // Drag-and-drop to Archive Bin
        const archiveBin = document.getElementById('archiveBinDropZone');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (archiveBin && csrfToken) {
            archiveBin.addEventListener('dragover', function (e) {
                e.preventDefault();
                archiveBin.classList.add('btn-dark');
            });
            archiveBin.addEventListener('dragleave', function () {
                archiveBin.classList.remove('btn-dark');
            });
            archiveBin.addEventListener('drop', async function (e) {
                e.preventDefault();
                archiveBin.classList.remove('btn-dark');
                const studentId = e.dataTransfer.getData('text/plain');
                if (!studentId) return;
                if (!confirm('Archive this student and move to Archive Bin?')) return;
                try {
                    const resp = await fetch(`{{ url('students') }}/${studentId}/archive`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: new URLSearchParams()
                    });
                    if (resp.ok) {
                        window.location.reload();
                    } else {
                        const txt = await resp.text();
                        alert('Failed to archive: ' + (txt || resp.status));
                    }
                } catch (err) {
                    alert('Network error. Please try again.');
                }
            });
        }
        document.querySelectorAll('tr.draggable-student[draggable="true"]').forEach(row => {
            row.addEventListener('dragstart', function (e) {
                const id = row.getAttribute('data-student-id');
                if (id) {
                    e.dataTransfer.setData('text/plain', id);
                }
            });
        });
    });
</script>
@endsection
