<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BrandingAudit;
use App\Models\BrandingSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class BrandingController extends Controller
{
    public function show()
    {
        $branding = BrandingSetting::cached();
        return view('admin.branding', compact('branding'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'system_name' => ['required', 'string', 'min:2', 'max:100'],
            'welcome_message' => ['required', 'string', 'min:2', 'max:200'],
            'subtext' => ['nullable', 'string', 'max:255'],
            'school_name' => ['nullable', 'string', 'max:255'],
            'mission' => ['nullable', 'string'],
            'vision' => ['nullable', 'string'],
            'core_values' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ]);

        $branding = BrandingSetting::cached();

        $changes = [];
        foreach (['system_name','welcome_message','subtext','school_name','mission','vision','core_values'] as $key) {
            $new = $data[$key] ?? null;
            if ($branding->{$key} !== $new) {
                $changes[$key] = ['old' => $branding->{$key}, 'new' => $new];
                $branding->{$key} = $new;
            }
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('public/branding');
            $public = Storage::url($path);
            if ($branding->logo_path !== $public) {
                $changes['logo_path'] = ['old' => $branding->logo_path, 'new' => $public];
                $branding->logo_path = $public;
            }
        }

        $branding->save();
        Cache::forget('branding.settings');
        BrandingSetting::cached();

        BrandingAudit::create([
            'branding_ID' => $branding->getKey(),
            'actor_account_ID' => optional(Auth::user())->getAuthIdentifier(),
            'action' => 'updated',
            'changes' => json_encode($changes),
        ]);

        return redirect()->route('admin.branding.show')->with('success', 'Branding updated');
    }
}

