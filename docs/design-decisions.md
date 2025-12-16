# UI Redesign – Design Decisions

## Visual Design
- Color scheme uses `--primary #1A73E8`, `--accent #4DB5FF`, and neutral surfaces for contrast. Meets WCAG AA contrast for text on surfaces.
- Typography set to Inter with clear hierarchy (h1–h6, card titles, small labels). Line height and spacing tuned for readability.
- Spacing system adopts consistent paddings/margins and radii (`--radius-sm/md/lg`).
- Subtle motion uses scale/translate and fade. Respects `prefers-reduced-motion`.

## Layout & Navigation
- Fixed top bar and collapsible sidebar provide persistent navigation without overwhelming content.
- Sidebar supports nested sections (e.g., Students) and maintains active states.
- Content wrapper uses card-like surface with shadow and border to separate context.
- Mobile layout adapts paddings and collapses sidebar; overlay on mobile ensures focus.

## Usability
- Inline validation uses Bootstrap validation states; first invalid field receives focus.
- Loading states added to calendar and stats; alerts auto-dismiss to reduce noise.
- Buttons and interactive elements have consistent hover/focus affordances.

## Performance
- Resource hints include `preconnect` for fonts; lazy loading for images via IntersectionObserver.
- CDN assets defer heavy JS; non-critical animations minimized with reduced-motion.
- Client-side caching for calendar details on date drill-down.

## Accessibility
- Skip link to main content and `role="main"` improve keyboard navigation.
- Sidebar uses `role="navigation"` and ARIA labels; toggle maintains `aria-expanded`.
- Color variables ensure sufficient contrast in light/dark; badges and icons maintain text alternatives.

## Risks & Trade-offs
- Over-reliance on CDN assets may impact offline dev; Vite manifest supported when available.
- Minimal JavaScript added to layout to avoid render-blocking; deeper form-specific patterns deferred to pages.

