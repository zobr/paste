# Paste

This is a very simple pastebin with minimal design and support for keyboard
shortcuts.

It is written in PHP with Slim framework and it uses MongoDB for text storage.

Try it out at http://p.smx.lt/


## Features

* Minimal design;
* Keyboard shortcuts;
* Syntax highlighting for over 150 languages;
* Clickable text links;
* IP blocking and whitelisting;
* Whitespace trimming;
* Simple authentication.


## Pre-requisites

* MongoDB `^3.0`
* PHP `^5.6`

Important pre-requisite is the newer MongoDB driver. This will install the
driver on an Ubuntu machine:

```
sudo pecl install mongodb
echo "extension=mongodb.so" >> /etc/php5/cli/php.ini
```


## Setup

Run a development server:

```
make serve
```

It will install the latest Composer and install all dependencies, as well as
check your environment.

If you see that server has started, it is ready to use (or to be deployed
on an end server).


## Configuration

You can load different configurations based on `APP_ENV` environment variable.
Create a copy of the `config/default.php` with the name that you will use in
`APP_ENV`, then override available options. If option does not exist in config,
it will be inherited from `config/default.php`.


## Contacts

Aleksej Komarov <[stylemistake@gmail.com]>

[stylemistake.com]: http://stylemistake.com/
[stylemistake@gmail.com]: mailto:stylemistake@gmail.com
