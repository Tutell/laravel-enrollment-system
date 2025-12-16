<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherAccessLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'desc') === 'asc' ? 'asc' : 'desc';
        $logs = TeacherAccessLog::query()
            ->with(['account', 'teacher'])
            ->orderBy('created_at', $sort)
            ->paginate(20)
            ->appends(['sort' => $sort]);

        return view('admin.logs', compact('logs', 'sort'));
    }
}
