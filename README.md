Technic Solder
=============

[![License](https://poser.pugx.org/solder/solder/license.svg)](https://packagist.org/packages/solder/solder)
[![Latest Stable Version](https://poser.pugx.org/solder/solder/v/stable.svg)](https://packagist.org/packages/solder/solder) [![Build Status](https://travis-ci.org/TechnicPack/TechnicSolder.svg?branch=master)](https://travis-ci.org/TechnicPack/TechnicSolder)
[![Latest Unstable Version](https://poser.pugx.org/solder/solder/v/unstable.svg)](https://packagist.org/packages/solder/solder) [![Build Status](https://travis-ci.org/TechnicPack/TechnicSolder.svg?branch=dev)](https://travis-ci.org/TechnicPack/TechnicSolder)

Join us on [Discord][discord]!

What is Solder?
--------------

Technic Solder is an API that sits between a modpack repository and the launcher. It allows you to easily manage multiple modpacks in one single location. It's the same API we use to distribute our modpacks!

Using Solder also means your packs will download each mod individually. This means the launcher can check MD5's against each version of a mod and if it hasn't changed, use the cached version of the mod instead. What does this mean? Small incremental updates to your modpack doesn't mean redownloading the whole thing every time!

Solder also interfaces with the Technic Platform using an API key you can generate through your account there. When Solder has this key it can directly interact with your Platform account. When creating new modpacks you will be able to import any packs you have registered in your Solder install. It will also create detailed mod lists on your Platform page! (assuming you have the respective data filled out in Solder). Neat huh?

Requirements
-------------

* PHP >= 7.2.0
* Composer - <https://getcomposer.org/>
* BCMath PHP Extension
* Ctype PHP Extension
* Fileinfo PHP extension
* JSON PHP Extension
* Mbstring PHP Extension
* OpenSSL PHP Extension
* PDO PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension
* A MySQL or PostgreSQL database

Installation/Updating Solder
-------------

Refer to our documentation here: <https://docs.solder.io/>

If there is any missing/incorrect info, please post an issue on our [issue tracker](https://github.com/TechnicPack/TechnicSolder/issues)

Troubleshooting
---------------

If you are having issues and can't seem to figure out what's going on, join our [development Discord server][discord].

[discord]: https://discord.gg/0XSjZibQg6yguy1x
