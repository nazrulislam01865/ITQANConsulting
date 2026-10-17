# StarPmAminul administrator recovery (server operator)

This project uses a **separate** Laravel session guard (`starpmaminul`) and database connection (`starpmaminul`) for `/starpmaminul/admin/login`. Do not reset users in the main ITQAN `users` table.

The supplied archive has **no production `.env`**. The included `database/starpmaminul.sqlite` holds one example/local administrator record with the email `admin@aminulislam.com`; it does **not** establish the live login email. Passwords are stored as hashes and cannot be read back.

## On the actual server (from the Laravel project root)

1. Verify you are operating on the live application and have an authorized, restorable backup of its database. Do **not** run `migrate:fresh`, drop tables, overwrite the live SQLite file from the archive, or rerun seeders to recover a password.
2. Verify the effective, **isolated** connection type (do not expose the database password or `.env`):

   ```bash
   php artisan tinker --execute='echo config("database.connections.starpmaminul.driver"), PHP_EOL;'
   ```

   The archive defaults to SQLite and optionally supports a separate MySQL connection. A PostgreSQL value is possible only if production has been configured for it; do not infer it from the main site's `DB_CONNECTION`.
3. Find the live administrator's ID and email:

   ```bash
   php artisan tinker --execute='dump(\App\Models\StarPmAminul\User::query()->get(["id", "name", "email"])->toArray());'
   ```

4. Reset the password **without placing it in a command line or interactive PHP history**. These commands use Bash. Replace `1` with the actual ID from step 3 if different:

   ```bash
   read -r -s -p 'New portfolio admin password (14+ characters): ' NEWPASS
   echo
   STARPMAMINUL_NEW_PASSWORD="$NEWPASS" php artisan tinker --execute='
   $admin = \App\Models\StarPmAminul\User::query()->findOrFail(1);
   $password = getenv("STARPMAMINUL_NEW_PASSWORD");
   if (! is_string($password) || strlen($password) < 14) {
       throw new \RuntimeException("Password must contain at least 14 characters.");
   }
   $admin->password = $password;
   $admin->save();
   echo "Password updated for {$admin->email}", PHP_EOL;
   '
   unset NEWPASS
   ```

   `App\Models\StarPmAminul\User` has a Laravel `hashed` cast on `password`, so assigning a new plain password hashes it automatically before it is stored. No application routes, database schema, or other users are changed.

5. Visit `/starpmaminul/admin/login`, sign in with the email printed in step 3 and the new password. If Laravel's configuration was changed on the production server and cached, run `php artisan optimize:clear` before checking connection values again; do not clear caches by changing credentials blindly.

## Security note

`config/starpmaminul.php` currently has an insecure example/default initial admin password. This is used by the **seeder only for a missing account**. Existing accounts are not reset by changing `.env` or rerunning the seeder (`firstOrCreate`). Rotate the live password as above and configure a unique, strong initial password before any fresh install. Never post the database credentials or the new password in public chats/logs.

## Founder's Message deployment

This update changes only `app/Services/Frontend/ItqanFrontendContentService.php` and `resources/views/frontend/pages/home.blade.php` for the visibility bug. After uploading both files, run `php artisan view:clear` and optionally `php artisan optimize:clear`. Check that `Home > Founder's Message > Show this section` unchecked hides the full section and checking it displays the existing content. The regression tests are in `tests/Feature/HomeFounderVisibilityTest.php`.
