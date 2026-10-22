.PHONY: help up down restart build rebuild logs ps shell migrate fresh seed tinker mysql flask-shell clean

help:
	@echo "ERP Docker - Available commands:"
	@echo "  make up        - Start all containers (detached)"
	@echo "  make down      - Stop & remove containers"
	@echo "  make restart   - Restart all containers"
	@echo "  make build     - Build images"
	@echo "  make rebuild   - Rebuild images from scratch"
	@echo "  make logs      - Follow all logs"
	@echo "  make ps        - Show container status"
	@echo "  make shell     - Shell into Laravel container"
	@echo "  make migrate   - Run Laravel migrations"
	@echo "  make fresh     - Migrate:fresh + seed"
	@echo "  make seed      - Run seeders"
	@echo "  make tinker    - Laravel tinker"
	@echo "  make mysql     - MySQL CLI (as root)"
	@echo "  make flask-shell - Shell into Flask container"
	@echo "  make clean     - DANGER: remove containers + volumes"

up:
	docker compose up -d
	@echo "✅ Laravel: http://127.0.0.1:$$(grep LARAVEL_PORT .env | cut -d= -f2)"
	@echo "✅ Flask:   http://127.0.0.1:$$(grep FLASK_PORT .env | cut -d= -f2)/weight-diff"

down:
	docker compose down

restart:
	docker compose restart

build:
	docker compose build

rebuild:
	docker compose build --no-cache

logs:
	docker compose logs -f

ps:
	docker compose ps

shell:
	docker compose exec erp_laravel sh

migrate:
	docker compose exec erp_laravel php artisan migrate

fresh:
	docker compose exec erp_laravel php artisan migrate:fresh --seed

seed:
	docker compose exec erp_laravel php artisan db:seed

tinker:
	docker compose exec erp_laravel php artisan tinker

mysql:
	docker compose exec erp_mysql mysql -uroot -p$$(grep MYSQL_ROOT_PASSWORD .env | cut -d= -f2)

flask-shell:
	docker compose exec erp_flask sh

clean:
	@echo "⚠️  This will DELETE all data (MySQL volume)."
	@read -p "Continue? [y/N] " ans && [ "$$ans" = "y" ] || exit 1
	docker compose down -v
