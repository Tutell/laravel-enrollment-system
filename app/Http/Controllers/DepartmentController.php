<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Cache::remember('departments:list', 600, function () {
            return Department::orderBy('name')->get();
        });

        return view('departments.index', compact('departments'));
    }

    public function show(Department $department, Request $request)
    {
        $sort = $request->query('sort', 'last_name');
        $dir = $request->query('dir', 'asc');
        $q = $request->query('q');
        $validSorts = ['last_name', 'first_name', 'contact_number'];
        $sort = in_array($sort, $validSorts) ? $sort : 'last_name';
        $dir = $dir === 'desc' ? 'desc' : 'asc';

        $query = Teacher::with(['account', 'department'])
            ->where('department_ID', $department->getKey());

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('first_name', 'like', '%'.$q.'%')
                    ->orWhere('last_name', 'like', '%'.$q.'%')
                    ->orWhere('contact_number', 'like', '%'.$q.'%');
            });
        }

        $teachers = $query->orderBy($sort, $dir)->paginate(50)->withQueryString();

        return view('departments.show', compact('department', 'teachers', 'sort', 'dir', 'q'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100', 'unique:departments,name'],
        ]);
        $base = Str::slug($data['name']);
        $slug = $base;
        $i = 1;
        while (Department::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }
        $dept = Department::create([
            'name' => $data['name'],
            'slug' => $slug,
        ]);
        Cache::forget('departments:list');

        return redirect()->route('departments.show', $dept)->with('success', 'Department created');
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100', 'unique:departments,name,'.$department->department_ID.',department_ID'],
        ]);
        $base = Str::slug($data['name']);
        $slug = $base;
        $i = 1;
        while (Department::where('slug', $slug)->where('department_ID', '!=', $department->department_ID)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }
        $department->update([
            'name' => $data['name'],
            'slug' => $slug,
        ]);
        Cache::forget('departments:list');

        return redirect()->route('departments.show', $department)->with('success', 'Department updated');
    }

    public function destroy(Department $department)
    {
        $hasTeachers = Teacher::where('department_ID', $department->department_ID)->exists();
        if ($hasTeachers) {
            return back()->withErrors(['department' => 'Cannot delete: teachers are assigned to this department.']);
        }
        $department->delete();
        Cache::forget('departments:list');

        return redirect()->route('departments.index')->with('success', 'Department deleted');
    }

    public function bulkAssignForm()
    {
        $departments = Department::orderBy('name')->get();
        $teachers = Teacher::with('account')->orderBy('last_name')->paginate(50);

        return view('departments.bulk_assign', compact('departments', 'teachers'));
    }

    public function bulkAssign(Request $request)
    {
        $data = $request->validate([
            'department_id' => ['required', 'exists:departments,department_ID'],
            'teacher_ids' => ['required', 'array'],
            'teacher_ids.*' => ['integer', 'exists:teachers,teacher_ID'],
        ]);
        Teacher::whereIn('teacher_ID', $data['teacher_ids'])
            ->update(['department_ID' => $data['department_id']]);
        Cache::forget('departments:list');

        return redirect()->route('departments.index')->with('success', 'Teachers assigned to department');
    }

    public function importForm()
    {
        $departments = Department::orderBy('name')->get();

        return view('departments.import', compact('departments'));
    }

    public function import(Request $request)
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
            'default_department_id' => ['nullable', 'exists:departments,department_ID'],
        ]);
        $deptByName = Department::all()->mapWithKeys(function ($d) {
            return [strtolower($d->name) => $d->department_ID];
        })->toArray();
        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $colmap = [];
        foreach ($header as $i => $col) {
            $colmap[strtolower(trim($col))] = $i;
        }
        $updated = 0;
        $failed = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $username = $colmap['username'] ?? null;
            $email = $colmap['email'] ?? null;
            $depcol = $colmap['department'] ?? null;
            $account = null;
            if ($email !== null) {
                $account = \App\Models\Account::where('Email', $row[$email] ?? null)->first();
            }
            if (! $account && $username !== null) {
                $account = \App\Models\Account::where('Username', $row[$username] ?? null)->first();
            }
            if (! $account) {
                $failed++;

                continue;
            }
            $teacher = Teacher::where('account_ID', $account->account_ID)->first();
            if (! $teacher) {
                $failed++;

                continue;
            }
            $deptId = $request->input('default_department_id');
            if ($depcol !== null) {
                $name = strtolower(trim((string) ($row[$depcol] ?? '')));
                if ($name && isset($deptByName[$name])) {
                    $deptId = $deptByName[$name];
                }
            }
            if ($deptId) {
                $teacher->department_ID = $deptId;
                $teacher->save();
                $updated++;
            } else {
                $failed++;
            }
        }
        fclose($handle);
        Cache::forget('departments:list');

        return redirect()->route('departments.index')->with('success', "Imported: {$updated}, Failed: {$failed}");
    }

    public function repair()
    {
        $map = Department::all()->mapWithKeys(function ($d) {
            return [strtolower($d->name) => $d->department_ID];
        });
        $updated = 0;
        Teacher::whereNull('department_ID')->chunk(200, function ($teachers) use ($map, &$updated) {
            foreach ($teachers as $t) {
                $name = strtolower(trim((string) $t->department));
                if ($name && isset($map[$name])) {
                    $t->department_ID = $map[$name];
                    $t->save();
                    $updated++;
                }
            }
        });
        Cache::forget('departments:list');

        return redirect()->route('departments.index')->with('success', "Repaired {$updated} teacher assignments.");
    }
}
