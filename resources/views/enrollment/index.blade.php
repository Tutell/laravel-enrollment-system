@extends('layouts.master')

@section('content')
<div class="container">
    <h1 class="h3 mb-3">Enrollment</h1>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Status</th>
                    <th>Processed By</th>
                    <th>Processed At</th>
                </tr>
            </thead>
            <tbody>
            @forelse($enrollments as $enrollment)
                <tr>
                    <td>{{ $enrollment->enrollment_id ?? $enrollment->enrollment_ID ?? '' }}</td>
                    <td>{{ optional($enrollment->student)->first_name }} {{ optional($enrollment->student)->last_name }}</td>
                    <td>{{ optional($enrollment->course)->course_code }}</td>
                    <td>{{ $enrollment->status }}</td>
                    <td>{{ optional($enrollment->student->account)->username ?? 'N/A' }}</td>
                    <td>{{ optional($enrollment->processed_at)->toDateTimeString() }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-muted">No data yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
