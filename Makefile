# Makefile for Composer Update Helper
# Simplifies Docker commands for development

.PHONY: help up down shell install test test-coverage cs-check cs-fix qa clean setup-hooks ensure-up

# Default target
help:
	@echo "Composer Update Helper - Development Commands"
	@echo ""
	@echo "Usage: make <target>"
	@echo ""
	@echo "Targets:"
	@echo "  up            Start Docker container"
	@echo "  down          Stop Docker container"
	@echo "  shell         Open shell in container"
	@echo "  install       Install Composer dependencies"
	@echo "  test          Run PHPUnit tests"
	@echo "  test-coverage Run tests with code coverage"
	@echo "  cs-check      Check code style"
	@echo "  cs-fix        Fix code style"
	@echo "  qa            Run all QA checks (cs-check + test)"
	@echo "  clean         Remove vendor and cache"
	@echo "  setup-hooks   Install git pre-commit hooks"
	@echo ""

# Build and start container
up:
	docker-compose build
	docker-compose up -d
	@echo "Installing dependencies..."
	docker-compose exec php composer install --no-interaction
	@echo "✅ Container ready!"

# Stop container
down:
	docker-compose down

# Ensure root container is running (start if not). Used by cs-fix, cs-check, qa, install, test, test-coverage.
ensure-up:
	@if ! docker-compose exec -T php true 2>/dev/null; then \
		echo "Starting container (root docker-compose)..."; \
		docker-compose up -d; \
		sleep 3; \
		docker-compose exec -T php composer install --no-interaction; \
	fi

# Open shell in container
shell:
	docker-compose exec php sh

# Install dependencies
install: ensure-up
	docker-compose exec -T php composer install

# Run tests
test: ensure-up
	docker-compose exec -T php composer test

# Run tests with coverage
test-coverage: ensure-up
	docker-compose exec -T php composer test-coverage

# Check code style
cs-check: ensure-up
	docker-compose exec -T php composer cs-check

# Fix code style
cs-fix: ensure-up
	docker-compose exec -T php composer cs-fix

# Run all QA
qa: ensure-up
	docker-compose exec -T php composer qa

# Clean vendor and cache
clean:
	rm -rf vendor
	rm -rf .phpunit.cache
	rm -rf coverage
	rm -f coverage.xml
	rm -f .php-cs-fixer.cache

# Setup git hooks for pre-commit checks
setup-hooks:
	chmod +x .githooks/pre-commit
	git config core.hooksPath .githooks
	@echo "✅ Git hooks installed! CS-check and tests will run before each commit."

