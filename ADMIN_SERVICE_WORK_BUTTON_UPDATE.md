# Service and Work Admin Button Update

## Service cards
- Button text now appears after all service content fields.
- Button route and custom button URL are now editable per service card.
- The frontend service card button now uses the saved route or URL instead of always linking to Contact.

## Work filter tabs
- Filter value is no longer shown in the admin form.
- Filter value is generated automatically from the filter label.
- Example: `E-commerce` becomes `e-commerce`, `Software` becomes `software`, and `All` stays `all`.

## Work cards
- Work cards now include editable Button label, Button route, and Button URL fields.
- The frontend work card button now uses the saved button data.

## Changed files
- resources/views/admin/pages/sections/partials/item-fields.blade.php
- app/Services/Admin/PageAdminService.php
- app/Services/Frontend/ItqanFrontendContentService.php
- resources/views/frontend/pages/services.blade.php
- resources/views/frontend/partials/work-card.blade.php
- database/seeders/ItqanWebsiteSeeder.php
- config/itqan.php
