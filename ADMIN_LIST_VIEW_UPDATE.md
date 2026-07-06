# Admin List View Update

This update keeps the existing controller, request, service, model, and frontend logic unchanged, but improves the admin editing experience where repeated records were becoming too long.

## What changed

- Repeated item sections now show a compact list/table first.
- Existing items are edited one at a time by clicking **Edit**.
- New items are added using the **Add Item** / **Add [type]** button.
- Simple single-content sections such as Hero, Founder, Story, and CTA remain as direct forms.

## Applied where useful

- Home repeated items: Who We Are cards, problem cards, service preview cards, working steps, testimonials, works preview cards.
- About repeated items: belief cards, mission/vision cards, hero buttons.
- Services repeated items: service cards and FAQ items.
- Works repeated items: filter tabs and work cards.
- Catalog repeated items: catalog pages.
- Contact repeated items: side note steps.

## Also included from latest requested fixes

- Service card button text, route, and URL fields are available at the end of each service card form.
- Work filter value is auto-generated from the filter label and no longer shown to the admin.
- Work cards have button label, route, and URL fields.
