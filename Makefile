.DEFAULT_GOAL := help

APP_PORT ?= 8000
STACK_APP_PORT ?= 8001
PHP ?= php
COMPOSER ?= composer
NPM ?= npm
VITE_PORT ?= 5173
VITE_HOST ?= 0.0.0.0
PID_DIR ?= .pids
VITE_PID_FILE ?= $(PID_DIR)/vite.pid
CONTAINER_WEB ?= pitstop-web
CONTAINER_QUEUE ?= pitstop-queue
CONTAINER_LOGS ?= pitstop-logs
DOCKER_RUN ?= docker run --rm -v "$$(pwd)":/app -w /app composer:2
DOCKER_COMPOSER ?= $(DOCKER_RUN) sh -lc

.PHONY: help doctor deps composer-install npm-install env setup setup-docker \
	dev serve queue logs build test test-docker lint lint-docker \
	format format-check format-docker format-check-docker \
	lint-js lint-css lint-docs format-web format-check-web quality quality-docker \
	migrate fresh seed clean \
	stack-up stack-down stack-status stack-logs

help: ## Show all available commands with descriptions
	@echo "PitStop Make targets"
	@echo
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z0-9_.-]+:.*##/ {printf "  %-24s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

doctor: ## Check required local tooling (php/composer/node/npm/docker)
	@echo "Checking local tools..."
	@command -v $(PHP) >/dev/null 2>&1 && echo "✓ php" || echo "✗ php"
	@command -v $(COMPOSER) >/dev/null 2>&1 && echo "✓ composer" || echo "✗ composer"
	@command -v node >/dev/null 2>&1 && echo "✓ node" || echo "✗ node"
	@command -v $(NPM) >/dev/null 2>&1 && echo "✓ npm" || echo "✗ npm"
	@command -v docker >/dev/null 2>&1 && echo "✓ docker" || echo "✗ docker"

deps: composer-install npm-install ## Install backend and frontend dependencies

composer-install: ## Install PHP dependencies (host composer)
	$(COMPOSER) install

npm-install: ## Install Node dependencies from lockfile
	$(NPM) ci

env: ## Ensure .env exists (copy from .env.example if missing)
	@test -f .env || cp .env.example .env

setup: ## Full local bootstrap (host tooling): install, key generate, migrate, build
	$(COMPOSER) setup

setup-docker: env ## Full bootstrap using Dockerized composer + local npm
	$(DOCKER_COMPOSER) 'git config --global --add safe.directory /app >/dev/null 2>&1; composer install'
	$(DOCKER_COMPOSER) 'git config --global --add safe.directory /app >/dev/null 2>&1; php artisan key:generate'
	$(DOCKER_COMPOSER) 'git config --global --add safe.directory /app >/dev/null 2>&1; php artisan migrate --force'
	$(NPM) ci
	$(NPM) run build

dev: ## Run the full development stack (app, queue, logs, vite)
	$(COMPOSER) dev

serve: ## Run Laravel dev server on APP_PORT (host php)
	$(PHP) artisan serve --host=0.0.0.0 --port=$(APP_PORT)

queue: ## Run queue listener
	$(PHP) artisan queue:listen --tries=1

logs: ## Tail Laravel logs with pail
	$(PHP) artisan pail --timeout=0

build: ## Build frontend assets
	$(NPM) run build

test: ## Run Laravel tests (host php/composer)
	$(COMPOSER) test

test-docker: ## Run Laravel tests in Dockerized composer image
	$(DOCKER_COMPOSER) 'git config --global --add safe.directory /app >/dev/null 2>&1; php artisan test'

lint: ## Run PHP lint checks (Pint --test)
	$(COMPOSER) lint

lint-docker: ## Run PHP lint checks via Dockerized composer
	$(DOCKER_COMPOSER) 'git config --global --add safe.directory /app >/dev/null 2>&1; composer lint'

format: ## Auto-format PHP code with Pint
	$(COMPOSER) format

format-check: ## Validate PHP formatting with Pint
	$(COMPOSER) format:check

format-docker: ## Auto-format PHP code via Dockerized composer
	$(DOCKER_COMPOSER) 'git config --global --add safe.directory /app >/dev/null 2>&1; composer format'

format-check-docker: ## Validate PHP formatting via Dockerized composer
	$(DOCKER_COMPOSER) 'git config --global --add safe.directory /app >/dev/null 2>&1; composer format:check'

lint-js: ## Run JavaScript lint checks
	$(NPM) run lint:js

lint-css: ## Run CSS lint checks
	$(NPM) run lint:css

lint-docs: ## Run markdown/docs lint checks
	$(NPM) run lint:docs

format-web: ## Auto-format docs/config/frontend scope with Prettier
	$(NPM) run format

format-check-web: ## Validate docs/config/frontend formatting with Prettier
	$(NPM) run format:check

quality: lint lint-js lint-css lint-docs format-check-web test ## Full local quality gate (host PHP + npm)

quality-docker: lint-docker lint-js lint-css lint-docs format-check-web test-docker ## Full quality gate with Dockerized PHP

migrate: ## Run database migrations
	$(PHP) artisan migrate --force

fresh: ## Recreate database and seed
	$(PHP) artisan migrate:fresh --seed

seed: ## Seed database
	$(PHP) artisan db:seed

