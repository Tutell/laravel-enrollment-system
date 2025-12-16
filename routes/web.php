<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\LogController as AdminLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\YearLevelController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Simple auth routes if you don't have full auth scaffolding
// Route::get('login', ...); // integrate with your preferred auth

Route::middleware(['web'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    })->name('home');
    
    // Debug route to check current user
    Route::get('/debug-user', function () {
        $user = Auth::user();
        if (!$user) {
            return 'Not logged in';
        }
        return [
            'id' => $user->getAuthIdentifier(),
            'username' => $user->Username ?? $user->username ?? 'N/A',
            'email' => $user->Email ?? $user->email ?? 'N/A', 
            'role' => $user->role ?? 'N/A',
            'all_attributes' => $user->getAttributes()
        ];
    })->middleware('auth');
    
    // Quick fix to make current user admin
    Route::get('/make-admin', function () {
        $user = Auth::user();
        if (!$user) {
            return 'Not logged in';
        }
        $user->role = 'admin';
        $user->save();
        return 'User role changed to admin. You can now access the teachers page.';
    })->middleware('auth');
    // Basic auth pages
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    // Public registration disabled; moved to admin-only
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Optional online payments (PayMongo sandbox)
    Route::get('/payments', [PaymentController::class, 'choose'])->name('payments.choose');
    Route::post('/payments/intent', [PaymentController::class, 'createIntent'])->name('payments.intent');
    Route::post('/payments/webhook', [PaymentController::class, 'webhook'])->name('payments.webhook');
    Route::get('/portal/register-lrn', [StudentPortalController::class, 'showRegisterForm'])->name('portal.register-lrn');
    Route::post('/portal/register-lrn', [StudentPortalController::class, 'register'])->name('portal.register-lrn.post');
    // Student statistics API endpoint
    Route::get('/students/stats', [StudentController::class, 'stats'])
        ->name('students.stats');
    Route::get('/students/export', [StudentController::class, 'export'])
        ->name('students.export')->middleware(['auth', 'role:admin,teacher']);
    // Student enrollment by grade level
    Route::get('/students/enrollment', [StudentController::class, 'enrollment'])
        ->name('students.enrollment');
    Route::get('/students/manage', [StudentController::class, 'manageByGrade'])
        ->name('students.manage')->middleware(['auth']);
    Route::get('/students/archive', [StudentController::class, 'archiveIndex'])
        ->name('students.archive')->middleware(['auth', 'role:admin,teacher']);
    // Get sections by grade level (AJAX)
    Route::get('/students/grade/{gradeLevel}/sections', [StudentController::class, 'getSectionsByGrade'])
        ->name('students.sections-by-grade');
    Route::resource('students', StudentController::class)->middleware(['auth', 'role:admin,teacher']);
    Route::post('students/{student}/approve', [StudentController::class, 'approve'])->name('students.approve');
    Route::post('students/{student}/archive', [StudentController::class, 'archive'])->name('students.archive.post')->middleware(['auth', 'role:admin,teacher']);
    Route::post('students/{student}/restore', [StudentController::class, 'restore'])->name('students.restore')->middleware(['auth', 'role:admin']);
    Route::post('students/{student}/re-enroll', [StudentController::class, 'reEnroll'])->name('students.re-enroll')->middleware(['auth', 'role:admin']);
    Route::post('students/{student}/permanent-delete', [StudentController::class, 'permanentDelete'])->name('students.permanent-delete')->middleware(['auth', 'role:admin']);
    Route::post('students/archive/bulk-delete', [StudentController::class, 'bulkPermanentDelete'])->name('students.archive.bulk-delete')->middleware(['auth', 'role:admin']);
    Route::get('students/{student}/guardians/create', [GuardianController::class, 'create'])->name('guardians.create');
    Route::post('students/{student}/guardians', [GuardianController::class, 'store'])->name('guardians.store');
    Route::resource('teachers', TeacherController::class)->middleware(['auth', 'role:admin']);
    Route::put('teachers/{teacher}/department', [TeacherController::class, 'setDepartment'])->name('teachers.department')->middleware(['auth', 'role:admin']);
    Route::put('teachers/{teacher}/status', [TeacherController::class, 'setStatus'])->name('teachers.status')->middleware(['auth', 'role:admin']);
    Route::get('/subjects/assign-teachers', [SubjectController::class, 'assignTeachersForm'])->name('subjects.assign-teachers')->middleware(['auth', 'role:admin']);
    Route::post('/subjects/assign-teachers', [SubjectController::class, 'assignTeachers'])->name('subjects.assign-teachers.post')->middleware(['auth', 'role:admin']);
    Route::pattern('subject', '[0-9]+');
    Route::resource('subjects', SubjectController::class)->middleware(['auth', 'role:admin']);
    Route::get('/subjects/grade/{gradeLevel}', [SubjectController::class, 'byGrade'])->name('subjects.by-grade')->middleware(['auth', 'role:admin,teacher']);
    Route::resource('departments', DepartmentController::class)->only(['index', 'show'])->middleware(['auth', 'role:admin,teacher']);
    // Courses deprecated: routes removed; use Year Levels instead
    Route::resource('sections', SectionController::class)->middleware(['auth', 'role:admin,teacher']);
    Route::post('sections/{section}/teachers', [SectionController::class, 'assignTeacher'])->name('sections.teachers.assign');
    Route::delete('sections/{section}/teachers/{teacher}', [SectionController::class, 'removeTeacher'])->name('sections.teachers.remove');
    Route::resource('academic-years', AcademicYearController::class)->middleware(['auth']);
    Route::resource('year-levels', YearLevelController::class)->only(['index', 'show'])->middleware(['auth', 'role:admin,teacher']);
    Route::post('year-levels/{year_level}/request', [YearLevelController::class, 'requestAssignment'])->name('year-levels.request')->middleware(['auth', 'role:teacher']);
    Route::put('year-levels/assignments/{assignment}/approve', [YearLevelController::class, 'approve'])->name('year-levels.approve')->middleware(['auth', 'role:admin']);
    Route::put('year-levels/assignments/{assignment}/reject', [YearLevelController::class, 'reject'])->name('year-levels.reject')->middleware(['auth', 'role:admin']);

    // Account creation moved to admin-only; keep old route removed from public

    // Admin account management
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('accounts', [AccountController::class, 'index'])->name('accounts.index');
        Route::get('accounts/{account}/edit', [AccountController::class, 'edit'])->name('accounts.edit');
        Route::put('accounts/{account}', [AccountController::class, 'update'])->name('accounts.update');
        Route::delete('accounts/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy');
        Route::get('logs', [AdminLogController::class, 'index'])->name('logs.index');
        Route::get('departments/bulk-assign', [DepartmentController::class, 'bulkAssignForm'])->name('departments.bulk');
        Route::post('departments/bulk-assign', [DepartmentController::class, 'bulkAssign'])->name('departments.bulk.post');
        Route::get('departments/import', [DepartmentController::class, 'importForm'])->name('departments.import');
        Route::post('departments/import', [DepartmentController::class, 'import'])->name('departments.import.post');
        Route::post('departments/repair', [DepartmentController::class, 'repair'])->name('departments.repair');
        Route::get('branding', [\App\Http\Controllers\Admin\BrandingController::class, 'show'])->name('branding.show');
        Route::post('branding', [\App\Http\Controllers\Admin\BrandingController::class, 'update'])->name('branding.update');
    });

    // enrollment and grades
    Route::resource('enrollment', EnrollmentController::class)->only(['index', 'store', 'update', 'destroy'])->middleware(['auth', 'role:admin']);
    Route::post('enrollment/{enrollment}/drop', [EnrollmentController::class, 'drop'])->name('enrollment.drop')->middleware(['auth', 'role:admin,teacher']);
    Route::post('enrollment/{enrollment}/unenroll', [EnrollmentController::class, 'unenroll'])->name('enrollment.unenroll')->middleware(['auth', 'role:admin']);
    Route::get('/grades/manage', [GradeController::class, 'manage'])->name('grades.manage')->middleware(['auth']);
    Route::post('/grades/bulk', [GradeController::class, 'bulk'])->name('grades.bulk')->middleware(['auth', 'role:admin,teacher']);
    Route::resource('grades', GradeController::class)->only(['index', 'store', 'update', 'destroy'])->middleware(['auth', 'role:admin,teacher']);
    Route::post('students/{student}/auto-enroll', [EnrollmentController::class, 'autoEnrollByGrade'])->name('students.auto-enroll')->middleware(['auth', 'role:admin,teacher']);
    Route::post('students/{student}/drop-all', [EnrollmentController::class, 'dropAllForStudent'])->name('students.drop-all')->middleware(['auth', 'role:admin,teacher']);
    Route::get('students/{student}/subjects', [EnrollmentController::class, 'manualSubjects'])->name('students.subjects')->middleware(['auth', 'role:admin']);
    Route::post('students/{student}/subjects', [EnrollmentController::class, 'assignSubjects'])->name('students.subjects.assign')->middleware(['auth', 'role:admin']);
    Route::post('students/{student}/subjects/teacher', [EnrollmentController::class, 'assignTeacher'])->name('students.subjects.assign-teacher')->middleware(['auth', 'role:admin']);
    Route::get('students/{student}/enrolled-subjects', [StudentController::class, 'enrolledSubjects'])->name('students.enrolled-subjects')->middleware(['auth', 'role:admin,teacher']);
    Route::get('students/{student}/grades', [StudentController::class, 'gradesReport'])->name('students.grades')->middleware(['auth', 'role:admin,teacher']);
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])
        ->name('student.dashboard')->middleware(['auth', 'role:student']);
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/settings', [SettingsController::class, 'show'])->name('settings');
    Route::get('/dashboard/enrollment-trend', [DashboardController::class, 'trend'])->name('dashboard.trend');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
});

// Admin account management and creation
Route::middleware(['web', 'auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('accounts/create', [AccountController::class, 'create'])->name('accounts.create');
    Route::post('accounts', [AccountController::class, 'store'])->name('accounts.store');
    Route::get('accounts/{account}/edit', [AccountController::class, 'edit'])->name('accounts.edit');
    Route::put('accounts/{account}', [AccountController::class, 'update'])->name('accounts.update');
    Route::delete('accounts/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy');
    Route::get('departments/create', [DepartmentController::class, 'create'])->name('departments.create');
    Route::post('departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::get('departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
    Route::put('departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
});
