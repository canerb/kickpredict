.PHONY: up down build restart logs app-logs web-logs db-logs shell clean

# Start all services
up:
	docker-compose up -d

# Stop all services
down:
	docker-compose down

# Build and start services
build:
	docker-compose down
	docker-compose build --no-cache
	docker-compose up -d

# Restart services
restart:
	docker-compose restart

# View all logs
logs:
	docker-compose logs -f

# View app container logs
app-logs:
	docker logs -f soccer_ai_app

# View webserver logs
web-logs:
	docker logs -f soccer_ai_webserver

# View database logs
db-logs:
	docker logs -f soccer_ai_db

# Shell into app container
shell:
	docker exec -it soccer_ai_app bash

# Clean up containers and images
clean:
	docker-compose down
	docker system prune -f

# Run migrations
migrate:
	docker exec -it soccer_ai_app php artisan migrate

# Generate application key
key:
	docker exec -it soccer_ai_app php artisan key:generate

# Clear caches
clear:
	docker exec -it soccer_ai_app php artisan optimize:clear 