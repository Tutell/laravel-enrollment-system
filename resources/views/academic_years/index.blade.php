@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Academic Years</h1>
    <a href="{{ route('academic-years.create') }}" class="btn btn-primary">Add Academic Year</a>
    <table class="table mt-3">
        <thead>
            <tr><th>#</th><th>School Year</th><th>Semester</th><th>Active</th><th>Actions</th></tr>
        </thead>
        <tbody>
        @foreach($academicYears as $ay)
            <tr>
                <td>{{ $ay->academic_year_id }}</td>
                <td>{{ $ay->school_year }}</td>
                <td>{{ $ay->semester }}</td>
                <td>{{ $ay->is_active ? 'Yes' : 'No' }}</td>
                <td>
                    <a href="{{ route('academic-years.show',$ay) }}" class="btn btn-sm btn-info">View</a>
                    <a href="{{ route('academic-years.edit',$ay) }}" class="btn btn-sm btn-warning">Edit</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $academicYears->links() }}
</div>
@endsection
