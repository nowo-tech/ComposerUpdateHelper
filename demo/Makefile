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
	@echo "  symfony           Start Symfony 8.0 demo (port 8002)"
	@echo "  yii               Start Yii 2 demo (port 8003)"
	@echo "  codeigniter       Start CodeIgniter 5 demo (port 8004)"
	@echo "  slim              Start Slim 5 demo (port 8005)"
	@echo "  legacy            Start Legacy Laravel 5.8 demo (port 8006)"
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
		docker-compose up -d && \
		echo "✅ Laravel demo running at http://localhost:$$(grep PORT .env | cut -d= -f2 || echo 8001)"

# Stop Laravel demo
laravel-down:
	@echo "🛑 Stopping Laravel demo..."
	@cd laravel && docker-compose down
	@echo "✅ Laravel demo stopped!"

# Install Laravel dependencies
laravel-install:
	@echo "📦 Installing Laravel dependencies..."
	@cd laravel && docker-compose exec app composer install || \
		(docker-compose up -d && sleep 2 && docker-compose exec app composer install)
	@echo "✅ Laravel dependencies installed!"

# Run Laravel tests
laravel-test:
	@echo "🧪 Running Laravel tests..."
	@cd laravel && docker-compose exec app composer test || \
		(docker-compose up -d && sleep 2 && docker-compose exec app composer test)

# Start Symfony demo
symfony:
	@echo "🎯 Starting Symfony 8.0 demo..."
	@cd symfony && \
		if [ ! -f .env ]; then cp .env.example .env; fi && \
		docker-compose up -d && \
		echo "✅ Symfony demo running at http://localhost:$$(grep PORT .env | cut -d= -f2 || echo 8002)"

# Stop Symfony demo
symfony-down:
	@echo "🛑 Stopping Symfony demo..."
	@cd symfony && docker-compose down
	@echo "✅ Symfony demo stopped!"

# Install Symfony dependencies
symfony-install:
	@echo "📦 Installing Symfony dependencies..."
	@cd symfony && docker-compose exec app composer install || \
		(docker-compose up -d && sleep 2 && docker-compose exec app composer install)
	@echo "✅ Symfony dependencies installed!"

# Run Symfony tests
symfony-test:
	@echo "🧪 Running Symfony tests..."
	@cd symfony && docker-compose exec app composer test || \
		(docker-compose up -d && sleep 2 && docker-compose exec app composer test)

# Start Yii demo
yii:
	@echo "🎨 Starting Yii 2 demo..."
	@cd yii && \
		if [ ! -f .env ]; then cp .env.example .env; fi && \
		docker-compose up -d && \
		echo "✅ Yii demo running at http://localhost:$$(grep PORT .env | cut -d= -f2 || echo 8003)"

# Stop Yii demo
yii-down:
	@echo "🛑 Stopping Yii demo..."
	@cd yii && docker-compose down
	@echo "✅ Yii demo stopped!"

# Install Yii dependencies
yii-install:
	@echo "📦 Installing Yii dependencies..."
	@cd yii && docker-compose exec app composer install || \
		(docker-compose up -d && sleep 2 && docker-compose exec app composer install)
	@echo "✅ Yii dependencies installed!"

# Run Yii tests
yii-test:
	@echo "🧪 Running Yii tests..."
	@cd yii && docker-compose exec app composer test || \
		(docker-compose up -d && sleep 2 && docker-compose exec app composer test)

# Start CodeIgniter demo
codeigniter:
	@echo "🔥 Starting CodeIgniter 5 demo..."
	@cd codeigniter && \
		if [ ! -f .env ]; then cp .env.example .env; fi && \
		docker-compose up -d && \
		echo "✅ CodeIgniter demo running at http://localhost:$$(grep PORT .env | cut -d= -f2 || echo 8004)"

# Stop CodeIgniter demo
codeigniter-down:
	@echo "🛑 Stopping CodeIgniter demo..."
	@cd codeigniter && docker-compose down
	@echo "✅ CodeIgniter demo stopped!"

# Install CodeIgniter dependencies
codeigniter-install:
	@echo "📦 Installing CodeIgniter dependencies..."
	@cd codeigniter && docker-compose exec app composer install || \
		(docker-compose up -d && sleep 2 && docker-compose exec app composer install)
	@echo "✅ CodeIgniter dependencies installed!"

# Run CodeIgniter tests
codeigniter-test:
	@echo "🧪 Running CodeIgniter tests..."
	@cd codeigniter && docker-compose exec app composer test || \
		(docker-compose up -d && sleep 2 && docker-compose exec app composer test)

# Start Slim demo
slim:
	@echo "⚡ Starting Slim 5 demo..."
	@cd slim && \
		if [ ! -f .env ]; then cp .env.example .env; fi && \
		docker-compose up -d && \
		echo "✅ Slim demo running at http://localhost:$$(grep PORT .env | cut -d= -f2 || echo 8005)"

# Stop Slim demo
slim-down:
	@echo "🛑 Stopping Slim demo..."
	@cd slim && docker-compose down
	@echo "✅ Slim demo stopped!"

# Install Slim dependencies
slim-install:
	@echo "📦 Installing Slim dependencies..."
	@cd slim && docker-compose exec app composer install || \
		(docker-compose up -d && sleep 2 && docker-compose exec app composer install)
	@echo "✅ Slim dependencies installed!"

# Run Slim tests
slim-test:
	@echo "🧪 Running Slim tests..."
	@cd slim && docker-compose exec app composer test || \
		(docker-compose up -d && sleep 2 && docker-compose exec app composer test)

# Start Legacy demo
legacy:
	@echo "🔧 Starting Legacy Laravel 5.8 demo..."
	@cd legacy && \
		if [ ! -f .env ]; then cp .env.example .env; fi && \
		docker-compose up -d && \
		echo "✅ Legacy demo running at http://localhost:$$(grep PORT .env | cut -d= -f2 || echo 8006)"

# Stop Legacy demo
legacy-down:
	@echo "🛑 Stopping Legacy demo..."
	@cd legacy && docker-compose down
	@echo "✅ Legacy demo stopped!"

# Install Legacy dependencies
legacy-install:
	@echo "📦 Installing Legacy dependencies..."
	@cd legacy && docker-compose exec app composer install || \
		(docker-compose up -d && sleep 2 && docker-compose exec app composer install)
	@echo "✅ Legacy dependencies installed!"

# Run Legacy tests
legacy-test:
	@echo "🧪 Running Legacy tests..."
	@cd legacy && docker-compose exec app composer test || \
		(docker-compose up -d && sleep 2 && docker-compose exec app composer test)

# Start all demos
all: laravel symfony yii codeigniter slim legacy
	@echo ""
	@echo "✅ All demos started!"
	@echo ""
	@echo "Access demos at:"
	@echo "  - Laravel:      http://localhost:8001"
	@echo "  - Symfony:     http://localhost:8002"
	@echo "  - Yii:         http://localhost:8003"
	@echo "  - CodeIgniter: http://localhost:8004"
	@echo "  - Slim:        http://localhost:8005"
	@echo "  - Legacy:      http://localhost:8006"

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
		(docker-compose up -d && sleep 2 && docker-compose exec app composer install)
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
		(docker-compose up -d && sleep 2 && docker-compose exec app composer test)

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

