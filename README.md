# Koptik - Sistem POS Kedai Kopi

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php)
![License](https://img.shields.io/badge/License-MIT-blue.svg)

Aplikasi Point of Sale (POS) modern untuk kedai kopi dengan sistem pemesanan berbasis QR Code. Aplikasi ini memungkinkan pelanggan melakukan pemesanan mandiri melalui scan QR code di meja, serta menyediakan dashboard admin lengkap untuk manajemen pesanan, stok, dan laporan.

## 🌟 Fitur Utama

### Untuk Pelanggan
- **Pemesanan QR Code** - Scan QR code di meja untuk memesan langsung
- **Menu Interaktif** - Browse menu dengan kategori yang mudah dinavigasi
- **Keranjang Real-time** - Kelola jumlah item dengan mudah
- **Cek Stok Otomatis** - Item habis otomatis tidak dapat dipesan
- **Pemesanan Mudah** - Cukup masukkan nama dan nomor meja

### Untuk Admin/Kasir
- **Dashboard Real-time**
  - Total pesanan hari ini
  - Pesanan yang menunggu pembayaran
  - Omzet harian
  - Alert stok menipis
- **Manajemen Pesanan**
  - Lihat pesanan masuk
  - Update status pesanan (Menunggu → Diproses → Selesai)
  - Konfirmasi pembayaran dengan hitung kembalian
  - Riwayat pesanan
- **Manajemen Menu**
  - CRUD item menu
  - Kategorisasi menu
  - Manajemen harga
  - Manajemen stok dengan notifikasi otomatis
- **Manajemen User**
  - Sistem role-based permissions
  - Multi-user dengan hak akses berbeda
- **Laporan Komprehensif**
  - Export laporan ke Excel
  - Export laporan ke PDF
  - Analitik penjualan

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 12.x
- **PHP**: ^8.2
- **Database**: SQLite (MySQL compatible)
- **Authentication**: Laravel Breeze
- **Authorization**: Custom Role-Based Access Control

### Frontend
- **Build Tool**: Vite
- **CSS Framework**: Tailwind CSS
- **JavaScript**: Alpine.js
- **Icons**: SVG Icons

### Libraries
- `barryvdh/laravel-dompdf` - PDF Generation
- `maatwebsite/excel` - Excel Export
- `yajra/laravel-datatables-oracle` - DataTables

## 📋 Prerequisites

Sebelum menginstall aplikasi ini, pastikan sudah terinstall:

- PHP >= 8.2
- Composer
- Node.js & NPM
- SQLite (atau MySQL/PostgreSQL)

## 🚀 Instalasi

1. **Clone repository**
```bash
git clone https://github.com/username/koptikv2.git
cd koptikv2
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Setup database**
```bash
# Untuk SQLite
touch database/database.sqlite

# Atau konfigurasi MySQL di .env
# DB_CONNECTION=mysql
# DB_DATABASE=koptik
# DB_USERNAME=root
# DB_PASSWORD=
```

5. **Run migrations & seeder**
```bash
php artisan migrate:fresh --seed
```

6. **Build assets**
```bash
npm run build
```

7. **Start development server**
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## 👤 Default Account

Setelah menjalankan seeder, Anda dapat login dengan:

**Admin:**
- Email: `admin@example.com`
- Password: `password`

**Kasir:**
- Email: `kasir@example.com`
- Password: `password`

## 📂 Struktur Project

```
koptikv2/
├── app/
│   ├── Http/
│   │   └── Controllers/    # Semua controller aplikasi
│   └── Models/             # Eloquent models
├── database/
│   ├── migrations/         # Database migrations
│   └── seeders/           # Database seeders
├── resources/
│   └── views/             # Blade templates
├── routes/
│   └── web.php            # Web routes
├── public/                # Public assets
└── .env                   # Environment configuration
```

## 🎯 Penggunaan

### Untuk Pelanggan
1. Scan QR code yang tersedia di meja
2. Pilih menu yang diinginkan
3. Atur jumlah pesanan
4. Klik "Pesan Sekarang"
5. Masukkan nama dan nomor meja
6. Tunggu pesanan diproses

### Untuk Admin
1. Login ke dashboard admin
2. Monitor pesanan masuk di halaman utama
3. Proses pesanan sesuai status
4. Konfirmasi pembayaran
5. Kelola menu dan stok
6. Generate laporan penjualan

## 🔧 Konfigurasi

### Mengubah Database ke MySQL

Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=koptik
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Mengubah URL Aplikasi

Edit file `.env`:
```env
APP_URL=http://your-domain.com
```

## 📸 Screenshots

*(Tambahkan screenshot aplikasi di sini)*

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan ikuti langkah berikut:

1. Fork repository ini
2. Buat branch fitur (`git checkout -b fitur/fitur-baru`)
3. Commit perubahan (`git commit -m 'Tambah fitur baru'`)
4. Push ke branch (`git push origin fitur/fitur-baru`)
5. Buat Pull Request

## 📄 Lisensi

Project ini dilisensikan under the MIT License - lihat file [LICENSE](LICENSE) untuk details.

## 👨‍💻 Author

**Nama Anda**
- Website: [https://yourwebsite.com](https://yourwebsite.com)
- GitHub: [@username](https://github.com/username)

## 🙏 Acknowledgments

- [Laravel](https://laravel.com)
- [Tailwind CSS](https://tailwindcss.com)
- [Alpine.js](https://alpinejs.dev)
- Semua kontributor lainnya

---

Dibuat dengan ❤️ untuk mendukung UMKM Indonesia
