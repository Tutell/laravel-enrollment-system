@extends('layouts.master')

@section('content')
<div class="container">
    <h1 class="h3 mb-3">Edit Subject</h1>
    <form method="POST" action="{{ route('subjects.update', $subject) }}">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name',$subject->name) }}">
                @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Grade Level</label>
                <select name="grade_level" class="form-select">
                    @foreach([7,8,9,10] as $g)
                        <option value="{{ $g }}" {{ old('grade_level',$subject->grade_level) == $g ? 'selected' : '' }}>Grade {{ $g }}</option>
                    @endforeach
                </select>
                @error('grade_level') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description',$subject->description) }}</textarea>
                @error('description') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="mt-3">
            <button class="btn btn-primary">Save changes</button>
            <a href="{{ route('subjects.index') }}" class="btn btn-secondary ms-2">Cancel</a>
        </div>
    </form>
 </div>
@endsection

