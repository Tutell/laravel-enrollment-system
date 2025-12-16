<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BrandingSetting extends Model
{
    use HasFactory;

    protected $table = 'branding_settings';

    protected $primaryKey = 'branding_ID';

    protected $fillable = [
        'system_name',
        'welcome_message',
        'subtext',
        'school_name',
        'mission',
        'vision',
        'core_values',
        'logo_path',
    ];

    public static function cached(): self
    {
        $value = Cache::remember('branding.settings', 600, function () {
            $existing = static::query()->first();
            if ($existing) {
                return $existing;
            }
            return static::create([
                'system_name' => config('app.name', 'Laravel'),
                'welcome_message' => 'Welcome To Laravel',
                'subtext' => null,
                'school_name' => null,
                'mission' => null,
                'vision' => null,
                'core_values' => null,
                'logo_path' => null,
            ]);
        });
        return $value;
    }
}

