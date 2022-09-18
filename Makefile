current-dir := $(dir $(abspath $(lastword $(MAKEFILE_LIST))))

.PHONY: prepare_local
prepare_local:
	curl -sS https://get.symfony.com/cli/installer | bash

.PHONY: init_db
init_db:
	docker-compose up -d --build mysql


.PHONY: db_down
db_down:
	docker-compose down -v --rmi=all --remove-orphans

.PHONY: start_local
start_local:
	docker-compose up -d mysql
	symfony serve

.PHONY: stop_local
stop_local:
	symfony server:stop
	docker-compose stop

##################
# CI
##################

.PHONY: analyze
analyze: lint require_check cs_check phpstan security_check schema_validate phpunit

.PHONY: lint
lint:
	php ./vendor/bin/parallel-lint -j 4 --exclude .git --exclude vendor ./

.PHONY: require_check
require_check:
	php ./vendor/bin/composer-require-checker check --config-file=./composer-require-checker.json

.PHONY: cs_check
cs_check:
	php ./vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php -v --allow-risky=yes --dry-run --diff --stop-on-violation

.PHONY: cs_fix
cs_fix:
	php ./vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php -v --allow-risky=yes --diff

.PHONY: schema_validate
schema_validate:
	php bin/console doctrine:cache:clear-metadata
	php bin/console doctrine:schema:validate

.PHONY: security_check
security_check:
	symfony security:check

.PHONY: phpstan
phpstan:
	php ./vendor/bin/phpstan analyse src -c phpstan.neon

.PHONY: phpunit
phpunit:
	php ./vendor/bin/phpunit