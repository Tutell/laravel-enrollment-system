@extends('layouts.master')

@section('title', 'Edit Student')

@section('page-title', 'Edit Student')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('students.update', $student) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">First name</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $student->first_name) }}">
                    @error('first_name') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Last name</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $student->last_name) }}">
                    @error('last_name') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">LRN</label>
                    <div class="input-group" data-bs-toggle="tooltip" data-bs-title="LRN cannot be modified after initial registration">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                        <input type="text" class="form-control bg-light" value="{{ $student->lrn }}" disabled>
                    </div>
                    @error('lrn') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $student->email) }}">
                    @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Contact number</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->phone) }}">
                    @error('phone') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Account</label>
                    <select name="account_id" class="form-select">
                        <option value="">Select an account</option>
                        @foreach(($accounts ?? []) as $id => $label)
                            <option value="{{ $id }}" {{ (old('account_id', $student->account_id) == $id) ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('account_id') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Section</label>
                    <select name="section_id" class="form-select">
                        <option value="">Select section (optional)</option>
                        @foreach(($sections ?? []) as $id => $name)
                            <option value="{{ $id }}" {{ (old('section_id', $student->section_id) == $id) ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('section_id') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="">Select</option>
                        <option value="Male" {{ old('gender', $student->gender)==='Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $student->gender)==='Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $student->gender)==='Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Birthdate</label>
                    <input type="date" name="birthdate" class="form-control" value="{{ old('birthdate', optional($student->birthdate)->format('Y-m-d')) }}">
                    @error('birthdate') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-8">
                    <label class="form-label">Home address</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $student->address) }}">
                    @error('address') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 mt-3">
                    <button class="btn btn-primary">Save changes</button>
                    <a href="{{ route('students.show', $student) }}" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
