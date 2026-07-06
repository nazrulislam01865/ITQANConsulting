# Font and size matching fix

The original prototype stores all typography, spacing, responsive font clamps, and visual rules inside its inline `<style>` block.

To keep the Laravel frontend visually identical, the exact style block from the template is now preserved in:

```txt
public/assets/css/itqan-template.css
resources/css/app.css
```

The layout loads the static CSS file directly before the Vite JavaScript file. This prevents Vite/Tailwind/CSS optimization from rewriting media queries, merging rules, or changing how the original font-size clamps are delivered.

The Vite config now builds JavaScript only. Backend/admin work can still make all content dynamic later without touching the visual CSS.
