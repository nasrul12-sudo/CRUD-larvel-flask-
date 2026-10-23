# ERP SPKO & NTHKO System

Aplikasi ERP untuk mengelola **Surat Perintah Kerja Operator (SPKO)** dan **Nota Terima Kerja (NTHKO)**, dilengkapi laporan selisih berat berbasis arsitektur *microservice* menggunakan Laravel dan Flask.

## Daftar Isi

- [1. Gambaran Umum](#1-gambaran-umum)
- [2. Arsitektur dan Teknologi](#2-arsitektur-dan-teknologi)
- [3. Persyaratan Sistem](#3-persyaratan-sistem)
- [4. Instalasi dan Menjalankan Aplikasi](#4-instalasi-dan-menjalankan-aplikasi)
- [5. Mengakses Container](#5-mengakses-container)
- [6. Mengakses Database MySQL](#6-mengakses-database-mysql)
- [7. Struktur Database](#7-struktur-database)
- [8. Alur Kerja Aplikasi](#8-alur-kerja-aplikasi)
- [9. Troubleshooting](#9-troubleshooting)

## 1. Gambaran Umum

ERP SPKO & NTHKO System dirancang untuk mengelola proses kerja operator, mulai dari perencanaan pekerjaan, pencatatan hasil pekerjaan, hingga pelaporan selisih berat.

Fitur utama:

- **Manajemen SPKO:** pencatatan perintah kerja, operator, produk, kuantitas, dan berat rencana.
- **Manajemen NTHKO:** pencatatan realisasi pekerjaan dan berat aktual berdasarkan SPKO.
- **Laporan selisih berat:** perbandingan berat aktual dengan berat rencana menggunakan Flask.
- **Database terpusat:** Laravel dan Flask menggunakan database MySQL yang sama.
- **Containerization:** seluruh layanan dijalankan menggunakan Docker Compose agar lingkungan aplikasi lebih konsisten.

## 2. Arsitektur dan Teknologi

### 2.1. Teknologi yang Digunakan

| Komponen | Teknologi | Fungsi |
|---|---|---|
| Framework utama | Laravel 13 | Manajemen SPKO, NTHKO, dan antarmuka aplikasi |
| Runtime PHP | PHP 8.4-FPM | Menjalankan aplikasi Laravel |
| Modul pelaporan | Flask 3.1 | Menyajikan laporan selisih berat |
| Runtime Python | Python 3.12 | Menjalankan layanan Flask |
| ORM Python | SQLAlchemy | Mengakses database dari Flask |
| Database | MySQL 8.0 | Penyimpanan data terpusat |
| Web server | Nginx 1.27 | Menerima dan meneruskan permintaan HTTP |
| Containerization | Docker & Docker Compose | Menjalankan dan mengelola layanan |

### 2.2. Diagram Arsitektur

```text
                         +------------------+
                         |     Browser      |
                         +--------+---------+
                                  |
                   +--------------+--------------+
                   |                             |
                   v                             v
          +------------------+          +------------------+
          | Nginx :8001      |          | Flask :5000      |
          | Laravel routing  |          | Weight Report    |
          +--------+---------+          +--------+---------+
                   |                             |
                   v                             |
          +------------------+                   |
          | Laravel / PHP-FPM|                   |
          | Internal :9000   |                   |
          +--------+---------+                   |
                   |                             |
                   +-------------+---------------+
                                 |
                                 v
                       +------------------+
                       | MySQL :3307      |
                       | database_erp     |
                       +------------------+
```

**Catatan:** Port `9000` merupakan port internal PHP-FPM, bukan endpoint HTTP yang biasanya diakses langsung melalui browser. Port `3307` adalah port database yang dipublikasikan ke host.

Seluruh layanan berada dalam jaringan Docker terisolasi bernama `erp_network`. Laravel menangani transaksi SPKO/NTHKO, sedangkan Flask membaca data untuk laporan selisih berat.

## 3. Persyaratan Sistem

Pastikan perangkat telah memiliki:

- Docker Engine versi 20.10 atau lebih baru.
- Docker Compose V2.
- Terminal yang mendukung perintah Docker.
- Port host `8001`, `5000`, dan `3307` tersedia.

Periksa instalasi Docker:

```bash
docker --version
docker compose version
```

Periksa apakah port yang diperlukan sedang digunakan oleh aplikasi lain sebelum menjalankan container.

## 4. Instalasi dan Menjalankan Aplikasi

### 4.1. Masuk ke Direktori Proyek

```bash
cd ~/Project/interview/erp-docker
```

Pastikan direktori tersebut berisi file `compose.yaml` atau `docker-compose.yml`.

### 4.2. Siapkan Environment

Salin file konfigurasi contoh:

```bash
cp .env.example .env
```

Buka file `.env` dan sesuaikan konfigurasi apabila diperlukan:

```bash
nano .env
```

Periksa terutama konfigurasi database, username, password, dan port. Jangan membagikan file `.env` yang berisi kredensial asli ke repositori publik.

### 4.3. Build Image Docker

```bash
docker compose build
```

### 4.4. Jalankan Seluruh Container

```bash
docker compose up -d
```

Periksa status layanan:

```bash
docker compose ps
```

Tunggu hingga MySQL berstatus `healthy` sebelum menjalankan migrasi. Status ini bergantung pada konfigurasi health check di Compose.

Untuk memantau proses startup:

```bash
docker compose logs -f erp_mysql
```

Tekan `Ctrl+C` untuk menghentikan pemantauan log tanpa mematikan container.

### 4.5. Migrasi dan Seeder Database

**Peringatan:** Perintah `migrate:fresh --seed` menghapus seluruh tabel yang dikelola oleh migrasi Laravel, lalu membuatnya kembali dan menjalankan seeder. Gunakan hanya untuk inisialisasi atau lingkungan pengujian yang datanya boleh dihapus.

```bash
docker compose exec erp_laravel php artisan migrate:fresh --seed
```

Jika database sudah berisi data penting dan tidak boleh dihapus, periksa status migrasi terlebih dahulu:

```bash
docker compose exec erp_laravel php artisan migrate:status
```

Jangan menjalankan `migrate:fresh` pada database produksi tanpa memastikan bahwa penghapusan data memang dikehendaki.

### 4.6. Akses Aplikasi

Setelah semua layanan berjalan, buka alamat berikut:

| Layanan | URL | Fungsi |
|---|---|---|
| Laravel | [http://127.0.0.1:8001](http://127.0.0.1:8001) | Manajemen SPKO dan NTHKO |
| Flask | [http://127.0.0.1:5000](http://127.0.0.1:5000) | Laporan selisih berat |

URL tersebut menggunakan alamat loopback sehingga ditujukan untuk akses dari komputer host yang menjalankan Docker.

## 5. Mengakses Container

Jalankan perintah berikut dari direktori `erp-docker`.

### 5.1. Container Laravel

Masuk ke shell container:

```bash
docker compose exec erp_laravel sh
```

Melihat daftar route Laravel:

```bash
docker compose exec erp_laravel php artisan route:list
```

Membersihkan cache view:

```bash
docker compose exec erp_laravel php artisan view:clear
```

### 5.2. Container Flask

Masuk ke shell container:

```bash
docker compose exec erp_flask sh
```

Melihat log Flask:

```bash
docker compose logs -f erp_flask
```

### 5.3. Melihat Log Layanan

Log Nginx:

```bash
docker compose logs -f erp_nginx
```

Log MySQL:

```bash
docker compose logs -f erp_mysql
```

Log seluruh layanan:

```bash
docker compose logs -f
```

### 5.4. Restart dan Menghentikan Container

Restart layanan Laravel:

```bash
docker compose restart erp_laravel
```

Menghentikan dan menghapus container serta jaringan yang dikelola Compose:

```bash
docker compose down
```

Menjalankan kembali layanan:

```bash
docker compose up -d
```

Perintah `docker compose down` tidak secara default menghapus named volume. Namun, hindari opsi `-v` jika data database harus dipertahankan.

## 6. Mengakses Database MySQL

Database menggunakan nama `database_erp` dan dipublikasikan melalui port host `3307`.

### 6.1. Mengakses MySQL dari Terminal

Masuk ke direktori proyek:

```bash
cd ~/Project/interview/erp-docker
```

Jalankan MySQL client di dalam container:

```bash
docker compose exec erp_mysql mysql -uroot -p"$(grep MYSQL_ROOT_PASSWORD .env | cut -d= -f2)"
```

Masukkan password root MySQL saat diminta. Cara ini lebih aman daripada menuliskan password langsung dalam argumen perintah.

Pilih database:

```sql
USE database_erp;
```

Lihat tabel:

```sql
SHOW TABLES;
```

Periksa data SPKO:

```sql
SELECT * FROM workallocation;
```

Periksa data NTHKO:

```sql
SELECT * FROM workcompletion;
```

Keluar dari MySQL:

```sql
EXIT;
```

### 6.2. Mengakses MySQL dengan Aplikasi GUI

Database dapat diakses menggunakan DBeaver, MySQL Workbench, atau HeidiSQL.

| Parameter | Nilai |
|---|---|
| Host | `127.0.0.1` |
| Port | `3307` |
| Database | `database_erp` |
| Username | `root` atau `erp_user` |
| Password | Sesuai konfigurasi `.env` |

**Penting:** Gunakan port `3307`, bukan `3306`, ketika mengakses database melalui host. Port internal container dapat berbeda dari port yang dipublikasikan ke host.

Pastikan nama variabel password dan username sesuai dengan konfigurasi aktual pada `.env` serta Compose.

### 6.3. Menjalankan Query Tanpa Masuk ke Shell

Contoh melihat data NTHKO:

```bash
docker compose exec erp_mysql mysql -uroot -p \
  -e "USE database_erp; SELECT * FROM workcompletion;"
```

MySQL akan meminta password secara interaktif.

## 7. Struktur Database

Aplikasi menggunakan enam tabel utama yang mengikuti struktur soal.

| No. | Tabel | Fungsi |
|---:|---|---|
| 1 | `employee` | Menyimpan data master operator |
| 2 | `product` | Menyimpan data master produk |
| 3 | `workallocation` | Menyimpan header SPKO atau rencana kerja |
| 4 | `workallocationitem` | Menyimpan detail SPKO, termasuk produk, kuantitas, dan berat rencana |
| 5 | `workcompletion` | Menyimpan header NTHKO atau realisasi pekerjaan |
| 6 | `workcompletionitem` | Menyimpan detail NTHKO, termasuk produk, kuantitas selesai, dan berat aktual |

### 7.1. Kolom Tambahan pada Produk

Selain kolom pada skema soal, tabel `product` menggunakan kolom tambahan berikut untuk mendukung input dinamis:

- `custom_description`
- `custom_serial_no`
- `custom_carat`

Kolom tambahan tersebut merupakan bagian dari implementasi aplikasi dan perlu diperiksa kesesuaiannya dengan migrasi database yang digunakan.

### 7.2. Hubungan Data

Secara konseptual, relasi utamanya adalah:

```text
employee
   |
   +---- workallocation (SPKO)
   |             |
   |             +---- workallocationitem ---- product
   |
   +---- workcompletion (NTHKO)
                 |
                 +---- workcompletionitem ---- product
```

Detail NTHKO juga perlu memiliki relasi yang valid ke SPKO atau detail SPKO asal agar hasil pekerjaan dapat dibandingkan dengan rencana yang benar. Implementasi relasi tersebut harus mengikuti foreign key dan mekanisme penghubung yang benar-benar digunakan pada skema database.

## 8. Alur Kerja Aplikasi

### 8.1. Membuat SPKO

1. Pilih operator.
2. Tentukan tanggal dan proses kerja.
3. Tambahkan satu atau beberapa produk.
4. Masukkan kuantitas dan berat rencana setiap produk.
5. Simpan SPKO dan gunakan nomor transaksi yang dihasilkan aplikasi.
6. Cetak SPKO jika diperlukan.

### 8.2. Membuat NTHKO

1. Pilih SPKO yang akan diselesaikan.
2. Masukkan hasil pekerjaan berdasarkan produk yang dikerjakan.
3. Catat kuantitas selesai dan berat aktual hasil timbangan.
4. Simpan NTHKO.

NTHKO mencatat realisasi pekerjaan dan tidak seharusnya mengubah data rencana pada SPKO secara langsung.

Sistem dapat menyembunyikan SPKO yang sudah selesai diproses agar tidak dipilih kembali. Perilaku ini mengikuti aturan implementasi aplikasi.

### 8.3. Menghitung Selisih Berat

Flask menyajikan laporan perbandingan berat aktual NTHKO dengan berat rencana SPKO.

Rumus dasar:

\[
\text{Selisih Berat} =
\text{Berat Aktual} - \text{Berat Rencana}
\]

Interpretasi hasil:

- **Positif:** berat aktual lebih besar daripada berat rencana.
- **Negatif:** berat aktual lebih kecil daripada berat rencana.
- **Nol:** berat aktual sama dengan berat rencana.

Perbandingan harus menggunakan satuan dan definisi berat yang sama. Jika berat disimpan sebagai berat total per baris, bandingkan totalnya. Jika berat disimpan per unit, kuantitas perlu diperhitungkan sebelum membandingkan total.

Data NTHKO yang belum tersedia tidak boleh otomatis dianggap memiliki berat aktual nol.

## 9. Troubleshooting

### 9.1. Error `View [workallocation.index] not found`

**Penyebab umum:** Laravel tidak menemukan file Blade yang dirujuk oleh controller.

Pastikan file view berada pada lokasi yang sesuai, misalnya:

```text
resources/views/workallocation/index.blade.php
```

Kemudian bersihkan cache view:

```bash
docker compose exec erp_laravel php artisan view:clear
```

Pastikan nama folder dan file sesuai dengan pemanggilan `view()` di controller.

### 9.2. Error `Unknown column 'updated_at'`

**Penyebab umum:** Model Eloquent mengharapkan kolom timestamp, tetapi tabel tidak memilikinya.

Jika tabel memang tidak menggunakan `created_at` dan `updated_at`, nonaktifkan timestamps pada model terkait:

```php
public $timestamps = false;
```

Lakukan hanya jika struktur tabel memang tidak memiliki kolom timestamp yang dikelola Eloquent.

### 9.3. Error `Permission denied` pada Storage

Periksa kepemilikan dan permission direktori `storage` serta `bootstrap/cache`.

Contoh penyesuaian untuk container yang menjalankan proses dengan UID/GID `82`:

```bash
sudo chown -R 82:82 storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

Jalankan perintah dari direktori aplikasi Laravel dan pastikan UID/GID sesuai dengan user proses PHP-FPM pada image yang digunakan. Hindari mengubah permission secara membabi buta tanpa memeriksa konfigurasi container.

### 9.4. Circular Import pada Flask

**Penyebab umum:** Modul database dan model saling mengimpor sehingga membentuk siklus import.

Pisahkan tanggung jawab modul. Sebagai contoh:

- `database.py`: konfigurasi engine dan objek `Base`.
- `models.py`: definisi model yang mengimpor `Base` dari `database.py`.
- `app.py`: inisialisasi aplikasi Flask dan pemanggilan modul yang dibutuhkan.

Pastikan `database.py` tidak mengimpor `models.py` jika `models.py` sudah mengimpor `database.py`.

### 9.5. Dropdown SPKO pada NTHKO Kosong

Beberapa kemungkinan penyebab:

- Belum ada SPKO yang tersimpan.
- Semua SPKO sudah ditandai selesai sesuai logika aplikasi.
- Query dropdown tidak menemukan SPKO yang memenuhi kriteria.
- Terjadi masalah relasi atau data pada database.

Periksa data SPKO dan NTHKO terlebih dahulu:

```sql
SELECT * FROM workallocation;
SELECT * FROM workcompletion;
```

Jika ingin menguji ulang, gunakan database pengujian atau data demo yang dapat dibuat ulang. Jangan menghapus tabel transaksi secara langsung pada database yang berisi data penting.

---

## Penutup

ERP SPKO & NTHKO System memisahkan pengelolaan transaksi dan pelaporan melalui dua layanan utama: Laravel untuk proses SPKO/NTHKO dan Flask untuk analisis selisih berat. Docker Compose menyatukan layanan tersebut dalam satu lingkungan pengembangan, dengan MySQL sebagai database bersama.

Sebelum digunakan di lingkungan produksi, pastikan konfigurasi relasi database, autentikasi, validasi input, keamanan kredensial, backup database, serta aturan perhitungan berat telah diuji.


