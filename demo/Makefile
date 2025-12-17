# Makefile for Demo Projects
# Simplifies Docker Compose commands for each demo

# Define available demos (no prefix, direct folder names)
DEMOS := laravel symfony yii codeigniter slim legacy

.PHONY: help clean all verify-all
.PHONY: $(foreach demo,$(DEMOS),$(demo) $(demo)-down $(demo)-install $(demo)-test $(demo)-shell $(demo)-logs)
.PHONY: up down install test shell logs

# Default target
help:
	@echo "Composer Update Helper - Demo Projects"
	@echo ""
	@echo "Usage: make <target>"
	@echo ""
	@for demo in $(DEMOS); do \
		version=$$(echo $$demo | sed 's/^./\U&/' | sed 's/laravel/Laravel 12/' | sed 's/symfony/Symfony 8.0/' | sed 's/yii/Yii 2/' | sed 's/codeigniter/CodeIgniter 4/' | sed 's/slim/Slim 4/' | sed 's/legacy/Laravel 12/'); \
		if [ -f $$demo/.env.example ]; then \
			port=$$(grep -E '^PORT=' $$demo/.env.example 2>/dev/null | cut -d '=' -f2 | head -1 || echo "8001"); \
		else \
			port=8001; \
		fi; \
		echo "$$version Demo (Port $$port):"; \
		echo "  $$demo           Start $$version demo containers"; \
		echo "  $$demo-down      Stop $$version demo containers"; \
		echo "  $$demo-install   Install dependencies for $$version demo"; \
		echo "  $$demo-test      Run tests for $$version demo"; \
		echo "  $$demo-shell     Open shell in $$version container"; \
		echo "  $$demo-logs      Show $$version container logs"; \
		echo ""; \
	done
	@echo "Generic commands (use with DEMO=<name>):"
	@echo "  up <demo>         Start a specific demo (e.g., make up DEMO=laravel)"
	@echo "  down <demo>       Stop a specific demo (e.g., make down DEMO=laravel)"
	@echo "  install <demo>    Install dependencies (e.g., make install DEMO=laravel)"
	@echo "  test <demo>       Run tests (e.g., make test DEMO=laravel)"
	@echo "  shell <demo>      Open shell in a specific demo container"
	@echo "  logs <demo>      Show logs of a specific demo"
	@echo ""
	@echo "Other commands:"
	@echo "  all               Start all demos"
	@echo "  verify-all        Verify all demos are running correctly (HTTP 200)"
	@echo "  clean             Stop and remove all demo containers and volumes"
	@echo ""
	@echo "Access URLs:"
	@for demo in $(DEMOS); do \
		version=$$(echo $$demo | sed 's/^./\U&/' | sed 's/laravel/Laravel 12/' | sed 's/symfony/Symfony 8.0/' | sed 's/yii/Yii 2/' | sed 's/codeigniter/CodeIgniter 4/' | sed 's/slim/Slim 4/' | sed 's/legacy/Laravel 12/'); \
		if [ -f $$demo/.env ]; then \
			port=$$(grep -E '^PORT=' $$demo/.env 2>/dev/null | cut -d '=' -f2 | head -1 || echo "8001"); \
		elif [ -f $$demo/.env.example ]; then \
			port=$$(grep -E '^PORT=' $$demo/.env.example 2>/dev/null | cut -d '=' -f2 | head -1 || echo "8001"); \
		else \
			port=8001; \
		fi; \
		echo "  $$version: http://localhost:$$port"; \
	done

