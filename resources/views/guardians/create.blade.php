@extends('layouts.master')

@section('title', 'Add Parent / Guardian')

@section('page-title', 'Parent / Guardian Background')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Validation Error!</strong> Please fix the following issues:
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <h5 class="mb-3">
            Student: {{ $student->first_name }} {{ $student->last_name }} (LRN: {{ $student->lrn }})
        </h5>

        <form action="{{ route('guardians.store', $student) }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full name</label>
                    <input type="text"
                           name="full_name"
                           class="form-control @error('full_name') is-invalid @enderror"
                           value="{{ old('full_name') }}">
                    @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Relationship</label>
                    <input type="text"
                           name="relationship"
                           class="form-control @error('relationship') is-invalid @enderror"
                           value="{{ old('relationship','Parent') }}">
                    @error('relationship') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Contact number</label>
                    <input type="text"
                           name="contact_number"
                           class="form-control @error('contact_number') is-invalid @enderror"
                           value="{{ old('contact_number') }}">
                    @error('contact_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email"
                           name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Address</label>
                    <input type="text"
                           name="address"
                           class="form-control @error('address') is-invalid @enderror"
                           value="{{ old('address') }}">
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Occupation</label>
                    <input type="text"
                           name="occupation"
                           class="form-control @error('occupation') is-invalid @enderror"
                           value="{{ old('occupation') }}">
                    @error('occupation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Save background</button>
                    <a href="{{ route('students.show', $student) }}" class="btn btn-secondary ms-2">Skip for now</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

