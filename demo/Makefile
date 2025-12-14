# Makefile for Demo Projects
# Simplifies Docker Compose commands for each demo

.PHONY: help laravel symfony yii codeigniter slim legacy all up down install logs test shell clean

# Default target
help:
	@echo "Composer Update Helper - Demo Projects"
	@echo ""
	@echo "Usage: make <target>"
	@echo ""
	@echo "Targets:"
	@echo "  help              Show this help message"
	@echo "  laravel           Start Laravel 12 demo (port 8001)"
	@echo "  symfony           Start Symfony 8.0 demo (port 8001)"
	@echo "  yii               Start Yii 2 demo (port 8001)"
	@echo "  codeigniter       Start CodeIgniter 4 demo (port 8001)"
	@echo "  slim              Start Slim 4 demo (port 8001)"
	@echo "  legacy            Start Laravel 12 demo (port 8001)"
	@echo "  all               Start all demos"
	@echo ""
	@echo "Generic commands (use with DEMO=<name>):"
	@echo "  up <demo>         Start a specific demo (e.g., make up DEMO=laravel)"
	@echo "  down <demo>       Stop a specific demo (e.g., make down DEMO=laravel)"
	@echo "  install <demo>    Install dependencies (e.g., make install DEMO=laravel)"
	@echo "  logs <demo>       Show logs of a specific demo"
	@echo "  test <demo>       Run tests of a specific demo"
	@echo "  shell <demo>      Open shell in a specific demo container"
	@echo ""
	@echo "Specific demo commands:"
	@echo "  <demo>-down       Stop specific demo (e.g., make laravel-down)"
	@echo "  <demo>-install    Install dependencies (e.g., make laravel-install)"
	@echo "  <demo>-test       Run tests (e.g., make laravel-test)"
	@echo ""
	@echo "  clean             Stop and remove all demo containers and volumes"
	@echo ""

# Start Laravel demo
laravel:
	@echo "🚀 Starting Laravel 12 demo..."
	@cd laravel && \
		if [ ! -f .env ]; then cp .env.example .env; fi && \
		PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		echo "🔍 Checking port $$PORT..."; \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			echo "⚠️  Port $$PORT is in use by container: $$CONTAINER"; \
			echo "🛑 Stopping container $$CONTAINER..."; \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
			echo "✅ Port $$PORT is now free"; \
		fi && \
		docker-compose up -d && \
		echo "✅ Laravel demo running at http://localhost:$$PORT"

# Stop Laravel demo
laravel-down:
	@echo "🛑 Stopping Laravel demo..."
	@cd laravel && docker-compose down
	@echo "✅ Laravel demo stopped!"

# Install Laravel dependencies
laravel-install:
	@echo "📦 Installing Laravel dependencies..."
	@cd laravel && docker-compose exec app composer install || \
		(PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
		fi; \
		docker-compose up -d && sleep 2 && docker-compose exec app composer install)
	@echo "✅ Laravel dependencies installed!"

# Run Laravel tests
laravel-test:
	@echo "🧪 Running Laravel tests..."
	@cd laravel && docker-compose exec app composer test || \
		(PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
		fi; \
		docker-compose up -d && sleep 2 && docker-compose exec app composer test)

# Start Symfony demo
symfony:
	@echo "🎯 Starting Symfony 8.0 demo..."
	@cd symfony && \
		if [ ! -f .env ]; then cp .env.example .env; fi && \
		PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		echo "🔍 Checking port $$PORT..."; \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			echo "⚠️  Port $$PORT is in use by container: $$CONTAINER"; \
			echo "🛑 Stopping container $$CONTAINER..."; \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
			echo "✅ Port $$PORT is now free"; \
		fi && \
		docker-compose up -d && \
		echo "✅ Symfony demo running at http://localhost:$$PORT"

# Stop Symfony demo
symfony-down:
	@echo "🛑 Stopping Symfony demo..."
	@cd symfony && docker-compose down
	@echo "✅ Symfony demo stopped!"

