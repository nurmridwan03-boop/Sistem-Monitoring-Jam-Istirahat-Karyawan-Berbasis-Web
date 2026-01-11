<?php
/**
 * Konfigurasi Firebase Cloud Messaging
 * 
 * CARA MENDAPATKAN SERVER KEY:
 * 1. Buka Firebase Console: https://console.firebase.google.com/
 * 2. Pilih project Anda
 * 3. Klik Settings (ikon gear) > Project settings
 * 4. Tab "Cloud Messaging"
 * 5. Di bagian "Cloud Messaging API (Legacy)", copy "Server key"
 * 
 * PASTIKAN FILE INI TIDAK DIAKSES LANGSUNG OLEH USER!
 * Simpan server key dengan aman!
 */

// Server Key dari Firebase Console
// GANTI dengan Server Key Anda dari Firebase Console
$FCM_SERVER_KEY = "YOUR_FCM_SERVER_KEY_HERE";

// VAPID Key (opsional, untuk web push)
// Dapatkan dari: Firebase Console > Cloud Messaging > Web Push certificates
$FCM_VAPID_KEY = "YOUR_VAPID_KEY_HERE";

?>



