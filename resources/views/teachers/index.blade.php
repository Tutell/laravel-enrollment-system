@extends('layouts.master')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Teachers</h1>
        <a href="{{ route('teachers.create') }}" class="btn btn-primary">Add Teacher</a>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('teachers.index') }}">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            @foreach(['active','disabled','pending','on_leave'] as $st)
                                <option value="{{ $st }}" @selected(($status ?? '')===$st)>{{ ucfirst(str_replace('_',' ', $st)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Grade</label>
                        <select name="grade_level" class="form-select">
                            <option value="">All</option>
                            @foreach(($grades ?? []) as $g)
                                <option value="{{ $g }}" @selected(($grade ?? '')==$g)>Grade {{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Subject</label>
                        <select name="subject_id" class="form-select">
                            <option value="">All</option>
                            @foreach(($subjects ?? []) as $sub)
                                <option value="{{ $sub->subject_ID }}" @selected(($subjectId ?? '')==$sub->subject_ID)>{{ $sub->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sort</label>
                        <div class="input-group">
                            <select name="sort" class="form-select">
                                <option value="">Default</option>
                                <option value="name" @selected(($sort ?? '')==='name')>Name</option>
                                <option value="status" @selected(($sort ?? '')==='status')>Status</option>
                                <option value="department" @selected(($sort ?? '')==='department')>Department</option>
                            </select>
                            <select name="dir" class="form-select">
                                <option value="asc" @selected(($dir ?? 'asc')==='asc')>Asc</option>
                                <option value="desc" @selected(($dir ?? 'asc')==='desc')>Desc</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">Reset</a>
                        <button class="btn btn-primary" type="submit">Apply</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Assigned Grades</th>
                <th>Subjects</th>
                <th>Status</th>
                <th class="text-nowrap">Actions</th>
              </tr>
            </thead>
            <tbody>
                @forelse($teachers as $t)
                <tr>
                    <td>{{ $t->teacher_ID ?? $t->teacher_id }}</td>
                    <td>{{ $t->last_name }}, {{ $t->first_name }}</td>
                    <td>{{ optional($t->account)->Email ?? '—' }}</td>
                    <td>
                        @if(Auth::check() && Auth::user()->role === 'admin')
                        <form method="POST" action="{{ route('teachers.department', $t) }}" class="d-flex gap-2 align-items-center">
                            @csrf
                            @method('PUT')
                            <select name="department_id" class="form-select form-select-sm" style="min-width: 180px;" onchange="this.form.submit()">
                                <option value="">—</option>
                                @foreach(($departments ?? []) as $dept)
                                    <option value="{{ $dept->department_ID }}" @selected(($t->department_ID ?? null) === $dept->department_ID)>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ optional($t->department)->name ?? ($t->department ?? '—') }}</small>
                        </form>
                        @else
                          {{ optional($t->department)->name ?? ($t->department ?? '—') }}
                        @endif
                    </td>
                    <td>
                        @php
                            $gradesAssigned = \App\Models\YearLevelAssignment::where('teacher_ID', $t->teacher_ID)
                                ->where('year_level_assignments.status','approved')
                                ->join('year_levels','year_levels.year_level_ID','=','year_level_assignments.year_level_ID')
                                ->pluck('year_levels.grade_level')
                                ->unique()
                                ->values()
                                ->toArray();
                        @endphp
                        @if(count($gradesAssigned))
                            <span class="badge bg-info">{{ implode(', ', array_map(fn($g)=>'G'.$g, $gradesAssigned)) }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $subjectsTaught = $t->courses->map(fn($c)=>optional($c->subject)->name)->filter()->unique()->values()->toArray();
                        @endphp
                        @if(count($subjectsTaught))
                            <small>{{ implode(', ', $subjectsTaught) }}</small>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if(Auth::check() && Auth::user()->role === 'admin')
                        <form method="POST" action="{{ route('teachers.status', $t) }}" class="d-flex gap-2 align-items-center">
                            @csrf
                            @method('PUT')
                            <select name="status" class="form-select form-select-sm" style="min-width: 160px;" onchange="this.form.submit()" aria-label="Teacher status">
                                @foreach(['active','disabled','pending','on_leave'] as $st)
                                    <option value="{{ $st }}" @selected((optional($t->account)->status ?? 'active') === $st)>{{ ucfirst(str_replace('_',' ', $st)) }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ ucfirst(str_replace('_',' ', optional($t->account)->status ?? 'active')) }}</small>
                        </form>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_',' ', optional($t->account)->status ?? 'active')) }}</span>
                        @endif
                    </td>
                    <td class="text-nowrap">
                        <a href="{{ route('teachers.show', $t) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('teachers.edit', $t) }}" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-muted">No teachers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($teachers->hasPages())
    <div class="mt-3">
        {{ $teachers->links() }}
    </div>
    @endif
</div>
@endsection
