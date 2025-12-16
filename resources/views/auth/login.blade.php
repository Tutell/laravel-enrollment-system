@extends('layouts.auth')

@section('title', 'Login')
@section('content')
        <h1 class="auth-title">Sign in to your account</h1>
        <p class="auth-subtitle">Access the enrollment dashboard and manage records securely.</p>

        <a href="{{ route('portal.register-lrn') }}" class="btn btn-outline-success w-100 mb-3">
          <i class="bi bi-mortarboard me-1"></i> Register via LRN
        </a>
        <div class="text-center text-muted mb-3" style="font-size:0.85rem;">For Enrolled Students Only</div>

        <form method="POST" action="{{ route('login.post') }}">
          @csrf
          <div class="mb-3">
            <label class="auth-form-label mb-1">Email</label>
            <div class="input-group">
              <span class="input-group-text bg-transparent border-end-0">
                <i class="bi bi-person text-muted"></i>
              </span>
              <input type="email" name="email" class="form-control border-start-0" value="{{ old('email') }}">
            </div>
          </div>
          <div class="mb-3">
            <label class="auth-form-label mb-1">Password</label>
            <div class="input-group">
              <span class="input-group-text bg-transparent border-end-0">
                <i class="bi bi-lock text-muted"></i>
              </span>
              <input type="password" name="password" class="form-control border-start-0" required>
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="remember" name="remember">
              <label class="form-check-label" for="remember" style="font-size:0.85rem;">Remember me</label>
            </div>
            <a href="{{ url('/forgot-password') }}" style="font-size:0.85rem;">Forgot password?</a>
          </div>
          <button class="btn btn-primary auth-primary-btn w-100" type="submit">Login</button>
        </form>
        <div class="text-center mt-3" style="font-size:0.9rem;">
          <span class="text-muted">New here? Please contact an administrator to create your account.</span>
        </div>
@endsection
