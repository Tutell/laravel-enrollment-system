## Objectives
- Redesign `layouts/master.blade.php` with a premium UI/UX theme (color, typography, animation, accessibility).
- Make login the default landing page; redirect authenticated users to the dashboard.
- Restrict registration to Students and Admin only (remove Parents and Faculty from registration).
- Preserve existing Blade sections, routes, and backend logic while improving asset performance and accessibility.

## Visual System
- Color Palette: professional gradient scheme (primary blue → accent azure, supporting neutrals). Document hex values and gradient definitions.
- Typography: pair modern sans-serif (Inter/Source Sans 3) with readable headings; include font preconnects and fallbacks.
- Icons: Bootstrap Icons already integrated; add consistent sizing and spacing tokens.
- Layout: responsive container system (lg/xxl breakpoints), grid-based spacing, card shadows, rounded radii.
- Motion: subtle transitions for nav, cards, alerts with prefers-reduced-motion support.

## Master Layout Redesign
- Header/Top Nav: premium bar with branding, user menu, and quick actions using accessible controls.
- Sidebar: collapsible, keyboard accessible; active route highlighting; ARIA roles.
- Content Wrapper: improved padding, breadcrumbs, page header/actions area; consistent alert/toast placements.
- Footer: lightweight with version and SY info; reduced footprint on mobile.
- Asset Strategy: keep conditional Vite/CDN loading; preload fonts; defer non-critical scripts.
- Dark Mode: maintain CSS variables for easy theme switching.
- Backward Compatibility: maintain `@yield('content')`, optional `@section('breadcrumbs')`, `@stack('styles')`, `@stack('scripts')`, and current includes; do not change route helpers.

## Authentication Flow Changes
- Default Landing: unauthenticated users hitting `/` are redirected to `login` (middleware or route change). Authenticated users go to `dashboard.index`.
- Login Page: premium-styled form with email/username + password, error states, remember me, accessible labels.
- Registration Page: premium-styled, role dropdown limited to `student` and `admin`; server-side validation enforces allowed roles.
- Session Management: use Laravel Auth; CSRF protection; rate-limit login; logout clears session.
- Error Handling: standardized error messaging blocks and toasts; avoid leaking credential details.

## Accessibility (WCAG)
- Semantic roles for nav (`nav`, `aside`, `main`, `footer`), `aria-label`s for landmarks.
- Keyboard navigation: focus outlines, skip-to-content link, toggles reachable via keyboard.
- Color contrast: meet AA standards; ensure hover/focus states are distinguishable.
- Form labels and `aria-describedby` for validation feedback.
- Prefers-reduced-motion media query for animations.

## Performance Optimization
- Font preconnect/preload; limit weights; fallback stack.
- Conditional asset pipeline: Vite build when available; CDN fallback otherwise.
- Lazy-load large images/illustrations; compress SVG where possible.
- Minimize JS; leverage CSS transitions; paginate and eager-load data in views.

## Quality Standards
- Cross-browser: test on Chrome, Edge, Firefox, Safari; iOS/Android.
- Mobile responsiveness: verify on common device sizes; sidebar collapse behavior.
- Performance benchmarks: Lighthouse/Pagespeed checks; bundle sizes; first contentful paint.
- Security review: CSRF, rate limiting, lockouts, session fixation prevention.
- UX testing: task flows (login, register, navigate modules), feedback collection.

## Implementation Steps
1. Update `layouts/master.blade.php`:
   - Refactor header, sidebar, content wrapper with premium styling; keep all `@yield`/`@stack` and includes.
   - Add skip link, ARIA attributes, focus-visible styles, prefers-reduced-motion.
   - Retain conditional Vite/CDN block and Bootstrap Icons.
2. Update auth views (login/register):
   - Apply premium card layout, typography, error states, and accessible labels.
   - Registration: restrict role selection to `student` and `admin` (and remove Parents/Faculty options).
3. Update auth controller and routes:
   - Enforce allowed roles server-side; normalize roles for DB enum as needed (`admin`/`student`).
   - Middleware/route change: unauthenticated `/` → `login`, authenticated → `dashboard.index`.
4. Accessibility pass:
   - Add ARIA landmarks and labels; ensure keyboard navigation works; color contrast checks.
5. Performance tweaks:
   - Preconnect/preload fonts; defer non-critical scripts; ensure images are optimized.
6. QA:
   - Cross-browser and device matrix; Lighthouse audit; authentication security review; UX smoke tests.
7. Documentation:
   - Notes on theme tokens, accessibility, asset pipeline, and testing checklist.

## Deliverables
- Premium master layout preserving Blade functionality.
- Premium login and registration pages (students/admin only).
- Updated auth flow with default landing and redirects.
- Accessibility and performance improvements verified.

## Assumptions
- Keep existing backend and routes; adjust only middleware/redirect logic and auth role validation.
- Continue using Bootstrap foundation with custom CSS variables and design system.

Please confirm, and I will implement the redesign and auth flow updates immediately.