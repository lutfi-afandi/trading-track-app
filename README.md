# 📈 TradePulse - Stock Portfolio Manager v2.0

**TradePulse** adalah aplikasi manajemen portofolio saham sederhana namun elegan yang dirancang untuk membantu investor memantau saldo, harga rata-rata (_Average Price_), dan keuntungan/kerugian yang direalisasikan (_Realized Profit/Loss_) secara real-time.

---

## ✨ Fitur Utama

- **Dashboard Overview**: Pantau total _Realized Profit/Loss_ dengan tampilan kartu mewah.
- **Smart Trading Form**:
  - Sistem _toggle_ BUY/SELL yang dinamis.
  - Estimasi biaya transaksi (_Net Amount_) termasuk biaya broker otomatis (Buy: 0.15%, Sell: 0.25%).
  - Validasi otomatis: Tidak bisa menjual lot melebihi stok yang dimiliki.
- **Running Portfolio**: Daftar saham yang sedang dimiliki dengan perhitungan _Moving Average_ otomatis.
- **Transaction History**: Catatan riwayat transaksi lengkap dengan opsi pembatalan/penghapusan.
- **Responsive UI**: Antarmuka modern menggunakan Bootstrap 5 yang nyaman diakses dari desktop maupun smartphone.

---

## 🛠️ Teknologi yang Digunakan

- **Frontend**: HTML5, CSS3 (Custom Luxury Styles), Bootstrap 5.
- **Library JS**:
  - [jQuery](https://jquery.com/) (DOM Manipulation & AJAX).
  - [SweetAlert2](https://sweetalert2.github.io/) (Pop-up notifikasi cantik).
  - [Flatpickr](https://flatpickr.js.org/) (Pemilih tanggal & waktu).
  - [FontAwesome](https://fontawesome.com/) (Ikon grafis).
- **Backend**: PHP & MySQL (via `api.php`).

---

## 🗄️ Struktur Database (SQL)

Salin kode ini ke dalam panel SQL (phpMyAdmin) Anda:

```sql
-- 1. Table: portfolio (Status kepemilikan saham saat ini)
CREATE TABLE `portfolio` (
  `stock_code` varchar(10) NOT NULL,
  `total_lot` int(11) NOT NULL DEFAULT 0,
  `avg_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_cost_inc_fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`stock_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Table: transaction_history (Log riwayat transaksi)
CREATE TABLE `transaction_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_code` varchar(10) NOT NULL,
  `type` enum('BUY','SELL') NOT NULL,
  `transaction_date` datetime NOT NULL,
  `price_per_lot` decimal(15,2) NOT NULL,
  `lot` int(11) NOT NULL,
  `fee` decimal(15,2) NOT NULL,
  `net_amount` decimal(15,2) NOT NULL,
  `profit_loss` decimal(15,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  INDEX (`stock_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

---

## 🚀 Cara Instalasi

1.  **Clone Repository** atau download file proyek.
2.  **Database Setup**:
    - Buat database baru di MySQL (contoh: `db_tradepulse`).
    - Import file SQL.
3.  **Konfigurasi API**:
    - Sesuaikan koneksi database pada file `api.php`.
4.  **Akses Aplikasi**:
    - Pindahkan folder ke `htdocs` (XAMPP) atau direktori web server Anda.
    - Buka browser dan akses `localhost/trading-track-app`.

---

## 📝 Catatan Penggunaan

- **Input Harga**: Masukkan harga per lembar saham tanpa titik/koma (Contoh: `1500`).
- **Format Lot**: 1 Lot dikonversi otomatis sebagai 100 lembar dalam perhitungan nilai total.
- **Sistem Tanggal**: Untuk transaksi jual (SELL), aplikasi secara otomatis mengunci tanggal agar tidak bisa memilih waktu sebelum tanggal beli terakhir saham tersebut.

---

## 👤 Author

- **Project Name**: TradePulse Stock Portfolio
- **Version**: 2.0 (2026 Edition)

---

_Developed with ✨ for better trading discipline._
```
