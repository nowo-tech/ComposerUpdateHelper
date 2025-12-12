# Makefile for Demo Projects
# Simplifies Docker Compose commands for each demo

.PHONY: help laravel symfony yii codeigniter slim legacy all up down logs test shell clean

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
	@echo "  up <demo>         Start a specific demo (e.g., make up laravel)"
	@echo "  down <demo>       Stop a specific demo (e.g., make down laravel)"
	@echo "  logs <demo>       Show logs of a specific demo"
	@echo "  test <demo>       Run tests of a specific demo"
	@echo "  shell <demo>      Open shell in a specific demo container"
	@echo "  clean             Stop and remove all demo containers and volumes"
	@echo ""

# Start Laravel demo
laravel:
	@echo "🚀 Starting Laravel 12 demo..."
	@cd laravel && \
		if [ ! -f .env ]; then cp .env.example .env; fi && \
		docker-compose up -d && \
		echo "✅ Laravel demo running at http://localhost:$$(grep PORT .env | cut -d= -f2 || echo 8001)"

# Start Symfony demo
symfony:
	@echo "🎯 Starting Symfony 8.0 demo..."
	@cd symfony && \
		if [ ! -f .env ]; then cp .env.example .env; fi && \
		docker-compose up -d && \
		echo "✅ Symfony demo running at http://localhost:$$(grep PORT .env | cut -d= -f2 || echo 8002)"

# Start Yii demo
yii:
	@echo "🎨 Starting Yii 2 demo..."
	@cd yii && \
		if [ ! -f .env ]; then cp .env.example .env; fi && \
		docker-compose up -d && \
		echo "✅ Yii demo running at http://localhost:$$(grep PORT .env | cut -d= -f2 || echo 8003)"

# Start CodeIgniter demo
codeigniter:
	@echo "🔥 Starting CodeIgniter 5 demo..."
	@cd codeigniter && \
		if [ ! -f .env ]; then cp .env.example .env; fi && \
		docker-compose up -d && \
		echo "✅ CodeIgniter demo running at http://localhost:$$(grep PORT .env | cut -d= -f2 || echo 8004)"

# Start Slim demo
slim:
	@echo "⚡ Starting Slim 5 demo..."
	@cd slim && \
		if [ ! -f .env ]; then cp .env.example .env; fi && \
		docker-compose up -d && \
		echo "✅ Slim demo running at http://localhost:$$(grep PORT .env | cut -d= -f2 || echo 8005)"

# Start Legacy demo
legacy:
	@echo "🔧 Starting Legacy Laravel 5.8 demo..."
	@cd legacy && \
		if [ ! -f .env ]; then cp .env.example .env; fi && \
		docker-compose up -d && \
		echo "✅ Legacy demo running at http://localhost:$$(grep PORT .env | cut -d= -f2 || echo 8006)"

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
	@cd $(DEMO) && docker-compose exec app composer test

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