# Install Symfony dependencies
symfony-install:
	@echo "📦 Installing Symfony dependencies..."
	@cd symfony && docker-compose exec app composer install || \
		(PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
		fi; \
		docker-compose up -d && sleep 2 && docker-compose exec app composer install)
	@echo "✅ Symfony dependencies installed!"

# Run Symfony tests
symfony-test:
	@echo "🧪 Running Symfony tests..."
	@cd symfony && docker-compose exec app composer test || \
		(PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
		fi; \
		docker-compose up -d && sleep 2 && docker-compose exec app composer test)

# Start Yii demo
yii:
	@echo "🎨 Starting Yii 2 demo..."
	@cd yii && \
		if [ ! -f .env ]; then cp .env.example .env; fi && \
		PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		echo "🔍 Checking port $$PORT..."; \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			echo "⚠️  Port $$PORT is in use by container: $$CONTAINER"; \
			echo "🛑 Stopping container $$CONTAINER..."; \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
			echo "✅ Port $$PORT is now free"; \
		fi && \
		docker-compose up -d && \
		echo "✅ Yii demo running at http://localhost:$$PORT"

# Stop Yii demo
yii-down:
	@echo "🛑 Stopping Yii demo..."
	@cd yii && docker-compose down
	@echo "✅ Yii demo stopped!"

# Install Yii dependencies
yii-install:
	@echo "📦 Installing Yii dependencies..."
	@cd yii && docker-compose exec app composer install || \
		(PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
		fi; \
		docker-compose up -d && sleep 2 && docker-compose exec app composer install)
	@echo "✅ Yii dependencies installed!"

# Run Yii tests
yii-test:
	@echo "🧪 Running Yii tests..."
	@cd yii && docker-compose exec app composer test || \
		(PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
		fi; \
		docker-compose up -d && sleep 2 && docker-compose exec app composer test)

# Start CodeIgniter demo
codeigniter:
	@echo "🔥 Starting CodeIgniter 4 demo..."
	@cd codeigniter && \
		if [ ! -f .env ]; then cp .env.example .env; fi && \
		PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		echo "🔍 Checking port $$PORT..."; \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			echo "⚠️  Port $$PORT is in use by container: $$CONTAINER"; \
			echo "🛑 Stopping container $$CONTAINER..."; \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
			echo "✅ Port $$PORT is now free"; \
		fi && \
		docker-compose up -d && \
		echo "✅ CodeIgniter demo running at http://localhost:$$PORT"

# Stop CodeIgniter demo
codeigniter-down:
	@echo "🛑 Stopping CodeIgniter demo..."
	@cd codeigniter && docker-compose down
	@echo "✅ CodeIgniter demo stopped!"

# Install CodeIgniter dependencies
codeigniter-install:
	@echo "📦 Installing CodeIgniter dependencies..."
	@cd codeigniter && docker-compose exec app composer install || \
		(PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
		fi; \
		docker-compose up -d && sleep 2 && docker-compose exec app composer install)
	@echo "✅ CodeIgniter dependencies installed!"

# Run CodeIgniter tests
codeigniter-test:
	@echo "🧪 Running CodeIgniter tests..."
	@cd codeigniter && docker-compose exec app composer test || \
		(PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
		fi; \
		docker-compose up -d && sleep 2 && docker-compose exec app composer test)

# Start Slim demo
slim:
	@echo "⚡ Starting Slim 4 demo..."
	@cd slim && \
		if [ ! -f .env ]; then cp .env.example .env; fi && \
		PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		echo "🔍 Checking port $$PORT..."; \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			echo "⚠️  Port $$PORT is in use by container: $$CONTAINER"; \
			echo "🛑 Stopping container $$CONTAINER..."; \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
			echo "✅ Port $$PORT is now free"; \
		fi && \
		docker-compose up -d && \
		echo "✅ Slim demo running at http://localhost:$$PORT"

