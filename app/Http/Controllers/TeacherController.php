<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Models\Department;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $grade = $request->query('grade_level');
        $subjectId = $request->query('subject_id');
        $sort = $request->query('sort');
        $dir = strtolower($request->query('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $query = Teacher::with(['courses.subject', 'sections', 'department', 'account']);

        if ($status) {
            $query->whereHas('account', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }
        if ($subjectId) {
            $query->whereHas('courses', function ($q) use ($subjectId) {
                $q->where('subject_ID', $subjectId)->orWhere('subject_id', $subjectId);
            });
        }
        if ($grade) {
            $teacherIds = \App\Models\YearLevelAssignment::where('status', 'approved')
                ->whereHas('yearLevel', function ($ylq) use ($grade) {
                    $ylq->where('grade_level', $grade);
                })
                ->pluck('teacher_ID')
                ->unique()
                ->all();
            $query->whereIn('teacher_ID', $teacherIds ?: [-1]);
        }

        if ($sort === 'status') {
            $query->leftJoin('accounts', 'teachers.account_ID', '=', 'accounts.account_ID')
                ->orderBy('accounts.status', $dir)
                ->select('teachers.*');
        } elseif ($sort === 'name') {
            $query->orderBy('last_name', $dir)->orderBy('first_name', $dir);
        } elseif ($sort === 'department') {
            $query->orderBy('department', $dir);
        }

        $teachers = $query->paginate(20)->appends($request->query());
        $departments = Department::orderBy('name')->get();
        $subjects = \App\Models\Subject::orderBy('name')->get();
        $grades = \App\Models\YearLevel::orderBy('grade_level')->pluck('grade_level')->unique()->toArray();

        return view('teachers.index', compact('teachers', 'departments', 'subjects', 'grades', 'status', 'grade', 'subjectId', 'sort', 'dir'));
    }

    public function create()
    {
        $availableAccounts = \App\Models\Account::whereDoesntHave('teacher')
            ->get()
            ->mapWithKeys(function ($account) {
                return [$account->account_id => $account->username.' (ID: '.$account->account_id.')'];
            })
            ->toArray();

        $departments = Department::orderBy('name')->get();

        return view('teachers.create', compact('availableAccounts', 'departments'));
    }

    public function store(StoreTeacherRequest $request)
    {
        $data = $request->validated();
        if (isset($data['account_id'])) {
            $data['account_ID'] = $data['account_id'];
            unset($data['account_id']);
        }
        if (isset($data['department_id'])) {
            $dept = Department::find($data['department_id']);
            if ($dept) {
                $data['department'] = $dept->name;
            }
        } elseif (isset($data['department'])) {
            $dept = Department::where('name', $data['department'])->first();
            if ($dept) {
                $data['department_id'] = $dept->department_ID;
            }
        }

        $teacher = Teacher::create($data);

        return redirect()->route('teachers.show', $teacher)->with('success', 'Teacher created');
    }

    public function show(Teacher $teacher)
    {
        $teacher->load(['courses.subject', 'courses.academicYear', 'sections.students']);

        return view('teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        $departments = Department::orderBy('name')->get();

        return view('teachers.edit', compact('teacher', 'departments'));
    }

    public function update(StoreTeacherRequest $request, Teacher $teacher)
    {
        $data = $request->validated();
        if (isset($data['account_id'])) {
            $data['account_ID'] = $data['account_id'];
            unset($data['account_id']);
        }
        if (isset($data['department_id'])) {
            $dept = Department::find($data['department_id']);
            if ($dept) {
                $data['department'] = $dept->name;
            }
        } elseif (isset($data['department'])) {
            $dept = Department::where('name', $data['department'])->first();
            if ($dept) {
                $data['department_id'] = $dept->department_ID;
            }
        }

        $teacher->update($data);

        return redirect()->route('teachers.show', $teacher)->with('success', 'Teacher updated');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();

        return redirect()->route('teachers.index')->with('success', 'Teacher deleted');
    }

    public function setDepartment(Request $request, Teacher $teacher)
    {
        $data = $request->validate([
            'department_id' => ['required', 'exists:departments,department_ID'],
        ]);
        $teacher->department_ID = $data['department_id'];
        // keep text field in sync if needed
        $dept = Department::find($data['department_id']);
        if ($dept) {
            $teacher->department = $dept->name;
        }
        $teacher->save();

        return redirect()->route('teachers.show', $teacher)->with('success', 'Department updated');
    }

    public function setStatus(Request $request, Teacher $teacher)
    {
        $account = $teacher->account;
        if (! $account) {
            return back()->withErrors(['status' => 'Teacher account not found.']);
        }
        $data = $request->validate([
            'status' => ['required', 'string', 'in:active,disabled,pending,on_leave'],
        ]);
        $old = $account->status;
        $account->update(['status' => $data['status']]);
        try {
            \App\Models\AccountAudit::create([
                'actor_account_ID' => (optional(\Illuminate\Support\Facades\Auth::user())->getAuthIdentifier()),
                'target_account_ID' => $account->account_ID,
                'action' => 'status_changed',
                'changes' => json_encode(['status' => ['old' => $old, 'new' => $data['status']]]),
            ]);
        } catch (\Throwable $e) {}
        return back()->with('success', 'Teacher status updated');
    }
}
