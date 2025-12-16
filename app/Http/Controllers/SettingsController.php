<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        return view('settings.show', compact('user'));
    }
}
