## Overview
- Introduce LRN-based student portal accounts (read-only), allowing students to register using their 12-digit LRN and set a password.
- Seed Grades and default Sections (A–C) for Grades 7–10; allow admins to manage sections and add more.
- Make Academic Years, Subjects, Courses, Teachers pages fully usable (CRUD), consistent with the master layout.
- Fix overflow on Sections index.
- Add production-level enhancements suitable for a school demo.

## Data Model Changes
1. Students
- Add `lrn` (char/varchar 12, unique, indexed) to `students`.
- Enforce LRN format in validation: 12-digit numeric.
2. Accounts & Roles
- Keep `accounts` for auth; link student portal accounts to the student via `account_id` once they register.
- Use `roles`/`account_roles` pivot; add roles: `student_portal_readonly`, `admin`, `faculty`.
- (Optional) add `status` on students (Active/Inactive) for visibility control.
3. Sections/Grades
- Ensure `sections` have `grade_level`, `section_name`, `capacity`; seed defaults for Grades 7–10 with sections A–C.

## LRN-Based Registration Flow
1. Routes
- `GET /portal/register-lrn` → show registration form (LRN + password).
- `POST /portal/register-lrn` → validate LRN, ensure student exists, create `Account` with read-only role, link to student.
- `GET /portal/login` → login page (reuse `auth.login`).
2. Controller
- `StudentPortalController`: `showRegisterForm`, `register`, `login` (use Laravel auth), `logout`.
3. Validation
- LRN: `required|digits:12|exists:students,lrn`.
- Password: strong (min length, etc.).
4. Policies/Middleware
- Middleware `role:student_portal_readonly` to restrict to view-only endpoints.
- Policies: Student can only view their own record; deny update/delete.

## Grades & Sections Seeding
1. Seeder
- Seed Grades 7–10; for each grade, create Sections A, B, C (capacity default, e.g., 40).
2. Admin Management
- Admin pages to list/add/edit/remove sections; grade-level dropdown; validation on capacity.

## CRUD Pages (Usable)
1. Academic Years
- Index, Create, Edit, Show; filters for active year; toggle active.
2. Subjects
- Index, Create, Edit, Show; search and pagination.
3. Courses
- Index, Create, Edit, Show; link subject/teacher/academic year; capacity.
4. Teachers
- Index, Create, Edit, Show; department, contact.
- All pages use master layout, Bootstrap 5, breadcrumbs, toasts, responsive tables.

## Sections Page Overflow Fix
- Wrap tables in `.table-responsive`.
- Ensure grid/cards don’t exceed container width; set `overflow-x: auto` where necessary.
- Clamp progress bar widths (already done) and remove inline width expressions.

## RBAC Implementation
- Middleware `role` with route groups for Admin/Faculty/StudentPortal.
- Policies: Students (view-own only), Faculty (view/manage rosters and grades), Admin (manage all entities).

## Production-Level Enhancements (Demo)
1. Performance & Stability
- Query optimization, eager loading, pagination defaults.
- Caching for lists; `.env` toggles for SQLite/MySQL.
2. Security
- Password hashing; rate-limited login; CSRF; validation everywhere.
- Audit logs (model events) for admin changes.
3. Files & Payments (Demo-ready)
- Document uploads with storage and validation; simple verification status.
- Payment integration stub (e.g., PayMongo) with mock/sandbox credentials and webhook endpoints.
4. Notifications
- Email confirmations for registration/enrollment steps; queued jobs.
5. Documentation & Sample Data
- README sections per module; seed sample students per grade.

## Implementation Steps
1. Migrations: add `lrn` to students; indexes; (optional) status.
2. Seeders: grades 7–10 with sections A–C; sample students.
3. Controllers & Routes: `StudentPortalController`; route groups with role middleware; CRUD controllers confirm validations.
4. Views: add portal registration page; polish CRUD pages with filters/search.
5. Policies/Middleware: implement role checks and student ownership.
6. QA: add feature tests for LRN registration, sections CRUD, viewing authorization.
7. Docs: update README and usage guide.

## Request for Confirmation
- Proceed with Option A: adapt models to current schema (uppercase DB columns) while adding `lrn`, or Option B: migrate to snake_case columns for consistency. If not specified, I’ll proceed with Option A to minimize migrations.
- I will start with LRN registration flow, sections seeding, and CRUD page usability improvements, then layer RBAC and tests.