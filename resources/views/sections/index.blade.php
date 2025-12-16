@extends('layouts.master')

@section('title', 'Sections Management')

@section('page-title', 'Sections Management')

@section('page-description', 'Manage class sections, assign teachers, and organize student groups')

@push('styles')
<style>
.sections-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.stat-inline {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    align-items: stretch;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    background-color: var(--surface);
    box-shadow: var(--shadow-sm);
}

.stat-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 999px;
    background-color: var(--primary-light);
    color: var(--primary);
}

.stat-label {
    font-size: 0.85rem;
    color: var(--text-secondary);
    line-height: 1;
}

.stat-value {
    font-weight: 700;
    font-size: 1rem;
    color: var(--text-primary);
    line-height: 1;
}

.sections-page-grid {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.sections-shell {
    display: flex;
    flex-direction: column;
}

.sections-header {
    position: sticky;
    top: 0;
    z-index: 2;
    background-color: var(--surface);
}

.sections-grid-wrapper {
    flex: 1 1 auto;
    overflow-y: visible;
    overflow-x: hidden;
    padding: 2rem;
    background-color: var(--surface);
}

.sections-grid-wrapper:focus-visible {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}

.sections-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.25rem;
}

.section-card {
    background-color: var(--surface);
    border-radius: var(--radius-md);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    padding: 1.25rem 1.25rem 0.75rem;
    display: flex;
    flex-direction: column;
    min-height: 100%;
    transition: box-shadow 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
}

.section-card:hover,
.section-card:focus-within {
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-light);
    transform: translateY(-2px);
}

.section-card:focus-within {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}

.section-card-header {
    margin-bottom: 0.75rem;
}

.section-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
}

.section-subtitle {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.section-icon {
    width: 36px;
    height: 36px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: var(--primary-light);
    color: var(--primary);
}

.section-grade {
    font-size: 0.875rem;
    font-weight: 600;
}

.section-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 0.75rem 1.5rem;
    margin-bottom: 0.75rem;
}

.section-meta-item dt {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--text-secondary);
    margin-bottom: 0.15rem;
}

.section-meta-item dd {
    margin: 0;
    font-size: 0.875rem;
    color: var(--text-primary);
}

.section-footer {
    margin-top: auto;
    padding-top: 0.75rem;
    border-top: 1px solid var(--border);
    gap: 1rem;
    flex-wrap: wrap;
}

.section-teacher-name {
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--text-primary);
}

