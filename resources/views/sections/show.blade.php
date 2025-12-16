@extends('layouts.master')

@section('content')
<div class="container">
    <h1>{{ $section->section_name }}</h1>
    <p>Grade Level: {{ $section->grade_level }}</p>
    <p>Capacity: {{ $section->capacity }}</p>

    <h3>Assigned Teachers</h3>
    @if($section->teachers->isNotEmpty())
        <ul class="mb-3">
            @foreach($section->teachers as $teacher)
                <li class="d-flex align-items-center justify-content-between">
                    <span>{{ $teacher->first_name }} {{ $teacher->last_name }}</span>
                    <form action="{{ route('sections.teachers.remove', [$section, $teacher]) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Remove</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-muted">No teachers assigned to this section.</p>
    @endif

    <form method="POST" action="{{ route('sections.teachers.assign', $section) }}" class="mb-4">
        @csrf
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Assign teacher</label>
                <select name="teacher_id" id="teacherSearchSelect" class="form-select">
                    <option value="">Select teacher</option>
                    @foreach($allTeachers as $teacher)
                        <option value="{{ $teacher->teacher_ID }}">
                            {{ $teacher->last_name }}, {{ $teacher->first_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" id="teacherSearchInput" class="form-control" placeholder="Search teacher">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100">Assign</button>
            </div>
        </div>
    </form>

    <h3>Students</h3>
    <ul>
        @foreach($section->students as $student)
            <li>{{ $student->first_name }} {{ $student->last_name }}</li>
        @endforeach
    </ul>
    <a href="{{ route('sections.edit',$section) }}" class="btn btn-warning">Edit</a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('teacherSearchInput');
    const select = document.getElementById('teacherSearchSelect');

    if (!searchInput || !select) return;

    searchInput.addEventListener('input', function () {
        const term = this.value.toLowerCase();
        Array.from(select.options).forEach(option => {
            if (!option.value) return;
            const text = option.text.toLowerCase();
            option.hidden = term && !text.includes(term);
        });
    });
});
</script>
@endsection
