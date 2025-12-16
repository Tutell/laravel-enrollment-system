@extends('layouts.master')

@section('title', 'Dashboard')

@section('page-title', 'Administration Dashboard')

@section('page-description', 'Overview of enrollments, capacity, and key metrics for the school year')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row g-3 mb-3">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="bi bi-speedometer2 text-primary fs-4"></i>
                        </div>
                        <div>
                            <h2 class="h5 mb-1 text-primary fw-semibold">School Overview</h2>
                            <p class="mb-0 text-muted">Summary of the current academic year activity</p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2" aria-label="Dashboard filters">
                        <div class="d-flex align-items-center gap-2">
                            <label for="filterAcademicYear" class="form-label mb-0 small">This School Year</label>
                            <select id="filterAcademicYear" class="form-select form-select-sm" aria-label="Select academic year">
                                @php
                                    $years = \App\Models\AcademicYear::orderByDesc('is_active')->orderByDesc('school_year')->get();
                                    $activeYear = $years->firstWhere('is_active', 1) ?: $years->first();
                                @endphp
                                @foreach($years as $y)
                                    <option value="{{ $y->academic_year_ID }}" @if(optional($activeYear)->academic_year_ID === $y->academic_year_ID) selected @endif>
                                        {{ $y->school_year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label for="filterGrades" class="form-label mb-0 small">All Grade</label>
                            <select id="filterGrades" class="form-select form-select-sm" multiple aria-label="Select grades">
                                @foreach(\App\Models\YearLevel::orderBy('grade_level')->pluck('grade_level')->unique() as $gl)
                                    <option value="{{ $gl }}">Grade {{ $gl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary" id="activeFiltersBadge" aria-live="polite"></span>
                            <button class="btn btn-outline-secondary btn-sm" id="resetFiltersBtn" aria-label="Reset filters">Reset</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Enrollment Progress</div>
                        @php
                            $enrolled = (int) ($stats['students'] ?? 0);
                            $capacity = 800;
                            $enrollmentPct = $capacity > 0 ? round(min(100, ($enrolled / $capacity) * 100)) : 0;
                        @endphp
                        <div class="h4 mb-1 fw-semibold">{{ $enrollmentPct }}%</div>
                        <div class="text-muted small">{{ $enrolled }} enrolled of {{ $capacity }} capacity</div>
                    </div>
                    <div class="position-relative" style="width:72px;height:72px;">
                        <svg viewBox="0 0 36 36" class="w-100 h-100">
                            <defs>
                                <linearGradient id="dashGauge" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#1A73E8"/>
                                    <stop offset="100%" stop-color="#4DB5FF"/>
                                </linearGradient>
                            </defs>
                            <path d="M18 2a16 16 0 1 1 0 32A16 16 0 1 1 18 2" fill="none" stroke="#e8eaed" stroke-width="3"/>
                            <path d="M18 2a16 16 0 1 1 0 32A16 16 0 1 1 18 2" fill="none" stroke="url(#dashGauge)" stroke-width="3" stroke-dasharray="{{ $enrollmentPct }},100" stroke-linecap="round"/>
                        </svg>
                        <div class="position-absolute top-50 start-50 translate-middle">
                            <span class="small fw-semibold">{{ $enrollmentPct }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="stat-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Students</div>
                        <div class="h4 mb-0 fw-semibold" id="statStudents">{{ $stats['students'] ?? 0 }}</div>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                        <i class="bi bi-people-fill text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Teachers</div>
                        <div class="h4 mb-0 fw-semibold" id="statTeachers">{{ $stats['teachers'] ?? 0 }}</div>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                        <i class="bi bi-person-badge-fill text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Sections</div>
                        <div class="h4 mb-0 fw-semibold" id="statSections">{{ $stats['sections'] ?? 0 }}</div>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                        <i class="bi bi-grid-3x3-gap-fill text-info"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Subjects</div>
                        <div class="h4 mb-0 fw-semibold" id="statSubjects">{{ $stats['subjects'] ?? 0 }}</div>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                        <i class="bi bi-book-half text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="h6 mb-1 fw-semibold text-primary">Enrollment Calendar</h2>
                            <div class="text-muted small">Explore daily enrollments and details</div>
                        </div>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Calendar view">
                            <button type="button" class="btn btn-outline-secondary" data-view="dayGridMonth">Month</button>
                            <button type="button" class="btn btn-outline-secondary" data-view="dayGridWeek">Week</button>
                            <button type="button" class="btn btn-outline-secondary" data-view="dayGridDay">Day</button>
                        </div>
                    </div>
                    <div class="position-relative" style="min-height:340px;">
                        <div id="enrollmentCalendar" aria-label="Enrollment calendar"></div>
                        <div id="calendarLoading" class="position-absolute top-50 start-50 translate-middle text-muted" style="display:none;">
                            <div class="spinner-border text-primary me-2" role="status" aria-hidden="true"></div>
                            <span>Loading…</span>
                        </div>
                        <div id="statsLoading" class="position-absolute top-0 end-0 m-2 text-muted" style="display:none;">
                            <div class="spinner-border spinner-border-sm text-secondary me-2" role="status" aria-hidden="true"></div>
                            <span class="small">Updating</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 mb-0 fw-semibold text-primary">Quick links</h2>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('students.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span>Manage students</span>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                        <a href="{{ route('teachers.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span>Manage teachers</span>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                        <a href="{{ route('year-levels.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span>Year Level Management</span>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                        <a href="{{ route('academic-years.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span>Academic year setup</span>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                        @if(auth()->check() && auth()->user()->role === 'admin')
                        <a href="{{ route('admin.logs.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span>View Logs</span>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
(function () {
  function initCalendar() {
    var el = document.getElementById('enrollmentCalendar');
    if (!el || !window.FullCalendar) return;
    var trendUrl = '{{ route('dashboard.trend') }}';
    var statsUrl = '{{ route('dashboard.stats') }}';
    var loadingEl = document.getElementById('calendarLoading');
    var statsLoadingEl = document.getElementById('statsLoading');
    var cache = {};
    var filterAcademicYear = document.getElementById('filterAcademicYear');
    var filterGrades = document.getElementById('filterGrades');
    var activeBadge = document.getElementById('activeFiltersBadge');
    var resetBtn = document.getElementById('resetFiltersBtn');

    function persistFilters() {
      var year = (filterAcademicYear.value || '').trim();
      var grades = Array.prototype.slice.call(filterGrades.options)
        .filter(function (opt) { return opt.selected; })
        .map(function (opt) { return opt.value; });
      var payload = { year: year, grades: grades };
      try { localStorage.setItem('dashboard.filters', JSON.stringify(payload)); } catch (e) {}
      updateActiveBadge(payload);
    }

    function restoreFilters() {
      var raw = null, payload = null;
      try { raw = localStorage.getItem('dashboard.filters'); } catch (e) {}
      try { payload = raw ? JSON.parse(raw) : null; } catch (e) { payload = null; }
      if (payload && payload.year) {
        Array.prototype.forEach.call(filterAcademicYear.options, function (opt) {
          opt.selected = String(opt.value) === String(payload.year);
        });
      }
      if (payload && payload.grades && payload.grades.length) {
        var set = {};
        payload.grades.forEach(function (g) { set[String(g)] = true; });
        Array.prototype.forEach.call(filterGrades.options, function (opt) {
          opt.selected = !!set[String(opt.value)];
        });
      }
      updateActiveBadge(payload || { year: filterAcademicYear.value, grades: [] });
    }

    function updateActiveBadge(payload) {
      var yearText = '';
      var selYear = filterAcademicYear.options[filterAcademicYear.selectedIndex];
      if (selYear) yearText = selYear.textContent || selYear.innerText || '';
      var gradesText = Array.prototype.slice.call(filterGrades.options)
        .filter(function (opt) { return opt.selected; })
        .map(function (opt) { return opt.textContent || opt.innerText || ''; })
        .join(', ');
      var txt = 'Year: ' + (yearText || '—');
      if (gradesText) txt += ' • Grades: ' + gradesText;
      activeBadge.textContent = txt;
    }

    function colorForCount(count) {
      var min = 0, max = 50; // dynamic scale cap
      var pct = Math.max(0, Math.min(1, count / max));
      var start = { r: 77, g: 181, b: 255 }; // light accent
      var end = { r: 26, g: 115, b: 232 };  // primary
      var r = Math.round(start.r + (end.r - start.r) * pct);
      var g = Math.round(start.g + (end.g - start.g) * pct);
      var b = Math.round(start.b + (end.b - start.b) * pct);
      return 'rgba(' + r + ',' + g + ',' + b + ',0.6)';
    }

    var calendar = new FullCalendar.Calendar(el, {
      initialView: 'dayGridMonth',
      height: 420,
      headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
      editable: false,
      dayMaxEvents: false,
      eventSources: [{
        events: function (info, success, failure) {
          loadingEl.style.display = '';
          var grades = Array.prototype.slice.call(filterGrades.options)
            .filter(function (opt) { return opt.selected; })
            .map(function (opt) { return opt.value; })
            .join(',');
          var year = (filterAcademicYear.value || '').trim();
          var url = trendUrl + '?range=days&start=' + encodeURIComponent(info.startStr) + '&end=' + encodeURIComponent(info.endStr);
          if (year) url += '&academic_year_id=' + encodeURIComponent(year);
          if (grades) url += '&grades=' + encodeURIComponent(grades);
          var xhr = new XMLHttpRequest();
          xhr.open('GET', url, true);
          xhr.setRequestHeader('Accept', 'application/json');
          xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
              if (xhr.status === 200) {
                try {
                  var data = JSON.parse(xhr.responseText);
                  var labels = data.labels || [];
                  var values = data.values || [];
                  var events = labels.map(function (d, i) {
                    var count = values[i] || 0;
                    return {
                      start: d,
                      display: 'background',
                      color: colorForCount(count),
                      extendedProps: { count: count }
                    };
                  });
                  success(events);
                } catch (e) { failure(e); }
                loadingEl.style.display = 'none';
              } else {
                failure(new Error('Failed to load calendar data'));
                loadingEl.style.display = 'none';
              }
            }
          };
          xhr.send();
        }
      }],
      datesSet: function () {
        // re-run eventSources fetch automatically
      },
      dateClick: function (arg) {
        openDetailModal(arg.dateStr);
      },
      eventDidMount: function (info) {
        var count = info.event.extendedProps.count || 0;
        var title = count + ' enrollment' + (count === 1 ? '' : 's');
        info.el.setAttribute('title', title);
      }
    });
    calendar.render();

    function refreshStats() {
      statsLoadingEl.style.display = '';
      var grades = Array.prototype.slice.call(filterGrades.options)
        .filter(function (opt) { return opt.selected; })
        .map(function (opt) { return opt.value; })
        .join(',');
      var year = (filterAcademicYear.value || '').trim();
      var url = statsUrl;
      var qs = [];
      if (year) qs.push('academic_year_id=' + encodeURIComponent(year));
      if (grades) qs.push('grades=' + encodeURIComponent(grades));
      if (qs.length) url += '?' + qs.join('&');
      var xhr = new XMLHttpRequest();
      xhr.open('GET', url, true);
      xhr.setRequestHeader('Accept', 'application/json');
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
          statsLoadingEl.style.display = 'none';
          if (xhr.status === 200) {
            try {
              var data = JSON.parse(xhr.responseText);
              var setText = function (id, v) {
                var el = document.getElementById(id);
                if (el) el.textContent = String(v || 0);
              };
              setText('statStudents', data.students);
              setText('statTeachers', data.teachers);
              setText('statSubjects', data.subjects);
              setText('statSections', data.sections);
            } catch (e) {}
          }
        }
      };
      xhr.send();
    }

    var buttons = document.querySelectorAll('[aria-label=\"Calendar view\"] .btn');
    Array.prototype.forEach.call(buttons, function (btn) {
      btn.addEventListener('click', function () {
        var view = btn.dataset.view;
        if (view) calendar.changeView(view);
      });
    });

    function statusBadge(status) {
      var s = (status || '').toLowerCase();
      if (s === 'enrolled') return '<span class=\"badge bg-success\">Enrolled</span>';
      if (s === 'dropped') return '<span class=\"badge bg-danger\">Dropped</span>';
      if (s === 'pending') return '<span class=\"badge bg-warning text-dark\">Pending</span>';
      return '<span class=\"badge bg-secondary\">' + (status || '—') + '</span>';
    }

    function openDetailModal(dateStr) {
      var modalEl = document.getElementById('enrollmentDetailModal');
      var modalBody = document.getElementById('enrollmentDetailBody');
      var modalTitle = document.getElementById('enrollmentDetailTitle');
      var gradeSelect = document.getElementById('detailFilterGrade');
      var sectionInput = document.getElementById('detailFilterSection');
      var footerStats = document.getElementById('enrollmentDetailStats');
      if (!modalEl) return;
      modalTitle.textContent = 'Enrollments on ' + dateStr;
      modalBody.innerHTML = '<div class=\"d-flex align-items-center\"><div class=\"spinner-border text-primary me-2\" role=\"status\" aria-hidden=\"true\"></div><span>Loading…</span></div>';
      var gradesSel = Array.prototype.slice.call(filterGrades.options)
        .filter(function (opt) { return opt.selected; })
        .map(function (opt) { return opt.value; })
        .join(',');
      var yearSel = (filterAcademicYear.value || '').trim();
      var url = trendUrl + '?date=' + encodeURIComponent(dateStr);
      if (yearSel) url += '&academic_year_id=' + encodeURIComponent(yearSel);
      if (gradesSel) url += '&grades=' + encodeURIComponent(gradesSel);

      function render(data) {
        var items = data.items || [];
        var grade = gradeSelect.value || '';
        var section = sectionInput.value || '';
        var filtered = items.filter(function (it) {
          var ok = true;
          if (grade) ok = ok && String(it.grade_level || '') === String(grade);
          if (section) ok = ok && String((it.section || '').toLowerCase()).includes(section.toLowerCase());
          return ok;
        });
        var rows = filtered.map(function (it) {
          return '<tr>' +
            '<td>' + (it.student_name || '—') + '</td>' +
            '<td>' + (it.section || '—') + '</td>' +
            '<td>' + (it.grade_level ?? '—') + '</td>' +
            '<td>' + (it.subject || '—') + '</td>' +
            '<td>' + statusBadge(it.status) + '</td>' +
          '</tr>';
        }).join('');
        modalBody.innerHTML =
          '<div class=\"table-responsive\">' +
            '<table class=\"table table-sm align-middle\">' +
              '<thead><tr><th>Student</th><th>Section</th><th>Grade</th><th>Subject</th><th>Status</th></tr></thead>' +
              '<tbody>' + (rows || '<tr><td colspan=\"5\" class=\"text-muted\">No enrollments</td></tr>') + '</tbody>' +
            '</table>' +
          '</div>';
        footerStats.innerHTML = '<span class=\"me-3\">Total: <strong>' + (data.count || 0) + '</strong></span>' +
          '<span class=\"me-3\">By Status: ' + Object.keys(data.byStatus || {}).map(function (k) { return k + ': ' + data.byStatus[k]; }).join(', ') + '</span>';
      }

      function loadAndRender() {
        if (cache[dateStr]) { render(cache[dateStr]); return; }
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.onreadystatechange = function () {
          if (xhr.readyState === 4) {
            if (xhr.status === 200) {
              try {
                var data = JSON.parse(xhr.responseText);
                cache[dateStr] = data;
                render(data);
              } catch (e) {
                modalBody.innerHTML = '<div class=\"alert alert-danger\">Failed to parse data.</div>';
              }
            } else {
              modalBody.innerHTML = '<div class=\"alert alert-danger\">Failed to load data.</div>';
            }
          }
        };
        xhr.send();
      }

      gradeSelect.onchange = function () { if (cache[dateStr]) render(cache[dateStr]); };
      sectionInput.oninput = function () { if (cache[dateStr]) render(cache[dateStr]); };
      var bsModal = new bootstrap.Modal(modalEl);
      bsModal.show();
      loadAndRender();
    }

    restoreFilters();
    persistFilters();
    refreshStats();
    filterAcademicYear.addEventListener('change', function () {
      persistFilters();
      calendar.refetchEvents();
      refreshStats();
    });
    filterGrades.addEventListener('change', function () {
      persistFilters();
      calendar.refetchEvents();
      refreshStats();
    });
    resetBtn.addEventListener('click', function () {
      Array.prototype.forEach.call(filterGrades.options, function (opt) { opt.selected = false; });
      persistFilters();
      calendar.refetchEvents();
      refreshStats();
    });
  }

  if (document.readyState === 'complete') {
    initCalendar();
  } else {
    document.addEventListener('DOMContentLoaded', initCalendar);
  }
})();
</script>
@endpush
<div class="modal fade" id="enrollmentDetailModal" tabindex="-1" aria-labelledby="enrollmentDetailTitle" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="enrollmentDetailTitle">Enrollments</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3 mb-3">
          <div class="col-sm-4">
            <label class="form-label">Filter by Grade</label>
            <select class="form-select" id="detailFilterGrade">
              <option value="">All grades</option>
              @foreach(\App\Models\YearLevel::orderBy('grade_level')->pluck('grade_level')->unique() as $gl)
                <option value="{{ $gl }}">Grade {{ $gl }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-sm-4">
            <label class="form-label">Filter by Section</label>
            <input type="text" class="form-control" id="detailFilterSection" placeholder="Type section name">
          </div>
        </div>
        <div id="enrollmentDetailBody"></div>
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <div id="enrollmentDetailStats" class="text-muted"></div>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
