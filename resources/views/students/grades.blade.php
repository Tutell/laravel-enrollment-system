@extends('layouts.master')

@section('title', 'Student Grades')
@section('page-title', 'Student Grades')
@section('page-description', 'View and manage grades for a single student')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.show', $student) }}">{{ $student->first_name }} {{ $student->last_name }}</a></li>
    <li class="breadcrumb-item active">Grades</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <h2 class="h5 mb-1">{{ $student->first_name }} {{ $student->last_name }}</h2>
            <div class="text-muted small">
                Student ID: {{ $student->student_ID }} • Grade {{ optional($student->section)->grade_level }} – {{ optional($student->section)->section_name }}
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-warning btn-sm js-export">Export Report</button>
            <button class="btn btn-outline-secondary btn-sm js-print">Print</button>
        </div>
    </div>
</div>

<form class="row g-3 mb-3" method="GET" action="{{ route('students.grades', $student) }}" aria-label="Filter">
    <div class="col-md-3">
        <label for="academic_year_id" class="form-label">Academic Year</label>
        <select id="academic_year_id" name="academic_year_id" class="form-select">
            <option value="">All</option>
            @foreach($academicYears as $ay)
                <option value="{{ $ay->academic_year_ID }}" @selected($ayFilter == $ay->academic_year_ID)>{{ $ay->school_year }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label for="semester" class="form-label">Semester</label>
        <select id="semester" name="semester" class="form-select">
            <option value="">All</option>
            @foreach($semesters as $sem)
                <option value="{{ $sem }}" @selected($semFilter == $sem)>{{ $sem }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 align-self-end">
        <button class="btn btn-primary w-100" type="submit">Filter</button>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle" id="grades-table">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Subject</th>
                        <th scope="col">1st Quarter</th>
                        <th scope="col">2nd Quarter</th>
                        <th scope="col">3rd Quarter</th>
                        <th scope="col">4th Quarter</th>
                        <th scope="col" class="bg-primary-subtle">Final Grade</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php $finalsForAverage = []; @endphp
                    @forelse($enrollments as $enr)
                        @php
                            $subjectName = optional($enr->course->subject)->name ?? '—';
                            $gradesByType = $student->grades->where('enrollment_id', $enr->enrollment_id)->groupBy('type');
                            $types = ['Q1','Q2','Q3','Q4','Final'];
                            $row = [];
                            foreach ($types as $t) {
                                $g = optional($gradesByType->get($t))->sortByDesc('date_recorded')->first();
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
                            <td>
                                @if($status === 'Passed')
                                    <span class="badge bg-success">Passed</span>
                                @elseif($status === 'Failed')
                                    <span class="badge bg-danger">Failed</span>
                                @else
                                    <span class="badge bg-secondary">—</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                @if($canEdit)
                                <button class="btn btn-sm btn-primary save-btn">Save</button>
                                @endif
                                @if($role === 'teacher' || $role === 'admin')
                                <form method="POST" action="{{ route('enrollment.drop', $enr) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning">Drop</button>
                                </form>
                                @endif
                                @if($role === 'admin')
                                <form method="POST" action="{{ route('enrollment.unenroll', $enr) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Unenroll</button>
                                </form>
                                @endif
                                @if(!$canEdit && $role !== 'teacher' && $role !== 'admin')
                                <button class="btn btn-sm btn-outline-secondary" disabled>Read-only</button>
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
        @if($canEdit)
        <div class="d-flex gap-2 justify-content-end">
            <button class="btn btn-primary btn-sm js-save-all">Save All</button>
        </div>
        @endif
    </div>
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
        const el = document.querySelector('meta[name="csrf-token"]');
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
            const row = btn.closest('tr');
            const inputs = row.querySelectorAll('.grade-input');
            saveInputs(inputs);
        });
    });
    const table = document.getElementById('grades-table');
    document.querySelectorAll('.js-save-all').forEach(function(btn){
        btn.addEventListener('click', function(){
            const inputs = table.querySelectorAll('.grade-input');
            saveInputs(inputs);
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
                const stCell = tds[5];
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
        a.download = filename || 'student-grades-report.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
    document.querySelectorAll('.js-export').forEach(function(btn){
        btn.addEventListener('click', function(){
            const name = '{{ $student->last_name }}, {{ $student->first_name }}';
            exportTable(document.getElementById('grades-table'), name.replace(/\s+/g,'_')+'_grades.csv');
        });
    });
    document.querySelectorAll('.js-print').forEach(function(btn){
        btn.addEventListener('click', function(){
            window.print();
        });
    });
})();
</script>
@endpush
