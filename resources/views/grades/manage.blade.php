@extends('layouts.master')

@section('title', 'Grades – Manage')
@section('page-title', 'Grades – Manage')
@section('page-description', 'View and edit student grades by grade level and section')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Grades – Manage</li>
@endsection

@section('content')
<form class="row g-3 mb-3" method="GET" action="{{ route('grades.manage') }}" aria-label="Filter grades">
    <div class="col-md-3">
        <label for="grade_level" class="form-label">Grade Level</label>
        <select id="grade_level" name="grade_level" class="form-select">
            <option value="">All</option>
            @foreach($gradeLevels as $gl)
                <option value="{{ $gl }}" @selected($gradeFilter == $gl)>Grade {{ $gl }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label for="section_id" class="form-label">Section</label>
        <select id="section_id" name="section_id" class="form-select">
            <option value="">All</option>
            @foreach($sections as $secOpt)
                <option value="{{ $secOpt->section_ID }}" @selected($sectionFilter == $secOpt->section_ID)>{{ $secOpt->section_name }} (G{{ $secOpt->grade_level }})</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label for="subject_id" class="form-label">Subject</label>
        <select id="subject_id" name="subject_id" class="form-select">
            <option value="">All</option>
            @foreach($subjects as $subject)
                <option value="{{ $subject->subject_ID }}" @selected($subjectFilter == $subject->subject_ID)>{{ $subject->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label for="academic_year_id" class="form-label">Academic Year</label>
        <select id="academic_year_id" name="academic_year_id" class="form-select">
            <option value="">All</option>
            @foreach($academicYears as $ay)
                <option value="{{ $ay->academic_year_ID }}" @selected($academicYearFilter == $ay->academic_year_ID)>{{ $ay->school_year }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label for="semester" class="form-label">Semester</label>
        <select id="semester" name="semester" class="form-select">
            <option value="">All</option>
            @foreach($semesters as $sem)
                <option value="{{ $sem }}" @selected($semesterFilter == $sem)>{{ $sem }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 align-self-end">
        <button class="btn btn-primary w-100" type="submit">Filter</button>
    </div>
</form>

<div class="row">
    @forelse($sections as $section)
        @php
            $canEdit = ($role === 'admin') || ($role === 'teacher' && (in_array($section->section_ID, $allowedSectionIds) || in_array($section->grade_level, $allowedGradeLevels)));
            $students = $section->students->sortBy(fn($s) => $s->last_name.' '.$s->first_name);
        @endphp
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Grade {{ $section->grade_level }}</strong>
                        <span class="ms-2">{{ $section->section_name }}</span>
                        <span class="badge bg-secondary ms-2">Students {{ $students->count() }}</span>
                    </div>
                    <div>
                        @if($canEdit)
                        <button class="btn btn-sm btn-primary js-save-all" data-section="{{ $section->section_ID }}">Save All</button>
                        @endif
                        <a href="{{ route('sections.show', $section) }}" class="btn btn-sm btn-outline-secondary">View Section</a>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($students as $student)
                        @php
                            $enrollments = $student->enrollments;
                            if ($subjectFilter) {
                                $enrollments = $enrollments->filter(function($enr) use ($subjectFilter) {
                                    return optional($enr->course)->subject_ID == $subjectFilter;
                                });
                            }
                            $enrollments = $enrollments->sortBy(function($enr){
                                return optional(optional($enr->course)->subject)->name ?? '';
                            });
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <button class="btn btn-link p-0 text-decoration-none js-toggle-student" type="button" aria-expanded="false" aria-controls="student-{{ $student->student_ID }}-panel">
                                    <span class="me-2 badge rounded-pill bg-secondary js-toggle-icon" data-state="collapsed">+</span>
                                    <span class="h6 mb-0">{{ $student->last_name }}, {{ $student->first_name }}</span>
                                    <small class="text-muted ms-2">LRN: {{ $student->lrn ?? '—' }}</small>
                                </button>
                                <span class="text-muted small">{{ optional($student->account)->Username }}</span>
                            </div>
                            <div id="student-{{ $student->student_ID }}-panel" class="collapse-panel collapsed" style="overflow:hidden; transition:max-height 0.25s ease; max-height:0;">
                            <div class="table-responsive pt-2">
                                <table class="table table-sm table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Learning Area</th>
                                            <th scope="col" aria-label="Quarter 1">Q1</th>
                                            <th scope="col" aria-label="Quarter 2">Q2</th>
                                            <th scope="col" aria-label="Quarter 3">Q3</th>
                                            <th scope="col" aria-label="Quarter 4">Q4</th>
                                            <th scope="col" class="bg-primary-subtle">Final Grade</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $finalsForAverage = []; @endphp
                                        @forelse($enrollments as $enr)
                                            @php
                                                $subjectName = optional(optional($enr->course)->subject)->name ?? '—';
                                                if ($academicYearFilter) {
                                                    if (optional($enr->course)->academic_year_ID != $academicYearFilter) continue;
                                                }
                                                if ($semesterFilter) {
                                                    $ay = optional($enr->course)->academicYear;
                                                    if ($ay && $ay->semester !== $semesterFilter) continue;
                                                }
                                                $gradesByType = $student->grades->where('enrollment_id', $enr->enrollment_id)->groupBy('type');
                                                $types = ['Q1','Q2','Q3','Q4','Final'];
                                                $row = [];
                                                foreach ($types as $t) {
                                                    $g = $gradesByType->get($t, collect())->sortByDesc('date_recorded')->first();
                                                    $row[$t] = $g ? ['id' => $g->grade_id, 'score' => $g->score] : ['id' => '', 'score' => ''];
                                                }
                                                $qScores = [];
                                                foreach (['Q1','Q2','Q3','Q4'] as $qt) {
                                                    if (is_numeric($row[$qt]['score'])) {
                                                        $qScores[] = (float) $row[$qt]['score'];
                                                    }
                                                }
                                                $computedFinal = count($qScores) ? round(array_sum($qScores) / count($qScores)) : '';
                                                if ($computedFinal !== '') {
                                                    $finalsForAverage[] = (float) $computedFinal;
                                                }
                                                $status = ($computedFinal !== '' && $computedFinal >= 75) ? 'Passed' : (($computedFinal !== '') ? 'Failed' : '');
                                                $gradePoint = '';
                                                $gradeDesc = '';
                                                if ($computedFinal !== '') {
                                                    $p = (int) $computedFinal;
                                                    if ($p >= 96) { $gradePoint = '1.00'; $gradeDesc = 'Excellent'; }
                                                    elseif ($p >= 94) { $gradePoint = '1.25'; $gradeDesc = 'Very Good'; }
                                                    elseif ($p >= 91) { $gradePoint = '1.50'; $gradeDesc = 'Very Good'; }
                                                    elseif ($p >= 88) { $gradePoint = '1.75'; $gradeDesc = 'Good'; }
                                                    elseif ($p >= 85) { $gradePoint = '2.00'; $gradeDesc = 'Good'; }
                                                    elseif ($p >= 83) { $gradePoint = '2.25'; $gradeDesc = 'Good'; }
                                                    elseif ($p >= 80) { $gradePoint = '2.50'; $gradeDesc = 'Fair'; }
                                                    elseif ($p >= 78) { $gradePoint = '2.75'; $gradeDesc = 'Fair'; }
                                                    elseif ($p >= 75) { $gradePoint = '3.00'; $gradeDesc = 'Pass'; }
                                                    else { $gradePoint = '5.00'; $gradeDesc = 'Failure'; }
                                                }
                                            @endphp
                                            <tr>
                                                <th scope="row">{{ $subjectName }}</th>
                                                @foreach(['Q1','Q2','Q3','Q4','Final'] as $t)
                                                <td>
                                                    @if($t !== 'Final')
                                                        <input type="number" class="form-control form-control-sm grade-input"
                                                               min="0" max="100" step="1" inputmode="numeric" pattern="\\d*"
                                                               value="{{ $row[$t]['score'] !== '' ? $row[$t]['score'] : '' }}"
                                                               @if(!$canEdit) disabled @endif
                                                               data-enrollment-id="{{ $enr->enrollment_id }}"
                                                               data-type="{{ $t }}"
                                                               data-grade-id="{{ $row[$t]['id'] }}">
                                                        <div class="invalid-feedback">Enter a whole number 0–100</div>
                                                    @else
                                                        <div class="fw-semibold">{{ $computedFinal !== '' ? $computedFinal : '—' }}</div>
                                                        @if($computedFinal !== '')
                                                            <div class="small text-muted">GP {{ $gradePoint }} • {{ $gradeDesc }}</div>
                                                        @endif
                                                    @endif
                                                </td>
                                                @endforeach
                                                <td class="text-nowrap">
                                                    @if($canEdit)
                                                    <button class="btn btn-sm btn-primary save-btn"
                                                            data-student="{{ $student->student_ID }}"
                                                            data-section="{{ $section->section_ID }}">
                                                        Save
                                                    </button>
                                                    @else
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>Read-only</button>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($status === 'Passed')
                                                        <span class="badge bg-success">Passed</span>
                                                    @elseif($status === 'Failed')
                                                        <span class="badge bg-danger">Failed</span>
                                                    @else
                                                        <span class="badge bg-secondary">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="8" class="text-muted">No enrolled subjects.</td></tr>
                                        @endforelse
                                        <tr class="table-light">
                                            <th scope="row">General Average</th>
                                            <td colspan="4"></td>
                                            <td>
                                                @php
                                                    $avg = count($finalsForAverage) ? round(array_sum($finalsForAverage) / count($finalsForAverage), 2) : '—';
                                                @endphp
                                                <strong>{{ $avg }}</strong>
                                            </td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex gap-2 justify-content-end">
                                <button class="btn btn-outline-secondary btn-sm js-print-card">Print</button>
                                <button class="btn btn-outline-warning btn-sm js-export-card">Export Report</button>
                            </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info">No sections found for the current filter.</div>
        </div>
    @endforelse
</div>

<div id="op-status" class="d-none" role="status" aria-live="polite">
    <div class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></div>
    <span class="ms-2 text-muted">Saving…</span>
</div>
@endsection

@push('scripts')
<script>
(function(){
    const status = document.getElementById('op-status');
    function showStatus() { status.classList.remove('d-none'); }
    function hideStatus() { status.classList.add('d-none'); }
    function token() {
        const el = document.querySelector('meta[name=\"csrf-token\"]');
        return el ? el.getAttribute('content') : '{{ csrf_token() }}';
    }
    async function saveInputs(inputs) {
        const payloads = [];
        inputs.forEach(function(input){
            const enrollmentId = input.getAttribute('data-enrollment-id');
            const gradeId = input.getAttribute('data-grade-id');
            const type = input.getAttribute('data-type');
            const scoreVal = input.value;
            if (!enrollmentId || !type || scoreVal === '') return;
            payloads.push({enrollment_id: enrollmentId, type, score: scoreVal, grade_id: gradeId});
        });
        if (!payloads.length) return;
        showStatus();
        try {
            const resp = await fetch('{{ route("grades.bulk") }}', {
                method: 'POST',
                headers: {
                    'Content-Type':'application/json',
                    'Accept':'application/json',
                    'X-CSRF-TOKEN': token(),
                },
                body: JSON.stringify({items: payloads}),
                credentials: 'same-origin',
            });
            hideStatus();
            const data = await resp.json().catch(() => ({}));
            if (!resp.ok) {
                if (data && data.errors) {
                    const msgs = data.errors.map(e => (e.type||'')+': '+(e.message||'error')).join('\n');
                    alert('Some grades failed:\n'+msgs);
                } else {
                    alert('Failed to save ('+resp.status+').');
                }
                return;
            }
            alert('Saved '+(data.saved || 0)+' changes.');
            location.reload();
        } catch (e) {
            hideStatus();
            alert('Error saving.');
        }
    }
    document.querySelectorAll('.save-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
            const table = btn.closest('table');
            const inputs = table.querySelectorAll('.grade-input');
            saveInputs(inputs);
        });
    });
    document.querySelectorAll('.js-save-all').forEach(function(btn){
        btn.addEventListener('click', function(){
            const sectionId = btn.getAttribute('data-section');
            const card = btn.closest('.card');
            const inputs = card.querySelectorAll('.grade-input');
            saveInputs(inputs);
        });
    });
    function csvEscape(v){ return ('"'+String(v).replace(/"/g,'""')+'"'); }
    function exportTable(table, filename){
        const rows = table.querySelectorAll('tbody tr');
        const headers = ['Subject','Q1','Q2','Q3','Q4','Final','Status','GeneralAverage'];
        const data = [headers.join(',')];
        let generalAvg = '';
        rows.forEach(function(tr){
            const th = tr.querySelector('th[scope="row"]');
            const tds = tr.querySelectorAll('td');
            if (th && tds.length >= 6) {
                const subject = th.textContent.trim();
                const in1 = tds[0].querySelector('input');
                const q1 = in1 ? in1.value : '';
                const in2 = tds[1].querySelector('input');
                const q2 = in2 ? in2.value : '';
                const in3 = tds[2].querySelector('input');
                const q3 = in3 ? in3.value : '';
                const in4 = tds[3].querySelector('input');
                const q4 = in4 ? in4.value : '';
                const in5 = tds[4].querySelector('input');
                let fin = '';
                if (in5) {
                    fin = in5.value;
                } else {
                    const finalText = tds[4].querySelector('.fw-semibold');
                    fin = finalText ? finalText.textContent.trim() : '';
                }
                const stCell = tds[6];
                const statusEl = stCell ? stCell.querySelector('.badge') : null;
                const st = statusEl ? statusEl.textContent.trim() : '';
                data.push([subject,q1,q2,q3,q4,fin,st,''].map(csvEscape).join(','));
            } else {
                const avgCell = tr.querySelector('strong');
                if (avgCell) generalAvg = avgCell.textContent.trim();
            }
        });
        if (generalAvg) {
            data.push(['','','','','','',csvEscape('General Average'),csvEscape(generalAvg)].join(','));
        }
        const blob = new Blob([data.join('\n')], {type: 'text/csv;charset=utf-8;'});
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename || 'grades-report.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
    document.querySelectorAll('.js-export-card').forEach(function(btn){
        btn.addEventListener('click', function(){
            const card = btn.closest('.card');
            const table = card.querySelector('table');
            const h3 = card.querySelector('h3');
            const full = h3 && h3.textContent ? h3.textContent : 'grades';
            const name = full.split('\n')[0].trim();
            exportTable(table, name.replace(/\s+/g,'_')+'_report.csv');
        });
    });
    document.querySelectorAll('.js-print-card').forEach(function(btn){
        btn.addEventListener('click', function(){
            window.print();
        });
    });
    function validateInt(input) {
        const v = input.value.trim();
        if (v === '') { input.classList.remove('is-invalid'); return true; }
        if (!/^\d+$/.test(v)) { input.classList.add('is-invalid'); return false; }
        const n = parseInt(v, 10);
        if (isNaN(n) || n < 0 || n > 100) { input.classList.add('is-invalid'); return false; }
        input.classList.remove('is-invalid');
        input.value = String(n);
        return true;
    }
    document.querySelectorAll('.grade-input').forEach(function(input){
        input.addEventListener('input', function(){
            input.value = input.value.replace(/[^0-9]/g, '');
            validateInt(input);
        });
        input.addEventListener('blur', function(){ validateInt(input); });
    });
    document.querySelectorAll('.js-toggle-student').forEach(function(btn){
        btn.addEventListener('click', function(){
            const panelId = btn.getAttribute('aria-controls');
            const panel = document.getElementById(panelId);
            if (!panel) return;
            const icon = btn.querySelector('.js-toggle-icon');
            const isCollapsed = panel.classList.contains('collapsed');
            if (isCollapsed) {
                panel.classList.remove('collapsed');
                panel.style.maxHeight = panel.scrollHeight + 'px';
                btn.setAttribute('aria-expanded', 'true');
                if (icon) { icon.textContent = '−'; icon.dataset.state = 'expanded'; icon.classList.remove('bg-secondary'); icon.classList.add('bg-primary'); }
            } else {
                panel.style.maxHeight = panel.scrollHeight + 'px';
                requestAnimationFrame(function(){
                    panel.style.maxHeight = '0';
                    panel.classList.add('collapsed');
                    btn.setAttribute('aria-expanded', 'false');
                    if (icon) { icon.textContent = '+'; icon.dataset.state = 'collapsed'; icon.classList.remove('bg-primary'); icon.classList.add('bg-secondary'); }
                });
            }
        });
    });
})();
</script>
@endpush
