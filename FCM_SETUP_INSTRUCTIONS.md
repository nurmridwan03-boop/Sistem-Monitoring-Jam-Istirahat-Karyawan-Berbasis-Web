# Panduan Setup Firebase Cloud Messaging (FCM)

## Langkah 1: Buat Firebase Project

1. Buka [Firebase Console](https://console.firebase.google.com/)
2. Klik "Add project" atau pilih project yang sudah ada
3. Isi nama project dan ikuti langkah-langkahnya

## Langkah 2: Dapatkan Konfigurasi Firebase

1. Di Firebase Console, klik ikon Settings (⚙️) > **Project settings**
2. Scroll ke bawah ke bagian "Your apps"
3. Klik ikon **Web (</>)** untuk menambahkan web app
4. Isi nama app (contoh: "Monitoring Istirahat")
5. Centang "Also set up Firebase Hosting" (opsional)
6. Klik **Register app**
7. Copy konfigurasi Firebase yang muncul (firebaseConfig)

## Langkah 3: Dapatkan Server Key

1. Di Firebase Console, tetap di halaman **Project settings**
2. Klik tab **Cloud Messaging**
3. Scroll ke bagian **Cloud Messaging API (Legacy)**
4. Copy **Server key** (terlihat seperti: AAAAxxxxx...)
5. Jika belum ada, klik **Cloud Messaging API (Legacy)** untuk mengaktifkannya

## Langkah 4: Dapatkan VAPID Key (untuk Web Push)

1. Di tab **Cloud Messaging**, scroll ke bagian **Web Push certificates**
2. Jika belum ada, klik **Generate key pair**
3. Copy **Key pair** yang muncul

## Langkah 5: Update Konfigurasi

### Update `fcm_config.php`:
```php
$FCM_SERVER_KEY = "AAAAxxxxx..."; // Server key dari langkah 3
$FCM_VAPID_KEY = "BKxxxxx...";    // VAPID key dari langkah 4
```

### Update `karyawan.php` dan `admin_break.php`:
Ganti bagian `firebaseConfig` dengan konfigurasi dari langkah 2:
```javascript
const firebaseConfig = {
    apiKey: "AIzaSy...",
    authDomain: "your-project.firebaseapp.com",
    projectId: "your-project-id",
    storageBucket: "your-project.appspot.com",
    messagingSenderId: "123456789",
    appId: "1:123456789:web:abc123",
    vapidKey: "BKxxxxx..." // VAPID key dari langkah 4
};
```

## Langkah 6: Buat Tabel Database

1. Upload file `install_fcm_table.php` ke server
2. Akses melalui browser: `http://yoursite.com/install_fcm_table.php`
3. Atau jalankan SQL langsung:
   ```sql
   CREATE TABLE IF NOT EXISTS fcm_tokens (
       id INT AUTO_INCREMENT PRIMARY KEY,
       user_id INT NOT NULL,
       token TEXT NOT NULL,
       device_info VARCHAR(255) DEFAULT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
       UNIQUE KEY unique_user_token (user_id, token(255)),
       INDEX idx_user_id (user_id)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   ```

## Langkah 7: Upload Service Worker

1. Upload file `firebase-messaging-sw.js` ke **root directory** (folder utama project)
2. Pastikan file bisa diakses di: `https://yoursite.com/firebase-messaging-sw.js`

## Langkah 8: Test Notifikasi

1. Login ke aplikasi
2. Buka browser console (F12)
3. Cek apakah token FCM muncul di console
4. Mulai istirahat dan tunggu sampai selesai
5. Notifikasi akan muncul otomatis

## Catatan Penting untuk InfinityFree:

1. **HTTPS Required**: FCM memerlukan HTTPS. InfinityFree menyediakan HTTPS gratis, pastikan domain sudah menggunakan HTTPS.

2. **Service Worker**: Pastikan `firebase-messaging-sw.js` berada di root directory dan bisa diakses.

3. **cURL**: Pastikan hosting mendukung cURL untuk mengirim notifikasi ke FCM API.

4. **Cron Job** (Opsional): Untuk notifikasi yang lebih reliable, setup cron job di InfinityFree:
   - Jalankan `auto_check_all_breaks.php` setiap 1 menit
   - Di InfinityFree: cPanel > Cron Jobs
   - Command: `php /home/username/public_html/auto_check_all_breaks.php`

## Troubleshooting:

- **Token tidak tersimpan**: Cek apakah user sudah login dan `save_fcm_token.php` bisa diakses
- **Notifikasi tidak muncul**: 
  - Cek browser console untuk error
  - Pastikan permission notification sudah diberikan
  - Pastikan Server Key dan VAPID Key sudah benar
- **Service Worker tidak terdaftar**: 
  - Pastikan file `firebase-messaging-sw.js` ada di root
  - Cek apakah HTTPS aktif
  - Cek browser console untuk error

## Testing Manual:

Anda bisa test mengirim notifikasi secara manual dengan membuat file test:
```php
<?php
require_once 'send_fcm_notification.php';
$result = sendFCMNotification(1, 'Test Notification', 'Ini adalah test notifikasi');
print_r($result);
?>
```



