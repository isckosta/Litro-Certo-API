.PHONY: help up down restart logs shell composer migrate seed test pint swagger postman queue install build clean

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up: ## Start all containers
	docker-compose up -d

down: ## Stop all containers
	docker-compose down

restart: ## Restart all containers
	docker-compose restart

logs: ## Show container logs
	docker-compose logs -f

shell: ## Access app container shell
	docker-compose exec app bash

composer: ## Run composer install
	docker-compose exec app composer install

migrate: ## Run database migrations
	docker-compose exec app php artisan migrate

migrate-fresh: ## Fresh migrations with seed
	docker-compose exec app php artisan migrate:fresh --seed

seed: ## Run database seeders
	docker-compose exec app php artisan db:seed

test: ## Run PHPUnit tests
	docker-compose exec app php artisan test

pint: ## Run Laravel Pint (code style)
	docker-compose exec app ./vendor/bin/pint

swagger: ## Generate Swagger documentation
	docker-compose exec app php artisan l5-swagger:generate

postman: ## Export Postman collection
	@echo "Postman collection available at: storage/api-docs/postman_collection.json"
	docker-compose exec app php artisan swagger:export:postman

queue: ## Start queue worker
	docker-compose exec app php artisan queue:work --tries=3

queue-listen: ## Listen to queue (dev mode)
	docker-compose exec app php artisan queue:listen

install: build composer migrate seed swagger ## Full installation
	@echo "Installation complete! Access: http://localhost:8000"
	@echo "Swagger UI: http://localhost:8000/api/documentation"
	@echo "RabbitMQ Management: http://localhost:15672 (user: litrocerto, pass: secret)"

build: ## Build and start containers
	docker-compose up -d --build

clean: ## Clean all containers and volumes
	docker-compose down -v
	rm -rf vendor node_modules

cache-clear: ## Clear all Laravel caches
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear

optimize: ## Optimize Laravel
	docker-compose exec app php artisan config:cache
	docker-compose exec app php artisan route:cache
	docker-compose exec app php artisan view:cache

key-generate: ## Generate application key
	docker-compose exec app php artisan key:generate

jwt-secret: ## Generate JWT secret
	docker-compose exec app php artisan jwt:secret

permissions: ## Fix storage permissions
	docker-compose exec app chmod -R 775 storage bootstrap/cache
	docker-compose exec app chown -R litrocerto:www-data storage bootstrap/cache
