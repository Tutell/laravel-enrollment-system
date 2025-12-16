@extends('layouts.master')

@section('title', 'Student Details')

@section('page-title', 'Student Details')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-0">{{ $student->first_name }} {{ $student->last_name }}</h3>
                <small class="text-muted">ID: {{ $student->student_ID }}</small>
            </div>
            <div>
                @php $role = optional(auth()->user())->role; @endphp
                @if(in_array(strtolower($role ?? ''), ['admin','teacher'], true))
                    <form method="POST" action="{{ route('students.auto-enroll', $student) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-lightning-charge me-1"></i> Auto-enroll by Grade
                        </button>
                    </form>
                @endif
                @if(strtolower($role ?? '') === 'admin')
                    <a href="{{ route('students.subjects', $student) }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-book me-1"></i> Manual Subject Assignment
                    </a>
                @endif
                <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-secondary">Back to list</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <ul class="list-group mb-4">
                    <li class="list-group-item"><strong>First name:</strong> {{ $student->first_name }}</li>
                    <li class="list-group-item"><strong>Last name:</strong> {{ $student->last_name }}</li>
                    <li class="list-group-item"><strong>LRN:</strong> {{ $student->lrn ?? 'N/A' }}</li>
                    <li class="list-group-item"><strong>Email:</strong> {{ $student->email ?? 'N/A' }}</li>
                    <li class="list-group-item"><strong>Contact:</strong> {{ $student->phone ?? 'N/A' }}</li>
                    <li class="list-group-item"><strong>Address:</strong> {{ $student->address ?? 'N/A' }}</li>
                    <li class="list-group-item"><strong>Gender:</strong> {{ $student->gender ?? 'N/A' }}</li>
                    <li class="list-group-item"><strong>Birthdate:</strong> {{ optional($student->birthdate)->toDateString() ?? 'N/A' }}</li>
                    <li class="list-group-item"><strong>Section:</strong> {{ $student->section->section_name ?? 'Not assigned' }}</li>
                </ul>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light fw-semibold">Subjects</div>
                    <div class="card-body">
                        @php
                            $subjects = $student->enrollments->map(function($enr){ return optional($enr->course)->subject; })->filter()->unique('subject_id');
                        @endphp
                        @if($subjects->count())
                            <ul class="list-group list-group-flush">
                                @foreach($subjects as $subj)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ $subj->name }}</span>
                                        <span class="badge bg-primary">Grade {{ $subj->grade_level ?? '—' }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-muted">No subjects assigned.</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h5>Grades</h5>
                @if($student->grades && $student->grades->isNotEmpty())
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Score</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($student->grades as $grade)
                            <tr>
                                <td>{{ $grade->type }}</td>
                                <td>{{ $grade->score }}</td>
                                <td>{{ optional($grade->date_recorded)->toDateTimeString() }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No grades recorded.</p>
                @endif
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-light fw-semibold">Enrolled Subjects</div>
                    <div class="card-body">
                        <div id="enrolledSubjectsLoading" class="d-flex align-items-center mb-2" style="display:none;">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status" aria-hidden="true"></div>
                            <span class="text-muted">Loading enrolled subjects…</span>
                        </div>
                        <div class="table-responsive" id="enrolledSubjectsTableWrapper">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Subject ID</th>
                                        <th>Subject Name</th>
                                        <th>Enrollment Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="enrolledSubjectsBody">
                                    @php
                                        $initialEnrollments = $student->enrollments->sortByDesc('enrollment_date');
                                        $initialRows = $initialEnrollments->map(function($enr) {
                                            $subj = optional(optional($enr->course)->subject);
                                            return [
                                                'subject_id' => $subj->subject_id ?? null,
                                                'subject_name' => $subj->name ?? null,
                                                'enrollment_date' => optional($enr->enrollment_date)->format('Y-m-d') ?? null,
                                                'status' => $enr->status ?? null,
                                            ];
                                        })->take(10);
                                    @endphp
                                    @forelse($initialRows as $r)
                                        <tr>
                                            <td>{{ $r['subject_id'] ?? '—' }}</td>
                                            <td>{{ $r['subject_name'] ?? '—' }}</td>
                                            <td>{{ $r['enrollment_date'] ?? '—' }}</td>
                                            <td>{{ $r['status'] ?? '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-muted">No subjects enrolled.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-between align-items-center mt-2" id="enrolledSubjectsPager" style="display:none;">
                                <button class="btn btn-outline-secondary btn-sm" id="enrolledPrevBtn" disabled>&laquo; Prev</button>
                                <small class="text-muted" id="enrolledPageInfo"></small>
                                <button class="btn btn-outline-secondary btn-sm" id="enrolledNextBtn" disabled>Next &raquo;</button>
                            </div>
                            <div class="text-muted mt-2" id="enrolledSubjectsEmpty" style="display:none;">No subjects enrolled.</div>
                        </div>
                        <script>
                            (function () {
                                const run = function () {
                                    const endpoint = "{{ route('students.enrolled-subjects', $student) }}";
                                    const loading = document.getElementById('enrolledSubjectsLoading');
                                    const wrapper = document.getElementById('enrolledSubjectsTableWrapper');
                                    const tbody = document.getElementById('enrolledSubjectsBody');
                                    const pager = document.getElementById('enrolledSubjectsPager');
                                    const prevBtn = document.getElementById('enrolledPrevBtn');
                                    const nextBtn = document.getElementById('enrolledNextBtn');
                                    const pageInfo = document.getElementById('enrolledPageInfo');
                                    const emptyMsg = document.getElementById('enrolledSubjectsEmpty');

                                    let currentPage = 1;

                                    async function loadSubjects(page = 1) {
                                        loading.style.display = '';
                                        // keep wrapper visible to avoid empty screen if fetch fails
                                        emptyMsg.style.display = 'none';
                                        pager.style.display = 'none';
                                        tbody.innerHTML = '';
                                        try {
                                            const url = endpoint + '?page=' + page + '&per_page=10';
                                            const resp = await fetch(url, {
                                                headers: { 'Accept': 'application/json' },
                                                credentials: 'same-origin',
                                            });
                                            if (!resp.ok) {
                                                throw new Error('HTTP ' + resp.status);
                                            }
                                            const ct = resp.headers.get('Content-Type') || '';
                                            if (!ct.includes('application/json')) {
                                                throw new Error('Unexpected content type: ' + ct);
                                            }
                                            const json = await resp.json();
                                            const rows = json.data || [];
                                            if (rows.length === 0) {
                                                emptyMsg.style.display = '';
                                                pageInfo.textContent = '';
                                                prevBtn.disabled = true;
                                                nextBtn.disabled = true;
                                                pager.style.display = '';
                                            } else {
                                                rows.forEach(function (r) {
                                                    const tr = document.createElement('tr');
                                                    const tdId = document.createElement('td'); tdId.textContent = r.subject_id ?? '—';
                                                    const tdName = document.createElement('td'); tdName.textContent = r.subject_name ?? '—';
                                                    const tdDate = document.createElement('td'); tdDate.textContent = r.enrollment_date ?? '—';
                                                    const tdStatus = document.createElement('td'); tdStatus.textContent = r.status ?? '—';
                                                    tr.appendChild(tdId); tr.appendChild(tdName); tr.appendChild(tdDate); tr.appendChild(tdStatus);
                                                    tbody.appendChild(tr);
                                                });
                                                pager.style.display = '';
                                                const meta = json.meta || {};
                                                currentPage = meta.current_page || page;
                                                const last = meta.last_page || currentPage;
                                                pageInfo.textContent = 'Page ' + currentPage + ' of ' + last + ' · Total ' + (meta.total || rows.length);
                                                prevBtn.disabled = !json.links || !json.links.prev;
                                                nextBtn.disabled = !json.links || !json.links.next;
                                                prevBtn.onclick = function () { if (currentPage > 1) loadSubjects(currentPage - 1); };
                                                nextBtn.onclick = function () { if (currentPage < last) loadSubjects(currentPage + 1); };
                                            }
                                        } catch (e) {
                                            emptyMsg.textContent = (e && e.message) ? ('Failed to load subjects (' + e.message + ').') : 'Failed to load subjects.';
                                            emptyMsg.style.display = '';
                                            pager.style.display = 'none';
                                        } finally {
                                            loading.style.display = 'none';
                                        }
                                    }

                                    loadSubjects(1);
                                };
                                if (document.readyState === 'loading') {
                                    document.addEventListener('DOMContentLoaded', run);
                                } else {
                                    run();
                                }
                            })();
                        </script>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <h5>Parent / Guardian Background</h5>
                @if($student->guardians && $student->guardians->isNotEmpty())
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Relationship</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Occupation</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($student->guardians as $guardian)
                            <tr>
                                <td>{{ $guardian->full_name }}</td>
                                <td>{{ $guardian->relationship }}</td>
                                <td>{{ $guardian->contact_number ?? 'N/A' }}</td>
                                <td>{{ $guardian->email ?? 'N/A' }}</td>
                                <td>{{ $guardian->occupation ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted mb-2">No parent / guardian information recorded.</p>
                    <a href="{{ route('guardians.create', $student) }}" class="btn btn-sm btn-outline-primary">
                        Add Parent / Guardian
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
