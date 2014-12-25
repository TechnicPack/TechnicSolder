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

Installation/Updating Solder
-------------

Refer to our wiki here: https://github.com/TechnicPack/TechnicSolder/wiki

If there is any missing info, please post an issue on our [issue tracker](https://github.com/TechnicPack/TechnicSolder/issues)

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

[@GenPage](http://twitter.com/gen_page)

If you are having issues and can't seem to figure out what's going on, come ask GenPage in IRC @ **irc.synirc.net #technic** or open an issue here on GitHub. Support is not guaranteed!

