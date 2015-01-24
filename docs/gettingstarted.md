Getting Started
===============

Technic Solder is a server-side PHP application based on the Laravel Framework. Its main goal is to provide delta encoded files for the Technic Launcher to process. This can include any type of file. Another goal is to process file check-sums to ensure the receiver is obtaining unaltered files.

**This application is intended for experienced system administrators**

##Installing

### Before you begin...

You will need to setup your server with a proper software stack. Technic recommends a "LEMP" stack. This stands for Linux OS, (E)Nginx web server, MySQL database server, and dynamic content processor PHP. Here are some great guides from Digital Ocean.

Ubuntu 14.04: https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-on-ubuntu-14-04

Debian 7: https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-on-debian-7

Arch Linux: https://www.digitalocean.com/community/tutorials/how-to-install-lemp-nginx-mysql-php-stack-on-arch-linux

CentOS 6: https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-on-centos-6

CentOS 7: https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-on-centos-7

_Note: Windows installation is not supported (No help will be provided.)_

***
### Composer

New to Solder v0.7 is Composer. Composer is a tool for dependency management in PHP. It allows you to declare the dependent libraries your project needs and it will install them in your project for you.

_When using the following guides, do not create your own composer.json file, and do not do the "composer install" or "php composer.phar install" command. These are handled later in this Getting Started guide._

Linux/Unix/Mac OS X: https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx
Windows: https://getcomposer.org/doc/00-intro.md#installation-windows

Installing the program globally is recommended to ensure proper execution.

Before using Composer make sure to check for updates.

If you installed Composer locally: `sudo php composer.phar self-update`

If you installed it globally: `sudo composer self-update`

***
### Installation

You can install solder manually by cloning the GitHub repository and running a composer command.

HTTP (recommended): `git clone https://github.com/TechnicPack/TechnicSolder.git`

SSH: `git clone git@github.com:TechnicPack/TechnicSolder.git`

_Note: If you prefer the cloned directory to be named differently, append the name to the git command. (ex. `git clone git@github.com:TechnicPack/TechnicSolder.git solder`)_

_Note: If you prefer to test dev changes, add `-b dev` to the clone command._

Now install the application from the root of the solder folder.

If you installed Composer locally: `php composer.phar install --no-interaction`

If you installed it globally: `composer install --no-interaction`

This will copy `app/config-sample` to `app/config` and `app/database-sample/production.sqlite` to `app/database/production.sqlite`


**This will take a considerable amount of time. Be Patient.**

Once the application is installed, make sure that the web directory root for the application is pointed to the public folder. Within the public folder, there is an index.php file that is the document root.

_Note: `public/resources` and `app/storage` folder and its contents requires write access by the web server._

***
### Database Setup

Solder already comes pre-setup and configure to use SQLite. 

If you wish to use a different database, please make the necessary changes to app/config/database.php and run the DB migrations. 

`php artisan migrate:install`

`php artisan migrate`

***
### Platform Linking

`app/config/solder.php` has 3 values that need to be filled in:

* **repo_location:** This points to the location of your repo files either locally or remotely. If your Solder install is on the same box as your mod repository, you can fill in the absolute path to those files. This will greatly increase the speed in which md5's are calculated for your database. If your mod repository is hosted elsewhere, you can simply put the web accessible URL in for this value. Make sure to include a trailing slash in *both* cases.
* **mirror_url:** The mirror url is the web accessible url to your mod repository. If you are using a URL for repo_location you can just fill in the same value here.
* **md5filetimeout:** This is the amount of time Solder will wait before giving up trying to calculate the MD5 checksum.

Platform API Keys are now handled through the application itself. See "Configure Solder -> API Key Management"

***
### Log in!

Once you have Solder configured, just access it at the url you set it up on and log in.

The default user information is:

> Email: admin@admin.com

> Password: admin

Change this information as soon as you log in!
