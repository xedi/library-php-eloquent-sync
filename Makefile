# Composer

.PHONY: composer-install composer-update composer-install-dev composer-dump-auto composer-add-dep composer-add-dev-dep composer-interactive
.SILENT: composer-install composer-update composer-install-dev composer-dump-auto composer-add-dep composer-add-dev-dep composer-interactive

composer-install:
	docker run --rm \
	--volume $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))):/app \
	--user $(id -u):$(id -g) \
	composer install --ignore-platform-reqs --no-scripts ${DOWNLOAD_PROGRESS}
	rm -f auth.json

composer-update:
	docker run --rm --interactive --tty \
	--volume $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))):/app \
	--user $(id -u):$(id -g) \
	composer update --ignore-platform-reqs --no-scripts ${DOWNLOAD_PROGRESS}
	rm -f auth.json

composer-install-dev:
	docker run --rm --interactive --tty \
	-v $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))):/app \
	--user $(id -u):$(id -g) \
	composer install --ignore-platform-reqs --no-scripts --dev ${DOWNLOAD_PROGRESS}
	rm -f auth.json

composer-dump-auto:
	docker run --rm \
	--volume $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))):/app \
	--user $(id -u):$(id -g) \
	composer dump-autoload
	rm -f auth.json

composer-add-dep:
	docker run --rm --interactive --tty \
	--volume $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))):/app \
	--user $(id -u):$(id -g) \
	composer /bin/bash -ci "composer require $(module) $(version) --ignore-platform-reqs --no-scripts"
	rm -f auth.json

composer-add-dev-dep:
	docker run --rm --interactive --tty \
	--volume $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))):/app \
	--user $(id -u):$(id -g) \
	composer /bin/bash -ci "composer require $(module) $(version) --dev --ignore-platform-reqs --no-scripts"
	rm -f auth.json

composer-interactive:
	docker run --rm --interactive --tty \
	--volume $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))):/app \
	--user $(id -u):$(id -g) \
	composer /bin/bash
	rm -f auth.json

# Static Analysis

.PHONY: phpcs
.SILENT: phpcs

phpcs:
	docker run --rm --interactive --tty \
	--volume $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))):/web/html \
	--user $(id -u):$(id -g) \
	xediltd/phpcs:latest


# Testing Tools

.PHONY: test unit-test integration-test
.SILENT: test unit-test integration-test

test:
	docker run --rm --interactive --tty \
	--volume $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))):/web/html \
	--user $(id -u):$(id -g) \
	xediltd/phpunit:latest vendor/bin/phpunit --testdox

unit-test:
	docker run --rm --interactive --tty \
	--volume $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))):/web/html \
	--user $(id -u):$(id -g) \
	xediltd/phpunit:latest vendor/bin/phpunit --testsuite Unit --testdox

integration-test:
	docker run --rm --interactive --tty \
	--volume $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))):/web/html \
	--user $(id -u):$(id -g) \
	xediltd/phpunit:latest vendor/bin/phpunit --testsuite Integration --testdox
