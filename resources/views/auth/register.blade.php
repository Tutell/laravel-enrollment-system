@extends('layouts.auth')

@section('title', 'Register')
@section('content')
        <h1 class="auth-title">Create your account</h1>
        <p class="auth-subtitle">Register as a student or administrator to access the system.</p>
        <form method="POST" action="{{ route('register.post') }}">
          @csrf
          <div class="row g-3">
            <div class="col-12">
              <label class="auth-form-label mb-1">Email</label>
              <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0">
                  <i class="bi bi-envelope text-muted"></i>
                </span>
                <input type="email" name="email" class="form-control border-start-0" value="{{ old('email') }}" required>
              </div>
            </div>
            <div class="col-12">
              <label class="auth-form-label mb-1">Username</label>
              <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0">
                  <i class="bi bi-person text-muted"></i>
                </span>
                <input type="text" name="username" class="form-control border-start-0" value="{{ old('username') }}" required>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <label class="auth-form-label mb-1">Password</label>
              <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0">
                  <i class="bi bi-lock text-muted"></i>
                </span>
                <input type="password" name="password" class="form-control border-start-0" required minlength="6">
              </div>
            </div>
            <div class="col-12 col-md-6">
              <label class="auth-form-label mb-1">Confirm Password</label>
              <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0">
                  <i class="bi bi-lock-fill text-muted"></i>
                </span>
                <input type="password" name="password_confirmation" class="form-control border-start-0" required minlength="6">
              </div>
            </div>
            <div class="col-12">
              <label class="auth-form-label mb-1">Role</label>
              <select class="form-select" name="role" required>
                <option value="student">Student</option>
                <option value="admin">Admin</option>
              </select>
            </div>
          </div>
          <button class="btn btn-primary auth-primary-btn w-100 mt-3" type="submit">Register</button>
        </form>
      
@endsection