.section-teacher-email {
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.section-teacher-avatar {
    width: 32px;
    height: 32px;
    border-radius: 999px;
    background-color: var(--primary-light);
    color: var(--primary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.5rem;
}

.section-teacher-unassigned {
    font-size: 0.85rem;
    color: var(--danger);
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
}

.section-capacity {
    min-width: 140px;
}

.section-capacity-progress {
    height: 6px;
    background-color: rgba(95, 99, 104, 0.12);
}

.section-capacity-label {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--text-secondary);
}

.section-actions {
    margin-top: 0.75rem;
}

.section-actions .btn {
    min-width: 2.25rem;
}

.section-actions .btn:focus-visible {
    box-shadow: 0 0 0 0.15rem rgba(26, 115, 232, 0.5);
}

.sections-empty-state {
    padding: 3rem 1.5rem;
}

.sections-empty-state-icon {
    font-size: 2.5rem;
    color: var(--text-secondary);
}

@media (max-width: 575.98px) {
    .sections-grid-wrapper {
        padding: 1rem;
    }

    .section-card {
        padding: 1rem 1rem 0.75rem;
    }
}
</style>
@endpush

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Sections</li>
@endsection

@section('page-actions')
    <a href="{{ route('sections.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add New Section
    </a>
@endsection

@section('content')
<div class="sections-container">
<div class="stat-inline mb-4">
    <div class="stat-item">
        <span class="stat-icon"><i class="bi bi-grid-3x3-gap"></i></span>
        <div>
            <div class="stat-label">Total Sections</div>
            <div class="stat-value">{{ $totalSections ?? '0' }}</div>
        </div>
    </div>
    <div class="stat-item">
        <span class="stat-icon"><i class="bi bi-person-check"></i></span>
        <div>
            <div class="stat-label">Teachers Assigned</div>
            <div class="stat-value">{{ $assignedTeachers ?? '0' }}</div>
        </div>
    </div>
    <div class="stat-item">
        <span class="stat-icon"><i class="bi bi-people"></i></span>
        <div>
            <div class="stat-label">Students Enrolled</div>
            <div class="stat-value">{{ $totalStudents ?? '0' }}</div>
        </div>
    </div>
    <div class="stat-item">
        <span class="stat-icon"><i class="bi bi-calendar-week"></i></span>
        <div>
            <div class="stat-label">Academic Year</div>
            <div class="stat-value">{{ $activeYear }}</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow">
    <div class="card-body p-0 sections-shell">
        @foreach([7,8,9,10] as $g)
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="h5 mb-0">Grade {{ $g }}</h2>
                <div class="d-flex align-items-center gap-2">
                    <label for="sectionSelect{{ $g }}" class="visually-hidden">Select section for Grade {{ $g }}</label>
                    <select id="sectionSelect{{ $g }}" class="form-select form-select-sm sectionSelectGrade" 
                            aria-label="Select section for Grade {{ $g }}" data-grade="{{ $g }}">
                        <option value="">All sections</option>
                        @foreach(($gradeSections[$g] ?? collect()) as $opt)
                            <option value="{{ $opt->section_id }}">{{ $opt->section_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped" role="table" aria-label="Sections table for Grade {{ $g }}">
                    <thead>
                        <tr>
                            <th scope="col">Section</th>
                            <th scope="col">Adviser</th>
                            <th scope="col">Students/Capacity</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($gradeSections[$g] ?? collect()) as $section)
                        @php
                            $studentCount = (int) ($section->students->count() ?? 0);
                            $capacity = (int) ($section->capacity ?? 40);
                        @endphp
                        <tr>
                            <td>{{ $section->section_name }}</td>
                            <td>
                                @if($section->teacher)
                                    {{ $section->teacher->first_name }} {{ $section->teacher->last_name }}
                                @else
                                    <span class="text-danger">Unassigned</span>
                                @endif
                            </td>
                            <td>{{ $studentCount }}/{{ $capacity }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('sections.show', $section) }}" class="btn btn-sm btn-outline-primary" aria-label="View section {{ $section->section_name }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('sections.edit', $section) }}" class="btn btn-sm btn-outline-warning" aria-label="Edit section {{ $section->section_name }}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ $section->section_id }}')" aria-label="Delete section {{ $section->section_name }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-muted">No sections for Grade {{ $g }}.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
        <div class="p-4 border-bottom sections-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="searchInput" 
                               placeholder="Search sections by name, teacher, or grade level..."
                               aria-label="Search sections by name, teacher, or grade level">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end gap-2">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" 
                                    data-bs-toggle="dropdown" aria-expanded="false"
                                    aria-label="Filter sections by grade level">
                                <i class="bi bi-funnel me-1"></i> Filter by Grade
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">All Grades</a></li>
                                <li><hr class="dropdown-divider"></li>
                                @foreach(['7', '8', '9', '10', '11', '12'] as $grade)
                                <li><a class="dropdown-item" href="#">{{ $grade }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        <button class="btn btn-outline-primary">
                            <i class="bi bi-printer me-1"></i> Print List
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="sections-grid-wrapper" role="region" aria-label="Sections list" tabindex="0">
            @if($sections->count() === 0)
            <div class="sections-empty-state text-center">
                <div class="py-4">
                    <div class="sections-empty-state-icon mb-3" aria-hidden="true">
                        <i class="bi bi-grid-3x3-gap"></i>
                    </div>
                    <h5 class="text-muted">No sections found</h5>
                    <p class="text-muted mb-4">Create your first section to organize students.</p>
                    <a href="{{ route('sections.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Create Section
                    </a>
                </div>
            </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($sections->hasPages())
        <div class="p-4 border-top">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Showing {{ $sections->firstItem() }} to {{ $sections->lastItem() }} of {{ $sections->total() }} sections
                    </small>
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Page Link --}}
                        @if($sections->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">&laquo;</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $sections->previousPageUrl() }}" aria-label="Previous">
                                    &laquo;
                                </a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach(range(1, $sections->lastPage()) as $page)
                            @if($page == $sections->currentPage())
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $sections->url($page) }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if($sections->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $sections->nextPageUrl() }}" aria-label="Next">
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
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Confirm Deletion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this section?</p>
                <p class="text-muted small mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    This action cannot be undone. All students in this section will need to be reassigned.
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Section</button>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));

        // Apply dynamic widths to progress bars (avoid inline CSS with Blade expressions)
        const progressBars = document.querySelectorAll('.progress-bar[data-width]');
        progressBars.forEach(el => {
            const pctRaw = parseInt(el.getAttribute('data-width'), 10);
            const pct = Math.max(0, Math.min(100, isNaN(pctRaw) ? 0 : pctRaw));
            el.style.width = pct + '%';
        });
        document.querySelectorAll('.sectionSelectGrade').forEach(function(sel) {
            sel.addEventListener('change', function() {
                const id = this.value;
                if (id) {
                    window.location.href = '/sections/' + id;
                }
            });
        });

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const cards = document.querySelectorAll('.section-card');
                
                cards.forEach(card => {
                    const text = card.textContent.toLowerCase();
                    card.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }
    });

    function confirmDelete(sectionId) {
        // Set the form action
        document.getElementById('deleteForm').action = `/sections/${sectionId}`;
        
        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>
@endsection
