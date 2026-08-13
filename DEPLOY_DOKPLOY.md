# Deploy ke GHCR + Dokploy

Project ini sekarang disiapkan untuk alur:

1. Push ke branch `main`
2. GitHub Actions build image dari `Dockerfile`
3. Image dipublish ke GHCR
4. Dokploy pull image tersebut dan menjalankan container WordPress

## File yang ditambahkan

- `.github/workflows/ghcr.yml`
- `docker-compose.dokploy.yml`
- `.env.dokploy.example`

## 1. Publish image ke GHCR

Workflow akan jalan otomatis saat:

- ada push ke branch `main`
- ada tag yang dimulai dengan `v`
- dijalankan manual lewat `workflow_dispatch`

Image yang dipublish:

- `ghcr.io/<owner>/<repo>:latest` untuk default branch
- `ghcr.io/<owner>/<repo>:main`
- `ghcr.io/<owner>/<repo>:sha-<commit>`
- `ghcr.io/<owner>/<repo>:v...` saat push tag release

## 2. Setting GitHub repository

Di GitHub, pastikan:

- Actions diizinkan berjalan
- package GHCR boleh dibuat oleh workflow
- package visibility disesuaikan

Jika package GHCR dibuat `private`, Dokploy perlu registry credential:

- Registry: `ghcr.io`
- Username: akun GitHub atau machine user
- Password/Token: GitHub Personal Access Token dengan scope minimal `read:packages`

## 3. Setup di Dokploy

Buat app baru dengan tipe `Docker Compose`, lalu gunakan isi file `docker-compose.dokploy.yml`.

Tambahkan environment variables berikut di Dokploy:

```env
GHCR_IMAGE=ghcr.io/OWNER/jendelaternak-wp
IMAGE_TAG=latest
APP_PORT=80
DB_HOST=mysql-host:3306
DB_NAME=wordpress_db
DB_USER=wordpress
DB_PASSWORD=change-me
WP_HOME=https://domain-anda.com
WP_SITEURL=https://domain-anda.com
WP_DEBUG=false
```

## 4. Catatan database

Compose Dokploy ini hanya menjalankan container WordPress. Database diasumsikan:

- memakai service MySQL/MariaDB terpisah di Dokploy, atau
- memakai database eksternal

Nilai `DB_HOST` harus diarahkan ke host database tersebut.

## 5. Volume persistence

Volume berikut dipakai untuk media upload WordPress:

- `wordpress_uploads`

Artinya file upload user tidak hilang saat container di-redeploy.

## 6. Catatan runtime

`wp-config.php` sudah disesuaikan agar `WP_DEBUG` mengikuti environment variable, jadi environment production bisa memakai:

```env
WP_DEBUG=false
```
