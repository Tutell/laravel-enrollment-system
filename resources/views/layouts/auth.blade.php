<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Sign In')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    body {
      font-family: Inter, system-ui, -apple-system, sans-serif;
      min-height: 100vh;
      margin: 0;
      background: radial-gradient(circle at top left, #4e54c8 0%, #8f94fb 35%, #ff7eb3 100%);
      display: flex;
      align-items: stretch;
      justify-content: center;
    }

    .auth-shell {
      width: 100%;
      max-width: 1260px;
      margin: 3rem 1.5rem;
      background: rgba(255, 255, 255, 0.08);
      border-radius: 24px;
      box-shadow: 0 20px 60px rgba(15, 23, 42, 0.4);
      overflow: hidden;
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(0, 1.25fr);
      backdrop-filter: blur(18px);
    }

    .auth-hero {
      padding: 3rem 3rem 3rem 3.25rem;
      color: #ffffff;
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .auth-hero-title {
      font-size: 2rem;
      font-weight: 700;
      letter-spacing: 0.02em;
    }

    .auth-hero-text {
      font-size: 0.95rem;
      max-width: 26rem;
      margin-top: 0.75rem;
      color: rgba(255, 255, 255, 0.8);
    }

    .auth-hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.35rem 0.8rem;
      border-radius: 999px;
      background: rgba(15, 23, 42, 0.35);
      font-size: 0.75rem;
      margin-bottom: 1.25rem;
    }

    .auth-hero-shapes {
      position: absolute;
      inset: auto 0 0 0;
      padding: 0 2.75rem 2.5rem;
      display: flex;
      gap: 0.9rem;
    }

    .auth-hero-shape {
      height: 10px;
      border-radius: 999px;
      background: linear-gradient(90deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.2));
      opacity: 0.9;
    }

    .auth-hero-shape:nth-child(1) { width: 22%; }
    .auth-hero-shape:nth-child(2) { width: 14%; opacity: 0.75; }
    .auth-hero-shape:nth-child(3) { width: 30%; opacity: 0.85; }
    .auth-hero-shape:nth-child(4) { width: 18%; opacity: 0.6; }

    .auth-panel {
      background: #ffffff;
      padding: 3.25rem 3rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .auth-brand {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      margin-bottom: 2rem;
    }

    .auth-brand-icon {
      width: 40px;
      height: 40px;
      border-radius: 999px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #1A73E8, #4DB5FF);
      color: #ffffff;
      font-size: 1.4rem;
    }

    .auth-brand-text {
      font-weight: 700;
      color: #1A73E8;
    }

    .auth-title {
      font-size: 1.35rem;
      font-weight: 600;
      margin-bottom: 0.35rem;
    }

    .auth-subtitle {
      font-size: 0.9rem;
      color: #6c757d;
      margin-bottom: 1.75rem;
    }

    .auth-card {
      border-radius: 18px;
      border: 1px solid #edf0f5;
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
    }

    .auth-form-label {
      font-size: 0.85rem;
      font-weight: 500;
      color: #495057;
    }

    .form-control {
      border-radius: 999px;
    }

    .form-control:focus {
      box-shadow: 0 0 0 .18rem rgba(26, 115, 232, .17);
      border-color: #1A73E8;
    }

    .auth-footer {
      font-size: 0.8rem;
      color: #adb5bd;
      margin-top: 1.75rem;
      text-align: center;
    }

    .auth-primary-btn {
      border-radius: 999px;
      background-image: linear-gradient(135deg, #1A73E8, #4DB5FF);
      border: none;
    }

    .auth-primary-btn:hover {
      filter: brightness(1.05);
    }

    @media (max-width: 992px) {
      .auth-shell {
        margin: 1.5rem 1rem;
        grid-template-columns: minmax(0, 1fr);
      }

      .auth-hero {
        display: none;
      }

      .auth-panel {
        padding: 2.5rem 2rem;
      }
    }
  </style>
  @stack('styles')
</head>
<body>
  <main class="auth-shell">
    <section class="auth-hero" aria-hidden="true">
      <div>
        <div class="auth-hero-badge">
          <i class="bi bi-mortarboard-fill"></i>
          <span>{{ $branding->school_name ?? 'Enrollment System' }}</span>
        </div>
        <h1 class="auth-hero-title">{{ $branding->welcome_message ?? ('Welcome to '.config('app.name')) }}</h1>
        <p class="auth-hero-text">{!! $branding->subtext ?? 'Manage student enrollment, sections, and academic records.' !!}</p>
      </div>
      <div class="auth-hero-shapes">
        <div class="auth-hero-shape"></div>
        <div class="auth-hero-shape"></div>
        <div class="auth-hero-shape"></div>
        <div class="auth-hero-shape"></div>
      </div>
    </section>
    <section class="auth-panel">
      <div class="auth-brand">
        @if(!empty($branding->logo_path))
          <img src="{{ $branding->logo_path }}" alt="Logo" style="height:40px;" />
        @else
          <div class="auth-brand-icon">
            <i class="bi bi-mortarboard-fill"></i>
          </div>
        @endif
        <div class="auth-brand-text">{{ config('app.name','Enrollment System') }}</div>
      </div>
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
      @endif
      <div class="card auth-card border-0">
        <div class="card-body">
          @yield('content')
        </div>
      </div>
      <p class="auth-footer">&copy; {{ date('Y') }} {{ config('app.name','High School Enrollment System') }}</p>
    </section>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
