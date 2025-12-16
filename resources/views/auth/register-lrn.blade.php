@extends('layouts.auth')
@section('title','Register via LRN')
@section('content')
<div class="row justify-content-center">
  <div class="col-md-7 col-lg-6">
    <div class="card auth-card border-0">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <div class="auth-brand-icon me-2" style="width:40px;height:40px;">
            <i class="bi bi-mortarboard-fill"></i>
          </div>
          <div>
            <div class="auth-title">Student Portal Registration</div>
            <div class="auth-subtitle">For enrolled students with a valid LRN</div>
          </div>
        </div>

        <form method="POST" action="{{ route('portal.register-lrn.post') }}">
          @csrf

          <div class="mb-3">
            <label class="auth-form-label mb-1">LRN (12 digits)</label>
            <div class="input-group">
              <span class="input-group-text bg-transparent border-end-0">
                <i class="bi bi-123 text-muted"></i>
              </span>
              <input type="text" name="lrn" class="form-control border-start-0" maxlength="12" pattern="[0-9]{12}" value="{{ old('lrn') }}" required>
            </div>
            @error('lrn') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="auth-form-label mb-1">Username</label>
            <div class="input-group">
              <span class="input-group-text bg-transparent border-end-0">
                <i class="bi bi-person text-muted"></i>
              </span>
              <input type="text" name="username" class="form-control border-start-0" value="{{ old('username') }}" required>
            </div>
            @error('username') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="auth-form-label mb-1">Email (optional)</label>
            <div class="input-group">
              <span class="input-group-text bg-transparent border-end-0">
                <i class="bi bi-envelope text-muted"></i>
              </span>
              <input type="email" name="email" class="form-control border-start-0" value="{{ old('email') }}">
            </div>
            @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="auth-form-label mb-1">Password</label>
              <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0">
                  <i class="bi bi-lock text-muted"></i>
                </span>
                <input type="password" name="password" class="form-control border-start-0" minlength="6" required>
              </div>
              @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
              <label class="auth-form-label mb-1 text-nowrap">Confirm Password</label>
              <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0">
                  <i class="bi bi-shield-check text-muted"></i>
                </span>
                <input type="password" name="password_confirmation" class="form-control border-start-0" minlength="6" required>
              </div>
            </div>
          </div>

          <div class="small text-muted mt-3">
            Your account will be available after admin activation. Please ensure your enrollment is official.
          </div>

          <button class="btn btn-primary auth-primary-btn w-100 mt-3" type="submit">Create Portal Account</button>
          <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100 mt-2">
            <i class="bi bi-box-arrow-in-right me-1"></i> Back to Login
          </a>
        </form>
      </div>
    </div>
  </div>
@endsection
