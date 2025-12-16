# High School Enrollment System – Assets

## Development
- Install Node dependencies:
```
npm install
```
- Start Vite dev server:
```
npm run dev
```

## Production Build
- Generate assets and manifest:
```
npm run build
```
- Laravel will load `public/build/manifest.json` automatically via `@vite(...)`.
- If the manifest is not present, the layout falls back to Bootstrap CDN so pages still render.

## Troubleshooting
- Error: `Vite manifest not found` → run `npm run build` or use the CDN fallback already included.
- Ensure `vite.config.js` and `resources/js/app.js` exist.

