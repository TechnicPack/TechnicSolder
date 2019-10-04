TechnicSolder
=============

[![License](https://poser.pugx.org/solder/solder/license.svg)](https://packagist.org/packages/solder/solder)
[![Latest Stable Version](https://poser.pugx.org/solder/solder/v/stable.svg)](https://packagist.org/packages/solder/solder) [![Build Status](https://travis-ci.org/TechnicPack/TechnicSolder.svg?branch=master)](https://travis-ci.org/TechnicPack/TechnicSolder)
[![Latest Unstable Version](https://poser.pugx.org/solder/solder/v/unstable.svg)](https://packagist.org/packages/solder/solder) [![Build Status](https://travis-ci.org/TechnicPack/TechnicSolder.svg?branch=dev)](https://travis-ci.org/TechnicPack/TechnicSolder)

Join us on [Discord][discord]!

What is Solder?
--------------

TechnicSolder is an API that sits between a modpack repository and the launcher. It allows you to easily manage multiple modpacks in one single location. It's the same API we use to distribute our modpacks!

Using Solder also means your packs will download each mod individually. This means the launcher can check MD5's against each version of a mod and if it hasn't changed, use the cached version of the mod instead. What does this mean? Small incremental updates to your modpack doesn't mean redownloading the whole thing every time!

Solder also interfaces with the Technic Platform using an API key you can generate through your account there. When Solder has this key it can directly interact with your Platform account. When creating new modpacks you will be able to import any packs you have registered in your Solder install. It will also create detailed mod lists on your Platform page! (assuming you have the respective data filled out in Solder). Neat huh?

Requirements
-------------

* PHP 5.6 up to 7.0 (PHP 7.1+ **are not** supported)
* PHP MCrypt extension
* PHP curl extension
* PHP GD extension
* A SQLite, MySQL, PostgreSQL, or MSSQL database
* Composer - PHP Dependency Manager - <https://getcomposer.org/>

Installation/Updating Solder
-------------

Refer to our documentation here: <https://docs.solder.io/>

If there is any missing/incorrect info, please post an issue on our [issue tracker](https://github.com/TechnicPack/TechnicSolder/issues)

Changes
---------------

Changes are now displayed within the application itself.

Also see the [Technic News](https://www.technicpack.net/article/category/10) feed for Solder updates


Troubleshooting
---------------

If you are having issues and can't seem to figure out what's going on, join our [development Discord server][discord].

[discord]: https://discord.gg/0XSjZibQg6yguy1x