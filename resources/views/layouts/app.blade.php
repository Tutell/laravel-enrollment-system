<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name','App') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GxJ5xqC2v0S1Q0Xr3QF5Q5" crossorigin="anonymous">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" role="navigation" aria-label="Student Top Navigation">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard.index') }}" aria-label="Home">{{ config('app.name','App') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @php $role = optional(auth()->user())->role; @endphp
                    @if(strtolower($role ?? '') === 'student')
                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('student.dashboard')) active @endif" href="{{ route('student.dashboard') }}" aria-label="Dashboard">Dashboard</a>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('subjects.index') }}" aria-label="Subjects">Subjects</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('sections.index') }}" aria-label="Sections">Sections</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('academic-years.index') }}" aria-label="Academic Years">Academic Years</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('students.index') }}" aria-label="Students">Students</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('teachers.index') }}" aria-label="Teachers">Teachers</a></li>
                    @endif
                </ul>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    @if(strtolower($role ?? '') === 'student')
                        <li class="nav-item"><a class="nav-link" href="{{ route('profile') }}" aria-label="Profile">Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('settings') }}" aria-label="Settings">Settings</a></li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-light btn-sm" aria-label="Logout">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('profile') }}" aria-label="Profile">Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('settings') }}" aria-label="Settings">Settings</a></li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-light btn-sm" aria-label="Logout">Logout</button>
                            </form>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    <main class="container py-4" aria-live="polite" aria-busy="false">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-6x014tYAfqVnU3YRtP2FfxhX6UuV+GkU7BfQvVfT0jNMQ2iP6kzFvTNRbW7sYlVh" crossorigin="anonymous"></script>
</body>
</html>
