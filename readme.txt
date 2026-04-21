================================================================================
                         HOTEL NEO MANAGEMENT SYSTEM
================================================================================

Hotel Neo adalah aplikasi manajemen hotel berbasis web yang dirancang untuk 
memberikan pengalaman menginap yang terintegrasi bagi tamu dan kemudahan 
operasional bagi administrator hotel. Proyek ini dikembangkan menggunakan 
framework Laravel, dengan fitur utama sistem In-Room Dining eksklusif dan 
integrasi pembayaran otomatis.

--------------------------------------------------------------------------------
PROFIL PENGEMBANG
--------------------------------------------------------------------------------
Nama       : Bintang Putra Adryan

--------------------------------------------------------------------------------
FITUR UTAMA
--------------------------------------------------------------------------------

A. Fitur Tamu (Guest)
   1. Smart Room Booking: Reservasi kamar dengan filter kapasitas dan 
      pengecekan ketersediaan tanggal secara real-time untuk mencegah 
      pemesanan ganda (Double Booking).
   2. Exclusive In-Room Dining: Katalog restoran yang diwajibkan hanya untuk 
      tamu yang memiliki reservasi kamar aktif.
   3. Integrated Payment: Pembayaran tagihan otomatis menggunakan antarmuka 
      aplikasi (API) Midtrans.
   4. Guest Dashboard: Dasbor personal untuk memantau riwayat transaksi 
      kamar dan pesanan makanan.
   5. Smart Authentication: Implementasi middleware untuk mencegah akses ulang 
      ke halaman otentikasi bagi tamu yang telah masuk ke dalam sistem.

B. Fitur Admin (Staff)
   1. Master Data Management: Pengelolaan data pengguna sistem, kamar, tipe 
      kamar, dan hak akses (role).
   2. Restaurant Management: Pengelolaan data menu makanan, kategori, dan 
      ketersediaan operasional restoran.
   3. Order Tracking: Pemantauan pesanan restoran secara berurutan, dari 
      status pesanan masuk hingga pesanan diantarkan ke kamar tamu.
   4. Financial Reports: Pencatatan laporan pendapatan hotel dan status 
      pembayaran melalui Midtrans.

--------------------------------------------------------------------------------
SPESIFIKASI TEKNIS (TECH STACK)
--------------------------------------------------------------------------------
- Bahasa Pemrograman : PHP 8.3+
- Framework          : Laravel 11/13
- Basis Data         : MySQL / MariaDB
- Frontend           : Bootstrap 5, Vanilla JavaScript, CSS
- Payment Gateway    : Midtrans (Sandbox Environment)
- Environment        : Node.js (NPM), Composer, Ngrok

--------------------------------------------------------------------------------
PANDUAN PEMASANGAN (INSTALLATION GUIDE)
--------------------------------------------------------------------------------

1. Kloning Repositori
   Unduh kode sumber proyek melalui terminal:
   > git clone https://github.com/Shilycia/hotels.git
   > cd hotels

2. Instalasi Dependensi Sistem
   Pasang seluruh dependensi PHP dan kompilasi aset frontend:
   > composer install
   > npm install
   > npm run dev

3. Konfigurasi Environment
   Salin file konfigurasi environment dan hasilkan kunci aplikasi (App Key):
   > cp .env.example .env
   > php artisan key:generate

4. Konfigurasi Basis Data dan Antarmuka Pembayaran
   Buka file .env dan sesuaikan parameter berikut dengan environment lokal:
   
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=hotel_neo
   DB_USERNAME=root
   DB_PASSWORD=

   MIDTRANS_MERCHANT_ID=Isi_Dengan_Merchant_ID_Anda
   MIDTRANS_CLIENT_KEY=Isi_Dengan_Client_Key_Anda
   MIDTRANS_SERVER_KEY=Isi_Dengan_Server_Key_Anda
   MIDTRANS_IS_PRODUCTION=false

5. Migrasi dan Seeding Basis Data
   Eksekusi perintah berikut untuk membangun struktur tabel dan mengisi data 
   awal sistem:
   > php artisan migrate:fresh --seed

6. Tautan Penyimpanan (Storage Link)
   Hubungkan direktori penyimpanan agar aset gambar dapat diakses oleh publik:
   > php artisan storage:link

--------------------------------------------------------------------------------
KONFIGURASI PAYMENT CALLBACK (NGROK)
--------------------------------------------------------------------------------
Untuk mengizinkan Midtrans mengirimkan notifikasi status pembayaran ke server 
lokal, ikuti langkah berikut:

1. Jalankan peladen (server) Laravel:
   > php artisan serve

2. Pada jendela terminal baru, jalankan Ngrok pada porta 8000:
   > ngrok http 8000

3. Salin URL publik yang dihasilkan oleh Ngrok (contoh: 
   https://abcd-123.ngrok-free.app).

4. Masuk ke Dashboard Midtrans Sandbox, navigasi ke Settings > Configuration.
   Masukkan URL berikut ke dalam kolom Payment Notification URL:
   https://abcd-123.ngrok-free.app/admin/midtrans/callback

--------------------------------------------------------------------------------
AKUN PENGUJIAN (DEMO ACCOUNTS)
--------------------------------------------------------------------------------

A. Hak Akses Administrator
   URL Akses : /login
   Email     : admin@hotelneo.com
   Password  : password

B. Hak Akses Tamu
   URL Akses : /guest/login
   Email     : guest@gmail.com
   Password  : password123

================================================================================
© 2026 Bintang Putra Adryan - SOFTWARE DEVELOPER 
================================================================================