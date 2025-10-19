.PHONY: help up down restart logs shell composer migrate seed test pint swagger postman queue install install-fresh build clean

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up: ## Start all containers
	docker compose up -d

down: ## Stop all containers
	docker compose down

restart: ## Restart all containers
	docker compose restart

logs: ## Show container logs
	docker compose logs -f

shell: ## Access app container shell
	docker compose exec app bash

composer: ## Run composer install/update
	docker compose exec app composer update
	docker compose exec app composer install

migrate: ## Run database migrations
	docker compose exec app php artisan migrate

migrate-fresh: ## Fresh migrations with seed
	docker compose exec app php artisan migrate:fresh --seed

seed: ## Run database seeders
	docker compose exec app php artisan db:seed

test: ## Run PHPUnit tests
	docker compose exec app php artisan test

pint: ## Run Laravel Pint (code style)
	docker compose exec app ./vendor/bin/pint

swagger: ## Generate Swagger documentation
	docker compose exec app php artisan l5-swagger:generate

postman: ## Export Postman collection
	@echo "Postman collection available at: storage/api-docs/postman_collection.json"
	docker compose exec app php artisan swagger:export:postman

queue: ## Start queue worker
	docker compose exec app php artisan queue:work --tries=3

queue-listen: ## Listen to queue (dev mode)
	docker compose exec app php artisan queue:listen

install: build setup-env composer key-generate jwt-secret publish-vendors migrate seed swagger ## Full installation
	@echo "========================================="
	@echo "Installation complete!"
	@echo "========================================="
	@echo "API: http://localhost:8000/api/v1"
	@echo "Swagger UI: http://localhost:8000/api/documentation"
	@echo ""
	@echo "Monitoring & Observability:"
	@echo "- Grafana: http://localhost:3000 (admin/admin123) - Dashboards + Logs"
	@echo "- Prometheus: http://localhost:9090 - Metrics"
	@echo "- Loki: http://localhost:3100 - Logs API (use via Grafana)"
	@echo ""
	@echo "Database & Cache:"
	@echo "- pgAdmin: http://localhost:5050 (admin@litrocerto.com.br/admin123)"
	@echo "- Redis Insight: http://localhost:5540"
	@echo ""
	@echo "Development Tools:"
	@echo "- RabbitMQ: http://localhost:15672 (litrocerto/secret)"
	@echo "- Mailhog: http://localhost:8025"
	@echo "- MinIO: http://localhost:9001 (litrocerto/litrocerto123)"
	@echo "- Portainer: https://localhost:9443"
	@echo ""
	@echo "Default users:"
	@echo "- admin@litrocerto.com.br / admin123"
	@echo "- usuario@teste.com.br / usuario123"

install-fresh: clean build setup-env composer key-generate jwt-secret publish-vendors migrate-fresh swagger ## Fresh installation (drops all data)
	@echo "========================================="
	@echo "Fresh installation complete!"
	@echo "========================================="
	@echo "API: http://localhost:8000/api/v1"
	@echo "Swagger UI: http://localhost:8000/api/documentation"
	@echo ""
	@echo "Monitoring & Observability:"
	@echo "- Grafana: http://localhost:3000 (admin/admin123) - Dashboards + Logs"
	@echo "- Prometheus: http://localhost:9090 - Metrics"
	@echo "- Loki: http://localhost:3100 - Logs API (use via Grafana)"
	@echo ""
	@echo "Database & Cache:"
	@echo "- pgAdmin: http://localhost:5050 (admin@litrocerto.com.br/admin123)"
	@echo "- Redis Insight: http://localhost:5540"
	@echo ""
	@echo "Development Tools:"
	@echo "- RabbitMQ: http://localhost:15672 (litrocerto/secret)"
	@echo "- Mailhog: http://localhost:8025"
	@echo "- MinIO: http://localhost:9001 (litrocerto/litrocerto123)"
	@echo "- Portainer: https://localhost:9443"
	@echo ""
	@echo "Default users:"
	@echo "- admin@litrocerto.com.br / admin123"
	@echo "- usuario@teste.com.br / usuario123"

setup-env: ## Copy .env.example to .env if not exists
	@if [ ! -f .env ]; then cp .env.example .env && echo ".env file created"; else echo ".env already exists"; fi

build: ## Build and start containers
	docker compose up -d --build

clean: ## Clean all containers and volumes
	docker compose down -v
	rm -rf vendor node_modules

cache-clear: ## Clear all Laravel caches
	docker compose exec app php artisan cache:clear
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	docker compose exec app php artisan view:clear

optimize: ## Optimize Laravel
	docker compose exec app php artisan config:cache
	docker compose exec app php artisan route:cache
	docker compose exec app php artisan view:cache

key-generate: ## Generate application key
	docker compose exec app php artisan key:generate

jwt-secret: ## Generate JWT secret
	docker compose exec app php artisan jwt:secret

permissions: ## Fix storage permissions
	docker compose exec app chmod -R 775 storage bootstrap/cache
	docker compose exec app chown -R litrocerto:www-data storage bootstrap/cache

publish-vendors: ## Publish vendor configs
	docker compose exec app php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider" --force
	docker compose exec app php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --force
	docker compose exec app php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider" --force
