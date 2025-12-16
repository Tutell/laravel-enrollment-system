@extends('layouts.master')

@section('title', 'Students – Manage by Grade & Section')
@section('page-title', 'Students – Manage by Grade & Section')
@section('page-description', 'View and manage students grouped by grade level and section')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Manage by Grade & Section</li>
@endsection

@section('content')
<div class="row">
    @foreach($gradeLevels as $gl)
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0">Grade {{ $gl }}</h2>
                    <span class="text-muted">Sections: {{ count($sectionsByGrade[$gl] ?? []) }}</span>
                </div>
                <div class="card-body">
                    @php $sections = $sectionsByGrade[$gl] ?? collect(); @endphp
                    @if($sections->isEmpty())
                        <div class="text-muted">No sections available for Grade {{ $gl }}.</div>
                    @else
                        <div class="row g-3">
                            @foreach($sections as $section)
                                <div class="col-12">
                                    <div class="card border-0">
                                        <div class="card-header bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $section->section_name }}</strong>
                                                    <span class="badge bg-secondary ms-2">Capacity {{ $section->capacity }}</span>
                                                </div>
                                                <div>
                                                    <a href="{{ route('sections.show', $section) }}" class="btn btn-sm btn-outline-secondary">View Section</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $students = $section->students;
                                                $canEdit = ($role === 'admin')
                                                    || ($role === 'teacher' && (in_array($section->section_ID, $allowedSectionIds) || in_array($section->grade_level, $allowedGradeLevels)));
                                            @endphp
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Student</th>
                                                            <th>LRN</th>
                                                            <th>Current Grade</th>
                                                            <th>Type</th>
                                                            <th>Score</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($students as $student)
                                                            @php
                                                                $enr = $student->enrollments->first();
                                                                $existing = $student->grades->where('type','Final')->sortByDesc('date_recorded')->first();
                                                                $gradeId = optional($existing)->grade_id;
                                                                $currentScore = optional($existing)->score;
                                                            @endphp
                                                            <tr>
                                                                <td>
                                                                    <a href="{{ route('students.show', $student) }}">{{ $student->first_name }} {{ $student->last_name }}</a>
                                                                    <div class="text-muted small">{{ optional($student->account)->username ?? '—' }}</div>
                                                                </td>
                                                                <td><code>{{ $student->lrn ?? '—' }}</code></td>
                                                                <td>{{ $currentScore ?? '—' }}</td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm" value="Final" disabled>
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control form-control-sm"
                                                                           min="0" max="100" step="1" inputmode="numeric" pattern="\\d*"
                                                                           value="{{ $currentScore ?? '' }}" disabled>
                                                                    <div class="invalid-feedback">Enter a whole number 0–100</div>
                                                                </td>
                                                                <td>
                                                                    <button class="btn btn-sm btn-outline-secondary" disabled>Read-only</button>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr><td colspan="6" class="text-muted">No students in this section.</td></tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
<div id="op-status" class="d-none">
    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
    <span class="ms-2 text-muted">Saving…</span>
  </div>
@endsection

@push('scripts')
<script>
(function(){
    const status = document.getElementById('op-status');
    function showStatus() { status.classList.remove('d-none'); }
    function hideStatus() { status.classList.add('d-none'); }
    function confirmSave() {
        return window.confirm('Save grade update?');
    }
    function token() {
        const el = document.querySelector('meta[name="csrf-token"]');
        return el ? el.getAttribute('content') : '{{ csrf_token() }}';
    }
    document.querySelectorAll('.save-btn').forEach(function(btn){
        btn.addEventListener('click', async function(){
            const enrollmentId = btn.getAttribute('data-enrollment-id');
            const gradeId = btn.getAttribute('data-grade-id');
            const input = btn.closest('tr').querySelector('.grade-input');
            const scoreVal = input.value;
            // validate integer
            if (!/^\d+$/.test(scoreVal) || parseInt(scoreVal,10) < 0 || parseInt(scoreVal,10) > 100) {
                input.classList.add('is-invalid');
                alert('Please enter a whole number between 0 and 100.');
                return;
            }
            if (!confirmSave()) return;
            showStatus();
            const payload = {
                enrollment_id: enrollmentId,
                type: 'Final',
                score: scoreVal,
                weight: 1,
                date_recorded: new Date().toISOString(),
            };
            try {
                const url = gradeId ? ('{{ route("grades.update", 0) }}'.replace('/0', '/'+gradeId)) : '{{ route("grades.store") }}';
                const method = gradeId ? 'POST' : 'POST';   
                const body = new URLSearchParams(payload);
                if (gradeId) {
                    body.append('_method', 'PUT');
                }
                const resp = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type':'application/x-www-form-urlencoded',
                        'Accept':'text/html,application/json',
                        'X-CSRF-TOKEN': token(),
                    },
                    body,
                    credentials: 'same-origin',
                });
            hideStatus();
                const text = await resp.text();
                if (!resp.ok) {
                    try {
                        const data = JSON.parse(text);
                        if (data && data.errors) {
                            const msgs = Object.values(data.errors).flat().join('\n');
                            alert('Failed to save grade:\n'+msgs);
                        } else {
                            alert('Failed to save grade ('+resp.status+').');
                        }
                    } catch (e) {
                        alert('Failed to save grade ('+resp.status+').');
                    }
                    return;
                }
                alert('Grade saved.');
                location.reload();
            } catch (e) {
                hideStatus();
                alert('Error saving grade.');
            }
        });
    });
    // input sanitization
    document.querySelectorAll('.grade-input').forEach(function(input){
        input.addEventListener('input', function(){
            input.value = input.value.replace(/[^0-9]/g, '');
            const v = input.value.trim();
            const n = v === '' ? null : parseInt(v,10);
            if (n === null) { input.classList.remove('is-invalid'); return; }
            if (isNaN(n) || n < 0 || n > 100) { input.classList.add('is-invalid'); return; }
            input.classList.remove('is-invalid');
            input.value = String(n);
        });
        input.addEventListener('blur', function(){
            const v = input.value.trim();
            if (v === '') { input.classList.remove('is-invalid'); return; }
            if (!/^\d+$/.test(v)) { input.classList.add('is-invalid'); return; }
            const n = parseInt(v,10);
            if (isNaN(n) || n < 0 || n > 100) { input.classList.add('is-invalid'); return; }
            input.classList.remove('is-invalid');
            input.value = String(n);
        });
    });
})();
</script>
@endpush
