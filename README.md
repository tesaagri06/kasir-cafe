# Kasir Cafe — Point of Sale System

Aplikasi kasir berbasis web untuk manajemen cafe, dibangun dengan **Laravel 10**. Mendukung dua mode akses: antarmuka web (Blade + session) untuk penggunaan harian, dan REST API (JWT) untuk integrasi sistem eksternal.

---

##Live Demo

> Deployed di **Railway**
> [http://kasir-cafe-production.up.railway.app](http://kasir-cafe-production.up.railway.app)

---

## Tech Stack
| Kategori | Teknologi |
| Backend | Laravel 10, PHP 8.2 |
| Auth (API) | JWT (`php-open-source-saver/jwt-auth`), Basic Auth, API Key |
| Auth (Web) | Laravel Session (`Auth::guard('web')`) |
| Frontend | Blade, Tailwind CSS, Vite |
| Database | MySQL (via PDO) |
| Deployment | Railway + Nixpacks |

---

## Fitur
### Manajemen
- **Menu** — tambah, edit, hapus, toggle aktif/nonaktif, manajemen stok
- **Kategori** — kelola kategori menu (dengan icon & status)
- **Transaksi** — buat transaksi baru dengan kalkulasi otomatis (subtotal + pajak 3% = grand total), walk-in & customer terdaftar, filter by status/tanggal/eco_packaging
- **Laporan** — overview pendapatan, laporan harian, best-selling, sustainability report (SDGs)
### Autentikasi & Akses
- Login / Register (Web & API)
- **3 metode auth API:**
  - `Authorization: Bearer <token>` — JWT Token
  - `Authorization: Basic <base64>` — HTTP Basic Auth
  - `X-API-KEY: <key>` — API Key per-user (di-generate otomatis saat register)
- Role-based access control: `admin`, `kasir`, `customer`
- JWT payload menyertakan `role`, `username`, `nama_lengkap`

### Fitur Khusus
- 🌱 **Eco Packaging** — tracking permintaan kemasan ramah lingkungan (opt-in per transaksi)
- ♻️ **Food Waste Log** — pencatatan sisa makanan dengan alasan (kadaluarsa / rusak / sisa_hari / lainnya)
- 📊 **SDGs Score** — sistem penilaian A–E berdasarkan % eco packaging dan jumlah food waste
- 👤 **Profil** — update data diri & ganti password
- ⚙️ **Pengaturan** — konfigurasi data cafe (key-value store)

---

## Arsitektur
```
kasir-cafe/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                  # JSON response controllers
│   │   │   │   ├── AuthController
│   │   │   │   ├── MenuController
│   │   │   │   ├── KategoriController
│   │   │   │   ├── TransaksiController
│   │   │   │   └── ReportController
│   │   │   └── Web/                  # Blade view controllers
│   │   │       ├── AuthWebController
│   │   │       ├── DashboardController
│   │   │       ├── MenuWebController
│   │   │       ├── KategoriWebController
│   │   │       ├── TransaksiWebController
│   │   │       ├── LaporanWebController
│   │   │       ├── PengaturanWebController
│   │   │       └── ProfilWebController
│   │   └── Middleware/
│   │       ├── WebAuthMiddleware      # Guard session untuk web
│   │       ├── ApiKeyMiddleware       # Header X-API-KEY
│   │       ├── BasicAuthMiddleware    # HTTP Basic Auth
│   │       └── RoleMiddleware         # Role check (kasir/admin/customer)
│   └── Models/
│       ├── User                       # JWTSubject, custom getAuthPassword()
│       ├── Menu                       # scope: aktif, tersedia; method: kurangiStok()
│       ├── Kategori                   # scope: aktif; relation: menuAktif()
│       ├── Transaksi                  # scope: selesai, periode; static: hitungPajak()
│       ├── DetailTransaksi
│       ├── FoodWasteLog               # scope: periode
│       └── Setting                    # Key-value store; static: get(), set(), getAll()
├── database/
│   ├── migrations/                    # 8 migration files
│   └── seeders/
│       ├── UserSeeder                 # Default accounts
│       ├── KategoriSeeder
│       ├── MenuSeeder
│       └── SettingsSeeder
├── routes/
│   ├── web.php                        # Web routes (middleware: web.auth)
│   └── api.php                        # API routes (middleware: auth:api / basic.auth / api.key)
├── Procfile                           # Railway: migrate --seed on release
└── nixpacks.toml                      # PHP 8.2 + Node 20 build config
```

---

## Instalasi Lokal
### Prasyarat
- PHP >= 8.2
- Composer
- Node.js >= 20 & NPM
- MySQL
### Langkah-langkah
```bash
# 1. Clone repository
git clone https://github.com/tesaagri06/kasir-cafe.git
cd kasir-cafe
# 2. Install dependency PHP
composer install
# 3. Install dependency JS & build asset
npm install && npm run build
# 4. Salin file environment
cp .env.example .env
# 5. Generate app key
php artisan key:generate
# 6. Generate JWT secret
php artisan jwt:secret
# 7. Konfigurasi .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=kasir_cafe
# DB_USERNAME=root
# DB_PASSWORD=
# 8. Jalankan migrasi & seeder
php artisan migrate --seed
# 9. Jalankan server
php artisan serve
```
Akses di: `http://localhost:8000`

---

## API Reference
Base URL: `/api`
### Health Check
```
GET /api/ping
```
### Auth
| Method | Endpoint | Akses | Deskripsi |
| POST | `/api/auth/register` | Public | Register (role otomatis: `customer`, API key di-generate) |
| POST | `/api/auth/login` | Public | Login dengan `username` + `password` |
| POST | `/api/auth/logout` | JWT | Logout & invalidate token |
| POST | `/api/auth/refresh` | JWT | Refresh JWT token |
| GET | `/api/auth/me` | JWT / Basic / API Key | Data user aktif |

### Kategori
| Method | Endpoint | Akses | Deskripsi |
| GET | `/api/kategori` | JWT | List kategori |
| GET | `/api/kategori/{id}` | JWT | Detail kategori |
| POST | `/api/kategori` | JWT (kasir/admin) | Tambah kategori |
| PATCH | `/api/kategori/{id}` | JWT (kasir/admin) | Update kategori |
| DELETE | `/api/kategori/{id}` | JWT (kasir/admin) | Hapus kategori |

### Menu
| Method | Endpoint | Akses | Deskripsi |
| GET | `/api/menu` | JWT / API Key | List menu |
| GET | `/api/menu/{id}` | JWT | Detail menu |
| POST | `/api/menu` | JWT (kasir/admin) | Tambah menu |
| PATCH | `/api/menu/{id}` | JWT (kasir/admin) | Update menu |
| DELETE | `/api/menu/{id}` | JWT (kasir/admin) | Hapus menu |

### Transaksi
| Method | Endpoint | Akses | Deskripsi |
| GET | `/api/transaksi` | JWT | List transaksi (customer hanya miliknya) |
| POST | `/api/transaksi` | JWT | Buat transaksi (atomic, lock stok) |
| GET | `/api/transaksi/{id}` | JWT | Detail transaksi |

**Query params GET `/api/transaksi`:**
`search`, `status`, `tanggal_dari`, `tanggal_sampai`, `eco_packaging`, `per_page` (max 50)
**Body POST `/api/transaksi`:**
```json
{
  "nama_customer": "Budi",
  "no_meja": 3,
  "eco_packaging": true,
  "catatan": "Tanpa es",
  "items": [
    { "id_menu": 1, "qty": 2 },
    { "id_menu": 3, "qty": 1 }
  ]
}
```

### Laporan
| Method | Endpoint | Akses | Deskripsi |
| GET | `/api/laporan/overview` | JWT (kasir/admin) | Ringkasan pendapatan + SDGs |
| GET | `/api/laporan/harian` | JWT (kasir/admin) | Laporan per hari |
| GET | `/api/laporan/best-selling` | JWT (kasir/admin) | Ranking menu terlaris |
| GET | `/api/laporan/sustainability` | JWT (kasir/admin) | SDGs score (grade A–E) |
| POST | `/api/laporan/food-waste` | JWT (kasir/admin) | Catat food waste |

**Query params laporan:** `tanggal_dari`, `tanggal_sampai` (default: bulan berjalan)

**Body POST `/api/laporan/food-waste`:**
```json
{
  "id_menu": 2,
  "jumlah": 5,
  "alasan": "sisa_hari",
  "catatan": "Tutup lebih awal"
}
```
`alasan` enum: `kadaluarsa` | `rusak` | `sisa_hari` | `lainnya`

### Autentikasi API
```
# JWT Bearer Token
Authorization: Bearer <token>
# Basic Auth
Authorization: Basic <base64(username:password)>
# API Key
X-API-KEY: <api_key>
```

---

## Role & Hak Akses
| Role | Hak Akses |
| `admin` | Akses penuh semua fitur |
| `kasir` | Manajemen menu, kategori, transaksi, laporan |
| `customer` | Lihat menu, buat transaksi, lihat riwayat transaksi milik sendiri |

> Role di-encode langsung dalam JWT payload sehingga tidak perlu query database ulang saat validasi.

---

## Database Schema
```
users            → id_user, username, password_hash, nama_lengkap, email, telepon, role, api_key
kategori         → id_kategori, nama_kategori, deskripsi, icon, status
menu             → id_menu, nama_menu, harga, stok, id_kategori (FK nullable), status_menu
transaksi        → id_transaksi, customer_id (FK nullable), nama_customer, no_meja,
                    total, pajak, grand_total, status, catatan, eco_packaging
detail_transaksi → id_detail, id_transaksi (FK), id_menu (FK), qty, harga_satuan, subtotal
food_waste_log   → id, id_menu (FK), jumlah, alasan, catatan
settings         → setting_key (PK string), setting_value
```

**Catatan desain:**
- `customer_id` pada transaksi `nullOnDelete` — histori keuangan tetap ada walau user dihapus
- `id_kategori` pada menu `nullOnDelete` — menu tetap ada walau kategori dihapus
- `eco_packaging` default `false` — customer harus opt-in secara sadar
- Pajak dihitung 3% dari subtotal, disimpan sebagai integer (Rupiah, tanpa desimal)

---

## 🌱 SDGs Integration
SDGs Score dihitung otomatis dari dua komponen:
| Komponen | Bobot | Keterangan |
| Eco Packaging | 60% | % transaksi yang request eco packaging |
| Food Waste | 40% | Makin banyak kejadian = makin rendah score |

**Grade:** A ≥85 → B ≥70 → C ≥55 → D ≥40 → E <40

**SDGs Goals yang dipantau:**
- SDG 12 — Konsumsi & Produksi Bertanggung Jawab
- SDG 13 — Penanganan Perubahan Iklim
- SDG 2 — Tanpa Kelaparan (food waste monitoring)

---

## Deployment (Railway)
Project ini menggunakan **Nixpacks** untuk build otomatis di Railway.
```toml
# nixpacks.toml — PHP 8.2 + Node 20
[phases.install]
cmds = ["composer install --no-dev", "npm ci", "npm run build"]

[phases.build]
cmds = ["php artisan config:cache", "php artisan route:cache", "php artisan view:cache"]
```

```
# Procfile
release: php artisan migrate --seed --force
web: php artisan serve --host=0.0.0.0 --port=$PORT
```

*Environment variables wajib di Railway:*
```
APP_KEY=
APP_URL=http://kasir-cafe-production.up.railway.app
JWT_SECRET=
DB_CONNECTION=mysql
DB_HOST=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

## Lisensi
[MIT License](LICENSE)
