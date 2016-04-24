# Paste

This is a very simple pastebin with minimal design and support for keyboard
shortcuts.

It is written in PHP and uses MongoDB.


## Pre-requisites

* MongoDB `^3.0`
* PHP `^5.5`

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


## Contacts

Aleksej Komarov <[stylemistake@gmail.com]>

[stylemistake.com]: http://stylemistake.com
[stylemistake@gmail.com]: mailto:stylemistake@gmail.com