# Stop Slim demo
slim-down:
	@echo "🛑 Stopping Slim demo..."
	@cd slim && docker-compose down
	@echo "✅ Slim demo stopped!"

# Install Slim dependencies
slim-install:
	@echo "📦 Installing Slim dependencies..."
	@cd slim && docker-compose exec app composer install || \
		(PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
		fi; \
		docker-compose up -d && sleep 2 && docker-compose exec app composer install)
	@echo "✅ Slim dependencies installed!"

# Run Slim tests
slim-test:
	@echo "🧪 Running Slim tests..."
	@cd slim && docker-compose exec app composer test || \
		(PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
		fi; \
		docker-compose up -d && sleep 2 && docker-compose exec app composer test)

# Start Legacy demo
legacy:
	@echo "🔧 Starting Laravel 12 demo..."
	@cd legacy && \
		if [ ! -f .env ]; then cp .env.example .env; fi && \
		PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		echo "🔍 Checking port $$PORT..."; \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			echo "⚠️  Port $$PORT is in use by container: $$CONTAINER"; \
			echo "🛑 Stopping container $$CONTAINER..."; \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
			echo "✅ Port $$PORT is now free"; \
		fi && \
		docker-compose up -d && \
		echo "✅ Legacy demo running at http://localhost:$$PORT"

# Stop Legacy demo
legacy-down:
	@echo "🛑 Stopping Legacy demo..."
	@cd legacy && docker-compose down
	@echo "✅ Legacy demo stopped!"

# Install Legacy dependencies
legacy-install:
	@echo "📦 Installing Legacy dependencies..."
	@cd legacy && docker-compose exec app composer install || \
		(PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
		fi; \
		docker-compose up -d && sleep 2 && docker-compose exec app composer install)
	@echo "✅ Legacy dependencies installed!"

# Run Legacy tests
legacy-test:
	@echo "🧪 Running Legacy tests..."
	@cd legacy && docker-compose exec app composer test || \
		(PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo 8001); \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
		fi; \
		docker-compose up -d && sleep 2 && docker-compose exec app composer test)

# Start all demos
all: laravel symfony yii codeigniter slim legacy
	@echo ""
	@echo "✅ All demos started!"
	@echo ""
	@echo "Access demos at:"
	@echo "  - Laravel:      http://localhost:8001"
	@echo "  - Symfony:     http://localhost:8001"
	@echo "  - Yii:         http://localhost:8001"
	@echo "  - CodeIgniter: http://localhost:8001"
	@echo "  - Slim:        http://localhost:8001"
	@echo "  - Legacy:      http://localhost:8001"

# Generic up command
up:
	@if [ -z "$(DEMO)" ]; then \
		echo "❌ Error: Please specify a demo. Usage: make up DEMO=<demo-name>"; \
		echo "Available demos: laravel, symfony, yii, codeigniter, slim, legacy"; \
		exit 1; \
	fi
	@echo "🚀 Starting $(DEMO) demo..."
	@cd $(DEMO) && \
		if [ ! -f .env ]; then cp .env.example .env; fi && \
		PORT_DEFAULT=8001; \
		PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo $$PORT_DEFAULT); \
		if [ -n "$$PORT" ]; then \
			echo "🔍 Checking port $$PORT..."; \
			CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
			if [ -n "$$CONTAINER" ]; then \
				echo "⚠️  Port $$PORT is in use by container: $$CONTAINER"; \
				echo "🛑 Stopping container $$CONTAINER..."; \
				docker stop $$CONTAINER 2>/dev/null || true; \
				docker rm $$CONTAINER 2>/dev/null || true; \
				echo "✅ Port $$PORT is now free"; \
			fi; \
		fi && \
		docker-compose up -d && \
		echo "✅ $(DEMO) demo started!"

