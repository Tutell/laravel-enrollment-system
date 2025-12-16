## Root Cause
- The layout uses `@vite(['resources/css/app.css','resources/js/app.js'])` but the Vite build artifacts are missing, so Laravel cannot find `public/build/manifest.json`.

## Fix Options (Choose One or Combine)
### Option A: Enable Vite properly (recommended for local dev)
1. Verify `package.json` and `vite.config.js` exist; add minimal `vite.config.js` if missing.
2. Install dependencies and run Vite:
   - `npm install`
   - Dev: `npm run dev` (hot reload, no manifest error while dev server runs)
   - Prod build: `npm run build` (generates `public/build/manifest.json`)
3. Keep `@vite(...)` in `layouts.master`.

### Option B: Add safe CDN fallback (works even without Node)
1. Update `layouts.master` to conditionally include Vite assets only if `public/build/manifest.json` exists; otherwise load Bootstrap + minimal CSS via CDN.
   - Blade check:
     - `@php $hasManifest = file_exists(public_path('build/manifest.json')); @endphp`
     - `@if($hasManifest) @vite([...]) @else <link rel="stylesheet" href="...bootstrap CDN..."> <script src="...bootstrap bundle..."></script> @endif`
2. Keep Bootstrap CDN as base styling so pages render even without Vite.

### Option C: Environment toggle for assets
1. Introduce `.env` flag like `ASSET_PIPELINE=vite|cdn`.
2. In `layouts.master`, branch on `config('app.asset_pipeline')` using `env('ASSET_PIPELINE','vite')`.

## Proposed Implementation (fast and robust)
1. Apply Option B (conditional fallback) in `layouts.master` to immediately stop the error without requiring build.
2. Add Option A setup so developers can run `npm run dev/build` when they want hot reload and optimized assets.
3. Document commands for dev/prod and add a short troubleshooting note.

## Steps I Will Execute
1. Update `layouts/master.blade.php` to detect `manifest.json` and fallback to Bootstrap CDN when missing.
2. Create minimal `vite.config.js` and ensure `resources/js/app.js` is present if needed.
3. Provide a README section describing:
   - Dev: `npm install && npm run dev`
   - Prod: `npm run build` (and how to serve built assets)
4. Optional: add `.env` toggle (Option C) after confirming you want this switch.

## Notes
- This preserves your current Bootstrap-based UI.
- No functional changes beyond asset loading; performance improves when Vite is used, but pages work without it.
- After this change, you can choose to always run with CDN on production if preferred, or use Vite builds for optimized bundles.