# Kodhe — Contoh Penerapan Framework

Project contoh yang memakai **KaryaKode/Kodhe Framework** (`kodhe/framework`).
Struktur ini mengikuti pola: `public/` (docroot) → `bootstrap/app.php` → `application/`.

## Kebutuhan

- PHP >= 8.1 (dengan `mbstring`, disarankan `iconv`)
- Composer

## Instalasi

```bash
cd kodhe
composer install          # framework diambil dari repo induk via path repository ../
cp .env.example .env      # sesuaikan kredensial DB/encryption key bila perlu
php -S localhost:8000 -t public   # dev server cepat
```

Buka `http://localhost:8000/` — route `welcome` merender tema Blade di
`application/views/default/`.

## Struktur Penting

| Path | Fungsi |
|---|---|
| `public/index.php` | Entry point; set `ENVIRONMENT` (default `production`, override via `CI_ENV`) |
| `bootstrap/app.php` | Boot framework, helper global `app()`/`kodhe()`/`get_instance()` |
| `application/config/` | Konfigurasi app (`config.php`, `middleware.php`, `routes`, dll.) |
| `application/routes/web.php` | Route modern web (`Route::get`, group, fallback) |
| `application/routes/api.php` | Route khusus API/REST (prefix `api` + middleware group `api`) |
| `application/middlewares/` | Middleware alias `auth`, `csrf`, `session`, `api.*`, dst. |
| `application/views/default/` | Tema Blade (layout + pages) |
| `database/migrations/` | Migrasi (satu file per tabel) |
| `bin/console` | CLI toolkit (setara `spark` di CodeIgniter 4) |
| `storage/` | Cache, logs, session (di-gitignore, dibuat saat instalasi) |

## Console & Migrasi Database (`php bin/console`)

Jalankan **selalu dari root project** (folder ini, tempat `vendor/` berada):

```bash
cd kodhe
composer install                       # sekali saja

php bin/console migrate                # jalankan semua migrasi pending
php bin/console migrate --status       # lihat applied / pending
php bin/console migrate --rollback     # undo 1 batch terakhir
php bin/console migrate --fresh        # rollback semua lalu up ulang
php bin/console make:migration create_posts_table   # buat file migrasi baru
php bin/console list                   # daftar semua command
```

> Alternatif: `php vendor/kodhe/framework/bin/console migrate` juga didukung —
> command `migrate` mendeteksi root project dari CWD maupun posisi file
> framework di `vendor/`, dan memuat `vendor/autoload.php` project secara
> otomatis. Pastikan `kodhe/database` ikut ter-install (sudah menjadi
> dependency `kodhe/framework` sejak versi ini).

## Keamanan

- `csrf_protection` aktif secara default (`application/config/config.php`).
- `ENVIRONMENT` default `production`; gunakan `CI_ENV=development` hanya lokal.
- Jangan pernah meng-commit `.env` asli; gunakan `.env.example`.

## Catatan

- Helper `app()`, `kodhe()`, dan `get_instance()` didefinisikan di `bootstrap/app.php`.
- Jika ingin memakai file `.env.php`, tambahkan paket dotenv ke `composer.json`
  (lihat komentar pada blok "Load the environment").
