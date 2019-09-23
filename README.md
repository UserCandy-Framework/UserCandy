![UserCandy](https://demo.usercandy.com/Templates/Default/Assets/images/UserCandyLogoLGBlack.png)

---

# UC Version 1.0.0

[![Discord](https://img.shields.io/discord/615493580366282753?label=Discord%20Chat)](https://discord.gg/XATkVce)
[![Join the chat at https://gitter.im/UserCandyFramework/community](https://img.shields.io/gitter/room/nwjs/nw.js.svg)](https://gitter.im/UserCandyFramework/community?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

---

## What is UserCandy?

UserCandy is a Free Open Source User Management PHP Framework built from the ground up to be Easy and Powerful.  Easy for Beginners and Powerful for Experts.

---

## Documentation

Full docs & tutorials are available at [www.usercandy.com](https://www.usercandy.com/)

---

## Demo Website

Check out the demo website at [demo.usercandy.com](https://demo.usercandy.com/)

---

## Requirements

The UserCandy server system requirements are limited.
* Apache Web Server or equivalent with mod rewrite support.
* IIS with URL Rewrite module installed - http://www.iis.net/downloads/microsoft/url-rewrite
* PHP 7.1.3 or greater is required
* fileinfo enabled (edit php.ini and uncomment php_fileinfo.dll or use php selector within cpanel if available.) mySQL Database Server or equivalent

UserCandy has been tested on the following systems.  Others may cause issues.  Please report in Forums if you have tried a different setup.
* Latest versions of Xammp on Windows 10
* Latest versions of LAMP stack on Ubuntu 16.01

---

# Basic Installation

1. Download the latest version of UserCandy Framework from UserCandy Downloads.
2. Unzip the UserCandy Framework zip file into your server directory. /var/www/html/ etc
3. Open your web browser and navigate to the url for your project. https://localhost/ etc
4. Follow the Installation instructions to create your config file and import data to database.
5. Once you have successfully installed the UserCandy Framework be sure your the first to Register.
6. Login to your UserCany Framework project and navigate to the AdminPanel from the dropdown by your username.
7. Chance settings as needed to fit your needs.
8. Enjoy your installation.  If you run into any issues please post on the UserCandy Forum.  We are happy to help.


---

# Install with Composer

UserCandy is on packagist [https://packagist.org/packages/usercandy/usercandy-framework](https://packagist.org/packages/usercandy/uc-user-management)

Install from terminal now by using:

```
composer create-project usercandy/usercandy-framework foldername dev-master
```

The foldername is the desired folder to be created.

Once installed on your server, open the site, and it will display an install script.

---

# Install with everything inside your public folder (NOT Recommended)

Option 1 - files above document root:

1. Download the latest version of UserCandy Framework from UserCandy Downloads.
2. Move index.php and .htaccess files to the webroot folder.
3. Open index.php and change the paths from using DIR to FILE:

````
define('ROOTDIR', realpath(__DIR__).'/');
define('SYSTEMDIR', realpath(__DIR__.'/system/').'/');
define('CUSTOMDIR', realpath(__DIR__.'/custom/').'/');
````

4. Edit .htaccess set the rewritebase if running on a sub folder otherwise a single / will do.
5. Edit /system/Example-Config.php settings to connect to your database. Refer to UserCandy Docs.
6. Rename the Example-Config.php to Config.php
7. Import the database.sql to your database (Updated table PREFIX if changed in Config.php).
8. Enjoy!

---

# Setting up a VirtualHost (Optional but recommended)

Navigate to:
````
<path to your xampp installation>\apache\conf\extra\httpd-vhosts.conf
````

and uncomment:

````
NameVirtualHost *:80
````

Then add your VirtualHost to the same file at the bottom:

````
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot "C:\xampp\htdocs\testproject\public"
    ServerName testproject.dev

    <Directory "C:\xampp\htdocs\testproject\public">
        Options Indexes FollowSymLinks Includes ExecCGI
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>
</VirtualHost>
````

Finally, find your hosts file and add:

````
127.0.0.1       testproject.dev
````

You should then have a virtual host set up, and in your web browser, you can navigate to testproject.dev to see what you are working on.

---
