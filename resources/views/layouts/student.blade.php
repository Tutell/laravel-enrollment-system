<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Student')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  @stack('styles')
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary" role="navigation" aria-label="Student Navigation">
    <div class="container">
      <a class="navbar-brand" href="{{ route('student.dashboard') }}" aria-label="Student Dashboard">{{ config('app.name','Enrollment System') }}</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#studentNav" aria-controls="studentNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="studentNav">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          @php $role = optional(auth()->user())->role; @endphp
          @if(strtolower($role ?? '') === 'student')
            <li class="nav-item"><a class="nav-link @if(request()->routeIs('student.dashboard')) active @endif" href="{{ route('student.dashboard') }}">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('profile') }}">Profile</a></li>
          @endif
        </ul>
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <main class="container py-4" aria-live="polite">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
    @endif
    @yield('content')
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
