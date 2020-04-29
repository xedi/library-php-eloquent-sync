# Composer

.PHONY: composer-install
.SILENT: composer-install

composer-install:
	docker run --rm \
	--volume $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))):/app \
	--user $(id -u):$(id -g) \
	composer install --ignore-platform-reqs --no-scripts --no-progress
	rm -f auth.json

# Static Analysis

.PHONY: phpcs
.SILENT: phpcs

phpcs:
	docker run --rm \
	--volume $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))):/web/html \
	--user $(id -u):$(id -g) \
	xediltd/phpcs:ci-latest

# PHPUnit

.PHONY: phpunit
.SILENT: phpunit

phpunit:
	docker run --rm \
	--volume $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))):/web/html \
	--user $(id -u):$(id -g) \
	xediltd/phpunit:latest
