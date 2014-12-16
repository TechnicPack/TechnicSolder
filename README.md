TechnicSolder
=============

[![Latest Stable Version](https://poser.pugx.org/solder/solder/v/stable.svg)](https://packagist.org/packages/solder/solder) [![Total Downloads](https://poser.pugx.org/solder/solder/downloads.svg)](https://packagist.org/packages/solder/solder) [![Latest Unstable Version](https://poser.pugx.org/solder/solder/v/unstable.svg)](https://packagist.org/packages/solder/solder) [![License](https://poser.pugx.org/solder/solder/license.svg)](https://packagist.org/packages/solder/solder)

What is Solder?
--------------

TechnicSolder is an API that sits between a modpack repository and the launcher. It allows you to easily manage multiple modpacks in one single location. It's the same API we use to distribute our modpacks!

Using Solder also means your packs will download each mod individually. This means the launcher can check MD5's against each version of a mod and if it hasn't changed, use the cached version of the mod instead. What does this mean? Small incremental updates to your modpack doesn't mean redownloading the whole thing every time!

Solder also interfaces with the Technic Platform using an API key you can generate through your account there. When Solder has this key it can directly interact with your Platform account. When creating new modpacks you will be able to import any packs you have registered in your Solder install. It will also create detailed mod lists on your Platform page! (assuming you have the respective data filled out in Solder) Neat huh?

Requirements
-------------

* PHP 5.6+ 
* PHP MCrypt Extension
* PHP Curl Extension
* PHP GD Extension
* A sqllite, mysql, pgsql, or sqlsrv database
* Composer - PHP Dependency Manager - http://getcomposer.org

Installation
-------------

First thing is first, clone Solder into a directory on your web box. You will want to setup Apache, Nginx, or whatever you are using to use the "public" folder as the document root. This way the main Solder operating files are not publically available through the web.

Next, copy **application/config-sample** to **application/config** and **public/.htaccess-sample** to **public/.htaccess**. Edit the files in the config directory to your liking. The only two files that currently *require* editing are **database.php** and **solder.php**. You will also want to give your box permissions to write to the **storage** folder. You can do this with a chmod or whatever you prefer.

Solder.php has 2 values that need to be filled in:

* **repo_location:** This points to the location of your repo files either locally or remotely. If your Solder install is on the same box as your mod repository, you can fill in the absolute path to those files. This will greatly increase the speed in which md5's are calculated for your database. If your mod repository is hosted elsewhere, you can simply put the web accessible URL in for this value. Make sure to include a trailing slash in *both* cases.
* **mirror_url:** The mirror url is the web accessible url to your mod repository. If you are using a URL for repo_location you can just fill in the same value here.

Once you have everything filled in for your configuration, you will need to download the project dependencies and migrate your database. Doing this is *really* easy, just run the following commands in order from the root Solder folder.

1. sudo composer self-update
2. composer install
3. php artisan migrate:install
4. php artisan migrate

That's all you have to do to get your database ready!

Once you have Solder configured, just access it at the url you set it up on and log in.

The default user information is:

> Email: admin@admin.com

> Password: admin

Change this information as soon as you log in!

Updating Solder
---------------

Solder waits for no one! If you use Solder you need to make sure it's up to date. Issues you may be having may have already been resolved in a recent commit. Who knows! Updating is simple.

1. Pull in changes from your origin (git pull origin)
2. Check if any config files were changed (usually not the case) and make adjustments if necessary
3. sudo composer self-update (check for updates to Composer)
4. composer update (update the project)
5. php artisan migrate (run for any new migrations)

That's it. Your API is now on the latest version.

**As of version 0.6, changelogs will be listed below with information regarding required migrations**

Changes
---------------

**Version 0.7-DEV**

-  Upgraded Laravel Framework from 3.0 to 4.2.x
-  New login page
-  Updated User Permission System
  -  Added Manage API/Clients
  -  Added Global Modpack permissions (These are required before assigning specific modpack access)
    -  Create
    -  Manage
    -  Delete
-  Mod Library sort by name on default
-  Improved Mod Version error messages
  -  MD5 Hashing failure/success
  -  Adding new version failure/success
  -  Deleting a version failure/success
-  New Modpack Management page
-  Optimize Build Management
  -  Sort/Search mods when adding
  -  Builds views now sort by mod name by default
  -  Added ability to search for mods within builds
  -  Builds views are now paginated
-  More frequent updates!

**Version 0.6**

-  Switched to Bootstrap 3 and revamped entire Solder interface
-  API Keys are now managed by the database. Your old key will be imported during migration
-  **0.6 requires a migration!**

Troubleshooting
---------------

If you are having issues and can't seem to figure out what's going on, come ask in IRC @ **irc.synirc.net #technic** or open an issue here on GitHub. Support is not guaranteed!