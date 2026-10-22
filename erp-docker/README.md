# ERP Docker

Setup Docker untuk proyek ERP (Laravel + Flask + MySQL).

## Prasyarat

- Docker 24+
- Docker Compose v2
- Port **8001**, **5000**, **3307** tersedia di host

## Port Mapping

| Service | Host Port | Container Port |
|---------|-----------|----------------|
| Laravel (via Nginx) | **8001** | 80 |
| Flask | **5000** | 5000 |
| MySQL | **3307** | 3306 |

> Port ini dipilih agar tidak bentrok dengan project lain (`dumas_*`, `kairos_*`).

## Cara Menjalankan

```bash
# 1. Copy environment template
cp .env.example .env
# edit .env, ganti password

# 2. Build & start
docker compose up -d --build

# 3. Tunggu MySQL healthy
docker compose ps

# 4. Migrate + seed (otomatis dari entrypoint? tidak, manual)
docker compose exec erp_laravel php artisan migrate:fresh --seed
```

Atau pakai Makefile:

```bash
make up
make fresh
```

## Akses

| Service | URL |
|---------|-----|
| Laravel SPKO | http://127.0.0.1:8001/workallocation |
| Laravel Report | http://127.0.0.1:8001/report/daily |
| Flask Selisih Berat | http://127.0.0.1:5000/weight-diff |
| MySQL | 127.0.0.1:3307 (user `erp_user`) |

## Arsitektur

```
        ┌──────────────────────────────────────────┐
        │          Docker network: erp_network     │
        │                                          │
        │  ┌────────────┐   ┌───────────────┐     │
Host    │  │ erp_nginx  │──▶│ erp_laravel   │     │
:8001 ──┼─▶│ (nginx)    │   │ (php-fpm 8.3) │     │
        │  └────────────┘   └───────┬───────┘     │
        │                            │             │
Host    │                     ┌──────▼──────┐      │
:5000 ──┼────────────────────▶│  erp_mysql  │      │
        │   ┌──────────────┐   │  (mysql 8)  │     │
        │   │ erp_flask    │──▶│             │     │
        │   │ (python 3.12)│   └─────────────┘     │
        │   └──────────────┘                       │
        └──────────────────────────────────────────┘
```

## Kredensial

Semua kredensial diatur **satu tempat** di `.env`:

- `MYSQL_USER` / `MYSQL_PASSWORD` → dipakai oleh Laravel & Flask
- `MYSQL_ROOT_PASSWORD` → hanya untuk admin MySQL CLI

Laravel **tidak** menyimpan password DB di `erp-app/.env` (kosong), karena **diinject** dari environment variable Docker.

## Troubleshooting

Lihat log:
```bash
docker compose logs -f erp_laravel
docker compose logs -f erp_nginx
docker compose logs -f erp_mysql
docker compose logs -f erp_flask
```

Reset total:
```bash
make clean    # hapus container + volume (data hilang)
make up
make fresh
```

## Backup Database

```bash
docker compose exec erp_mysql sh -c 'exec mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" database_erp' > backup_$(date +%Y%m%d).sql
```

## Restore Database

```bash
cat backup_20250101.sql | docker compose exec -T erp_mysql sh -c 'exec mysql -uroot -p"$MYSQL_ROOT_PASSWORD" database_erp'
```
