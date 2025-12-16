@extends('layouts.master')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Subjects</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('subjects.assign-teachers') }}" class="btn btn-outline-success">Assign Teachers</a>
            <a href="{{ route('subjects.create') }}" class="btn btn-primary">Add Subject</a>
        </div>
    </div>

    @php
        $grades = [7,8,9,10];
    @endphp

    @foreach($grades as $g)
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">Grade {{ $g }}</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Code</th><th>Name</th><th>Actions</th></tr></thead>
                    <tbody>
                    @forelse($subjects->where('grade_level', $g) as $subject)
                        <tr>
                            <td>{{ strtoupper(str_replace(' ', '-', $subject->name)) }}-G{{ $g }}</td>
                            <td>{{ $subject->name }}</td>
                            <td>
                                <a href="{{ route('subjects.show',$subject) }}" class="btn btn-sm btn-info">View</a>
                                <a href="{{ route('subjects.edit',$subject) }}" class="btn btn-sm btn-warning">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-muted">No subjects for Grade {{ $g }} yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach

    @php
        $unassigned = $subjects->where('grade_level', null);
    @endphp
    @if($unassigned->count())
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5 mb-3">Unassigned</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>Code</th><th>Name</th><th>Actions</th></tr></thead>
                    <tbody>
                    @foreach($unassigned as $subject)
                        <tr>
                            <td>{{ strtoupper(str_replace(' ', '-', $subject->name)) }}-GNA</td>
                            <td>{{ $subject->name }}</td>
                            <td>
                                <a href="{{ route('subjects.edit',$subject) }}" class="btn btn-sm btn-warning">Assign Grade</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
