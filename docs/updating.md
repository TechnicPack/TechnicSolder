Updating from v0.7 onward
=========================

**Make sure to backup your SQL database before any update!**

Puts the Solder application into maintenance mode. (No one can access)

`php artisan down`

This will pull the latest changes from the GitHub repo

`git pull`

_Note: If this fails, you probably installed solder using composer. Please reinstall using the Getting Started guide._

This will update existing or new dependencies

If you installed composer locally: `php composer.phar update`

If you installed it globally: `composer update`

_This command will fail if Solder is not put into maintenance mode_

Brings the Solder application out maintenance mode.

`php artisan up`

_Note: If the update contains a new database migration (DB change), you will not notice it. Please make sure to always check the changelogs for any updates before proceeding._

Here is the command to migrate new DB changes

`php artisan migrate`

**If you installed Solder using composer create-project, please reinstall solder again using the most recent info on the Getting Started guide.**

## Updating from pre-v0.7
**Make sure to backup your SQL database and Modpack resources (images) before attempting**

When updating your Solder install, you will be doing a complete wipe of the application. Just completely delete the entire solder installation and follow the Installation steps laid out in the [Getting Started](https://github.com/TechnicPack/TechnicSolder/wiki/Getting-Started) page. Once installed, follow the Laravel DB migration steps below.

### Laravel DB migration changes

Laravel changes the way it handles DB migrations which will cause problems when trying to migrate the new DB migrations in v0.7. 

The old table name was set as 'laravel_migrations'. The new table name that tracks your migrations is set in `app/config/database.php` under the key-value pair `migrations`. Because of this, when you try to migrate the new changes, it will attempt to start from the beginning. 

Old structure is as follows: 3 columns (bundle, name, batch).
New structure is as follows: 2 columns (migration, batch).

The `bundle` column can be deleted and the `name` column renamed to `migration`.

Once you have done that and renamed the table as well with `RENAME TABLE` `laravel_migrations` TO `migrations`.

You should be able to execute `php artisan migrate` without any more issues.