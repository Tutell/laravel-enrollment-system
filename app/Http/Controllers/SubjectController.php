<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::orderBy('grade_level')->orderBy('name')->get();

        return view('subjects.index', compact('subjects'));
    }

    public function byGrade($gradeLevel)
    {
        $subjects = Subject::where('grade_level', $gradeLevel)->orderBy('name')->get();

        return response()->json(
            $subjects->map(function ($s) {
                return ['id' => $s->getKey(), 'name' => $s->name, 'grade_level' => $s->grade_level];
            })
        );
    }

    public function assignTeachersForm(Request $request)
    {
        $gradeLevel = $request->query('grade_level');
        $subjects = Subject::with('teachers')->orderBy('name')
            ->when($gradeLevel, fn($q) => $q->where('grade_level', $gradeLevel))
            ->get();
        $grades = \App\Models\YearLevel::orderBy('grade_level')->pluck('grade_level')->unique()->toArray();
        $teachers = \App\Models\Teacher::with(['account', 'qualifiedSubjects'])->orderBy('last_name')->get();
        return view('subjects.assign_teachers', compact('subjects', 'grades', 'teachers', 'gradeLevel'));
    }

    public function assignTeachers(Request $request)
    {
        $data = $request->validate([
            'grade_level' => ['required', 'integer'],
            'subject_ids' => ['required', 'array'],
            'subject_ids.*' => ['integer', 'exists:subjects,subject_ID'],
            'teacher_ids' => ['required', 'array'],
            'teacher_ids.*' => ['integer', 'exists:teachers,teacher_ID'],
        ]);
        $gradeLevel = $data['grade_level'];
        $subjects = Subject::whereIn('subject_ID', $data['subject_ids'])
            ->where('grade_level', $gradeLevel)
            ->get();
        if ($subjects->count() !== count($data['subject_ids'])) {
            return back()->withErrors(['grade_level' => 'Selected subjects do not match the chosen grade level']);
        }
        $teachers = \App\Models\Teacher::whereIn('teacher_ID', $data['teacher_ids'])->get();
        $qualifiedMap = [];
        foreach ($teachers as $t) {
            $qualifiedMap[$t->teacher_ID] = $t->qualifiedSubjects()->pluck('subjects.subject_ID')->all();
        }
        $errors = [];
        $created = 0;
        foreach ($subjects as $subject) {
            foreach ($teachers as $teacher) {
                if (! in_array($subject->subject_ID, $qualifiedMap[$teacher->teacher_ID] ?? [])) {
                    $errors[] = $teacher->last_name.' not qualified for '.$subject->name;
                    continue;
                }
                $hasGrade = \App\Models\YearLevelAssignment::where('teacher_ID', $teacher->teacher_ID)
                    ->where('year_level_assignments.status', 'approved')
                    ->whereHas('yearLevel', function ($q) use ($gradeLevel) {
                        $q->where('grade_level', $gradeLevel);
                    })->exists();
                if (! $hasGrade) {
                    $errors[] = $teacher->last_name.' not assigned to Grade '.$gradeLevel;
                    continue;
                }
                try {
                    \Illuminate\Support\Facades\DB::table('subject_teacher')->insert([
                        'subject_ID' => $subject->subject_ID,
                        'teacher_ID' => $teacher->teacher_ID,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $created++;
                } catch (\Throwable $e) {}
            }
        }
        if ($errors) {
            return back()->with('warning', implode('; ', $errors))->with('success', $created ? $created.' assignments created' : null);
        }
        return back()->with('success', $created ? $created.' assignments created' : 'No assignments created');
    }

    public function create()
    {
        return view('subjects.create');
    }

    public function store(StoreSubjectRequest $request)
    {
        $data = $request->validated();
        $subject = Subject::create($data);

        return redirect()->route('subjects.show', $subject)->with('success', 'Subject created');
    }

    public function show(Subject $subject)
    {
        $subject->load('courses');

        return view('subjects.show', compact('subject'));
    }

    public function edit(Subject $subject)
    {
        return view('subjects.edit', compact('subject'));
    }

    public function update(StoreSubjectRequest $request, Subject $subject)
    {
        $data = $request->validated();
        $subject->update($data);

        return redirect()->route('subjects.show', $subject)->with('success', 'Subject updated');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'Subject deleted');
    }
}
