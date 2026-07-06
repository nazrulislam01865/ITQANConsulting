# Home Works Feature Update

## What changed

- Home page Works Preview no longer has separate work-card content.
- Home page Works Preview now reads from the Works Page work-card records.
- Each Works Page work card has a `Feature this work on Home page` checkbox.
- The admin sidebar now stores and restores its scroll position after navigation.

## How to use

1. Go to Admin → Works Page → Work Listing Section.
2. Edit a Work card.
3. Enable `Feature this work on Home page`.
4. Save.
5. The selected work appears in the Home page Works Preview section.

If no work has ever been configured with the feature setting, the frontend safely falls back to the first four works so the section does not become empty after upgrade.
