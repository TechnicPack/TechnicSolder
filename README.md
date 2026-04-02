Technic Solder
=============

[![License](https://poser.pugx.org/solder/solder/license.svg)](https://packagist.org/packages/solder/solder)

![Latest Stable Version](https://poser.pugx.org/solder/solder/v/stable.svg) ![Build Status](https://github.com/TechnicPack/TechnicSolder/actions/workflows/tests.yml/badge.svg?branch=main)

Join us on [Discord][discord]!

What is Solder?
--------------

Technic Solder is an API that sits between a modpack repository and the launcher. It allows you to easily manage multiple modpacks in one single location. It's the same API we use to distribute our modpacks!

Using Solder also means your packs will download each mod individually. This means the launcher can check MD5's against each version of a mod and if it hasn't changed, use the cached version of the mod instead. What does this mean? Small incremental updates to your modpack doesn't mean redownloading the whole thing every time!

Solder also interfaces with the Technic Platform using an API key you can generate through your account there. When Solder has this key it can directly interact with your Platform account. When creating new modpacks you will be able to import any packs you have registered in your Solder install. It will also create detailed mod lists on your Platform page! (assuming you have the respective data filled out in Solder). Neat huh?

Requirements
-------------

* PHP 8.4 or higher
* [Composer](https://getcomposer.org/)
* [Node.js](https://nodejs.org/) 20 or higher and npm
* `unzip` package
* Ctype PHP Extension
* cURL PHP Extension
* DOM PHP Extension
* Fileinfo PHP Extension
* Filter PHP Extension
* Hash PHP Extension
* Mbstring PHP Extension
* OpenSSL PHP Extension
* PCRE PHP Extension
* PDO PHP Extension
* Session PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension
* A MySQL or PostgreSQL database

You can find commands for this in the [getting started page](https://docs.solder.io/getting-started/#requirements).

Installation/Updating Solder
-------------

Refer to our documentation here: <https://docs.solder.io/>

If there is any missing/incorrect info, please post an issue on our [issue tracker](https://github.com/TechnicPack/TechnicSolder/issues)

Using Docker
-------------

See the [Docker setup guide](https://docs.solder.io/getting-started/docker/) for full instructions.

Quick start:

```bash
git clone https://github.com/TechnicPack/TechnicSolder.git
cd TechnicSolder
docker compose up -d
```

Troubleshooting
---------------

If you are having issues and can't seem to figure out what's going on, join our [development Discord server][discord].

[discord]: https://discord.gg/0XSjZibQg6yguy1x
