@extends('layouts.master')

@section('title','Assign Teachers to Subjects')
@section('page-title','Assign Teachers')
@section('page-description','Choose a grade, pick subjects, select teachers, then assign')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('subjects.index') }}">Subjects</a></li>
    <li class="breadcrumb-item active">Assign Teachers</li>
@endsection

@section('content')
<div class="card border-0 shadow" role="region" aria-label="Assign Teachers">
    <div class="card-body">
        <div id="alertRegion" aria-live="polite">
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle me-2"></i><span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><span>{{ session('warning') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-x-circle me-2"></i><span>{{ $errors->first() }}</span>
                </div>
            @endif
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex gap-2">
                <a href="{{ route('subjects.index') }}" class="btn btn-lg btn-outline-primary" aria-label="Back to Subjects">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <a href="{{ route('dashboard.index') }}" class="btn btn-lg btn-outline-primary" aria-label="Go to Home">
                    <i class="bi bi-house-door"></i>
                </a>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <button type="button" class="btn btn-lg btn-outline-dark" id="toggleContrast" aria-pressed="false" aria-label="Toggle high contrast">
                    <i class="bi bi-circle-half"></i>
                </button>
                <button type="button" class="btn btn-lg btn-outline-dark" id="increaseText" aria-label="Increase text size">
                    <i class="bi bi-zoom-in"></i>
                </button>
                <button type="button" class="btn btn-lg btn-outline-dark" id="decreaseText" aria-label="Decrease text size">
                    <i class="bi bi-zoom-out"></i>
                </button>
                <button type="button" class="btn btn-lg btn-outline-dark" id="toggleAudio" aria-pressed="false" aria-label="Toggle audio feedback">
                    <i class="bi bi-volume-up"></i>
                </button>
            </div>
        </div>
        <div class="d-flex align-items-center mb-3" aria-label="Progress">
            <div class="me-3 fw-bold">Steps:</div>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-primary" id="step1">1. Grade</span>
                <span class="badge bg-secondary" id="step2">2. Subjects</span>
                <span class="badge bg-secondary" id="step3">3. Teachers</span>
                <span class="badge bg-secondary" id="step4">4. Review</span>
            </div>
        </div>
        <div class="mb-3">
            <details>
                <summary class="btn btn-lg btn-outline-success" aria-label="Show help">Show help</summary>
                <div class="mt-3">
                    <p class="mb-2">Pick a grade, choose the subjects, then select teachers. Press Assign to finish.</p>
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="How to assign teachers" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    </div>
                </div>
            </details>
        </div>
        <form method="POST" action="{{ route('subjects.assign-teachers.post') }}" id="assignForm" aria-labelledby="formTitle">
            @csrf
            <h2 id="formTitle" class="visually-hidden">Assign Teachers Form</h2>
            <input type="hidden" name="grade_level" id="gradeLevelHidden" value="{{ $gradeLevel ?? '' }}">
            <select name="subject_ids[]" id="subjectHiddenSelect" multiple class="visually-hidden" aria-hidden="true">
                @foreach($subjects as $s)
                    <option value="{{ $s->subject_ID }}"></option>
                @endforeach
            </select>
            <select name="teacher_ids[]" id="teacherHiddenSelect" multiple class="visually-hidden" aria-hidden="true">
                @foreach($teachers as $t)
                    <option value="{{ $t->teacher_ID }}"></option>
                @endforeach
            </select>
            <div class="row g-4">
                <div class="col-lg-3" id="gradePanel">
                    <div class="p-3 border rounded-3" tabindex="0" aria-label="Select grade level">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fs-5">Grade</label>
                            <i class="bi bi-info-circle" data-bs-toggle="tooltip" title="Choose the grade to filter subjects"></i>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-lg btn-outline-primary grade-btn" data-grade="" aria-label="All grades">All</button>
                            @foreach($grades as $g)
                                <button type="button" class="btn btn-lg btn-outline-primary grade-btn" data-grade="{{ $g }}" aria-label="Grade {{ $g }}">Grade {{ $g }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-4" id="subjectsPanel">
                    <div class="p-3 border rounded-3" tabindex="0" aria-label="Choose subjects">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fs-5">Subjects</label>
                            <i class="bi bi-info-circle" data-bs-toggle="tooltip" title="Pick the subjects for the selected grade"></i>
                        </div>
                        <div class="d-flex flex-wrap gap-3" id="subjectsList">
                            @foreach($subjects as $s)
                                <div class="subject-card card shadow-sm" data-subject-id="{{ $s->subject_ID }}" data-grade="{{ $s->grade_level }}" role="button" tabindex="0" aria-pressed="false">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="fw-bold">G{{ $s->grade_level }} – {{ $s->name }}</div>
                                            <span class="badge bg-info text-dark">{{ $s->teachers->count() }} assigned</span>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input subject-check" type="checkbox" value="{{ $s->subject_ID }}" id="sub{{ $s->subject_ID }}" aria-label="Select subject {{ $s->name }}">
                                            <label class="form-check-label" for="sub{{ $s->subject_ID }}">Select</label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-5" id="teachersPanel">
                    <div class="p-3 border rounded-3" tabindex="0" aria-label="Choose teachers">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fs-5">Teachers</label>
                            <i class="bi bi-info-circle" data-bs-toggle="tooltip" title="Search and select teachers to assign"></i>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control form-control-lg" id="teacherSearch" placeholder="Search name or department" aria-label="Search teachers">
                        </div>
                        <div class="d-flex flex-wrap gap-3" id="teachersList" aria-label="Teacher list">
                            @foreach($teachers as $t)
                                <div class="teacher-card card shadow-sm" data-teacher-id="{{ $t->teacher_ID }}" data-name="{{ strtolower($t->last_name.', '.$t->first_name) }}" data-dept="{{ strtolower($t->department ?? '') }}" draggable="true" role="button" tabindex="0" aria-pressed="false">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="fw-bold">{{ $t->last_name }}, {{ $t->first_name }}</div>
                                            <div class="small text-muted">Dept: {{ $t->department ?? 'N/A' }}</div>
                                        </div>
                                        <button type="button" class="btn btn-outline-success btn-lg select-teacher" data-teacher-id="{{ $t->teacher_ID }}" aria-label="Select teacher {{ $t->last_name }}, {{ $t->first_name }}"><i class="bi bi-plus-circle"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3 p-3 border rounded-3 bg-light" id="selectedTeachersBin" aria-label="Selected teachers dropzone">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="fw-bold">Selected Teachers</div>
                                <button type="button" class="btn btn-outline-danger btn-sm" id="clearTeachers">Clear</button>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-2" id="selectedTeachersChips"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary" id="summarySubjectsCount">0 subjects</span>
                    <span class="badge bg-success" id="summaryTeachersCount">0 teachers</span>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('subjects.assign-teachers') }}" class="btn btn-lg btn-outline-secondary" id="resetBtn" aria-label="Reset"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</a>
                    <button type="submit" class="btn btn-lg btn-success" id="submitBtn" aria-label="Assign"><i class="bi bi-check2-circle me-1"></i>Assign</button>
                </div>
            </div>
        </form>
    </div>
