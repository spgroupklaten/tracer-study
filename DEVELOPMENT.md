# Tracer Study - Development Guide

## 📋 Project Requirements

### Sistem Operasi
- **Windows** (dengan Laragon) atau **Linux/MacOS**

### Software Requirements

| Software | Minimum Version | Direkomendasikan |
|----------|-----------------|------------------|
| PHP | 8.2+ | 8.3.x |
| Composer | 2.0+ | 2.9.x |
| Node.js | 18+ | 22.x |
| NPM | 8+ | 10.x |
| MySQL | 8.0 | 8.0+ |
| Git | 2.0+ | Latest |

### Ekstensi PHP Required
- BCMath PHP Extension
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- Intl PHP Extension
- MBstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- SQLite PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

---

## 🚀 Cara Menjalankan Laravel

### 1. Install Dependencies

```bash
# Install PHP dependencies (Composer)
composer install

# Install Node.js dependencies (NPM)
npm install
```

### 2. Setup Environment

```bash
# Copy .env.example to .env
copy .env.example .env
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Jalankan Development Server

```bash
# Menggunakan Artisan
php artisan serve

# Atau menggunakan Laragon (otomatis)
# Buka http://tracer-study.test di browser
```

Server akan berjalan di `http://localhost:8000`

---

## 🗄️ Cara Menjalankan Migration

### Cek Status Migration

```bash
php artisan migrate:status
```

### Jalankan Migration

```bash
# Jalankan semua migration yang belum dijalankan
php artisan migrate

# Jalankan migration dengan seeding
php artisan migrate --seed
```

### Rollback Migration

```bash
# Rollback migration terakhir
php artisan migrate:rollback

# Rollback semua migration
php artisan migrate:fresh

# Rollback dan jalankan ulang
php artisan migrate:fresh --seed
```

### Membuat Migration Baru

```bash
php artisan make:migration create_nama_tabel_table
```

---

## 🎨 Cara Menjalankan Frontend

### Development Mode (Hot Reload)

```bash
npm run dev
```

### Build untuk Production

```bash
npm run build
```

### Clear Cache

```bash
# Clear compiled views
php artisan view:clear

# Clear config cache
php artisan config:clear

# Clear all cache
php artisan optimize:clear
```

---

## 🧪 Command Testing

### Jalankan Semua Test

```bash
php artisan test
```

### Jalankan Test dengan Coverage

```bash
php artisan test --coverage
```

### Jalankan Specific Test

```bash
php artisan test --filter=NamaTest
```

### Unit Testing

```bash
php artisan test --testsuite=Unit
```

### Feature Testing

```bash
php artisan test --testsuite=Feature
```

---

## 📁 Struktur Folder Utama

```
tracer-study/
│
├── app/                          # Aplikasi utama
│   ├── Http/
│   │   ├── Controllers/         # HTTP Controllers
│   │   ├── Middleware/          # HTTP Middleware
│   │   └── Requests/            # Form Request Validation
│   ├── Models/                   # Eloquent Models
│   ├── Services/                 # Business Logic Layer
│   ├── Enums/                    # Enum Classes
│   └── Providers/                # Service Providers
│
├── bootstrap/                    # Bootstrap aplikasi
│   ├── app.php                   # Application bootstrap
│   └── providers.php             # Provider registration
│
├── config/                       # Konfigurasi Laravel
│   ├── app.php                   # App config
│   ├── auth.php                  # Authentication config
│   ├── database.php              # Database config
│   ├── cache.php                 # Cache config
│   ├── session.php               # Session config
│   └── queue.php                 # Queue config
│
├── database/                     # Database related
│   ├── migrations/               # Database migrations
│   ├── factories/                # Model factories
│   └── seeders/                  # Database seeders
│
├── public/                       # Public folder
│   ├── index.php                 # Entry point
│   └── .htaccess                 # Apache config
│
├── resources/                     # Frontend resources
│   ├── css/                      # Stylesheets
│   ├── js/                       # JavaScript
│   └── views/                    # Blade templates
│
├── routes/                       # Route definitions
│   ├── web.php                   # Web routes
│   ├── api.php                   # API routes
│   ├── console.php               # Console routes
│   └── admin.php                 # Admin routes
│
├── storage/                      # Storage (gitignored)
│   ├── app/                      # App storage
│   ├── framework/                # Framework cache
│   └── logs/                    # Log files
│
├── tests/                        # Test files
│   ├── Feature/                  # Feature tests
│   └── Unit/                     # Unit tests
│
├── vendor/                       # Composer dependencies (gitignored)
│
├── .env                          # Environment file (gitignored)
├── .env.example                  # Environment template
├── composer.json                 # Composer dependencies
├── package.json                  # NPM dependencies
├── vite.config.js                # Vite configuration
└── DEVELOPMENT.md                # Dokumentasi development
```

---

## 📝 Notes

### Environment Variables

Pastikan `.env` memiliki konfigurasi yang benar:

```env
APP_NAME=TracerStudy
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tracer_study
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Shortcut Laragon (Windows)

Jika menggunakan Laragon:
- **Start**: Klik tombol "Start" di Laragon
- **Web**: Buka `http://tracer-study.test`
- **Terminal**: Klik "Terminal" untuk akses command line

---

## 🔧 Troubleshooting

### Masalah Umum

**Composer not found**
```bash
# Windows dengan Laragon
php C:\laragon\bin\composer\composer.phar install
```

**Node not found**
```bash
# Windows dengan Laragon - gunakan full path
C:\laragon\bin\nodejs\node-v22\node.exe -v
```

**Migration error**
```bash
php artisan migrate:fresh
```

**Permission error (Linux/MacOS)**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 📚 Referensi

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Bootcamp](https://bootcamp.laravel.com)
- [Tailwind CSS](https://tailwindcss.com)
- [Vite](https://vitejs.dev)
