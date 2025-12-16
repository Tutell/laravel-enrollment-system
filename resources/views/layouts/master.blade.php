<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Primary Meta Tags -->
    <title>@yield('title', config('app.name', 'Enrollment System'))</title>
    <meta name="description" content="High School Enrollment Management System">
    
    <!-- Favicon & Theme Color -->
    <link rel="icon" type="image/x-icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🎓</text></svg>">
    <meta name="theme-color" content="#1A73E8">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 with Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Custom Styles with Enhanced Design -->
    <style>
        :root {
            --primary: #1A73E8;
            --primary-dark: #0F4EB3;
            --primary-light: rgba(26, 115, 232, 0.1);
            --background: #F7F9FC;
            --surface: #FFFFFF;
            --accent: #4DB5FF;
            --success: #34A853;
            --warning: #FBBC05;
            --danger: #EA4335;
            --text-primary: #202124;
            --text-secondary: #5F6368;
            --border: #E8EAED;
            --shadow-sm: 0 2px 8px rgba(26, 115, 232, 0.08);
            --shadow-md: 0 4px 12px rgba(26, 115, 232, 0.12);
            --shadow-lg: 0 8px 24px rgba(26, 115, 232, 0.16);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, var(--background) 0%, #ffffff 100%);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Sidebar Navigation - Unique Feature */
        .sidebar-container {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 280px;
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            z-index: 1000;
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s ease;
            box-shadow: var(--shadow-lg);
        }

        .sidebar-container.active {
            transform: translateX(0);
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            margin: 0.25rem 0.75rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateX(4px);
        }

        .nav-link i {
            font-size: 1.25rem;
            width: 24px;
            text-align: center;
        }

        /* Desktop collapsed sidebar (fully hidden) */
        @media (min-width: 992px) {
            body.layout-sidebar-collapsed .sidebar-container {
                width: 0;
                transform: translateX(-100%);
                visibility: hidden;
            }
        }

        /* Main Content Area */
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            padding-top: 70px;
        }

        @media (min-width: 992px) {
            .sidebar-container {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 280px;
            }
            body.layout-sidebar-collapsed .main-content {
                margin-left: 0;
            }
        }

        /* Top Navigation Bar */
        .top-nav {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            background: var(--surface);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            z-index: 999;
            padding: 0.75rem 1.5rem;
            box-shadow: var(--shadow-sm);
        }

        @media (min-width: 992px) {
            .top-nav {
                left: 280px;
            }
            body.layout-sidebar-collapsed .top-nav {
                left: 80px;
            }
        }

        .nav-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-brand:hover {
            color: var(--primary-dark);
        }

        /* Statistics Cards - Unique Feature */
        .stat-card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-light);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);
        }

        .table thead th {
            background-color: rgba(255, 255, 255, 0.9);
            color: var(--text-primary);
            border-bottom-color: rgba(232, 234, 237, 0.8);
            font-weight: 600;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .table-striped tbody tr:nth-of-type(even) {
            background-color: rgba(0, 0, 0, 0.01);
        }

        .text-muted {
            color: var(--text-secondary) !important;
        }

        .card .text-muted {
            color: var(--text-secondary) !important;
        }

        /* Quick Actions */
        .quick-action {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            border: 2px solid transparent;
        }

        .quick-action:hover {
            background: var(--primary);
            color: white;
            transform: scale(1.05);
            border-color: var(--primary);
        }

        /* Enhanced Alerts */
        .alert-enhanced {
            border: none;
            border-radius: var(--radius-md);
            padding: 1rem 1.25rem;
            box-shadow: var(--shadow-sm);
            border-left: 4px solid;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Loading Skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Content Wrapper */
        .content-wrapper {
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-md);
            animation: fadeIn 0.4s ease-out;
            border: 1px solid var(--border);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 1.25rem;
                margin: 0 -1rem;
                border-radius: 0;
                border-left: none;
                border-right: none;
            }
            
            .top-nav {
                padding: 0.75rem 1rem;
            }
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            :root {
                --background: #121212;
                --surface: #1e1e1e;
                --text-primary: #f5f5f5;
                --text-secondary: #d0d3d9;
                --border: #2d2d2d;
            }
            
            body {
                background: linear-gradient(135deg, #121212 0%, #1a1a1a 100%);
            }

            .table thead th {
                background-color: #22252a;
                color: #f5f5f5;
                border-bottom-color: #3a3d45;
            }

            .table-striped tbody tr:nth-of-type(odd) {
                background-color: #1b1e24;
            }

            .table-striped tbody tr:nth-of-type(even) {
                background-color: #181b20;
            }

.table-hover tbody tr {
    transition: all 0.2s ease;
}

.table-hover tbody tr:hover {
    background-color: rgba(26, 115, 232, 0.12);
    transform: translateX(2px);
}

.badge {
    padding: 0.35em 0.65em;
    font-weight: 500;
}

        }
    </style>

    @stack('styles')
    @php $hasManifest = file_exists(public_path('build/manifest.json')); @endphp
    @if($hasManifest)
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    @endif
</head>

<body>
    <a href="#mainContent" class="visually-hidden-focusable position-absolute top-0 start-0 m-2 px-3 py-2 bg-white border rounded" style="z-index:2000">Skip to content</a>
    <!-- Sidebar Navigation -->
    <aside class="sidebar-container" id="sidebar" role="navigation" aria-label="Sidebar Navigation" aria-hidden="false">
        <div class="sidebar-header">
            <h2 class="h4 mb-0">
                <i class="bi bi-mortarboard-fill me-2"></i>
                {{ config('app.name', 'Enrollment System') }}
            </h2>
            <small class="text-white-50">{{ $branding->school_name ?? 'High School Management' }}</small>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-item">
                <a class="nav-link @if(request()->routeIs('dashboard.*')) active @endif"
                   href="{{ route('dashboard.index') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                    <span class="badge bg-success ms-auto">New</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a class="nav-link @if(request()->routeIs('students.*')) active @endif"
                   href="{{ route('students.index') }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Students</span>
                </a>
                <div class="ms-3" @if(!request()->routeIs('students.*')) style="display: none;" @endif>
                    <a class="nav-link py-1 @if(request()->routeIs('students.index')) active @endif" 
                       href="{{ route('students.index') }}" style="font-size: 0.9rem;">
                        <i class="bi bi-list me-2"></i>All Students
                    </a>
                    <a class="nav-link py-1 @if(request()->routeIs('students.enrollment')) active @endif" 
                       href="{{ route('students.enrollment') }}" style="font-size: 0.9rem;">
                        <i class="bi bi-graph-up me-2"></i>Enrollment by Grade
                    </a>
                    <a class="nav-link py-1 @if(request()->routeIs('students.manage')) active @endif" 
                       href="{{ route('students.manage') }}" style="font-size: 0.9rem;">
                        <i class="bi bi-people me-2"></i>Manage by Grade & Section
                    </a>
                    <a class="nav-link py-1 @if(request()->routeIs('grades.manage')) active @endif" 
                       href="{{ route('grades.manage') }}" style="font-size: 0.9rem;">
                        <i class="bi bi-journal-check me-2"></i>Grades – Manage
                    </a>
                    <a class="nav-link py-1 @if(request()->routeIs('students.create')) active @endif" 
                       href="{{ route('students.create') }}" style="font-size: 0.9rem;">
                        <i class="bi bi-plus-circle me-2"></i>New Student
                    </a>
                    <a class="nav-link py-1 @if(request()->routeIs('students.archive')) active @endif" 
                       href="{{ route('students.archive') }}" style="font-size: 0.9rem;">
                        <i class="bi bi-archive me-2"></i>Archived Students
                    </a>
                </div>
            </div>
            
            <div class="nav-item">
                <a class="nav-link @if(request()->routeIs('teachers.*')) active @endif"
                   href="{{ route('teachers.index') }}">
                    <i class="bi bi-person-badge-fill"></i>
                    <span>Teachers</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a class="nav-link @if(request()->routeIs('year-levels.*')) active @endif"
                   href="{{ route('year-levels.index') }}">
                    <i class="bi bi-collection"></i>
                    <span>Year Levels</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a class="nav-link @if(request()->routeIs('subjects.*')) active @endif"
                   href="{{ route('subjects.index') }}">
                    <i class="bi bi-book-half"></i>
                    <span>Subjects</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a class="nav-link @if(request()->routeIs('sections.*')) active @endif"
                   href="{{ route('sections.index') }}">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                    <span>Sections</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a class="nav-link @if(request()->routeIs('departments.*')) active @endif"
                   href="{{ route('departments.index') }}">
                    <i class="bi bi-building"></i>
                    <span>Departments</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a class="nav-link @if(request()->routeIs('academic-years.*')) active @endif"
                   href="{{ route('academic-years.index') }}">
                    <i class="bi bi-calendar-week-fill"></i>
                    <span>Academic Years</span>
                </a>
            </div>
            
            @if(Auth::check() && Auth::user()->role === 'admin')
            <div class="nav-item">
                <a class="nav-link @if(request()->routeIs('admin.accounts.*')) active @endif"
                   href="{{ route('admin.accounts.index') }}">
                    <i class="bi bi-people-gear"></i>
                    <span>Manage Accounts</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link @if(request()->routeIs('admin.branding.*')) active @endif"
                   href="{{ route('admin.branding.show') }}">
                    <i class="bi bi-brush"></i>
                    <span>System Branding</span>
                </a>
            </div>
            @endif
            
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Top Navigation -->
        <nav class="top-nav">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Sidebar Toggle -->
                <button class="btn btn-outline-primary" id="sidebarToggle"
                        type="button"
                        aria-label="Toggle navigation sidebar"
                        aria-expanded="true"
                        aria-controls="sidebar">
                    <i class="bi bi-list"></i>
                </button>
                
                <!-- Current Page Title -->
                <h4 class="mb-0 text-primary fw-semibold d-none d-md-block">
                    @yield('page-title', 'Dashboard')
                </h4>
                
                <!-- Quick Actions & User Menu -->
                <div class="d-flex align-items-center gap-3">
                    <!-- Quick Actions -->
                    <div class="d-none d-md-flex gap-2">
                        <a href="{{ route('students.create') }}" class="quick-action">
                            <i class="bi bi-plus-circle"></i>
                            New Student
                        </a>
                        <a href="{{ route('sections.create') }}" class="quick-action">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                            New Section
                        </a>
                    </div>
                    
                    <!-- User Menu -->
                    <div class="dropdown">
                        <button class="btn btn-link text-decoration-none p-0" type="button" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div class="d-none d-md-block">
                                    <div class="small fw-semibold">Administrator</div>
                                    <div class="x-small text-muted">High School Staff</div>
                                </div>
                                <i class="bi bi-chevron-down text-muted"></i>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('settings') }}"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main id="mainContent" class="container-fluid py-4 px-3 px-lg-4" role="main" tabindex="-1">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert-enhanced alert-success alert-dismissible fade show mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <div class="flex-grow-1">{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-enhanced alert-danger alert-dismissible fade show mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        <div class="flex-grow-1">{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert-enhanced alert-warning alert-dismissible fade show mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-2">Please fix the following errors:</h6>
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            <div class="content-wrapper">
                @includeIf('partials.toasts')
                @hasSection('breadcrumbs')
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb bg-transparent p-0">
                            @yield('breadcrumbs')
                        </ol>
                    </nav>
                @endif

                @hasSection('page-header')
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="h3 mb-1 fw-bold text-primary">@yield('page-header')</h1>
                            @hasSection('page-description')
                                <p class="text-muted mb-0">@yield('page-description')</p>
                            @endif
                        </div>
                        @hasSection('page-actions')
                            <div class="d-flex gap-2">
                                @yield('page-actions')
                            </div>
                        @endif
                    </div>
                @endif

                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="mt-5 pt-4 border-top text-center text-muted">
                <div class="container">
                    <p class="mb-1">
                        &copy; {{ date('Y') }} {{ config('app.name', 'High School Enrollment System') }}. All rights reserved.
                    </p>
                    <p class="small">
                        <span class="badge bg-primary">v1.0</span>
                        Current Academic Year: {{ date('Y') }}-{{ date('Y')+1 }}
                        <span class="mx-2">•</span>
                        Last Updated: {{ now()->format('M d, Y') }}
                    </p>
                </div>
            </footer>
        </main>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        (function () {
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
            if (prefersReducedMotion.matches) {
                document.documentElement.classList.add('reduce-motion');
            }
            prefersReducedMotion.addEventListener?.('change', (e) => {
                if (e.matches) document.documentElement.classList.add('reduce-motion');
                else document.documentElement.classList.remove('reduce-motion');
            });
        }());

        // Sidebar toggle functionality (mobile overlay + desktop collapse)
        (function () {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');

            if (!sidebar || !toggleBtn) return;

            function isDesktop() {
                return window.innerWidth >= 992;
            }

            function setAriaExpanded(expanded) {
                toggleBtn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            }
            function setSidebarHidden(hidden) {
                sidebar.setAttribute('aria-hidden', hidden ? 'true' : 'false');
            }
            function applyPersistedState() {
                try {
                    const persisted = localStorage.getItem('sidebarCollapsed');
                    const collapsed = persisted === '1';
                    if (isDesktop()) {
                        document.body.classList.toggle('layout-sidebar-collapsed', collapsed);
                        setAriaExpanded(!collapsed);
                        setSidebarHidden(collapsed);
                    } else {
                        // On mobile, use overlay behavior
                        sidebar.classList.remove('active');
                        setAriaExpanded(false);
                        setSidebarHidden(false);
                    }
                } catch (e) {
                    // ignore
                }
            }

            toggleBtn.addEventListener('click', function () {
                if (isDesktop()) {
                    document.body.classList.toggle('layout-sidebar-collapsed');
                    const collapsed = document.body.classList.contains('layout-sidebar-collapsed');
                    setAriaExpanded(!collapsed);
                    setSidebarHidden(collapsed);
                    try { localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0'); } catch (e) {}
                } else {
                    sidebar.classList.toggle('active');
                    const open = sidebar.classList.contains('active');
                    setAriaExpanded(open);
                    setSidebarHidden(!open);
                }
            });

            // Initialize persisted state
            applyPersistedState();

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function (event) {
                if (!sidebar || !toggleBtn) return;

                if (isDesktop()) return;

                if (!sidebar.contains(event.target) &&
                    !toggleBtn.contains(event.target) &&
                    sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                    setAriaExpanded(false);
                    setSidebarHidden(true);
                }
            });

            // Keep ARIA state in sync on resize
            window.addEventListener('resize', function () {
                if (isDesktop()) {
                    sidebar.classList.remove('active');
                    const collapsed = document.body.classList.contains('layout-sidebar-collapsed');
                    setAriaExpanded(!collapsed);
                    setSidebarHidden(collapsed);
                    applyPersistedState();
                } else {
                    document.body.classList.remove('layout-sidebar-collapsed');
                    setAriaExpanded(sidebar.classList.contains('active'));
                    setSidebarHidden(!sidebar.classList.contains('active'));
                }
            });
        }());

        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert-enhanced');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Client-side form validation UX
        (function () {
            document.addEventListener('submit', function (e) {
                const form = e.target;
                if (form.tagName !== 'FORM') return;
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    form.classList.add('was-validated');
                    const firstInvalid = form.querySelector(':invalid');
                    if (firstInvalid) {
                        firstInvalid.focus({ preventScroll: false });
                        firstInvalid.setAttribute('aria-invalid', 'true');
                    }
                }
            }, true);
        }());

        // Add active class to current route
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            document.querySelectorAll('.nav-link').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Lazy-load images with data-src
        (function () {
            const supportsLazy = 'IntersectionObserver' in window;
            if (!supportsLazy) return;
            const io = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        const src = img.getAttribute('data-src');
                        if (src) {
                            img.src = src;
                            img.removeAttribute('data-src');
                        }
                        img.setAttribute('loading', 'lazy');
                        io.unobserve(img);
                    }
                });
            }, { rootMargin: '200px' });
            document.querySelectorAll('img[data-src]').forEach(img => io.observe(img));
        }());
    </script>

    @stack('scripts')
</body>
</html>
