all:
	@echo "make test"

test:
	php vendor/bin/phpunit

install:
	curl -sS https://getcomposer.org/installer | php
	php composer.phar install

server:
	php -S localhost:8080 -t examples
