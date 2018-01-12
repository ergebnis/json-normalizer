.PHONY: bench composer coverage cs infection it test

it: cs test bench

bench: composer
	vendor/bin/phpbench run --report=aggregate

composer:
	composer self-update
	composer validate
	composer install

coverage: composer
	vendor/bin/phpunit --configuration=test/Unit/phpunit.xml --coverage-text

cs: composer
	vendor/bin/php-cs-fixer fix --config=.php_cs --diff --verbose

infection:
	vendor/bin/infection --min-covered-msi=75 --min-msi=75

test: composer
	vendor/bin/phpunit --configuration=test/Unit/phpunit.xml
