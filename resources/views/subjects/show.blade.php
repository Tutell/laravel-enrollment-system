@extends('layouts.master')

@section('content')
<div class="container">
    <h1>{{ $subject->name }}</h1>
    <p>{{ $subject->description }}</p>
    <h3>Courses</h3>
    <ul>
        @foreach($subject->courses as $course)
            <li>{{ $course->course_code }}</li>
        @endforeach
    </ul>
    <a href="{{ route('subjects.edit',$subject) }}" class="btn btn-warning">Edit</a>
</div>
@endsection

