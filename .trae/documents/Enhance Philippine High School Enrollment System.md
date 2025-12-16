## Current State Analysis
- Framework: Laravel 12, Vite, Bootstrap 5 (CDN), PHPUnit.
- Data: SQLite in dev; migrations use uppercase column names (e.g., `student_ID`), while models use lowercase (e.g., `student_id`).
- Modules present: Students, Teachers, Subjects, Sections, Courses, Academic Years, Enrollment, Grades.
- Routes: REST resources for core modules; dashboard at `/`. No full auth scaffolding implemented.
- Views: CRUD lists for several modules; master layout exists; dashboard implemented; some pages missing (teachers/courses detailed views, auth).
- Roles: `roles` table + `account_roles` pivot, plus `accounts.role` enum; inconsistent usage.

## Key Constraints & Risks
- Column-name mismatches between migrations and models can break relations, updates, and route binding.
- Mixed role implementations (pivot + enum) complicate RBAC.
- No payment provider configured; Philippine methods (GCash/Maya/PayMongo) needed.
- Limited validation and tests; no performance budget or accessibility baseline.

## Roadmap (Phased, Prioritized)
### Phase 1: Foundations (High Priority)
1. Data model harmonization: align models to migration column names or add migrations to rename columns to snake_case; update relations consistently.
2. Full auth scaffolding with email/password + password reset (Laravel Breeze) and seed base roles.
3. RBAC baseline: policies/middlewares for Students, Parents, Faculty, Admin; unify on `account_roles` pivot for flexibility.
4. Convert dev DB to MySQL in `.env` option; keep SQLite for local quick start.

### Phase 2: Core Enrollment Workflow
1. Multi-step enrollment wizard (profile → academics → requirements → review → submit).
2. Document upload & verification (storage + status tracking). Require PDF/JPG; file size limits.
3. Payment integration (PayMongo) with GCash/Maya; webhook handling; receipts.
4. Notifications: email confirmations and dashboard alerts for status changes.

### Phase 3: Views & UX (Bootstrap 5, Philippines-themed)
1. Authentication views: login/register/forgot-password.
2. Admin dashboard: KPIs (enrollment, capacity, payments), shortcuts, reports.
3. Faculty: class rosters, grade submission, attendance quick inputs.
4. Parents: student progress, attendance, billing history.
5. Students: enrollment status, schedule, requirements checklist.
6. UI enhancements: form validation (real-time), responsive tables (sorting/filtering), smooth page transitions, WCAG-compliant semantics.
7. Branding: Philippine flag-inspired palette (blue #0038A8, red #CE1126, yellow #FCD116), modern serif/sans font pairing, school crest placeholder.

### Phase 4: Quality Assurance & Performance
1. Unit and feature tests for models, controllers, policies, enrollment flow, payments, uploads.
2. Cross-browser/device matrix (Chrome/Edge/Safari/iOS/Android; low-resolution devices common locally).
3. Usability testing scripts and feedback loop; iterate on top tasks.
4. Performance: minimize JS/CSS, lazy load images, cache queries, paginate large tables, defer noncritical scripts, optimize for variable Philippine internet conditions.

### Phase 5: Documentation & Delivery
1. Technical docs: architecture diagrams (Mermaid), module overview, data model map, RBAC, payment flow, notification events.
2. API references for AJAX endpoints (stats, uploads, payments).
3. Deployment guide: .env templates, queues, schedule, storage, SSL, webhooks.
4. Role-based user manuals and admin training materials.
5. Packaging: installation script, migrations, sample data seeders, configuration templates for small/medium/large schools.

## Detailed Implementation Plan
### 1) Data Model Harmonization
- Option A (preferred): update models to match current uppercase DB columns with `$primaryKey` and correct relation keys; add accessors/mutators if needed.
- Option B: write migrations to rename columns to standard snake_case and update all relations accordingly; run `migrate:fresh` in dev.
- Update all belongsTo/hasMany/belongsToMany to explicit foreign/owner keys.

### 2) Authentication & RBAC
- Install Breeze for auth views & controllers; customize layouts to `layouts.master`.
- Introduce `roles` pivot as canonical source of truth; drop `accounts.role` enum usage from code where possible.
- Middleware: `role:admin|faculty|student|parent`; policies for module actions.
- Seeders: Admin, Faculty, Student roles + sample accounts.

### 3) Enrollment Workflow
- Wizard routes: `/enrollment/apply/{step}`; controller storing progress in DB.
- Requirements: model `requirements` with status (submitted/verified/rejected) per student; file uploads to `storage/app/public/requirements`.
- Payments: integrate PayMongo (GCash/Maya); create `payments` table, handle intents, webhooks, store receipts.
- Notifications: mailables + queued jobs; success/failure messages; dashboard alerts.

### 4) View Pages (Generate Missing)
- `resources/views/auth`: login, register, forgot/reset.
- `resources/views/admin`: dashboard, student manager, schedule planner, reports.
- `resources/views/faculty`: rosters, grades.
- `resources/views/parents`: overview, progress, billing.
- `resources/views/students`: enrollment wizard, status, schedule.
- Shared partials: nav, breadcrumbs, modals, toasts.

### 5) UX Enhancements
- Bootstrap 5 components: forms, cards, navs; bootstrap-icons.
- Client-side validation: HTML5 + lightweight JS; server validation with FormRequest.
- Data tables: simple sortable/filterable tables (vanilla JS) or integrate lightweight DataTables.
- Transitions: CSS transitions on route changes; toasts for feedback.
- Accessibility: proper roles/labels, keyboard navigation, color contrast checks.

### 6) Testing & Perf
- PHPUnit tests: models (relations), controllers (CRUD, wizard), policies, payment webhooks, uploads.
- Factories & seeders for realistic scenarios.
- Perf: query optimization, indexes, N+1 audits, pagination defaults; asset minification with Vite; CDN fallbacks.

### 7) Docs & Packaging
- `docs/` directory: architecture, data model, RBAC, API, deployment.
- Installation script: composer, .env, key:generate, migrate, seed, npm build.
- Sample data: small/medium/large seeders; config templates.

## Deliverables
- Implemented views/layouts with Bootstrap 5 and branding.
- Unified RBAC and enrollment workflow with payments & notifications.
- Test suite with CI-ready commands.
- Documentation set and installation package.

## Next Step
- Confirm whether we should harmonize by renaming DB columns (Option B) or adjust models to current schema (Option A). Once confirmed, I will begin Phase 1 and proceed iteratively. 