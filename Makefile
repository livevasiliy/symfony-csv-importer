# Sometimes non-interactive mode should be enabled (e.g. pre-push hooks)
ifeq (true, $(non-i))
  	PHP=docker-compose exec -T php
else
	PHP=docker-compose exec php
endif

php: prerequisites
	docker-compose exec php sh

##############################################################
# Docker compose                                             #
##############################################################
build:
	cp .env.dist .env
	docker-compose build

run:
	docker-compose up

stop:
	docker-compose stop

down:
	docker-compose down -v --rmi=all --remove-orphans

##############################################################
# Application	                                             #
##############################################################
tests: prerequisites
	$(PHP) bin/phpunit
	$(PHP) composer coverage-check

##############################################################
# Xdebug				                                     #
##############################################################

xdebug-status:
	@cd docker/php/xdebug && bash xdebug status

xdebug-stop:
	@cd docker/php/xdebug && bash xdebug stop

xdebug-start:
	@cd docker/php/xdebug && bash xdebug start

##############################################################
# Prerequisites Setup                                        #
##############################################################

# We need both vendor/autoload.php and composer.lock being up to date
.PHONY: prerequisites
prerequisites: vendor/autoload.php composer.lock
