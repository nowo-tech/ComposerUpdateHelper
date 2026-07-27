# Makefile for Composer Update Helper
# All dev targets use the root docker-compose.yml.

COMPOSE_FILE := docker-compose.yml
COMPOSE     := docker-compose -f $(COMPOSE_FILE)
SERVICE_PHP := php

.PHONY: help up down down-dev build shell install ensure-up test test-coverage coverage-check cs-check cs-fix rector rector-dry phpstan qa \
	check-no-cursor-coauthor check-open-prs strip-cursor-coauthor-from-history \
	release-check release-check-demos composer-sync clean update validate assets setup-hooks update-deps update-deps-demos

help:
	@echo "Composer Update Helper - Development Commands"
	@echo ""
	@echo "Usage: make <target>"
	@echo ""
	@echo "Targets:"
	@echo "  up              Start Docker container"
	@echo "  down            Stop Docker container"
	@echo "  down-dev        Stop containers (keep volumes; REQ-MAKE-007)"
	@echo "  build           Rebuild Docker image (no cache)"
	@echo "  shell           Open shell in container"
	@echo "  install         Install Composer dependencies"
	@echo "  assets          No-op (no frontend in this package)"
	@echo "  test            Run PHPUnit tests"
	@echo "  test-coverage   Run tests with code coverage"
	@echo "  coverage-check  Fail if PHP line coverage is below 99%"
	@echo "  cs-check        Check code style"
	@echo "  cs-fix          Fix code style"
	@echo "  rector          Apply Rector refactoring"
	@echo "  rector-dry      Run Rector in dry-run mode"
	@echo "  phpstan         Run PHPStan static analysis"
	@echo "  qa              Run QA (cs-check + test)"
	@echo "  release-check   Pre-release pipeline (git/PR gates, sync, style, analysis, coverage)"
	@echo "  check-no-cursor-coauthor  Fail if Cursor co-author trailers in history (REQ-GIT-001)"
	@echo "  check-open-prs  Fail if unresolved open GitHub PRs remain (REQ-REL-003)"
	@echo "  composer-sync   Validate composer.json and align composer.lock"
	@echo "  clean           Remove vendor and local artifacts"
	@echo "  update          composer update in container"
	@echo "  validate        composer validate --strict"
	@echo "  setup-hooks     Install git hooks (pre-commit + commit-msg)"
	@echo ""

build:
	$(COMPOSE) build --no-cache

up:
	$(COMPOSE) build
	$(COMPOSE) up -d
	@echo "Installing dependencies..."
	$(COMPOSE) exec $(SERVICE_PHP) composer install --no-interaction
	@echo "✅ Container ready!"

down:
	$(COMPOSE) down

# Stop containers without removing volumes (REQ-MAKE-007)
down-dev:
	$(COMPOSE) down --remove-orphans

shell:
	$(COMPOSE) exec $(SERVICE_PHP) sh

install: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer install

ensure-up:
	@if ! $(COMPOSE) exec -T $(SERVICE_PHP) true 2>/dev/null; then \
		echo "Starting container (root docker-compose)..."; \
		$(COMPOSE) up -d; \
		sleep 3; \
		$(COMPOSE) exec -T $(SERVICE_PHP) composer install --no-interaction; \
	fi

test: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer test

test-coverage: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer test-coverage | tee coverage-php.txt
	@./.scripts/php-coverage-percent.sh coverage-php.txt

coverage-check: test-coverage
	@./.scripts/php-coverage-percent.sh coverage-php.txt >/tmp/cuh-cov.txt
	@pct=$$(sed -n 's/.*Global PHP coverage (Lines):[[:space:]]*\([0-9][0-9]*\.[0-9]*\).*/\1/p' /tmp/cuh-cov.txt | head -1); \
	awk -v p="$${pct:-0}" 'BEGIN { if ((p+0) < 99) { printf "ERROR: PHP coverage %s%% is below 99%% (REQ-TEST-006)\n", p > "/dev/stderr"; exit 1 } printf "OK: PHP coverage %s%% >= 99%%\n", p }'

cs-check: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer cs-check

cs-fix: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer cs-fix

rector: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer rector

rector-dry: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer rector-dry

phpstan: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer phpstan

qa: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer qa

update: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer update --no-interaction

validate: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer validate --strict

composer-sync: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer validate --strict
	$(COMPOSE) exec -T $(SERVICE_PHP) composer update --no-install

# Pre-release (REQ-MAKE-002): git/PR gates → composer-sync → QA → demos
release-check: ensure-up check-no-cursor-coauthor check-open-prs composer-sync cs-fix cs-check rector-dry phpstan test-coverage release-check-demos

release-check-demos:
	@if [ -f demo/Makefile ]; then $(MAKE) -C demo release-check; else echo "No demo/Makefile — skip release-check-demos"; fi

clean:
	rm -rf vendor .phpunit.cache coverage coverage.xml .php-cs-fixer.cache
	rm -f coverage-php.txt

assets:
	@echo "No frontend assets in this package."

check-no-cursor-coauthor:
	@chmod +x .scripts/check-no-cursor-coauthor.sh
	@./.scripts/check-no-cursor-coauthor.sh HEAD

check-open-prs:
	@chmod +x .scripts/check-open-prs.sh
	@bash .scripts/check-open-prs.sh

setup-hooks:
	@chmod +x .githooks/pre-commit 2>/dev/null || true
	@chmod +x .githooks/commit-msg 2>/dev/null || true
	@git config core.hooksPath .githooks
	@echo "✅ Git hooks installed (.githooks — includes commit-msg for REQ-GIT-001)."


# REQ-MAKE-008: update-deps (REQ-MAKE-008)
BUNDLE_ROOT := $(abspath $(dir $(lastword $(MAKEFILE_LIST))))
include $(BUNDLE_ROOT)/../.scripts/Makefile.update-deps.mk

strip-cursor-coauthor-from-history:
	@chmod +x .scripts/strip-cursor-coauthor-from-history.sh
	@./.scripts/strip-cursor-coauthor-from-history.sh main
