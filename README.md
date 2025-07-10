
# 📡 R Gateway – WhatsApp API Gateway

**R Gateway** adalah sistem penghubung (gateway) untuk mengirim pesan WhatsApp secara otomatis melalui REST API. Sistem ini cocok untuk integrasi dengan aplikasi pihak ketiga seperti sistem billing, notifikasi transaksi, chatbot, dll.

---

## 🚀 Fitur

- ✅ Kirim pesan WhatsApp via API
- ✅ Token API per client
- ✅ Dukungan multi-user / multi-session
- ✅ Dashboard pengelolaan pengguna dan status koneksi
- ✅ Riwayat pengiriman & laporan

## 🖼️ Tampilan Dashboard

Berikut contoh tampilan dashboard dari aplikasi **R Gateway**:
![Dashboard](public/screenshots/dashboard.jpg)
---

## 🔧 Instalasi

### 1. Clone
```bash
git clone https://github.com/kholif18/r-gateway.git
cd r-gateway
cp .env.example .env
```

### 2. Konfigurasi `.env`
Edit / sesuaikan kode berikut pada file `.env`:

```env
DB_CONNECTION=sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

WA_BACKEND_URL=http://wa_backend:3000
WHATSAPP_SOCKET_URL=http://localhost:3000
API_SECRET=your_api_secret_key
```

### 3. Install
```bash
composer install
php artisan key:generate
```

### 4. Jalankan migrasi & seeder
```bash
php artisan migrate --seed
```

### 5. Jalankan server Laravel
```bash
php artisan serve
```

## ⚙️ Konfigurasi Backend WhatsApp (wa-backend)
Pastikan server WhatsApp (wa-backend) sudah berjalan di alamat dan port yang benar.

## 🔧 Contoh Alamat `WA_BACKEND_URL` dan `WHATSAPP_SOCKET_URL`

| Lingkungan       | WA_BACKEND_URL             | WHATSAPP_SOCKET_URL        |
|------------------|----------------------------|----------------------------|
| Docker           | `http://wa_backend:3000`   | `http://wa_backend:3000`   |
| Lokal (host)     | `http://localhost:3000`    | `http://localhost:3000`    |
| Lokal (IP lokal) | `http://127.0.0.1:3000`     | `http://127.0.0.1:3000`     |
| Jaringan LAN     | `http://192.168.1.100:3000` | `http://192.168.1.100:3000` |

### 📝 Contoh di file `.env` Laravel

```env
WA_BACKEND_URL=http://localhost:3000
WHATSAPP_SOCKET_URL=http://localhost:3000

---

## 🧪 API Pengiriman Pesan

### Endpoint
```
POST /api/send
```

### Header
```http
Authorization: Bearer {API_TOKEN}
Content-Type: application/json
```

### Body JSON
```json
{
  "phone": "6281234567890",
  "message": "Pesan dari sistem Anda"
}
```

### Response
```json
{
  "status": "success",
  "message": "Pesan berhasil dikirim",
  "data": {
    "phone": "6281234567890",
    "status": "sent",
    "sent_at": "2025-06-28T19:26:15"
  }
}
```

---

# 📲 Autentikasi & Koneksi WhatsApp

## 🧪 API Pengiriman Pesan

### Endpoint
```
POST /api/send
```

### Header
```http
Authorization: Bearer {API_TOKEN}
Content-Type: application/json
```

### Body JSON
```json
{
  "phone": "6281234567890",
  "message": "Pesan dari sistem Anda"
}
```

---

# 📲 Autentikasi & Koneksi WhatsApp

## 🚀 Mulai Session (Scan QR Code)

### Endpoint
```
POST /session/start
```

### Header
```http
X-API-SECRET: {API_SECRET}
Content-Type: application/json
```

### Response
```json
{
  "message": "Session started",
  "status": "qr" // atau "connected" jika sudah login
}
```

> Client akan menerima QR Code melalui Socket.IO event: `session:qr`

---

## 🔍 Cek Status Session

### Endpoint
```
GET /session/status?session={sessionId}
```

### Header
```http
X-API-SECRET: {API_SECRET}
```

### Response
```json
{
  "session": "username",
  "status": "connected" // atau "qr", "disconnected", dll.
}
```

---

## 🔓 Logout Session

### Endpoint
```
GET /session/logout?session={sessionId}
```

### Header
```http
X-API-SECRET: {API_SECRET}
```

### Response
```json
{
  "message": "Session username berhasil logout dan dihapus"
}
```

---

# 👥 Kirim Pesan Grup

### Endpoint
```
POST /session/send-group
```

### Header
```http
X-API-SECRET: {API_SECRET}
Content-Type: application/json
```

### Body
```json
{
  "groupName": "Nama Grup",
  "message": "Pesan ke grup"
}
```

---

# 📎 Kirim Media dari URL

### Endpoint
```
POST /session/send-media
```

### Header
```http
X-API-SECRET: {API_SECRET}
Content-Type: application/json
```

### Body
```json
{
  "phone": "6281234567890",
  "caption": "Ini gambarnya",
  "url": "https://example.com/image.jpg"
}
```

---

# 📤 Kirim Media via Upload (FormData)

### Endpoint
```
POST /session/send-media-upload
```

### Header
```http
X-API-SECRET: {API_SECRET}
Content-Type: multipart/form-data
```

### Form Data

| Field     | Tipe    | Deskripsi                          |
|-----------|---------|------------------------------------|
| `phone`   | string  | Nomor tujuan (format internasional) |
| `file`    | file    | File media                         |
| `caption` | string  | (opsional) Caption                 |

---

# 📢 Kirim Pesan Massal (Bulk)

### Endpoint
```
POST /session/send-bulk
```

### Header
```http
X-API-SECRET: {API_SECRET}
Content-Type: application/json
```

### Body
```json
{
  "messages": [
    {
      "phone": "6281234567890",
      "message": "Halo 1"
    },
    {
      "phone": "6289876543210",
      "message": "Halo 2"
    }
  ]
}
```

---

# 🧠 Catatan Tambahan

- Semua endpoint membutuhkan header `X-API-SECRET` untuk autentikasi internal.
- Pastikan session aktif (status `connected`) sebelum mengirim pesan.
- Koneksi WebSocket (`Socket.IO`) akan otomatis memberi update QR dan status login ke client.

---

📌 Untuk integrasi dengan frontend Laravel atau Blade, pastikan nilai `WHATSAPP_SOCKET_URL` sudah sesuai di `.env`, dan gunakan seperti:

```js
const socket = io("{{ config('services.whatsapp.socket_url') }}/?session={{ Auth::user()->username }}");
```
---

## 🔑 API Token

Token API dibuat otomatis saat user mendaftar. Untuk melihat / mengganti token:
- Login sebagai admin
- Masuk ke menu **Client Management**
- Salin atau regenerate token

---

## 📊 Dashboard

Dashboard menampilkan:
- Status gateway (connected / disconnected)
- Total pesan hari ini
- Persentase sukses kirim
- Riwayat pesan terakhir

---

## 📘 Panduan Lengkap

Cek halaman bantuan di aplikasi:
```
/help
/help/api
```

---

## 🛠 Stack Teknologi

- Laravel 12
- Bootstrap 5
- WhatsApp Web.js (backend)
- Docker (opsional)
- FontAwesome Icons

---

## 🧑‍💻 Kontribusi

Pull request sangat diterima! Silakan fork, buat branch baru, dan kirim PR. Pastikan kode sudah teruji dengan baik.

---

## 📄 Lisensi

R Gateway dikembangkan oleh [Ravaa Creative](https://ravaa.my.id)  
Lisensi: MIT License
