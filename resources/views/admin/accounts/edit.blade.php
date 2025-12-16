@extends('layouts.master')

@section('title','Edit Account')

@section('page-title','Edit Account')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.accounts.update', $account) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username', $account->username) }}">
                    @error('username') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $account->email) }}">
                    @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        @foreach(['student','teacher','admin','parent','faculty'] as $role)
                            <option value="{{ $role }}" {{ old('role', $account->role) === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                    @error('role') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" {{ old('status', $account->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="disabled" {{ old('status', $account->status ?? 'active') === 'disabled' ? 'selected' : '' }}>Disabled</option>
                    </select>
                    @error('status') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">New password (optional)</label>
                    <input type="password" name="password" class="form-control">
                    @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 mt-3">
                    <button class="btn btn-primary">Save changes</button>
                    <a href="{{ route('admin.accounts.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

