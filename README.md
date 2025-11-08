Solder Next
=============

[![License](https://poser.pugx.org/solder/solder/license.svg)](https://packagist.org/packages/solder/solder)

[//]: # (![Latest Stable Version]&#40;https://poser.pugx.org/solder/solder/v/stable.svg&#41; ![Build Status]&#40;https://github.com/TechnicPack/TechnicSolder/actions/workflows/tests.yml/badge.svg?branch=master&#41;)

[//]: # ()
[//]: # (![Latest Unstable Version]&#40;https://poser.pugx.org/solder/solder/v/unstable.svg&#41; ![Build Status]&#40;https://github.com/TechnicPack/TechnicSolder/actions/workflows/tests.yml/badge.svg?branch=dev&#41;)

Join us on [Discord][discord]!

What is Solder Next?
--------------
Solder Next aims to be a successor of [Technic Solder](https://github.com/TechnicPack/TechnicSolder).
We aim to add more functionality into Solder, with backwards compatability, so that it can still be used with the [Technic platform and Launcher](https://www.technicpack.net/).
It will also expose a more elaborate API that will allow you to build custom launchers, using a more standardized API format.

Using Solder Next also means the Technic Launcher will download each mod individually. 
This means the launcher can check MD5's against each version of a mod and if it hasn't changed, use the cached version of the mod instead. 
What does this mean? Small incremental updates to your modpack doesn't mean redownloading the whole thing every time!

Solder also interfaces with the Technic Platform using an API key you can generate through your account there.
When Solder has this key it can directly interact with your Platform account. 
When creating new modpacks you will be able to import any packs you have registered in your Solder install. 
It will also create detailed mod lists on your Platform page! (assuming you have the respective data filled out in Solder).

Credits
-------------

Solder Next would not have been possible without the initial work of the [Technic team](https://www.technicpack.net/about-us).

You can also check out the original repo over [here](https://github.com/TechnicPack/TechnicSolder)!

Requirements
-------------

* PHP 8.2 or higher
* [Composer](https://getcomposer.org/)
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

[//]: # (You can find commands for this in the [getting started page]&#40;https://docs.solder.io/reference/getting-started#requirements&#41;.)

Installation/Updating Solder Next
-------------

Refer to our documentation here: 

[//]: # (<https://docs.solder.io/>)

[//]: # (If there is any missing/incorrect info, please post an issue on our [issue tracker]&#40;https://github.com/TechnicPack/TechnicSolder/issues&#41;)

Using Docker
-------------

Docker can make managing your instance of Solder Next easier. 
To get started, you will need to install Docker and Docker Compose to your host system.
You will also need to have this repo cloned. Here's an [example for Ubuntu 22.04](https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-on-ubuntu-22-04). 
Follow the instructions below from the cloned directory:

Build the Solder Docker image.
```bash
docker build --no-cache -t solder -f ./docker/Dockerfile .
```

Run the setup to prepare your instance. 
You might need to modify `start.sh` to disable setting a new app key if you already have one. 
Make sure not to run this more than once unless you want a new app key.

```bash
docker compose -f compose-setup.yml up setup
```

Finally, you can turn on your instance of Solder.
```bash
docker compose up -d --remove-orphans
```

(The `--remove-orphans` flag is necessary to remove the container used in the setup.)

Refer to the [Docker docs](https://docs.docker.com/) for more information about how to use it.

Troubleshooting
---------------

If you are having issues and can't seem to figure out what's going on, join our [development Discord server][discord].

Disclaimer
---------------
Solder Next is an independent community fork of Technic Solder and is not affiliated with or endorsed by Technic or the Technic team.

[discord]: https://discord.gg/YmvZuR695j
