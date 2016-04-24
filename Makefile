# Makefile

## Default APP_ENV to 'local' (dev environment)
ifndef $(APP_ENV)
	export APP_ENV := local
endif

.DEFAULT_GOAL := build

## --------------------------------------------------------
##  File targets
## --------------------------------------------------------

composer.phar:
	php -r "readfile('https://getcomposer.org/installer');" | php

vendor: composer.phar composer.json
	php composer.phar install
	@touch vendor

storage:
	@mkdir storage


## --------------------------------------------------------
##  Phony targets
## --------------------------------------------------------

build: vendor storage

serve: vendor storage
	cd public && php -S localhost:3000

clean:
	@rm -rf storage

dbclean:
	mongo --eval 'db.dropDatabase() && quit()' paste

distclean: clean
	@rm -rf composer.phar
	@rm -rf vendor
