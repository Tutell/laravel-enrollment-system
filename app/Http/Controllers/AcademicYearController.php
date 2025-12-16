<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAcademicYearRequest;
use App\Models\AcademicYear;
use App\Models\AccountAudit;
use Illuminate\Support\Facades\Auth;

class AcademicYearController extends Controller
{
    public function index()
    {
        if (Auth::check() && strtolower(Auth::user()->role ?? '') === 'student') {
            AccountAudit::create([
                'actor_account_ID' => Auth::user()->getAuthIdentifier(),
                'target_account_ID' => null,
                'action' => 'unauthorized_access',
                'changes' => json_encode(['route' => 'academic-years.index']),
            ]);

            return redirect()->route('student.dashboard')->withErrors(['access' => 'You are not authorized to view Academic Years']);
        }
        $academicYears = AcademicYear::paginate(20);

        return view('academic_years.index', compact('academicYears'));
    }

    public function create()
    {
        if (Auth::check() && strtolower(Auth::user()->role ?? '') === 'student') {
            AccountAudit::create([
                'actor_account_ID' => Auth::user()->getAuthIdentifier(),
                'target_account_ID' => null,
                'action' => 'unauthorized_access',
                'changes' => json_encode(['route' => 'academic-years.create']),
            ]);

            return redirect()->route('student.dashboard')->withErrors(['access' => 'You are not authorized to create Academic Years']);
        }

        return view('academic_years.create');
    }

    public function store(StoreAcademicYearRequest $request)
    {
        if (Auth::check() && strtolower(Auth::user()->role ?? '') === 'student') {
            AccountAudit::create([
                'actor_account_ID' => Auth::user()->getAuthIdentifier(),
                'target_account_ID' => null,
                'action' => 'unauthorized_access',
                'changes' => json_encode(['route' => 'academic-years.store']),
            ]);

            return redirect()->route('student.dashboard')->withErrors(['access' => 'You are not authorized to modify Academic Years']);
        }
        $data = $request->validated();
        $ay = AcademicYear::create($data);

        return redirect()->route('academic-years.show', $ay)->with('success', 'Academic year created');
    }

    public function show(AcademicYear $academic_year)
    {
        if (Auth::check() && strtolower(Auth::user()->role ?? '') === 'student') {
            AccountAudit::create([
                'actor_account_ID' => Auth::user()->getAuthIdentifier(),
                'target_account_ID' => null,
                'action' => 'unauthorized_access',
                'changes' => json_encode(['route' => 'academic-years.show', 'id' => $academic_year->getKey()]),
            ]);

            return redirect()->route('student.dashboard')->withErrors(['access' => 'You are not authorized to view Academic Years']);
        }
        $yearLevels = \App\Models\YearLevel::orderBy('grade_level')->get();
        $sectionsByGrade = \App\Models\Section::select('grade_level')
            ->selectRaw('COUNT(*) as sections')
            ->groupBy('grade_level')
            ->pluck('sections', 'grade_level');
        $studentsByGrade = \App\Models\Student::join('sections', 'students.section_ID', '=', 'sections.section_ID')
            ->select('sections.grade_level')
            ->selectRaw('COUNT(*) as cnt')
            ->groupBy('sections.grade_level')
            ->pluck('cnt', 'sections.grade_level');

        return view('academic_years.show', [
            'academicYear' => $academic_year,
            'yearLevels' => $yearLevels,
            'sectionsByGrade' => $sectionsByGrade,
            'studentsByGrade' => $studentsByGrade,
        ]);
    }

    public function edit(AcademicYear $academic_year)
    {
        if (Auth::check() && strtolower(Auth::user()->role ?? '') === 'student') {
            AccountAudit::create([
                'actor_account_ID' => Auth::user()->getAuthIdentifier(),
                'target_account_ID' => null,
                'action' => 'unauthorized_access',
                'changes' => json_encode(['route' => 'academic-years.edit', 'id' => $academic_year->getKey()]),
            ]);

            return redirect()->route('student.dashboard')->withErrors(['access' => 'You are not authorized to edit Academic Years']);
        }

        return view('academic_years.edit', ['academicYear' => $academic_year]);
    }

    public function update(StoreAcademicYearRequest $request, AcademicYear $academic_year)
    {
        if (Auth::check() && strtolower(Auth::user()->role ?? '') === 'student') {
            AccountAudit::create([
                'actor_account_ID' => Auth::user()->getAuthIdentifier(),
                'target_account_ID' => null,
                'action' => 'unauthorized_access',
                'changes' => json_encode(['route' => 'academic-years.update', 'id' => $academic_year->getKey()]),
            ]);

            return redirect()->route('student.dashboard')->withErrors(['access' => 'You are not authorized to modify Academic Years']);
        }
        $data = $request->validated();
        $academic_year->update($data);

        return redirect()->route('academic-years.show', $academic_year)->with('success', 'Academic year updated');
    }

    public function destroy(AcademicYear $academic_year)
    {
        if (Auth::check() && strtolower(Auth::user()->role ?? '') === 'student') {
            AccountAudit::create([
                'actor_account_ID' => Auth::user()->getAuthIdentifier(),
                'target_account_ID' => null,
                'action' => 'unauthorized_access',
                'changes' => json_encode(['route' => 'academic-years.destroy', 'id' => $academic_year->getKey()]),
            ]);

            return redirect()->route('student.dashboard')->withErrors(['access' => 'You are not authorized to delete Academic Years']);
        }
        $academic_year->delete();

        return redirect()->route('academic-years.index')->with('success', 'Academic year deleted');
    }
}
