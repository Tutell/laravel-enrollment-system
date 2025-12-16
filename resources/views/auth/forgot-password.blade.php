@extends('layouts.auth')

@section('title', 'Forgot Password')
@section('content')
        <h1 class="h5 mb-3">Reset your password</h1>
        <form method="POST" action="{{ url('/forgot-password') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <button class="btn btn-primary w-100" type="submit">Send reset link</button>
        </form>
      
@endsection
