@extends('layouts.master')

@section('title','Add Teacher')
@section('page-title','Add New Teacher')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('teachers.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Account</label>
                    <select name="account_id" class="form-select @error('account_id') is-invalid @enderror">
                        <option value="">Select an account</option>
                        @foreach(($availableAccounts ?? []) as $id => $label)
                            <option value="{{ $id }}" {{ old('account_id') == $id ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('account_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">First name</label>
                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}">
                    @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Last name</label>
                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}">
                    @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Contact number</label>
                    <input type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number') }}">
                    @error('contact_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-select @error('department_id') is-invalid @enderror">
                        <option value="">Select a department</option>
                        @foreach(($departments ?? []) as $dept)
                            <option value="{{ $dept->department_ID }}" {{ old('department_id') == $dept->department_ID ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted">If not listed, type a new department name:</small>
                    <input type="text" name="department" class="form-control mt-1 @error('department') is-invalid @enderror" value="{{ old('department') }}" placeholder="Optional">
                    @error('department') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 mt-3">
                    <button class="btn btn-primary">Create teacher</button>
                    <a href="{{ route('teachers.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