# Generic down command
down:
	@if [ -z "$(DEMO)" ]; then \
		echo "❌ Error: Please specify a demo. Usage: make down DEMO=<demo-name>"; \
		echo "Available demos: laravel, symfony, yii, codeigniter, slim, legacy"; \
		exit 1; \
	fi
	@echo "🛑 Stopping $(DEMO) demo..."
	@cd $(DEMO) && docker-compose down
	@echo "✅ $(DEMO) demo stopped!"

# Generic install command
install:
	@if [ -z "$(DEMO)" ]; then \
		echo "❌ Error: Please specify a demo. Usage: make install DEMO=<demo-name>"; \
		echo "Available demos: laravel, symfony, yii, codeigniter, slim, legacy"; \
		exit 1; \
	fi
	@echo "📦 Installing dependencies for $(DEMO) demo..."
	@cd $(DEMO) && docker-compose exec app composer install || \
		(PORT_DEFAULT=$$([ "$(DEMO)" = "laravel" ] && echo "8001" || \
		                 [ "$(DEMO)" = "symfony" ] && echo "8002" || \
		                 [ "$(DEMO)" = "yii" ] && echo "8003" || \
		                 [ "$(DEMO)" = "codeigniter" ] && echo "8004" || \
		                 [ "$(DEMO)" = "slim" ] && echo "8005" || \
		                 [ "$(DEMO)" = "legacy" ] && echo "8006" || echo "8001"); \
		PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo $$PORT_DEFAULT); \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
		fi; \
		docker-compose up -d && sleep 2 && docker-compose exec app composer install)
	@echo "✅ $(DEMO) dependencies installed!"

# Generic logs command
logs:
	@if [ -z "$(DEMO)" ]; then \
		echo "❌ Error: Please specify a demo. Usage: make logs DEMO=<demo-name>"; \
		echo "Available demos: laravel, symfony, yii, codeigniter, slim, legacy"; \
		exit 1; \
	fi
	@cd $(DEMO) && docker-compose logs -f

# Generic test command
test:
	@if [ -z "$(DEMO)" ]; then \
		echo "❌ Error: Please specify a demo. Usage: make test DEMO=<demo-name>"; \
		echo "Available demos: laravel, symfony, yii, codeigniter, slim, legacy"; \
		exit 1; \
	fi
	@echo "🧪 Running tests for $(DEMO) demo..."
	@cd $(DEMO) && docker-compose exec app composer test || \
		(PORT_DEFAULT=$$([ "$(DEMO)" = "laravel" ] && echo "8001" || \
		                 [ "$(DEMO)" = "symfony" ] && echo "8002" || \
		                 [ "$(DEMO)" = "yii" ] && echo "8003" || \
		                 [ "$(DEMO)" = "codeigniter" ] && echo "8004" || \
		                 [ "$(DEMO)" = "slim" ] && echo "8005" || \
		                 [ "$(DEMO)" = "legacy" ] && echo "8006" || echo "8001"); \
		PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | head -1 || echo $$PORT_DEFAULT); \
		CONTAINER=$$(docker ps --format "{{.Names}}" --filter "publish=$$PORT" 2>/dev/null | head -1); \
		if [ -n "$$CONTAINER" ]; then \
			docker stop $$CONTAINER 2>/dev/null || true; \
			docker rm $$CONTAINER 2>/dev/null || true; \
		fi; \
		docker-compose up -d && sleep 2 && docker-compose exec app composer test)

# Generic shell command
shell:
	@if [ -z "$(DEMO)" ]; then \
		echo "❌ Error: Please specify a demo. Usage: make shell DEMO=<demo-name>"; \
		echo "Available demos: laravel, symfony, yii, codeigniter, slim, legacy"; \
		exit 1; \
	fi
	@cd $(DEMO) && docker-compose exec app sh

# Clean all demos
clean:
	@echo "🧹 Cleaning all demos..."
	@for demo in laravel symfony yii codeigniter slim legacy; do \
		echo "Cleaning $$demo..."; \
		cd $$demo && docker-compose down -v 2>/dev/null || true; \
		cd ..; \
	done
	@echo "✅ All demos cleaned!"