# Generate up targets for each demo
define up-target-template
$(1):
	@VERSION=$$$$(echo $(1) | sed 's/^./\U&/' | sed 's/laravel/Laravel 12/' | sed 's/symfony/Symfony 8.0/' | sed 's/yii/Yii 2/' | sed 's/codeigniter/CodeIgniter 4/' | sed 's/slim/Slim 4/' | sed 's/legacy/Laravel 12/'); \
	echo "🚀 Starting $$$$VERSION demo..."; \
	cd $(1) && \
	if [ ! -f .env ]; then cp .env.example .env 2>/dev/null || true; fi && \
	PORT=$$$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
	echo "🔍 Checking port $$$$PORT..."; \
	for container in $$$$(docker ps -a --format "{{.Names}}" 2>/dev/null); do \
		if docker port "$$$$container" 2>/dev/null | grep -q ":$$$$PORT"; then \
			echo "⚠️  Port $$$$PORT is in use by container: $$$$container"; \
			echo "🛑 Stopping and removing container $$$$container..."; \
			docker stop "$$$$container" 2>/dev/null || true; \
			docker rm "$$$$container" 2>/dev/null || true; \
			echo "✅ Port $$$$PORT is now free"; \
			sleep 2; \
			break; \
		fi; \
	done; \
	docker-compose up -d && \
	echo "⏳ Waiting for demo to be ready..." && \
	sleep 5 && \
	ATTEMPT=1; \
	while [ $$$$ATTEMPT -le 12 ]; do \
		HTTP_CODE=$$$$(curl -s -o /dev/null -w "%{http_code}" http://localhost:$$$$PORT 2>/dev/null || echo "000"); \
		if echo "$$$$HTTP_CODE" | grep -q "200"; then \
			echo "✅ $$$$VERSION demo is running and responding at http://localhost:$$$$PORT"; \
			exit 0; \
		fi; \
		echo "⏳ Attempt $$$$ATTEMPT/12: Waiting for demo to respond (got HTTP $$$$HTTP_CODE)..."; \
		sleep 5; \
		ATTEMPT=$$$$(expr $$$$ATTEMPT + 1); \
	done; \
	echo "❌ Demo did not respond with HTTP 200 after 12 attempts"; \
	echo "💡 Check logs with: make $(1)-logs"; \
	exit 1
endef

# Generate down targets for each demo
define down-target-template
$(1)-down:
	@VERSION=$$(echo $(1) | sed 's/^./\U&/' | sed 's/laravel/Laravel 12/' | sed 's/symfony/Symfony 8.0/' | sed 's/yii/Yii 2/' | sed 's/codeigniter/CodeIgniter 4/' | sed 's/slim/Slim 4/' | sed 's/legacy/Laravel 12/'); \
	echo "🛑 Stopping $$VERSION demo..."; \
	cd $(1) && docker-compose down; \
	echo "✅ $$VERSION demo stopped!"
endef

# Generate install targets for each demo
define install-target-template
$(1)-install:
	@VERSION=$$(echo $(1) | sed 's/^./\U&/' | sed 's/laravel/Laravel 12/' | sed 's/symfony/Symfony 8.0/' | sed 's/yii/Yii 2/' | sed 's/codeigniter/CodeIgniter 4/' | sed 's/slim/Slim 4/' | sed 's/legacy/Laravel 12/'); \
	echo "📦 Installing $$VERSION dependencies..."; \
	cd $(1) && docker-compose exec app composer install || \
		(PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		for container in $$(docker ps --format "{{.Names}}" 2>/dev/null); do \
			if docker port "$$container" 2>/dev/null | grep -q ":$$PORT"; then \
				docker stop "$$container" 2>/dev/null || true; \
				docker rm "$$container" 2>/dev/null || true; \
				break; \
			fi; \
		done; \
		docker-compose up -d && sleep 2 && docker-compose exec app composer install); \
	echo "✅ $$VERSION dependencies installed!"
endef

# Generate shell targets for each demo
define shell-target-template
$(1)-shell:
	@cd $(1) && docker-compose exec app sh
endef

# Generate logs targets for each demo
define logs-target-template
$(1)-logs:
	@cd $(1) && docker-compose logs -f
endef

# Generate test targets for each demo
define test-target-template
$(1)-test:
	@VERSION=$$(echo $(1) | sed 's/^./\U&/' | sed 's/laravel/Laravel 12/' | sed 's/symfony/Symfony 8.0/' | sed 's/yii/Yii 2/' | sed 's/codeigniter/CodeIgniter 4/' | sed 's/slim/Slim 4/' | sed 's/legacy/Laravel 12/'); \
	echo "🧪 Running $$VERSION tests..."; \
	cd $(1) && docker-compose exec app composer test || \
		(PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		for container in $$(docker ps --format "{{.Names}}" 2>/dev/null); do \
			if docker port "$$container" 2>/dev/null | grep -q ":$$PORT"; then \
				docker stop "$$container" 2>/dev/null || true; \
				docker rm "$$container" 2>/dev/null || true; \
				break; \
			fi; \
		done; \
		docker-compose up -d && sleep 2 && docker-compose exec app composer test)
endef

# Generate all targets for each demo
$(foreach demo,$(DEMOS),$(eval $(call up-target-template,$(demo))))
$(foreach demo,$(DEMOS),$(eval $(call down-target-template,$(demo))))
$(foreach demo,$(DEMOS),$(eval $(call install-target-template,$(demo))))
$(foreach demo,$(DEMOS),$(eval $(call shell-target-template,$(demo))))
$(foreach demo,$(DEMOS),$(eval $(call logs-target-template,$(demo))))
$(foreach demo,$(DEMOS),$(eval $(call test-target-template,$(demo))))

# Start all demos
all: $(DEMOS)
	@echo ""
	@echo "✅ All demos started!"
	@echo ""
	@echo "Access demos at:"
	@for demo in $(DEMOS); do \
		version=$$(echo $$demo | sed 's/^./\U&/' | sed 's/laravel/Laravel 12/' | sed 's/symfony/Symfony 8.0/' | sed 's/yii/Yii 2/' | sed 's/codeigniter/CodeIgniter 4/' | sed 's/slim/Slim 4/' | sed 's/legacy/Laravel 12/'); \
		if [ -f $$demo/.env ]; then \
			port=$$(grep -E '^PORT=' $$demo/.env 2>/dev/null | cut -d '=' -f2 | head -1 || echo "8001"); \
		elif [ -f $$demo/.env.example ]; then \
			port=$$(grep -E '^PORT=' $$demo/.env.example 2>/dev/null | cut -d '=' -f2 | head -1 || echo "8001"); \
		else \
			port=8001; \
		fi; \
		echo "  - $$version: http://localhost:$$port"; \
	done

# Generic up command - supports: make up symfony or make up DEMO=symfony
up:
	@DEMO_NAME=""; \
	for goal in $(MAKECMDGOALS); do \
		if [ -n "$$(echo '$(DEMOS)' | grep -w $$goal)" ]; then \
			DEMO_NAME=$$goal; \
			break; \
		fi; \
	done; \
	if [ -n "$$DEMO_NAME" ]; then \
		$(MAKE) $$DEMO_NAME; \
	elif [ -n "$(DEMO)" ]; then \
		$(MAKE) $(DEMO); \
	else \
		echo "❌ Error: Please specify a demo. Usage: make up <demo-name> or make up DEMO=<demo-name>"; \
		echo "Available demos: $(DEMOS)"; \
		exit 1; \
	fi

# Generic down command
down:
	@DEMO_NAME=""; \
	for goal in $(MAKECMDGOALS); do \
		if [ -n "$$(echo '$(DEMOS)' | grep -w $$goal)" ]; then \
			DEMO_NAME=$$goal; \
			break; \
		fi; \
	done; \
	if [ -n "$$DEMO_NAME" ]; then \
		$(MAKE) $$DEMO_NAME-down; \
	elif [ -n "$(DEMO)" ]; then \
		$(MAKE) $(DEMO)-down; \
	else \
		echo "❌ Error: Please specify a demo. Usage: make down <demo-name> or make down DEMO=<demo-name>"; \
		echo "Available demos: $(DEMOS)"; \
		exit 1; \
	fi

# Generic install command
install:
	@DEMO_NAME=""; \
	for goal in $(MAKECMDGOALS); do \
		if [ -n "$$(echo '$(DEMOS)' | grep -w $$goal)" ]; then \
			DEMO_NAME=$$goal; \
			break; \
		fi; \
	done; \
	if [ -n "$$DEMO_NAME" ]; then \
		$(MAKE) $$DEMO_NAME-install; \
	elif [ -n "$(DEMO)" ]; then \
		$(MAKE) $(DEMO)-install; \
	else \
		echo "❌ Error: Please specify a demo. Usage: make install <demo-name> or make install DEMO=<demo-name>"; \
		echo "Available demos: $(DEMOS)"; \
		exit 1; \
	fi

# Generic test command
test:
	@DEMO_NAME=""; \
	for goal in $(MAKECMDGOALS); do \
		if [ -n "$$(echo '$(DEMOS)' | grep -w $$goal)" ]; then \
			DEMO_NAME=$$goal; \
			break; \
		fi; \
	done; \
	if [ -n "$$DEMO_NAME" ]; then \
		$(MAKE) $$DEMO_NAME-test; \
	elif [ -n "$(DEMO)" ]; then \
		$(MAKE) $(DEMO)-test; \
	else \
		echo "❌ Error: Please specify a demo. Usage: make test <demo-name> or make test DEMO=<demo-name>"; \
		echo "Available demos: $(DEMOS)"; \
		exit 1; \
	fi

# Generic shell command
shell:
	@DEMO_NAME=""; \
	for goal in $(MAKECMDGOALS); do \
		if [ -n "$$(echo '$(DEMOS)' | grep -w $$goal)" ]; then \
			DEMO_NAME=$$goal; \
			break; \
		fi; \
	done; \
	if [ -n "$$DEMO_NAME" ]; then \
		$(MAKE) $$DEMO_NAME-shell; \
	elif [ -n "$(DEMO)" ]; then \
		$(MAKE) $(DEMO)-shell; \
	else \
		echo "❌ Error: Please specify a demo. Usage: make shell <demo-name> or make shell DEMO=<demo-name>"; \
		echo "Available demos: $(DEMOS)"; \
		exit 1; \
	fi

# Generic logs command
logs:
	@DEMO_NAME=""; \
	for goal in $(MAKECMDGOALS); do \
		if [ -n "$$(echo '$(DEMOS)' | grep -w $$goal)" ]; then \
			DEMO_NAME=$$goal; \
			break; \
		fi; \
	done; \
	if [ -n "$$DEMO_NAME" ]; then \
		$(MAKE) $$DEMO_NAME-logs; \
	elif [ -n "$(DEMO)" ]; then \
		$(MAKE) $(DEMO)-logs; \
	else \
		echo "❌ Error: Please specify a demo. Usage: make logs <demo-name> or make logs DEMO=<demo-name>"; \
		echo "Available demos: $(DEMOS)"; \
		exit 1; \
	fi

# Verify all demos are running correctly
verify-all:
	@echo "🔍 Verifying all demos..."
	@FAILED=0; \
	for demo in $(DEMOS); do \
		echo ""; \
		echo "Checking $$demo..."; \
		$(MAKE) $$demo > /dev/null 2>&1 && \
		sleep 2 && \
		PORT=$$(grep "^PORT=" $$demo/.env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		if curl -s -o /dev/null -w "%{http_code}" http://localhost:$$PORT | grep -q "200"; then \
			echo "✅ $$demo is running correctly at http://localhost:$$PORT"; \
			$(MAKE) $$demo-down > /dev/null 2>&1; \
		else \
			echo "❌ $$demo failed verification (no HTTP 200 response)"; \
			FAILED=$$((FAILED + 1)); \
			$(MAKE) $$demo-down > /dev/null 2>&1; \
		fi; \
	done; \
	echo ""; \
	if [ $$FAILED -gt 0 ]; then \
		echo "❌ $$FAILED demo(s) failed verification"; \
		exit 1; \
	else \
		echo "✅ All demos verified successfully"; \
	fi

# Clean vendor and cache from all demos
clean:
	@echo "🧹 Cleaning all demo containers and volumes..."
	@for demo in $(DEMOS); do \
		echo "Cleaning $$demo..."; \
		cd $$demo && docker-compose down -v 2>/dev/null || true; \
	done
	@echo "✅ Cleaned!"