clean: ## Remove build artifacts and caches
	rm -rf public/build
	$(PHP) artisan optimize:clear || true

stack-up: env ## Launch full stack in background (web, queue, logs, vite)
	@$(MAKE) stack-down >/dev/null
	@mkdir -p $(PID_DIR) storage/logs
	@if ss -ltn | grep -q ":$(STACK_APP_PORT) "; then \
		echo "Port $(STACK_APP_PORT) is already in use. Override with: make stack-up STACK_APP_PORT=<port>"; \
		exit 1; \
	fi
	@if ss -ltn | grep -q ":$(VITE_PORT) "; then \
		echo "Port $(VITE_PORT) is already in use. Override with: make stack-up VITE_PORT=<port>"; \
		exit 1; \
	fi
	@docker run -d --name $(CONTAINER_WEB) -v "$$(pwd)":/app -w /app -p $(STACK_APP_PORT):$(STACK_APP_PORT) composer:2 sh -lc 'git config --global --add safe.directory /app >/dev/null 2>&1; php artisan serve --host=0.0.0.0 --port=$(STACK_APP_PORT)' >/dev/null
	@docker run -d --name $(CONTAINER_QUEUE) -v "$$(pwd)":/app -w /app composer:2 sh -lc 'git config --global --add safe.directory /app >/dev/null 2>&1; php artisan queue:listen --tries=1' >/dev/null
	@docker run -d --name $(CONTAINER_LOGS) -v "$$(pwd)":/app -w /app composer:2 sh -lc 'touch storage/logs/laravel.log && tail -n 0 -f storage/logs/laravel.log' >/dev/null
	@nohup $(NPM) run dev -- --host $(VITE_HOST) --port $(VITE_PORT) --strictPort > storage/logs/vite-dev.log 2>&1 & echo $$! > "$(VITE_PID_FILE)"
	@echo "Started Vite (PID $$(cat $(VITE_PID_FILE)))."
	@HOST_IP=$$(hostname -I | awk '{print $$1}'); \
		echo "Laravel local:  http://127.0.0.1:$(STACK_APP_PORT)"; \
		echo "Laravel remote: http://$$HOST_IP:$(STACK_APP_PORT)"; \
		echo "Vite local:     http://127.0.0.1:$(VITE_PORT)"; \
		echo "Vite remote:    http://$$HOST_IP:$(VITE_PORT) (@vite/client)"

stack-down: ## Stop all project Laravel/Node background services (containers + host processes)
	@docker rm -f $(CONTAINER_WEB) $(CONTAINER_QUEUE) $(CONTAINER_LOGS) >/dev/null 2>&1 || true
	@for cid in $$(docker ps -q --filter volume="$$(pwd)"); do docker rm -f $$cid >/dev/null 2>&1 || true; done
	@if [ -f "$(VITE_PID_FILE)" ]; then \
		if kill -0 $$(cat "$(VITE_PID_FILE)") >/dev/null 2>&1; then kill $$(cat "$(VITE_PID_FILE)") >/dev/null 2>&1 || true; fi; \
		rm -f "$(VITE_PID_FILE)"; \
	fi
	@pkill -f "$$(pwd).*npm run dev" >/dev/null 2>&1 || true
	@pkill -f "$$(pwd).*vite" >/dev/null 2>&1 || true
	@pkill -f "$$(pwd).*artisan serve" >/dev/null 2>&1 || true
	@pkill -f "$$(pwd).*artisan queue:listen" >/dev/null 2>&1 || true
	@pkill -f "$$(pwd).*artisan pail" >/dev/null 2>&1 || true
	@rm -rf $(PID_DIR)
	@echo "Background stack stopped."

stack-status: ## Show status of background web, queue, logs, and Vite processes
	@echo "=== Laravel containers ==="
	@docker ps --filter name=$(CONTAINER_WEB) --filter name=$(CONTAINER_QUEUE) --filter name=$(CONTAINER_LOGS) --format 'table {{.Names}}\t{{.Status}}\t{{.Ports}}'
	@echo "=== Vite process ==="
	@if [ -f "$(VITE_PID_FILE)" ] && kill -0 $$(cat "$(VITE_PID_FILE)") >/dev/null 2>&1; then \
		echo "vite: running (PID $$(cat $(VITE_PID_FILE)))"; \
	else \
		echo "vite: not running"; \
	fi

stack-logs: ## Tail logs for background web/queue/log containers and Vite dev server
	@echo "--- $(CONTAINER_WEB) logs (last 50) ---"
	@docker logs --tail 50 $(CONTAINER_WEB) 2>/dev/null || echo "$(CONTAINER_WEB) not running"
	@echo "--- $(CONTAINER_QUEUE) logs (last 50) ---"
	@docker logs --tail 50 $(CONTAINER_QUEUE) 2>/dev/null || echo "$(CONTAINER_QUEUE) not running"
	@echo "--- $(CONTAINER_LOGS) logs (last 50) ---"
	@docker logs --tail 50 $(CONTAINER_LOGS) 2>/dev/null || echo "$(CONTAINER_LOGS) not running"
	@echo "--- Vite logs (last 50) ---"
	@tail -n 50 storage/logs/vite-dev.log 2>/dev/null || echo "vite log not found"
