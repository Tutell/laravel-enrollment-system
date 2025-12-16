<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['subject', 'teacher', 'academicYear'])->paginate(20);

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        $subjects = \App\Models\Subject::orderBy('name')->get();
        $teachers = \App\Models\Teacher::orderBy('last_name')->get();
        $years = \App\Models\AcademicYear::orderBy('school_year', 'desc')->get();

        return view('courses.create', compact('subjects', 'teachers', 'years'));
    }

    public function store(StoreCourseRequest $request)
    {
        $data = $request->validated();
        if (isset($data['subject_id'])) {
            $data['subject_ID'] = $data['subject_id'];
            unset($data['subject_id']);
        }
        if (isset($data['teacher_id'])) {
            $data['teacher_ID'] = $data['teacher_id'];
            unset($data['teacher_id']);
        }
        if (isset($data['academic_year_id'])) {
            $data['academic_year_ID'] = $data['academic_year_id'];
            unset($data['academic_year_id']);
        }

        $course = Course::create($data);

        return redirect()->route('courses.show', $course)->with('success', 'Course created');
    }

    public function show(Course $course)
    {
        $course->load(['subject', 'teacher', 'academicYear', 'enrollments.student']);

        return view('courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $subjects = \App\Models\Subject::orderBy('name')->get();
        $teachers = \App\Models\Teacher::orderBy('last_name')->get();
        $years = \App\Models\AcademicYear::orderBy('school_year', 'desc')->get();

        return view('courses.edit', compact('course', 'subjects', 'teachers', 'years'));
    }

    public function update(StoreCourseRequest $request, Course $course)
    {
        $data = $request->validated();
        if (isset($data['subject_id'])) {
            $data['subject_ID'] = $data['subject_id'];
            unset($data['subject_id']);
        }
        if (isset($data['teacher_id'])) {
            $data['teacher_ID'] = $data['teacher_id'];
            unset($data['teacher_id']);
        }
        if (isset($data['academic_year_id'])) {
            $data['academic_year_ID'] = $data['academic_year_id'];
            unset($data['academic_year_id']);
        }

        $course->update($data);

        return redirect()->route('courses.show', $course)->with('success', 'Course updated');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Course deleted');
    }
}