</div>
@push('styles')
<style>
.subject-card,.teacher-card{transition:transform .15s ease,box-shadow .15s ease}
.subject-card:focus,.teacher-card:focus{outline:3px solid #1A73E8; outline-offset:2px}
.subject-card.active,.teacher-card.active{transform:scale(1.02); box-shadow:0 0 0 4px rgba(26,115,232,.2)}
#selectedTeachersBin{min-height:74px; transition:background-color .2s ease}
#selectedTeachersBin.drag-over{background-color:#e6f0ff}
.chip{display:inline-flex; align-items:center; gap:.5rem; padding:.5rem .75rem; border-radius:999px; background:#1A73E8; color:#fff}
.chip .remove{border:none; background:transparent; color:#fff}
.hc-mode .subject-card,.hc-mode .teacher-card{box-shadow:0 0 0 3px #000}
.hc-mode .chip{background:#000; color:#fff}
.text-scale-1{font-size:1rem}
.text-scale-2{font-size:1.125rem}
.text-scale-3{font-size:1.25rem}
.btn-lg{font-size:1.125rem}
</style>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var gradeHidden=document.getElementById('gradeLevelHidden');
    var subjectHidden=document.getElementById('subjectHiddenSelect');
    var teacherHidden=document.getElementById('teacherHiddenSelect');
    var subjectsList=document.getElementById('subjectsList');
    var teachersList=document.getElementById('teachersList');
    var selectedBin=document.getElementById('selectedTeachersBin');
    var selectedChips=document.getElementById('selectedTeachersChips');
    var summarySubjects=document.getElementById('summarySubjectsCount');
    var summaryTeachers=document.getElementById('summaryTeachersCount');
    var submitBtn=document.getElementById('submitBtn');
    var toggleContrast=document.getElementById('toggleContrast');
    var increaseText=document.getElementById('increaseText');
    var decreaseText=document.getElementById('decreaseText');
    var toggleAudio=document.getElementById('toggleAudio');
    var textScale=1;
    var audioOn=false;
    var step1=document.getElementById('step1'),step2=document.getElementById('step2'),step3=document.getElementById('step3'),step4=document.getElementById('step4');
    Array.from(document.querySelectorAll('[data-bs-toggle=\"tooltip\"]')).forEach(function(el){new bootstrap.Tooltip(el)});
    function updateSteps(){var s=subjectHidden.selectedOptions.length>0; var t=teacherHidden.selectedOptions.length>0; step1.classList.add('bg-primary'); step2.classList.toggle('bg-primary',s); step3.classList.toggle('bg-primary',t); step4.classList.toggle('bg-primary',s&&t)}
    function playBeep(){if(!audioOn) return; try{var ctx=new (window.AudioContext||window.webkitAudioContext)(); var o=ctx.createOscillator(); var g=ctx.createGain(); o.type='sine'; o.frequency.setValueAtTime(880,ctx.currentTime); g.gain.setValueAtTime(0.001,ctx.currentTime); g.gain.exponentialRampToValueAtTime(0.2,ctx.currentTime+0.02); o.connect(g); g.connect(ctx.destination); o.start(); setTimeout(function(){o.stop()},150);}catch(e){}}
    function setSummary(){summarySubjects.textContent=subjectHidden.selectedOptions.length+' subjects'; summaryTeachers.textContent=teacherHidden.selectedOptions.length+' teachers'; updateSteps()}
    function selectSubject(id, active){Array.from(subjectHidden.options).forEach(function(o){if(o.value==id){o.selected=active}}); setSummary(); playBeep()}
    function selectTeacher(id, active){Array.from(teacherHidden.options).forEach(function(o){if(o.value==id){o.selected=active}}); if(active){var chip=document.createElement('span'); chip.className='chip'; chip.dataset.teacherId=id; var nameCard=document.querySelector('.teacher-card[data-teacher-id=\"'+id+'\"] .fw-bold'); chip.innerHTML=(nameCard?nameCard.textContent:'Teacher')+' <button type=\"button\" class=\"remove\" aria-label=\"Remove\">×</button>'; selectedChips.appendChild(chip)} else {Array.from(selectedChips.querySelectorAll('.chip')).forEach(function(c){if(c.dataset.teacherId==id){c.remove()}})} setSummary(); playBeep()}
    document.querySelectorAll('.grade-btn').forEach(function(btn){btn.addEventListener('click',function(){var g=this.dataset.grade; gradeHidden.value=g||''; Array.from(subjectsList.querySelectorAll('.subject-card')).forEach(function(card){var show=!g||card.dataset.grade==g; card.style.display=show?'':'none'; var cb=card.querySelector('.subject-check'); if(!show){cb.checked=false; selectSubject(cb.value,false)} }); step2.classList.add('bg-primary') })});
    subjectsList.addEventListener('click',function(e){var card=e.target.closest('.subject-card'); if(!card) return; var cb=card.querySelector('.subject-check'); var active=!cb.checked; cb.checked=active; card.classList.toggle('active',active); card.setAttribute('aria-pressed',active?'true':'false'); selectSubject(cb.value,active)});
    subjectsList.addEventListener('keydown',function(e){if(e.key==='Enter'||e.key===' '){e.preventDefault(); var card=document.activeElement.closest('.subject-card'); if(!card) return; var cb=card.querySelector('.subject-check'); var active=!cb.checked; cb.checked=active; card.classList.toggle('active',active); card.setAttribute('aria-pressed',active?'true':'false'); selectSubject(cb.value,active)}});
    teachersList.addEventListener('click',function(e){var btn=e.target.closest('.select-teacher'); var card=e.target.closest('.teacher-card'); if(btn){var id=btn.dataset.teacherId; var active=true; var already=Array.from(teacherHidden.selectedOptions).some(function(o){return o.value==id}); active=!already; selectTeacher(id,active); var tc=document.querySelector('.teacher-card[data-teacher-id=\"'+id+'\"]'); tc.classList.toggle('active',active); tc.setAttribute('aria-pressed',active?'true':'false')} else if(card){var id=card.dataset.teacherId; var already=Array.from(teacherHidden.selectedOptions).some(function(o){return o.value==id}); selectTeacher(id,!already); card.classList.toggle('active',!already); card.setAttribute('aria-pressed',!already?'true':'false') }});
    teachersList.addEventListener('keydown',function(e){if(e.key==='Enter'||e.key===' '){e.preventDefault(); var card=document.activeElement.closest('.teacher-card'); if(!card) return; var id=card.dataset.teacherId; var already=Array.from(teacherHidden.selectedOptions).some(function(o){return o.value==id}); selectTeacher(id,!already); card.classList.toggle('active',!already); card.setAttribute('aria-pressed',!already?'true':'false')}});
    teachersList.addEventListener('dragstart',function(e){var card=e.target.closest('.teacher-card'); if(!card) return; e.dataTransfer.setData('text/plain',card.dataset.teacherId)});
    selectedBin.addEventListener('dragover',function(e){e.preventDefault(); selectedBin.classList.add('drag-over')});
    selectedBin.addEventListener('dragleave',function(){selectedBin.classList.remove('drag-over')});
    selectedBin.addEventListener('drop',function(e){e.preventDefault(); selectedBin.classList.remove('drag-over'); var id=e.dataTransfer.getData('text/plain'); if(!id) return; var already=Array.from(teacherHidden.selectedOptions).some(function(o){return o.value==id}); if(!already){selectTeacher(id,true); var tc=document.querySelector('.teacher-card[data-teacher-id=\"'+id+'\"]'); if(tc){tc.classList.add('active'); tc.setAttribute('aria-pressed','true')}}});
    selectedChips.addEventListener('click',function(e){var btn=e.target.closest('.remove'); if(!btn) return; var chip=btn.closest('.chip'); var id=chip.dataset.teacherId; selectTeacher(id,false); var tc=document.querySelector('.teacher-card[data-teacher-id=\"'+id+'\"]'); if(tc){tc.classList.remove('active'); tc.setAttribute('aria-pressed','false')}});
    document.getElementById('clearTeachers').addEventListener('click',function(){Array.from(teacherHidden.options).forEach(function(o){o.selected=false}); Array.from(document.querySelectorAll('.teacher-card')).forEach(function(c){c.classList.remove('active'); c.setAttribute('aria-pressed','false')}); selectedChips.innerHTML=''; setSummary()});
    document.getElementById('teacherSearch').addEventListener('input',function(){var term=this.value.toLowerCase(); Array.from(teachersList.querySelectorAll('.teacher-card')).forEach(function(card){var name=(card.dataset.name||''); var dept=(card.dataset.dept||''); var show=!term||(name.includes(term)||dept.includes(term)); card.style.display=show?'':'none'})});
    document.getElementById('resetBtn').addEventListener('click',function(){});
    document.getElementById('assignForm').addEventListener('submit',function(e){if(subjectHidden.selectedOptions.length===0||teacherHidden.selectedOptions.length===0){e.preventDefault(); var alertRegion=document.getElementById('alertRegion'); var div=document.createElement('div'); div.className='alert alert-danger d-flex align-items-center'; div.innerHTML='<i class=\"bi bi-x-circle me-2\"></i><span>Please select at least one subject and one teacher.</span>'; alertRegion.prepend(div); return} submitBtn.disabled=true; playBeep()});
    toggleContrast.addEventListener('click',function(){var on=document.body.classList.toggle('hc-mode'); this.setAttribute('aria-pressed',on?'true':'false')});
    increaseText.addEventListener('click',function(){textScale=Math.min(3,textScale+1); document.body.classList.remove('text-scale-1','text-scale-2','text-scale-3'); document.body.classList.add('text-scale-'+textScale)});
    decreaseText.addEventListener('click',function(){textScale=Math.max(1,textScale-1); document.body.classList.remove('text-scale-1','text-scale-2','text-scale-3'); document.body.classList.add('text-scale-'+textScale)});
    toggleAudio.addEventListener('click',function(){audioOn=!audioOn; this.setAttribute('aria-pressed',audioOn?'true':'false')});
    setSummary();
});
</script>
@endpush
@endsection
