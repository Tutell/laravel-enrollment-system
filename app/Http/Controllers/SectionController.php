<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSectionRequest;
use App\Models\Section;
use App\Models\Teacher;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::with(['teacher', 'students'])->paginate(10);

        // Add statistics
        $totalSections = Section::count();
        $assignedTeachers = Section::whereHas('teacher')->count();
        $totalStudents = \App\Models\Student::count(); // Or sum of students in all sections

        // Get current academic year
        $currentYear = date('Y');
        $activeYear = "{$currentYear}-".($currentYear + 1);

        $gradeSections = Section::whereIn('grade_level', [7, 8, 9, 10])
            ->with('teacher', 'students')
            ->orderBy('grade_level')
            ->orderBy('section_name')
            ->get()
            ->groupBy('grade_level');

        return view('sections.index', compact(
            'sections',
            'totalSections',
            'assignedTeachers',
            'totalStudents',
            'activeYear',
            'gradeSections'
        ));
    }

    public function create()
    {
        return view('sections.create');
    }

    public function store(StoreSectionRequest $request)
    {
        $data = $request->validated();
        $section = Section::create($data);

        return redirect()->route('sections.show', $section)->with('success', 'Section created');
    }

    public function show(Section $section)
    {
        $section->load(['teacher', 'teachers', 'students']);

        $allTeachers = Teacher::orderBy('last_name')->get();

        return view('sections.show', compact('section', 'allTeachers'));
    }

    public function assignTeacher(Section $section)
    {
        $data = request()->validate([
            'teacher_id' => ['required', 'exists:teachers,teacher_ID'],
        ]);

        $teacherId = $data['teacher_id'];

        if (! $section->teachers()->where('teachers.teacher_ID', $teacherId)->exists()) {
            $section->teachers()->attach($teacherId);
        }
        if (! $section->teacher_ID) {
            $section->teacher_ID = $teacherId;
            $section->save();
        }

        return redirect()->route('sections.show', $section)->with('success', 'Teacher assigned to section.');
    }

    public function removeTeacher(Section $section, Teacher $teacher)
    {
        $section->teachers()->detach($teacher->teacher_ID);
        if ($section->teacher_ID === $teacher->teacher_ID) {
            $replacement = $section->teachers()->first();
            $section->teacher_ID = optional($replacement)->teacher_ID;
            $section->save();
        }

        return redirect()->route('sections.show', $section)->with('success', 'Teacher removed from section.');
    }

    public function edit(Section $section)
    {
        return view('sections.edit', compact('section'));
    }

    public function update(StoreSectionRequest $request, Section $section)
    {
        $data = $request->validated();
        $section->update($data);

        return redirect()->route('sections.show', $section)->with('success', 'Section updated');
    }

    public function destroy(Section $section)
    {
        $section->delete();

        return redirect()->route('sections.index')->with('success', 'Section deleted');
    }
}
