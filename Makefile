.PHONY: bench coverage cs infection it stan test

it: cs stan test bench

bench: vendor
	vendor/bin/phpbench run --report=aggregate

coverage: vendor
	vendor/bin/phpunit --configuration=test/Unit/phpunit.xml --coverage-text

cs: vendor
	vendor/bin/php-cs-fixer fix --config=.php_cs --diff --verbose

infection: vendor
	vendor/bin/infection --min-covered-msi=95 --min-msi=95

stan: vendor
	vendor/bin/phpstan analyse --configuration=phpstan.neon --level=max src

test: vendor
	vendor/bin/phpunit --configuration=test/AutoReview/phpunit.xml
	vendor/bin/phpunit --configuration=test/Unit/phpunit.xml

vendor: composer.json composer.lock
	composer self-update
	composer validate
	composer install
