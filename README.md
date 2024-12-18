# IniBencana - Sistem Informasi Bencana

Sistem informasi untuk mengelola dan memantau data bencana.

## Persyaratan Sistem
- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js & NPM
- Git

## Cara Instalasi

1. Clone repository ini
```bash
git clone <URL_REPOSITORY_GITHUB_ANDA>
```

2. Masuk ke direktori project
```bash
cd IniBencana
```

3. Install dependensi PHP menggunakan Composer
```bash
composer install
```

4. Install dependensi JavaScript
```bash
npm install
```

5. Salin file .env.example menjadi .env
```bash
cp .env.example .env
```

6. Generate application key
```bash
php artisan key:generate
```

7. Konfigurasi database di file .env
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inibencana
DB_USERNAME=root
DB_PASSWORD=
```

8. Jalankan migrasi database
```bash
php artisan migrate
```

9. Jalankan seeder (jika ada)
```bash
php artisan db:seed
```

10. Compile assets
```bash
npm run dev
```

11. Jalankan server development
```bash
php artisan serve
```

Aplikasi sekarang dapat diakses di `http://localhost:8000`

## Cara Push ke GitHub

1. Inisialisasi Git repository (jika belum)
```bash
git init
```

2. Tambahkan remote repository
```bash
git remote add origin <URL_REPOSITORY_GITHUB_ANDA>
```

3. Tambahkan semua file ke staging
```bash
git add .
```

4. Commit perubahan
```bash
git commit -m "Initial commit"
```

5. Push ke GitHub
```bash
git push -u origin main
```

## File yang Perlu Diperhatikan

- Pastikan file `.env` tidak ter-commit ke repository (sudah ada di .gitignore)
- Simpan file `.env.example` sebagai template konfigurasi
- Folder `vendor` dan `node_modules` sudah masuk dalam `.gitignore`

## Kontribusi

Silakan buat pull request untuk berkontribusi pada project ini.

## Lisensi

[MIT License](LICENSE)
